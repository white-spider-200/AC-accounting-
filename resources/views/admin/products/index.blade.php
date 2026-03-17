@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __('Products') }}</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>

            <li class="breadcrumb-item active">{{ __('Products') }}</li>
        </ol>
    </nav>
</div><!-- End Page Title -->
    <div class="card">
        <div class="card-body">

            <div class="row">
                <div class="col-md-7">
                </div>
                <div class="col-md-2">
                    <a href="{{ route('products.create') }}" class="btn btn-primary mt-2"><i class="bi bi-plus"></i>{{ __('Add Product') }}</a>
                </div>
                <div class="col-md-3 mt-2 search-container">
                    <div class="search-bar ">
                        <form class="search-form d-flex align-items-center" method="get" action="{{ route('products.index') }}">
                            <input type="text" name="q" placeholder="{{ __('Search Word') }}" class="w-100" title="{{ __('Search') }}">
                            <button type="submit" title="Search"><i class="bi bi-search"></i></button>

                        </form>
                    </div>
                </div>
            </div>
            @if (count($products) > 0)
                <div class="table-responsive">
                    <table class="table ">
                        <thead>

                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Image') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Actions') }}</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>

                                    <td>
                                        {{ app()->getLocale() == 'ar' ? $product-> label_ar : $product-> label_en }}
                                    </td>
                                    <td>
                                        @if(!empty($product-> img))
                                            <img src="/uploads/images/products/{{ $product-> img }}" height="100px"/>
                                        @endif
                                    </td>
                                    <td>
                                        {{ number_format($product-> price,3) }}
                                    </td>
                                    <td>
                                        {{ app()->getLocale() == 'ar' ? @$product-> category-> label_ar : @$product-> category-> label_en }}
                                    </td>
                                    <td>
                                        <a href="{{ route('products.edit', ['product' => $product-> id]) }}"
                                            class="btn btn-primary mt-2"><i class="bi bi-pencil-square"></i>{{ __('Edit') }}</a>
                                        <form action="{{ route('products.destroy', ['product' => $product-> id]) }}"
                                            method="POST" class="d-inline-block" id="product-{{ $product-> id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger mt-2"  onclick="deleteit('product-{{ $product->id }}');"><i class="bi bi-trash-fill"></i>{{ __('Delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-1">
                    {{ $products->appends(Request::all())->links() }}
                </div>
            @else
                <p>{{ __('No products  found.') }}</p>
            @endif
        </div>
    </div>

@endsection
