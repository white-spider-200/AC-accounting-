<?php

namespace App\Services\Accounting;

class ProfitLossService
{
    public function __construct(private readonly TrialBalanceService $trialBalanceService)
    {
    }

    public function generate(?string $from, ?string $to): array
    {
        $trial = $this->trialBalanceService->generate($from, $to);
        $rows = $trial['rows'];

        $revenue = (float) $rows
            ->where('type', 'Revenue')
            ->sum(fn (array $row) => $row['credit'] - $row['debit']);

        $cogs = (float) $rows
            ->where('code', '5100')
            ->sum(fn (array $row) => $row['debit'] - $row['credit']);

        $operatingExpenses = (float) $rows
            ->where('type', 'Expense')
            ->where('code', '!=', '5100')
            ->sum(fn (array $row) => $row['debit'] - $row['credit']);

        $grossProfit = $revenue - $cogs;
        $netProfit = $grossProfit - $operatingExpenses;

        return [
            'revenue' => $revenue,
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'operating_expenses' => $operatingExpenses,
            'net_profit' => $netProfit,
            'summary' => [
                ['label' => 'Revenue', 'value' => $revenue],
                ['label' => 'COGS', 'value' => $cogs],
                ['label' => 'Gross Profit', 'value' => $grossProfit],
                ['label' => 'Expenses', 'value' => $operatingExpenses],
                ['label' => 'Net Profit', 'value' => $netProfit, 'highlight' => true],
            ],
        ];
    }
}
