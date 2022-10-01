<div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('FixedAssetTypeController@update', [$fa_type->id]),
            'method' => 'PUT', 'id' => 'fixed_asset_type_edit_form']) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('fixed_asset.edit_fixed_asset_type')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label("name", __("fixed_asset.name")) !!}
                            <div class="input-group">
                            <span class="input-group-addon">
                                    <i class="fa fa-pencil"></i>
                                </span>
                                {!! Form::text("name", $fa_type->name, ["class" => "form-control", "required", "placeholder" => __("fixed_asset.name")]) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label("description", __("fixed_asset.description")) !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-pencil"></i>
                                </span>
                                {!! Form::text("description", $fa_type->description, ["class" => "form-control", "placeholder" => __("fixed_asset.description")]) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label("percentage", __("fixed_asset.percentage")) !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-percent"></i>
                                </span>
                                {!! Form::text("percentage", $fa_type->percentage, ["class" => "form-control input_number", "required", "placeholder" => __("fixed_asset.percentage")]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group">
                            {!! Form::label("accounting_account", __("accounting.accounting_account")) !!}
                            {!! Form::select("accounting_account_id", $accounting_account, $fa_type->accounting_account_id, ["class" => "form-control select-account", "required", "placeholder" => __("accounting.accounting_account")]) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>
        {!! Form::close() !!}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->