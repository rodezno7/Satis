<div class="row">
  <div class="col-xs-12">
    <h2 class="page-header">
      <b>@lang('lang_v1.stock_transfers')</b> (<b>@lang('accounting.correlative'):</b> #{{ $sell_transfer->ref_no }})
      <small class="pull-right"><b>@lang('messages.date'):</b> {{ @format_date($sell_transfer->transaction_date) }}</small>
    </h2>
  </div>
</div>

<div class="row invoice-info">
  <div class="col-sm-4 invoice-col">
    @lang('lang_v1.location_from'):
    <address>
      <strong>{{ $location_details['sell']->name }}</strong>
      
      @if (!empty($location_details['sell']->landmark))
        <br>{{ $location_details['sell']->landmark }}
      @endif

      @if (!empty($location_details['sell']->city) || !empty($location_details['sell']->state) || !empty($location_details['sell']->country))
        <br>{{ implode(',', array_filter([$location_details['sell']->city, $location_details['sell']->state, $location_details['sell']->country])) }}
      @endif

      @if (!empty($sell_transfer->contact->tax_number))
        <br>@lang('contact.tax_no'): {{ $sell_transfer->contact->tax_number }}
      @endif

      @if (!empty($location_details['sell']->mobile))
        <br>@lang('contact.mobile'): {{ $location_details['sell']->mobile }}
      @endif

      @if (!empty($location_details['sell']->email))
        <br>Email: {{ $location_details['sell']->email }}
      @endif
    </address>
  </div>

  <div class="col-md-4 invoice-col">
    @lang('lang_v1.location_to'):
    <address>
      <strong>{{ $location_details['purchase']->name }}</strong>
      
      @if (!empty($location_details['purchase']->landmark))
        <br>{{ $location_details['purchase']->landmark }}
      @endif

      @if (!empty($location_details['purchase']->city) || !empty($location_details['purchase']->state) || !empty($location_details['purchase']->country))
        <br>{{ implode(',', array_filter([$location_details['purchase']->city, $location_details['purchase']->state, $location_details['purchase']->country])) }}
      @endif

      @if (!empty($sell_transfer->contact->tax_number))
        <br>@lang('contact.tax_no'): {{ $sell_transfer->contact->tax_number }}
      @endif

      @if (!empty($location_details['purchase']->mobile))
        <br>@lang('contact.mobile'): {{ $location_details['purchase']->mobile }}
      @endif

      @if (!empty($location_details['purchase']->email))
        <br>Email: {{ $location_details['purchase']->email }}
      @endif
    </address>
  </div>

  <div class="col-sm-4 invoice-col">
    <b>@lang('purchase.ref_no'):</b> #{{ $sell_transfer->ref_no }}<br/>
    <b>@lang('messages.date'):</b> {{ @format_date($sell_transfer->transaction_date) }}<br/>
  </div>
</div>

<br>

<div class="row">
  <div class="col-xs-12">
    <div class="table-responsive">
      <table class="table bg-gray">
        <tr class="bg-green">
          <th class="text-center">#</th>
          <th class="text-center">SKU</th>
          <th>@lang('sale.product')</th>
          <th class="text-center">@lang('sale.qty')</th>
          <th class="text-center" @if ($show_costs_or_prices == 'none') style="display: none;" @endif>@lang('sale.unit_price')</th>
          <th class="text-center" @if ($show_costs_or_prices == 'none') style="display: none;" @endif>@lang('sale.subtotal')</th>
        </tr>
        @php 
          $total = 0.00;
        @endphp
        @foreach($sell_transfer->sell_lines as $sell_lines)
          <tr>
            <td class="text-center">{{ $loop->iteration }}</td>

            <td>{{ $sell_lines->variations->sub_sku }}</td>

            <td>
              {{ $sell_lines->product->name }}
              @if ($sell_lines->product->type == 'variable')
                - {{ $sell_lines->variations->product_variation->name }}
                - {{ $sell_lines->variations->name }}
              @endif

              @if ($lot_n_exp_enabled && !empty($sell_lines->lot_details))
                <br>
                <strong>@lang('lang_v1.lot_n_expiry'):</strong> 
                @if (!empty($sell_lines->lot_details->lot_number))
                  {{ $sell_lines->lot_details->lot_number }}
                @endif
                @if (!empty($sell_lines->lot_details->exp_date))
                  - {{ @format_date($sell_lines->lot_details->exp_date) }}
                @endif
               @endif
            </td>

            <td class="text-right">
              {{ $sell_lines->quantity }}
            </td>

            @php
							$price = $show_costs_or_prices == 'prices' && ! is_null($sell_lines->sale_price) ? $sell_lines->sale_price : $sell_lines->unit_price;
						@endphp

            <td class="text-right" @if ($show_costs_or_prices == 'none') style="display: none;" @endif>
              <span class="display_currency" data-currency_symbol="true" data-precision="{{ $decimals_in_inventories }}">
                {{ $price }}
              </span>
            </td>

            <td class="text-right" @if ($show_costs_or_prices == 'none') style="display: none;" @endif>
              <span class="display_currency" data-currency_symbol="true" data-precision="{{ $decimals_in_inventories }}">
                {{ $price * $sell_lines->quantity }}
              </span>
            </td>
          </tr>

          @php 
            $total += $price * $sell_lines->quantity;
          @endphp
        @endforeach
      </table>
    </div>
  </div>
</div>

<br>

<div class="row" @if ($show_costs_or_prices == 'none') style="display: none;" @endif>
  <div class="col-xs-12">
    <div class="table-responsive">
      <table class="table">
        <tr>
          <th>
            @lang('purchase.net_total_amount'):
          </th>
          <td></td>
          <td width="15%">
            <span class="display_currency pull-right" data-currency_symbol="true">
              {{ $total }}
            </span>
          </td>
        </tr>

        @php
          $final_total = $total;
        @endphp

        @if (!empty($sell_transfer->shipping_charges))
          @php
            $final_total += $sell_transfer->shipping_charges;
          @endphp
          <tr>
            <th>
              @lang('purchase.additional_shipping_charges'):
            </th>
            <td class="text-right">
              <b>(+)</b>
            </td>
            <td>
              <span class="display_currency pull-right" data-currency_symbol="true">
                {{ $sell_transfer->shipping_charges }}
              </span>
            </td>
          </tr>
        @endif

        <tr>
          <th>
            @lang('purchase.purchase_total'):
          </th>
          <td></td>
          <td>
            <span class="display_currency pull-right" data-currency_symbol="true" >
              {{ $final_total }}
            </span>
          </td>
        </tr>
      </table>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-sm-12">
    <strong>
      @lang('purchase.additional_notes'):
    </strong>
    <br>
    <p class="well well-sm no-shadow bg-gray">
      @if($sell_transfer->additional_notes)
        {{ $sell_transfer->additional_notes }}
      @else
        --
      @endif
    </p>
  </div>
</div>

<div class="row">
  <div class="col-sm-12">
      <table style="margin: 15px 0 15px 0;" width="100%">
        <tr>
          <td width="5px;">F.</td>
          <td style="border-bottom: 1px solid black;" width="40%"></td>
          <td></td>
          <td width="5px;">F.</td>
          <td style="border-bottom: 1px solid black;" width="40%"></td>
        </tr>
        <tr>
          <td></td>
          <td class="text-center">
            {{ config('app.business') == 'optics' ? __('accounting.reviewed_by') : __('lang_v1.received') }}
          </td>
          <td></td>
          <td></td>
          <td class="text-center">
            {{ config('app.business') == 'optics' ? __('lang_v1.received_by') : __('lang_v1.delivered') }}
          </td>
        </tr>
      </table>
  </div>
</div>

{{-- Barcode --}}
<div class="row print_section">
  <div class="col-xs-12">
    <img
      class="center-block"
      src="data:image/png;base64,{{ DNS1D::getBarcodePNG($sell_transfer->ref_no, 'C128', 2,30,array(39, 48, 54), true) }}">
  </div>
</div>