@extends('layouts.app')

@section('title', __('accounting.book_sales_final_consumer'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('accounting.book_sales_final_consumer')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="panel with-nav-tabs panel-default boxform_u box-solid_u">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                {{-- <li><a href="#tab-list" data-toggle="tab">@lang('accounting.list')</a></li> --}}
                <li class="active"><a href="#tab-report" data-toggle="tab">@lang('accounting.report')</a></li>
            </ul>
        </div>

        <div class="panel-body">
            <div class="tab-content">
                {{-- <div class="tab-pane fade" id="tab-list">
                    <div class="row">
                        <div class="col-xs-12">
                            
                        </div>
                    </div>
                </div> --}}

                <div class="tab-pane fade in active" id="tab-report">
                    {{-- <h4>@lang('accounting.report')</h4> --}}
                    {!! Form::open(['id'=>'form_final_consumer', 'action' => 'ReporterController@getBookFinalConsumer', 'method' => 'post', 'target' => '_blank']) !!}
                    <div class="row">
                        <div class="col-xs-12">
                            <div id="msj-errors" class="alert alert-danger alert-dismissible" role="alert" style="display: none;">              
                                <strong id="msj"></strong>
                            </div>
                        </div>
                    </div>

                    <div class="row">       
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="from">@lang('accounting.from')</label>
                                <div class="wrap-inputform">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    {!! Form::date('initial_date', \Carbon\Carbon::now()->format('Y-m-d'),
                                        ['id' => 'initial_date', 'class' => 'inputform2', 'required']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="account">@lang('accounting.to')</label>
                                <div class="wrap-inputform">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    {!! Form::date('final_date', \Carbon\Carbon::now()->format('Y-m-d'),
                                        ['id' => 'final_date', 'class' => 'inputform2', 'required']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>@lang('accounting.location')</label>
                                {!! Form::select('location', $locations, '',
                                    ['id' => 'location', 'class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;', 'required']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <label>@lang('accounting.size_font')</label>
                            <select name="size" id="size" class="form-control select2" style="width: 100%;" required>
                                <option value="7">7</option>
                                <option value="8" selected>8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>@lang('accounting.format')</label>
                                <select name="report_type" id="report_type" class="form-control select2" style="width: 100%" required>
                                    <option value="pdf" selected>PDF</option>
                                    <option value="excel">Excel</option>
                                </select>                       
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-4">
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
    window.onload = function () {
        document.getElementById('initial_date').oninput = checkDate;
        document.getElementById('final_date').oninput = checkDate;
    }

    function checkDate() {
        var initial_date = document.getElementById('initial_date');
        var final_date = document.getElementById('final_date');

        if (Date.parse(initial_date.value) > Date.parse(final_date.value)) {
            final_date.setCustomValidity(LANG.validate_date_range);
        } else {
            final_date.setCustomValidity('');
        }
    }
</script>
@endsection