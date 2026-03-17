<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Adjustment;
use App\Models\Warehouse;
use App\Models\AdjustmentDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $search = $request->q;

        $adjustments = Adjustment::orderBy('id', 'desc');
        if (isset($request->q) and !empty($request->q)) {
            $adjustments = $adjustments->where('notes', 'like',  "%{$request->q}%")
                ->orWhere('id', $search);
        }
        if (isset($request->from_date) and !empty($request->from_date)) {
            $adjustments = $adjustments->where('adjustment_date', '>',  date('Y-m-d', strtotime("-1 day", strtotime($request->from_date))));
        }
        if (isset($request->to_date) and !empty($request->to_date)) {
            $adjustments = $adjustments->where('adjustment_date', '<',  date('Y-m-d',  strtotime("+1 day", strtotime($request->to_date))));
        }
        if (isset($request->warehouse_id) and !empty($request->warehouse_id)) {
            $adjustments = $adjustments->where('warehouse_id',  $request->warehouse_id);
        }
        $warehouses = Warehouse::all();
        $pageLimit = 10;
        if (isset($request->csv)) {
            $pageLimit = 5000;
        }
        $adjustments = $adjustments->paginate($pageLimit);
        $count = $adjustments->total();
        if (isset($request->csv) and !empty($request->csv)) {

            $filename = uniqid() . ".csv";
            $handle = fopen($filename, 'w+');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, array(__('ID'), __('Total') , __('Date')));

            foreach ($adjustments as $row) {

                fputcsv($handle, array($row->id, $row-> total_products, $row-> real_date));
            }

            fclose($handle);
            $headers = array(

                'Content-Encoding' => 'utf-8',
                'Content-Type' => 'text/csv; charset=utf-8'
            );



            return \Response::download($filename, 'adjustments.csv', $headers);
        }
        return view('admin.adjustments.index', compact( 'adjustments', 'warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $warehouses = Warehouse::all();
        return view('admin.adjustments.create', compact( 'warehouses'));
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
            'real_date' => 'nullable|min:0',
            'warehouse_id' => 'required',
            'total_products' => 'nullable|min:0'
        ]);
        $id = Adjustment::create($validatedData);
        foreach ($request->json()->all()['data'] as $k => $value) {

            if (is_array($value)) {

                DB::beginTransaction();
                $warehouse = Warehouse::find($request->warehouse_id);
                $newQuantity = optional(optional($warehouse->products()->where('product_id', $k)->withPivot('qty')->first())->pivot)->qty ?? 0;
                if($value['adjustment_type'] == 1){
                    $newQuantity = $newQuantity + $value['qty'];
                }else{
                    if($value['qty'] > $newQuantity){
                        return false;
                    }else{
                        $newQuantity = $newQuantity - $value['qty'];
                    }
                }
                 // depend on adjustment_type will add or subtract
                $warehouse->products()->syncWithoutDetaching([
                    $k => ['qty' => $newQuantity]
                ]);
                AdjustmentDetail::create([
                    'in_warehouse'=>$newQuantity,'product_id' => $k,
                    'warehouse_id' => $request->warehouse_id, 'qty' => $value['qty'], 'adjustment_id' => $id->id
                ]);

                DB::commit();
            }
        }

        return session()->flash('success', __('Adjustment Added Successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Adjustment $adjustment
     * @return \Illuminate\Http\Response
     */
    public function edit(Adjustment $adjustment)
    {
        $warehouses = Warehouse::all();

        $details = AdjustmentDetail::where('adjustment_id', $adjustment->id)->get();

        return view('admin.adjustments.edit', compact( 'adjustment', 'warehouses','details'));
    }
    public function show(Adjustment $adjustment)
    {

        $details = AdjustmentDetail::where('adjustment_id', $adjustment->id)->get();
        return view('admin.adjustments.show', compact( 'adjustment','details'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Adjustment $adjustment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Adjustment $adjustment)
    {
        $validatedData = $request->validate([
            'comment' => 'nullable',
            'real_date' => 'nullable|min:0',
            'warehouse_id' => 'required',
            'total_products' => 'nullable|min:0'
        ]);
        DB::beginTransaction();

        try {
            $adjustment->update($validatedData);
            $id = $adjustment->id;
            /** to modify warehouse qty will get all old quantities and remove from stored qty */
            $oldQtys = AdjustmentDetail::where('adjustment_id', $id)->get(['product_id','qty']);

            foreach($oldQtys as $old){
                    $oldQty = $old-> qty ;

                    $productId = $old-> product_id ;
                    $warehouse = Warehouse::find($request-> warehouse_id);
                    $currentQuantity = optional(optional($warehouse->products()->where('product_id', $productId)->withPivot('qty')->first())->pivot)->qty ?? 0;
                    $newQuantity = $currentQuantity - $oldQty;

                    if($newQuantity < 0){
                        $newQuantity  = 0 ;
                    }
                    $warehouse->products()->syncWithoutDetaching([
                        $productId => ['qty' => $newQuantity]
                    ]);
            }
            AdjustmentDetail::where('adjustment_id', $id)->delete();
            foreach ($request->json()->all()['data'] as $k => $value) {
                if (is_array($value)) {
                    $warehouse = Warehouse::find($request->warehouse_id);
                    $newQuantity = optional(optional($warehouse->products()->where('product_id', $k)->withPivot('qty')->first())->pivot)->qty ?? 0;
                    if($value['adjustment_type'] == 1){
                        $newQuantity = $newQuantity + $value['qty'];
                    }else{
                        if($value['qty'] > $newQuantity){
                            return false;
                        }else{
                            $newQuantity = $newQuantity - $value['qty'];
                        }
                    }
                    if($newQuantity < 0){
                        $newQuantity = 0;
                    }
                    $warehouse->products()->syncWithoutDetaching([
                        $k => ['qty' => $newQuantity]
                    ]);
                    AdjustmentDetail::create([
                        'in_warehouse'=>$newQuantity,'product_id' => $k,
                        'warehouse_id' => $request-> warehouse_id, 'qty' => $value['qty'], 'adjustment_id' => $id
                    ]);

                }
            }

            DB::commit();
            return session()->flash('success', __('Adjustment Added Successfully'));
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
     * @param  Adjustment $adjustment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Adjustment $adjustment)
    {
        $adjustmentId = $adjustment->id;
        if ($adjustment->delete()) {
            AdjustmentDetail::where('adjustment_id', $adjustmentId)->delete();
        }
        // Redirect back to the index page with a success message
        return redirect()->route('adjustments.index')
            ->with('success',  __('messages.adjustment_deleted_successfully'));
    }
}
