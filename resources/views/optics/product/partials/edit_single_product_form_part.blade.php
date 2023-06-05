@if(!session('business.enable_price_tax')) 
  @php
    $default = 0;
    $class = 'hide';
  @endphp
@else
  @php
    $default = null;
    $class = '';
  @endphp
@endif

@php
  $price_precision = config('app.price_precision');
@endphp

<div class="col-sm-12"><br>
    <div class="table-responsive">
    <table class="table table-bordered add-product-price-table table-condensed {{$class}}">
        <tr>
          <th>@lang('product.default_purchase_price')</th>
          <th>@lang('product.profit_percent') @show_tooltip(__('tooltip.profit_percent'))</th>
          <th>@lang('product.default_selling_price')</th>
        </tr>
        @foreach($product_deatails->variations as $variation )
            @if($loop->first)
                <tr>
                    <td>
                        <input type="hidden" name="single_variation_id" value="{{$variation->id}}">

                        <div class="col-sm-6">
                          {!! Form::label('single_dpp', trans('product.exc_of_tax') . ':') !!}<span class="text-danger"> <strong>*</strong></span>

                          {!! Form::text('single_dpp', number_format($variation->default_purchase_price, $price_precision), ['class' => 'form-control input-sm dpp input_number', 'placeholder' => 'Excluding Tax', 'required']); !!}
                        </div>

                        <div class="col-sm-6">
                          {!! Form::label('single_dpp_inc_tax', trans('product.inc_of_tax') . ':') !!}<span class="text-danger"> <strong>*</strong></span>
                        
                          {!! Form::text('single_dpp_inc_tax', number_format($variation->dpp_inc_tax, $price_precision), ['class' => 'form-control input-sm dpp_inc_tax input_number', 'placeholder' => 'Including Tax', 'required']); !!}
                        </div>
                    </td>

                    <td>
                        <br/>
                        {!! Form::text('profit_percent', number_format($variation->profit_percent, $price_precision), ['class' => 'form-control input-sm input_number', 'id' => 'profit_percent', 'required']); !!}
                    </td>

                    <td>
                        <label><span class="dsp_label"></span></label>
                        {!! Form::text('single_dsp', number_format($variation->default_sell_price, $price_precision), ['class' => 'form-control input-sm dsp input_number', 'placeholder' => 'Excluding tax', 'id' => 'single_dsp', 'required']); !!}

                        {!! Form::text('single_dsp_inc_tax', number_format($variation->sell_price_inc_tax, $price_precision), ['class' => 'form-control input-sm hide input_number', 'placeholder' => 'Including tax', 'id' => 'single_dsp_inc_tax', 'required']); !!}
                    </td>
                </tr>
            @endif
        @endforeach
    </table>
    </div>
</div>