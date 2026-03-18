<?php

namespace App\Services\Accounting;

use App\Models\GlAccount;

class BalanceSheetService
{
    public function __construct(private readonly TrialBalanceService $trialBalanceService)
    {
    }

    public function generate(string $asOfDate): array
    {
        $trial = $this->trialBalanceService->generate(null, $asOfDate);
        $rows = collect($trial['rows']);
        $accounts = GlAccount::query()->pluck('name', 'code');

        $assets = (float) $rows
            ->where('type', 'Asset')
            ->sum(fn (array $row) => $row['debit'] - $row['credit']);

        $liabilities = (float) $rows
            ->where('type', 'Liability')
            ->sum(fn (array $row) => $row['credit'] - $row['debit']);

        $equity = (float) $rows
            ->where('type', 'Equity')
            ->sum(fn (array $row) => $row['credit'] - $row['debit']);

        $currentEarnings = (float) $rows
            ->where('type', 'Revenue')
            ->sum(fn (array $row) => $row['credit'] - $row['debit'])
            - (float) $rows
                ->where('type', 'Expense')
                ->sum(fn (array $row) => $row['debit'] - $row['credit']);

        $equity += $currentEarnings;

        $sheetRows = $rows
            ->whereIn('type', ['Asset', 'Liability', 'Equity'])
            ->map(function (array $row) use ($accounts) {
                $isAsset = $row['type'] === 'Asset';

                return [
                    'type' => $row['type'],
                    'code' => $row['code'],
                    'account' => $accounts->get($row['code'], $row['account']),
                    'balance' => $isAsset ? ($row['debit'] - $row['credit']) : ($row['credit'] - $row['debit']),
                ];
            })
            ->values();

        return [
            'rows' => $sheetRows,
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'liabilities_and_equity' => $liabilities + $equity,
            'current_earnings' => $currentEarnings,
        ];
    }
}
