<div id="preview_body">
@php
	$loop_count = 0;
@endphp
@foreach($product_details as $details)
	@while($details['qty'] > 0)
		@php
			$loop_count += 1;
			$is_new_row = (!$barcode_details->is_continuous) && (($loop_count == 1) || ($loop_count % $barcode_details->stickers_in_one_row) == 1) ? true : false;
			
			$stickers_in_one_sheet = ! empty($print['business_logo']) || ! empty($print['brand_logo']) ? $barcode_details->stickers_in_one_sheet_with_logo : $barcode_details->stickers_in_one_sheet;
			$height = ! empty($print['business_logo']) || ! empty($print['brand_logo']) ? $barcode_details->height_with_logo : $barcode_details->height;
		@endphp

		@if(($barcode_details->is_continuous && $loop_count == 1) || (!$barcode_details->is_continuous && ($loop_count % $stickers_in_one_sheet) == 1))
			{{-- Actual Paper --}}
			<div style="@if(!$barcode_details->is_continuous) height:{{$barcode_details->paper_height}}in !important; @else height:100% !important; @endif width:{{$barcode_details->paper_width}}in !important; line-height: 16px !important;" class="label-border-outer">

			{{-- Paper Internal --}}
			<div style="margin-top:{{$barcode_details->top_margin}}in !important; margin-bottom:{{$barcode_details->top_margin}}in !important; margin-left:{{$barcode_details->left_margin}}in !important;margin-right:{{$barcode_details->left_margin}}in !important;" class="label-border-internal">
		@endif

		@if((!$barcode_details->is_continuous) && ($loop_count % $stickers_in_one_sheet) <= $barcode_details->stickers_in_one_row)
			@php $first_row = true @endphp
		@elseif($barcode_details->is_continuous && ($loop_count <= $barcode_details->stickers_in_one_row) )
			@php $first_row = true @endphp
		@else
			@php $first_row = false @endphp
		@endif

		<div
			style="
				height: {{ $height }}in !important;
				line-height: {{ $height }}in;
				width: {{ $barcode_details->width * 0.97 }}in !important;
				display: inline-block;
				@if (! $is_new_row)
					margin-left:{{ $barcode_details->col_distance }}in !important;
				@endif
				@if (! $first_row)
					margin-top: {{ $barcode_details->row_distance }}in !important;
				@endif
			"
			class="sticker-border text-center">
			<div style="display: inline-block; vertical-align: middle; line-height: 16px !important; width: 95%;">
				<div style="padding: 0 0 2px 0;">
					{{-- Business logo --}}
					@if (! empty($print['business_logo']))
						@if (! empty($business_logo) && file_exists(public_path('uploads/business_logos/' . $business_logo)))
							<div style="display: inline-block; width: 45%; max-height: 0.5in;">
								<img src="{{ url('/uploads/business_logos/' . $business_logo) }}" style="max-height: 0.5in; max-width: 100%; display: inline; padding: 2px 0;">
							</div>
						@endif
					@endif

					{{-- Brand logo --}}
					@if (! empty($print['brand_logo']) && ! empty($details['details']->brand_logo))
						@if (file_exists(public_path('uploads/img/' . $details['details']->brand_logo)))
							<div style="display: inline-block; width: 45%; max-height: 0.5in;">
								<img src="{{ url('/uploads/img/' . $details['details']->brand_logo) }}" style="max-height: 0.5in; max-width: 100%; display: inline; padding: 2px 0;">
							</div>
						@endif
					@endif
				</div>

				{{-- Business Name --}}
				@if(!empty($print['business_name']))
					<b style="display: block !important" class="text-uppercase">{{$business_name}}</b>
				@endif

				{{-- Product Name --}}
				@if(!empty($print['name']))
					<div id="spn-product-name" class="line-clamp {{ $name_lines }}">
						{{$details['details']->product_actual_name}}
					</div>
				@endif

				{{-- Variation --}}
				@if(!empty($print['variations']) && $details['details']->is_dummy != 1)
					<span style="display: block !important">
						<b>{{$details['details']->product_variation_name}}</b>:{{$details['details']->variation_name}}
					</span>
					
				@endif

				{{-- Price --}}
				@if(!empty($print['price']))
					<b>@lang('product.price'):</b>
					<span class="display_currency" data-currency_symbol = true>
						@if($print['price_type'] == 'inclusive')
							{{$details['details']->sell_price_inc_tax}}
						@else
							{{$details['details']->default_sell_price}}
						@endif
					</span>
				@endif


				{{-- Barcode --}}
				<img
					class="center-block"
					style="max-width: 90% !important; max-height: {{ $barcode_details->height / 4 }}in !important; opacity: 0.9"
					src="data: image/png; base64, {{ DNS1D::getBarcodePNG($details['details']->sub_sku, $details['details']->barcode_type, 2,30, array(39, 48, 54), true) }}">

			</div>
		</div>

		@if(!$barcode_details->is_continuous && ($loop_count % $stickers_in_one_sheet) == 0)
			{{-- Actual Paper --}}
			</div>

			{{-- Paper Internal --}}
			</div>
		@endif

		@php
			$details['qty'] = $details['qty'] - 1;
		@endphp
	@endwhile
@endforeach

@if($barcode_details->is_continuous || ($loop_count % $stickers_in_one_sheet) != 0)
	{{-- Actual Paper --}}
	</div>

	{{-- Paper Internal --}}
	</div>
@endif

</div>

<style type="text/css">

	@media print{
		#preview_body{
			display: block !important;
		}
	}
	@page {
		size: {{$barcode_details->paper_width}}in @if($barcode_details->paper_height != 0){{$barcode_details->paper_height}}in @endif;

		/*width: {{$barcode_details->paper_width}}in !important;*/
		/*height:@if($barcode_details->paper_height != 0){{$barcode_details->paper_height}}in !important @else auto @endif;*/
		margin-top: 0in;
		margin-bottom: 0in;
		margin-left: 0in;
		margin-right: 0in;
		
		@if($barcode_details->is_continuous)
			page-break-inside : avoid !important;
		@endif
	}
</style>