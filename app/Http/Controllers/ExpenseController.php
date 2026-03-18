<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpensesCategory;
use App\Services\Accounting\TransactionPostingService;

use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct(private readonly TransactionPostingService $transactionPostingService)
    {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $search = $request->q;
        $expenses = Expense::query()
            ->where('description', 'like', '%' . $search . '%')
            ->orderBy('id', 'desc');
        if (isset($request->from_date) and !empty($request->from_date)) {
            $expenses = $expenses->where('expense_date', '>',  date('Y-m-d', strtotime("-1 day", strtotime($request->from_date))));
        }
        if (isset($request->to_date) and !empty($request->to_date)) {
            $expenses = $expenses->where('expense_date', '<',  date('Y-m-d',  strtotime("+1 day", strtotime($request->to_date))));
        }
        $expenses = $expenses->paginate(10);
        return view('admin.expenses.index', compact('expenses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $expensesCategories = ExpensesCategory::all();
        return view('admin.expenses.create', compact('expensesCategories'));
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
            'comment' => 'required|string',
            'label_en' => 'required|string',
            'label_ar' => 'required|string',
            'price' => 'required',
            'expenses_categories_id' => 'required',
            'real_date' => 'required'
        ]);

        // Create a new Warehouse model with the validated data
        $expense = Expense::create($validatedData);
        $this->transactionPostingService->postExpense($expense);

        // Redirect back to the index page with a success message
        return redirect()->route('expenses.index')
            ->with('success',  __('messages.expenses_created_successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Expenses $warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit(Expense $expense)
    {
        $expensesCategories = ExpensesCategory::all();
        return view('admin.expenses.edit', compact('expense', 'expensesCategories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Expense $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expense $expense)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'comment' => 'required|string',
            'label_en' => 'required|string',
            'label_ar' => 'required|string',
            'price' => 'required',
            'expenses_categories_id' => 'required',
            'real_date' => 'required'
        ]);

        $expense->update($validatedData);
        $this->transactionPostingService->postExpense($expense->fresh());
        return redirect()->route('expenses.index')
            ->with('success', __('messages.expense_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Expense $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        // Redirect back to the index page with a success message
        return redirect()->route('expenses.index')
            ->with('success',  __('messages.expense_deleted_successfully'));
    }
}
