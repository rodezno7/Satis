{{-- Stock transfer related settings --}}
<div class="pos-tab-content">
    <div class="row">
        {{-- Enable remission note --}}
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                    {!! Form::checkbox('enable_remission_note', 1, $business->enable_remission_note,
                        ['class' => 'input-icheck', 'id' => 'enable_remission_note']); !!}
                    {{ __( 'lang_v1.enable_remission_note' ) }}
                    </label>
                    @show_tooltip(__('lang_v1.tooltip_enable_remission_note'))
                </div>
            </div>
        </div>
    </div>
</div>