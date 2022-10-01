<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('PaymentCommitmentController@store'),
            'method' => 'post', 'id' => 'payment_commitment_add_form']) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('contact.add_payment_commitment')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group">
                            {!! Form::label("reference", __("lang_v1.reference")) !!}
                            {!! Form::text("code", null, ["class" => "form-control", "required", "placeholder" => __("lang_v1.reference")]) !!}
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            {!! Form::label("date", __("lang_v1.date")) !!}
                            {!! Form::text("date", @format_date('now'), ["class" => "form-control date", "required", "placeholder" => __("lang_v1.date")]) !!}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label("supplier", __("contact.supplier") . ":") !!}
                            {!! Form::select('supplier_id', [], null, ['class' => 'form-control',
                                'placeholder' => __('messages.please_select'), 'id' => 'supplier', "style" => "width: 100%;"]) !!}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label("location", __("business.location")) !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-thumb-tack"></i>
                                </span>
                                {!! Form::select("location_id", $locations, null, ["class" => "form-control", "required", "id" => "location_id", "placeholder" => __("business.location")]) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label("type", __("contact.type")) !!}
                            {!! Form::select("type", ["automatic" => __("contact.automatic"), "manual" => __("contact.manual")], "automatic", ["class" => "form-control", "id" => "type"]) !!}
                        </div>
                    </div>
                    <div class="col-sm-4 automatic">
                        <div class="form-group" style="margin-top: 25px;">
                            {!! Form::select("add_purchase", [], null, ["class" => "form-control", "id" => "add_purchase", "placeholder" => __('contact.search')]) !!}
                        </div>
                    </div>
                    <div class="col-sm-4 manual" style="display: none;">
                        <div class="form-group" style="margin-top: 25px;">
                            <button class="btn btn-primary" id="add_manual"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="payment_commitments">
                        <thead>
                            <tr>
                                <th style="text-align: center;">@lang('document_type.document_type')</th>
                                <th style="text-align: center;">@lang('lang_v1.reference')</th>
                                <th style="text-align: center;">@lang('lang_v1.amount')</th>
                                <th style="width: 10%; text-align: center;">@lang('messages.action')</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3 col-sm-offset-9">
                    <div class="form-group">
                        {!! Form::label("total", __("purchase.total")) !!}
                        <span class="display_currency" data-currency_symbol="true" id="total_amount_text">$ 0.00</span>
                        {!! Form::hidden("total_amount", null, ["id" => "total_amount"]) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>
        {!! Form::close() !!}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->