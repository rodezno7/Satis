<div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        {!! Form::open(["url" => action("CostCenterController@store"), "method" => "post", "id" => "add_cost_center_form", "file" => false]) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">@lang('cost_center.add_cost_center')</h4 class="modal-title">
            </div>
            <div class="modal-body">
                <div class="col-12">
                    <div class="form-group">
                        {!! Form::label("name", __('accounting.name')) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-building-o"></i>
                            </span>
                            {!! Form::text("name", null, ["class" => "form-control",
                                "placeholder" => __('accounting.name'), "required"]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        {!! Form::label("description", __('accounting.description')) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-pencil"></i>
                            </span>
                            {!! Form::text("description", null, ["class" => "form-control", "placeholder" => __('accounting.description')]) !!}
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        {!! Form::label("location", __("business.location")) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                            {!! Form::select("location_id", $locations, null, ["class" => "form-control",
                                "placeholder" => __("messages.please_select"), "required"]) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        {!! Form::close() !!}
    </div>
</div>