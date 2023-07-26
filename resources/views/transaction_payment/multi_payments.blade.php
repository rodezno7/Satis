@extends('layouts.app')
@section('title', __('payment.multi_payments'))

@section('css')
    <style>
        .row {
            margin-top: 15px !important;
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
                                    <th>{{ mb_strtoupper(__('lang_v1.date')) }}</th>
                                    <th>{{ mb_strtoupper(__('lang_v1.correlative')) }}</th>
                                    <th>{{ mb_strtoupper(__('purchase.due')) }}</th>
                                    <th>{{ mb_strtoupper(__('sale.total')) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Invoinces table records here -->
                            </tbody>
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
                minimumInputLength: 5,
                escapeMarkup: function (markup) {
                    return markup;
                }
            });

            $("select#customer").on("select2:select", function (d) {
                if (d.params.data.id) {
                    $('select#search_invoices').removeAttr('disabled');
                }
            });

            /** Get due invoices */
            let customer_id = $('select#customer').val();
            $("select#search_invoices").select2({
                ajax: {
                    type: "get",
                    url: "/sells/get-trans-due-by-customer/"+ customer_id,
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
                placeholder: LANG.search_invoices,
                minimumInputLength: 1,
                escapeMarkup: function (markup) {
                    return markup;
                }
            });

        });
    </script>
@endsection
