<div class="modal fade" id="modal_return_stock" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">@lang('lab_order.return_stock')</h4>
      </div>

      <div class="modal-body">
        <div class="row">
          {{-- employee_id --}}
          <div class="col-sm-12">
            <label>@lang('lab_order.return_materials_to_stock')</label>
            <br>
            <label class="radio-inline">
              {!! Form::radio('ereturn_stock', 1, null, ['class' => 'rs_rb']) !!} @lang('accounting.yes')
            </label>
            <label class="radio-inline">
              {!! Form::radio('ereturn_stock', 0, null, ['class' => 'rs_rb']) !!} @lang('accounting.not')
            </label>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" data-dismiss="modal" aria-label="Close"
          class="btn btn-default">@lang('messages.close')</button>
      </div>
    </div>
  </div>
</div>
