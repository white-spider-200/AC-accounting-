<?php

namespace App\Services\Accounting;

use App\Models\GlAccount;
use App\Models\GlAccountingMapping;
use Illuminate\Validation\ValidationException;

class MappingService
{
    public function coreAccount(string $key): GlAccount
    {
        $mapping = GlAccountingMapping::query()
            ->where('group_type', 'core')
            ->where(function ($query) use ($key) {
                $query->where('mapping_key', $key)->orWhere('key', $key);
            })
            ->where('is_active', true)
            ->first();

        $accountId = $mapping?->debit_account_id ?: $mapping?->gl_account_id;

        if (! $accountId) {
            throw ValidationException::withMessages([
                'mapping' => "Missing active core accounting mapping for key [{$key}]",
            ]);
        }

        return GlAccount::query()->findOrFail($accountId);
    }

    public function paymentTypeAccount(?int $paymentTypeId): GlAccount
    {
        if ($paymentTypeId) {
            $mapping = GlAccountingMapping::query()
                ->where('group_type', 'payment_type')
                ->where('reference_id', $paymentTypeId)
                ->where('is_active', true)
                ->first();

            $accountId = $mapping?->debit_account_id ?: $mapping?->gl_account_id;
            if ($accountId) {
                return GlAccount::query()->findOrFail($accountId);
            }
        }

        return $this->optionalCoreAccount(['cash_account', 'sale_cash', 'bank_account'])
            ?? GlAccount::query()->where('code', '1110')->firstOrFail();
    }

    public function expenseCategoryAccount(?int $expenseCategoryId): GlAccount
    {
        if ($expenseCategoryId) {
            $mapping = GlAccountingMapping::query()
                ->where('group_type', 'expense_category')
                ->where('reference_id', $expenseCategoryId)
                ->where('is_active', true)
                ->first();

            $accountId = $mapping?->debit_account_id ?: $mapping?->gl_account_id;
            if ($accountId) {
                return GlAccount::query()->findOrFail($accountId);
            }
        }

        return $this->optionalCoreAccount(['expense_default'])
            ?? GlAccount::query()->where('code', '5999')->firstOrFail();
    }

    public function optionalCoreAccount(array $keys): ?GlAccount
    {
        $mapping = GlAccountingMapping::query()
            ->where('group_type', 'core')
            ->where(function ($query) use ($keys) {
                $query->whereIn('mapping_key', $keys)->orWhereIn('key', $keys);
            })
            ->where('is_active', true)
            ->first();

        $accountId = $mapping?->debit_account_id ?: $mapping?->gl_account_id;

        return $accountId ? GlAccount::query()->find($accountId) : null;
    }
}
