@extends('layouts.restaurant')
@section('title', __( 'restaurant.orders' ))

@section('content')

<!-- Main content -->
<section class="content min-height-90hv no-print">
    
    <div class="row">
        <div class="col-md-12 text-center">
            <h3>@lang( 'order.orders' )</h3>
        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <div class="col-sm-4 col-md-3 col-lg-2">
                <div class="form-group">
                    {!! Form::text('date_range', @format_date('now - 6 days') . ' - ' . @format_date('now'), ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'order_date_range', 'readonly']) !!}
                </div>
            </div>
            <div class="col-sm-4 col-md-3 col-lg-2">
                <div class="form-group">
                    {!! Form::select("status", $status, null, ["class" => "form-control select2",
                    "id" => "status", "style" => "width: 100%;", "placeholder" => __("order.all_status")]) !!}
                </div>
            </div>
            <div class="col-sm-4 col-md-3 col-lg-2">
                <div class="form-group">
                    {!! Form::select("delivery_type", $delivery_types, null, ["class" => "form-control select2",
                    "id" => "delivery_type", "style" => "width: 100%;", "placeholder" => __("order.delivery_type")]) !!}
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3">
                <div class="form-group">
                    {!! Form::select("customer", $customers, null, ["class" => "form-control select2",
                    "id" => "customer", "style" => "width: 100%;", "placeholder" => __("order.all_customers")]) !!}
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3">
                <div class="form-group">
                    {!! Form::select("seller", $sellers, null, ["class" => "form-control select2",
                    "id" => "seller", "style" => "width: 100%;", "placeholder" => __("order.all_sellers")]) !!}
                </div>
            </div>
        </div>
    </div>

	<div class="box">
        <div class="box-header">
        	<h3 class="box-title">@lang( 'order.all_your_orders' )</h3>

            <div class="pull-right">
                {!! Form::open(['id' => 'form_order_planner_reports', 'action' => 'OrderController@orderPlannerReport', 'method' => 'post', 'target' => '_blank']) !!}
                    <button type="button" class="btn btn-sm btn-primary" id="refresh_orders" style="margin-left: 5px;">
                        <i class="fa fa-refresh"></i> @lang( 'restaurant.refresh' )
                    </button>        
                    <button type="submit" class="btn btn-sm btn-success btn-report" data-type="excel"
                        title="@lang('order.tooltip_export_order_dispatch_report')" style="margin-left: 5px;">
                        <i class="fa fa-file-excel-o"></i> @lang( 'EXCEL' )
                    </button>
                    <button type="submit" class="btn btn-sm btn-danger btn-report" data-type="pdf"
                        title="@lang('order.tooltip_export_order_dispatch_report')" style="margin-left: 5px;">
                        <i class="fa fa-file-pdf-o"></i> @lang( 'PDF' )
                    </button>
                    {!! Form::hidden("report_type", null, ["id" => "report_type"]) !!}
                    {!! Form::hidden("start_date", null, ["id" => "start_date_report"]) !!}
                    {!! Form::hidden("end_date", null, ["id" => "end_date_report"]) !!}
                    {!! Form::hidden("status", null, ["id" => "status_report"]) !!}
                    {!! Form::hidden("delivery_type", null, ["id" => "delivery_type_report"]) !!}
                    {!! Form::hidden("customer", null, ["id" => "customer_report"]) !!}
                    {!! Form::hidden("seller", null, ["id" => "seller_report"]) !!}
                {!! Form::close() !!}  
            </div>          
        </div>
        <div class="box-body">
        	 <input type="hidden" id="orders_for" value="waiter">
            <div class="row" id="orders_div">
             @include('order.partials.show_orders')
            </div>
        </div>
        <div class="overlay hide">
          <i class="fa fa-refresh fa-spin"></i>
        </div>
    </div>
    <div class="modal fade show_order_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
</section>
<!-- /.content -->

@endsection

