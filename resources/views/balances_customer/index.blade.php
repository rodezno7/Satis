@extends('layouts.app')

@section('title', __('customer.customer_balances'))

@section('css')
    <link rel="stylesheet" href="{{ asset('accounting/css/jquery-confirm.min.css') }}">

    <script th:src="@{/js/datatables.min.js}"></script>

    <style>
        .swal2-popup {
            font-size: 1.4rem !important;
            color: rgb(50, 243, 50);
        }

        table#balances_customer tbody tr {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <!-- Contect Header (Page Header) -->
    <section class="content-header section-customer-balances">
        <h1 style="display: inline-block">@lang('customer.customer_balances')</h1>
    </section>

    <section class="content-header section-account-statement" style="display: none;">
        <h1 style="display: inline-block">@lang('report.account_statement')</h1>

        <div class="pull-right" style="display: inline-block">
            <button type="button" class="btn btn-block btn-danger" id="back">
                @lang('crm.back')
            </button>
        </div>
    </section>

    <section class="content">
        <input type="hidden" id="business-start-date" value="{{ $business->start_date }}">

        <div class="boxform_u box-solid_u section-customer-balances">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label("customer_seller", __("customer.seller")) !!}
                            {!! Form::select("customer_seller", $sellers, null,
                                ['class' => 'form-control select2', 'id' => 'seller', 'placeholder' => __('customer.all_sellers')]) !!}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <div class="input-group">
                                <button type="button" class="btn btn-primary" id="cb_date_filter" style="margin-top: 25px;">
                                    <span>
                                        <i class="fa fa-calendar"></i>&nbsp; {{ __('messages.filter_by_date') }}
                                    </span>
                                    <i class="fa fa-caret-down"></i>
                                </button>
                                <input type="hidden" id="start_date_filter">
                                <input type="hidden" id="end_date_filter">
                            </div>
                        </div>
                    </div>
                </div>
                @can('customer.view')
                    <div class="table-responsive">
                        <table class="table table-striped table-text-center" id="balances_customer" width="100%" style="font-size: inherit;">
                            <thead>
                                <tr>
                                    <th>@lang('customer.code')</th>
                                    <th>@lang('contact.customer')</th>
                                    <th class="text-center">@lang('accounting.balance_to_date')</th>
                                    <th class="text-center">@lang('customer.final_total')</th>
                                    <th class="text-center">@lang('customer.remaining_credit')</th>
                                    <th class="text-center">@lang('customer.credit_limit')</th>
                                    <th class="text-center">@lang('customer.balance_limit')</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="bg-gray footer-total text-center">
                                    <td colspan="2"><strong>@lang('sale.total')</strong></td>
                                    <td><span class="display_currency" id="footer_balance_to_date" data-currency_symbol="true"></span></td>
                                    <td><span class="display_currency" id="footer_payments" data-currency_symbol="true"></span></td>
                                    <td><span class="display_currency" id="footer_remaining_credit" data-currency_symbol="true"></span></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endcan
            </div>
        </div>

        <div class="boxform_u box-solid_u section-account-statement" style="display: none;">
            {{-- Show customer balance --}}
            @include('balances_customer.show', ['date_filters' => $date_filters, 'months' => $months])
        </div>

        <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
        <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>
    </section>
@endsection

