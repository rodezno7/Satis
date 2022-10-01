@extends('layouts.app')
@section('title', __('report.warehouse_closure_report'))

@section('content')

{{-- Content Header (Page header) --}}
<section class="content-header">
    <h1>{{ __('report.warehuose_daily_movements_report')}}</h1>
</section>

{{-- Main content --}}
<section class="content">

    {{-- Filters --}}
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
                    {!! Form::open(['url' => action('ReportController@postCostOfSaleDetailReport'),
                        'method' => 'post', 'id' => 'cost_of_sale_detail_report_filter_form', 'target' => '_blank']) !!}

                        <div class="row">
                            {{-- date_range --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('date_range', __('report.date_range') . ':') !!}
                                    {!! Form::text(
                                        'date_range',
                                        @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month'),
                                        [
                                            'placeholder' => __('lang_v1.select_a_date_range'),
                                            'class' => 'form-control',
                                            'id' => 'date_range',
                                            'readonly'
                                        ]) !!}

                                    {!! Form::hidden('start_date', null, ['id' => 'start_date']) !!}
                                    {!! Form::hidden('end_date', null, ['id' => 'end_date']) !!}
                                </div>
                            </div>
                            
                            {{-- warehouse_id --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('warehouse_id',  __('warehouse.warehouse') . ':') !!}
                                    {!! Form::select('warehouse_id', $warehouses, null,
                                        ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2', 'style' => 'width: 100%;', 'id' => 'warehouse_id', 'required']); !!}
                                </div>
                            </div>

                            {{-- document_type_id --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('document_type_id', __('document_type.title') . ':') !!}
                                    {!! Form::select('document_type_id', $document_types, null,
                                        ['placeholder' => __('accounting.all'), 'class' => 'form-control select2', 'style' => 'width: 100%;', 'id' => 'document_type_id']); !!}
                                </div>
                            </div>

                            {{-- type --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('type', __('movement_type.movement_type') . ':') !!}
                                    {!! Form::select('type', $types, null,
                                        ['class' => 'form-control select2', 'style' => 'width: 100%;', 'id' => 'type']); !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- size --}}
                            <div class="col-sm-3">
                                <label>@lang('accounting.size_font')</label>
                                <select name="size" id="size" class="form-control select2" style="width: 100%;" required>
                                    <option value="7">7</option>
                                    <option value="8" selected>8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                </select>
                            </div>

                            {{-- format --}}
                            <div class="col-sm-3">
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
                            {{-- button --}}
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <button type="submit" id="btn_report" class="btn btn-success">
                                        @lang('accounting.generate')
                                    </button>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
              </div>

            </div>
        </div>  
    </div>

    {{-- Datatable --}}
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="cost_of_sale_detail_report_table">
                        <thead>
                            <tr>
                                <th style="width: 7%">{{ __('accounting.date') }}</th>
                                <th style="width: 7%">{{ __('accounting.code') }}</th>
                                <th style="width: 26%">{{ __('accounting.description') }}</th>
                                <th style="width: 25%">{{ __('credit.observations') }}</th>
                                <th style="width: 5%">{{ __('accounting.type') }}</th>
                                <th style="width: 9%">{{ __('accounting.reference') }}</th>
                                <th style="width: 7%">{{ __('accounting.inflow') }}</th>
                                <th style="width: 7%">{{ __('accounting.outflow') }}</th>
                                <th style="width: 7%">{{ __('accounting.annulled') }}</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="bg-gray font-17 text-center footer-total">
                                <td colspan="6">&nbsp;</td>
                                <td id="footer_input"></td>
                                <td id="footer_output"></td>
                                <td id="footer_annulled"></td>
                            </tr>
                        </tfoot>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</section>
<!-- /.content -->

@endsection

@section('javascript')
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
@endsection