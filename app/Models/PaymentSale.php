<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaymentSale extends Model
{
    use HasFactory;

    protected $table = 'payment_sales';

    protected $fillable = ['payment_id', 'sale_id', 'amount'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function getPaidAttribute()
    {
        return (float) ($this->attributes['amount'] ?? $this->payment?->amount ?? 0);
    }

    public function getAmountAttribute($value)
    {
        return (float) $value;
    }

    public function getRealDateAttribute()
    {
        return $this->payment?->payment_date;
    }

    public function getUserAttribute()
    {
        return $this->payment?->user;
    }

    public function getPaymentTypeAttribute()
    {
        return $this->payment?->paymentType;
    }

    public static function createPayment($request, $saleId = null)
    {
        $validatedData = $request->validate([
            'sale_id' => 'required',
            'payment_type_id' => 'required',
            'paid' => 'required|numeric|min:0.01',
            'due_date' => 'nullable',
            'real_date' => 'nullable|string',
            'comment' => 'nullable',
        ]);

        $validatedData['user_id'] = auth()->id();

        try {
            DB::beginTransaction();

            $sale = Sale::findOrFail($saleId ?: $request->sale_id);

            $payment = Payment::create([
                'user_id' => $validatedData['user_id'],
                'payment_type_id' => $validatedData['payment_type_id'],
                'payment_status_id' => 3,
                'amount' => $validatedData['paid'],
                'payment_date' => $validatedData['real_date'] ?: now()->toDateString(),
                'notes' => $validatedData['comment'] ?? null,
            ]);

            $pivot = self::create([
                'payment_id' => $payment->id,
                'sale_id' => $sale->id,
                'amount' => $validatedData['paid'],
            ]);

            $sale->payment_status_id = $sale->due <= 0 ? 3 : 2;
            $sale->save();

            $returnedStatus = PaymentStatus::find($sale->payment_status_id);

            DB::commit();

            return [
                'message' => __('messages.payment_added_successfully'),
                'newdue' => $sale->due,
                'newpaid' => $sale->paid,
                'returnedstatusclass' => $returnedStatus?->class_name ?? 'secondary',
                'status' => 1,
                'returnedstatus' => (app()->getLocale() == 'ar') ? ($returnedStatus?->label_ar ?? 'Unknown') : ($returnedStatus?->label_en ?? 'Unknown'),
                'payment_id' => $pivot->id,
            ];
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error($e);

            return ['message' => __('messages.payment_not_added_something_wrong'), 'status' => 0];
        }
    }
}
