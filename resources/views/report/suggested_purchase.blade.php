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
            {!! Form::open(['id'=>'form_suggested_purchase_report', 'action' => 'PurchaseController@suggestedPurchaseReport', 'method' => 'post', 'target' => '_blank']) !!}
            <div class="row">
                {{-- location_id --}}
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("location", __("business.location")) !!}
                        {!! Form::select("location", $locations, null, ["class" => "form-control", "id" => "location"]) !!}
                    </div>
                </div>

                {{-- brand --}}
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('brand', __('brand.brand')) !!}
                        {!! Form::select('brand', $brands, null, ['class' => 'form-control select2', 'style' => 'width: 100%;']) !!}
                    </div>
                </div>

                {{-- end date --}}
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('date', __('report.date')) !!}
                        {!! Form::text('date', @format_date('now'), ['class' => 'form-control input-date', 'readonly']) !!}
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
            $('input.input-date').datepicker({
                autoclose: true,
                format:datepicker_date_format,
                endDate: new Date()
            });
        });
    </script>
@endsection