<?php

namespace App\Http\Controllers;

use App\Http\Requests\Accounting\SaveAccountingMappingsRequest;
use App\Http\Requests\Accounting\StoreGlAccountRequest;
use App\Http\Requests\Accounting\StoreJournalEntryRequest;
use App\Http\Requests\Accounting\StoreOpeningBalanceRequest;
use App\Http\Requests\Accounting\StorePeriodRequest;
use App\Http\Requests\Accounting\UpdateGlAccountRequest;
use App\Http\Requests\Accounting\UpdatePeriodRequest;
use App\Models\Client;
use App\Models\GlAccount;
use App\Models\GlJournalEntry;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\Accounting\BalanceSheetService;
use App\Services\Accounting\CashflowService;
use App\Services\Accounting\JournalPostingService;
use App\Services\Accounting\LedgerReportService;
use App\Services\Accounting\PayableReportService;
use App\Services\Accounting\PeriodService;
use App\Services\Accounting\ProfitLossService;
use App\Services\Accounting\ReceivableReportService;
use App\Services\Accounting\SetupService;
use App\Services\Accounting\TransactionPostingService;
use App\Services\Accounting\TrialBalanceService;
use App\Services\Accounting\VatSummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    public function __construct(
        private readonly SetupService $setupService,
        private readonly ProfitLossService $profitLossService,
        private readonly TrialBalanceService $trialBalanceService,
        private readonly BalanceSheetService $balanceSheetService,
        private readonly VatSummaryService $vatSummaryService,
        private readonly ReceivableReportService $receivableReportService,
        private readonly PayableReportService $payableReportService,
        private readonly CashflowService $cashflowService,
        private readonly JournalPostingService $journalPostingService,
        private readonly TransactionPostingService $transactionPostingService,
        private readonly PeriodService $periodService,
        private readonly LedgerReportService $ledgerReportService,
    ) {
    }

    public function profitAndLoss(Request $request)
    {
        $this->setupService->ensureInitialized();

        $filters = $this->baseFilters($request, ['warehouse_id', 'client_id']);
        $report = $this->profitLossService->generate($filters['from'], $filters['to']);

        if ($request->get('export') === 'excel') {
            return $this->exportCsv('profit-loss', [
                ['Metric', 'Amount'],
                ...collect($report['summary'])->map(fn ($row) => [$row['label'], $this->money($row['value'])])->all(),
            ]);
        }

        if ($request->get('export') === 'pdf') {
            return $this->printView('Profit & Loss (P&L)', $filters, [
                ['title' => 'Summary', 'headers' => ['Metric', 'Amount'], 'rows' => collect($report['summary'])->map(fn ($row) => [$row['label'], $this->money($row['value'])])->all()],
            ]);
        }

        return view('admin.accounting.profit-loss', [
            'title' => 'Profit & Loss (P&L)',
            'filters' => $filters,
            'warehouses' => Warehouse::orderBy('name')->get(),
            'clients' => Client::orderBy('name')->get(),
            'summary' => $report['summary'],
        ]);
    }

    public function accountsReceivable(Request $request)
    {
        $this->setupService->ensureInitialized();

        $filters = $this->baseFilters($request, ['warehouse_id', 'client_id']);
        $report = $this->receivableReportService->generate($filters);

        if ($request->get('export') === 'excel') {
            return $this->exportCsv('accounts-receivable', array_merge(
                [['Date', 'Client', 'Grand Total', 'Paid', 'Due', 'Status']],
                $report['rows']->map(fn ($row) => [
                    $row['date'],
                    $row['client_name'],
                    $this->money($row['grand_total']),
                    $this->money($row['paid']),
                    $this->money($row['due']),
                    $row['status'],
                ])->all()
            ));
        }

        if ($request->get('export') === 'pdf') {
            return $this->printView('Accounts Receivable (Customers)', $filters, [
                ['title' => 'Totals', 'headers' => ['Metric', 'Amount'], 'rows' => [
                    ['Total Receivables', $this->money($report['total_receivables'])],
                    ['Aging 0-30', $this->money($report['aging']['0_30'])],
                    ['Aging 31-60', $this->money($report['aging']['31_60'])],
                    ['Aging 60+', $this->money($report['aging']['60_plus'])],
                    ['AR Control (GL)', $this->money($report['gl_control_balance'])],
                ]],
            ]);
        }

        return view('admin.accounting.accounts-receivable', [
            'title' => 'Accounts Receivable (Customers)',
            'filters' => $filters,
            'warehouses' => Warehouse::orderBy('name')->get(),
            'clients' => Client::orderBy('name')->get(),
            'rows' => $report['rows'],
            'totalReceivables' => $report['total_receivables'],
            'aging' => $report['aging'],
        ]);
    }

    public function accountsPayable(Request $request)
    {
        $this->setupService->ensureInitialized();

        $filters = $this->baseFilters($request, ['warehouse_id', 'supplier_id']);
        $report = $this->payableReportService->generate($filters);

        if ($request->get('export') === 'excel') {
            return $this->exportCsv('accounts-payable', array_merge(
                [['Date', 'Supplier', 'Grand Total', 'Paid', 'Due', 'Status']],
                $report['rows']->map(fn ($row) => [
                    $row['date'],
                    $row['supplier_name'],
                    $this->money($row['grand_total']),
                    $this->money($row['paid']),
                    $this->money($row['due']),
                    $row['status'],
                ])->all()
            ));
        }

        if ($request->get('export') === 'pdf') {
            return $this->printView('Accounts Payable (Suppliers)', $filters, [
                ['title' => 'Totals', 'headers' => ['Metric', 'Amount'], 'rows' => [
                    ['Total Payables', $this->money($report['total_payables'])],
                    ['Aging 0-30', $this->money($report['aging']['0_30'])],
                    ['Aging 31-60', $this->money($report['aging']['31_60'])],
                    ['Aging 60+', $this->money($report['aging']['60_plus'])],
                    ['AP Control (GL)', $this->money($report['gl_control_balance'])],
                ]],
            ]);
        }

        return view('admin.accounting.accounts-payable', [
            'title' => 'Accounts Payable (Suppliers)',
            'filters' => $filters,
            'warehouses' => Warehouse::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'rows' => $report['rows'],
            'totalPayables' => $report['total_payables'],
            'aging' => $report['aging'],
        ]);
    }

    public function cashflow(Request $request)
    {
        $this->setupService->ensureInitialized();

        $filters = $this->baseFilters($request);
        $report = $this->cashflowService->generate($filters['from'], $filters['to']);

        $incomingByMethod = collect([
            ['payment_method' => 'Operating Activities', 'total' => max($report['operating'], 0)],
            ['payment_method' => 'Investing Activities', 'total' => max($report['investing'], 0)],
            ['payment_method' => 'Financing Activities', 'total' => max($report['financing'], 0)],
        ]);

        $outgoingPayments = collect([
            ['payment_method' => 'Operating Activities', 'total' => abs(min($report['operating'], 0))],
            ['payment_method' => 'Investing Activities', 'total' => abs(min($report['investing'], 0))],
            ['payment_method' => 'Financing Activities', 'total' => abs(min($report['financing'], 0))],
        ]);

        $expensesByCategory = collect([
            ['category' => 'Net Cash Movement', 'total' => $report['net_cash']],
        ]);

        if ($request->get('export') === 'excel') {
            return $this->exportCsv('cashflow', [
                ['Metric', 'Amount'],
                ['Incoming Total', $this->money($report['incoming_total'])],
                ['Outgoing Total', $this->money($report['outgoing_total'])],
                ['Net Cash', $this->money($report['net_cash'])],
                ['Operating', $this->money($report['operating'])],
                ['Investing', $this->money($report['investing'])],
                ['Financing', $this->money($report['financing'])],
            ]);
        }

        return view('admin.accounting.cashflow', [
            'title' => 'Cashflow',
            'filters' => $filters,
            'incomingByMethod' => $incomingByMethod,
            'outgoingPayments' => $outgoingPayments,
            'expensesByCategory' => $expensesByCategory,
            'incomingTotal' => $report['incoming_total'],
            'outgoingTotal' => $report['outgoing_total'],
            'expensesTotal' => $report['outgoing_total'],
            'netCash' => $report['net_cash'],
        ]);
    }

    public function vatSummary(Request $request)
    {
        $this->setupService->ensureInitialized();

        $filters = $this->baseFilters($request, ['warehouse_id', 'client_id', 'supplier_id']);
        $report = $this->vatSummaryService->generate($filters['from'], $filters['to']);

        if ($request->get('export') === 'excel') {
            return $this->exportCsv('vat-summary', [
                ['Metric', 'Amount'],
                ...collect($report['summary'])->map(fn ($row) => [$row['label'], $this->money($row['value'])])->all(),
            ]);
        }

        if ($request->get('export') === 'pdf') {
            return $this->printView('VAT Summary', $filters, [
                ['title' => 'Summary', 'headers' => ['Metric', 'Amount'], 'rows' => collect($report['summary'])->map(fn ($row) => [$row['label'], $this->money($row['value'])])->all()],
            ]);
        }

        return view('admin.accounting.vat-summary', [
            'title' => 'VAT Summary',
            'filters' => $filters,
            'warehouses' => Warehouse::orderBy('name')->get(),
            'clients' => Client::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'summary' => $report['summary'],
        ]);
    }

    public function trialBalance(Request $request)
    {
        $this->setupService->ensureInitialized();

        $filters = $this->baseFilters($request);
        $trial = $this->trialBalanceService->generate($filters['from'], $filters['to']);

        return view('admin.accounting.gl-trial-balance', [
            'title' => 'Trial Balance',
            'filters' => $filters,
            'accounts' => $trial['rows'],
            'totals' => $trial['totals'],
        ]);
    }

    public function ledgerReport(Request $request)
    {
        $this->setupService->ensureInitialized();

        $filters = $this->baseFilters($request, ['account_id']);
        $accounts = GlAccount::query()->where('is_active', true)->orderBy('code')->get();
        $report = null;

        if (! empty($filters['account_id'])) {
            $report = $this->ledgerReportService->accountLedger((int) $filters['account_id'], $filters['from'], $filters['to']);
        }

        return view('admin.accounting.gl-ledger', [
            'title' => 'GL Report',
            'filters' => $filters,
            'accounts' => $accounts,
            'report' => $report,
        ]);
    }

    public function glProfitAndLoss(Request $request)
    {
        $this->setupService->ensureInitialized();

        $filters = $this->baseFilters($request);
        $report = $this->profitLossService->generate($filters['from'], $filters['to']);

        return view('admin.accounting.gl-profit-loss', [
            'title' => 'Profit & Loss',
            'filters' => $filters,
            'rows' => collect([
                ['type' => 'Revenue', 'code' => '4100', 'account' => 'Sales Revenue', 'balance' => $report['revenue']],
                ['type' => 'Expense', 'code' => '5100', 'account' => 'Cost of Goods Sold (COGS)', 'balance' => $report['cogs']],
                ['type' => 'Expense', 'code' => '5999', 'account' => 'General Expense (Unmapped)', 'balance' => $report['operating_expenses']],
            ]),
            'revenue' => $report['revenue'],
            'cogs' => $report['cogs'],
            'operatingExpenses' => $report['operating_expenses'],
            'expenses' => $report['cogs'] + $report['operating_expenses'],
            'netProfit' => $report['net_profit'],
        ]);
    }

    public function balanceSheet(Request $request)
    {
        $this->setupService->ensureInitialized();

        $filters = [
            'as_of' => $request->input('as_of', now()->toDateString()),
        ];
        $report = $this->balanceSheetService->generate($filters['as_of']);

        return view('admin.accounting.gl-balance-sheet', [
            'title' => 'Balance Sheet',
            'filters' => $filters,
            'rows' => $report['rows'],
            'assets' => $report['assets'],
            'liabilities' => $report['liabilities'],
            'equity' => $report['equity'],
            'liabilitiesAndEquity' => $report['liabilities_and_equity'],
        ]);
    }

    public function glVatSummary(Request $request)
    {
        $this->setupService->ensureInitialized();

        $filters = $this->baseFilters($request);
        $report = $this->vatSummaryService->generate($filters['from'], $filters['to']);

        return view('admin.accounting.gl-vat-summary', [
            'title' => 'reports.gl.vat_summary',
            'filters' => $filters,
            'vatCollected' => $report['vat_collected'],
            'vatPaid' => $report['vat_paid'],
            'vatNet' => $report['vat_net'],
            'lines' => $report['lines'],
        ]);
    }

    public function chartOfAccounts()
    {
        $this->setupService->ensureInitialized();

        $accounts = GlAccount::query()
            ->with('parent')
            ->orderBy('code')
            ->get()
            ->map(fn (GlAccount $account) => (object) [
                'id' => $account->id,
                'code' => $account->code,
                'name' => $account->name,
                'name_ar' => $account->name_ar,
                'type' => $account->type,
                'normal_balance' => $account->normal_balance,
                'is_active' => $account->is_active,
                'parent_code' => $account->parent?->code,
                'parent_name' => $account->parent?->name,
            ]);

        return view('admin.accounting.chart-of-accounts', [
            'title' => 'Chart of Accounts',
            'accounts' => $accounts,
        ]);
    }

    public function journalEntries(Request $request)
    {
        $this->setupService->ensureInitialized();

        $entries = GlJournalEntry::query()
            ->when($request->input('status'), fn ($query, $status) => $query->where('status', $status))
            ->when($request->input('from'), fn ($query, $from) => $query->whereDate('entry_date', '>=', $from))
            ->when($request->input('to'), fn ($query, $to) => $query->whereDate('entry_date', '<=', $to))
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.accounting.journal-entries', [
            'title' => 'Journal Entries',
            'entries' => $entries,
            'filters' => [
                'status' => $request->input('status'),
                'from' => $request->input('from'),
                'to' => $request->input('to'),
            ],
        ]);
    }

    public function accountingMappings()
    {
        $this->setupService->ensureInitialized();

        $accounts = GlAccount::query()->orderBy('code')->get();
        $coreMappings = DB::table('gl_accounting_mappings')
            ->where('group_type', 'core')
            ->orderBy('label')
            ->get();
        $paymentMappings = DB::table('payment_types as payment_types')
            ->leftJoin('gl_accounting_mappings as mappings', function ($join) {
                $join->on('mappings.reference_id', '=', 'payment_types.id')
                    ->where('mappings.group_type', '=', 'payment_type');
            })
            ->orderBy('payment_types.id')
            ->get([
                'payment_types.id',
                DB::raw("COALESCE(payment_types.label_en, payment_types.name) as name"),
                'mappings.gl_account_id',
            ]);
        $expenseMappings = DB::table('expenses_categories as categories')
            ->leftJoin('gl_accounting_mappings as mappings', function ($join) {
                $join->on('mappings.reference_id', '=', 'categories.id')
                    ->where('mappings.group_type', '=', 'expense_category');
            })
            ->orderBy('categories.id')
            ->get([
                'categories.id',
                DB::raw("COALESCE(categories.label_en, categories.name) as name"),
                'mappings.gl_account_id',
            ]);

        return view('admin.accounting.accounting-mappings', [
            'title' => 'Accounting Mappings',
            'accounts' => $accounts,
            'coreMappings' => $coreMappings,
            'paymentMappings' => $paymentMappings,
            'expenseMappings' => $expenseMappings,
        ]);
    }

    public function openingBalances()
    {
        $this->setupService->ensureInitialized();

        $records = GlJournalEntry::query()
            ->where('is_opening', true)
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->get()
            ->map(fn (GlJournalEntry $entry) => (object) [
                'entry_date' => $entry->entry_date?->toDateString() ?? $entry->entry_date,
                'entry_no' => $entry->entry_no,
                'description' => $entry->description,
                'status' => $entry->status,
                'amount' => $entry->amount,
            ]);

        return view('admin.accounting.opening-balances', [
            'title' => 'reports.opening.title',
            'records' => $records,
        ]);
    }

    public function periods()
    {
        $this->setupService->ensureInitialized();

        $periods = DB::table('gl_periods')->orderByDesc('period')->get();

        return view('admin.accounting.periods', [
            'title' => 'reports.periods.title',
            'periods' => $periods,
        ]);
    }

    public function createAccount()
    {
        $this->setupService->ensureInitialized();

        return view('admin.accounting.account-form', [
            'title' => 'Add Account',
            'account' => null,
            'parents' => GlAccount::query()->orderBy('code')->get(),
            'types' => ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'],
        ]);
    }

    public function storeAccount(StoreGlAccountRequest $request)
    {
        $this->setupService->ensureInitialized();

        $validated = $request->validated();
        $normalBalance = $validated['normal_balance'] ?? (in_array($validated['type'], ['Asset', 'Expense'], true) ? 'debit' : 'credit');

        GlAccount::query()->create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'name_ar' => $validated['name_ar'] ?? null,
            'type' => $validated['type'],
            'category' => $validated['category'] ?? null,
            'normal_balance' => $normalBalance,
            'parent_id' => $validated['parent_id'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('accounting.gl-management.chart-of-accounts')->with('success', __('Account added successfully'));
    }

    public function editAccount(int $account)
    {
        $this->setupService->ensureInitialized();

        return view('admin.accounting.account-form', [
            'title' => 'Edit Account',
            'account' => GlAccount::query()->findOrFail($account),
            'parents' => GlAccount::query()->where('id', '!=', $account)->orderBy('code')->get(),
            'types' => ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'],
        ]);
    }

    public function updateAccount(UpdateGlAccountRequest $request, int $account)
    {
        $this->setupService->ensureInitialized();

        $validated = $request->validated();
        $normalBalance = $validated['normal_balance'] ?? (in_array($validated['type'], ['Asset', 'Expense'], true) ? 'debit' : 'credit');

        GlAccount::query()->where('id', $account)->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'name_ar' => $validated['name_ar'] ?? null,
            'type' => $validated['type'],
            'category' => $validated['category'] ?? null,
            'normal_balance' => $normalBalance,
            'parent_id' => $validated['parent_id'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'updated_at' => now(),
        ]);

        return redirect()->route('accounting.gl-management.chart-of-accounts')->with('success', __('Account updated successfully'));
    }

    public function deleteAccount(int $account)
    {
        $this->setupService->ensureInitialized();

        GlAccount::query()->where('id', $account)->delete();

        return redirect()->route('accounting.gl-management.chart-of-accounts')->with('success', __('Account deleted successfully'));
    }

    public function createJournalEntry()
    {
        $this->setupService->ensureInitialized();

        return view('admin.accounting.journal-entry-form', [
            'title' => 'New Journal Entry',
            'accounts' => GlAccount::query()->where('is_active', true)->orderBy('code')->get(),
        ]);
    }

    public function storeJournalEntry(StoreJournalEntryRequest $request)
    {
        $this->setupService->ensureInitialized();

        $validated = $request->validated();

        $lines = collect($request->input('line_account_id', []))
            ->map(function ($accountId, $index) use ($request) {
                return [
                    'gl_account_id' => $accountId,
                    'description' => $request->input("line_description.$index"),
                    'debit' => (float) ($request->input("line_debit.$index") ?: 0),
                    'credit' => (float) ($request->input("line_credit.$index") ?: 0),
                    'client_id' => $request->input("line_client_id.$index"),
                    'supplier_id' => $request->input("line_supplier_id.$index"),
                    'invoice_id' => $request->input("line_invoice_id.$index"),
                ];
            })
            ->filter(fn ($line) => $line['gl_account_id'] && ($line['debit'] > 0 || $line['credit'] > 0))
            ->values()
            ->all();

        $this->journalPostingService->post([
            'entry_date' => $validated['entry_date'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'reference_no' => $validated['reference_no'] ?? null,
            'source_type' => 'manual_journal',
            'source_id' => null,
            'created_by' => auth()->id(),
            'lines' => $lines,
        ]);

        return redirect()->route('accounting.gl-management.journal-entries')->with('success', __('Journal entry created successfully'));
    }

    public function showJournalEntry(int $entry)
    {
        $this->setupService->ensureInitialized();

        $entryRecord = GlJournalEntry::query()->with(['lines.account'])->findOrFail($entry);

        $lines = $entryRecord->lines->map(fn ($line) => (object) [
            'code' => $line->account?->code,
            'name' => $line->account?->name,
            'description' => $line->line_description ?: $line->description,
            'debit' => $line->debit,
            'credit' => $line->credit,
        ]);

        return view('admin.accounting.journal-entry-show', [
            'title' => 'Journal Entry',
            'entry' => $entryRecord,
            'lines' => $lines,
        ]);
    }

    public function saveAccountingMappings(SaveAccountingMappingsRequest $request)
    {
        $this->setupService->ensureInitialized();

        foreach ($request->input('core', []) as $mappingId => $accountId) {
            DB::table('gl_accounting_mappings')
                ->where('id', $mappingId)
                ->update([
                    'gl_account_id' => $accountId ?: null,
                    'debit_account_id' => $accountId ?: null,
                    'updated_at' => now(),
                ]);
        }

        foreach ($request->input('payment_type', []) as $paymentTypeId => $accountId) {
            DB::table('gl_accounting_mappings')->updateOrInsert(
                ['group_type' => 'payment_type', 'mapping_key' => 'payment_type', 'reference_id' => $paymentTypeId],
                [
                    'key' => 'payment_type',
                    'name' => 'Payment Type',
                    'label' => 'Payment Type',
                    'gl_account_id' => $accountId ?: null,
                    'debit_account_id' => $accountId ?: null,
                    'credit_account_id' => null,
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        foreach ($request->input('expense_category', []) as $categoryId => $accountId) {
            DB::table('gl_accounting_mappings')->updateOrInsert(
                ['group_type' => 'expense_category', 'mapping_key' => 'expense_category', 'reference_id' => $categoryId],
                [
                    'key' => 'expense_category',
                    'name' => 'Expense Category',
                    'label' => 'Expense Category',
                    'gl_account_id' => $accountId ?: null,
                    'debit_account_id' => $accountId ?: null,
                    'credit_account_id' => null,
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        return redirect()->route('accounting.gl-management.accounting-mappings')->with('success', __('Mappings saved successfully'));
    }

    public function createOpeningBalance()
    {
        $this->setupService->ensureInitialized();

        return view('admin.accounting.opening-balance-form', [
            'title' => 'reports.opening.create',
            'accounts' => GlAccount::query()->where('is_active', true)->orderBy('code')->get(),
        ]);
    }

    public function storeOpeningBalance(StoreOpeningBalanceRequest $request)
    {
        $this->setupService->ensureInitialized();

        $validated = $request->validated();

        $journal = $this->transactionPostingService->postOpeningBalance(
            (int) $validated['gl_account_id'],
            (float) $validated['amount'],
            $validated['entry_date'],
            $validated['description'],
        );

        if (DB::getSchemaBuilder()->hasTable('gl_opening_balances')) {
            DB::table('gl_opening_balances')->insert([
                'entry_date' => $validated['entry_date'],
                'entry_no' => $journal->entry_no,
                'description' => $validated['description'],
                'status' => 'posted',
                'gl_account_id' => $validated['gl_account_id'],
                'amount' => $validated['amount'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('accounting.gl-management.opening-balances')->with('success', __('Opening balance created successfully'));
    }

    public function createPeriod()
    {
        $this->setupService->ensureInitialized();

        return view('admin.accounting.period-form', [
            'title' => 'reports.periods.create',
            'period' => null,
        ]);
    }

    public function storePeriod(StorePeriodRequest $request)
    {
        $this->setupService->ensureInitialized();

        $validated = $request->validated();
        $this->periodService->createOrUpdateFromCode($validated['period'], $validated['status'], $validated['notes'] ?? null);

        return redirect()->route('accounting.gl-management.periods')->with('success', __('Period created successfully'));
    }

    public function editPeriod(int $period)
    {
        $this->setupService->ensureInitialized();

        return view('admin.accounting.period-form', [
            'title' => 'reports.periods.edit',
            'period' => DB::table('gl_periods')->where('id', $period)->firstOrFail(),
        ]);
    }

    public function updatePeriod(UpdatePeriodRequest $request, int $period)
    {
        $this->setupService->ensureInitialized();

        $validated = $request->validated();
        $this->periodService->createOrUpdateFromCode($validated['period'], $validated['status'], $validated['notes'] ?? null, $period);

        return redirect()->route('accounting.gl-management.periods')->with('success', __('Period updated successfully'));
    }

    protected function baseFilters(Request $request, array $extraKeys = []): array
    {
        $from = $request->input('from', now()->subMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        if (! empty($from) && ! empty($to)) {
            try {
                if (\Carbon\Carbon::parse($from)->gt(\Carbon\Carbon::parse($to))) {
                    [$from, $to] = [$to, $from];
                }
            } catch (\Throwable $e) {
                // Keep raw values.
            }
        }

        $filters = ['from' => $from, 'to' => $to];

        foreach ($extraKeys as $key) {
            $filters[$key] = $request->input($key);
        }

        return $filters;
    }

    protected function exportCsv(string $name, array $rows)
    {
        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $name . '-' . now()->format('Ymd_His') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function printView(string $title, array $filters, array $sections)
    {
        return response()->view('admin.accounting.print', [
            'title' => $title,
            'filters' => $filters,
            'sections' => $sections,
        ]);
    }

    protected function money(float $value): string
    {
        return number_format($value, 2, '.', '');
    }
}
