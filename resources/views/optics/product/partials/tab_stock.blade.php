<br>
<div class="row">
  <div class="col-md-12">
    <h4 style="margin-top: 0;">@lang('product.stock_per_warehouse')</h4>
    <div class="table-responsive">
      <table class="table bg-gray text-center">
        <tr class="bg-green">
          <th>@lang('cashier.business_location')</th>
          <th>@lang('warehouse.warehouse')</th>
          <th>@lang('product.stock2')</th>
          <th>@lang('reservation.reserved')</th>
          <th>@lang('order.available')</th>
        </tr>
        @foreach ($product->variations as $variation)
          @if ($variation->variation_location_details->count())
            @foreach ($variation->variation_location_details as $vld)
              <tr>
              @if ($vld->location)
                <td>{{ $vld->location->name }}</td>
              @else
                <td>--</td>
              @endif
              
              @if ($vld->warehouse)
                <td>{{ $vld->warehouse->name }}</td>
              @else
                <td>--</td>
              @endif

              @if ($vld->qty_available > 0)
                @if ($vld->qty_available <= $product->alert_quantity)
                <td style="color: #dd4b39; font-weight: bold;">
                @else
                <td>
                @endif
                  {{ round($vld->qty_available, 2) }}
                  @if ($vld->qty_available <= $product->alert_quantity)
                  <i class="fa fa-warning" data-toggle="tooltip" data-placement="top"
                    data-html="true" data-original-title="@lang('product.critical_stock', ['number' => $product->alert_quantity])" aria-hidden="true"></i>
                  @endif
                </td>
              @else
                <td style="color: #dd4b39; font-weight: bold;">
                  0
                  <i class="fa fa-warning" data-toggle="tooltip" data-placement="top"
                      data-html="true" data-original-title="@lang('product.critical_stock', ['number' => $product->alert_quantity])" aria-hidden="true"></i>
                </td>
              @endif
              
              <td>
                @if ($vld->qty_reserved > 0)
                  {{ round($vld->qty_reserved, 2) }}
                @else
                  0
                @endif
              </td>

              <td>
                @if (($vld->qty_available - $vld->qty_reserved) >= 0)
                  @if (($vld->qty_available - $vld->qty_reserved) <= $vld->qty_available)
                    {{ round($vld->qty_available - $vld->qty_reserved, 2) }}
                  @else
                    {{ round($vld->qty_available, 2) }}
                  @endif
                @else
                  0
                @endif
              </td>
            </tr>
            @endforeach
          @else
            <tr>
              <td colspan="5" class="text-center">@lang('lang_v1.no_records')</td>
            </tr>
          @endif
        @endforeach
      </table>
    </div>
  </div>

  <!-- critical stock -->
  {{--
  @php
    $stock_ = \App\Product::getStock($product->id)['stock']
  @endphp
  <div class="col-md-4">
    @if ($stock_ <= $product->alert_quantity)
    <div class="small-box bg-red">
    @else
    <div class="small-box bg-green">
    @endif
      <div class="inner">
        <h3>{{ $stock_ }}</h3>
        <p style="color: #fff">@lang('product.critical_stock', ['number' => $product->alert_quantity])</p>
      </div>
      <div class="icon">
        <i class="fa fa-area-chart"></i>
      </div>
    </div>
  </div>
  --}}
</div>
