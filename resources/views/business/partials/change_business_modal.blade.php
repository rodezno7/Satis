<div class="modal-dialog modal-md" role="document">
	<div class="modal-content">
		{!! Form::open(['url' => action('BusinessController@changeBusiness'), 'method' => 'patch', 'id' => 'change_business_form']) !!}
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			<h4 class="modal-title">@lang("business.change_business")</h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group mb-3">
						<label for="business_id">@lang('business.business')</label>
						{!! Form::select('business_id', $business, '', [
									'class' => 'form-control',
									'placeholder' => __('messages.please_select'),
									'required']) !!}
					</div>
					<div class="form-group mb-3">
						<label class="form-label" for="username">@lang('lang_v1.password')</label>
						<input id="password" type="password" class="form-control" autocomplete="off" name="password" required>
					</div>
					<input type="hidden" id="username" name="username" value="{{Auth::user()->username}}">
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="submit" class="btn btn-primary">@lang("business.swap")</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.cancel')</button>
		</div>
		{!! Form::close() !!}
	</div>
</div>