<!-- Main content -->
{!! Form::open(['url' => action('Optics\ProductController@store'), 'method' => 'post', 
'id' => 'product_add_form','class' => 'product_form', 'files' => true ]) !!}
<div class="boxform_u box-solid_u">
  <div class="box-body">
    <div class="row">
      {{-- Number of decimal places to store and use in calculations --}}
			<input type="hidden" id="price_precision" value="{{ config('app.price_precision') }}">

      {{-- type_ --}}
      <input type="hidden" id="type_" value="{{ $type_ }}">

      {{-- Don't check with supplier has tax number --}}
      <input type="hidden" id="no-verified-supplier" value="1">

      {{-- Check sku url --}}
      <input type="hidden" id="check-sku-url" value="{{ action('Optics\ProductController@checkSkuUnique') }}">

      {{-- Number of decimal places to store and use in calculations --}}
			<input type="hidden" id="price_precision" value="{{ config('app.price_precision') }}">

      {{-- clasification --}}
      <div class="col-sm-4">
        <div class="form-group">
          <label from="clasification">@lang('product.clasification')</label>
          <select name="clasification" id="clasification" class="form-control select2" style="width: 100%;">    
            <option value='product' @if ($type_ != 'material') selected @endif>@lang('product.clasification_product')</option>
            <option value='kits'>@lang('product.clasification_kits')</option>
            <option value='service'>@lang('product.clasification_service')</option>
            <option value='material' @if ($type_ == 'material') selected @endif>@lang('material.clasification_material')</option>
          </select>
        </div>
      </div>

      {{-- name --}}
      <div class="col-sm-4">
        <div class="form-group">
          {!! Form::label('name', $type_ != 'material' ? __('product.product_name') . ':*' : __('material.material_name') . ':*') !!}
          {!! Form::text('name', !empty($duplicate_product->name) ? $duplicate_product->name : null, ['class' => 'form-control', 'required',
          'placeholder' => __('product.product_name')]); !!}
          <input type="hidden" name="create" value="1">
        </div>
      </div>

      {{-- status --}}
      <div class="col-sm-4">
        <div class="form-group">          
          <label>
            <input type="checkbox" name="is_active" id="is_active" value="1" class="input-icheck" checked>
            <strong>@lang('product.is_active')</strong>
          </label>@if ($type_ != "material") @show_tooltip(__('product.is_active_help')) @else @show_tooltip(__('material.is_active_help')) @endif
          <p class="help-block"><i>@if ($type_ != "material") @lang('product.is_active_help') @else @lang('material.is_active_help') @endif</i></p>
        </div>
      </div>

      <div class="clearfix"></div>

      {{-- model --}}
      <div class="col-sm-4 product_exc_fields">
        <div class="form-group">
          {!! Form::label('model', __('product.model') . ':') !!}
          {!! Form::text('model', null, ['class' => 'form-control',
          'placeholder' => __('product.model')]); !!}
        </div>
      </div>

      {{-- measurement --}}
      <div class="col-sm-4 product_exc_fields">
        <div class="form-group">
          {!! Form::label('measurement', __('lang_v1.size') . ':') !!}
          {!! Form::text('measurement', null, ['class' => 'form-control size-mask',
          'placeholder' => __('lang_v1.size')]); !!}
        </div>
      </div>

      {{-- material_id --}}
      <div class="col-sm-4 product_exc_fields">
        <div class="form-group">
          {!! Form::label('material_id', __('product.clasification_material') . ':') !!}
          {!! Form::select('material_id', $materials,
            !empty($duplicate_product->material_id) ? $duplicate_product->material_id : null,
            ['style' => 'width: 100%', 'class' => 'form-control select2',
            'placeholder' => __('messages.please_select')]); !!}
          </div>
        </div>

        <div class="clearfix"></div>

        {{-- category_id --}}
        <div class="col-sm-4 @if(!session('business.enable_category')) hide @endif">
          <div class="form-group">
            {!! Form::label('category_id', __('product.category') . ':') !!}
            {!! Form::select('category_id', $categories, !empty($duplicate_product->category_id) ? $duplicate_product->category_id : null, ['style' => 'width: 100%;', 'placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!}
          </div>
        </div>

        {{-- sub_category_id --}}
        <div class="col-sm-4 @if(!(session('business.enable_category') && session('business.enable_sub_category'))) hide @endif">
          <div class="form-group">
            {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
            {!! Form::select('sub_category_id', $sub_categories, !empty($duplicate_product->sub_category_id) ? $duplicate_product->sub_category_id : null, ['style' => 'width: 100%;', 'placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!}
          </div>
        </div>

        {{-- sku --}}
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('sku', __('product.sku') . ':') !!} @show_tooltip(__('tooltip.sku'))
            {!! Form::text('sku', null,
              ['class' => 'form-control', 'placeholder' => __('product.sku'), 'id' => 'sku']); !!}
          </div>
        </div>

        {{-- AR for services --}}
        <div class="col-sm-4" id="ar_div" style="display: none;">
          <div class="form-group">
            {!! Form::label('ar', "AR" . ':') !!}
            {!! Form::select("ar", $ar, null, ["class" => "form-control select2", "id" => "ar",
            "placeholder" => __('messages.please_select'), "style" => "width: 100%"]) !!}
          </div>
        </div>

        <div class="clearfix"></div>

        {{-- Exclude service --}}
        <div id="divExcludeService">
          {{-- barcode_type --}}
        {{--
        <div class="col-sm-4 product_fields">
          <div class="form-group">
            {!! Form::label('barcode_type', __('product.barcode_type') . ':*') !!}
            {!! Form::select('barcode_type', $barcode_types, !empty($duplicate_product->barcode_type) ? $duplicate_product->barcode_type : $barcode_default, ['style' => 'width: 100%', 'class' => 'form-control select2', 'required']); !!}
          </div>
        </div>
        --}}

        {{-- material_type_id --}}
        <div class="col-sm-4 material_fields" id="div_material_type">
          <div class="form-group">
            {!! Form::label('material_type_id', __('material_type.material_type') . ':') !!}
            <div class="input-group">
              {!! Form::select('material_type_id', $material_types,
                !empty($duplicate_product->material_type_id) ? $duplicate_product->material_type_id : null,
                ['style' => 'width: 100%', 'class' => 'form-control select2',
                'placeholder' => __('messages.please_select')]); !!}
                <span class="input-group-btn">
                  <button type="button"
                  @if(!auth()->user()->can('material_type.create')) disabled @endif
                  class="btn btn-default bg-white btn-flat btn-modal"
                  data-href="{{action('Optics\MaterialTypeController@create', ['quick_add' => true])}}"
                  title="@lang('material_type.add_material_type')" data-container=".view_modal">
                  <i class="fa fa-plus-circle text-primary fa-lg"></i>
                </button>
              </span>
            </div>
          </div>
        </div>

        {{-- brand_id --}}
        <div class="col-sm-4" id="div_brand">
          <div class="form-group">
            {!! Form::label('brand_id', __('product.brand') . ':') !!}
            <div class="input-group">
              {!! Form::select('brand_id', $brands, !empty($duplicate_product->brand_id) ? $duplicate_product->brand_id : null, ['style' => 'width: 100%', 'placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!}
              <span class="input-group-btn">
                <button type="button" @if(!auth()->user()->can('brand.create')) disabled @endif class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('BrandController@create', ['quick_add' => true])}}" title="@lang('brand.add_brand')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
              </span>
            </div>
          </div>
        </div>

        {{-- unit_id --}}
        <div class="col-sm-4" id="div_unit">
          <div class="form-group">
            {!! Form::label('unit_id', __('product.unit') . ':*') !!}

            <div class="input-group">
              {!! Form::select('unit_id', $units,
                !empty($duplicate_product->unit_id) ? $duplicate_product->unit_id : session('business.default_unit'),
                ['style' => 'width: 100%', 'class' => 'form-control select2', 'required']) !!}

                <span class="input-group-btn">
                  <button
                    @if (!auth()->user()->can('unit.create'))
                    disabled
                    @endif
                    class="btn btn-default bg-white btn-flat btn-modal"
                    data-href="{{ action('UnitController@create', ['quick_add' => true]) }}"
                    title="@lang('brand.add_brand')" data-container=".view_modal">
                    <i class="fa fa-plus-circle text-primary fa-lg"></i>
                  </button>
                </span>
              </div>
            </div>
          </div>
        </div>

        {{-- Exclude service 2 --}}
        <div id="divExcludeService2">
          {{-- enable_stock --}}
          <div class="col-sm-4 @if(!empty($duplicate_product) && $duplicate_product->enable_stock == 0) hide @endif" id="alert_quantity_div">
           <div class="form-group" style="display: none;">
            <label>
              {!! Form::checkbox('enable_stock', 1, !empty($duplicate_product) ? $duplicate_product->enable_stock : true, ['class' => 'input-icheck', 'id' => 'enable_stock']); !!} <strong>@lang('product.manage_stock')</strong>
            </label>@show_tooltip(__('tooltip.enable_stock')) <p class="help-block"><i>@lang('product.enable_stock_help')</i></p>
          </div>

          {{-- alert_quantity --}}
          <div class="form-group">
            {!! Form::label('alert_quantity',  __('product.alert_quantity') . ':*') !!} @show_tooltip(__('tooltip.alert_quantity'))
            {!! Form::number('alert_quantity', !empty($duplicate_product->alert_quantity) ? $duplicate_product->alert_quantity : null , ['id' => 'alert_quantity', 'class' => 'form-control', 'required',
            'placeholder' => __('product.alert_quantity'), 'min' => '0']); !!}
          </div>        
        </div>

        {{-- dai --}}
        <div class="col-sm-4 product_fields">
          <div class="form-group">
            <label from="dai">@lang('product.dai')</label>
            <input type="text" name="dai" id="dai" class="form-control" placeholder="@lang('product.dai_label')">
          </div>
        </div>
    </div>

    <div class="clearfix"></div>

    {{-- description --}}
    <div class="col-sm-8">
      <div class="form-group">
        {!! Form::label('product_description', $type_ != 'material' ? __('lang_v1.product_description') . ':' : __('material.material_description') . ':') !!}
        {!! Form::textarea('product_description', !empty($duplicate_product->product_description) ? $duplicate_product->product_description : null, ['id' => 'product_description' ,'class' => 'form-control']); !!}
      </div>
    </div>

    {{-- image --}}
    <div class="col-sm-4">
      <div class="form-group">
        {!! Form::label('image', $type_ != 'material' ? __('lang_v1.product_image') . ':' : __('material.material_image') . ':') !!}
        {!! Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); !!}
        <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
      </div>
    </div>

    <div class="col-sm-8">
      <div class="form-group">
        {!! Form::label(__('quote.warranty') . ':') !!}
        <input type="text" name="warranty" id="warranty" class="form-control" placeholder="@lang('quote.warranty')">
      </div>
    </div>

    
  </div>
</div>

{{-- Exclude service 3 --}}
<div id="divExcludeService3">
  <div class="boxform_u box-solid_u">
    <div class="box-body">
      <div class="row">
        {{-- contact_id --}}
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!! Form::select('contact_id', [], null, ['style' => 'width: 100%', 'class' => 'form-control', 'placeholder' => __('messages.please_select'), 'id' => 'supplier_id']); !!}
              <span class="input-group-btn">
                <button type="button" class="btn btn-default bg-white btn-flat add_new_supplier" data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
              </span>
            </div>
          </div>
        </div>

        <div class="col-sm-6">
          <div class="form-group">
            <label for="supplier_name">&nbsp;</label>
            {!! Form::text('supplier_name', '', ['class' => 'form-control', 'readonly', 'id' => 'supplier_name']); !!}
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
                @if ($type_ == 'product')
                <th>@lang('contact.catalogue')</th>
                <th>@lang('contact.uxc')</th>
                <th>@lang('lang_v1.weight')</th>
                <th>@lang('contact.dimensions')</th>
                <th>@lang('contact.custom')</th>
                @endif
              </tr>
            </thead>
            <tbody id="lista">
            </tbody>
          </table>
        </div>
      </div>            
    </div>
  </div>
</div>

{{-- Exclude service 4 --}}
<div id="divExcludeService4" style="display: none;">
  <div class="boxform_u box-solid_u">
    <div class="box-body">
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            {!! Form::label('kit_children', __('product.clasification_product') . ':*') !!}
            <select name="kit_children" id="kit_children" class="form-control select2" style="width: 100%;">
              <option value="0">@lang('messages.please_select')</option>
              @foreach($products as $product)
              @if($product->sku != $product->sub_sku)
              <option value="{{ $product->id }}">{{ $product->name_product }} {{ $product->name_variation }}</option>
              @else
              <option value="{{ $product->id }}">{{ $product->name_product }}</option>
              @endif
              @endforeach
            </select>
          </div>
        </div>            
        <div class="col-sm-9">
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <table id="kitTable" class="table table-responsive" width="100%">
            <thead>
              <tr>
                <th style="width: 10%;">@lang('messages.actions')</th>
                <th style="width: 15%;">@lang('sale.product')</th>
                <th style="width: 15%;">@lang('product.sku')</th>
                <th style="width: 15%;">@lang('product.brand')</th>
                <th style="width: 15%;">@lang('product.unit')</th>
                <th style="width: 15%;">@lang('product.price')</th>
                <th style="width: 15%;">@lang('product.product_quantity')</th>
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
      <div class="col-sm-4 @if($hide) hide @endif">
        <div class="form-group">
          <div class="multi-input">
            {!! Form::label('expiry_period', __('product.expires_in') . ':') !!}<br>
            {!! Form::text('expiry_period', !empty($duplicate_product->expiry_period) ? @num_format($duplicate_product->expiry_period) : $expiry_period, ['class' => 'form-control pull-left input_number',
            'placeholder' => __('product.expiry_period'), 'style' => 'width:60%;']); !!}
            {!! Form::select('expiry_period_type', ['months'=>__('product.months'), 'days'=>__('product.days'), '' =>__('product.not_applicable') ], !empty($duplicate_product->expiry_period_type) ? $duplicate_product->expiry_period_type : 'months', ['style' => 'width: 100%', 'class' => 'form-control select2 pull-left', 'style' => 'width:40%;', 'id' => 'expiry_period_type']); !!}
          </div>
        </div>
      </div>
      @endif
      <div class="col-sm-4">
        <div class="form-group">
          <label>
            {!! Form::checkbox('enable_sr_no', 1, !(empty($duplicate_product)) ? $duplicate_product->enable_sr_no : false, ['id' => 'imei','class' => 'input-icheck']); !!} <strong>@lang('lang_v1.enable_imei_or_sr_no')</strong>
          </label>@show_tooltip(__('lang_v1.tooltip_sr_no'))
        </div>
      </div>

      <div class="clearfix"></div>

      <!-- Rack, Row & position number -->
      @if(session('business.enable_racks') || session('business.enable_row') || session('business.enable_position'))
      <div class="col-md-12">
        <h4>@lang('lang_v1.rack_details'):
          @show_tooltip(__('lang_v1.tooltip_rack_details'))
        </h4>
      </div>
      @foreach($business_locations as $id => $location)
      <div class="col-sm-3">
        <div class="form-group">
          {!! Form::label('rack_' . $id,  $location . ':') !!}

          @if(session('business.enable_racks'))
          {!! Form::text('product_racks[' . $id . '][rack]', !empty($rack_details[$id]['rack']) ? $rack_details[$id]['rack'] : null, ['class' => 'form-control', 'id' => 'rack_' . $id, 
          'placeholder' => __('lang_v1.rack')]); !!}
          @endif

          @if(session('business.enable_row'))
          {!! Form::text('product_racks[' . $id . '][row]', !empty($rack_details[$id]['row']) ? $rack_details[$id]['row'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.row')]); !!}
          @endif

          @if(session('business.enable_position'))
          {!! Form::text('product_racks[' . $id . '][position]', !empty($rack_details[$id]['position']) ? $rack_details[$id]['position'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.position')]); !!}
          @endif
        </div>
      </div>
      @endforeach
      @endif
      <div class="col-sm-4">
        <div class="form-group">
          {!! Form::label('weight',  __('lang_v1.weight') . ':') !!}
          {!! Form::text('weight', !empty($duplicate_product->weight) ? $duplicate_product->weight : null, ['id' => 'weight', 'class' => 'form-control', 'placeholder' => __('lang_v1.weight')]); !!}
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
</div>

<div class="boxform_u box-solid_u">
  <div class="box-body">
    <div class="row">
      <div class="col-sm-4 @if(!session('business.enable_price_tax')) hide @endif">
        <div class="form-group">
          {!! Form::label('tax_type', __('product.selling_price_tax_type') . ':*') !!}
          {!! Form::select('tax_type', ['inclusive' => __('product.inclusive'), 'exclusive' => __('product.exclusive')], !empty($duplicate_product->tax_type) ? $duplicate_product->tax_type : 'exclusive',
          ['style' => 'width: 100%', 'class' => 'form-control select2', 'required']); !!}
        </div>
      </div>       

      <div class="col-sm-4 @if(!session('business.enable_price_tax')) hide @endif">
        <div class="form-group">
          {!! Form::label('tax', __('product.applicable_tax') . ':') !!}
          {{--!! Form::select('tax', $taxes, !empty($duplicate_product->tax) ? $duplicate_product->tax : null, ['style' => 'width: 100%', 'placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!--}}
          <select name="tax" id="tax" class="form-control select2" placeholder="'Please Select'" style="width: 100%;">
            <option value="" data-tax_amount="0" data-tax_type="fixed" selected>@lang('lang_v1.none')</option>
            @foreach($taxes as $tax)
            <option value="{{ $tax['id'] }}" data-tax_amount="{{ $tax['percent'] }}" data-tax_type="">{{ $tax['name'] }}</option>
            @endforeach
          </select>
          {!! Form::hidden('tax_percent', null, ['id' => 'tax_percent']) !!}

        </div>
      </div>
      <div class="col-sm-4" id="div_type">
        <div class="form-group">
          {!! Form::label('type', __('product.product_type') . ':*') !!} @show_tooltip(__('tooltip.product_type'))
          {!! Form::select('type', ['single' => __('product.single'), 'variable' => __('product.variable')], !empty($duplicate_product->type) ? $duplicate_product->type : null, ['style' => 'width: 100%', 'class' => 'form-control select2',
          'required', 'data-action' => !empty($duplicate_product) ? 'duplicate' : 'add', 'data-product_id' => !empty($duplicate_product) ? $duplicate_product->id : '0']); !!}
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="form-group col-sm-11 col-sm-offset-1" id="product_form_part"></div>

      <input type="hidden" id="variation_counter" value="1">
      <input type="hidden" id="default_profit_percent" 
      value="{{ $default_profit_percent }}">

    </div>
  </div>
</div>


<div class="row">
  <div class="col-sm-12">
    <input type="hidden" name="submit_type" id="submit_type">
    <div class="text-center">
      <div class="btn-group">
        @if($selling_price_group_count)
        <button id="submit_n_add_selling_prices" type="submit" value="submit_n_add_selling_prices" class="btn btn-warning submit_product_form">@lang('lang_v1.save_n_add_selling_price_group_prices')</button>
        @endif

        <button id="opening_stock_button" @if(!empty($duplicate_product) && $duplicate_product->enable_stock == 0) disabled @endif type="submit" value="submit_n_add_opening_stock" class="btn bg-purple submit_product_form">@lang('lang_v1.save_n_add_opening_stock')</button>

        <button type="submit" value="save_n_add_another" class="btn bg-maroon submit_product_form">@lang('lang_v1.save_n_add_another')</button>

        <button id="btnAdd" type="submit" value="submit" class="btn btn-primary submit_product_form">@lang('messages.save')</button>
      </div>
    </div>
  </div>
</div>

{!! Form::close() !!}
<!-- /.content -->
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
  {{-- @include('contact.create', ['quick_add' => true]) --}}
</div>