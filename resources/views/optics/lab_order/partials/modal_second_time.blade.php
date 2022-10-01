<div class="modal fade" id="modal_second_time" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">@lang('lab_order.order_a_second_time')</h4>
      </div>

      <div class="modal-body">
        <div class="row">
          {{-- employee_id --}}
          <div class="col-sm-12">
            <div class="form-group">
                {!! Form::label('employee_id', __('customer.employee') . ':') !!}
                {!! Form::select('eemployee_id', $employees, '', ['class' => 'form-control', 'id' => 'eemployee_id']) !!}
            </div>
          </div>

          {{-- reason --}}
          <div class="col-sm-12">
            <div class="form-group">
                {!! Form::label('reason', __('lab_order.reason') . ':') !!}
                {!! Form::textarea('ereason', '',
                  ['class' => 'form-control', 'placeholder' => __('lab_order.reason'), 'rows' => '3', 'id' => 'ereason']) !!}
            </div>
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
