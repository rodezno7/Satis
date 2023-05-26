@extends('layouts.app')

@section('title', __('accounting.purchases_book'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('accounting.purchases_book')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="panel with-nav-tabs panel-default boxform_u box-solid_u">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-report" data-toggle="tab">@lang('accounting.report')</a></li>
            </ul>
        </div>

        <div class="panel-body">
            <div class="tab-content">
                <div class="tab-pane fade in active" id="tab-report">
                    {!! Form::open(['id'=>'form_taxpayer', 'action' => 'ReporterController@getPurchasesBook', 'method' => 'post', 'target' => '_blank']) !!}
                    <div class="row">
                        <div class="col-xs-12">
                            <div id="msj-errors" class="alert alert-danger alert-dismissible" role="alert" style="display: none;">              
                                <strong id="msj"></strong>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-md-4 cold-sm-6">
                            <div class="form-group">
                                {!! Form::label('location', __('business.location')) !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </span>
                                    {!! Form::select('location', $locations, null, ['class' => 'form-control select2',
                                        'style' => 'width: 100%;', 'placeholder' => __('report.all_locations')]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 cold-sm-6">
                            <div class="form-group">
                                <label for="from">@lang('accounting.from')</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    {!! Form::text('initial_date', @format_date('now'),
                                        ['id' => 'initial_date', 'class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 cold-sm-6">
                            <div class="form-group">
                                <label for="account">@lang('accounting.to')</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    {!! Form::text('final_date', @format_date('now'),
                                        ['id' => 'final_date', 'class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-md-4 cold-sm-6">
                            <label>@lang('accounting.size_font')</label>
                            <select name="size" id="size" class="form-control select2" style="width: 100%;" required>
                                <option value="7">7</option>
                                <option value="8" selected>8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-4 cold-sm-6">
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
                        <div class="col-lg-3 col-md-4 cold-sm-6">
                            <div class="form-group">
                                <input type="submit" class="btn btn-success" value="@lang('accounting.generate')" id="button_report">
                                @if (auth()->user()->can('accounting.close_vat_books'))
                                    <button type="button" class="btn btn-primary close_vat_book">@lang('report.close_purchase_book')</button>
                                @endif
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

        $("input#initial_date, input#final_date").datetimepicker({
            format: moment_date_format,
            ignoreReadonly: true
	    });

        $(document).on('click', 'button.close_vat_book', function(){
            var start_date = $('input#initial_date').val();
            var end_date = $('input#final_date').val();

            swal({ 
                title: LANG.sure,
                text: LANG.wont_be_able_revert,
                buttons: [LANG.cancel, LANG.ok]
            }).then((value) => {
                if(value){
                    $.ajax({
                        type: "post",
                        url: "/purchases/close-book",
                        data: { start_date: start_date, end_date: end_date },
                        success: function(data){
                            if(data.success){
                                toastr.success(data.msg);
                            } else {
                                toastr.error(data.msg);
                            }
                        }
                    });
                }
            });
        });
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