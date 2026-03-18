<?php

namespace App\Services\Accounting;

use App\Models\GlAccount;
use Illuminate\Support\Collection;

class TrialBalanceService
{
    public function __construct(private readonly LedgerQueryService $ledgerQueryService)
    {
    }

    public function generate(?string $from = null, ?string $to = null): array
    {
        $accounts = GlAccount::query()->orderBy('code')->get()->keyBy('id');

        $movementByAccount = $this->ledgerQueryService
            ->linesQuery($from, $to)
            ->get()
            ->groupBy('gl_account_id')
            ->map(fn (Collection $lines) => [
                'debit' => (float) $lines->sum('debit'),
                'credit' => (float) $lines->sum('credit'),
            ]);

        $openingByAccount = collect();
        if ($from) {
            $openingByAccount = $this->ledgerQueryService
                ->openingLinesQuery($from)
                ->get()
                ->groupBy('gl_account_id')
                ->map(fn (Collection $lines) => [
                    'debit' => (float) $lines->sum('debit'),
                    'credit' => (float) $lines->sum('credit'),
                ]);
        }

        $rows = $accounts->map(function (GlAccount $account) use ($movementByAccount, $openingByAccount) {
            $opening = $openingByAccount->get($account->id, ['debit' => 0.0, 'credit' => 0.0]);
            $movement = $movementByAccount->get($account->id, ['debit' => 0.0, 'credit' => 0.0]);

            $debitTotal = (float) $opening['debit'] + (float) $movement['debit'];
            $creditTotal = (float) $opening['credit'] + (float) $movement['credit'];

            $normal = strtolower((string) ($account->normal_balance ?: (in_array(strtolower($account->type), ['asset', 'expense'], true) ? 'debit' : 'credit')));
            $raw = $debitTotal - $creditTotal;
            $natural = $normal === 'credit' ? (-1 * $raw) : $raw;

            return [
                'id' => $account->id,
                'code' => $account->code,
                'account' => $account->name,
                'type' => $account->type,
                'normal_balance' => $normal,
                'opening_debit' => (float) $opening['debit'],
                'opening_credit' => (float) $opening['credit'],
                'movement_debit' => (float) $movement['debit'],
                'movement_credit' => (float) $movement['credit'],
                'debit' => $raw > 0 ? $raw : 0.0,
                'credit' => $raw < 0 ? abs($raw) : 0.0,
                'balance' => $raw,
                'natural_balance_amount' => $natural,
            ];
        })->filter(fn (array $row) =>
            abs($row['opening_debit']) > 0.0001
            || abs($row['opening_credit']) > 0.0001
            || abs($row['movement_debit']) > 0.0001
            || abs($row['movement_credit']) > 0.0001
        )->values();

        return [
            'rows' => $rows,
            'totals' => [
                'debit' => (float) $rows->sum('debit'),
                'credit' => (float) $rows->sum('credit'),
                'balance' => (float) $rows->sum('debit') - (float) $rows->sum('credit'),
            ],
        ];
    }
}
