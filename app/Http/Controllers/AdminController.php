<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\Warehouse;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();
        $lastSevenDays = collect(range(6, 0))
            ->map(fn (int $offset) => $today->copy()->subDays($offset));

        $expenseAmountColumn = $this->resolveColumn('expenses', ['amount', 'price']);
        $expenseDateColumn = $this->resolveColumn('expenses', ['expense_date', 'real_date', 'created_at']);

        $summary = [
            'salesTodayCount' => (int) Sale::query()
                ->whereDate(DB::raw('COALESCE(sale_date, created_at)'), $today)
                ->count(),
            'salesTodayAmount' => (float) Sale::query()
                ->whereDate(DB::raw('COALESCE(sale_date, created_at)'), $today)
                ->sum('total_amount'),
            'salesThisMonth' => (float) Sale::query()
                ->whereBetween(DB::raw('COALESCE(sale_date, created_at)'), [$monthStart->toDateString(), $today->toDateString()])
                ->sum('total_amount'),
            'purchasesThisMonth' => (float) Purchase::query()
                ->whereBetween(DB::raw('COALESCE(purchase_date, created_at)'), [$monthStart->toDateString(), $today->toDateString()])
                ->sum('total_amount'),
            'expensesThisMonth' => $expenseAmountColumn
                ? (float) Expense::query()
                    ->whereBetween(DB::raw(sprintf('COALESCE(%s, created_at)', $expenseDateColumn)), [$monthStart->toDateString(), $today->toDateString()])
                    ->sum($expenseAmountColumn)
                : 0.0,
            'productsCount' => Product::count(),
            'clientsCount' => Client::count(),
            'suppliersCount' => Supplier::count(),
            'warehousesCount' => Warehouse::count(),
            'usersCount' => User::count(),
        ];

        $summary['netMonth'] = $summary['salesThisMonth'] - $summary['purchasesThisMonth'] - $summary['expensesThisMonth'];

        $chart = [
            'labels' => $lastSevenDays->map(fn (Carbon $date) => $date->format('M d'))->all(),
            'sales' => $lastSevenDays->map(fn (Carbon $date) => $this->sumForDate('sales', 'total_amount', 'sale_date', $date))->all(),
            'purchases' => $lastSevenDays->map(fn (Carbon $date) => $this->sumForDate('purchases', 'total_amount', 'purchase_date', $date))->all(),
            'expenses' => $lastSevenDays->map(fn (Carbon $date) => $this->sumForDate('expenses', $expenseAmountColumn, $expenseDateColumn, $date))->all(),
        ];

        $recentSales = Sale::query()
            ->with(['client:id,name', 'warehouse:id,name'])
            ->select(['id', 'client_id', 'warehouse_id', 'invoice_number', 'sale_date', 'total_amount', 'created_at'])
            ->orderByRaw('COALESCE(sale_date, created_at) DESC')
            ->limit(5)
            ->get();

        $recentPurchases = Purchase::query()
            ->with(['supplier:id,name', 'warehouse:id,name'])
            ->select(['id', 'supplier_id', 'warehouse_id', 'invoice_number', 'purchase_date', 'total_amount', 'created_at'])
            ->orderByRaw('COALESCE(purchase_date, created_at) DESC')
            ->limit(5)
            ->get();

        return view('admin.index', compact('summary', 'chart', 'recentSales', 'recentPurchases'));
    }

    private function resolveColumn(string $table, array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (Schema::hasColumn($table, $candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function sumForDate(string $table, ?string $amountColumn, ?string $dateColumn, Carbon $date): float
    {
        if (! $amountColumn || ! $dateColumn) {
            return 0.0;
        }

        return (float) DB::table($table)
            ->whereDate(DB::raw(sprintf('COALESCE(%s, created_at)', $dateColumn)), $date->toDateString())
            ->sum($amountColumn);
    }
}
