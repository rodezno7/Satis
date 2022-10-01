<!--Purchase related settings -->
<div class="pos-tab-content active">
	<div class="row">

		<div class="boxform_u box-solid_u">
			<div class="box-header">
				<h3 class="box-title">@lang( 'geography.all_your_countries' )</h3>
				<div class="box-tools">
					<button type="button" class="btn btn-primary" data-toggle='modal' data-target='#modal-add-country' data-backdrop="static" id="btn-new-country"><i class="fa fa-plus"></i> @lang('messages.add')
					</button>
				</div>
			</div>
			<div class="box-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-condensed table-hover" id="countries-table" width="100%">
						<thead>
							<th>@lang('geography.name')</th>
							<th>@lang('geography.short_name')</th>
							<th>@lang('geography.code')</th>
							<th>@lang('geography.flag')</th>
							<th>@lang('messages.actions')</th>
						</thead>
					</table>
				</div>
			</div>
		</div>

	</div>
</div>