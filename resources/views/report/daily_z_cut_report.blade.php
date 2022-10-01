@extends('layouts.app')
@section('title', __('report.daily_z_cut_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.daily_z_cut_report')}}</h1>
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
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('cashier',  __('cashier.cashiers') . ':') !!}
                                {!! Form::select('cashier', $cashiers, null, ['class' => 'form-control select2',
                                    'style' => 'width:100%', "id" => "cashier", 'placeholder' => __('report.all')]); !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="daily_z_cut_report_table">
                            <thead>
                                <tr>
                                    <th>@lang('cashier.cashier')</th>
                                    <th>@lang('cashier.closed')</th>
                                    <th>@lang('lang_v1.correlative')</th>
                                    <th>@lang('cash_register.total_cash')</th>
                                    <th>@lang('cash_register.total_card')</th>
                                    <th>@lang('cash_register.total_check')</th>
                                    <th>@lang('cash_register.total_transfer')</th>
                                    <th>@lang('cash_register.total_return')</th>
                                    <th>@lang('cash_register.total_physical')</th>
                                    <th>@lang('messages.action')</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade daily_z_cut_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

@endsection

@section('javascript')
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
@endsection