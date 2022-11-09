<div class="pos-tab-content">
    <div class="row">
        {{-- expense_settings[hide_location_column] --}}
        <div class="col-sm-4">
            <div class="form-group">
                <div class="checkbox" style="margin-top: 24px;">
                    <label>
                        {!! Form::checkbox('expense_settings[hide_location_column]', 1, $expense_settings['hide_location_column'], [
                            'class' => 'input-icheck',
                        ]) !!}
                        {{ __('business.hide_location_column') }}
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
