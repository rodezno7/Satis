@extends('layouts.app')
@section('title', __('contact.view_contact'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <div class="row text-center">
        <div class="col-sm-12">
            <img src="{{asset('img/default/satis.png')}}" alt="" style="width: 120px; height: 100px;" class="img-fluid">
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content no-print">
    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">
                        <i class="fa fa-user margin-r-5"></i>
                        @if($contact->type == 'both') 
                            @lang('contact.contact_info', ['contacts' => __('contact.contact')])
                        @else
                            @lang('contact.contact_info', ['contacts' => __('contact.' . $contact->type)])
                        @endif
                    </h3>
                </div>
                <div class="box-body">
                    <span id="view_contact_page"></span>
                    <div class="row">
                        <div class="col-sm-6">
                            <div>
                                @if ($contact->supplier_business_name)
                                    <strong>{{ $contact->supplier_business_name }}</strong><br>
                                    <small>{{ $contact->name }}</small>
                                @else
                                    <strong>{{ $contact->name }}</strong>    
                                @endif
                                <br><br>
                                <strong>@lang('business.address')</strong>
                                <p class="text-muted">
                                    @if($contact->landmark)
                                        {{ $contact->landmark }}
                                    @endif
        
                                    {{ ', ' . $contact->city }}
        
                                    @if($contact->state)
                                        {{ ', ' . $contact->state }}
                                    @endif
                                    <br>
                                    @if($contact->country)
                                        {{ $contact->country }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div>
                                <strong> @lang('contact.mobile')</strong>
                                <p class="text-muted">
                                    {{ $contact->mobile }}
                                </p>
                                @if($contact->landline)
                                    <strong> @lang('contact.landline')</strong>
                                    <p class="text-muted">
                                        {{ $contact->landline }}
                                    </p>
                                @endif
                                @if($contact->alternate_number)
                                    <strong> @lang('business.alternate_contact_number')</strong>
                                    <p class="text-muted">
                                        {{ $contact->alternate_number }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        @if ($contact->is_supplier == 1)
                        <div class="col-md-4">
                            <strong>{{__('contact.is_supplier')}}</strong>
                            <p class="text-muted">
                                {{ __('messages.yes') }}
                            </p>
                        </div>
                        @endif
                        @if ($contact->is_provider == 1)
                        <div class="col-md-4">
                            <strong>{{__('contact.is_provider')}}</strong>
                            <p class="text-muted">
                                {{ __('messages.yes') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">
                        <i class="fa fa-money margin-r-5"></i>
                        {{__('contact.balance')}}
                    </h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div>
                            @if( $contact->type == 'supplier' || $contact->type == 'both')
                                <strong>@lang('report.total_purchase')</strong>
                                <p class="text-muted">
                                <span class="display_currency" data-currency_symbol="true">
                                {{ $contact->total_purchase }}</span>
                                </p>
                                <strong>@lang('contact.total_purchase_paid')</strong>
                                <p class="text-muted">
                                <span class="display_currency" data-currency_symbol="true">
                                {{ $contact->purchase_paid }}</span>
                                </p>
                                <strong>@lang('contact.total_purchase_due')</strong>
                                <p class="text-muted">
                                <span class="display_currency" data-currency_symbol="true">
                                {{ $contact->total_purchase - $contact->purchase_paid }}</span>
                                </p>
                            @endif
                            @if( $contact->type == 'customer' || $contact->type == 'both')
                                <strong>@lang('report.total_sell')</strong>
                                <p class="text-muted">
                                <span class="display_currency" data-currency_symbol="true">
                                {{ $contact->total_invoice }}</span>
                                </p>
                                <strong>@lang('contact.total_sale_paid')</strong>
                                <p class="text-muted">
                                <span class="display_currency" data-currency_symbol="true">
                                {{ $contact->invoice_received }}</span>
                                </p>
                                <strong>@lang('contact.total_sale_due')</strong>
                                <p class="text-muted">
                                <span class="display_currency" data-currency_symbol="true">
                                {{ $contact->total_invoice - $contact->invoice_received }}</span>
                                </p>
                            @endif
                            @if(!empty($contact->opening_balance) && $contact->opening_balance != '0.00')
                                <strong>@lang('lang_v1.opening_balance')</strong>
                                <p class="text-muted">
                                <span class="display_currency" data-currency_symbol="true">
                                {{ $contact->opening_balance }}</span>
                                </p>
                                <strong>@lang('lang_v1.opening_balance_due')</strong>
                                <p class="text-muted">
                                <span class="display_currency" data-currency_symbol="true">
                                {{ $contact->opening_balance - $contact->opening_balance_paid }}</span>
                                </p>
                            @endif
                            </div>
                        </div>
                        @if( $contact->type == 'supplier' || $contact->type == 'both')
                            <div class="clearfix"></div>
                            <div class="col-sm-12" style="display:none;">
                                @if(($contact->total_purchase - $contact->purchase_paid) > 0)
                                    <a href="{{action('TransactionPaymentController@getPayContactDue', [$contact->id])}}?type=purchase" class="pay_purchase_due btn btn-primary btn-sm pull-right"><i class="fa fa-money" aria-hidden="true"></i> @lang("contact.pay_due_amount")</a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box">
        <div class="box-header">
        	<h3 class="box-title">
                <i class="fa fa-info-circle margin-r-5" aria-hidden="true"></i>
                {{__('contact.tax_info_title')}}
            </h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-sm-12">
                    @if ($contact->supplier_business_name)
                    <div class="col-md-4">
                        <strong>{{__('contact.social_reason')}}</strong>
                        <p class="text-muted">
                            {{ $contact->name }}
                        </p>
                    </div>
                    @endif
                    @if ($contact->name)
                    <div class="col-md-4">
                        <strong>{{__('contact.business_name')}}</strong>
                        <p class="text-muted">
                            {{ $contact->supplier_business_name }}
                        </p>
                    </div>
                    @endif
                    @if ($contact->organization_type)
                    <div class="col-md-4">
                        <strong>{{__('contact.type_person')}}</strong>
                        @if($contact->organization_type == 'natural')
                        <p class="text-muted">
                            {{__('contact.type_person_natural')}}
                        </p>
                        @elseif ($contact->organization_type == 'juridica')
                        <p class="text-muted">
                            {{__('contact.type_person_business')}}
                        </p>
                        @endif
                    </div>
                    @endif
                    @if ($contact->business_activity)
                    <div class="col-md-4">
                        <strong>{{__('contact.business_activity')}}</strong>
                        <p class="text-muted">
                            {{ $contact->business_activity }}
                        </p>
                    </div>
                    @endif
                    @if ($contact->nit)
                    <div class="col-md-4">
                        <strong>NIT</strong>
                        <p class="text-muted">
                            {{ $contact->nit }}
                        </p>
                    </div>
                    @endif
                    @if ($contact->dni)
                    <div class="col-md-4">
                        <strong>{{__('contact.id_number')}}</strong>
                        <p class="text-muted">
                            {{ $contact->dni }}
                        </p>
                    </div>
                    @endif
                    @if ($contact->tax_number)
                    <div class="col-md-4">
                        <strong>@lang('contact.tax_no')</strong>
                        <p class="text-muted">
                            {{ $contact->tax_number }}
                        </p>
                    </div>
                    @endif
                    <div class="col-md-4">
                        <strong>@lang('business.address')</strong>
                        <p class="text-muted">
                            @if ($contact->country || $contact->state || $contact->city || $contact->landmark)
                                @if($contact->landmark)
                                    {{ $contact->landmark }}
                                @endif
                                @if($contact->city)
                                    {{ ', ' . $contact->city }}
                                @endif
                                @if($contact->state)
                                    {{ ', ' . $contact->state }}
                                @endif
                                <br>
                                @if($contact->country)
                                    {{ ', ' . $contact->country }}
                                @endif
                            @else
                                {{'n/a'}}
                            @endif
                        </p>
                    </div>
                    @if ($contact->is_exempt)
                    <div class="col-md-4">
                        <strong>@lang('contact.is_exempt')</strong>&nbsp;@show_tooltip(__("contact.no_taxes_applied"))
                        <p class="text-muted">
                            @if ($contact->is_exempt == 1)
                                {{ __('messages.yes') }}
                            @else
                                {{ __('No') }}
                            @endif
                        </p>
                    </div>
                    @endif
                </div>
                <div class="col-sm-6"></div>
            </div>
        </div>
    </div>
    
    
    <!-- list purchases -->
    @if( in_array($contact->type, ['supplier', 'both']) )
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">
                    <i class="fa fa-shopping-cart margin-r-5"></i>
                    @lang( 'contact.all_purchases_linked_to_this_contact')
                </h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="input-group">
                              <button type="button" class="btn btn-primary" id="daterange-btn">
                                <span>
                                  <i class="fa fa-calendar"></i> {{ __("messages.filter_by_date") }}
                                </span>
                                <i class="fa fa-caret-down"></i>
                              </button>
                            </div>
                          </div>
                    </div>
                    <div class="col-sm-12">
                        <table class="table table-striped table-text-center ajax_view" id="purchase_table" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center">@lang('messages.date')</th>
                                    <th class="text-center">@lang('sale.document_no')</th>
                                    <th class="text-center">@lang('document_type.title')</th>
                                    <th>@lang('purchase.supplier')</th>
                                    <th class="text-center">@lang('purchase.purchase_status')</th>
                                    <th class="text-center">@lang('purchase.payment_status')</th>
                                    <th class="text-center">@lang('purchase.total_invoiced')</th>
                                    <th class="text-center">@lang('customer.remaining_credit') &nbsp;&nbsp;<i class="fa fa-info-circle text-info" data-toggle="tooltip" data-placement="bottom" data-html="true" data-original-title="{{ __('messages.purchase_due_tooltip')}}" aria-hidden="true"></i></th>
                                    <th class="text-center">@lang('messages.actions')</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- list sales -->
    @if( in_array($contact->type, ['customer', 'both']) )
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">
                    <i class="fa fa-money margin-r-5"></i>
                    @lang( 'contact.all_sells_linked_to_this_contact')
                </h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="input-group">
                              <button type="button" class="btn btn-primary" id="sells-daterange-btn">
                                <span>
                                  <i class="fa fa-calendar"></i> {{ __("messages.filter_by_date") }}
                                </span>
                                <i class="fa fa-caret-down"></i>
                              </button>
                            </div>
                          </div>
                    </div>
                    <div class="col-sm-12">
                        <table class="table table-striped table-text-center ajax_view" id="sell_table" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-center">@lang('messages.date')</th>
                                    <th class="text-center">@lang('sale.document_no')</th>
                                    <th>@lang('sale.customer_name')</th>
                                    <th class="text-center">@lang('sale.payment_status')</th>
                                    <th class="text-center">@lang('sale.total_amount')</th>
                                    <th class="text-center">@lang('sale.total_paid')</th>
                                    <th class="text-center">@lang('sale.total_remaining')</th>
                                    <th class="text-center">@lang('messages.actions')</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel"></div>
@stop
@section('javascript')
<script type="text/javascript">
$(document).ready( function(){
    //Purchase table
    purchase_table = $('#purchase_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: '/purchases?supplier_id={{ $contact->id }}',
        columnDefs: [ {
            "targets": 6,
            "orderable": false,
            "searchable": false
        } ],
        columns: [
            { data: 'transaction_date', name: 'transaction_date', className: 'text-center' },
            { data: 'ref_no', name: 'ref_no', className: 'text-center' },
            { data: 'doc_name', name: 'document_types.document_name', className: 'text-center' },
            { data: 'name', name: 'contacts.name' },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'payment_status', name: 'payment_status', className: 'text-center' },
            { data: 'final_total', name: 'final_total', className: 'text-right' },
            { data: 'payment_due', name: 'payment_due', className: 'text-right' },
            { data: 'action', name: 'action', className: 'text-center' }
        ],
        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('#purchase_table'));
        },
        createdRow: function( row, data, dataIndex ) {
            $( row ).find('td:eq(4)').attr('class', 'clickable_td text-center');
        }
    });
    //Date range as a button
    $('#daterange-btn').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#daterange-btn span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            purchase_table.ajax.url( '/purchases?supplier_id={{ $contact->id }}&start_date=' + start.format('YYYY-MM-DD') +
                '&end_date=' + end.format('YYYY-MM-DD') ).load();
        }
    );
    $('#daterange-btn').on('cancel.daterangepicker', function(ev, picker) {
        purchase_table.ajax.url( '/purchases?supplier_id={{ $contact->id }}').load();
        $('#daterange-btn span').html('<i class="fa fa-calendar"></i> {{ __("messages.filter_by_date") }}');
    });

    var sell_table = $('#sell_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: '/sells?customer_id={{ $contact->id }}',
        columnDefs: [ {
            "targets": 7,
            "orderable": false,
            "searchable": false
        } ],
        columns: [
            { data: 'transaction_date', name: 'transaction_date'  },
            { data: 'invoice_no', name: 'invoice_no'},
            { data: 'name', name: 'contacts.name'},
            { data: 'payment_status', name: 'payment_status'},
            { data: 'final_total', name: 'final_total'},
            { data: 'total_paid', searchable: false},
            { data: 'total_remaining', name: 'total_remaining'},
            { data: 'action', name: 'action'}
        ],
        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('#purchase_table'));
        },
        createdRow: function( row, data, dataIndex ) {
            $( row ).find('td:eq(3)').attr('class', 'clickable_td');
        }
    });
    $('#sells-daterange-btn').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#sells-daterange-btn span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            sell_table.ajax.url( '/sells?supplier_id={{ $contact->id }}&start_date=' + start.format('YYYY-MM-DD') +
                '&end_date=' + end.format('YYYY-MM-DD') ).load();
        }
    );
    $('#sells-daterange-btn').on('cancel.daterangepicker', function(ev, picker) {
        sell_table.ajax.url( '/sells?supplier_id={{ $contact->id }}').load();
        $('#daterange-btn span').html('<i class="fa fa-calendar"></i> {{ __("messages.filter_by_date") }}');
    });
});
</script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>

@endsection
