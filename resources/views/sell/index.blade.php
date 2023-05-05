@extends('layouts.app')

@section('title', __('lang_v1.all_sales'))

@section('content')
{{-- Content Header (Page header) --}}
<section class="content-header no-print">
    <h1>@lang( 'sale.sells')</h1>
</section>

{{-- Main content --}}
<section class="content no-print">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang( 'lang_v1.all_sales')</h3>
            @can('sell.create')
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{ action('SellPosController@create') }}">
                    <i class="fa fa-plus"></i> @lang('messages.add')
                </a>
            </div>
            @endcan
        </div>
        <div class="box-body">
            @can('sell.view')
            <div class="row">
                {{-- Location --}}
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label("location", __("kardex.location") . ":") !!}
                        @if (is_null($default_location))
                        {!! Form::select("select_location", $locations, null,
                            ["class" => "form-control select2", "id" => "select_location"]) !!}
                        {!! Form::hidden('location', 'all', ['id' => 'location']) !!}
                        @else
                        {!! Form::select("select_location", $locations, null,
                            ["class" => "form-control select2", "id" => "location", 'disabled']) !!}
                        {!! Form::hidden('location', $default_location, ['id' => 'location']) !!}
                        @endif
                    </div>
                </div>

                {{-- Document type --}}
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label("document_type", __("document_type.title") . ":") !!}
                        {!! Form::select("select_document_type", $document_types, null,
                            ["class" => "form-control select2", "id" => "select_document_type"]) !!}
                        {!! Form::hidden('document_type', 'all', ['id' => 'document_type']) !!}
                    </div>
                </div>

                {{-- Payment status --}}
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label("payment_status", __("sale.payment_status") . ":") !!}
                        {!! Form::select("payment_status", $payment_status, null,
                            ["class" => "form-control select2", "id" => "payment_status"]) !!}
                    </div>
                </div>

                {{-- Date --}}
                <div class="col-sm-3">
                    <div class="form-group">
                        <div class="input-group">
                            <button type="button" class="btn btn-primary" id="sell_date_filter" style="margin-top: 25px;">
                                <span>
                                    <i class="fa fa-calendar"></i>&nbsp; {{ __('messages.filter_by_date') }}
                                </span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- App business --}}
                <input type="hidden" id="app-business" value="{{ config('app.business') }}">
            </div>
            @if (count($sellers) > 0 && config('app.business') != "optics")
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label(__("quote.seller_name")) !!}
                        {!! Form::select("seller", $sellers, null, ["class" => "form-control select2",
                            'id' => 'seller', 'placeholder' => __('sale.all_sellers')]) !!}
                    </div>
                </div>
            </div>
            @endif
            <div class="table-responsive">
                <table class="table table-striped table-text-center" id="sell_table" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center">@lang('messages.date')</th>
                            <th class="text-center">@lang('sale.document_no')</th>
                            <th class="text-center">@lang('document_type.title')</th>
                            <th>@lang('contact.customer')</th>
                            @if (config('app.business') == 'optics')
                            <th class="text-center">@lang('sale.location')</th>
                            <th class="text-center">@lang('sale.payment_status')</th>
    						<th class="text-center">@lang('sale.total_invoice')</th>
                            <th class="text-center">@lang('lang_v1.payment_note')</th>
                            <th class="text-center">@lang('sale.total_paid')</th>
                            <th class="text-center">@lang('sale.total_balance_due')</th>
                            @else
                            <th class="text-center">@lang('sale.payment_status')</th>
                            <th class="text-center">@lang('lang_v1.payment_method')</th>
                            <th class="text-center">@lang('sale.subtotal')</th>
                            <th class="text-center">@lang('sale.discount')</th>
                            <th class="text-center">@lang('tax_rate.taxes')</th>
                            <th class="text-center">@lang('sale.total_amount')</th>
                            @endif
                            <th class="text-center">@lang('messages.actions')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray font-17 footer-total text-center">
                            @if (config('app.business') == 'optics')
                            <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                            <td id="footer_payment_status_count"></td>
                            <td><span class="display_currency" id="footer_sale_total" data-currency_symbol ="true"></span></td>
                            <td>&nbsp;</td>
                            <td><span class="display_currency" id="footer_total_paid" data-currency_symbol ="true"></span></td>
                            <td class="text-left"><small>@lang('lang_v1.sell_due') - <span class="display_currency" id="footer_total_remaining" data-currency_symbol ="true"></span><br>@lang('lang_v1.sell_return_due') - <span class="display_currency" id="footer_total_sell_return_due" data-currency_symbol ="true"></span></small></td>
                            @else
                            <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                            <td id="footer_payment_status_count"></td>
                            <td>&nbsp;</td>
                            <td><span class="display_currency" id="footer_sale_subtotal" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="footer_sale_discount_amount" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="footer_sale_tax_amount" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="footer_sale_total" data-currency_symbol="true"></span></td>
                            @endif
                            <td>&nbsp;</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endcan
        </div>
    </div>
