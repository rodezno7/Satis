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
                {{-- location --}}
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("location", __("business.location")) !!} <span style="color: red;">*</span>
                        {!! Form::select('location', $locations, null, ['class' => 'form-control', 'id' => 'location',
                            'placeholder' => __('business.select_location'), 'required']) !!}
                    </div>
                </div>

                {{-- warehouse --}}
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="form-group">
                        {!! Form::label("warehouse", __("warehouse.warehouse")) !!}
                        {!! Form::select('warehouse', [], null, ['class' => 'form-control', 'id' => 'warehouse',
                            'placeholder' => __('warehouse.all_warehouses')]) !!}
                    </div>
                </div>

                {{-- brand --}}
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('brand', __('brand.brand')) !!} <span style="color: red;">*</span>
                        {!! Form::select('brand', $brands, null, ['class' => 'form-control select2', 'style' => 'width: 100%;']) !!}
                    </div>
                </div>

                {{-- end date --}}
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('date', __('report.date')) !!} <span style="color: red;">*</span>
                        {!! Form::text('date', @format_date('now'), ['class' => 'form-control input-date', 'readonly']) !!}
                    </div>
                </div>

                {{-- number of months --}}
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('months', __('lang_v1.months')) !!} <span style="color: red;">*</span>
                        {!! Form::select('months', ['3' => '3', '6' => '6', '9' => '9', '12' => '12', '18' => '18', '24' => '24'],
                            '24', ['class' => 'form-control']) !!}
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

            $('select#location').on('change', function () {
                let location = $(this).val();

                if (location) {
                    $.ajax({
                        type: 'GET',
                        url: '/warehouses/get_warehouses/'+ location,
                        dataType: 'json',
                        success: function (warehouses) {
                            $('select#warehouse').empty();
                            $('select#warehouse').append(new Option(LANG.all_warehouses, '', true, true));

                            $.each(warehouses, function (i, w) {
                                $('select#warehouse').append(new Option(w.name, w.id, false));
                            });
                        } 
                    });

                } else {
                    $('select#warehouse').empty();
                    $('select#warehouse').append(new Option(LANG.all_warehouses, '', true, true));
                }
            });
        });
    </script>
@endsection