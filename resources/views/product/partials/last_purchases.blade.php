<br>
<div class="row">
  <div class="col-md-12">
    <div class="table-responsive">
      <h4 style="margin-top: 0;">@lang('product.last_three_purchases')</h4>
      <table class="table bg-gray">
        <tr class="bg-green">
          <th>@lang('invoice.date')</th>
          <th>@lang('report.document_no')</th>
          <th>@lang('purchase.supplier')</th>
          <th>@lang('lang_v1.quantity')</th>
          <th>@lang('product.unit_cost')</th>
          <th>@lang('receipt.total')</th>
        </tr>
        @php
          $purchases = \App\Product::lastThreePurchases($product->id);
        @endphp

        @if ($purchases->count())
          @foreach ($purchases as $purchase)
            <tr>
              <td>
                {{ \Carbon\Carbon::parse($purchase->transaction_date)->format('d/m/Y') }}
              </td>

              <td>
                @if (! empty($purchase->ref_no))  
                  {{ $purchase->ref_no }}
                @else
                  -
                @endif
              </td>

              <td>
                @if (! empty($purchase->name))  
                  {{ $purchase->name }}
                @else
                  -
                @endif
              </td>

              <td>
                {{ round($purchase->quantity, 2) }}
              </td>

              @php
                $purchase_price = $purchase->purchase_type == 'international' ? $purchase->purchase_price_inc_tax : $purchase->purchase_price;
              @endphp

              <td>
                <span class="display_currency" data-currency_symbol="true" data-precision="6">
                  {{ $purchase_price }}
                </span>
              </td>

              <td>
                <span class="display_currency" data-currency_symbol="true">
                  {{ $purchase->quantity * $purchase_price }}
                </span>
              </td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="5" class="text-center">@lang('lang_v1.no_records')</td>
          </tr>
        @endif
      </table>
    </div>
  </div>
</div>
