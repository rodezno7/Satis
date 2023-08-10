<div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('rrhh.personnel_actions'): <span style="color: gray">{{ $employee->first_name }} {{ $employee->last_name }}</span></h4> 
        </div>
        <div class="modal-body">
            <div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="form-group">
						@can('rrhh_personnel_action.create')
							<button type="button" class="btn btn-info btm-sm" id='btn_add_personnel_actions' onclick="btnAddPersonnelActions()"
							style="padding: 5px 8px; margin-right: 5px; margin-top: -2px;">
							<i class="fa fa-plus"></i>
							{{ __('rrhh.add') }}
							</button>
						@endcan
					</div>
					@include('rrhh.personnel_actions.table')
				</div>				
			</div>
		</div>
	</div>
</div>