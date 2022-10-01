<br>
<div class="row">
  <!-- col-1 -->
  <div class="col-sm-4 invoice-col">

    <b>@lang('cashier.code'):</b>
    {{ $product->sku }}<br>

    <b>@lang('product.brand'): </b>
    {{ $product->brand->name or '--' }}<br>

    <b>@lang('product.clasification'): </b>
    @switch($product->clasification)
      @case('product')
      @lang('product.clasification_product')
      @break
      @case('kits')
      @lang('product.clasification_kits')
      @break
      @case('service')
      @lang('product.clasification_service')
      @break
    @endswitch
    <br>

    <b>@lang('product.status'): </b>
    @if ($product->status == 'active')
      @lang('product.status_active')<br>
    @else
      @lang('product.status_inactive')<br>
    @endif

    <b>@lang('product.unit'): </b>
    {{ $product->unit->actual_name or '--' }}<br>

    @if (!empty($product->product_custom_field1))
      <b>@lang('lang_v1.product_custom_field1'): </b>
      {{ $product->product_custom_field1 }} <br>
    @endif

    @if (!empty($product->product_custom_field2))
      <b>@lang('lang_v1.product_custom_field2'): </b>
      {{ $product->product_custom_field2 }} <br>
    @endif

    @if (!empty($product->product_custom_field3))
      <b>@lang('lang_v1.product_custom_field3'): </b>
      {{ $product->product_custom_field3 }} <br>
    @endif

    @if (!empty($product->product_custom_field4))
      <b>@lang('lang_v1.product_custom_field4'): </b>
      {{ $product->product_custom_field4 }} <br>
    @endif
  </div>
  <!-- /.col-1 -->

  <!-- col-2 -->
  <div class="col-sm-4 invoice-col">
    <b>@lang('product.barcode_type'): </b>
    {{ $product->barcode_type or '--' }} <br>

    <b>@lang('product.category'): </b>
    {{ $product->category->name or '--' }}<br>

    <b>@lang('product.sub_category'): </b>
    {{ $product->sub_category->name or '--' }}<br>

    <b>@lang('product.manage_stock'): </b>
    @if ($product->enable_stock)
      @lang('messages.yes')
    @else
      @lang('messages.no')
    @endif
    <br>

    @if ($product->enable_stock)
      <b>@lang('product.alert_quantity'): </b>
      {{ $product->alert_quantity or '--' }} <br>
    @endif
  </div>
  <!-- /.col-2 -->

  <!-- col-3 -->
  <div class="col-sm-4 invoice-col">
    <b>@lang('product.expires_in'): </b>
    @php
    $expiry_array = ['months'=>__('product.months'), 'days'=>__('product.days'), '' =>__('product.not_applicable')];
    @endphp
    @if (!empty($product->expiry_period) && !empty($product->expiry_period_type))
      {{ $product->expiry_period }} {{ $expiry_array[$product->expiry_period_type] }}
    @else
      {{ $expiry_array[''] }}
    @endif
    <br>

    @if ($product->weight)
      <b>@lang('lang_v1.weight'): </b>
      {{ $product->weight }} <br>
    @endif

    <b>@lang('product.applicable_tax'): </b>
    {{ $product->product_tax->name or __('lang_v1.none') }}
    @php
    $tax_type = ['inclusive' => __('product.inclusive'), 'exclusive' => __('product.exclusive')];
    @endphp
    <br>

    <b>@lang('product.selling_price_tax_type'): </b>
    {{ $tax_type[$product->tax_type] }} <br>

    <!--
        <b>@lang('product.dai'): </b>
        -->
    {{--
    {{ $product->dai or '--' }}
    --}}

    @php
    $product_type = ['single' => 'Single', 'variable' => 'Variable'];
    @endphp
    <b>@lang('product.product_type'): </b>
    {{ $product_type[$product->type] }}
    <br>
  </div>
  <!-- /.col-3 -->
</div>

<br>
<div class="row">
  <div class="col-md-4">
    @if ($product->product_description)
      <h4>@lang('lang_v1.description')</h4>
      {!! $product->product_description !!}
    @endif
  </div>

  @if ($rack_details->count())
    @if (session('business.enable_racks') || session('business.enable_row') || session('business.enable_position'))
      <!-- rack -->
      <div class="col-md-8">
        <h4>@lang('product.location_details')</h4>
        <div class="table-responsive">
          <table class="table table-condensed bg-gray text-center">
            <tr class="bg-green">
              <th>@lang('business.location')</th>
                <th>@lang('lang_v1.rack')</th>
                <th>@lang('lang_v1.row')</th>
                <th>@lang('lang_v1.position')</th>
            </tr>
            @foreach ($rack_details as $rd)
              <tr>
                <td>{{ $rd->name }}</td>
                  <td>@if (session('business.enable_racks')){{ $rd->rack }}@endif</td>
                  <td>@if (session('business.enable_row')){{ $rd->row }}@endif</td>
                  <td>@if (session('business.enable_position')){{ $rd->position }}@endif</td>   
              </tr>
            @endforeach
          </table>
        </div>
      </div>
    @endif
  @endif
</div>
