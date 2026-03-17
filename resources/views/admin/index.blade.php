@extends('layouts.app')

@section('content')
    <section class="section dashboard">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body py-4">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                            <div>
                                <h4 class="mb-1">Operations Dashboard</h4>
                                <p class="text-muted mb-0">Live totals from sales, purchases, expenses, and master data.</p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('sales.create') }}" class="btn btn-primary">New Sale</a>
                                <a href="{{ route('purchases.create') }}" class="btn btn-outline-primary">New Purchase</a>
                                <a href="{{ route('expenses.create') }}" class="btn btn-outline-secondary">New Expense</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xxl-3 col-md-6">
                <div class="card info-card sales-card">
                    <div class="card-body">
                        <h5 class="card-title">Sales Today</h5>
                        <h3 class="mb-1">{{ number_format($summary['salesTodayAmount'], 2) }}</h3>
                        <p class="text-muted mb-0">{{ number_format($summary['salesTodayCount']) }} invoices posted today.</p>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6">
                <div class="card info-card revenue-card">
                    <div class="card-body">
                        <h5 class="card-title">Sales This Month</h5>
                        <h3 class="mb-1">{{ number_format($summary['salesThisMonth'], 2) }}</h3>
                        <p class="text-muted mb-0">Gross revenue recorded this month.</p>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6">
                <div class="card info-card customers-card">
                    <div class="card-body">
                        <h5 class="card-title">Purchases This Month</h5>
                        <h3 class="mb-1">{{ number_format($summary['purchasesThisMonth'], 2) }}</h3>
                        <p class="text-muted mb-0">Supplier purchases booked this month.</p>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-md-6">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">Net This Month</h5>
                        <h3 class="mb-1 {{ $summary['netMonth'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($summary['netMonth'], 2) }}
                        </h3>
                        <p class="text-muted mb-0">Sales minus purchases and expenses.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">7 Day Activity</h5>
                        <div id="activityChart"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Current Snapshot</h5>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span>Expenses This Month</span>
                            <strong>{{ number_format($summary['expensesThisMonth'], 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span>Products</span>
                            <strong>{{ number_format($summary['productsCount']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span>Clients</span>
                            <strong>{{ number_format($summary['clientsCount']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span>Suppliers</span>
                            <strong>{{ number_format($summary['suppliersCount']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span>Warehouses</span>
                            <strong>{{ number_format($summary['warehousesCount']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between pt-2">
                            <span>Users</span>
                            <strong>{{ number_format($summary['usersCount']) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-6">
                <div class="card recent-sales overflow-auto">
                    <div class="card-body">
                        <h5 class="card-title">Recent Sales</h5>
                        <table class="table table-borderless align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Client</th>
                                    <th>Warehouse</th>
                                    <th>Date</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentSales as $sale)
                                    <tr>
                                        <td>{{ $sale->invoice_number ?: 'SALE-' . $sale->id }}</td>
                                        <td>{{ optional($sale->client)->name ?: 'Walk-in / Unassigned' }}</td>
                                        <td>{{ optional($sale->warehouse)->name ?: 'Not set' }}</td>
                                        <td>{{ $sale->sale_date ?: optional($sale->created_at)->format('Y-m-d') }}</td>
                                        <td class="text-end">{{ number_format((float) $sale->total_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No sales have been recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card recent-sales overflow-auto">
                    <div class="card-body">
                        <h5 class="card-title">Recent Purchases</h5>
                        <table class="table table-borderless align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Supplier</th>
                                    <th>Warehouse</th>
                                    <th>Date</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentPurchases as $purchase)
                                    <tr>
                                        <td>{{ $purchase->invoice_number ?: 'PUR-' . $purchase->id }}</td>
                                        <td>{{ optional($purchase->supplier)->name ?: 'Unassigned' }}</td>
                                        <td>{{ optional($purchase->warehouse)->name ?: 'Not set' }}</td>
                                        <td>{{ $purchase->purchase_date ?: optional($purchase->created_at)->format('Y-m-d') }}</td>
                                        <td class="text-end">{{ number_format((float) $purchase->total_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No purchases have been recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const chartRoot = document.querySelector('#activityChart');

            if (!chartRoot || typeof ApexCharts === 'undefined') {
                return;
            }

            new ApexCharts(chartRoot, {
                chart: {
                    type: 'area',
                    height: 320,
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.25,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                colors: ['#198754', '#0d6efd', '#dc3545'],
                series: [
                    {
                        name: 'Sales',
                        data: @json($chart['sales'])
                    },
                    {
                        name: 'Purchases',
                        data: @json($chart['purchases'])
                    },
                    {
                        name: 'Expenses',
                        data: @json($chart['expenses'])
                    }
                ],
                xaxis: {
                    categories: @json($chart['labels'])
                },
                yaxis: {
                    labels: {
                        formatter(value) {
                            return Number(value).toFixed(0);
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter(value) {
                            return Number(value).toFixed(2);
                        }
                    }
                }
            }).render();
        });
    </script>
@endsection
