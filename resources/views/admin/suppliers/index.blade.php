@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __('Suppliers') }}</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>

            <li class="breadcrumb-item active">{{ __('Suppliers') }}</li>
        </ol>
    </nav>
</div><!-- End Page Title -->
    <div class="card">
        <div class="card-body">

            <div class="row">
                <div class="col-md-7">
                </div>
                <div class="col-md-2">
                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary mt-2"><i class="bi bi-plus"></i>{{ __('Add Supplier') }}</a>
                </div>
                <div class="col-md-3 mt-2 search-container">
                    <div class="search-bar ">
                        <form class="search-form d-flex align-items-center" method="get" action="{{ route('suppliers.index') }}">
                            <input type="text" name="q" placeholder="{{ __('Search Word') }}" class="w-100" title="{{ __('Search') }}">
                            <button type="submit" title="Search"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                </div>
            </div>
            @if (count($suppliers) > 0)

                <table class="table w-100">
                    <thead>

                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $supplier)
                            <tr>
                                <td>{{ $supplier-> name }}</td>
                                <td>
                                    {{ $supplier-> phone }}
                                </td>

                                <td>
                                    <a href="{{ route('suppliers.edit', ['supplier' => $supplier->id]) }}"
                                        class="btn btn-primary mt-2"><i class="bi bi-pencil-square"></i><span class="d-none d-sm-inline btn-desk">{{ __('Edit') }}</span></a>
                                    <form action="{{ route('suppliers.destroy', ['supplier' => $supplier->id]) }}"
                                        method="POST" class="d-inline-block" id="supplier-{{ $supplier->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger mt-2"  onclick="deleteit('supplier-{{ $supplier->id }}');"><i class="bi bi-trash-fill"></i><span class="d-none d-sm-inline btn-desk">{{ __('Delete') }}<span class="d-none d-sm-inline btn-desk"></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-1">
                    {{ $suppliers->appends(Request::all())->links() }}
                </div>
            @else
                <p>{{ __('No suppliers found.') }}</p>
            @endif
        </div>
    </div>

@endsection
