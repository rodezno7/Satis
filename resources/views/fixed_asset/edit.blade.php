<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('FixedAssetController@update', [$fixed_asset->id]),
            'method' => 'put', 'id' => 'fixed_asset_edit_form']) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('fixed_asset.edit_fixed_asset')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label("code", __("fixed_asset.code")) !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa">#</i>
                                </span>
                                {!! Form::text("code", $fixed_asset->code, ["class" => "form-control", "readonly", "placeholder" => __("fixed_asset.code")]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="form-group">
                            {!! Form::label("name", __("fixed_asset.name")) !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-pencil"></i>
                                </span>
                                {!! Form::text("name", $fixed_asset->name, ["class" => "form-control", "required", "placeholder" => __("fixed_asset.name")]) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label("fixed_asset_type", __("fixed_asset.fixed_asset_type")) !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-cubes"></i>
                                </span>
                                {!! Form::select("fixed_asset_type_id", $fixed_asset_types, $fixed_asset->fixed_asset_type_id, ["class" => "form-control select2", "required", "placeholder" => __("fixed_asset.fixed_asset_type"), 'style' => 'width: 100%;']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group">
                            {!! Form::label("description", __("fixed_asset.description")) !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-pencil"></i>
                                </span>
                                {!! Form::text("description", $fixed_asset->description, ["class" => "form-control", "required", "placeholder" => __("fixed_asset.description")]) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label("location", __("business.location")) !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-thumb-tack"></i>
                                </span>
                                {!! Form::select("location_id", $locations, $fixed_asset->location_id, ["class" => "form-control", "required", "placeholder" => __("business.location")]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label("brand", __("brand.brand")) !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-ravelry"></i>
                                </span>
                                {!! Form::select("brand_id", $brands, $fixed_asset->brand_id, ["class" => "form-control select2", "placeholder" => __("brand.brand"), 'style' => 'width:100%;']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label("model", __("fixed_asset.model")) !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-cube"></i>
                                </span>
                                {!! Form::text("model", $fixed_asset->model, ["class" => "form-control", "placeholder" => __("fixed_asset.model")]) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label("type", __("fixed_asset.type")) !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-car"></i>
                                </span>
                                {!! Form::select("type", ["new" => __("fixed_asset.new"), "used" => __("fixed_asset.used")],
                                    $fixed_asset->type, ["class" => "form-control", "required", "placeholder" => __("fixed_asset.type")]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label("year", __("lang_v1.year")) !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {!! Form::text("year", $fixed_asset->year, ["class" => "form-control input_number", "required", "placeholder" => __("lang_v1.year")]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label("initial_value", __("fixed_asset.initial_value")) !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-money"></i>
                                </span>
                                {!! Form::text("initial_value", $fixed_asset->initial_value, ["class" => "form-control input_number", "required", "placeholder" => __("fixed_asset.initial_value")]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label("current_value", __("fixed_asset.current_value")) !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-money"></i>
                                </span>
                                {!! Form::text("current_value", $fixed_asset->current_value, ["class" => "form-control input_number", "required", "placeholder" => __("fixed_asset.current_value")]) !!}
                            </div>
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