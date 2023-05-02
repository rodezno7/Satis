<!-- Edit discount Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="posEditExportExpenseModal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">@lang('sale.edit_export_expenses')</h4>
			</div>
			<div class="modal-body">
				<div class="row">
				    <div class="col-md-4">
				        <div class="form-group">
                            <label for="fob_amount">@lang("sale.fob")</label>
                            <div class="input-group">
				                <span class="input-group-addon">
				                    <i class="fa">$</i>
				                </span>
                                <input type="text"
                                    class="form-control input_number"
                                    name="fob_amount"
                                    id="fob_amount"
                                    value="0.00">
                            </div>
                        </div>
				    </div>
				    <div class="col-md-4">
				        <div class="form-group">
                            <label for="freight_amount">@lang('sale.freight')</label>
                            <div class="input-group">
				                <span class="input-group-addon">
				                    <i class="fa">$</i>
				                </span>
                                <input type="text"
                                    class="form-control input_number"
                                    name="freight_amount"
                                    id="freight_amount"
                                    value="0.00">
                            </div>
                        </div>
				    </div>
				    <div class="col-md-4">
				        <div class="form-group">
                            <label for="insurance_amount">@lang('sale.insurance')</label>
                            <div class="input-group">
				                <span class="input-group-addon">
				                    <i class="fa">$</i>
				                </span>
                                <input type="text"
                                    class="form-control input_number"
                                    name="insurance_amount"
                                    id="insurance_amount"
                                    value="0.00">
                            </div>
                        </div>
				    </div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->