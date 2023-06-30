<!-- Main content -->
{!! Form::open(['url' => action('ProductController@store'), 'method' => 'post', 'id' => 'product_add_form','class' =>
'product_form', 'files' => true ]) !!}
<div class="boxform_u box-solid_u">
  <div class="box-body">
    <div class="row align-items-center">
      {{-- Number of decimal places to store and use in calculations --}}
      <input type="hidden" id="price_precision" value="{{ config('app.price_precision') }}">

      <div class="form-group col-lg-4 col-sm-6">
          <label from="clasification">@lang('product.clasification')</label>
          <select name="clasification" id="clasification" class="form-control select2" style="width: 100%;">
            <option value='product' selected>@lang('product.clasification_product')</option>
            <option value='kits'>@lang('product.clasification_kits')</option>
            <option value='service'>@lang('product.clasification_service')</option>
          </select>
      </div>

      <div class="form-group col-lg-4 col-sm-6">
          {!! Form::label('name', __('product.product_name') . ':') !!}<span class="text-danger"> <strong>*</strong></span>
          {!! Form::text('name', !empty($duplicate_product->name) ? $duplicate_product->name : null, ['class' =>
          'form-control', 'required',
          'placeholder' => __('product.product_name')]); !!}
          <input type="hidden" name="create" value="1">
      </div>
      <div class="form-group col-lg-4 col-sm-6" style="vertical-align: middle; !important">
        <label>
          <input type="checkbox" name="is_active" id="is_active" value="1" class="input-icheck" checked>
          <strong>@lang('product.is_active')</strong>
        </label>@show_tooltip(__('product.is_active_help')) 
        <p class="help-block"><i>@lang('product.is_active_help')</i></p>
      </div>

      {{-- <div class="clearfix"></div> --}}

      {{-- flag-category --}}
      <input type="hidden" id="flag-category" value="">

      {{-- category_id --}}
      <div class="form-group col-lg-4 col-sm-6 py-3 @if(!session('business.enable_category')) hide @endif">

        {!! Form::label('category_id', __('product.category') . ':') !!}
        <div class="input-group">
          {!! Form::select('category_id', $categories,
          ! empty($duplicate_product->category_id) ? $duplicate_product->category_id : null,
          ['style' => 'width: 100%;', 'placeholder' => __('messages.please_select'), 'class' => 'form-control
          select2']); !!}

          <span class="input-group-btn">
            <button id="btn-plus-category" @if (! auth()->user()->can('category.create'))
              disabled
              @endif
              class="btn btn-default bg-white btn-flat btn-modal"
              data-href="{{ action('CategoryController@create', ['quick_add' => true, 'type' => 'category']) }}"
              title="@lang('category.add_category')"
              data-container=".view_modal">
              <i class="fa fa-plus-circle text-primary fa-lg"></i>
            </button>
          </span>
        </div>
      </div>

      {{-- sub_category_id --}}
      <div
        class="col-lg-4 col-sm-6 @if(!(session('business.enable_category') && session('business.enable_sub_category'))) hide @endif">
        <div class="form-group">
          {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}

          <div class="input-group">
            {!! Form::select('sub_category_id', $sub_categories,
            ! empty($duplicate_product->sub_category_id) ? $duplicate_product->sub_category_id : null,
            ['style' => 'width: 100%;', 'placeholder' => __('messages.please_select'), 'class' => 'form-control
            select2']); !!}

            <span class="input-group-btn">
              <button id="btn-plus-sub-category" @if (! auth()->user()->can('category.create'))
                disabled
                @endif
                class="btn btn-default bg-white btn-flat btn-modal"
                data-href="{{ action('CategoryController@create', ['quick_add' => true, 'type' => 'sub-category']) }}"
                title="@lang('category.add_category')"
                data-container=".view_modal">
                <i class="fa fa-plus-circle text-primary fa-lg"></i>
              </button>
            </span>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-sm-6">
        <div class="form-group">
          {!! Form::label('sku', __('product.sku') . ':') !!} @show_tooltip(__('tooltip.sku'))
          {!! Form::text('sku', null, ['class' => 'form-control',
          'placeholder' => __('product.sku')]); !!}
        </div>
      </div>

      {{-- <div class="clearfix"></div> --}}

      <div id="divExcludeService">

        <div class="col-lg-4 col-sm-6">
          <div class="form-group">
            {!! Form::label('barcode_type', __('product.barcode_type') . ':') !!}<span class="text-danger"> <strong>*</strong></span>
            {!! Form::select('barcode_type', $barcode_types, !empty($duplicate_product->barcode_type) ?
            $duplicate_product->barcode_type : $barcode_default, ['style' => 'width: 100%', 'class' => 'form-control
            select2', 'required']); !!}
          </div>
        </div>

        <div class="col-lg-4 col-sm-6" id="div_brand">
          <div class="form-group">
            {!! Form::label('brand_id', __('product.brand') . ':') !!}
            <div class="input-group">
              {!! Form::select('brand_id', $brands, !empty($duplicate_product->brand_id) ? $duplicate_product->brand_id
              : null, ['style' => 'width: 100%', 'placeholder' => __('messages.please_select'), 'class' => 'form-control
              select2']); !!}
              <span class="input-group-btn">
                <button type="button" @if(!auth()->user()->can('brand.create')) disabled @endif class="btn btn-default
                  bg-white btn-flat btn-modal" data-href="{{action('BrandController@create', ['quick_add' => true])}}"
                  title="@lang('brand.add_brand')" data-container=".view_modal"><i
                    class="fa fa-plus-circle text-primary fa-lg"></i></button>
              </span>
            </div>
          </div>
        </div>

        {{-- unit_id --}}
        <div class="col-lg-4 col-sm-6" id="div_unit">
          <div class="form-group">
            {!! Form::label('unit_id', __('product.unit') . ':') !!}

            <div class="input-group">
              {!! Form::select('unit_id', $units,
              ! empty($duplicate_product->unit_id) ? $duplicate_product->unit_id : session('business.default_unit'),
              ['style' => 'width: 100%', 'class' => 'form-control select2', 'required']); !!}

              <span class="input-group-btn">
                <button @if (! auth()->user()->can('unit.create'))
                  disabled
                  @endif
                  class="btn btn-default bg-white btn-flat btn-modal"
                  data-href="{{ action('UnitController@create', ['quick_add' => true]) }}"
                  title="@lang('unit.add_unit')"
                  data-container=".view_modal">
                  <i class="fa fa-plus-circle text-primary fa-lg"></i>
                </button>
              </span>
            </div>
          </div>
        </div>

      </div>
      {{-- <div class="clearfix"></div> --}}
      <div id="divExcludeService2">
        <div
          class="col-lg-4 col-sm-6 @if(!empty($duplicate_product) && $duplicate_product->enable_stock == 0) hide @endif"
          id="alert_quantity_div">
          <div class="form-group" style="display: none;">
            <label>
              {!! Form::checkbox('enable_stock', 1, !empty($duplicate_product) ? $duplicate_product->enable_stock :
              true, ['class' => 'input-icheck', 'id' => 'enable_stock']); !!}
              <strong>@lang('product.manage_stock')</strong>
            </label>@show_tooltip(__('tooltip.enable_stock')) <p
              class="help-block"><i>@lang('product.enable_stock_help')</i></p>
          </div>
          <div class="form-group">
            {!! Form::label('alert_quantity', __('product.alert_quantity') . ':') !!}<span class="text-danger"> <strong>*</strong></span>
            @show_tooltip(__('tooltip.alert_quantity'))
            {!! Form::number('alert_quantity', !empty($duplicate_product->alert_quantity) ?
            $duplicate_product->alert_quantity : 0 , ['id' => 'alert_quantity', 'class' => 'form-control input_number',
            'required',
            'placeholder' => __('product.alert_quantity'), 'min' => '0']); !!}
          </div>
        </div>
        <div class="form-group col-lg-4 col-sm-6 my-auto">
          <label>
            {!! Form::checkbox('check_dai', 1, false, ['class' => 'input-icheck', 'id' => 'check_dai']); !!}
            <strong>@lang('product.dai')</strong>
          </label>
          @show_tooltip(__('tooltip.check_dai'))
          <p class="help-block"><i>@lang('product.check_dai_help')</i></p>
        </div>
      </div>
      {{-- <div class="clearfix"></div> --}}
    </div>
    <div class="row">
      <div class="form-group col-lg-8 col-sm-12">
        <div class="form-group py-2">
          {!! Form::label('product_description', __('lang_v1.product_description') . ':') !!}
          {!! Form::textarea('product_description', !empty($duplicate_product->product_description) ?
          $duplicate_product->product_description : null, ['id' => 'product_description' ,'class' => 'form-control']);
          !!}
        </div>
        <div class="row form-group">
          <div class="col-lg-3 col-sm-3 align-middle">
            <br/>
            <label for="has_warranty">
              {!! Form::checkbox('has_warranty', '0', false, ['id' => 'has_warranty']) !!}
              <strong>@lang('quote.has_warranty')</strong>
            </label>
          </div>
          <div class="col-lg-4 col-sm-3" id="hasW" style="display: none">
            {!! Form::label(__('quote.warranty') . ':') !!}
            <input type="text" name="warranty" id="warranty" class="form-control" placeholder="@lang('quote.warranty')">
          </div>
        </div>
      </div>
      <div class="form-group col-lg-4 col-sm-12">
        {!! Form::label('image', __('lang_v1.product_image') . ':') !!}
        {!! Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); !!}
        <p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit')
          / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p>
      </div>
    </div>
  </div>
</div>

<div class="boxform_u box-solid_u" id="divExcludeService4" style="display: none;">
  <div class="box-body">
      <div class="row">
        <div class="col-lg-6 col-sm-12">
          <div class="form-group">
            {!! Form::label('kit_children', __('product.clasification_product') . ':') !!}<span class="text-danger"> <strong>*</strong></span>
            {!! Form::select('kit_children', [], null, ['style' => 'width: 100%', 'class' => 'form-control',
              'placeholder' => __('messages.please_select'), 'id' => 'kit_children']); !!}
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <div class="table-responsive">
            <table id="kitTable" class="table" width="100%">
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
            {!! Form::label('expiry_period', __('product.expires_in') . ':') !!}<span class="text-danger"> <strong>*</strong></span><br>
            {!! Form::text('expiry_period', !empty($duplicate_product->expiry_period) ?
            @num_format($duplicate_product->expiry_period) : $expiry_period, ['class' => 'form-control pull-left
            input_number',
            'placeholder' => __('product.expiry_period'), 'style' => 'width:60%;']); !!}
            {!! Form::select('expiry_period_type', ['months'=>__('product.months'), 'days'=>__('product.days'), ''
            =>__('product.not_applicable') ], !empty($duplicate_product->expiry_period_type) ?
            $duplicate_product->expiry_period_type : 'months', ['style' => 'width: 100%', 'class' => 'form-control
            select2 pull-left', 'style' => 'width:40%;', 'id' => 'expiry_period_type']); !!}
          </div>
        </div>
      </div>
      @endif
      <div class="col-lg-6 col-sm-5">
        <div class="form-group">
          <br/>
          <label>
            {!! Form::checkbox('enable_sr_no', 1, !(empty($duplicate_product)) ? $duplicate_product->enable_sr_no :
            false, ['id' => 'imei','class' => 'input-icheck']); !!}
            <strong>@lang('lang_v1.enable_imei_or_sr_no')</strong>
          </label>@show_tooltip(__('lang_v1.tooltip_sr_no'))
        </div>
      </div>

      {{-- <div class="clearfix"></div> --}}

      <!-- Rack, Row & position number -->
      @if(session('business.enable_racks') || session('business.enable_row') || session('business.enable_position'))
      <div class="col-lg-12 col-sm-12">
        <h4>@lang('lang_v1.rack_details'):
          @show_tooltip(__('lang_v1.tooltip_rack_details'))
        </h4>
      </div>
      @foreach($business_locations as $id => $location)
      <div class="col-lg-3 col-sm-6">
        <div class="form-group">
          {!! Form::label('rack_' . $id, $location . ':') !!}

          @if(session('business.enable_racks'))
          {!! Form::text('product_racks[' . $id . '][rack]', !empty($rack_details[$id]['rack']) ?
          $rack_details[$id]['rack'] : null, ['class' => 'form-control', 'id' => 'rack_' . $id,
          'placeholder' => __('lang_v1.rack')]); !!}
          @endif

          @if(session('business.enable_row'))
          {!! Form::text('product_racks[' . $id . '][row]', !empty($rack_details[$id]['row']) ?
          $rack_details[$id]['row'] : null, ['class' => 'form-control', 'placeholder' => __('lang_v1.row')]); !!}
          @endif

          @if(session('business.enable_position'))
          {!! Form::text('product_racks[' . $id . '][position]', !empty($rack_details[$id]['position']) ?
          $rack_details[$id]['position'] : null, ['class' => 'form-control', 'placeholder' =>
          __('lang_v1.position')]); !!}
          @endif
        </div>
      </div>
      @endforeach
      @endif
      <div class="col-lg-3 col-sm-6">
        <div class="form-group">
          {!! Form::label('weight', __('lang_v1.weight') . ':') !!}
          {!! Form::text('weight', null, ['id' => 'weight', 'class' => 'form-control input_number', 'placeholder' =>
          __('lang_v1.weight')]); !!}
        </div>
      </div>
      <div class="col-lg-3 col-sm-6">
        <div class="form-group">
          {!! Form::label('volume', __('product.volume') . ':') !!}
          {!! Form::text('volume', null, ['id' => 'volume', 'class' => 'form-control input_number', 'placeholder' =>
          __('product.volume')]); !!}
        </div>
      </div>
      <div class="col-lg-3 col-sm-6">
        <div class="form-group">
          {!! Form::label('download_time', __('product.download_time') . ':') !!}
          {!! Form::text('download_time', null, ['id' => 'download_time', 'class' => 'form-control', 'placeholder' =>
          'hh:mm:ss']); !!}
        </div>
      </div>
      {{-- <div class="clearfix"></div> --}}
    </div>
  </div>
</div>
<div class="boxform_u box-solid_u">
  <div class="box-body">
    <div class="row">
      <div class="col-lg-5 col-sm-6 @if(!session('business.enable_price_tax')) hide @endif">
        <div class="form-group">
          {!! Form::label('tax_type', __('product.selling_price_tax_type') . ':') !!}<span class="text-danger"> <strong>*</strong></span>
          {!! Form::select('tax_type', ['inclusive' => __('product.inclusive'), 'exclusive' =>
          __('product.exclusive')], !empty($duplicate_product->tax_type) ? $duplicate_product->tax_type : 'exclusive',
          ['style' => 'width: 100%', 'class' => 'form-control select2', 'required']); !!}
        </div>
      </div>

      <div class="col-lg-4 col-sm-6 @if(!session('business.enable_price_tax')) hide @endif">
        <div class="form-group">
          {!! Form::label('tax', __('product.applicable_tax') . ':') !!}<span class="text-danger"> <strong>*</strong></span>
          {!! Form::select(
          'tax',
          $taxes,
          ! empty($duplicate_product->tax) ? $duplicate_product->tax : $default_products_tax,
          [
          'style' => 'width: 100%',
          'placeholder' => __('messages.please_select'),
          'class' => 'form-control select2'
          ]
          ); !!}
          {!! Form::hidden('tax_percent', null, ['id' => 'tax_percent']) !!}
        </div>
      </div>
      <div class="col-lg-3 col-sm-6" id="div_type">
        <div class="form-group">
          {!! Form::label('type', __('product.product_type') . ':') !!}<span class="text-danger"> <strong>*</strong></span> @show_tooltip(__('tooltip.product_type'))
          {!! Form::select('type', ['single' => 'Single', 'variable' => 'Variable'], !empty($duplicate_product->type)
          ? $duplicate_product->type : null, ['style' => 'width: 100%', 'class' => 'form-control select2',
          'required', 'data-action' => !empty($duplicate_product) ? 'duplicate' : 'add', 'data-product_id' =>
          !empty($duplicate_product) ? $duplicate_product->id : '0']); !!}
        </div>
      </div>
      {{-- <div class="clearfix"></div> --}}
      <div class="form-group col-sm-12" id="product_form_part"></div>

      <input type="hidden" id="variation_counter" value="1">
      <input type="hidden" id="default_profit_percent" value="{{ $default_profit_percent }}">

    </div>

    
    <div class="row">
      <div class="col-sm-12 text-right">
        <input type="hidden" name="submit_type" id="submit_type">
        <div class="btn-group">
          <div class="btn-group dropleft" role="group">
            <button type="submit" value="submit" class="btn btn-primary submit_product_form">@lang('messages.save')</button>
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-sort-desc"></i>
            <span class="sr-only">Toggle Dropdown</span>
            </button>
            <ul class="btn-primary dropdown-menu dropdown-menu-right" role="menu">
              <li>
                @if($selling_price_group_count)
                <a href="#" id="submit_n_add_selling_prices" type="submit" value="submit_n_add_selling_prices"
                  class="submit_product_form">
                  @lang('lang_v1.save_n_add_selling_price_group_prices')
                </a>
                @endif
              </li>
              <li>
                <a href="#" id="opening_stock_button" @if(!empty($duplicate_product) && $duplicate_product->enable_stock == 0)
                  disabled @endif type="submit" value="submit_n_add_opening_stock" class="submit_product_form">
                  @lang('lang_v1.save_n_add_opening_stock')
                </a>
              </li>
              <li>
                <a href="#" type="submit" value="save_n_add_another" class="submit_product_form">
                  @lang('lang_v1.save_n_add_another')
                </a>
              </li>
            </ul>
          </div>
        </div>
        <button type="button" id="cancel_product" class="btn btn-danger">@lang('messages.cancel')</button>
      </div>
    </div>
  </div>
  
</div>
{!! Form::close() !!}