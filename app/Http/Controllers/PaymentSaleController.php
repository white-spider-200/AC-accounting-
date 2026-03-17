<?php

namespace App\Http\Controllers;

use App\Models\PaymentSale;
use App\Models\Sale;
use App\Models\PaymentStatus;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentSaleController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = PaymentSale::createPayment($request);

        return response()->json($data);
    }
    public function salePayments($id = null){
        $payments = PaymentSale::with(['payment.paymentType', 'payment.user'])->where('sale_id',$id)->get();
        return view('admin.paymentsales.list', compact('payments'));

    }
    public function singlePayment($id){
        $payment = PaymentSale::find($id);

        $sale = Sale::find($payment->sale_id);

        return view('admin.paymentsales.singlepayment', compact('payment','sale'));

        }

}
