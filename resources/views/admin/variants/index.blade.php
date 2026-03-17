@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __('Variants') }}</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>

            <li class="breadcrumb-item active">{{ __('Variants') }}</li>
        </ol>
    </nav>
</div><!-- End Page Title -->
    <div class="card">
        <div class="card-body">

            <div class="row">
                <div class="col-md-6">
                </div>
                <div class="col-md-3">
                    <a href="{{ route('variants.create') }}" class="btn btn-primary mt-2"><i class="bi bi-plus"></i>{{ __('Add Variant') }}</a>
                </div>
                <div class="col-md-3 mt-2 search-container">
                    <div class="search-bar ">
                        <form class="search-form d-flex align-items-center" method="get" action="{{ route('variants.index') }}">
                            <input type="text" name="q" placeholder="{{ __('Search Word') }}" class="w-100" title="{{ __('Search') }}">
                            <button type="submit" title="Search"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                </div>
            </div>
            @if (count($variants) > 0)

                <table class="table w-100">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Parent') }}</th>
                            <th>{{ __('Actions') }}</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($variants as $variant)
                            <tr>

                                <td>
                                    {{ app()->getLocale() == 'ar' ? $variant-> label_ar : $variant-> label_en }}

                                </td>
                                <td>{{ @$variant-> parent-> label_en }}</td>
                                <td>
                                    <a href="{{ route('variants.edit', ['variant' => $variant-> id]) }}"
                                        class="btn btn-primary mt-2"><i class="bi bi-pencil-square"></i><span class="d-none d-sm-inline btn-desk">{{ __('Edit') }}</span></a>
                                    <form action="{{ route('variants.destroy', ['variant' => $variant-> id]) }}"
                                        method="POST" class="d-inline-block" id="variant-{{ $variant-> id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger mt-2"  onclick="deleteit('variant-{{ $variant->id }}');"><i class="bi bi-trash-fill"></i><span class="d-none d-sm-inline btn-desk">{{ __('Delete') }}</span></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-1">
                    {{ $variants->appends(Request::all())->links() }}
                </div>
            @else
                <p>{{ __('No Variants.') }}</p>
            @endif
        </div>
    </div>

@endsection
