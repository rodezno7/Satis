<div class="pos-tab-content">
	<div class="boxform_u box-solid_u">
		<div class="box-header">
			<h3 class="box-title">@lang( 'rrhh.all_your_afps' )</h3>
			<div class="box-tools">
				@can('rrhh_catalogues.create')
				<button type="button" class="btn btn-primary" id="add_afp" value='4'><i class="fa fa-plus"></i> @lang( 'messages.add' )
				</button>
				@endcan
			</div>
		</div>
		<div class="box-body">
			<div class="table-responsive">
				<table class="table table-striped table-bordered table-condensed table-hover" id="afps-table" width="100%">
					<thead>
						<th>@lang('rrhh.code')</th>
						<th>@lang('rrhh.short_name')</th>
						<th>@lang('rrhh.name')</th>
						<th>@lang('rrhh.status')</th>
						<th>@lang('rrhh.actions' )</th>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>