@section('javascript')
    <script type="text/javascript">
    //date filter for orders

        $(document).on('click', '#refresh_orders', function(){
            refresh_orders();
        });

        
        $(document).on("change", "select#customer, select#status, select#delivery_type, select#seller", function(){
            refresh_orders();
        });

        $(document).ready(function(){
            if ($('input#order_date_range').length == 1) {
                var start = moment().subtract(6, 'days');
                var end = moment();
                dateRangeSettings['startDate'] = start;
                dateRangeSettings['endDate'] = end;

                $('input#order_date_range').daterangepicker(
                    dateRangeSettings,
                    function() {
                        $('input#order_date_range').val(start.format(moment_date_format) + ' - ' + end.format(moment_date_format));
                        refresh_orders();
                    }
                );

                $('input#order_date_range').val(start.format(moment_date_format) + ' - ' + end.format(moment_date_format));

                $('input#order_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('input#order_date_range').val(start.format(moment_date_format) + ' - ' + end.format(moment_date_format));
                    refresh_orders();
                });
            }
            
            $(document).on('click', 'a.change_order_status_btn', function(e){
                e.preventDefault();
                var href = $(this).data('href');
                var status = $(this).closest("div").find("input#order_status").val();
                var invoiced = $(this).closest("div").find("input#invoiced").val();

                if(status == "prepared" && invoiced == 0){
                    Swal.fire(
                        LANG.notice,
                        LANG.order_not_invoiced,
                        'warning'
                    );

                    return false;
                }

                if(status == "opened" || status == "in_preparation"){
                    $.ajax({
                        type: "GET",
                        url: "/orders/get_in_charge_people",
                        dataType: "json",
                        success: function(Empleados){
                            Swal.fire({
                                title: LANG.in_charge_person,
                                input: 'select',
                                inputOptions: {
                                    Empleados
                                },
                                inputPlaceholder: LANG.select_person_in_charge,
                                showCancelButton: true,
                                inputValidator: (employee) => {
                                    if(employee){
                                        $.ajax({
                                            method: "GET",
                                            url: href + "/" + employee,
                                            dataType: "json",
                                            success: function(result){
                                                if(result.success == true){
                                                    refresh_orders();
                                                    toastr.success(result.msg);
                                                } else {
                                                    toastr.error(result.msg);
                                                }
                                            }
                                        });
                                    } else{
                                        Swal.fire(
                                            LANG.notice,
                                            LANG.employee_not_chosen,
                                            'warning'
                                        );
                                    }
                                }
                            });
                        }
                    });
                } else {
                    swal({
                        title: LANG.sure,
                        icon: "info",
                        buttons: true,
                        }).then((willDelete) => {
                            if (willDelete) {
                                $.ajax({
                                    method: "GET",
                                    url: href,
                                    dataType: "json",
                                    success: function(result){
                                        if(result.success == true){
                                            refresh_orders();
                                            toastr.success(result.msg);
                                        } else {
                                            toastr.error(result.msg);
                                        }
                                    }
                                });
                            }
                        });
                }
            });

            /** Show order */
            $(document).on("click", "a.show_order", function(e){
                e.preventDefault();

                $.ajax({
                    url: $(this).data("href"),
                    dataType: "html",
                    success: function(data){
                        $('div.show_order_modal').html(data).modal('show');
                    }
                });
            });

            $('div.show_order_modal').on('shown.bs.modal', function () {
                calculate_discount();
            });
            
            /*
            $('input#order_date_range').on('change', function(){
                refresh_orders();
            });*/

            /** Set report type to hidden input */
            $(document).on("click", "button.btn-report", function(){
                var type = $(this).data("type");
                var start_date = $('input#order_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end_date = $('input#order_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                var status = $("select#status").val();
                var delivery_type = $("select#delivery_type").val();
                var customer = $("select#customer").val();
                var seller = $("select#seller").val();

                $("form#form_order_planner_reports input#report_type").val(type);
                $("form#form_order_planner_reports input#start_date_report").val(start_date);
                $("form#form_order_planner_reports input#end_date_report").val(end_date);
                $("form#form_order_planner_reports input#status_report").val(status);
                $("form#form_order_planner_reports input#delivery_type_report").val(delivery_type);
                $("form#form_order_planner_reports input#customer_report").val(customer);
                $("form#form_order_planner_reports input#seller_report").val(seller);
            });
        });

        function refresh_orders(){
            var customer = $("select#customer").val();
            var status = $("select#status").val();
            var delivery_type = $("select#delivery_type").val();
            var seller = $("select#seller").val();
            let start_date = $('input#order_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
            let end_date = $('input#order_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');

            $('div.overlay').removeClass('hide');
            $.ajax({
                method: "POST",
                url: '/orders/refresh-orders-list',
                dataType: "html",
                data : {
                    customer : customer,
                    status : status,
                    delivery_type : delivery_type,
                    seller : seller,
                    start_date: start_date,
                    end_date: end_date
                },
                success: function(data){
                    $('div#orders_div').html(data);
                    $('div.overlay').addClass('hide');
                }
            });            
        }

        function calculate_line_discount(tr){
            var tax_detail = $("select#tax_detail").val();
            var quantity_line = __read_number(tr.find("input#quantity"));
            var unit_price = tax_detail == "yes" ? __read_number(tr.find("input#unit_price_exc_tax")) : __read_number(tr.find("input#unit_price_inc_tax"));
            var discount_line_type = tr.find("select#discount_line_type").val();
            var discount_line_amount = __read_number(tr.find("input#discount_line_amount"));
            var discount_calculated_line_amount_text = tr.find("span#discount_calculated_line_amount_text");
            var discount_calculated_line_amount = tr.find("input#discount_calculated_line_amount");


            var discount = __calculate_amount(discount_line_type, discount_line_amount, unit_price) * quantity_line;
            discount_calculated_line_amount_text.text(__currency_trans_from_en(discount.toFixed(2)));
            __write_number(discount_calculated_line_amount, discount, false, 4);

            tr = discount_calculated_line_amount.closest("tr");
            calculate_row_line_total(tr);
            calculate_tax_line_amount(tr, discount)
        }

        function calculate_tax_line_amount(tr, discount){
            var tax_detail = $("select#tax_detail").val();
            var tax_line_amount = tr.find("input#tax_line_amount");
            var tax_percent = tr.find("input#tax_percent");
            var quantity_line = __read_number(tr.find("input#quantity"));
            var unit_price_inc_tax = __read_number(tr.find("input#unit_price_inc_tax"));
            var tax_line_amount_value = 0.00;
            
            if(tax_detail == "no"){
                var line_total_inc_tax = (quantity_line * unit_price_inc_tax) - discount;
                tax_line_amount_value = (line_total_inc_tax / (tax_percent + 1) * tax_percent)
            }
            __write_number(tax_line_amount, tax_line_amount_value, false, 4);
        }

        function calculate_row_line_total(tr){
            var tax_detail = $("select#tax_detail").val();
            var quantity_line = __read_number(tr.find("input#quantity"));
            var unit_price = tax_detail == "yes" ? __read_number(tr.find("input#unit_price_exc_tax")) : __read_number(tr.find("input#unit_price_inc_tax"));
            var discount_calculated_line_amount = __read_number(tr.find("input#discount_calculated_line_amount"));
            var line_total_text = tr.find("span#line_total_text");
            var line_total = tr.find("input#line_total");

            var row_line_total = ((quantity_line * unit_price) - discount_calculated_line_amount);
            __write_number(line_total, row_line_total, false, 4);
            line_total_text.text(__currency_trans_from_en(row_line_total.toFixed(2)));

            calculate_discount();
        }

        function calculate_subtotal(){
            var subtotal_amount = 0;
            
            $("table#order_table tbody tr").each(function(){
                var line_total = __read_number($(this).find("input#line_total"));
                subtotal_amount += line_total;
            });

            return subtotal_amount.toFixed(4);
        }

        function get_tax_percent(){
            var tax_percent = 0;
            
            $("table#order_table tbody tr").each(function(){
                tax_percent = __read_number($(this).find("input#tax_percent"));
            });

            return tax_percent;
        }

        function calculate_discount(){
            var subtotal = calculate_subtotal();
            var discount_type = $("select#discount_type").val();
            var discount_amount = __read_number($("input#discount_amount"));
            var discount_calculated_amount_text = $("span#discount_calculated_amount_text");
            var discount_calculated_amount = $("input#discount_calculated_amount");

            var discount = __calculate_amount(discount_type, discount_amount, subtotal);
            discount_calculated_amount_text.text(__currency_trans_from_en(discount.toFixed(2)));
            __write_number(discount_calculated_amount, discount, false, 4);

            calculate_taxes(discount);
        }

        function calculate_taxes(discount){
            var tax_detail = $("select#tax_detail").val();
            var tax_amount = $("input#tax_amount");
            var tax_amount_text = $("span#tax_amount_text");
            var tax_amount_value = 0;

            if(tax_detail == "yes"){
                var tax_percent = get_tax_percent();
                var subtotal_amount = calculate_subtotal() - discount;

                tax_amount_value = subtotal_amount * tax_percent;
                tax_amount_text.text(__currency_trans_from_en(tax_amount_value.toFixed(2)));
                __write_number(tax_amount, tax_amount_value, false, 4);

            } else{
                tax_amount_text.text(__currency_trans_from_en(tax_amount_value.toFixed(2)));
                __write_number(tax_amount, tax_amount_value, false, 4);
            }

            calculate_total_final();
        }

        function calculate_total_final(){
            var discount_calculated_amount = __read_number($("input#discount_calculated_amount"));
            var tax_detail = $("select#tax_detail").val();
            var subtotal_text = $("span#subtotal_text");
            var subtotal = $("input#subtotal");
            var total_final_text = $("span#total_final_text");
            var total_final = $("input#total_final");
            var subtotal_value = calculate_subtotal();
            var total_total_value = 0;

            if(tax_detail == "yes"){
                var tax_amount = __read_number($("input#tax_amount"));
                total_total_value = (subtotal_value - discount_calculated_amount) + tax_amount;
            } else{
                total_total_value = subtotal_value - discount_calculated_amount;
            }

            subtotal_text.text(__currency_trans_from_en(subtotal_value));
            total_final_text.text(__currency_trans_from_en(total_total_value));
            __write_number(subtotal, subtotal_value, false, 4);
            __write_number(total_final, total_total_value, false, 4);
        }
    </script>
@endsection