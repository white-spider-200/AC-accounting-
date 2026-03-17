@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Currency Details') }}</div>
                      code_ar comment
                    <div class="card-body">
                        <div class="form-group">
                            <label for="label_en">{{ __('Label English') }}</label>
                            <input type="text" name="label_en" id="label_en" class="form-control" value="{{ $currency-> label_en }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="label_ar">{{ __('Label Arabic') }}</label>
                            <input type="text" name="label_ar" id="label_ar" class="form-control" value="{{ $currency-> label_ar }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="code_en">{{ __('Code English') }}</label>
                            <input type="text" name="code_en" id="code_en" class="form-control" value="{{ $currency-> code_en }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="code_ar">{{ __('Code Arabic') }}</label>
                            <input type="text" name="code_ar" id="code_ar" class="form-control" value="{{ $currency-> code_ar }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="comment">{{ __('Comment') }}</label>
                            <input type="text" name="comment" id="comment" class="form-control" value="{{ $currency-> comment }}" readonly>
                        </div>

                        <a href="{{ route('currencies.edit', ['currency' => $currency-> id]) }}" class="btn btn-primary">{{ __('Edit') }}</a>
                        <a href="{{ route('currencies.index') }}" class="btn btn-secondary">{{ __('Back to List') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
