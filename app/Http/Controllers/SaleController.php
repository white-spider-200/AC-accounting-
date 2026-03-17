<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Client;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\SaleDetail;
use App\Models\SaleStatus;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use App\Models\PaymentSale;
use App\Models\ProductsCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
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
        $sales = Sale::with(['statusName', 'paymentStatus'])->orderBy('id', 'desc');
        if (isset($request->q) and !empty($request->q)) {
            $sales = $sales->where('notes', 'like',  "%{$request->q}%")
                ->orWhere('id', $search);
        }
        if (isset($request->from_date) and !empty($request->from_date)) {
            $sales = $sales->where('sale_date', '>',  date('Y-m-d', strtotime("-1 day", strtotime($request->from_date))));
        }
        if (isset($request->to_date) and !empty($request->to_date)) {
            $sales = $sales->where('sale_date', '<',  date('Y-m-d',  strtotime("+1 day", strtotime($request->to_date))));
        }
        if (isset($request->warehouse_id) and !empty($request->warehouse_id)) {
            $sales = $sales->where('warehouse_id',  $request->warehouse_id);
        }
        if (isset($request->client_id) and !empty($request-> client_id)) {
            $sales = $sales->where('client_id',  $request-> client_id);
        }
        if (isset($request->status) and !empty($request->status)) {
            $sales = $sales->where('sale_status_id',  $request->status);
        }
        $clients = Client::all();
        $warehouses = Warehouse::all();
        $salesStatuses = SaleStatus::all();

        $pageLimit = 10;
        if (isset($request->csv)) {
            $pageLimit = 5000;
        }
        $sales = $sales->paginate($pageLimit);
        $count = $sales->total();
        if (isset($request->csv) and !empty($request->csv)) {

            $filename = uniqid() . ".csv";
            $handle = fopen($filename, 'w+');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, array(__('ID'), __('Total') ,  __('Paid'), __('Due'),__('Payment Status'), __('Date'), __('Status')));

            foreach ($sales as $row) {

                fputcsv($handle, array($row->id, number_format($row->grand_total, 2), number_format($row->paid, 2), number_format($row->due, 2), (app()->getLocale() == 'ar' ? @$row->paymentStatus->label_ar : @$row->paymentStatus->label_en) , $row->real_date, ((app()->getLocale() == 'ar') ? @$row->statusName->label_ar : @$row->statusName->label_en)));
            }

            fclose($handle);
            $headers = array(

                'Content-Encoding' => 'utf-8',
                'Content-Type' => 'text/csv; charset=utf-8'
            );
            return \Response::download($filename, 'purchases.csv', $headers);
        }
        return view('admin.sales.index', compact('paymentTypes', 'sales', 'clients', 'warehouses', 'salesStatuses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if(auth()->user()->type == 1){
            $warehouses = Warehouse::all();
        }else{
            $warehouses = Warehouse::whereIn('id', auth()->user()->warehouses->pluck('id'))->get();
        }

        $clients = Client::all();
        $salesStatuses = SaleStatus::all();
        $suggestions = [];
        $paymentTypes = PaymentType::all();
        $tempId = uniqid();
        $defaultWareHouse = @$warehouses[0]->id;
        $categories = [];
        if(isset(request()->warehouse_id) and !empty(request()->warehouse_id)){
            $defaultWareHouse = request()->warehouse_id;
        }
        if(isset( request()->pos )){
            $perPage = 10; // Number of items per page
            $page = 1; // Desired page number
            $result = Product::getProductsByWarehouse($defaultWareHouse, $perPage, $page);
            $suggestions = $result['data'];
            $total = $result['total'];
            $perPage = $result['perPage'];
            $currentPage = $result['currentPage'];
            $categories  = ProductsCategory::all();

        }
        return view('admin.sales.create', compact('categories','defaultWareHouse','tempId','salesStatuses','paymentTypes', 'warehouses', 'clients','suggestions'));
    }
    public function getProductsByWarehouse(Request $request)
    {
        $defaultWarehouseId = $request->input('warehouse_id');
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $category = $request->input('category', 0);
        $result = Product::getProductsByWarehouse($defaultWarehouseId, $perPage, $page,$category);
        $suggestions = $result['data'];
        return view('admin.sales.products', compact('suggestions'));

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
            'client_id' => 'required',
            'warehouse_id' => 'required',
            'tax_whole_sale_send' => 'nullable|min:0'
        ]);
        $id = Sale::create($validatedData);

        foreach ($request->json()->all()['data'] as $k => $value) {

            if (is_array($value)) {
                DB::beginTransaction();
                $warehouse = Warehouse::find($request-> warehouse_id);
                $currentQuantity = optional(optional($warehouse->products()->where('product_id', $k)->withPivot('qty')->first())->pivot)->qty ?? 0;
                $newQuantity = $currentQuantity - $value['qty'];
                $warehouse->products()->syncWithoutDetaching([
                    $k => ['qty' => $newQuantity]
                ]);
                SaleDetail::create([
                    'in_warehouse'=>$newQuantity,'product_id' => $k, 'client_id' => $request-> client_id,
                    'warehouse_id' => $request->warehouse_id, 'tax' => $value['original_tax'],
                    'discount' => $value['discount'], 'price' => $value['cost_price'],
                    'total' => $value['subtotal'], 'qty' => $value['qty'], 'sale_id' => $id->id,
                    'discount_type' => $value['discount-type'], 'currency' => $value['currency'], 'tax_value_beforeqty' => $value['wholetaxbeforeqty'], 'tax_price' => ($value['wholetaxbeforeqty'] * $value['qty'])
                ]);

                DB::commit();
            }
        }
        if(isset(request()->paid)){
         //in case of pos

            $request ->merge([
                'sale_id' => $id->id
            ])  ;

            $data = PaymentSale::createPayment($request);
            //to get invoice
            $sale = Sale::find($id->id);

            return view('admin.paymentsales.invoice', compact('sale'));

        }
        return session()->flash('success', __('messages.sale_added_successfully'));
    }
    public function invoice($id){
        // this invoice depend on sale after sale happen invoice containing all details of products
        // related to POS so most of processes have one payment
        $sale = Sale::find($id);

        return view('admin.paymentsales.invoice', compact('sale'));

    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  Purchases $purchase
     * @return \Illuminate\Http\Response
     */
    public function edit(Sale $sale)
    {
        if(auth()->user()->type == 1){
            $warehouses = Warehouse::all();
        }else{
            $warehouses = Warehouse::whereIn('id', auth()->user()->warehouses->pluck('id'))->get();
        }
        $clients = Client::all();
        $salesStatuses = SaleStatus::all();
        $saleDetails = SaleDetail::where('sale_id', $sale-> id)->get();

        return view('admin.sales.edit', compact('saleDetails', 'sale', 'salesStatuses', 'warehouses', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Purchase $sale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sale $sale)
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
            'client_id' => 'required',
            'warehouse_id' => 'required',
            'tax_whole_sale_send' => 'nullable|min:0'
        ]);
        DB::beginTransaction();
        //to fix the deduction of new grand total if user add tax
        try {
            $sale->update($validatedData);
            $id = $sale-> id;
            /** to modify warehouse qty will get all old quantities and remove from stored qty */
            $oldQtys = SaleDetail::where('sale_id', $id)->get(['product_id','qty']);
            foreach($oldQtys as $old){
                    $oldQty = $old-> qty ;
                    $productId = $old->product_id;
                    $warehouse = Warehouse::find($request->warehouse_id);
                    $currentQuantity = optional(optional($warehouse->products()->where('product_id', $productId)->withPivot('qty')->first())->pivot)->qty ?? 0;
                    $newQuantity = $currentQuantity + $oldQty;
                    if($newQuantity < 0){
                        $newQuantity  = 0 ;
                    }
                    $warehouse->products()->syncWithoutDetaching([
                        $productId => ['qty' => $newQuantity]
                    ]);
            }
            SaleDetail::where('sale_id', $id)->delete();
            foreach ($request->json()->all()['data'] as $k => $value) {

                if (is_array($value)) {
                    $warehouse = Warehouse::find($request->warehouse_id);
                    $currentQuantity = optional(optional($warehouse->products()->where('product_id', $k)->withPivot('qty')->first())->pivot)->qty ?? 0;
                    $newQuantity = $currentQuantity - $value['qty'];
                    if ($newQuantity < 0) {
                        $newQuantity = 0;
                    }
                    $warehouse->products()->syncWithoutDetaching([
                        $k => ['qty' => $newQuantity]
                    ]);
                    SaleDetail::create([
                        'in_warehouse'=>$newQuantity,'product_id' => $k, 'client_id' => $request-> client_id,
                        'warehouse_id' => $request->warehouse_id, 'tax' => $value['original_tax'],
                        'discount' => $value['discount'], 'price' => $value['cost_price'],
                        'total' => $value['subtotal'], 'qty' => $value['qty'], 'sale_id' => $id,
                        'discount_type' => $value['discount-type'], 'currency' => $value['currency'], 'tax_value_beforeqty' => $value['wholetaxbeforeqty'], 'tax_price' => ($value['wholetaxbeforeqty'] * $value['qty'])
                    ]);

                }
            }

            DB::commit();
            return session()->flash('success', __('Sale Added Successfully'));
        } catch (\Exception $e) {
            // Rollback the transaction if any operation failed
            DB::rollback();
            \Log::error($e);
            //dd($e);
            return session()->flash('error', __('Something Wrong'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Sale $sale
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sale $sale)
    {
        $saleId = $sale-> id;
        if ($sale-> delete()) {
            SaleDetail::where('sale_id', $saleId)->delete();
        }

        // Redirect back to the index page with a success message
        return redirect()->route('sales.index')
            ->with('success',  __('messages.sale_deleted_successfully'));
    }
}
