<div class="pos-tab-content">
	<div class="">
		<div class="box-header">
			<h3 class="box-title">@lang( 'rrhh.all_salarial_constances' )</h3> @show_tooltip(__('rrhh.message_salarial_constances'))
			<div class="box-tools">
				@can('rrhh_catalogues.create')
                    <a href="{!!URL::to('/rrhh-catalogues/salarial-constance/create')!!}" type="button" class="btn btn-primary" id="btn_salarial_constance"><i
                        class="fa fa-plus"></i> @lang('messages.add')
                    </a>
                @endcan
			</div>
		</div>
		<div class="box-body">
			<div class="table-responsive">
				<table class="table table-striped table-bordered table-condensed table-hover" id="salarial_constances-table" width="100%">
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