<table style="width:100%;">
	<thead>
		<tr>
			<td>

			@if(!empty($receipt_details->invoice_heading))

			@endif

			<p class="text-right">
			</p>

			</td>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>

@if(!empty($receipt_details->header_text))
	<div class="row invoice-info">
		<div class="col-xs-12">
			{!! $receipt_details->header_text !!}
		</div>
	</div>
@endif

<!-- business information here -->
<div class="row invoice-info">

	<div class="col-md-6 invoice-col width-50 color-555">
		
		<!-- Logo -->
		@if(!empty($receipt_details->logo))
			<br/>
		@endif

		<!-- Shop & Location Name  -->
		@if(!empty($receipt_details->display_name))
			<p>
				<h2>{{$receipt_details->display_name}}</h2>
				@if(!empty($receipt_details->address))
					{!! $receipt_details->address !!}
					{!! $receipt_details->email !!}
				@endif

				@if(!empty($receipt_details->contact))
					<br/>{{ $receipt_details->contact }}
				@endif

				@if(!empty($receipt_details->website))
					<br/>{{ $receipt_details->website }}
				@endif

				@if(!empty($receipt_details->tax_info1))
					<br/>{{ $receipt_details->tax_label1 }} {{ $receipt_details->tax_info1 }}
				@endif

				@if(!empty($receipt_details->tax_info2))
					<br/>{{ $receipt_details->tax_label2 }} {{ $receipt_details->tax_info2 }}
				@endif

				@if(!empty($receipt_details->location_custom_fields))
					<br/>{{ $receipt_details->location_custom_fields }}
				@endif
			</p>
		@endif

		<!-- Table information-->
        @if(!empty($receipt_details->table_label) || !empty($receipt_details->table))
        	<p>
				@if(!empty($receipt_details->table_label))
					{!! $receipt_details->table_label !!}
				@endif
				{{$receipt_details->table}}
			</p>
        @endif

		<!-- Waiter info -->
		@if(!empty($receipt_details->service_staff_label) || !empty($receipt_details->service_staff))
        	<p>
				@if(!empty($receipt_details->service_staff_label))
					{!! $receipt_details->service_staff_label !!}
				@endif
				{{$receipt_details->service_staff}}
			</p>
        @endif
	</div>
	
	<div class="col-md-6 invoice-col width-50  pull-right">
		<!-- Logo -->
		@if(!empty($receipt_details->logo))
			<img  src="{{$receipt_details->logo}}" class="img collage_image pull-right">
			<br/><br/>
		@endif

	</div>
</div>

<!-- business information here -->
<div class="row invoice-info">
	<div class="col-md-4 invoice-col width-33">
		<b>@lang('contact.customer')</b>
		<!-- customer info -->
		@if(!empty($receipt_details->custumer_name))
			{!! $receipt_details->custumer_name !!}
		@endif
		<br>
		<b>@lang('contact.mobile')</b>
		<!-- customer info -->
		@if(!empty($receipt_details->custumer_phone))
			{!! $receipt_details->custumer_phone !!}
		@endif
	</div>
	<div class="col-md-4 invoice-col width-33">
		<b> @lang('lang_v1.delivery_time') </b>
				
		<br/><b>@lang('business.address')</b>
		<!-- customer info -->
		@if(!empty($receipt_details->custumer_landmark))
			{!! $receipt_details->custumer_landmark !!}
		@endif
		
	</div>
		<div class="col-md-4 invoice-col width-33">
		
		<p class="text-left">
			@if(!empty($receipt_details->invoice_no_prefix))
				<b>{!! $receipt_details->invoice_no_prefix !!}</b>
			@endif
				{{$receipt_details->invoice_no}} <br/>

			<b> @lang('invoice.date') </b>
				{{$receipt_details->invoice_date}}
		</p>
	</div>


</div>



