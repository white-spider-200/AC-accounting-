@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>{{ __('Measures') }}</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>

            <li class="breadcrumb-item active">{{ __('Measures') }}</li>
        </ol>
    </nav>
</div><!-- End Page Title -->
    <div class="card">
        <div class="card-body">

            <div class="row">
                <div class="col-md-7">
                </div>
                <div class="col-md-2">
                    <a href="{{ route('measures.create') }}" class="btn btn-primary mt-2"><i class="bi bi-plus"></i>{{ __('Add Measure') }}</a>
                </div>
                <div class="col-md-3 mt-2 search-container">
                    <div class="search-bar ">
                        <form class="search-form d-flex align-items-center" method="get" action="{{ route('measures.index') }}">
                            <input type="text" name="q" placeholder="{{ __('Search Word') }}" class="w-100" title="{{ __('Search') }}">
                            <button type="submit" title="Search"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                </div>
            </div>
            @if (count($measures) > 0)

                <table class="table w-100">
                    <thead>

                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Label') }}</th>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($measures as $measure)
                            <tr>
                                <td>{{ $measure-> name }}</td>
                                <td>
                                    {{ app()->getLocale() == 'ar' ? $measure-> label_ar : $measure-> label_en }}
                                </td>
                                <td>
                                    {{ app()->getLocale() == 'ar' ? $measure-> code_ar : $measure-> code_en }}

                                </td>
                                <td>
                                    <a href="{{ route('measures.edit', ['measure' => $measure->id]) }}"
                                        class="btn btn-primary mt-2"><i class="bi bi-pencil-square"></i><span class="d-none d-sm-inline btn-desk">{{ __('Edit') }}</span></a>
                                    <form action="{{ route('measures.destroy', ['measure' => $measure->id]) }}"
                                        method="POST" class="d-inline-block" id="measure-{{ $measure->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger mt-2"  onclick="deleteit('measure-{{ $measure->id }}');"><i class="bi bi-trash-fill"></i><span class="d-none d-sm-inline btn-desk">{{ __('Delete') }}</span></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-1">
                    {{ $measures->appends(Request::all())->links() }}
                </div>
            @else
                <p>{{ __('No Measures found.') }}</p>
            @endif
        </div>
    </div>

@endsection
