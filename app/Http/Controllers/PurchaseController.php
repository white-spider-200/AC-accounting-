<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\PurchaseDetail;
use App\Models\PurchaseStatus;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $search = $request->q;
        $paymentTypes = PaymentType::all();
        $purchases = Purchase::with(['statusName', 'paymentStatus'])->orderBy('id', 'desc');
        if (isset($request->q) and !empty($request->q)) {
            $purchases = $purchases->where('notes', 'like',  "%{$request->q}%")
                ->orWhere('id', $search);
        }
        if (isset($request->from_date) and !empty($request->from_date)) {
            $purchases = $purchases->where('purchase_date', '>',  date('Y-m-d', strtotime("-1 day", strtotime($request->from_date))));
        }
        if (isset($request->to_date) and !empty($request->to_date)) {
            $purchases = $purchases->where('purchase_date', '<',  date('Y-m-d',  strtotime("+1 day", strtotime($request->to_date))));
        }
        if (isset($request->warehouse_id) and !empty($request->warehouse_id)) {
            $purchases = $purchases->where('warehouse_id',  $request->warehouse_id);
        }
        if (isset($request->supplier_id) and !empty($request->supplier_id)) {
            $purchases = $purchases->where('supplier_id',  $request->supplier_id);
        }
        if (isset($request->status) and !empty($request->status)) {
            $purchases = $purchases->where('purchase_status_id',  $request->status);
        }
        $suppliers = Supplier::all();
        $warehouses = Warehouse::all();
        $purchaseStatuses = PurchaseStatus::all();

        $pageLimit = 10;
        if (isset($request->csv)) {
            $pageLimit = 5000;
        }
        $purchases = $purchases->paginate($pageLimit);
        $count = $purchases->total();
        if (isset($request->csv) and !empty($request->csv)) {

            $filename = uniqid() . ".csv";
            $handle = fopen($filename, 'w+');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, array(__('ID'), __('Total') ,  __('Paid'), __('Due'),__('Payment Status'), __('Date'), __('Status')));

            foreach ($purchases as $row) {

                fputcsv($handle, array($row->id, number_format($row->grand_total, 2), number_format($row->paid, 2), number_format($row->due, 2), (app()->getLocale() == 'ar' ? @$row->paymentStatus->label_ar : @$row->paymentStatus->label_en) , $row->real_date, ((app()->getLocale() == 'ar') ? @$row->statusName->label_ar : @$row->statusName->label_en)));
            }

            fclose($handle);
            $headers = array(

                'Content-Encoding' => 'utf-8',
                'Content-Type' => 'text/csv; charset=utf-8'
            );



            return \Response::download($filename, 'purchases.csv', $headers);
        }
        return view('admin.purchases.index', compact('paymentTypes', 'purchases', 'suppliers', 'warehouses', 'purchaseStatuses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $warehouses = Warehouse::all();
        $suppliers = Supplier::all();
        $purchaseStatuses = PurchaseStatus::all();
        return view('admin.purchases.create', compact('purchaseStatuses', 'warehouses', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'comment' => 'nullable',
            'status' => 'nullable|min:0',
            'discount' => 'nullable|min:0',
            'shippment_price' => 'nullable|min:0',
            'order_tax' => 'nullable|min:0',
            'real_date' => 'nullable|min:0',
            'grand_total' => 'nullable|min:0',
            'supplier_id' => 'required',
            'warehouse_id' => 'required',
            'tax_whole_purchase_send' => 'nullable|min:0'
        ]);
        $id = Purchase::create($validatedData);
        foreach ($request->json()->all()['data'] as $k => $value) {

            if (is_array($value)) {
                DB::beginTransaction();
                $warehouse = Warehouse::find($request->warehouse_id);
                $currentQuantity = optional(optional($warehouse->products()->where('product_id', $k)->withPivot('qty')->first())->pivot)->qty ?? 0;
                $newQuantity = $currentQuantity + $value['qty'];
                $warehouse->products()->syncWithoutDetaching([
                    $k => ['qty' => $newQuantity]
                ]);
                PurchaseDetail::create([
                    'in_warehouse'=>$newQuantity,'product_id' => $k, 'supplier_id' => $request->supplier_id,
                    'warehouse_id' => $request->warehouse_id, 'tax' => $value['original_tax'],
                    'discount' => $value['discount'], 'price' => $value['cost_price'],
                    'total' => $value['subtotal'], 'qty' => $value['qty'], 'purchase_id' => $id->id,
                    'discount_type' => $value['discount-type'], 'currency' => $value['currency'], 'tax_value_beforeqty' => $value['wholetaxbeforeqty'], 'tax_price' => ($value['wholetaxbeforeqty'] * $value['qty'])
                ]);

                DB::commit();
            }
        }

        return session()->flash('success', __('Purchase Added Successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Purchases $purchase
     * @return \Illuminate\Http\Response
     */
    public function edit(Purchase $purchase)
    {
        $warehouses = Warehouse::all();
        $suppliers = Supplier::all();
        $purchaseStatuses = PurchaseStatus::all();
        $purchaseDetails = PurchaseDetail::where('purchase_id', $purchase->id)->get();

        return view('admin.purchases.edit', compact('purchaseDetails', 'purchase', 'purchaseStatuses', 'warehouses', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Purchase $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Purchase $purchase)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'comment' => 'nullable',
            'status' => 'nullable|min:0',
            'discount' => 'nullable|min:0',
            'shippment_price' => 'nullable|min:0',
            'order_tax' => 'nullable|min:0',
            'real_date' => 'nullable|min:0',
            'grand_total' => 'nullable|min:0',
            'supplier_id' => 'required',
            'warehouse_id' => 'required',
            'tax_whole_purchase_send' => 'nullable|min:0'
        ]);
        DB::beginTransaction();

        try {
            $purchase->update($validatedData);
            $id = $purchase->id;
            /** to modify warehouse qty will get all old quantities and remove from stored qty */
            $oldQtys = PurchaseDetail::where('purchase_id', $id)->get(['product_id','qty']);
            foreach($oldQtys as $old){
                    $oldQty = $old-> qty ;
                    $productId = $old->product_id;
                    $warehouse = Warehouse::find($request->warehouse_id);
                    $currentQuantity = optional(optional($warehouse->products()->where('product_id', $productId)->withPivot('qty')->first())->pivot)->qty ?? 0;
                    $newQuantity = $currentQuantity - $oldQty;
                    if($newQuantity < 0){
                        $newQuantity  = 0 ;
                    }
                    $warehouse->products()->syncWithoutDetaching([
                        $productId => ['qty' => $newQuantity]
                    ]);
            }
            PurchaseDetail::where('purchase_id', $id)->delete();
            foreach ($request->json()->all()['data'] as $k => $value) {

                if (is_array($value)) {
                    $warehouse = Warehouse::find($request->warehouse_id);
                    $currentQuantity = optional(optional($warehouse->products()->where('product_id', $k)->withPivot('qty')->first())->pivot)->qty ?? 0;
                    $newQuantity = $currentQuantity + $value['qty'];
                    $warehouse->products()->syncWithoutDetaching([
                        $k => ['qty' => $newQuantity]
                    ]);
                    PurchaseDetail::create([
                        'in_warehouse'=>$newQuantity,'product_id' => $k, 'supplier_id' => $request->supplier_id,
                        'warehouse_id' => $request->warehouse_id, 'tax' => $value['original_tax'],
                        'discount' => $value['discount'], 'price' => $value['cost_price'],
                        'total' => $value['subtotal'], 'qty' => $value['qty'], 'purchase_id' => $id,
                        'discount_type' => $value['discount-type'], 'currency' => $value['currency'], 'tax_value_beforeqty' => $value['wholetaxbeforeqty'], 'tax_price' => ($value['wholetaxbeforeqty'] * $value['qty'])
                    ]);

                }
            }

            DB::commit();
            return session()->flash('success', __('Purchase Added Successfully'));
        } catch (\Exception $e) {
            // Rollback the transaction if any operation failed
            DB::rollback();
            \Log::error($e);
            return session()->flash('error', __('Something Wrong'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Purchase $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        $purchaseId = $purchase->id;
        if ($purchase->delete()) {
            PurchaseDetail::where('purchase_id', $purchaseId)->delete();
        }

        // Redirect back to the index page with a success message
        return redirect()->route('purchases.index')
            ->with('success',  __('messages.Purchase_deleted_successfully'));
    }
}