<div class="row color-555">
	<div class="col-xs-12">
		<br/>
		<table class="table table-bordered table-no-top-cell-border">
			<thead>
				<tr style="background-color: #357ca5 !important; color: white !important; font-size: 15px !important" class="table-no-side-cell-border table-no-top-cell-border text-center">
					<td style="background-color: #357ca5 !important; color: white !important;width: 5% !important">#</td>
					
					@php
						$p_width = 25;
					@endphp
					@if($receipt_details->show_cat_code != 1)
						@php
							$p_width = 35;
						@endphp
					@endif
					<td style="background-color: #357ca5 !important; color: white !important; width: {{$p_width}}% !important">
						{{$receipt_details->table_product_label}}
					</td>
					<td style="background-color: #357ca5 !important; color: white !important;width: 10% !important;">
						{{$receipt_details->table_qty_label}}
					</td>
					<td style="background-color: #357ca5 !important; color: white !important;width: 10% !important;">
						{{$receipt_details->table_unit_price_label}}
					</td>
					<td style="background-color: #357ca5 !important; color: white !important;width: 10% !important;">
						{{$receipt_details->table_subtotal_label}}
					</td>
				</tr>
			</thead>
			<tbody>
				@foreach($receipt_details->lines as $line)
					<tr>
						<td class="text-center">
							{{$loop->iteration}}
						</td>
						<td style="word-break: break-all;">
                            {{$line['name']}} {{$line['variation']}} 
                            @if(!empty($line['sub_sku'])), {{$line['sub_sku']}} @endif @if(!empty($line['brand'])), {{$line['brand']}} @endif
                            @if(!empty($line['sell_line_note']))({{$line['sell_line_note']}}) @endif
                            @if(!empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif 
                            @if(!empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif 
                        </td>

						<td class="text-right">
							{{$line['quantity']}} {{$line['units']}}
						</td>
						<td class="text-right">
							{{$line['unit_price_before_discount']}}
						</td>
						<td class="text-right">
							{{$line['line_total']}}
						</td>
					</tr>
					@if(!empty($line['modifiers']))
						@foreach($line['modifiers'] as $modifier)
							<tr>
								<td class="text-center">
									&nbsp;
								</td>
								<td>
		                            {{$modifier['name']}} {{$modifier['variation']}} 
		                            @if(!empty($modifier['sub_sku'])), {{$modifier['sub_sku']}} @endif 
		                            @if(!empty($modifier['sell_line_note']))({{$modifier['sell_line_note']}}) @endif 
		                        </td>

								@if($receipt_details->show_cat_code == 1)
			                        <td>
			                        	@if(!empty($modifier['cat_code']))
			                        		{{$modifier['cat_code']}}
			                        	@endif
			                        </td>
			                    @endif

								<td class="text-right">
									{{$modifier['quantity']}} {{$modifier['units']}}
								</td>
								<td class="text-center">
									{{$modifier['unit_price_exc_tax']}}
								</td>
								<td class="text-right">
									{{$modifier['line_total']}}
								</td>
							</tr>
						@endforeach
					@endif
				@endforeach

				@php
					$lines = count($receipt_details->lines);
				@endphp

				@for ($i = $lines; $i < 5; $i++)
    				<tr>
    					<td>&nbsp;</td>
    					<td>&nbsp;</td>
    					<td>&nbsp;</td>
    					<td>&nbsp;</td>
    					@if($receipt_details->show_cat_code == 1)
    						<td>&nbsp;</td>
    					@endif
    				</tr>
				@endfor

			</tbody>
		</table>
	</div>
</div>

<div class="row invoice-info color-555" style="page-break-inside: avoid !important">
	<div class="col-md-6 invoice-col width-50">
		<b class="pull-left">@lang('lang_v1.received_by') @lang('lang_v1.line')</b>
	</div>
</div>
<div class="row invoice-info color-555" style="page-break-inside: avoid !important">
	<div class="col-md-6 invoice-col width-50">
		<br/><br/>
		<b class="pull-left">@lang('lang_v1.line1')</b>
	</div>
</div>
<div class="row invoice-info color-555 pull-right width-33">
		<div class="col-md-6 invoice-col" style="width: 100%">
		@if(!empty($receipt_details->payments))
			<table>	
					<tr><td class="text-left with-custum">@lang('lang_v1.sub_total')</td><td>{{$receipt_details->subtotal}}</td></tr>
					<tr><td class="text-left with-custum">@lang('lang_v1.discount')</td><td>{{$receipt_details->discount}}</td></tr>
					<tr><td class="text-left with-custum">@lang('lang_v1.sub_total_discount')</td><td>${{$receipt_details->subtotal_unformatted}}</td></tr>
					<tr><td class="text-left with-custum">@lang('lang_v1.iva')</td><td>{{$receipt_details->tax}}</td></tr>
					<tr><td class="text-left with-custum">@lang('lang_v1.total_taxes')</td><td>{{$receipt_details->tax}}</td></tr>
					<tr><td class="text-left with-custum">@lang('lang_v1.shipping')</td><td>{{$receipt_details->shipping_charges}}</td></tr>
				 	<tr><td class="text-left with-custum">@lang('lang_v1.total')</td><td>{{$receipt_details->total}}</td></tr>
			</table>
		@endif
		</div>
</div>
@if(!empty($receipt_details->footer_text))
	<div class="col-md-6 invoice-col width-33">
		<div class="col-xs-12">
			@if(!empty($receipt_details->payments))
				<table>	
					@foreach($receipt_details->payments as $payment)
					<tr></td>{{$payment['method']}}</td></tr>
					<tr><td>{{$payment['amount']}}</td></tr>
					<tr><td>{{$payment['amount']}}</td>	</tr>
				    @endforeach
				</table>
			@endif
		</div>
	</div>
@endif

			</td>
		</tr>
	</tbody>
</table>