@section('javascript')
    {{-- Moment JS --}}
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>

    {{-- Datetime JS --}}
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.13/dataRender/datetime.js"></script>

    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>

    <script src="{{ asset('js/functions.js?v=' . $asset_v)}}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $.fn.modal.Constructor.prototype.enforceFocus = function() {};

            $.fn.dataTable.ext.errMode = 'throw';

            // On show of choose_month_modal modal
            $('.choose_month_modal').on('shown.bs.modal', function() {
                $(this).find('.select2').select2();
            });

            dataTableBalancesC();

            $(document).on('change', 'select#seller', function () {
                dataTableBalancesC();
            });

            $('#balances_customer').on('dblclick', 'tr', function() {
                var data = balances_customer.row(this).data();
                if (typeof data.id != "undefined") {
                    $(location).attr('href', `/customer-balances/getData/${data.id}`);
                }
            });

            // On click of a row in the balances_customer table
            $(document).on('click', 'table#balances_customer tbody tr', function(e) {
                if (! $(e.target).is('td.selectable_td input[type=checkbox]') &&
                    ! $(e.target).is('td.selectable_td') &&
                    ! $(e.target).is('td.clickable_td') &&
                    ! $(e.target).is('a') &&
                    ! $(e.target).is('button') &&
                    ! $(e.target).hasClass('label') &&
                    ! $(e.target).is('li') &&
                    $(this).data('href') &&
                    ! $(e.target).is('i')) {

                    $('.section-customer-balances').hide();
                    $('.section-account-statement').show();

                    customer_id = $(this).data('customer');

                    $('#customer_id').val(customer_id);
                    $('#email_customer_id').val(customer_id);

                    $('input[name="date-filter"]:checked').parent('label').removeClass('active');
                    $('input[name="date-filter"]:checked').attr('checked', false);

                    $('#range-date-filter').addClass('active');

                    let current_time = new Date();

                    let start = $('input#business-start-date').val();
                    let end = current_time.getFullYear() + '-12-31';

                    $('#range-date-filter').data('daterangepicker').setStartDate(moment(start, 'YYYY-MM-DD'));
                    $('#range-date-filter').data('daterangepicker').setEndDate(moment(end, 'YYYY-MM-DD'));

                    assignInputs(start, end);

                    loadCustomerData(customer_id);

                    loadSales(customer_id, start, end);
                }
            });

            /** Start customer balance filter by date */
            dateRangeSettings['startDate'] = moment().subtract(29, 'days');
            dateRangeSettings['endDate'] = moment();

            $('button#cb_date_filter').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('button#cb_date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    let start_date = $('button#cb_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    let end_date = $('button#cb_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                    $("input#start_date_filter").val(start_date);
                    $("input#end_date_filter").val(end_date);

                    dataTableBalancesC();
                }
            );

            $('button#cb_date_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('button#cb_date_filter').html('<i class="fa fa-calendar"></i> '+ LANG.filter_by_date);
                dataTableBalancesC();
            });
            /** End customer balance filter by date */

            // On change date-filter input
            $(document).on('change', 'input[name="date-filter"]', function() {
                let start = $('input[name="date-filter"]:checked').data('start');
		        let end = $('input[name="date-filter"]:checked').data('end');

                $('#range-date-filter').html('<span>{{ __('report.date_range') }}</span>');

                assignInputs(start, end);

                loadSales(customer_id, start, end);
            });

            // On click of back button
            $(document).on('click', '#back', function(e) {
                $('.section-account-statement').hide();
                $('.section-customer-balances').show();
            });

            // On submit of choose_month_form form
            $(document).on('submit', 'form#choose_month_form', function(e) {
                e.preventDefault();

                $('input[name="date-filter"]:checked').parent('label').removeClass('active');
                $('input[name="date-filter"]:checked').attr('checked', false);

                $('#range-date-filter').html('<span>{{ __('report.date_range') }}</span>');

                $.ajax({
                    method: 'post',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function(result) {
                        if (result.success === true) {
                            $('div#choose_month_modal').modal('hide');
                            assignInputs(result.start, result.end);
                            loadSales(customer_id, result.start, result.end);
                            
                        } else {
                            Swal.fire({
                                title: result.msg,
                                icon: 'error',
                            });
                        }
                    }
                });
            });

            //  On click of btn-actions button
            $(document).on('click', '.btn-actions', function() {
                let id = $(this).data('transaction-id');
                add_toggle_dropdown($(this), id);
            });

            // On click of send-account-statement button
            $(document).on('click', '#send-account-statement', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: LANG.sure,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: LANG.yes,
                    cancelButtonText: LANG.not,
                }).then((resul) => {
                    if (resul.isConfirmed) {
                        $('#form_email_account_statement').submit();
                    }
                });
            });

            // Date range picker
            $('#range-date-filter').daterangepicker(
                dateRangeSettings,
                function (startDate, endDate) {
                    $('#range-date-filter span').html(startDate.format(moment_date_format) + ' ~ ' + endDate.format(moment_date_format));

                    let start = $('#range-date-filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    let end = $('#range-date-filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    let customer_id = $('#customer_id').val();

                    $('input[name="date-filter"]:checked').parent('label').removeClass('active');
                    $('input[name="date-filter"]:checked').attr('checked', false);

                    assignInputs(start, end);

                    loadSales(customer_id, start, end);
                }
            );

            // On cancel of range-date-filter label
            $('#range-date-filter').on('cancel.daterangepicker', function (ev, picker) {
                $('#range-date-filter').html('<span>{{ __('report.date_range') }}</span>');

                let start = $('#range-date-filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                let end = $('#range-date-filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                let customer_id = $('#customer_id').val();

                $('input[name="date-filter"]:checked').parent('label').removeClass('active');
                $('input[name="date-filter"]:checked').attr('checked', false);

                assignInputs(start, end);

                loadSales(customer_id, start, end);
            });

            // On apply of range-date-filter label
            $('#range-date-filter').on('apply.daterangepicker', function (ev, picker) {
                $('#range-date-filter').html('<span>{{ __('report.date_range') }}</span>');

                let start = $('#range-date-filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                let end = $('#range-date-filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                let customer_id = $('#customer_id').val();

                $('input[name="date-filter"]:checked').parent('label').removeClass('active');
                $('input[name="date-filter"]:checked').attr('checked', false);

                $('#range-date-filter').addClass('active');

                assignInputs(start, end);

                loadSales(customer_id, start, end);
            });
        });

        function dataTableBalancesC() {
            $("table#balances_customer").DataTable().destroy();

            let start_date = $('input#start_date_filter').val();
            let end_date = $('input#end_date_filter').val();

            var balances_customer = $("#balances_customer").DataTable({
                pageLength: 25,
                // deferRender: true,
                processing: true,
                order: [
                    [0, 'asc']
                ],
                serverSide: true,
                ajax: {
                    url: "/balances_customer/get-data",
                    data: function (d) {
                        d.seller = $('select#seller').val() ?? 0;
                        d.start_date = start_date.length ? start_date : moment().subtract(29, 'days').format('YYYY-MM-DD');
                        d.end_date = end_date.length ? end_date : moment().format('YYYY-MM-DD');
                    }
                },
                columns: [
                    { data: 'id', className: 'text-center' },
                    { data: 'full_name', name: 'full_name' },
                    { data: 'final_total',name: 'final_total',searchable: false, className: 'text-center' },
                    { data: 'total_paid', name: 'total_paid', searchable: false, className: 'text-center' },
                    { data: 'total_remaining',name: 'total_remaining', searchable: false, className: 'text-center' },
                    { data: 'credit_limit', name: 'credit_limit', searchable:false, className: 'text-center' },
                    { data: 'limit_balance', name: 'limit_balance', searchable:false, className: 'text-center' }
                ],
                fnDrawCallback: function (oSettings) {
                    $('#footer_balance_to_date').text(sum_table_col($('#balances_customer'), 'balance_to_date'));
                    $('#footer_payments').text(sum_table_col($('#balances_customer'), 'payments'));
                    $('#footer_remaining_credit').text(sum_table_col($('#balances_customer'), 'remaining_credit'));

                    __currency_convert_recursively($('#balances_customer'));
                }
            });
        }

        /**
         * Load customer account statement data.
         * 
         * @param  int  id
         * @return void
         */
        function loadCustomerData(id) {
            $.ajax({
                method: 'get',
                url: '/customer-balances/' + id,
                dataType: 'json',
                success: function(result) {
                    $('#lbl-customer-name').text(result.name);
                    let total_remaining = parseFloat(result.final_total) - parseFloat(result.total_paid);
                    $('#lbl-total-remaining').text(__number_f(total_remaining, true, false, 2));
                    $('#lbl-credit-limit').text(__number_f(result.credit_limit, true, false, 2));
                    if(result.email){
                        $('#send_account_statement').show();   
                    }else{
                        $('#send_account_statement').hide();
                    }                    
                }
            });
        }

        /**
         * Get sales pending payment from the customer.
         * 
         * @param  int  id
         * @param  string  start
         * @param  string  end
         * @return void
         */
        function loadSales(id, start, end) {            
            let account_statement_table = $('#account-statement-table').DataTable();

            account_statement_table.destroy();

            account_statement_table = $('#account-statement-table').DataTable({
                pageLength: 25,
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/sells?customer_id=' + id,
                    data: function(d) {
                        d.customer_id = id;
                        d.start_date = start;
                        d.end_date = end;
                        d.is_direct_sale = 1;
                        d.payment_status = 1;
                    }
                },
                columns: [
                    { data: 'transaction_date', name: 'transaction_date', className: 'text-center' },
                    { data: 'due_date', name: 'due_date', className: 'text-center' },
                    { data: 'document_name', name: 'document_name', className: 'text-center' },
                    { data: 'correlative', name: 'correlative', className: 'text-center' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'payment_status', name: 'payment_status', className: 'text-center' },
                    { data: 'final_total_bc', name: 'final_total_bc', className: 'text-center' },
                    { data: 'total_paid', name: 'total_paid', className: 'text-center' },
                    { data: 'total_remaining', name: 'total_remaining', className: 'text-center' },
                    { data: 'action', orderable: false, searchable: false, className: 'text-center' }
                ],
                columnDefs: [
                    {
                        targets: 0,
                        render: $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'DD/MM/YYYY')
                    },
                    {
                        targets: 1,
                        render: function (data, type, row, meta) {
                            if (data !== null) {
                                return moment(data).format('DD/MM/YYYY');
                            } else {
                                return LANG.undefined;
                            }
                        },
                        createdCell: function(td, cellData, rowData, row, col) {                                                                                    
                            if (cellData < moment().format('YYYY-MM-DD')) {
                               $(td).addClass('bg-danger');                                
                            }
                        }
                    }
                ],
                fnDrawCallback: function(oSettings) {
                    $('#footer-final-total-bc').text(sum_table_col($('#account-statement-table'), 'final_total_bc'));
                    $('#footer-total-paid').text(sum_table_col($('#account-statement-table'), 'total_paid'));
                    $('#footer-total-remaining').text(sum_table_col($('#account-statement-table'), 'total_remaining'));

                    __currency_convert_recursively($('#account-statement-table'));
                }
            });
        }

        /**
         * Get options from the actions button.
         * 
         * @param  button  btn
         * @param  int  id
         * @return void
         */
        function add_toggle_dropdown(btn, id) {
            $.ajax({
                method: "get",
                url: '/balances_customer/get_toggle_dropdown/' + id,
                dataType: 'html',
                success: function(data) {
                    btn.closest('.btn-group').find('ul').html(data);
                }
            });
        }

        /**
         * Assign values ​​to inputs.
         * 
         * @param  date  start_date
         * @param  date  end_date
         * @return void
         */
        function assignInputs(start_date, end_date) {
            $('#start_date').val(start_date);
            $('#end_date').val(end_date);

            $('#email_start_date').val(start_date);
            $('#email_end_date').val(end_date);
        }
    </script>
@endsection
