<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Purchase;
use App\Models\PaymentStatus;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'purchase_id' => 'required',
            'payment_type_id' => 'required',
            'paid' => 'required|numeric|min:0.01',
            'due_date' => 'nullable',
            'real_date' => 'nullable|string',
            'comment' => 'nullable'
        ]);

        try {
            DB::beginTransaction();

            $payment = Payment::create([
                'purchase_id' => $validatedData['purchase_id'],
                'user_id' => auth()->id(),
                'payment_type_id' => $validatedData['payment_type_id'],
                'payment_status_id' => 3,
                'amount' => $validatedData['paid'],
                'payment_date' => $validatedData['real_date'] ?: now()->toDateString(),
                'notes' => $validatedData['comment'] ?? null,
            ]);

            $purchase = Purchase::findOrFail($request->purchase_id);
            $purchase->payment_status_id = $purchase->due <= 0 ? 3 : 2;
            $purchase->save();

            $returnedStatus = PaymentStatus::find($purchase->payment_status_id);
            $data = [
                'message' => __('Payment Added Successfully'),
                'newdue'=> $purchase->due,
                'newpaid'=> $purchase->paid,
                'returnedstatusclass'=> $returnedStatus?->class_name ?? 'secondary',
                'returnedstatus'=> (app()->getLocale() == 'ar' ) ? ($returnedStatus?->label_ar ?? 'Unknown') : ($returnedStatus?->label_en ?? 'Unknown'),
                'payment_id' => $payment->id,
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error($e);
            $data = ['message' => __('Payment Not Added Something Wrong')];
        }



        return response()->json($data);
    }
    public function purchasePayments($id = null){
        $payments = Payment::with(['paymentType', 'user'])->where('purchase_id',$id)->get();
        return view('admin.payments.list', compact('payments'));

    }
    public function singlePayment($id){
        $payment = Payment::find($id);

        $purchase = Purchase::find($payment->purchase_id);

        return view('admin.payments.singlepayment', compact('payment','purchase'));

        }
}
