{{-- Header --}}
<div class="row">
  <div class="col-xs-12">
    <h2 class="page-header">
      @lang('stock_adjustment.stock_adjustment') - #{{ $stock_adjustment->ref_no }}
      <small class="pull-right"><b>@lang('messages.date'):</b> {{ @format_date($stock_adjustment->transaction_date) }}</small>
    </h2>
  </div>
</div>

{{-- Info --}}
<div class="row invoice-info">
  <div class="col-sm-6 invoice-col" style="width: 50%;">
    <strong>@lang('business.location'):</strong> {{ $stock_adjustment->warehouse->name }}
    <address>
      {{ $stock_adjustment->location->name }}
      
      @if (! empty($stock_adjustment->location->landmark))
        <br>{{ $stock_adjustment->location->landmark }}
      @endif

      @if (! empty($stock_adjustment->location->city) || ! empty($stock_adjustment->location->state) || ! empty($stock_adjustment->location->country))
        <br>{{ implode(', ', array_filter([$stock_adjustment->location->city, $stock_adjustment->location->state, $stock_adjustment->location->country])) }}
      @endif

      @if (! empty($stock_adjustment->contact->tax_number))
        <br><strong>@lang('contact.tax_no'):</strong> {{ $stock_adjustment->contact->tax_number }}
      @endif

      @if (! empty($stock_adjustment->location->mobile))
        <br><strong>@lang('contact.mobile'):</strong> {{ $stock_adjustment->location->mobile }}
      @endif

      @if (! empty($stock_adjustment->location->email))
        <br><strong>Email:</strong> {{ $stock_adjustment->location->email }}
      @endif
    </address>
  </div>

  <div class="col-sm-6 invoice-col" style="width: 50%;">
    <b>@lang('purchase.ref_no'):</b> {{ $stock_adjustment->ref_no }}<br/>

    <b>@lang('messages.date'):</b> {{ @format_date($stock_adjustment->transaction_date) }}<br/>

    <b>@lang('stock_adjustment.adjustment_type'):</b> {{ __('stock_adjustment.' . $stock_adjustment->adjustment_type) }}<br/>

    <b>@lang('stock_adjustment.created_by'):</b> {{ $stock_adjustment->sales_person->first_name . ' ' . $stock_adjustment->sales_person->last_name }}<br/>
  </div>
</div>

<br>

{{-- Table --}}
<div class="row">
  <div class="col-xs-12">
    <div class="table-responsive">
      <table class="table bg-gray">
        <tr class="bg-green">
          <th>#</th>
          <th>@lang('sale.product')</th>
          <th>@lang('sale.qty')</th>
          <th @if ($show_costs_or_prices == 'none') style="display: none;" @endif>@lang('sale.unit_price')</th>
          <th @if ($show_costs_or_prices == 'none') style="display: none;" @endif>@lang('sale.subtotal')</th>
        </tr>
        @php 
          $total = 0.00;
        @endphp
        @foreach($lines as $line)
          @php
            if ($stock_adjustment->adjustment_type == 'normal') {
              $unit_price = $show_costs_or_prices == 'prices' && ! is_null($line->sale_price) ? $line->sale_price : $line->purchase_price;
            } else {
              $unit_price = $show_costs_or_prices == 'prices' && ! is_null($line->sale_price) ? $line->sale_price : $line->unit_price;
            }
          @endphp
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
              {{ $line->product->name }}
                @if( $line->product->type == 'variable')
                - {{ $line->variations->product_variation->name }}
                - {{ $line->variations->name }}
                @endif
            </td>
            <td>{{ $line->quantity }}</td>
            <td>
              <span class="display_currency" data-currency_symbol="true" data-precision="{{ $decimals_in_inventories }}">{{ $unit_price }}</span>
            </td>
            <td>
              <span class="display_currency" data-currency_symbol="true" data-precision="{{ $decimals_in_inventories }}">{{ $unit_price * $line->quantity }}</span>
            </td>
          </tr>
          @php 
            $total += ($unit_price * $line->quantity);
          @endphp
        @endforeach
        <tr>
          <td colspan="3"></td>
          <th>@lang('accounting.total')</th>
          <td><span class="display_currency" data-currency_symbol="true">{{ $total }}</span></td>
        </tr>
      </table>
    </div>
  </div>
</div>

<br>

{{-- Notes --}}
<div class="row">
  <div class="col-sm-6">
    <strong>@lang('stock_adjustment.reason_for_stock_adjustment'):</strong><br>
    <p class="well well-sm no-shadow bg-gray">
      @if ($stock_adjustment->additional_notes)
        {{ $stock_adjustment->additional_notes }}
      @else
        --
      @endif
    </p>
  </div>
</div>

{{-- Barcode --}}
<div class="row print_section">
  <div class="col-xs-12">
    <img class="center-block" src="data:image/png;base64, {{ DNS1D::getBarcodePNG($stock_adjustment->ref_no, 'C128', 2, 30, array(39, 48, 54), true) }}">
  </div>
</div>