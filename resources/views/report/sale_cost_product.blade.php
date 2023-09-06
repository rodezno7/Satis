@extends('layouts.app')
@section('title', __('report.suggested_purchase_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.suggested_purchase_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="box">
        <div class="box-body">
            {!! Form::open(['action' => 'PurchaseController@getSaleCostProductReport', 'method' => 'post', 'target' => '_blank']) !!}
            <div class="row">
                {{-- location --}}
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("location", __("business.location")) !!} <span style="color: red;">*</span>
                        {!! Form::select('location', $locations, null, ['class' => 'form-control', 'id' => 'location',
                            'placeholder' => __('business.select_location'), 'required']) !!}
                    </div>
                </div>

                {{-- brand --}}
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('brand', __('brand.brand')) !!} <span style="color: red;">*</span>
                        {!! Form::select('brand', $brands, null, ['class' => 'form-control select2', 'style' => 'width: 100%;']) !!}
                    </div>
                </div>

                {{-- Date range --}}
                <div class="col-sm-3">
                    <div class="form-group">
                        <div class="input-group">
                        <button type="button" class="btn btn-primary" id="date_filter" style="margin-top: 25px;">
                            <span>
                            <i class="fa fa-calendar"></i>&nbsp; {{ __('messages.filter_by_date') }}
                            </span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                        {!! Form::hidden("start_date", date('Y-m-d'), ['id' => 'start_date']) !!}
                        {!! Form::hidden("end_date", date('Y-m-d'), ['id' => 'end_date']) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-6" style="margin-top: 25px;">
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-file-excel-o"></i> @lang('accounting.generate')
                        </button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</section>
<!-- /.content -->
@endsection
@section('javascript')
    <script>
        $(function () {
            /** TODO */
        });
    </script>
@endsection