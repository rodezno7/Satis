<!--Purchase related settings -->
<div class="pos-tab-content">

	<div class="row">
		<div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
			<label>@lang('accounting.shortcut')</label>
		</div>
		<div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
			<label>@lang('accounting.description')</label>
		</div>
		<div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
			<label>@lang('accounting.type')</label>
		</div>
	</div>

	@foreach($shortcuts as $item)

	<div class="row">
		<div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
			<input type="text" name="shortcut[]" class="form-control" value="{{ $item->shortcut }}" readonly>
			<input type="hidden" name="shortcut_id[]" value="{{ $item->id }}">
		</div>
		<div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
			@if($item->type == 'action')
			<input type="text" name="description[]" class="form-control" value="{{ $item->description }}" readonly>
			@else
			<input type="text" name="description[]" class="form-control" value="{{ $item->description }}">
			@endif
		</div>
		<div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
			@if($item->type == 'action')
			<input type="text" name="type[]" class="form-control" value="@lang('messages.action')" readonly>
			@else
			<input type="text" name="type[]" class="form-control" value="@lang('accounting.comment')" readonly>
			@endif
		</div>
	</div>



	@endforeach
	


</div>