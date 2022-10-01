@extends('layouts.app')
@section('title', __('contact.pay_term'))
    <link rel="stylesheet" href="{{ asset('accounting/css/jquery-confirm.min.css') }}">
    <script th:src="@{/js/datatables.min.js}"></script>
    <style>
        .swal2-popup {
            font-size: 1.4rem !important;
        }

    </style>
@section('content')

    <!-- Contect Header (Page Header) -->
    <section class="content-header">
        <h1>@lang( 'payment.manage_payment_term' )</small></h1>
    </section>

    <section class="content">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <h3 class="box-title">@lang( 'payment.all_payment' )</h3>
                @can('payment_term.create')
                    <div class="box-tools">
                        <button type="button" class="btn btn-block btn-primary btn-modal"
                            data-href="{{ action('PaymentTermController@create') }}" data-container=".employees_modal">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                    </div>
                @endcan
            </div>
            <div class="box-body">
                @can('payment_term.view')
                    <div id="lista" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-condensed table-hover"
                                id="payment_terms_table" width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('payment.name')</th>
                                        <th>@lang('payment.description')</th>
                                        <th>@lang('payment.days')</th>
                                        <th>@lang('payment.action')</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                @endcan
            </div>
        </div>

        <div class="modal fade employees_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
@endsection
@section('javascript')
    <script type="text/javascript" src="{{ asset('js/sweetalert2.js') }}"></script>
    <script type="text/javascript">
    $(document).ready(function() {
            dataTableBusiness();
    });

        function dataTableBusiness() {
            var payment_terms = $("#payment_terms_table").DataTable({
                pageLength: 25,
                //deferRender: true,
                processing: true,
                serverSide: true,
                ajax: "/payment_terms/get-data",
                columns: [{
                        data: 'name',
                    },
                    {
                        data: 'description',
                    },
                    {
                        data: 'days',
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                    "targets": '_all',
                    "className": "text-center"
                }]
            });

            console.log(payment_terms);

        }

        $('.employees_modal').on('shown.bs.modal', function() {

            $('#hired_date').datepicker({
                autoclose: true,
                format: datepicker_date_format
            });

            $('#birth_date').datepicker({
                autoclose: true,
                format: datepicker_date_format
            });

            $('#fired_date').datepicker({
                autoclose: true,
                format: datepicker_date_format
            });

        });

        $(document).on('submit', 'form#payment_terms_add_form', function(e) {
            debugger;
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
                        $("#payment_terms_table").DataTable().ajax.reload();
                        $('div.employees_modal').modal('hide');
                        Swal.fire({
                            title: "{{ __('payment.payment_terms_create') }}",
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false,
                        });
                        $("#content").hide();
                    } else {
                        Swal.fire({
                            title: result.msg,
                            icon: "error",
                        });
                    }
                }
            });
        });

        $(document).on('click', 'a.edit_payment_terms_button', function() {
            $("div.employees_modal").load($(this).data('href'), function() {
                $(this).modal('show');

                $('form#payment_term_edit_form').submit(function(e) {
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
                                $("#payment_terms_table").DataTable().ajax.reload();
                                $('div.employees_modal').modal('hide');
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

        function deletePaymentTerm(id) {
            Swal.fire({
                title: LANG.sure,
                text: "{{ __('messages.delete_content') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('messages.accept') }}",
                cancelButtonText: "{{ __('messages.cancel') }}"
            }).then((willDelete) => {
                if (willDelete.value) {
                    route = '/payment-terms/' + id;
                    $.ajax({
                        url: route,
                        type: 'DELETE',
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == true) {
                                Swal.fire({
                                    title: result.msg,
                                    icon: "success",
                                    timer: 3000,
                                    showConfirmButton: false,
                                });
                                $("#payment_terms_table").DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire({
                                    title: result.msg,
                                    icon: "error",
                                });
                            }
                        }
                    });
                }
            });
        }

    </script>
@endsection
