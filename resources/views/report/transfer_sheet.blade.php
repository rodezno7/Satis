@extends('layouts.app')
@section('title', __('lab_order.transfers_sheet'))

@section('content')

{{-- Content Header (Page header) --}}
<section class="content-header">
    <h1>{{ __('lab_order.transfers_sheet')}}</h1>
</section>

{{-- Main content --}}
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
              <div class="box-header with-border">
                <h3 class="box-title">@lang('report.generate_report')</h3>
              </div>
              <div class="box-body">
                  {{-- Form --}}
                  {!! Form::open(['id' => 'form_transfer_sheet', 'action' => 'ReportController@postTransferSheet', 'method' => 'post', 'target' => '_blank']) !!}
                   
                    <div class="row">
                        {{-- warehouse_id --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('warehouse_id',  __('accounting.location') . ':') !!}
                                {!! Form::select('warehouse_id', $warehouses, null,
                                    ['class' => 'form-control select2', 'style' => 'width:100%', 'required']) !!}
                            </div>
                        </div>

                        {{-- transfer_date --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label('transfer_date', __('messages.date') . ':') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    {!! Form::text('transfer_date', @format_date('now'),
                                        ['class' => 'form-control', 'readonly', 'required', 'id' => 'transfer_date']) !!}
                                </div>
                            </div>
                        </div>

                        {{-- report_type --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>@lang('accounting.format')</label>
                                <select name="report_type" id="report_type" class="form-control select2" style="width: 100%" required>
                                    <option value="pdf" selected>PDF</option>
                                    <option value="excel">Excel</option>
                                </select>                       
                            </div>
                        </div>

                        {{-- size_font --}}
                        <div class="col-sm-3">
                            <label>@lang('accounting.size_font')</label>
                            <select name="size" id="size" class="form-control select2" style="width: 100%;" required>
                                <option value="7">7</option>
                                <option value="8" selected>8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        {{-- delivers --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('delivers',  __('report.delivers') . ':') !!}
                                {!! Form::text('delivers', '', ['class' => 'form-control', 'placeholder' => __('report.delivers')]) !!}
                            </div>
                        </div>

                        {{-- receives --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('receives',  __('report.receives') . ':') !!}
                                {!! Form::text('receives', '', ['class' => 'form-control', 'placeholder' => __('report.receives')]) !!}
                            </div>
                        </div>

                        {{-- enable_signature_column --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                <div class="checkbox" style="margin-top: 0;">
                                    <label>
                                        {!! Form::checkbox('enable_signature_column', 1, false, ['id' => 'enable_signature_column']) !!}
                                        <strong>@lang('report.enable_signature_column')</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- button --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input type="submit" class="btn btn-success" value="@lang('accounting.generate')" id="button_report">
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
              </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).ready(function() {
        // Datepicker
        $('#transfer_date').datepicker({
            autoclose: true,
            format: datepicker_date_format
        });
    });
</script>
@endsection