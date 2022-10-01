<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('PaymentCommitmentController@update', $pc->id),
            'method' => 'put', 'id' => 'payment_commitment_edit_form']) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('contact.edit_payment_commitment')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group">
                            {!! Form::label("reference", __("lang_v1.reference")) !!}
                            {!! Form::text("code", $pc->reference, ["class" => "form-control", "required", "placeholder" => __("lang_v1.reference")]) !!}
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            {!! Form::label("date", __("lang_v1.date")) !!}
                            {!! Form::text("date", @format_date($pc->date), ["class" => "form-control date", "required", "placeholder" => __("lang_v1.date")]) !!}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label("supplier", __("contact.supplier") . ":") !!}
                            {!! Form::select('supplier_id', $supplier, $pc->supplier_id, ['class' => 'form-control',
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
                                {!! Form::select("location_id", $locations, $pc->location_id, ["class" => "form-control", "required", "id" => "location_id", "placeholder" => __("business.location")]) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label("type", __("contact.type")) !!}
                            {!! Form::select("type_old", ["automatic" => __("contact.automatic"), "manual" => __("contact.manual")], $pc->type, ["class" => "form-control", "id" => "type", "disabled"]) !!}
                            {!! Form::hidden("type", $pc->type) !!}
                        </div>
                    </div>
                    <div class="col-sm-4 automatic" @if($pc->type == 'manual') style="display: none;" @endif>
                        <div class="form-group" style="margin-top: 25px;">
                            {!! Form::select("add_purchase", [], null, ["class" => "form-control", "id" => "add_purchase", "placeholder" => __('contact.search')]) !!}
                        </div>
                    </div>
                    <div class="col-sm-4 manual" @if($pc->type == 'automatic') style="display: none;" @endif>
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
                            @if ($pc->type == "manual")
                                @foreach ($pc->payment_commitment_lines as $pcl)
                                    <tr>
                                        <td>
                                            {!! Form::text("payment_commitment_lines[". $loop->index ."][doc_type]", $pcl->document_name,
                                                ["class" => "form-control input-sm", "data-name" => "doc_type"]) !!}
                                            {!! Form::hidden("payment_commitment_lines[". $loop->index ."][pcl_id]", $pcl->id, ["data-name" => "pcl_id"]) !!}
                                        </td>
                                        <td>
                                            {!! Form::text("payment_commitment_lines[". $loop->index ."][ref_no]", $pcl->reference, ["class" => "form-control input-sm", "data-name" => "ref_no"]) !!}
                                        </td>
                                        <td>
                                            {!! Form::text("payment_commitment_lines[". $loop->index ."][amount]", $pcl->total, ["class" => "form-control input-sm row_amount", "data-name" => "amount"]) !!}
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <button type="button" class="btn btn-danger btn-xs btn-delete-row"><i class="fa fa-times" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                @foreach ($pc->payment_commitment_lines as $pcl)
                                    <tr>
                                        <td>
                                            {{ $pcl->document_name }}
                                            {!! Form::hidden("payment_commitment_lines[". $loop->index ."][doc_type]", $pcl->document_name, ["data-name" => "doc_type"]) !!}
                                            {!! Form::hidden("payment_commitment_lines[". $loop->index ."][pcl_id]", $pcl->id, ["data-name" => "pcl_id"]) !!}
                                            {!! Form::hidden("payment_commitment_lines[". $loop->index ."][transaction_id]", $pcl->transaction_id, ["data-name" => "transaction_id"]) !!}
                                        </td>
                                        <td>
                                            {{ $pcl->reference }}
                                            {!! Form::hidden("payment_commitment_lines[". $loop->index ."][ref_no]", $pcl->reference, ["data-name" => "ref_no"]) !!}
                                        </td>
                                        <td>
                                            <span class="display_currency" data-currency_symbol="true">{{ $pcl->total }}</span>
                                            {!! Form::hidden("payment_commitment_lines[". $loop->index ."][amount]", $pcl->total, ["class" => "row_amount", "data-name" => "amount"]) !!}
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <button type="button" class="btn btn-danger btn-xs btn-delete-row"><i class="fa fa-times" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3 col-sm-offset-9">
                    <div class="form-group">
                        {!! Form::label("total", __("purchase.total")) !!}
                        <span class="display_currency" data-currency_symbol="true" id="total_amount_text">$ {{ round($pc->total, 2) }}</span>
                        {!! Form::hidden("total_amount", $pc->total, ["id" => "total_amount"]) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" @if($pc->is_annulled) disabled="true" @endif>@lang( 'messages.update' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>
        {!! Form::close() !!}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->