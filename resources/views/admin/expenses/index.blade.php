@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __('Expenses') }}</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>

            <li class="breadcrumb-item active">{{ __('Expenses') }}</li>
        </ol>
    </nav>
</div><!-- End Page Title -->
    <div class="card">
        <div class="card-body">

            <div class="row">
                <div class="col-md-2">
                    <a href="{{ route('expenses.create') }}" class="btn btn-primary mt-2"><i class="bi bi-plus"></i>{{ __('Add Expense') }}</a>
                </div>
                <div class="col-md-10 mt-2 search-container">
                    <div class="search-bar ">
                        <form class="search-form d-flex align-items-center row" method="get" action="{{ route('expenses.index') }}">
                            <div class="col-md-3">
                                    <input type="text" name="q" placeholder="{{ __('Search Word') }}" class="" title="{{ __('Search') }}">

                            </div>
                            <div class="col-md-1 text-right mt-mobile">
                                <label> {{ __('From') }}</label>
                            </div>
                            <div class="col mt-mobile">
                                <input type="date" name="from_date" id="from_date" placeholder="{{ __('From Date') }}" class=""/>
                            </div>
                            <div class="col-md-1 text-right mt-mobile">
                                {{ __('To') }}
                            </div>
                            <div class="col mt-mobile">
                                <input type="date" name="to_date" id="to_date" value="{{  date('Y-m-d') }}" placeholder="{{ __('To Date') }}" class=""/>
                            </div>

                            <div class="col mt-mobile">
                                <input type="submit" title="Search" value="{{ __('Search') }}" class="btn btn-primary ml-10" style="color: #fff;width: 59px" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @if (count($expenses) > 0)

                <table class="table w-100">
                    <thead>

                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Actions') }}</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expenses as $expense)
                            <tr>

                                <td>
                                    {{ app()->getLocale() == 'ar' ? $expense-> label_ar : $expense-> label_en }}
                                </td>
                                <td>
                                    {{ number_format($expense-> price,3) }}
                                </td>
                                <td>
                                    {{ $expense-> real_date }}
                                </td>
                                <td>
                                    <a href="{{ route('expenses.edit', ['expense' => $expense-> id]) }}"
                                        class="btn btn-primary mt-2"><i class="bi bi-pencil-square"></i><span class="d-none d-sm-inline btn-desk">{{ __('Edit') }}</span></a>
                                    <form action="{{ route('expenses.destroy', ['expense' => $expense-> id]) }}"
                                        method="POST" class="d-inline-block" id="expense-{{ $expense-> id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger mt-2"  onclick="deleteit('expense-{{ $expense->id }}');"><i class="bi bi-trash-fill"></i><span class="d-none d-sm-inline btn-desk">{{ __('Delete') }}</span></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-1">
                    {{ $expenses->appends(Request::all())->links() }}
                </div>
            @else
                <p>{{ __('No Expenses  found.') }}</p>
            @endif
        </div>
    </div>

@endsection
