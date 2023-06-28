<div class="pos-tab-content">
	<div class="">
		<div class="box-header">
			<h3 class="box-title">@lang( 'rrhh.all_your_types_contracts' )</h3>
			<div class="box-tools">
				@can('rrhh_catalogues.create')
				<button type="button" class="btn btn-primary" id="add_types_contracts" value='14'><i class="fa fa-plus"></i> @lang( 'messages.add' )
				</button>
				@endcan
			</div>
		</div>
		<div class="box-body">
			<div class="table-responsive">
				<table class="table table-striped table-bordered table-condensed table-hover" id="types_contracts-table" width="100%">
					<thead>
						<th>@lang('rrhh.name')</th>
						<th>@lang('rrhh.status')</th>
						<th>@lang('rrhh.actions' )</th>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>