@extends('layouts.app')
@section('title', __('payroll.payroll'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> @lang('payroll.payroll')
            <small></small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary" id="accordion">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseFilter">
                                <i class="fa fa-filter" aria-hidden="true"></i> @lang('report.filters')
                            </a>
                        </h3>
                    </div>
                    <div id="collapseFilter" class="panel-collapse active collapse in" aria-expanded="true">
                        <div class="box-body">
                            {{-- clasification --}}
                            {!! Form::open([
                                'id' => 'form_report_payroll',
                                'action' => 'PayrollReportController@generateAnnualSummary',
                                'method' => 'post',
                            ]) !!}
                                <div class="col-lg-2 col-md-3 col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('year', __('payroll.year')) !!}
                                        <select name="year" id="year" class="form-control">
                                            <option value="">{{ __('accounting.all') }}</option> 
                                            @foreach ($years as $year)
                                                <option value="{{ $year->year }}">{{ $year->year }}</option>  
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- button --}}
                                <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6">
                                    <div id="button_report" class="btn btn-success ld-ext-right" style="margin-top: 24px;">
                                        <span id="spn-generate">@lang('accounting.generate') xlsx</span>
                                        <div class="ld ld-ring ld-spin" style="color:#fff"></div>
                                    </div>
                                </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-condensed table-hover" id="payroll_table"
                                width="100%">
                                <thead>
                                    <th width="15%">@lang('payroll.type')</th>
                                    <th width="20%">@lang('payroll.name')</th>
                                    <th width="20%">@lang('payroll.period')</th>
                                    <th>@lang('payroll.payment_period')</th>
                                    <th>@lang('payroll.ISR_apply')</th>
                                    <th>@lang('payroll.status')</th>
                                </thead>
                            </table>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = 'throw';

            var payroll_table = $("#payroll_table").DataTable({
                select: true,
                deferRender: true,
                processing: true,
                serverSide: true,
                "ajax": {
                    "url": "/payroll-annual-summary",
                    "data": function(d) {
                        d.year = $("#year").val();
                    }
                },
                columns: [
                    {
                        data: 'payrollType',
                        name: 'payrollType',
                        className: "text-center"
                    },
                    {
                        data: 'payrollName',
                        name: 'payrollName',
                        className: "text-center"
                    },
                    {
                        data: 'period',
                        name: 'period',
                        className: "text-center"
                    },
                    {
                        data: 'payment_period',
                        name: 'payment_period',
                        className: "text-center"
                    },
                    {
                        data: 'isr',
                        name: 'isr',
                        className: "text-center"
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: "text-center"
                    },

                ],
                columnDefs: [
                    { "searchable": false, "targets": [1, 4] }
                ],
                dom: '<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
                
            });

            // Location filter
            $('select#year').on('change', function() {
                $("input#year").val($("select#year").val());
                payroll_table.ajax.reload();
            });

            $(document).on('click', '#button_report', function(e) {
                $('form#form_report_payroll').submit();
            });
        });
    </script>
@endsection