</section>

<div class="modal fade payment_modal"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade transaction_modal_edit" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_invoice_payment_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

{{-- This will be printed --}}
{{-- <section class="invoice print_section" id="receipt_section">
</section> --}}

@stop

@section('javascript')
{{-- Moment JS --}}
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>

{{-- Datetime JS --}}
<script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.13/dataRender/datetime.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $.fn.dataTable.ext.errMode = 'throw';

        // Date range as a button
        dateRangeSettings['startDate'] = moment().subtract(30, 'days');
        dateRangeSettings['endDate'] = moment();

        $('#sell_date_filter').daterangepicker(
            dateRangeSettings,

            function (start, end) {
                $('#sell_date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                sell_table.ajax.reload();
            }
        );

        $('#sell_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#sell_date_filter').html('<i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}');
            sell_table.ajax.reload();
        });

        let app_business = $('#app-business').val();

        let table_columns = [];

        if (app_business == 'optics') {
            table_columns =  [
                { data: 'transaction_date', name: 'transaction_date', className: 'text-center' },
                { data: 'correlative', name: 'correlative', className: 'text-center' },
                { data: 'document_name', name: 'document_name', className: 'text-center' },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'location', name: 'location', className: 'text-center' },
                { data: 'payment_status', name: 'payment_status', className: 'text-center' },
                { data: 'final_total', name: 'final_total', className: 'text-center' },
                { data: 'note', name: 'note', className: 'text-center' },
                { data: 'total_paid', name: 'total_paid', className: 'text-center' },
                { data: 'total_remaining', name: 'total_remaining', className: 'text-center' },
                { data: 'action', orderable: false, searchable: false, className: 'text-center' }
            ];
        } else {
            table_columns =  [
                { data: 'transaction_date', name: 'transaction_date', className: 'text-center' },
                { data: 'correlative', name: 'correlative', className: 'text-center' },
                { data: 'document_name', name: 'document_name', className: 'text-center' },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'payment_status', name: 'payment_status', className: 'text-center' },
                { data: 'method', name: 'method', className: 'text-center' },
                { data: 'total_before_tax', name: 'total_before_tax', className: 'text-right' },
                { data: 'discount_amount', name: 'discount_amount', className: 'text-right' },
                { data: 'tax_amount', name: 'tax_amount', className: 'text-right' },
                { data: 'final_total', name: 'final_total', className: 'text-right' },
                { data: 'action', orderable: false, searchable: false, className: 'text-center' }
            ];
        }

        sell_table = $('#sell_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/sells",
                "data": function(d) {
                    var start = $('#sell_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#sell_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                    d.location_id = $("#location").val();
                    d.document_type_id = $("input#document_type").val();
                    d.is_direct_sale = 1;
                    d.payment_status = $("#payment_status").val();
                    d.seller_id = $("select#seller").val() ?? 0;
                }
            },
            columns: table_columns,
            columnDefs: [{
                targets: 0,
                render: $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'DD/MM/YYYY hh:mm a')
            }],
            "fnDrawCallback": function (oSettings) {
                let app_business = $('#app-business').val();

                if (app_business == 'optics') {
                    $('#footer_sale_total').text(sum_table_col($('#sell_table'), 'final-total'));
                    $('#footer_total_paid').text(sum_table_col($('#sell_table'), 'total_paid'));
                    $('#footer_total_remaining').text(sum_table_col($('#sell_table'), 'total_remaining'));
                    $('#footer_total_sell_return_due').text(sum_table_col($('#sell_table'), 'sell_return_due'));
                    $('#footer_payment_status_count ').html(__sum_status_html($('#sell_table'), 'payment-status-label'));

                } else {
                    $('#footer_sale_total').text(sum_table_col($('#sell_table'), 'final-total'));
                    $('#footer_sale_subtotal').text(sum_table_col($('#sell_table'), 'subtotal'));
                    $('#footer_sale_discount_amount').text(sum_table_col($('#sell_table'), 'discount_amount'));
                    $('#footer_sale_tax_amount').text(sum_table_col($('#sell_table'), 'tax-amount'));
                    $('#footer_payment_status_count').html(__sum_status_html($('#sell_table'), 'payment-status-label'));
                }

                __currency_convert_recursively($('#sell_table'));
            },
            createdRow: function (row, data, dataIndex) {
                $(row).find('td:eq(4)').attr('class', 'clickable_td');

                let app_business = $('#app-business').val();

                if (app_business == 'optics') {
                    $(row).find('td:eq(6)').attr('class', 'clickable_td_return text-right');
                } else {
                    $(row).find('td:eq(9)').attr('class', 'clickable_td_return text-right');
                }
            }
        });

        // On change of dataTables_filter input
        $('.dataTables_filter input').off().on('change', function() {
            $('#sell_table').DataTable().search(this.value.trim(), false, false).draw();
        });

        // Location filter
        $('select#select_location').on('change', function() {
            $("input#location").val($("select#select_location").val());
            sell_table.ajax.reload();
        });

        // Document type filter
        $('select#select_document_type').on('change', function() {
            $("input#document_type").val($("select#select_document_type").val());
            sell_table.ajax.reload();
        });

        // Seller filter
        $('select#seller').on('change', function() {
            sell_table.ajax.reload();
        });

        // Payment status filter
        $('select#payment_status').on('change', function() {
            sell_table.ajax.reload();
        });

        $('.transaction_modal_edit').on('shown.bs.modal', function(e) {
            let modal = $(this);

            /** Get parent correlatives */
            modal.find("select#return_parent_id").select2({
                ajax: {
                    type: "get",
                    url: "/sells/get-parent-correlative",
                    dataType: "json",
                    data: function(params){
                        let location = modal.find("input#location_id").val();
                        let customer = modal.find("input#customer_id").val();
                        
                        return {
                            q: params.term,
                            location: location,
                            customer: customer
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                },
                minimumInputLength: 1,
                escapeMarkup: function (markup) {
                    return markup;
                }
            });

            /** Set parent correlative */
            modal.find("select#return_parent_id").on("select2:select", function (e) {
                let corr = e.params.data.correlative;
                modal.find("input#parent_correlative").val(corr);
            });
        });
    });

    // On click of edit_transaction_button link
    $(document).on('click', 'a.edit_transaction_button', function() {
        $("div.transaction_modal_edit").load($(this).data('href'), function() {
            $(this).modal('show');
            $('form#transaction_edit_form').submit(function(e) {
                e.preventDefault();
                $(this).find('button[type="submit"]').attr('disabled', false);
                var data = $(this).serialize();

                $.ajax({
                    method: "POST",
                    url: $(this).attr("action"),
                    dataType: "json",
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            $("#sell_table").DataTable().ajax.reload();
                            $('div.transaction_modal_edit').modal('hide');
                            Swal.fire({
                                title: result.msg,
                                icon: "success",
                                timer: 2000,
                                showConfirmButton: false,
                            });
                            $('#content').hide();
                        } else {
                            Swal.fire({
                                title: result.msg,
                                icon: "error",
                            });
                        }
                    }
                });
            });
        });
    });

    // On click of btn-actions button
    $(document).on('click', '.btn-actions', function() {
        let id = $(this).data('transaction-id');
        add_toggle_dropdown($(this), id);
    });

    function add_toggle_dropdown(btn, id) {
        $.ajax({
            method: "GET",
            url: '/pos/get_toggle_dropdown/' + id,
            dataType: 'html',
            success: function(data) {
                btn.closest('.btn-group').find('ul').html(data);
            }
        });
    }
</script>

<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection
