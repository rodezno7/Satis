<div class="modal-header">
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="modalTitle"> @lang('Detalles de') @lang('movement_type.'. $kardex->name .'') 
      (<b>@lang('Número de referencia'):</b> #{{ !empty($purchase->ref_no) ? $purchase->ref_no : $kardex->reference }})
    </h4>
</div>
<div class="modal-body">
  <br>
  <div class="row">
    <div class="col-sm-12 col-xs-12">
      <div class="table-responsive">
        <table class="table bg-gray">
          <thead>
            <tr class="bg-green">
              <th>#</th>
              <th>@lang( 'product.product_name' )</th>
              <th>@lang( 'Cantidad' )</th>
              <th>@lang('product.unit_cost')</th>
              <th>@lang('product.total_amount')</th>
            </tr>
          </thead>
          @php 
            $total_before_tax = 0.0000;
          @endphp
          @foreach($purchase_lines as $purchase_line)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>
                {{ $purchase_line->product->name }}
                 @if( $purchase_line->product->type == 'variable')
                  - {{ $purchase_line->variations->product_variation->name}}
                  - {{ $purchase_line->variations->name}}
                 @endif
              </td>
              <td><span class="display_currency" data-currency_symbol="false">{{ $purchase_line->quantity }}</span></td>

              <td><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->purchase_price }}</span></td>
              <td><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->purchase_price * $purchase_line->quantity }}</span></td>
            </tr>
            @php 
              $total_before_tax += ($purchase_line->quantity * $purchase_line->purchase_price);
            @endphp
          @endforeach
        </table>
      </div>
    </div>
  </div>
  <br>
  <div class="col-md-6 col-sm-12 col-xs-12">
    <div class="table-responsive">
      <table class="table">
        <tr>
          <th>@lang('purchase.net_total_amount'): </th>
          <td></td>
          <td><span class="display_currency pull-right" data-currency_symbol="true">{{ $total_before_tax }}</span></td>
        </tr>
        <tr>
          <th>@lang('purchase.discount'):</th>
          <td>
            <b>(-)</b>
            @if($purchase->discount_type == 'percentage')
              ({{$purchase->discount_amount}} %)
            @endif
          </td>
          <td>
            @php
                $total_amount_discount = 0;
                if($purchase->discount_type == 'percentage'){
                  $total_amount_discount = $purchase->discount_amount * $total_before_tax / 100;
                }else{
                  $total_amount_discount = $purchase->discount_amount;
                }
            @endphp
            <span class="display_currency pull-right" data-currency_symbol="true">
              {{ $total_amount_discount }}
            </span>
          </td>
        </tr>
        <tr>
          <th>@lang('Impuesto'):</th>
          <td><b>(+)</b></td>
          <td class="text-right">
              <strong><small>{{ $name_tax_purchase }}</small>
              </strong>&nbsp;<span class="display_currency pull-right" data-currency_symbol="true">
                {{ $percent > 0 ? (($total_before_tax - $total_amount_discount) * $percent) :  0.0000 }}
              </span><br>
            </td>
        </tr>
        <tr>
          <th>@lang('Total'):</th>
          <td></td>
          <td><span class="display_currency pull-right" data-currency_symbol="true" >
            {{ $total_before_tax +  (($total_before_tax - $total_amount_discount) * $percent)}}
          </span></td>
        </tr>
      </table>
    </div>
  </div>
  {{-- Barcode --}}
  <div class="row print_section">
    <div class="col-xs-12">
      <img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($purchase->ref_no, 'C128', 2,30,array(39, 48, 54), true)}}">
    </div>
  </div>
</div>