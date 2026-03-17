@extends('layouts.app')

@section('content')
    <style>
        form .col-form-label {
            font-weight: 600;
        }
    </style>


    <div class="pagetitle">
        <h1>{{ __('Configurations') }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">{{ __('Home') }}</a></li>

                <li class="breadcrumb-item active">{{ __('Configurations') }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">


                        <!-- General Form Elements -->
                        <form action="/admin/saveconfiguration" method="post">
                            @csrf


                            <ul class="nav nav-tabs nav-tabs-bordered" role="tablist">

                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview"
                                        aria-selected="true" role="tab" tabindex="-1">English</a>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <a class="nav-link " data-bs-toggle="tab" data-bs-target="#profile-edit"
                                        aria-selected="false" role="tab">العربية</a>
                                </li>



                            </ul>
                            <div class="tab-content pt-2">

                                <div class="tab-pane fade profile-overview show active" id="profile-overview"
                                    role="tabpanel">
                                    <h5 class="card-title">English</h5>

                                    <!-- panel-->
                                    @foreach ($configurations as $k => $configuration)
                                        <div class="row mt-3 mb-3 k{{ $k }}">

                                            @if ($configuration->field_type == 'text')
                                                <label for="name" class="col-sm-2 col-form-label">
                                                    {{ $configuration->label_en }}</label>

                                                <div class="col-sm-10 mt-1">
                                                    <textarea class="form-control" name="{{ $configuration->name }}_en" style="height: 100px">{{ $configuration->field_value_en }} </textarea>
                                                </div>
                                            @elseif ($configuration->field_type == 'number')
                                                <label for="name" class="col-sm-2 col-form-label">
                                                    {{ $configuration->label_en }}</label>

                                                <div class="col-sm-10 mt-1">
                                                    <input type="number" class="form-control" name="{{ $configuration->name }}_en"  value="{{ $configuration->field_value_en }}"/>
                                                </div>
                                            @elseif($configuration->field_type == 'file')
                                                <label for="name"
                                                    class="col-sm-2 col-form-label">{{ $configuration->label_en }}</label>

                                                <div class="col-sm-10">
                                                    <input class="form-control filepond" type="file"
                                                        name="{{ $configuration->name }}_en" id="formFile"
                                                        process="/admin/savelogo" toUpdate="logo">
                                                </div>
                                            @elseif($configuration->field_type == 'multiple')
                                                <label for="name"
                                                    class="col-sm-2 col-form-label">{{ $configuration->label_en }}</label>

                                                <div class="col-sm-10">
                                                    <select class="form-select" name="{{ $configuration-> name }}_en"
                                                        aria-label="Default select example">
                                                        <!-- handling currencies -->
                                                        @if ($configuration-> name == 'defaultcurrency')

                                                            @foreach ($currencies as $currency)
                                                                <option value="{{ $currency-> id }}" {{ ($currency-> id == $configuration-> field_value_en) ? 'selected="true"': '' }}>{{ $currency-> label_en }}
                                                                </option>
                                                            @endforeach
                                                        @else
                                                        @foreach (explode(',', $configuration->field_value_en) as $option)
                                                            <option value="{{ $option }}">{{ strtoupper($option) }}
                                                            </option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            @endif

                                        </div>
                                    @endforeach

                                </div>

                                <div class="tab-pane fade profile-edit pt-3  " id="profile-edit" role="tabpanel">
                                    <h5 class="card-title">العربية</h5>
                                    <!-- panel -->
                                    @foreach ($configurations as $configuration)
                                        <div class="row mt-3 mb-3">

                                            @if ($configuration->field_type == 'text')
                                                <label for="name"
                                                    class="col-sm-2 col-form-label">{{ $configuration->label_ar }}</label>

                                                <div class="col-sm-10 mt-1">
                                                    <textarea class="form-control" name="{{ $configuration->name }}_ar" style="height: 100px">{{ $configuration->field_value_ar }} </textarea>
                                                </div>
                                            @endif

                                        </div>
                                    @endforeach
                                </div>

                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"> </label>
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                                </div>
                            </div>

                        </form><!-- End General Form Elements -->

                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
