@extends('layouts.app')
@section('title', __('payment.multi_payments'))

@section('css')
    <style>
        .row {
            margin-top: 15px !important;
        }

        table#invoices tbody tr td {
            vertical-align: middle;
        }

        table#invoices thead tr th {
            text-align: center;
        }

        .fa.fa-times {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang('payment.multi_payments')
        <small>Registrar pagos múltiples</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    {!! Form::label('customer', __('customer.customer')) !!}
                    {!! Form::select('customer', [], null, ['class' => 'form-control', 'id' => 'customer']) !!}
                    {!! Form::hidden(null, null, ['id' => 'customer_id']) !!}
                </div>
                <div class="col-md-3 col-sm-6">
                    {!! Form::label('search_invoice', __('sale.search_invoices')) !!}
                    {!! Form::select('search_invoice', [], null, ['class' => 'form-control', 'id' => 'search_invoices', 'disabled']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="invoices">
                            <thead>
                                <tr>
                                    <th style="width: 17%;">{{ mb_strtoupper(__('lang_v1.date')) }}</th>
                                    <th style="width: 17%;">{{ mb_strtoupper(__('lang_v1.correlative')) }}</th>
                                    <th style="width: 17%;">{{ mb_strtoupper(__('purchase.due')) }}</th>
                                    <th style="width: 17%;">{{ mb_strtoupper(__('payment.payment')) }}</th>
                                    <th style="width: 17%;">{{ mb_strtoupper(__('sale.total')) }}</th>
                                    <th>{{ mb_strtoupper(__('messages.actions')) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Invoinces table records here -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" style="text-align: center;">
                                        TOTALES
                                    </th>
                                    <th id="total_due" style='text-align: right;'></th>
                                    <th id="total_payment" style='text-align: right;'></th>
                                    <th id="total_final" style='text-align: right;'></th>
                                    <th>&nbsp;</th>
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
@stop
@section('javascript')
    <script>
        $(function () {
            /** Get customers */
            $("select#customer").select2({
                ajax: {
                    type: "get",
                    url: "/customers/get_only_customers",
                    dataType: "json",
                    data: function(params){
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                },
                placeholder: LANG.search_customer,
                minimumInputLength: 3,
                escapeMarkup: function (markup) {
                    return markup;
                }
            });

            $("select#customer").on("select2:select", function (d) {
                let id = d.params.data.id;

                if (id) {
                    $('input#customer_id').val(id);
                    $('table#invoices tbody').empty();
                    updateInvoiceTableTotals();
                    $('select#search_invoices').removeAttr('disabled');
                }
            });

            /** Get due invoices */
            $('select#search_invoices').select2({
                ajax: {
                    type: 'get',
                    url: function () {
                        customer_id = $('input#customer_id').val();
                        return '/sells/get-trans-due-by-customer/'+ customer_id;
                    },
                    dataType: 'json',
                    data: function(params){
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                },
                placeholder: LANG.search_invoices,
                minimumInputLength: 1,
                escapeMarkup: function (markup) {
                    return markup;
                }
            });

            $('select#search_invoices').on('select2:select', function (d) {
                let data = d.params.data;
                let table = $('table#invoices tbody');

                if (transExists(data.transaction_id)) {
                    swal({
                        title: LANG.warning,
                        text: LANG.wont_be_able_revert,
                        icon: 'warning'
                    });
                }

                let tr = `
                    <tr>
                        <td>
                            ${data.transaction_date}
                            <input type='hidden'
                                class='transaction_id'
                                data-name='transaction_id'
                                value='${data.transaction_id}' />
                        </td>
                        <td style='text-align: center;'>${data.correlative}</td>
                        <td style='text-align: right;'>
                            <span class='display_currency'
                                data-currency_symbol='true'
                                data-currency_precission='2'>
                                ${data.balance}
                            </span>
                            <input type='hidden' class='balance' value='${data.balance}' />
                        </td>
                        <td>
                            <input type='text' value='${parseFloat(data.balance).toFixed(2)}'
                                class='form-control input-sm input_number payment'
                                style='text-align: right;' data-name='amount'>
                        </td>
                        <td style='text-align: right;'>
                            <span class='display_currency'
                                data-currency_symbol='true'
                                data-currency_precission='2'>
                                ${data.final_total}
                            </span>
                            <input type='hidden' class='total_final' value='${data.final_total}' />
                        </td>
                        <td style='text-align: center;'>
                            <i class='fa fa-times text-danger' title='${LANG.delete}'></i>
                        </td>
                    </tr>
                `;

                table.prepend(tr);
                updateInvoiceTableIndexes();
                updateInvoiceTableTotals();
                $('select#search_invoices').val('').trigger('change');
                __currency_convert_recursively($('table#invoices'));
            });

            $(document).on('click', 'i.fa-times', function () {
                let tr = $(this).closest('tr');

                swal({
                    title: LANG.sure,
                    text: LANG.wont_be_able_revert,
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        tr.remove();
                        updateInvoiceTableIndexes();
                        updateInvoiceTableTotals();
                        __currency_convert_recursively($('table#invoices'));
                    }
                });
            });

            /**
             * Update indexes on invoices table
             * @return void
            */
            function updateInvoiceTableIndexes() {
                let rows = $('table#invoices tbody tr');

                $.each(rows, function (index, row) {
                    let inputs = $(row).find('input');

                    $.each(inputs, function (i, input) {
                        let name = $(input).data('name');
                        $(input).attr('name', 'payments['+ index +']['+ name +']');
                    });
                });
            }

            /**
             * Update total on invoices table
             * 
             * @return void
            */
            function updateInvoiceTableTotals() {
                let rows = $('table#invoices tbody tr');
                let foot = $('table#invoices tfoot tr');
                let total_due = 0;
                let total_payment = 0;
                let total_final = 0;

                $.each(rows, function (index, row) {
                    let due = parseFloat($(row).find('input.balance').val());
                    let pay = parseFloat($(row).find('input.payment').val());
                    let total = parseFloat($(row).find('input.total_final').val());

                    total_due += due;
                    total_payment += pay;
                    total_final += total;
                });

                if (!(total_due > 0) &&
                    !(total_payment > 0) &&
                    !(total_final > 0)) {
                        foot.find('th#total_due').empty();
                        foot.find('th#total_payment').empty();
                        foot.find('th#total_final').empty();

                        return;
                }

                foot.find('th#total_due')
                    .html('<span class="display_currency" data-currency_symbol="true" data-currency_precission="2">'+ total_due +'</span>');
                foot.find('th#total_payment')
                    .html('<span class="display_currency" data-currency_symbol="true" data-currency_precission="2">'+ total_payment +'</span>');
                foot.find('th#total_final')
                    .html('<span class="display_currency" data-currency_symbol="true" data-currency_precission="2">'+ total_final +'</span>');
            }

            /**
             * Determinate if transactions already exists
             * 
             * @return boolean
            */
            function transExists(id) {
                let rows = $('table#invoices tbody tr');
                let exists = false;

                $.each(rows, function (index, row) {
                    let trasaction_id = $(row).find('input.transaction_id').val();
                   
                    if (transaction_id == id) {
                        exists = true;
                    }
                });

                return exists;
            }
        });
    </script>
@endsection
