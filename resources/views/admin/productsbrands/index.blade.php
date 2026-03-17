@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __('Brands') }}</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>

            <li class="breadcrumb-item active">{{ __('Brands') }}</li>
        </ol>
    </nav>
</div><!-- End Page Title -->
    <div class="card">
        <div class="card-body">

            <div class="row">
                <div class="col-md-6">
                </div>
                <div class="col-md-3">
                    <a href="{{ route('productsbrands.create') }}" class="btn btn-primary mt-2"><i class="bi bi-plus"></i>{{ __('Add Brand') }}</a>
                </div>
                <div class="col-md-3 mt-2 search-container">
                    <div class="search-bar ">
                        <form class="search-form d-flex align-items-center" method="get" action="{{ route('productsbrands.index') }}">
                            <input type="text" name="q" placeholder="{{ __('Search Word') }}" class="w-100" title="{{ __('Search') }}">
                            <button type="submit" title="Search"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                </div>
            </div>
            @if (count($productsBrands) > 0)

                <table class="table table-responsive">
                    <thead>

                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Image') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productsBrands as $productsBrand)
                            <tr>

                                <td>
                                    {{ app()->getLocale() == 'ar' ? $productsBrand-> label_ar : $productsBrand-> label_en }}
                                </td>
                                <td>
                                    @if(!empty($productsBrand-> img ))
                                    <img src="/uploads/images/brands/{{ $productsBrand-> img }}" height="100px" loading="lazy"/>
                                    @endif

                                </td>
                                <td>
                                    <a href="{{ route('productsbrands.edit', ['productsBrand' => $productsBrand-> id]) }}"
                                        class="btn btn-primary mt-2"><i class="bi bi-pencil-square"></i><span class="d-none d-sm-inline btn-desk">{{ __('Edit') }}</span></a>
                                    <form action="{{ route('productsbrands.destroy', ['productsBrand' => $productsBrand-> id]) }}"
                                        method="POST" class="d-inline-block" id="productsbrand-{{ $productsBrand-> id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger mt-2"  onclick="deleteit('productsbrand-{{ $productsBrand->id }}');"><i class="bi bi-trash-fill"></i><span class="d-none d-sm-inline btn-desk">{{ __('Delete') }}</span></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-1">
                    {{ $productsBrands->appends(Request::all())->links() }}
                </div>
            @else
                <p>{{ __('No products Brand found.') }}</p>
            @endif
        </div>
    </div>

@endsection
