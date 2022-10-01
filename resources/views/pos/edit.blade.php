<div class="modal-dialog modal-lg" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('PosController@update', [$pos->id]), 'method' => 'PUT', 'id' => 'pos_edit_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('card_pos.edit')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                {{-- name --}}
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('name', __('card_pos.name')) !!}
                        {!! Form::text('name', $pos->name, ['class' => 'form-control', 'required', 'placeholder' =>
                        __('payment.pos_name')]) !!}
                    </div>
                </div>

                {{-- employee_id --}}
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('employee_id', __('card_pos.employee')) !!}
                        {!! Form::select('employee_id', $employees,$pos->employee_id,['class' => 'form-control select2', 'style'=>'width:100%', 'required', 'placeholder' =>
                        __('messages.please_select')]) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- brand --}}
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('brand', __('card_pos.brand')) !!}
                        {!! Form::text('brand', $pos->brand, ['class' => 'form-control', 'placeholder' =>
                        __('card_pos.brand'), 'style'=>'width:100%']) !!}
                    </div>
                </div>

                {{-- model --}}
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('model', __('card_pos.model')) !!}
                        {!! Form::text('model', $pos->model, ['class' => 'form-control', 'placeholder' =>
                        __('card_pos.model')]) !!}
                    </div>
                </div>

                {{-- bank_account_id --}}
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('bank_account_id', __('card_pos.bank')) !!}
                        {!! Form::select('bank_account_id', $bank_accounts, $pos->bank_account_id,['class' => 'form-control select2',
                            'id'=> 'select_bank','style'=>'width:100%', 'required', 'placeholder' => __('messages.please_select')]) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- authorization_key --}}
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('authorization_key', __('card_pos.authorization_key')) !!}
                        {!! Form::password('authorization_key', [
                            'class' => 'form-control',
                            'placeholder' => __('card_pos.authorization_key'),
                            'minlength' => '4',
                            'maxlength' => '4'
                        ]) !!}
                    </div>
                </div>

                {{-- confirm_authorization_key --}}
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('confirm_authorization_key', __('card_pos.confirm_authorization_key')) !!}
                        {!! Form::password('confirm_authorization_key', [
                            'class' => 'form-control',
                            'placeholder' => __('card_pos.confirm_authorization_key'),
                            'minlength' => '4',
                            'maxlength' => '4'
                        ]) !!}
                    </div>
                </div>

                {{-- location_id --}}
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('location_id', __('card_pos.business')) !!}
                        {!! Form::select('location_id', $business_locations ,$pos->location_id, ['class' => 'form-control select2',
                            'required', 'placeholder' =>__('messages.please_select'), 'style'=>'width:100%']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- description --}}
                <div class="col-md-8">
                    <div class="form-group">
                        {!! Form::label('description', __('card_pos.description')) !!}
                        {!! Form::textarea('description', $pos->description, ['class' => 'form-control', 'placeholder' =>
                        __('card_pos.description'), 'rows' =>3, 'cols'=>2]) !!}
                    </div>
                </div>

                {{-- status --}}
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('status', __('card_pos.status')) !!}
                        {!! Form::select('status', $status ,$pos->status, ['class' => 'form-control select2',
                            'required', 'placeholder' =>__('messages.please_select'), 'style'=>'width:100%']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" id="btn-edit-payment_term" class="btn btn-primary">@lang('messages.save')</button>
            <button type="button" data-dismiss="modal" aria-label="Close" id="btn-close-modal-edit-payment_term"
                class="btn btn-default">@lang('messages.close')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script>
    $(document).ready(function(){
        $('select.select2').off().select2();
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    });
</script>