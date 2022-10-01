@extends('layouts.app')
@section('title', __('contact.payment_commitments'))

@section('content')

<section class="content-header">
    <h1>@lang('contact.payment_commitments')
        <small>@lang('contact.manage_your_payment_commitments')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">

	<div class="box">
        <div class="box-header">
        	<h3 class="box-title">@lang('contact.all_your_payment_commitments')</h3>
            @can('payment_commitment.create')
            	<div class="box-tools">
                    <a class="btn btn-block btn-primary add-payment-commitment" href="{{action('PaymentCommitmentController@create')}}">
                        <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
            @endcan
        </div>
        <div class="box-body">
            @can('payment_commitment.view')
                <div class="table-responsive">
            	<table class="table table-bordered table-striped" style="width: 99.99%;" id="payment_commitments_table">
            		<thead>
            			<tr>
                            <th>@lang('lang_v1.date')</th>
                            <th>@lang('lang_v1.reference')</th>
                            <th>@lang('contact.type')</th>
                            <th>@lang('contact.supplier')</th>
                            <th>@lang('business.location')</th>
                            <th>@lang('lang_v1.amount')</th>
                            <th>@lang('messages.action')</th>
            			</tr>
            		</thead>
            	</table>
                </div>
            @endcan
        </div>
    </div>

    <div class="modal fade payment_commitments_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@endsection
@section('javascript')
    <script src="{{ 'js/payment_commitment.js?v='. $asset_v }}"></script>
@endsection