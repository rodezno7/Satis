<div class="modal-dialog modal-lg" role="dialog">
  <div class="modal-content" style="border-radius: 10px;">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">@lang('crm.follow'): <span id="lbl_customer"></span></h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('name', __('crm.contact_type') . ' : ') !!}
            <span id="contact_type_lbl"></span>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('name', __('crm.contactreason') . ' : ') !!}
            <span id="contact_reason_lbl"></span>
          </div>
        </div>

      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('econtact_mode', __('crm.conctact_mode')) !!}
            <span id="contact_mode_lbl"></span>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('eproduct_cat_id', __('crm.interest') . ' : ') !!}
            <span id="interest_lbl"></span>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label>@lang('crm.notes')</label>
            <span id="notes_lbl"></span>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label>@lang('crm.date')</label>
            <span id="date_lbl"></span>
          </div>
        </div>
      </div>

      <div class="row" id="eeediv_products" style="display: none;">
        <div class="form-group col-sm-12 col-md-12 col-lg-12 col-xs-12">
          <h4 class="modal-title">@lang('crm.not_stock')</h4>
          <table class="table">
            <thead>
              <th>@lang('product.name')</th>
              <th>@lang('product.sku')</th>
              <th>@lang('product.actual_stock')</th>
              <th>@lang('product.required_quantity')</th>
            </thead>
            <tbody id="eeelist">
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="button" data-dismiss="modal" aria-label="Close"
        class="btn btn-default">@lang('messages.close')</button>
    </div>
  </div>
</div>
