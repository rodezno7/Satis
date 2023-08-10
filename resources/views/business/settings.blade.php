@extends('layouts.app')
@section('title', __('business.business_settings'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('business.business_settings')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content"> 
    {!! Form::open(['url' => action('BusinessController@postBusinessSettings'), 'method' => 'post', 'id' => 'bussiness_edit_form',
    'files' => true ]) !!}
    <div class="row">
        <div class="col-xs-12">
         <!--  <pos-tab-container> -->
            <div class="col-xs-12 pos-tab-container">
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
                    <div class="list-group">
                        <a href="#" class="list-group-item text-center active">@lang('business.business')</a>
                        <!--a href="#" class="list-group-item text-center">@lang('business.tax') @show_tooltip(__('tooltip.business_tax'))</a-->
                        <a href="#" class="list-group-item text-center">@lang('business.product')</a>
                        <a href="#" class="list-group-item text-center">@lang('business.sale')</a>
                        <a href="#" class="list-group-item text-center">@lang('purchase.purchases')</a>
                        <a href="#" class="list-group-item text-center">@lang('expense.expenses')</a>
                        <a href="#" class="list-group-item text-center">@lang('lang_v1.stock_transfers')</a>
                        @if(!config('constants.disable_expiry', true))
                        <a href="#" class="list-group-item text-center">@lang('business.dashboard')</a>
                        @endif
                        <a href="#" class="list-group-item text-center">@lang('business.system')</a>
                        <a href="#" class="list-group-item text-center">@lang('lang_v1.prefixes')</a>
                        <!--a href="#" class="list-group-item text-center">@lang('lang_v1.email_settings')</a-->
                        <!--a href="#" class="list-group-item text-center">@lang('lang_v1.sms_settings')</a-->
                        <a href="#" class="list-group-item text-center">@lang('sale.pos_sale')</a>
                        @if(auth()->user()->can('business_settings.access_module'))
                            <a href="#" class="list-group-item text-center">@lang('lang_v1.modules')</a>
                        @endif
                        <a href="#" class="list-group-item text-center">@lang('quote.quotes')</a>
                        <a href="#" class="list-group-item text-center">@lang('customer.customers')</a>
                    </div>
                </div>
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                    <!-- tab 1 start -->
                    @include('business.partials.settings_business')
                    <!-- tab 1 end -->
                    <!-- tab 2 start -->
                    {{--@include('business.partials.settings_tax')--}}
                    <!-- tab 2 end -->
                    <!-- tab 3 start -->
                    @include('business.partials.settings_product')
                    <!-- tab 3 end -->
                    <!-- tab 4 start -->
                    @include('business.partials.settings_sales')
                    <!-- tab 4 end -->
                    <!-- tab 5 start -->
                    @include('business.partials.settings_purchase')
                    <!-- tab 5 end -->
                    @include('business.partials.settings_expense')
                    <!-- tab 6 start -->
                    @include('business.partials.settings_stock_transfer')
                    <!-- tab 6 end -->
                    <!-- tab 7 start -->
                    @if(!config('constants.disable_expiry', true))
                    @include('business.partials.settings_dashboard')
                    @endif
                    <!-- tab 7 end -->
                    <!-- tab 8 start -->
                    @include('business.partials.settings_system')
                    <!-- tab 8 end -->
                    <!-- tab 9 start -->
                    @include('business.partials.settings_prefixes')
                    <!-- tab 9 end -->
                    <!-- tab 10 start -->
                    {{--@include('business.partials.settings_email')--}}
                    <!-- tab 10 end -->
                    <!-- tab 11 start -->
                    {{--@include('business.partials.settings_sms')--}}
                    <!-- tab 11 end -->
                    <!-- tab 12 start -->
                    @include('business.partials.settings_pos')
                    <!-- tab 12 end -->
                    <!-- tab 13 start -->
                    @if(auth()->user()->can('business_settings.access_module'))
                        @include('business.partials.settings_modules')
                    @endif
                    <!-- tab 13 end -->
                    <!-- tab 14 start -->
                    @include('business.partials.settings_quotes')
                    <!-- tab 14 end -->
                    <!-- tab 15 start -->
                    @include('business.partials.settings_customers')
                    <!-- tab 15 end -->
                </div>
            </div>
            <!--  </pos-tab-container> -->
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <button class="btn btn-danger pull-right" type="submit">@lang('business.update_settings')</button>
        </div>
    </div>
    {!! Form::close() !!}
</section>
<!-- /.content -->
@endsection
@section('javascript')
<script>

    $('#default_sales_discount').change(function() {

        discount = parseFloat($('#default_sales_discount').val());
        $('#limit_discount').val(discount.toFixed(2));

    });

    $('#limit_discount').change(function(){

        setLimitDiscount();
        
    });

    function setLimitDiscount() {

        discount = parseFloat($('#default_sales_discount').val());
        limit = parseFloat($('#limit_discount').val());

        if (limit < discount) {

            Swal.fire
            ({
                title: '{{__('sale.limit_error')}}',
                icon: "error",
            });

            $('#default_sales_discount').val('');
            $('#limit_discount').val('');



        }

    }

    $('#annull_sale_expiry').on('click', function(){

        if($(this).is(":checked")){
            Swal.fire({
                title: LANG.sure,
                text: "{{ __('business.annull_sale_expiry_message') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('messages.accept') }}",
                cancelButtonText: "{{ __('messages.cancel') }}"
            }).then((willAccept) => {
                if (willAccept.value) {
                    route = '/business/update_annull_sale';
                    $.ajax({
                        method: "PUT",
                        url: route,
                        data: {'annull_sale_expiry': 1},
                        dataType: "json",
                        success: function(result) {
                            if (result.success == true) {
                                Swal.fire({
                                    title: result.msg,
                                    icon: "info",
                                    timer: 4000,
                                    showConfirmButton: false,
                                });
                                $("#annull_sale_expiry").prop("checked", true);
                            } else {
                                Swal.fire({
                                    title: result.msg,
                                    icon: "error",
                                });
                            }
                        }
                    });
                }else{
                    $("#annull_sale_expiry").prop("checked", false);
                }
            });
        }
            //Si se ha desmarcado se ejecuta el siguiente mensaje.
        else{
            route = '/business/update_annull_sale';
            $.ajax({
                method: "PUT",
                url: route,
                data: {'annull_sale_expiry': 0},
                dataType: "json",
                success: function(result) {
                    if (result.success == true) {
                        Swal.fire({
                            title: result.msg_dos,
                            icon: "success",
                            timer: 4000,
                            showConfirmButton: false,
                        });
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
</script>
@endsection