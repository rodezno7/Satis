<div class="modal-dialog modal-lg" role="dialog">
  <div class="modal-content" style="border-radius: 10px;">
    {!! Form::open(['id' => 'follow_customer_edit_form']) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang('crm.edit_follow')</h4>
    </div>
    <div class="modal-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('name', __('crm.contact_type') . ' : ') !!}

            <select name="eecontact_type" id="eecontact_type" class="select2 required" style="width: 100%">
              <option value='entrante'>@lang('crm.option_in')</option>
              <option value='saliente'>@lang('crm.option_out')</option>
              <option value='no_aplica'>@lang('crm.option_none')</option>
            </select>

            <input type="hidden" name="follow_customer_id" id="follow_customer_id">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('name', __('crm.contactreason') . ' : ') !!}

            {!! Form::select('eecontact_reason_id', $contactreason, '',
                ['class' => 'select2', 'required', 'id' => 'eecontact_reason_id', 'style' => 'width: 100%;']) !!}
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('econtact_mode', __('crm.conctact_mode')) !!}

            <div class="wrap-inputform">
              {!! Form::select('econtact_mode_id', $contactmode, '',
                ['class' => 'inputform2 select2', 'id' => 'econtact_mode_id', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]) !!}
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('eproduct_cat_id', __('crm.interest') . ' : ') !!}

            {!! Form::select('eeproduct_cat_id', $categories, '',
                ['class' => 'select2', 'id' => 'eeproduct_cat_id', 'style' => 'width: 100%;']) !!}
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="form-check-input">
              {{ __('crm.not_found') }} {!! Form::checkbox('eechk_not_found', '1', true,
                ['id' => 'eechk_not_found', 'onClick' => 'eeshowNotFoundDesc()']) !!}
            </label>

            {!! Form::textarea('eeproducts_not_found_desc', null,
                ['class' => 'form-control', 'id' => 'eeproducts_not_found_desc', 'style' => 'display: none;']) !!}
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label>@lang('crm.notes')</label>
            {!! Form::textarea('eenotes', null,
                ['class' => 'form-control', 'id' => 'eenotes', 'rows' => 2]) !!}
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="form-check-input">
              {{ __('crm.not_stock') }}
              {!! Form::checkbox('eechk_not_stock', '1', false, ['id' => 'eechk_not_stock',
                'onClick' => 'eshowNotStockDesc()']) !!}
            </label>
          </div>
        </div>

        <div class="col-md-6">
          <label>@lang('crm.date')</label>
          <div class="wrap-inputform">
            <span class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </span>

            <input type="date" id="edate" name="edate" class="inputform2"
              value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
          </div>
        </div>
      </div>

      <div class="row" id="eediv_products" style="display: none;">
        <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
          <div class="form-group">
            <label>@lang('accounting.location')</label>
            <select name="eelocations" id="eelocations" class="select2" style="width: 100%">
              <option value="0" disabled selected>@lang('messages.please_select')</option>
              @foreach ($locations as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="col-sm-3 col-md-3 col-lg-3 col-xs-12">
          <div class="form-group">
            <label>@lang('product.products')</label>
            <select name="eeproducts" id="eeproducts" class="inputform2 select2" style="width: 100%">
              <option value="0">@lang('messages.please_select')</option>
              @foreach ($products as $item)
                @if ($item->sku != $item->sub_sku)
                  <option value="{{ $item->id }}">{{ $item->name_product }} {{ $item->name_variation }}</option>
                @else
                  <option value="{{ $item->id }}">{{ $item->name_product }}</option>
                @endif
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-group col-sm-12 col-md-12 col-lg-12 col-xs-12">
          <table class="table">
            <thead>
              <th>Op</th>
              <th>@lang('product.name')</th>
              <th>@lang('product.sku')</th>
              <th>@lang('product.actual_stock')</th>
              <th>@lang('product.required_quantity')</th>
            </thead>
            <tbody id="eelist">
            </tbody>
          </table>
        </div>
      </div>
    </div>
    
    <div class="modal-footer">
      <button type="button" id="btn-edit-follow-customer" class="btn btn-primary">@lang('messages.save')</button>
      <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-default"
        id="btn-close-modal-edit-follow">@lang('messages.close')</button>
    </div>
    {!! Form::close() !!}
  </div>
</div>
