@extends('layouts.app')
@section('title', __('reservation.reservations'))

@section('content')

{{-- Content Header (Page header) --}}
<section class="content-header">
    <h1>
        @lang('reservation.reservations')
        <small>@lang('reservation.manage_reservations')</small>
    </h1>
</section>

{{-- Main content --}}
<section class="content">
	<div class="boxform_u box-solid_u">
        <div class="box-header">
        	<h3 class="box-title">@lang('reservation.all_your_reservations')</h3>
        </div>
        
        <div class="box-body">
            @can('reservation.view')
            {{-- Form --}}
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
                          <button type="button" class="btn btn-primary" id="reservation_date_filter" style="margin-top: 25px;">
                            <span>
                              <i class="fa fa-calendar"></i>&nbsp; {{ __('messages.filter_by_date') }}
                            </span>
                            <i class="fa fa-caret-down"></i>
                          </button>
                        </div>
                      </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
            	<table class="table table-hover table-text-center" id="reservations_table" width="100%">
            		<thead>
            			<tr>
                            <th class="text-center">@lang('accounting.reference')</th>
                            <th class="text-center">@lang('messages.date')</th>
                            <th>@lang('crm.customer')</th>
                            <th class="text-center">@lang('quote.invoiced')</th>
                            <th class="text-center">@lang('reservation.total_amount')</th>
                            <th>@lang('lang_v1.payment_note')</th>
                            <th class="text-center">@lang('reservation.amount_paid')</th>
                            <th>@lang('quote.employee')</th>
                            <th class="text-center">@lang('messages.actions')</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            @endcan
        </div>
    </div>

    {{-- Show reservation modal --}}
    <div class="modal fade reservation_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

    {{-- Create payment modal --}}
    <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

    {{-- Edit payment modal --}}
    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</section>
<!-- /.content -->
@endsection

@section('javascript')
    {{-- Moment JS --}}
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>

    {{-- Datetime JS --}}
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.13/dataRender/datetime.js"></script>

    {{-- Reservation script --}}
    <script src="{{ asset('js/reservation.js?v=' . $asset_v) }}"></script>

    {{-- Payment script --}}
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection