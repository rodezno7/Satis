@extends('layouts.app')
@section('title', __('lab_order.lab_orders'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>@lang( 'lab_order.lab_orders' )
        <small>@lang( 'lab_order.manage_lab_orders' )</small>
    </h1>
</section>

<!-- Main content -->
<section class="content no-print">

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">
                @lang('lab_order.lab_orders_by_location')
            </h3>
        </div>
        
        <div class="box-body">
            @can('sell.view')
            <div class="table-responsive">
            <table class="table table-bordered table-striped show_detail" id="lab_orders_table">
                <thead>
                    <tr>
                        <th>@lang('lab_order.no_order')</th>
                        <th>@lang('document_type.invoice')</th>
                        <th>@lang('accounting.location')</th>
                        <th>@lang('contact.customer')</th>
                        <th>@lang('graduation_card.patient')</th>
                        <th>@lang('cashier.status')</th>
                        <th>@lang('business.register')</th>
                        <th>@lang('lab_order.delivery')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
            </div>
            @endcan
        </div>
    </div>

    <div class="modal fade view_lab_order_modal" tabindex="-1"
        role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>

<section id="order_section" class="print_section"></section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).ready(function() {
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};

        var lab_orders_table = $('#lab_orders_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/lab-orders-by-location',
            columnDefs: [{
                "targets": [8],
                "orderable": false,
                "searchable": false
            }]
        });

        $('#lab_orders_table').on('click', 'tr', function(e) {
            if (!$(e.target).is('td.selectable_td input[type=checkbox]') && !$(e.target).is('td.selectable_td') && !$(e.target).is('td.clickable_td') && !$(e.target).is('a') && !$(e.target).is('button') && !$(e.target).hasClass('label') && !$(e.target).is('li') && $(this).data("href") && !$(e.target).is('i')) {
                $.ajax({
                    url: $(this).data("href"),
                    dataType: "html",
                    success: function(result) {
                        $('.view_lab_order_modal').html(result).modal('show');
                    }
                });
            }
        });
    });

    function viewOrder(id) {
        var route = '/lab-orders/' + id;
        $.ajax({
            url: route,
            dataType: "html",
            success: function(result) {
                $('.view_lab_order_modal').html(result).modal('show');
            }
        });
    }

    $(document).on('click', 'a.change-status', function(e) {
        e.preventDefault();
        var href = $(this).data('href');
        $.ajax({
            method: "GET",
            url: href,
            dataType: "json",
            success: function(result) {
                if (result.success) {
                    Swal.fire({
                        title: result.msg,
                        icon: "success",
                        timer: 3000,
                        showConfirmButton: false,
                    });
                    $('#lab_orders_table').DataTable().ajax.reload(null, false);
                } else {
                    Swal.fire
                    ({
                        title: ""+result.msg+"",
                        icon: "error",
                    });
                }
            }
        });
    });

    $(document).on('click', 'a.print-order', function(e) {
        e.preventDefault();
        var href = $(this).data('href');
        $.ajax({
            method: "GET",
            url: href,
            dataType: "json",
            success: function(result) {
                if (result.success == 1 && result.order.html_content != '') {
                    $('#lab_orders_table').DataTable().ajax.reload(null, false);
                    $('#order_section').html(result.order.html_content);
                    __currency_convert_recursively($('#order_section'));
                    setTimeout(function() { window.print(); }, 1000);
                } else {
                    Swal.fire
                    ({
                        title: ""+result.msg+"",
                        icon: "error",
                    });
                }
            }
        });
    });
</script>
@endsection