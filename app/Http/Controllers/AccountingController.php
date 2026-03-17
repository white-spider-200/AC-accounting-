<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Supplier;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AccountingController extends Controller
{
    public function profitAndLoss(Request $request)
    {
        $filters = $this->baseFilters($request, ['warehouse_id', 'client_id']);
        $salesQuery = DB::table('sales');
        $this->applySaleFilters($salesQuery, $filters);

        $revenue = (float) $salesQuery->sum('total_amount');

        $cogsQuery = DB::table('sale_details')
            ->join('sales', 'sales.id', '=', 'sale_details.sale_id');
        $this->applySaleFilters($cogsQuery, $filters, 'sales');
        $cogs = (float) $cogsQuery->sum('sale_details.total_price');

        $expensesQuery = DB::table('expenses');
        $this->applyDateRange($expensesQuery, 'expense_date', $filters['from'], $filters['to']);
        $expenses = (float) $expensesQuery->sum('amount');

        $grossProfit = $revenue - $cogs;
        $netProfit = $grossProfit - $expenses;

        $summary = [
            ['label' => 'Revenue', 'value' => $revenue],
            ['label' => 'COGS', 'value' => $cogs],
            ['label' => 'Gross Profit', 'value' => $grossProfit],
            ['label' => 'Expenses', 'value' => $expenses],
            ['label' => 'Net Profit', 'value' => $netProfit, 'highlight' => true],
        ];

        if ($request->get('export') === 'excel') {
            return $this->exportCsv('profit-loss', [
                ['Metric', 'Amount'],
                ...collect($summary)->map(fn ($row) => [$row['label'], $this->money($row['value'])])->all(),
            ]);
        }

        if ($request->get('export') === 'pdf') {
            return $this->printView('Profit & Loss (P&L)', $filters, [
                ['title' => 'Summary', 'headers' => ['Metric', 'Amount'], 'rows' => collect($summary)->map(fn ($row) => [$row['label'], $this->money($row['value'])])->all()],
            ]);
        }

        return view('admin.accounting.profit-loss', [
            'title' => 'Profit & Loss (P&L)',
            'filters' => $filters,
            'warehouses' => Warehouse::orderBy('name')->get(),
            'clients' => Client::orderBy('name')->get(),
            'summary' => $summary,
        ]);
    }

    public function accountsReceivable(Request $request)
    {
        $filters = $this->baseFilters($request, ['warehouse_id', 'client_id']);
        $rows = $this->receivableRows($filters);
        $aging = $this->agingBuckets($rows, 'due', 'date');
        $totalReceivables = $rows->sum('due');

        if ($request->get('export') === 'excel') {
            return $this->exportCsv('accounts-receivable', array_merge(
                [['Date', 'Client', 'Grand Total', 'Paid', 'Due', 'Status']],
                $rows->map(fn ($row) => [
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
                    ['Total Receivables', $this->money($totalReceivables)],
                    ['Aging 0-30', $this->money($aging['0_30'])],
                    ['Aging 31-60', $this->money($aging['31_60'])],
                    ['Aging 60+', $this->money($aging['60_plus'])],
                ]],
                ['title' => 'Receivables', 'headers' => ['#', 'Date', 'Client', 'Grand Total', 'Paid', 'Due', 'Status'], 'rows' => $rows->map(fn ($row) => [
                    $row['id'],
                    $row['date'],
                    $row['client_name'],
                    $this->money($row['grand_total']),
                    $this->money($row['paid']),
                    $this->money($row['due']),
                    $row['status'],
                ])->all()],
            ]);
        }

        return view('admin.accounting.accounts-receivable', [
            'title' => 'Accounts Receivable (Customers)',
            'filters' => $filters,
            'warehouses' => Warehouse::orderBy('name')->get(),
            'clients' => Client::orderBy('name')->get(),
            'rows' => $rows,
            'totalReceivables' => $totalReceivables,
            'aging' => $aging,
        ]);
    }

    public function accountsPayable(Request $request)
    {
        $filters = $this->baseFilters($request, ['warehouse_id', 'supplier_id']);
        $rows = $this->payableRows($filters);
        $aging = $this->agingBuckets($rows, 'due', 'date');
        $totalPayables = $rows->sum('due');

        if ($request->get('export') === 'excel') {
            return $this->exportCsv('accounts-payable', array_merge(
                [['Date', 'Supplier', 'Grand Total', 'Paid', 'Due', 'Status']],
                $rows->map(fn ($row) => [
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
                    ['Total Payables', $this->money($totalPayables)],
                    ['Aging 0-30', $this->money($aging['0_30'])],
                    ['Aging 31-60', $this->money($aging['31_60'])],
                    ['Aging 60+', $this->money($aging['60_plus'])],
                ]],
                ['title' => 'Payables', 'headers' => ['#', 'Date', 'Supplier', 'Grand Total', 'Paid', 'Due', 'Status'], 'rows' => $rows->map(fn ($row) => [
                    $row['id'],
                    $row['date'],
                    $row['supplier_name'],
                    $this->money($row['grand_total']),
                    $this->money($row['paid']),
                    $this->money($row['due']),
                    $row['status'],
                ])->all()],
            ]);
        }

        return view('admin.accounting.accounts-payable', [
            'title' => 'Accounts Payable (Suppliers)',
            'filters' => $filters,
            'warehouses' => Warehouse::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'rows' => $rows,
            'totalPayables' => $totalPayables,
            'aging' => $aging,
        ]);
    }

    public function cashflow(Request $request)
    {
        $filters = $this->baseFilters($request);

        $incomingByMethod = DB::table('payment_sales')
            ->join('payments', 'payments.id', '=', 'payment_sales.payment_id')
            ->leftJoin('payment_types', 'payment_types.id', '=', 'payments.payment_type_id')
            ->selectRaw("COALESCE(payment_types.label_en, payment_types.name, 'Unknown') as payment_method")
            ->selectRaw('SUM(COALESCE(payment_sales.amount, payments.amount, 0)) as total');
        $this->applyDateRange($incomingByMethod, 'payments.payment_date', $filters['from'], $filters['to']);
        $incomingByMethod = $incomingByMethod
            ->groupBy('payment_method')
            ->orderBy('payment_method')
            ->get()
            ->map(fn ($row) => [
                'payment_method' => $row->payment_method,
                'total' => (float) $row->total,
            ]);

        $outgoingPayments = DB::table('payments')
            ->leftJoin('payment_types', 'payment_types.id', '=', 'payments.payment_type_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('payment_sales')
                    ->whereColumn('payment_sales.payment_id', 'payments.id');
            })
            ->selectRaw("COALESCE(payment_types.label_en, payment_types.name, 'Unknown') as payment_method")
            ->selectRaw('SUM(payments.amount) as total');
        $this->applyDateRange($outgoingPayments, 'payments.payment_date', $filters['from'], $filters['to']);
        $outgoingPayments = $outgoingPayments
            ->groupBy('payment_method')
            ->orderBy('payment_method')
            ->get()
            ->map(fn ($row) => [
                'payment_method' => $row->payment_method,
                'total' => (float) $row->total,
            ]);

        $expensesByCategory = DB::table('expenses')
            ->leftJoin('expenses_categories', 'expenses_categories.id', '=', 'expenses.expenses_category_id')
            ->selectRaw("COALESCE(expenses_categories.label_en, expenses_categories.name, 'Uncategorized') as category")
            ->selectRaw('SUM(expenses.amount) as total');
        $this->applyDateRange($expensesByCategory, 'expenses.expense_date', $filters['from'], $filters['to']);
        $expensesByCategory = $expensesByCategory
            ->groupBy('category')
            ->orderBy('category')
            ->get()
            ->map(fn ($row) => [
                'category' => $row->category,
                'total' => (float) $row->total,
            ]);

        $incomingTotal = $incomingByMethod->sum('total');
        $outgoingTotal = $outgoingPayments->sum('total');
        $expensesTotal = $expensesByCategory->sum('total');
        $netCash = $incomingTotal - $outgoingTotal - $expensesTotal;

        if ($request->get('export') === 'excel') {
            $rows = [
                ['Incoming Total', $this->money($incomingTotal)],
                ['Outgoing Total', $this->money($outgoingTotal)],
                ['Net Cash', $this->money($netCash)],
                ['Total Expenses', $this->money($expensesTotal)],
                [],
                ['Incoming by Payment Method'],
                ['Payment Method', 'Total'],
                ...$incomingByMethod->map(fn ($row) => [$row['payment_method'], $this->money($row['total'])])->all(),
                [],
                ['Outgoing (Purchases) by Payment Method'],
                ['Payment Method', 'Total'],
                ...$outgoingPayments->map(fn ($row) => [$row['payment_method'], $this->money($row['total'])])->all(),
                [],
                ['Expenses by Category'],
                ['Expense Category', 'Total'],
                ...$expensesByCategory->map(fn ($row) => [$row['category'], $this->money($row['total'])])->all(),
            ];

            return $this->exportCsv('cashflow', $rows);
        }

        if ($request->get('export') === 'pdf') {
            return $this->printView('Cashflow', $filters, [
                ['title' => 'Totals', 'headers' => ['Metric', 'Amount'], 'rows' => [
                    ['Incoming Total', $this->money($incomingTotal)],
                    ['Outgoing Total', $this->money($outgoingTotal)],
                    ['Net Cash', $this->money($netCash)],
                    ['Total Expenses', $this->money($expensesTotal)],
                ]],
                ['title' => 'Incoming by Payment Method', 'headers' => ['Payment Method', 'Total'], 'rows' => $incomingByMethod->map(fn ($row) => [$row['payment_method'], $this->money($row['total'])])->all()],
                ['title' => 'Outgoing (Purchases) by Payment Method', 'headers' => ['Payment Method', 'Total'], 'rows' => $outgoingPayments->map(fn ($row) => [$row['payment_method'], $this->money($row['total'])])->all()],
                ['title' => 'Expenses by Category', 'headers' => ['Expense Category', 'Total'], 'rows' => $expensesByCategory->map(fn ($row) => [$row['category'], $this->money($row['total'])])->all()],
            ]);
        }

        return view('admin.accounting.cashflow', [
            'title' => 'Cashflow',
            'filters' => $filters,
            'incomingByMethod' => $incomingByMethod,
            'outgoingPayments' => $outgoingPayments,
            'expensesByCategory' => $expensesByCategory,
            'incomingTotal' => $incomingTotal,
            'outgoingTotal' => $outgoingTotal,
            'expensesTotal' => $expensesTotal,
            'netCash' => $netCash,
        ]);
    }

    public function vatSummary(Request $request)
    {
        $filters = $this->baseFilters($request, ['warehouse_id', 'client_id', 'supplier_id']);

        $salesVatQuery = DB::table('sales');
        $this->applySaleFilters($salesVatQuery, $filters);
        $vatCollected = (float) $salesVatQuery->sum('tax_amount');

        $purchasesVatQuery = DB::table('purchases');
        $this->applyPurchaseFilters($purchasesVatQuery, $filters);
        $vatPaid = (float) $purchasesVatQuery->sum('tax_amount');

        $vatNet = $vatCollected - $vatPaid;
        $summary = [
            ['label' => 'VAT Collected (Sales)', 'value' => $vatCollected],
            ['label' => 'VAT Paid (Purchases)', 'value' => $vatPaid],
            ['label' => 'VAT Net', 'value' => $vatNet, 'highlight' => true],
        ];

        if ($request->get('export') === 'excel') {
            return $this->exportCsv('vat-summary', [
                ['Metric', 'Amount'],
                ...collect($summary)->map(fn ($row) => [$row['label'], $this->money($row['value'])])->all(),
            ]);
        }

        if ($request->get('export') === 'pdf') {
            return $this->printView('VAT Summary', $filters, [
                ['title' => 'Summary', 'headers' => ['Metric', 'Amount'], 'rows' => collect($summary)->map(fn ($row) => [$row['label'], $this->money($row['value'])])->all()],
            ]);
        }

        return view('admin.accounting.vat-summary', [
            'title' => 'VAT Summary',
            'filters' => $filters,
            'warehouses' => Warehouse::orderBy('name')->get(),
            'clients' => Client::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'summary' => $summary,
        ]);
    }

    public function trialBalance(Request $request)
    {
        $filters = $this->baseFilters($request);
        $accounts = $this->glAccounts($filters);
        $totals = [
            'debit' => $accounts->sum('debit'),
            'credit' => $accounts->sum('credit'),
            'balance' => $accounts->sum('balance'),
        ];

        return view('admin.accounting.gl-trial-balance', [
            'title' => 'Trial Balance',
            'filters' => $filters,
            'accounts' => $accounts,
            'totals' => $totals,
        ]);
    }

    public function glProfitAndLoss(Request $request)
    {
        $filters = $this->baseFilters($request);
        $snapshot = $this->glSnapshot($filters);
        $rows = collect([
            [
                'type' => 'Revenue',
                'code' => '4100',
                'account' => 'Sales Revenue',
                'balance' => $snapshot['revenue'],
            ],
            [
                'type' => 'Expense',
                'code' => '5100',
                'account' => 'Operating Expenses',
                'balance' => $snapshot['expenses'],
            ],
        ]);

        return view('admin.accounting.gl-profit-loss', [
            'title' => 'Profit & Loss',
            'filters' => $filters,
            'rows' => $rows,
            'revenue' => $snapshot['revenue'],
            'expenses' => $snapshot['expenses'],
            'netProfit' => $snapshot['net_profit'],
        ]);
    }

    public function balanceSheet(Request $request)
    {
        $filters = [
            'as_of' => $request->input('as_of', now()->toDateString()),
        ];
        $snapshot = $this->glSnapshot([
            'from' => null,
            'to' => $filters['as_of'],
        ]);

        $rows = collect([
            ['type' => 'Asset', 'code' => '1110', 'account' => 'Cash', 'balance' => $snapshot['cash']],
            ['type' => 'Asset', 'code' => '1130', 'account' => 'Accounts Receivable', 'balance' => $snapshot['accounts_receivable']],
            ['type' => 'Asset', 'code' => '1140', 'account' => 'Inventory', 'balance' => $snapshot['inventory']],
            ['type' => 'Asset', 'code' => '1150', 'account' => 'VAT Input (Recoverable)', 'balance' => $snapshot['vat_input']],
            ['type' => 'Liability', 'code' => '2110', 'account' => 'Accounts Payable', 'balance' => $snapshot['accounts_payable']],
            ['type' => 'Liability', 'code' => '2120', 'account' => 'VAT Output (Payable)', 'balance' => $snapshot['vat_output']],
        ]);

        return view('admin.accounting.gl-balance-sheet', [
            'title' => 'Balance Sheet',
            'filters' => $filters,
            'rows' => $rows,
            'assets' => $snapshot['assets'],
            'liabilities' => $snapshot['liabilities'],
            'equity' => $snapshot['equity'],
            'liabilitiesAndEquity' => $snapshot['liabilities'] + $snapshot['equity'],
        ]);
    }

    public function glVatSummary(Request $request)
    {
        $filters = $this->baseFilters($request);
        $snapshot = $this->glSnapshot($filters);
        $lines = $this->glVatLines($filters);

        return view('admin.accounting.gl-vat-summary', [
            'title' => 'reports.gl.vat_summary',
            'filters' => $filters,
            'vatCollected' => $snapshot['vat_output'],
            'vatPaid' => $snapshot['vat_input'],
            'vatNet' => $snapshot['vat_output'] - $snapshot['vat_input'],
            'lines' => $lines,
        ]);
    }

    public function chartOfAccounts()
    {
        $this->ensureGlManagementSetup();

        $accounts = DB::table('gl_accounts as accounts')
            ->leftJoin('gl_accounts as parents', 'parents.id', '=', 'accounts.parent_id')
            ->orderBy('accounts.code')
            ->get([
                'accounts.id',
                'accounts.code',
                'accounts.name',
                'accounts.name_ar',
                'accounts.type',
                'accounts.is_active',
                'parents.code as parent_code',
                'parents.name as parent_name',
            ]);

        return view('admin.accounting.chart-of-accounts', [
            'title' => 'Chart of Accounts',
            'accounts' => $accounts,
        ]);
    }

    public function journalEntries()
    {
        $this->ensureGlManagementSetup();

        $entries = DB::table('gl_journal_entries')
            ->when(request('status'), fn ($query, $status) => $query->where('status', $status))
            ->when(request('from'), fn ($query, $from) => $query->whereDate('entry_date', '>=', $from))
            ->when(request('to'), fn ($query, $to) => $query->whereDate('entry_date', '<=', $to))
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.accounting.journal-entries', [
            'title' => 'Journal Entries',
            'entries' => $entries,
            'filters' => [
                'status' => request('status'),
                'from' => request('from'),
                'to' => request('to'),
            ],
        ]);
    }

    public function accountingMappings()
    {
        $this->ensureGlManagementSetup();

        $accounts = DB::table('gl_accounts')->orderBy('code')->get();
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
        $this->ensureGlManagementSetup();

        $records = DB::table('gl_opening_balances')
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->get();

        return view('admin.accounting.opening-balances', [
            'title' => 'reports.opening.title',
            'records' => $records,
        ]);
    }

    public function periods()
    {
        $this->ensureGlManagementSetup();

        $periods = DB::table('gl_periods')
            ->orderByDesc('period')
            ->get();

        return view('admin.accounting.periods', [
            'title' => 'reports.periods.title',
            'periods' => $periods,
        ]);
    }

    public function createAccount()
    {
        $this->ensureGlManagementSetup();

        return view('admin.accounting.account-form', [
            'title' => 'Add Account',
            'account' => null,
            'parents' => DB::table('gl_accounts')->orderBy('code')->get(),
            'types' => $this->accountTypes(),
        ]);
    }

    public function storeAccount(Request $request)
    {
        $this->ensureGlManagementSetup();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:gl_accounts,code'],
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in($this->accountTypes())],
            'parent_id' => ['nullable', 'exists:gl_accounts,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::table('gl_accounts')->insert([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'name_ar' => $validated['name_ar'] ?? null,
            'type' => $validated['type'],
            'parent_id' => $validated['parent_id'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('accounting.gl-management.chart-of-accounts')->with('success', __('Account added successfully'));
    }

    public function editAccount(int $account)
    {
        $this->ensureGlManagementSetup();

        return view('admin.accounting.account-form', [
            'title' => 'Edit Account',
            'account' => DB::table('gl_accounts')->where('id', $account)->firstOrFail(),
            'parents' => DB::table('gl_accounts')->where('id', '!=', $account)->orderBy('code')->get(),
            'types' => $this->accountTypes(),
        ]);
    }

    public function updateAccount(Request $request, int $account)
    {
        $this->ensureGlManagementSetup();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('gl_accounts', 'code')->ignore($account)],
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in($this->accountTypes())],
            'parent_id' => ['nullable', 'exists:gl_accounts,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::table('gl_accounts')
            ->where('id', $account)
            ->update([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'name_ar' => $validated['name_ar'] ?? null,
                'type' => $validated['type'],
                'parent_id' => $validated['parent_id'] ?? null,
                'is_active' => $request->boolean('is_active'),
                'updated_at' => now(),
            ]);

        return redirect()->route('accounting.gl-management.chart-of-accounts')->with('success', __('Account updated successfully'));
    }

    public function deleteAccount(int $account)
    {
        $this->ensureGlManagementSetup();

        DB::table('gl_accounts')->where('id', $account)->delete();

        return redirect()->route('accounting.gl-management.chart-of-accounts')->with('success', __('Account deleted successfully'));
    }

    public function createJournalEntry()
    {
        $this->ensureGlManagementSetup();

        return view('admin.accounting.journal-entry-form', [
            'title' => 'New Journal Entry',
            'accounts' => DB::table('gl_accounts')->where('is_active', 1)->orderBy('code')->get(),
        ]);
    }

    public function storeJournalEntry(Request $request)
    {
        $this->ensureGlManagementSetup();

        $validated = $request->validate([
            'entry_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['draft', 'posted'])],
            'line_account_id' => ['required', 'array', 'min:2'],
            'line_account_id.*' => ['nullable', 'exists:gl_accounts,id'],
            'line_description' => ['nullable', 'array'],
            'line_debit' => ['nullable', 'array'],
            'line_credit' => ['nullable', 'array'],
        ]);

        $lines = collect($request->input('line_account_id', []))
            ->map(function ($accountId, $index) use ($request) {
                return [
                    'gl_account_id' => $accountId,
                    'description' => $request->input("line_description.$index"),
                    'debit' => (float) ($request->input("line_debit.$index") ?: 0),
                    'credit' => (float) ($request->input("line_credit.$index") ?: 0),
                ];
            })
            ->filter(fn ($line) => $line['gl_account_id'] && ($line['debit'] > 0 || $line['credit'] > 0))
            ->values();

        abort_if($lines->count() < 2, 422, 'At least two journal lines are required.');

        $debitTotal = $lines->sum('debit');
        $creditTotal = $lines->sum('credit');

        abort_unless(abs($debitTotal - $creditTotal) < 0.0001, 422, 'Debits and credits must balance.');

        DB::transaction(function () use ($validated, $lines, $debitTotal) {
            $entryId = DB::table('gl_journal_entries')->insertGetId([
                'entry_no' => $this->nextJournalEntryNumber(),
                'entry_date' => $validated['entry_date'],
                'description' => $validated['description'],
                'status' => $validated['status'],
                'amount' => $debitTotal,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($lines as $line) {
                DB::table('gl_journal_entry_lines')->insert([
                    'journal_entry_id' => $entryId,
                    'gl_account_id' => $line['gl_account_id'],
                    'description' => $line['description'],
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()->route('accounting.gl-management.journal-entries')->with('success', __('Journal entry created successfully'));
    }

    public function showJournalEntry(int $entry)
    {
        $this->ensureGlManagementSetup();

        $entryRecord = DB::table('gl_journal_entries')->where('id', $entry)->firstOrFail();
        $lines = DB::table('gl_journal_entry_lines as lines')
            ->join('gl_accounts as accounts', 'accounts.id', '=', 'lines.gl_account_id')
            ->where('lines.journal_entry_id', $entry)
            ->get([
                'accounts.code',
                'accounts.name',
                'lines.description',
                'lines.debit',
                'lines.credit',
            ]);

        return view('admin.accounting.journal-entry-show', [
            'title' => 'Journal Entry',
            'entry' => $entryRecord,
            'lines' => $lines,
        ]);
    }

    public function saveAccountingMappings(Request $request)
    {
        $this->ensureGlManagementSetup();

        foreach ($request->input('core', []) as $mappingId => $accountId) {
            DB::table('gl_accounting_mappings')
                ->where('id', $mappingId)
                ->update([
                    'gl_account_id' => $accountId ?: null,
                    'updated_at' => now(),
                ]);
        }

        foreach ($request->input('payment_type', []) as $paymentTypeId => $accountId) {
            DB::table('gl_accounting_mappings')->updateOrInsert(
                ['group_type' => 'payment_type', 'mapping_key' => 'payment_type', 'reference_id' => $paymentTypeId],
                ['label' => 'Payment Type', 'gl_account_id' => $accountId ?: null, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        foreach ($request->input('expense_category', []) as $categoryId => $accountId) {
            DB::table('gl_accounting_mappings')->updateOrInsert(
                ['group_type' => 'expense_category', 'mapping_key' => 'expense_category', 'reference_id' => $categoryId],
                ['label' => 'Expense Category', 'gl_account_id' => $accountId ?: null, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        return redirect()->route('accounting.gl-management.accounting-mappings')->with('success', __('Mappings saved successfully'));
    }

    public function createOpeningBalance()
    {
        $this->ensureGlManagementSetup();

        return view('admin.accounting.opening-balance-form', [
            'title' => 'reports.opening.create',
            'accounts' => DB::table('gl_accounts')->where('is_active', 1)->orderBy('code')->get(),
        ]);
    }

    public function storeOpeningBalance(Request $request)
    {
        $this->ensureGlManagementSetup();

        $validated = $request->validate([
            'entry_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['draft', 'posted'])],
            'gl_account_id' => ['nullable', 'exists:gl_accounts,id'],
            'amount' => ['required', 'numeric'],
        ]);

        DB::table('gl_opening_balances')->insert([
            'entry_date' => $validated['entry_date'],
            'entry_no' => $this->nextOpeningBalanceNumber(),
            'description' => $validated['description'],
            'status' => $validated['status'],
            'gl_account_id' => $validated['gl_account_id'] ?? null,
            'amount' => $validated['amount'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('accounting.gl-management.opening-balances')->with('success', __('Opening balance created successfully'));
    }

    public function createPeriod()
    {
        $this->ensureGlManagementSetup();

        return view('admin.accounting.period-form', [
            'title' => 'reports.periods.create',
            'period' => null,
        ]);
    }

    public function storePeriod(Request $request)
    {
        $this->ensureGlManagementSetup();

        $validated = $request->validate([
            'period' => ['required', 'string', 'max:20', 'unique:gl_periods,period'],
            'status' => ['required', Rule::in(['open', 'closed'])],
            'notes' => ['nullable', 'string'],
        ]);

        DB::table('gl_periods')->insert([
            'period' => $validated['period'],
            'status' => $validated['status'],
            'closed_at' => $validated['status'] === 'closed' ? now() : null,
            'notes' => $validated['notes'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('accounting.gl-management.periods')->with('success', __('Period created successfully'));
    }

    public function editPeriod(int $period)
    {
        $this->ensureGlManagementSetup();

        return view('admin.accounting.period-form', [
            'title' => 'Edit Period',
            'period' => DB::table('gl_periods')->where('id', $period)->firstOrFail(),
        ]);
    }

    public function updatePeriod(Request $request, int $period)
    {
        $this->ensureGlManagementSetup();

        $validated = $request->validate([
            'period' => ['required', 'string', 'max:20', Rule::unique('gl_periods', 'period')->ignore($period)],
            'status' => ['required', Rule::in(['open', 'closed'])],
            'notes' => ['nullable', 'string'],
        ]);

        DB::table('gl_periods')->where('id', $period)->update([
            'period' => $validated['period'],
            'status' => $validated['status'],
            'closed_at' => $validated['status'] === 'closed' ? now() : null,
            'notes' => $validated['notes'] ?? null,
            'updated_at' => now(),
        ]);

        return redirect()->route('accounting.gl-management.periods')->with('success', __('Period updated successfully'));
    }

    protected function renderPage(string $title, string $group)
    {
        return view('admin.accounting.page', [
            'title' => $title,
            'group' => $group,
        ]);
    }

    protected function baseFilters(Request $request, array $extraKeys = []): array
    {
        $filters = [
            'from' => $request->input('from', now()->subMonth()->toDateString()),
            'to' => $request->input('to', now()->toDateString()),
        ];

        foreach ($extraKeys as $key) {
            $filters[$key] = $request->input($key);
        }

        return $filters;
    }

    protected function applySaleFilters($query, array $filters, string $table = 'sales'): void
    {
        $this->applyDateRange($query, $table . '.sale_date', $filters['from'], $filters['to']);

        if (!empty($filters['warehouse_id'])) {
            $query->where($table . '.warehouse_id', $filters['warehouse_id']);
        }

        if (!empty($filters['client_id'])) {
            $query->where($table . '.client_id', $filters['client_id']);
        }
    }

    protected function applyPurchaseFilters($query, array $filters, string $table = 'purchases'): void
    {
        $this->applyDateRange($query, $table . '.purchase_date', $filters['from'], $filters['to']);

        if (!empty($filters['warehouse_id'])) {
            $query->where($table . '.warehouse_id', $filters['warehouse_id']);
        }

        if (!empty($filters['supplier_id'])) {
            $query->where($table . '.supplier_id', $filters['supplier_id']);
        }
    }

    protected function applyDateRange($query, string $column, ?string $from, ?string $to): void
    {
        if (!empty($from)) {
            $query->whereDate($column, '>=', $from);
        }

        if (!empty($to)) {
            $query->whereDate($column, '<=', $to);
        }
    }

    protected function receivableRows(array $filters): Collection
    {
        return DB::table('sales')
            ->leftJoin('clients', 'clients.id', '=', 'sales.client_id')
            ->leftJoinSub(
                DB::table('payment_sales')
                    ->select('sale_id', DB::raw('SUM(amount) as paid_total'))
                    ->groupBy('sale_id'),
                'payment_totals',
                'payment_totals.sale_id',
                '=',
                'sales.id'
            )
            ->when(!empty($filters['warehouse_id']), fn ($query) => $query->where('sales.warehouse_id', $filters['warehouse_id']))
            ->when(!empty($filters['client_id']), fn ($query) => $query->where('sales.client_id', $filters['client_id']))
            ->when(!empty($filters['from']), fn ($query) => $query->whereDate('sales.sale_date', '>=', $filters['from']))
            ->when(!empty($filters['to']), fn ($query) => $query->whereDate('sales.sale_date', '<=', $filters['to']))
            ->orderByDesc('sales.sale_date')
            ->orderByDesc('sales.id')
            ->get([
                'sales.id',
                'sales.sale_date',
                'sales.total_amount',
                DB::raw("COALESCE(clients.name, 'Walk') as client_name"),
                DB::raw('COALESCE(payment_totals.paid_total, 0) as paid_total'),
            ])
            ->map(function ($row) {
                $grandTotal = (float) $row->total_amount;
                $paid = (float) $row->paid_total;
                $due = max($grandTotal - $paid, 0);

                return [
                    'id' => $row->id,
                    'date' => $row->sale_date,
                    'client_name' => $row->client_name,
                    'grand_total' => $grandTotal,
                    'paid' => $paid,
                    'due' => $due,
                    'status' => $due <= 0 ? 'Paid' : ($paid > 0 ? 'Partial' : 'Unpaid'),
                    'status_class' => $due <= 0 ? 'success' : ($paid > 0 ? 'warning' : 'secondary'),
                ];
            });
    }

    protected function payableRows(array $filters): Collection
    {
        $paymentsByPurchase = Schema::hasColumn('payments', 'purchase_id')
            ? DB::table('payments')
                ->select('purchase_id', DB::raw('SUM(amount) as paid_total'))
                ->groupBy('purchase_id')
            : null;

        $query = DB::table('purchases')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_id');

        if ($paymentsByPurchase) {
            $query->leftJoinSub($paymentsByPurchase, 'payment_totals', 'payment_totals.purchase_id', '=', 'purchases.id');
        }

        return $query
            ->when(!empty($filters['warehouse_id']), fn ($builder) => $builder->where('purchases.warehouse_id', $filters['warehouse_id']))
            ->when(!empty($filters['supplier_id']), fn ($builder) => $builder->where('purchases.supplier_id', $filters['supplier_id']))
            ->when(!empty($filters['from']), fn ($builder) => $builder->whereDate('purchases.purchase_date', '>=', $filters['from']))
            ->when(!empty($filters['to']), fn ($builder) => $builder->whereDate('purchases.purchase_date', '<=', $filters['to']))
            ->orderByDesc('purchases.purchase_date')
            ->orderByDesc('purchases.id')
            ->get([
                'purchases.id',
                'purchases.purchase_date',
                'purchases.total_amount',
                DB::raw("COALESCE(suppliers.name, 'Unknown Supplier') as supplier_name"),
                DB::raw($paymentsByPurchase ? 'COALESCE(payment_totals.paid_total, 0) as paid_total' : '0 as paid_total'),
            ])
            ->map(function ($row) {
                $grandTotal = (float) $row->total_amount;
                $paid = (float) $row->paid_total;
                $due = max($grandTotal - $paid, 0);

                return [
                    'id' => $row->id,
                    'date' => $row->purchase_date,
                    'supplier_name' => $row->supplier_name,
                    'grand_total' => $grandTotal,
                    'paid' => $paid,
                    'due' => $due,
                    'status' => $due <= 0 ? 'Paid' : ($paid > 0 ? 'Partial' : 'Unpaid'),
                    'status_class' => $due <= 0 ? 'success' : ($paid > 0 ? 'warning' : 'secondary'),
                ];
            });
    }

    protected function agingBuckets(Collection $rows, string $amountKey, string $dateKey): array
    {
        $aging = [
            '0_30' => 0.0,
            '31_60' => 0.0,
            '60_plus' => 0.0,
        ];

        $today = Carbon::today();

        foreach ($rows as $row) {
            if (($row[$amountKey] ?? 0) <= 0 || empty($row[$dateKey])) {
                continue;
            }

            $days = Carbon::parse($row[$dateKey])->diffInDays($today);

            if ($days <= 30) {
                $aging['0_30'] += $row[$amountKey];
                continue;
            }

            if ($days <= 60) {
                $aging['31_60'] += $row[$amountKey];
                continue;
            }

            $aging['60_plus'] += $row[$amountKey];
        }

        return $aging;
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

    protected function ensureGlManagementSetup(): void
    {
        if (! Schema::hasTable('gl_accounts')) {
            return;
        }

        if (! DB::table('gl_accounts')->exists()) {
            $seedAccounts = [
                ['code' => '1000', 'name' => 'Assets', 'name_ar' => 'الأصول', 'type' => 'Asset', 'parent_code' => null],
                ['code' => '1100', 'name' => 'Current Assets', 'name_ar' => 'الأصول المتداولة', 'type' => 'Asset', 'parent_code' => '1000'],
                ['code' => '1110', 'name' => 'Cash', 'name_ar' => 'الصندوق', 'type' => 'Asset', 'parent_code' => '1100'],
                ['code' => '1120', 'name' => 'Bank', 'name_ar' => 'البنك', 'type' => 'Asset', 'parent_code' => '1100'],
                ['code' => '1130', 'name' => 'Accounts Receivable', 'name_ar' => 'الذمم المدينة', 'type' => 'Asset', 'parent_code' => '1100'],
                ['code' => '1140', 'name' => 'Inventory', 'name_ar' => 'المخزون', 'type' => 'Asset', 'parent_code' => '1100'],
                ['code' => '1150', 'name' => 'VAT Input (Recoverable)', 'name_ar' => 'ضريبة مدخلات قابلة للاسترداد', 'type' => 'Asset', 'parent_code' => '1100'],
                ['code' => '2000', 'name' => 'Liabilities', 'name_ar' => 'الالتزامات', 'type' => 'Liability', 'parent_code' => null],
                ['code' => '2100', 'name' => 'Current Liabilities', 'name_ar' => 'الالتزامات المتداولة', 'type' => 'Liability', 'parent_code' => '2000'],
                ['code' => '2110', 'name' => 'Accounts Payable', 'name_ar' => 'الذمم الدائنة', 'type' => 'Liability', 'parent_code' => '2100'],
                ['code' => '2120', 'name' => 'VAT Output (Payable)', 'name_ar' => 'ضريبة مخرجات مستحقة', 'type' => 'Liability', 'parent_code' => '2100'],
                ['code' => '3000', 'name' => 'Equity', 'name_ar' => 'حقوق الملكية', 'type' => 'Equity', 'parent_code' => null],
                ['code' => '3100', 'name' => 'Retained Earnings', 'name_ar' => 'الأرباح المبقاة', 'type' => 'Equity', 'parent_code' => '3000'],
                ['code' => '4000', 'name' => 'Revenue', 'name_ar' => 'الإيرادات', 'type' => 'Revenue', 'parent_code' => null],
                ['code' => '4100', 'name' => 'Sales Revenue', 'name_ar' => 'إيرادات المبيعات', 'type' => 'Revenue', 'parent_code' => '4000'],
                ['code' => '5000', 'name' => 'Expenses', 'name_ar' => 'المصروفات', 'type' => 'Expense', 'parent_code' => null],
                ['code' => '5100', 'name' => 'Cost of Goods Sold (COGS)', 'name_ar' => 'تكلفة البضاعة المباعة', 'type' => 'Expense', 'parent_code' => '5000'],
                ['code' => '5999', 'name' => 'General Expense (Unmapped)', 'name_ar' => 'مصروف عام غير مربوط', 'type' => 'Expense', 'parent_code' => '5000'],
            ];

            foreach ($seedAccounts as $account) {
                DB::table('gl_accounts')->insert([
                    'code' => $account['code'],
                    'name' => $account['name'],
                    'name_ar' => $account['name_ar'],
                    'type' => $account['type'],
                    'parent_id' => $account['parent_code'] ? DB::table('gl_accounts')->where('code', $account['parent_code'])->value('id') : null,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if (Schema::hasTable('gl_accounting_mappings') && ! DB::table('gl_accounting_mappings')->where('group_type', 'core')->exists()) {
            foreach ([
                'accounts_receivable' => ['label' => 'Accounts Receivable (AR) Control', 'account_code' => '1130'],
                'accounts_payable' => ['label' => 'Accounts Payable (AP) Control', 'account_code' => '2110'],
                'sales_revenue' => ['label' => 'Sales Revenue', 'account_code' => '4100'],
                'inventory' => ['label' => 'Inventory (Stock)', 'account_code' => '1140'],
                'cogs' => ['label' => 'Cost of Goods Sold (COGS)', 'account_code' => '5100'],
                'vat_output' => ['label' => 'VAT Output (Sales VAT)', 'account_code' => '2120'],
                'vat_input' => ['label' => 'VAT Input (Purchase VAT)', 'account_code' => '1150'],
            ] as $key => $mapping) {
                DB::table('gl_accounting_mappings')->insert([
                    'group_type' => 'core',
                    'mapping_key' => $key,
                    'label' => $mapping['label'],
                    'reference_id' => null,
                    'gl_account_id' => DB::table('gl_accounts')->where('code', $mapping['account_code'])->value('id'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    protected function accountTypes(): array
    {
        return ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'];
    }

    protected function nextJournalEntryNumber(): string
    {
        $nextId = (int) DB::table('gl_journal_entries')->max('id') + 1;

        return 'JE-' . now()->format('Ym') . '-' . str_pad((string) $nextId, 8, '0', STR_PAD_LEFT);
    }

    protected function nextOpeningBalanceNumber(): string
    {
        $nextId = (int) DB::table('gl_opening_balances')->max('id') + 1;

        return 'OB-' . now()->format('Ym') . '-' . str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);
    }

    protected function glSnapshot(array $filters): array
    {
        $salesQuery = DB::table('sales');
        $this->applySaleFilters($salesQuery, $filters);
        $revenue = (float) $salesQuery->sum('total_amount');
        $vatOutput = (float) $salesQuery->sum('tax_amount');

        $receivables = $this->receivableRows($filters)->sum('due');
        $payables = $this->payableRows($filters)->sum('due');

        $purchasesQuery = DB::table('purchases');
        $this->applyPurchaseFilters($purchasesQuery, $filters);
        $vatInput = (float) $purchasesQuery->sum('tax_amount');

        $expensesQuery = DB::table('expenses');
        $this->applyDateRange($expensesQuery, 'expense_date', $filters['from'] ?? null, $filters['to'] ?? null);
        $expenses = (float) $expensesQuery->sum('amount');

        $incomingCash = DB::table('payment_sales')
            ->join('payments', 'payments.id', '=', 'payment_sales.payment_id');
        $this->applyDateRange($incomingCash, 'payments.payment_date', $filters['from'] ?? null, $filters['to'] ?? null);
        $incomingCash = (float) $incomingCash->sum(DB::raw('COALESCE(payment_sales.amount, payments.amount, 0)'));

        $outgoingPayments = DB::table('payments')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('payment_sales')
                    ->whereColumn('payment_sales.payment_id', 'payments.id');
            });
        $this->applyDateRange($outgoingPayments, 'payments.payment_date', $filters['from'] ?? null, $filters['to'] ?? null);
        $outgoingPayments = (float) $outgoingPayments->sum('payments.amount');

        $purchaseDetails = DB::table('purchase_details')
            ->join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id');
        $this->applyPurchaseFilters($purchaseDetails, $filters, 'purchases');
        $inventoryIn = (float) $purchaseDetails->sum('purchase_details.total_price');

        $saleDetails = DB::table('sale_details')
            ->join('sales', 'sales.id', '=', 'sale_details.sale_id');
        $this->applySaleFilters($saleDetails, $filters, 'sales');
        $inventoryOut = (float) $saleDetails->sum('sale_details.total_price');

        $inventory = $inventoryIn - $inventoryOut;
        $cash = $incomingCash - $outgoingPayments - $expenses;
        $netProfit = $revenue - $expenses;
        $assets = $cash + $receivables + $inventory + $vatInput;
        $liabilities = $payables + $vatOutput;
        $equity = $netProfit;

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'net_profit' => $netProfit,
            'cash' => $cash,
            'accounts_receivable' => (float) $receivables,
            'inventory' => $inventory,
            'vat_input' => $vatInput,
            'accounts_payable' => (float) $payables,
            'vat_output' => $vatOutput,
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
        ];
    }

    protected function glAccounts(array $filters): Collection
    {
        $snapshot = $this->glSnapshot($filters);

        return collect([
            ['code' => '1110', 'account' => 'Cash', 'balance' => $snapshot['cash']],
            ['code' => '1130', 'account' => 'Accounts Receivable', 'balance' => $snapshot['accounts_receivable']],
            ['code' => '1140', 'account' => 'Inventory', 'balance' => $snapshot['inventory']],
            ['code' => '1150', 'account' => 'VAT Input (Recoverable)', 'balance' => $snapshot['vat_input']],
            ['code' => '2110', 'account' => 'Accounts Payable', 'balance' => -1 * $snapshot['accounts_payable']],
            ['code' => '2120', 'account' => 'VAT Output (Payable)', 'balance' => -1 * $snapshot['vat_output']],
            ['code' => '3100', 'account' => 'Current Earnings', 'balance' => -1 * $snapshot['net_profit']],
            ['code' => '4100', 'account' => 'Sales Revenue', 'balance' => -1 * $snapshot['revenue']],
            ['code' => '5100', 'account' => 'Operating Expenses', 'balance' => $snapshot['expenses']],
        ])->map(function ($row) {
            $balance = (float) $row['balance'];

            return [
                'code' => $row['code'],
                'account' => $row['account'],
                'debit' => $balance > 0 ? $balance : 0.0,
                'credit' => $balance < 0 ? abs($balance) : 0.0,
                'balance' => $balance,
            ];
        });
    }

    protected function glVatLines(array $filters): Collection
    {
        $salesLines = DB::table('sales')
            ->when(!empty($filters['from']), fn ($query) => $query->whereDate('sale_date', '>=', $filters['from']))
            ->when(!empty($filters['to']), fn ($query) => $query->whereDate('sale_date', '<=', $filters['to']))
            ->orderByDesc('sale_date')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'date' => $row->sale_date,
                'entry_no' => 'SAL-' . $row->id,
                'description' => 'VAT collected on sale',
                'debit' => 0.0,
                'credit' => (float) ($row->tax_amount ?? 0),
            ]);

        $purchaseLines = DB::table('purchases')
            ->when(!empty($filters['from']), fn ($query) => $query->whereDate('purchase_date', '>=', $filters['from']))
            ->when(!empty($filters['to']), fn ($query) => $query->whereDate('purchase_date', '<=', $filters['to']))
            ->orderByDesc('purchase_date')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'date' => $row->purchase_date,
                'entry_no' => 'PUR-' . $row->id,
                'description' => 'VAT paid on purchase',
                'debit' => (float) ($row->tax_amount ?? 0),
                'credit' => 0.0,
            ]);

        return $salesLines
            ->concat($purchaseLines)
            ->sortByDesc('date')
            ->take(8)
            ->values();
    }
}
