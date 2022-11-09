@extends('layouts.app')
@section('title', __('purchase.purchases'))

@section('css')
    <style>
        .select2-dropdown {
            z-index: 1061;
        }
    </style>
@endsection
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>@lang('purchase.purchases')
            <small></small>
        </h1>
        <!-- <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                <li class="active">Here</li>
            </ol> -->
    </section>

    <!-- Main content -->
    <section class="content no-print">

        <div class="box">
            <div class="box-header">
                <h3 class="box-title">@lang('purchase.all_purchases')</h3>
                @can('purchase.create')
                    <div class="box-tools">
                        <a class="btn btn-block btn-primary" id="btn_add">
                            <i class="fa fa-plus"></i> @lang('messages.add')</a>
                    </div>
                @endcan
            </div>
            <div class="box-body">
                @can('purchase.view')
                    <div class="row">
                        {{-- purchase-type --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label('purchase-type', __('purchase.purchase_type')) !!}

                                {!! Form::select(
                                    'purchase_type',
                                    ['national' => __('purchase.national'), 'international' => __('purchase.importation')],
                                    null,
                                    ['id' => 'purchase-type', 'class' => 'form-control select2', 'placeholder' => __('kardex.all_2')]
                                ) !!}
                            </div>
                        </div>

                        {{-- payment-status --}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label('payment-status', __('sale.payment_status')) !!}

                                {!! Form::select(
                                    'payment-status',
                                    ['all' => __("kardex.all"), 'paid' => __('sale.paid'), 'pending' => __('sale.pending')],
                                    null,
                                    ['class' => 'form-control select2', 'id' => 'payment-status']
                                ) !!}
                            </div>
                        </div>

                        {{-- daterange-btn --}}
                        <div class="col-sm-3">
                            <div class="form-group" style="margin-top: 25px;">
                                <div class="input-group">
                                    <button type="button" class="btn btn-primary" id="purchase_daterange">
                                        <span>
                                            <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                                        </span>
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                    {!! Form::hidden("start_date", date('Y-m-d', strtotime('- 29 days')), ['id' => 'start_date']) !!}
                                    {!! Form::hidden("end_date", date('Y-m-d'), ['id' => 'end_date']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped ajax_view table-text-center" id="purchase_table" width="100%">
                            <thead>
                                <tr>
                                    <th>@lang('messages.date')</th>
                                    <th>@lang('purchase.ref_no')</th>
                                    <th>@lang('purchase.location')</th>
                                    <th>@lang('purchase.supplier')</th>
                                    <th>@lang('purchase.purchase_type')</th>
                                    <th>@lang('purchase.purchase_status')</th>
                                    <th>@lang('purchase.payment_status')</th>
                                    <th>@lang('purchase.grand_total')</th>
                                    <th>@lang('customer.remaining_credit') &nbsp;&nbsp;<i class="fa fa-info-circle text-info"
                                            data-toggle="tooltip" data-placement="bottom" data-html="true"
                                            data-original-title="{{ __('messages.purchase_due_tooltip') }}"
                                            aria-hidden="true"></i></th>
                                    <th>@lang('messages.action')</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="bg-gray font-17 text-center footer-total">
                                    <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                                    <td id="footer_status_count"></td>
                                    <td id="footer_payment_status_count"></td>
                                    <td><span class="display_currency" id="footer_purchase_total"
                                            data-currency_symbol="true"></span></td>
                                    <td class="text-left"><small>@lang('report.purchase_due') - <span class="display_currency"
                                                id="footer_total_due" data-currency_symbol="true"></span><br>
                                            @lang('lang_v1.purchase_return') - <span class="display_currency"
                                                id="footer_total_purchase_return_due" data-currency_symbol="true"></span>
                                        </small></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endcan
            </div>
        </div>

        <div class="modal fade product_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

        <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

        <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

        <div class="modal fade purchase_return_discount" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>

    <section id="receipt_section" class="print_section"></section>

    <!-- /.content -->
@stop
@section('javascript')
    <script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection
