@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>{{ __('Add Expense') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('expenses.index') }}">{{ __('Expenses') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Add Expense') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="card">

        <div class="card-body">
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <div class="form-group row mt-2">
                    <label for="real_date" class="col-md-4 col-form-label text-md-right">{{ __('Date') }} *</label>
                    <div class="col-md-6">
                        <input type="date" name="real_date" id="real_date" class="form-control"
                            value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="price" class="col-md-4 col-form-label text-md-right">{{ __('Amount') }} *</label>
                    <div class="col-md-6">
                        <input type="number" step="any" name="price" id="price" class="form-control"
                            value="{{ old('price') }}" placeholder="1000.50" required >
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="expenses_categories_id" class="col-md-4 col-form-label text-md-right">{{ __('Category') }} *</label>

                    <div class="col-md-6">
                        <select name="expenses_categories_id" id="expenses_categories_id" class="form-select" required>
                            <option value=""> ... </option>
                            @foreach($expensesCategories as $category)
                            <option value="{{ $category-> id }}"> {{ app()->getLocale() == 'ar' ? $category-> label_ar : $category-> label_en }}</option>
                            @endforeach
                        </select>

                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="label_en" class="col-md-4 col-form-label text-md-right">{{ __('Label English') }} *</label>
                    <div class="col-md-6">
                        <input type="text" name="label_en" id="label_en" class="form-control"
                            value="{{ old('label_en') }}" placeholder="Salary of May" required>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="label_ar" class="col-md-4 col-form-label text-md-right">{{ __('Label Arabic') }} *</label>
                    <div class="col-md-6">
                        <input type="text" name="label_ar" id="label_ar" class="form-control"
                            value="{{ old('label_ar') }}" placeholder="راتب شهر 5" required>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <label for="comment" class="col-md-4 col-form-label text-md-right">{{ __('Comment') }} </label>
                    <div class="col-md-6">
                        <textarea name="comment" id="comment" placeholder="{{ __('Comment') }}"  class="form-control" >{{ old('comment') }}</textarea>
                    </div>
                </div>

                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                </div>
            </form>
        </div>
    </div>
@endsection
