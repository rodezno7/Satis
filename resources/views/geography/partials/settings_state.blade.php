<!--Purchase related settings -->
<div class="pos-tab-content">
	<div class="row">

		<div class="boxform_u box-solid_u">
			<div class="box-header">
				<h3 class="box-title">@lang( 'geography.all_your_states' )</h3>
				<div class="box-tools">
					<button type="button" class="btn btn-primary" data-toggle='modal' data-target='#modal-add-state' data-backdrop="static" id="btn-new-state"><i class="fa fa-plus"></i> @lang( 'messages.add' )
					</button>
				</div>
			</div>
			<div class="box-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-condensed table-hover" id="states-table" width="100%">
						<thead>
							<th>@lang('geography.name')</th>
							<th>@lang('geography.zip_code')</th>
							<th>@lang('geography.country')</th>
							<th>@lang('geography.zone')</th>
							<th>@lang( 'messages.actions' )</th>
						</thead>
					</table>
				</div>
			</div>
		</div>

	</div>
</div>