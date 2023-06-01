@extends('layouts.app')
@section('title', __('product.edit_product'))
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang('product.edit_product')</h1>
  <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
      </ol> -->
</section>

<!-- Main content -->
<section class="content">
  {!! Form::open(['url' => action('ProductController@update' , [$product->id] ), 'method' => 'PUT', 'id' =>
  'product_edit_form',
  'class' => 'product_form', 'files' => true ]) !!}
  <input type="hidden" id="product_id" value="{{ $product->id }}">
  <input type="hidden" id="product_clasification" value="{{ $product->clasification }}">

  {{-- Number of decimal places to store and use in calculations --}}
  <input type="hidden" id="price_precision" value="{{ config('app.price_precision') }}">

  <div class="boxform_u box-solid_u">
    <div class="box-body">
      <div class="row">
        <div class="col-lg-4 col-sm-6">
          <div class="form-group">
            {!! Form::label('clasification', __('product.clasification') . ':') !!}

            {!! Form::select('clasification', ['product' => 'Producto', 'kits' => 'Kits', 'service' => 'Servicio'],
            $product->clasification, ['class' => 'form-control select2']); !!}
          </div>
        </div>
        <div class="col-lg-4 col-sm-6">
          <div class="form-group">
            {!! Form::label('name', __('product.product_name') . ':*') !!}
            {!! Form::text('name', $product->name, ['class' => 'form-control', 'required',
            'placeholder' => __('product.product_name')]); !!}
          </div>
        </div>
        <div class=" form-group col-lg-4 col-sm-6">
          <label>
            {!! Form::checkbox('is_active', 1, $is_active, ['class' => 'input-icheck', 'id' => 'is_active']); !!}
            <strong>@lang('product.is_active')</strong>
          </label>@show_tooltip(__('product.is_active_help')) <small class="help-block">
            <i>@lang('product.is_active_help')</i></small>
        </div>
        <div class="col-lg-4 col-sm-6 @if(!session('business.enable_category')) hide @endif">
          <div class="form-group">
            {!! Form::label('category_id', __('product.category') . ':') !!}
            {!! Form::select('category_id', $categories, $product->category_id, ['placeholder' =>
            __('messages.please_select'), 'class' => 'form-control select2']); !!}
          </div>
        </div>
        <div
          class="col-lg-4 col-sm-6 @if(!(session('business.enable_category') && session('business.enable_sub_category'))) hide @endif">
          <div class="form-group">
            {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
            {!! Form::select('sub_category_id', $sub_categories, $product->sub_category_id, ['placeholder' =>
            __('messages.please_select'), 'class' => 'form-control select2']); !!}
          </div>
        </div>
        <div
          class="col-lg-4 col-sm-6 @if(!(session('business.enable_category') && session('business.enable_sub_category'))) hide @endif">
          <div class="form-group">
            {!! Form::label('sku', __('product.sku') . ':*') !!} @show_tooltip(__('tooltip.sku'))
            {!! Form::text('sku', $product->sku, ['class' => 'form-control',
            'placeholder' => __('product.sku'), 'required', 'readonly']); !!}
          </div>
        </div>
        <div id="divExcludeService">
          <div class="col-lg-4 col-sm-6">
            <div class="form-group">
              {!! Form::label('barcode_type', __('product.barcode_type') . ':*') !!}
              {!! Form::select('barcode_type', $barcode_types, $product->barcode_type, ['placeholder' =>
              __('messages.please_select'), 'class' => 'form-control select2', 'required']); !!}
            </div>
          </div>

          <div id="div_brand" class="col-lg-4 col-sm-6 @if(!session('business.enable_brand')) hide @endif">
            <div class="form-group">
              {!! Form::label('brand_id', __('product.brand') . ':') !!}
              <div class="input-group">
                {!! Form::select('brand_id', $brands, $product->brand_id, ['placeholder' =>
                __('messages.please_select'), 'class' => 'form-control select2']); !!}
                <span class="input-group-btn">
                  <button type="button" @if(!auth()->user()->can('brand.create')) disabled @endif class="btn btn-default
                    bg-white btn-flat btn-modal" data-href="{{action('BrandController@create', ['quick_add' => true])}}"
                    title="@lang('brand.add_brand')" data-container=".view_modal"><i
                      class="fa fa-plus-circle text-primary fa-lg"></i></button>
                </span>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-sm-6">
            <div class="form-group">
              {!! Form::label('unit_id', __('product.unit') . ':*') !!}
              @if($conf_units == 1)
              {!! Form::select('unit_id', $units, $product->unit_group_id, ['placeholder' =>
              __('messages.please_select'), 'class' => 'form-control select2', 'required', 'id' => 'unit_id', 'style' =>
              'width: 100%;']); !!}
              @else
              <div class="input-group">
                {!! Form::select('unit_id', $units, $product->unit_id, ['placeholder' => __('messages.please_select'),
                'class' => 'form-control select2', 'required', 'id' => 'unit_id']); !!}
                <span class="input-group-btn">
                  <button type="button" @if(!auth()->user()->can('unit.create')) disabled @endif
                    class="btn btn-default bg-white btn-flat btn-modal quick_add_unit"
                    data-href="{{action('UnitController@create', ['quick_add' => true])}}"
                    title="@lang('unit.add_unit')"
                    data-container=".view_modal">
                    <i class="fa fa-plus-circle text-primary fa-lg"></i>
                  </button>
                </span>
              </div>
              @endif
            </div>
          </div>
        </div>
        <div id="divExcludeService2">

          <div class="form-group" style="display: none;">
            <label>
              {!! Form::checkbox('enable_stock', 1, $product->enable_stock, ['class' => 'input-icheck', 'id' =>
              'enable_stock']); !!} <strong>@lang('product.manage_stock')</strong>
            </label>@show_tooltip(__('tooltip.enable_stock')) <small class="help-block">
              <i>@lang('product.enable_stock_help')</i></small>
          </div>

          <div class="col-lg-4 col-sm-6">
            <div id="alert_quantity_div" class="form-group" @if(!$product->enable_stock) style="display:none" @endif>
              {!! Form::label('alert_quantity', __('product.alert_quantity') . ':*') !!}
              @show_tooltip(__('tooltip.alert_quantity'))
              {!! Form::number('alert_quantity', $product->alert_quantity, ['id' => 'alert_quantity', 'class' =>
              'form-control', 'required',
              'placeholder' => __('product.alert_quantity') , 'min' => '0']); !!}
            </div>
          </div>

          {{-- <div class="col-sm-3">
            <div class="form-group">
              <label from="dai">@lang('product.dai')</label>
              <input type="text" name="dai" value="{{$product->dai}}" id="dai" class="form-control"
                placeholder="@lang('product.dai_label')">
            </div>
          </div> --}}

          <div class="col-lg-4 col-sm-6">
            <div class="form-group">
              <label>
                {!! Form::checkbox('check_dai', 1, $product->check_dai, ['class' => 'input-icheck', 'id' =>
                'check_dai']); !!}
                <strong>@lang('product.dai')</strong>
              </label>
              @show_tooltip(__('tooltip.check_dai'))
              <small class="help-block"><i>@lang('product.check_dai_help')</i></small>
            </div>
          </div>

          {{-- <div class="col-sm-3">
            <div class="form-group">
              <label from="provider_code">@lang('product.provider_code')</label>
              <input type="text" name="provider_code" id="provider_code" value="{{ $product->provider_code }}"
                class="form-control" placeholder="@lang('product.provider_code')">
            </div>
          </div>
          <div class="col-sm-3">
            <div class="form-group">
              <label from="drive_unit ">@lang('product.drive_unit')</label>
              <input type="text" name="drive_unit" id="drive_unit" value="{{ $product->drive_unit }}"
                class="form-control input_number" placeholder="@lang('product.drive_unit')">
            </div>
          </div> --}}
        </div>
      </div>


      <div class="row">
        <div class="col-lg-8 col-sm-12">
          <div class="form-group">
            {!! Form::label('product_description', __('lang_v1.product_description') . ':') !!}
            {!! Form::textarea('product_description', $product->product_description, ['class' => 'form-control']); !!}
          </div>
          <div class="row form-group">
            <div class="col-lg-3 col-sm-3">
              <label for="has_warranty">
                {!! Form::checkbox('has_warranty', '1', $product->has_warranty,
                ['id' => 'has_warranty', 'onClick' => 'showWarranty()']) !!}
                @lang('quote.has_warranty')
              </label>
            </div>
            <div class="col-lg-4 col-sm-3" id="hasW">
              {!! Form::label(__('quote.warranty') . ':') !!}
              <input type="text" name="warranty" id="warranty" value="{{ $product->warranty }}" class="form-control"
                placeholder="@lang('quote.warranty')">
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-sm-12">
          <div class="form-group">
            {!! Form::label('image', __('lang_v1.product_image') . ':') !!}
            {!! Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); !!}
            <small class="help-block">@lang('purchase.max_file_size', ['size' =>
              (config('constants.document_size_limit')
              / 1000000)]). @lang('lang_v1.aspect_ratio_should_be_1_1') @if(!empty($product->image)) <br>
              @lang('lang_v1.previous_image_will_be_replaced') @endif
            </small>
          </div>
        </div>



      </div>
    </div>
  </div>
  {{-- <div id="divExcludeService3">
    <div class="boxform_u box-solid_u">
      <div class="box-body">
        <div class="row">
          <div class="col-sm-3">
            <div class="form-group">
              {!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-user"></i>
                </span>
                {!! Form::select('contact_id', [], null, ['class' => 'form-control', 'placeholder' =>
                __('messages.please_select'), 'id' => 'supplier_id']); !!}
                <span class="input-group-btn">
                  <button type="button" class="btn btn-default bg-white btn-flat add_new_supplier" data-name=""><i
                      class="fa fa-plus-circle text-primary fa-lg"></i></button>
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-9">
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <table id="suppliersTable" class="table table-responsive" width="100%">
            <thead>
              <tr>
                <th style="width: 10%">@lang('messages.actions')</th>
                <th>@lang('business.business_name')</th>
                <th>@lang('contact.name')</th>
                <th>@lang('contact.contact')</th>
                <th>@lang('contact.catalogue')</th>
                <th>@lang('contact.uxc')</th>
                <th>@lang('lang_v1.weight')</th>
                <th>@lang('contact.dimensions')</th>
                <th>@lang('contact.custom')</th>
                <th>@lang('contact.last_purchase_date')</th>
                <th>@lang('lang_v1.quantity')</th>
                <th>@lang('purchase.unit_price')</th>
                <th>@lang('purchase.purchase_total')</th>
              </tr>
            </thead>
            <tbody id="lista">
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div> --}}
  <div id="divExcludeService4" @if(!auth()->user()->can('product.view_cost')) class='hide' @endif style="display:
    none;">
    <div class="boxform_u box-solid_u">
      <div class="box-body">
        <div class="row">
          <div class="col-lg-4 col-sm-6">
            <div class="form-group">
              {!! Form::label('kit_children', __('product.clasification_product') . ':*') !!}
              <select name="kit_children" id="kit_children" class="form-control select2" style="width: 100%;">
                <option value="0">@lang('messages.please_select')</option>
                @foreach($products as $prod)
                @if($prod->sku != $prod->sub_sku)
                <option value="{{ $prod->id }}">{{ $prod->name_product }} {{ $prod->name_variation }}</option>
                @else
                <option value="{{ $prod->id }}">{{ $prod->name_product }}</option>
                @endif
                @endforeach
              </select>
            </div>
          </div>
          {{-- <div class="col-sm-9">
          </div> --}}
        </div>

        <div class="row">
          <div class="col-sm-12">
            <table id="kitTable" class="table table-responsive" width="100%">
              <thead>
                <tr>
                  <th style="width: 15%;">@lang('sale.product')</th>
                  <th style="width: 15%;">@lang('product.sku')</th>
                  <th style="width: 15%;">@lang('product.brand')</th>
                  <th style="width: 15%;">@lang('product.unit')</th>
                  <th style="width: 15%;">@lang('product.price')</th>
                  <th style="width: 15%;">@lang('product.product_quantity')</th>
                  <th style="width: 10%;">@lang('messages.actions')</th>
                </tr>
              </thead>
              <tbody id="listak">
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="boxform_u box-solid_u" id="div_imei">
    <div class="box-body">
      <div class="row">
        @if(session('business.enable_product_expiry'))
        @if(session('business.expiry_type') == 'add_expiry')
        @php
        $expiry_period = 12;
        $hide = true;
        @endphp
        @else
        @php
        $expiry_period = null;
        $hide = false;
        @endphp
        @endif
        <div class="col-lg-6 col-sm-7 @if($hide) hide @endif">
          <div class="form-group">
            <div class="multi-input">
              @php
              $disabled = false;
              $disabled_period = false;
              if( empty($product->expiry_period_type) || empty($product->enable_stock) ){
              $disabled = true;
              }
              if( empty($product->enable_stock) ){
              $disabled_period = true;
              }
              @endphp
              {!! Form::label('expiry_period', __('product.expires_in') . ':') !!}<br>
              {!! Form::text('expiry_period', @num_format($product->expiry_period), ['class' => 'form-control pull-left
              input_number',
              'placeholder' => __('product.expiry_period'), 'style' => 'width:60%;', 'disabled' => $disabled]); !!}
              {!! Form::select('expiry_period_type', ['months'=>__('product.months'), 'days'=>__('product.days'), ''
              =>__('product.not_applicable') ], $product->expiry_period_type, ['class' => 'form-control select2
              pull-left', 'style' => 'width:40%;', 'id' => 'expiry_period_type', 'disabled' => $disabled_period]); !!}
            </div>
          </div>
        </div>
        @endif
        <div class="col-lg-6 col-sm-5">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('enable_sr_no', 1, $product->enable_sr_no, ['id' => 'imei', 'class' =>
              'input-icheck']); !!} <strong>@lang('lang_v1.enable_imei_or_sr_no')</strong>
              @show_tooltip(__('lang_v1.tooltip_sr_no'))
            </label>
          </div>
        </div>
        <!-- Rack, Row & position number -->
        @if(session('business.enable_racks') || session('business.enable_row') || session('business.enable_position'))
        <div class="col-md-12">
          <h4>@lang('lang_v1.rack_details'):
            @show_tooltip(__('lang_v1.tooltip_rack_details'))
          </h4>
        </div>
        @foreach($business_locations as $id => $location)
        <div class="col-lg-3 col-sm-6">
          <div class="form-group">
            {!! Form::label('rack_' . $id, $location . ':') !!}
            @if(!empty($rack_details[$id]))
            @if(session('business.enable_racks'))
            {!! Form::text('product_racks_update[' . $id . '][rack]', $rack_details[$id]['rack'], ['class' =>
            'form-control', 'id' => 'rack_' . $id]); !!}
            @endif

            @if(session('business.enable_row'))
            {!! Form::text('product_racks_update[' . $id . '][row]', $rack_details[$id]['row'], ['class' =>
            'form-control']); !!}
            @endif

            @if(session('business.enable_position'))
            {!! Form::text('product_racks_update[' . $id . '][position]', $rack_details[$id]['position'], ['class' =>
            'form-control']); !!}
            @endif
            @else
            {!! Form::text('product_racks[' . $id . '][rack]', null, ['class' => 'form-control', 'id' => 'rack_' . $id,
            'placeholder' => __('lang_v1.rack')]); !!}

            {!! Form::text('product_racks[' . $id . '][row]', null, ['class' => 'form-control', 'placeholder' =>
            __('lang_v1.row')]); !!}

            {!! Form::text('product_racks[' . $id . '][position]', null, ['class' => 'form-control', 'placeholder' =>
            __('lang_v1.position')]); !!}
            @endif

          </div>
        </div>
        @endforeach
        @endif
        <div class="col-lg-3 col-sm-6">
          <div class="form-group">
            {!! Form::label('weight', __('lang_v1.weight') . ':') !!}
            {!! Form::text('weight', $product->weight, ['id' => 'weight', 'class' => 'form-control', 'placeholder' =>
            __('lang_v1.weight')]); !!}
          </div>
        </div>
        <div class="col-lg-3 col-sm-6">
          <div class="form-group">
            {!! Form::label('volume', __('product.volume') . ':') !!}
            {!! Form::text('volume', $product->volume, ['id' => 'volume', 'class' => 'form-control input_number',
            'placeholder' => __('product.volume')]); !!}
          </div>
        </div>
        <div class="col-lg-3 col-sm-6">
          <div class="form-group">
            {!! Form::label('download_time', __('product.download_time') . ':') !!}
            {!! Form::text('download_time', $product->download_time, ['id' => 'download_time', 'class' =>
            'form-control', 'placeholder' => 'hh:mm:ss']); !!}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="boxform_u box-solid_u">
    <div class="box-body">
      <div class="row">
        <div class="col-lg-4 col-sm-6 @if(!session('business.enable_price_tax')) hide @endif">
          <div class="form-group">
            {!! Form::label('tax', __('product.applicable_tax') . ':') !!}
            {!! Form::select('tax', $taxes, $product->tax, ['placeholder' => __('messages.please_select'), 'class' =>
            'form-control select2']); !!}
            {!! Form::hidden('tax_percent', $tax_percent, ['id' => 'tax_percent']) !!}
          </div>
        </div>
        <div class="col-lg-4 col-sm-6 @if(!session('business.enable_price_tax')) hide @endif">
          <div class="form-group">
            {!! Form::label('tax_type', __('product.selling_price_tax_type') . ':*') !!}
            {!! Form::select('tax_type',['inclusive' => __('product.inclusive'), 'exclusive' =>
            __('product.exclusive')], $product->tax_type,
            ['class' => 'form-control select2', 'required']); !!}
          </div>
        </div>
        <div class="col-lg-4 col-sm-6">
          <div class="form-group">
            {!! Form::label('type', __('product.product_type') . ':*') !!} @show_tooltip(__('tooltip.product_type'))
            {!! Form::select('type', ['single' => 'Single', 'variable' => 'Variable'], $product->type, ['class' =>
            'form-control select2',
            'required','disabled', 'data-action' => 'edit', 'data-product_id' => $product->id ]); !!}
          </div>
        </div>
      </div>
      @can("product.edit_price")
      <div class="row">
        <div class="form-group col-sm-12" id="product_form_part"></div>
        <input type="hidden" id="variation_counter" value="0">
        <input type="hidden" id="default_profit_percent" value="{{ $default_profit_percent }}">
      </div>
      @endcan
      <div class="row">
        <input type="hidden" name="submit_type" id="submit_type">
        <div class="col-sm-12">
          <div class="text-center">

            <div class="btn-group">
              <div class="btn-group dropleft" role="group">
                <button type="submit" value="submit" class="btn btn-primary submit_product_edit_form">@lang('messages.update')</button>
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-sort-desc"></i>
                <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                  <li>
                    @if($selling_price_group_count)
                    <a href="#" id="submit_n_add_selling_prices" type="submit" value="submit_n_add_selling_prices"
                      class="submit_product_form">@lang('lang_v1.save_n_add_selling_price_group_prices')</a>
                    @endif
                  </li>
                  <li>
                    <a href="#" id="update_n_edit_opening_stock" type="submit" @if(empty($product->enable_stock)) disabled="true"
                      @endif id="opening_stock_button" value="update_n_edit_opening_stock" class="submit_product_form">
                      @lang('lang_v1.update_n_edit_opening_stock')
                    </a>
                  </li>
                  <li>
                    <a  href="#" type="submit" value="save_n_add_another" class="submit_product_form">
                      @lang('lang_v1.update_n_add_another')
                    </a>
                  </li>
                </ul>
              </div>
            </div>
            <a href="{!!URL::to('/products')!!}">
              <button id="btnBack" type="button" class="btn btn-danger">@lang('messages.cancel')</button>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  {!! Form::close() !!}
</section>
<!-- /.content -->
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
  @include('contact.create', ['quick_add' => true])
</div>

@endsection

@section('javascript')
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
  var cont = 0;
  var supplier_ids = [];

  Array.prototype.removeItem = function (a) {
    for (var i = 0; i < this.length; i++) {
      if (this[i] == a) {
        for (var i2 = i; i2 < this.length - 1; i2++) {
          this[i2] = this[i2 + 1];
        }
        this.length = this.length - 1;
        return;
      }
    }
  };

  function showWarranty() {
        if ($("#has_warranty").is(":checked")) {
            $('#has_warranty').val('1');
            $('#hasW').show();
        } else {
            $('#hasW').hide();
            $("#warranty").val('');
            $('#has_warranty').val('0');
        }
    }

  
  function addSupplier()
  {
    var route = "/contacts/showSupplier/"+id;
    $.get(route, function(res){
      supplier_id = res.id;
      name = res.name;
      count = parseInt(jQuery.inArray(supplier_id, supplier_ids));
      if (count >= 0)
      {
       Swal.fire
       ({
        title: "{{__('contact.supplier_already_added')}}",
        icon: "error",
      });
     }
     else
     {      
      supplier_ids.push(supplier_id);
      var fila = '<tr class="selected" id="fila'+cont+'" style="height: 10px"><td><input type="hidden" name="supplier_ids[]" value="'+supplier_id+'">'+res.supplier_business_name+'</td><td>'+name+'</td><td>'+res.mobile+'</td><td><input type="text" id="catalogue'+cont+'" name="catalogue['+cont+']" class="form-control form-control-sm" required></td><td><input type="number" name="uxc['+cont+']" class="form-control form-control-sm" required></td><td><input type="number" name="weight_product['+cont+']" class="form-control form-control-sm" required></td><td><input type="number" name="dimensions['+cont+']" class="form-control form-control-sm" required></td><td><input type="text" name="custom_field['+cont+']" class="form-control form-control-sm" required></td><td>N/A</td><td>N/A</td><td>N/A</td><td>N/A</td><td><button id="bitem'+cont+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteSupplier('+cont+', '+supplier_id+');"><i class="fa fa-times"></i></button></td></tr>'
      $("#lista").append(fila);
      $("#catalogue"+cont+"").focus();
      cont++;
    }
  });
  }
  $("#supplier_id").change(function(event){  
    id = $("#supplier_id").val();
    if(id.length > 0)
    {
      addSupplier();
    }
  });


  function deleteSupplier(index, id){ 
    $("#fila" + index).remove();
    supplier_ids.removeItem(id);
  }

  $(document).ready(function()
  {
    showWarranty();
    product_id = $("#product_id").val();
    var route = "/products/productHasSuppliers/"+product_id
    $.get(route, function(res){
      $(res).each(function(key,value){
        supplier_id = value.id
        supplier_ids.push(supplier_id);
        var fila = '<tr class="selected" id="fila'+cont+'" style="height: 10px"><td><input type="hidden" name="supplier_ids[]" value="'+value.id+'">'+value.supplier_business_name+'</td><td>'+value.name+'</td><td>'+value.mobile+'</td><td><input type="text" id="catalogue'+cont+'" value="'+value.catalogue+'" name="catalogue['+cont+']" class="form-control form-control-sm" required></td><td><input type="number" value="'+value.uxc+'" name="uxc['+cont+']" class="form-control form-control-sm" required></td><td><input type="number" value="'+value.weight+'" name="weight_product['+cont+']" class="form-control form-control-sm" required></td><td><input type="number" value="'+value.dimensions+'" name="dimensions['+cont+']" class="form-control form-control-sm" required></td><td><input type="text" value="'+value.custom_field+'" name="custom_field['+cont+']" class="form-control form-control-sm" required></td><td>'+value.last_purchase+'</td><td>'+value.quantity+'</td><td>'+value.price+'</td><td>'+value.total+'</td><td><button id="bitem'+cont+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteSupplier('+cont+', '+supplier_id+');"><i class="fa fa-times"></i></button></td></tr>'
        $("#lista").append(fila);
        cont++;
      });
    });
    
    var route = "/products/kitHasProduct/"+product_id;
    $.get(route, function(res){
      $(res).each(function(key,value){
        variation_id = value.variation_id;
        product_id = value.product_id;
        if(value.kit_line != null){
          kit_line = value.kit_line;
        }
        if(value.sku == value.sub_sku){
          name = value.name_product;
        }
        else{
          name = ""+value.name_product+" "+value.name_variation+"";
        }
        if(value.brand != null){
          brand = value.brand;
        }
        else{
          brand = 'N/A';
        }
        if(value.default_purchase_price != null){
          price = value.default_purchase_price;
        }
        else{
          price = 'N/A';
        }
        if(value.sub_sku != null){
          sku = value.sub_sku;
        }
        else{
          sku = 'N/A';
        }
        var count = $('input#product_id-'+variation_id);
        //scount = parseInt(jQuery.inArray(variation_id, kit_idsk));
        if (count.length > 0)
        {
          Swal.fire
          ({
            title: "{{__('product.product_already_added')}}",
            icon: "error",
          });
        }
        else{
          contk = variation_id;
          //contk = $('#kitTable >tbody >tr').length;
          unit_label = '<input type="hidden" value="'+value.unit_kit+'" name="kit_child['+contk+']">'+value.unit+'';
          kit_idsk.push(variation_id);
          valor.push(contk);
          var fila = '<tr class="selected" id="filak'+contk+'" style="height: 10px"><td><input type="hidden" name="product_ids[]" value="'+variation_id+'" id="product_id-'+variation_id+'">'+name+'</td><td>'+sku+'</td><td>'+brand+'</td><td><input type="hidden" name="clas_product['+contk+']" value="'+value.clasification+'">'+unit_label+'</td><td><input type="hidden" name="price['+contk+']" id="price'+contk+'" value="'+price+'">'+price+'</td><td><input type="number" id="quantity'+contk+'" name="quantity['+contk+']" class="form-control form-control-sm" min="0.01" value="'+value.quantity+'" onchange="getTotalKit()" required></td><td><button id="bitem'+contk+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteChildren('+contk+', '+variation_id+');"><i class="fa fa-times"></i></button></td></tr>';
          $("#listak").append(fila);
          contk++;
        }
      });
    });

    type = $("#product_clasification").val();

    $("#clasification").prop('disabled', true);

    if(type == "service")
    {
      $("#unit_id").prop('required', false);
      $('#divExcludeService').hide();
      $('#divExcludeService2').hide();
      $('#divExcludeService3').hide();
      $('#divExcludeService4').hide();
      $("#submit_n_add_selling_prices").hide();
      $("#update_n_edit_opening_stock").hide();
      $("#div_type").hide();
      $("#div_imei").hide();
    }

    if(type == "kits"){
      $("#unit_id").prop('required', false);
      $('#divExcludeService').show();
      $("#div_brand").hide();
      $("#div_unit").hide();
      $('#divExcludeService2').hide();
      $('#divExcludeService3').hide();
      $('#divExcludeService4').show();
      $("#submit_n_add_selling_prices").hide();
      $("#update_n_edit_opening_stock").hide();
      $("#div_type").hide();
      $("#div_imei").hide();
    }

    if(type == 'product'){
      $("#div_brand").show();
      $("#div_unit").show();
      $("#div_imei").show();
      $("#unit_id").prop('required', true);
      $('#divExcludeService').show();
      $('#divExcludeService2').show();
      $('#divExcludeService3').show();
      $('#divExcludeService4').hide();
      $("#div_type").show();
    }    
  });

$(document).on('submit', 'form#product_add_form', function(e){
  $(this).find('button[type="submit"]').attr('disabled', true);
  $("#btnBack").attr('disabled', true);
});


var contk = 0;
var kit_idsk = [];

var valor=[];
var total = 0;

function addChildren()
{
  
  var route = "/products/showProduct/"+id;
  $.get(route, function(res){
    variation_id = res.variation_id;
    product_id = res.product_id;
    //contk = $('#kitTable >tbody >tr').length;
    contk = variation_id;
    if(res.sku == res.sub_sku){
      name = res.name_product;
    }
    else{
      name = ""+res.name_product+" "+res.name_variation+"";
    }
    if(res.brand != null){
      brand = res.brand;
    }
    else{
      brand = 'N/A';
    }
    if(res.default_purchase_price != null){
      price = res.default_purchase_price;
    }
    else{
      price = 'N/A';
    }
    if(res.sub_sku != null){
      sku = res.sub_sku;
    }
    else{
      sku = 'N/A';
    }
    count = $('input#product_id-'+variation_id);
    //count = parseInt(jQuery.inArray(variation_id, kit_idsk));
    if (count.length > 0)
    {
     Swal.fire
     ({
      title: "{{__('product.product_already_added')}}",
      icon: "error",
    });
   }
   else
   {
    if(res.clasification == 'service'){
      
      unit = 'N/A'
      kit_idsk.push(variation_id);
      valor.push(contk);
      var fila = '<tr class="selected" id="filak'+contk+'" style="height: 10px"><td><input type="hidden" name="product_ids[]" value="'+variation_id+'" id="product_id-'+variation_id+'">'+name+'</td><td>'+sku+'</td><td>'+brand+'</td><td><input type="hidden" name="clas_product['+contk+']" value="service">'+unit+'</td><td><input type="hidden" name=price['+contk+'] id="price'+contk+'" value="'+price+'">'+price+'</td><td><input type="number" id="quantity'+contk+'" name="quantity['+contk+']" class="form-control form-control-sm" min="0.01" value="1" onchange="getTotalKit()" required></td><td><button id="bitem'+contk+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteChildren('+contk+', '+variation_id+');"><i class="fa fa-times"></i></button></td></tr>';
      $("#listak").append(fila);
      contk++;
      getTotalKit();
    }
    else{
      //contk = $('#kitTable >tbody >tr').length;
      var route = "/products/getUnitPlan/"+product_id;
      $.get(route, function(res){
        //contk = $('#kitTable >tbody >tr').length;
        contk = variation_id;
        if(res.plan == 'group'){
          var route = "/products/getUnitsFromGroup/"+res.unit_group_id;
          $.get(route, function(res){
            content = "";
            $(res).each(function(key,value){
              content = content + '<option value="'+value.id+'">'+value.actual_name+'</option>';
            });
            kit_idsk.push(variation_id);
            valor.push(contk);
            var fila = '<tr class="selected" id="filak'+contk+'" style="height: 10px"><td><input type="hidden" name="product_ids[]" value="'+variation_id+'" id="product_id-'+variation_id+'">'+name+'</td><td>'+sku+'</td><td>'+brand+'</td><td><input type="hidden" name="clas_product['+contk+']" value="product"><select name="kit_child['+contk+']" id="kit_child['+contk+']" class="form-control select2">'+content+'</select></td><td><input type="hidden" name=price['+contk+'] id="price'+contk+'" value="'+price+'">'+price+'</td><td><input type="number" id="quantity'+contk+'" name="quantity['+contk+']" class="form-control form-control-sm" min="0.01" value="1" onchange="getTotalKit()" required></td><td><button id="bitem'+contk+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteChildren('+contk+', '+variation_id+');"><i class="fa fa-times"></i></button></td></tr>';
            $("#listak").append(fila);
            contk++;
            getTotalKit();
          });
        }
        else{
          unit = '<input type="hidden" value="'+res.unit_id+'" name="kit_child['+contk+']">'+res.name+'';

          kit_idsk.push(variation_id);
          valor.push(contk);
          var fila = '<tr class="selected" id="filak'+contk+'" style="height: 10px"><td><input type="hidden" name="product_ids[]" value="'+variation_id+'" id="product_id-'+variation_id+'">'+name+'</td><td>'+sku+'</td><td>'+brand+'</td><td><input type="hidden" name="clas_product['+contk+']" value="product">'+unit+'</td><td><input type="hidden" name=price['+contk+'] id="price'+contk+'" value="'+price+'">'+price+'</td><td><input type="number" id="quantity'+contk+'" name="quantity['+contk+']" class="form-control form-control-sm" min="0.01" value="1" onchange="getTotalKit()" required></td><td><button id="bitem'+contk+'" type="button" class="btn btn-danger btn-xs remove-item" onclick="deleteChildren('+contk+', '+variation_id+');"><i class="fa fa-times"></i></button></td></tr>';
          $("#listak").append(fila);
          contk++;
          getTotalKit();
        }
      });
    }
  }
});
}
function deleteChildren(index, id){ 
  Swal.fire({
			title: LANG.sure,
			text: LANG.cancel_product_msg,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: LANG.yes,
			cancelButtonText: LANG.not,
		}).then((resul) => {
			if (resul.isConfirmed) {
          $("#filak" + index).remove();
          kit_idsk.removeItem(id);
          if(kit_idsk.length == 0)
          {
            total=0;
            contk = 0;
            kit_idsk=[];
            valor=[];
          }
          getTotalKit();
				
			} else {
				return false;
			}
		}); 
}

$(document).on( 'change', 'select#kit_children', function(event){
  id = $("#kit_children").val();
  if(id != 0){
    addChildren();
    $("#kit_children").val(0).change();
  }
});

$(document).on('click', 'button#btnBack', function(e) {
		e.preventDefault();
		Swal.fire({
			title: LANG.sure,
			text: LANG.cancel_transfer_msg,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: LANG.yes,
			cancelButtonText: LANG.not,
		}).then((resul) => {
			if (resul.isConfirmed) {
				setTimeout(function() { window.location = '/products'; }, 1000);
				
			} else {
				return false;
			}
		});
	});

function getTotalKit()
{
  quantity = 0.00;
  price = 0.00;
  total = 0.00;
  $.each(valor, function(value){
    quantityg = $("#quantity"+value+"").val();
    priceg = $("#price"+value+"").val();
    
    if(quantityg)
    {
      if(isNaN(quantityg))
      {
        quantity = parseFloat(0.00);
      }
      else
      {
        quantity = parseFloat($("#quantity"+value+"").val());
      }
    }
    else
    {
      quantity = parseFloat(0.00);
    }

    if(priceg)
    {
      if(isNaN(priceg))
      {
        price = 0.00;
      }
      else
      {
        price = parseFloat($("#price"+value+"").val());
      }

    }
    else
    {
      price = 0.00;
    }
    subtotal = quantity * price;
    if(isNaN(subtotal))
    {
      subtotal = 0.00;
    }
    if(isInfinite(total))
    {
      subtotal = 0.00;
    }
    total = total + subtotal;
  });
  $("#single_dpp").val(total);
  var purchase_exc_tax = __read_number($('input#single_dpp'));
  purchase_exc_tax = (purchase_exc_tax == undefined) ? 0 : purchase_exc_tax;

  var tax_rate = $('select#tax').find(':selected').data('rate');
  tax_rate = (tax_rate == undefined) ? 0 : tax_rate;

  var price_precision = $('#price_precision').val();

  var purchase_inc_tax = __add_percent(purchase_exc_tax, tax_rate);
  __write_number($('input#single_dpp_inc_tax'), purchase_inc_tax, false, price_precision);

  var profit_percent = __read_number($('#profit_percent'));
  var selling_price = __add_percent(purchase_exc_tax, profit_percent);
  __write_number($('input#single_dsp'), selling_price, false, price_precision);

  var selling_price_inc_tax = __add_percent(selling_price, tax_rate);
  __write_number($('input#single_dsp_inc_tax'), selling_price_inc_tax, false, price_precision);
}

function isInfinite(n)
{
  return n === n/0;
}
</script>
@endsection