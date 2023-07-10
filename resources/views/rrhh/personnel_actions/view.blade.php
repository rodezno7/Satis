<div class="modal-header">
    <h4 class="modal-title" id="formModal">@lang('rrhh.personnel_action') 
		<button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
			<span aria-hidden="true">&times;</span>
		</button>
	</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <h5 class="text-center">{{ mb_strtoupper(__('rrhh.general_information')) }}</h5>
            <table class="table table-responsive table-bordered table-condensed table-text-center" style="font-size: inherit;">
                <tbody>
                    <tr>
                        <th>{{ __('rrhh.type_personnel_action') }}</th>
                        <td>{{ $personnelAction[0]->type }}</td>
                        <th width="15%">{{ __('rrhh.status') }}</th>
                        <td width="20%">
                            {{ $personnelAction[0]->status }}
                            @if ($personnelAction[0]->status == 'Autorizada')
                                {{ @format_date($personnelAction[0]->authorization_date) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th width="23%">{{ __('rrhh.requested_by') }}</th>
                        <td>{{ $personnelAction[0]->first_name }} {{ $personnelAction[0]->last_name }}</td>
                        <th width="15%">{{ __('rrhh.created_date') }}</th>
                        <td width="20%">{{ @format_date($personnelAction[0]->created_at) }}</td>
                    </tr>
                </tbody>
            </table>

            <h5 class="text-center">{{ mb_strtoupper($personnelAction[0]->type) }}</h5>
            <table class="table table-responsive table-bordered table-condensed table-text-center" style="font-size: inherit;">
                <tbody>
                    <tr>
                        <th width="23%">{{ __('rrhh.employee') }}</th>
                        <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                        <th width="15%">{{ __('rrhh.employee_status') }}</th>
                        <td width="20%">
                            @if ($employee->status == 1)
                            <span>Activo</span>
                            @else
                            <span>Inactivo</span>
                            @endif
                        </td>
                    </tr>
                    @foreach ($actions as $action)
                        @if ($action->rrhh_required_action_id == 2) {{-- Cambiar departamento --}}
                            <tr>
                                <th>{{ __('rrhh.previous_department') }}</th>
                                <td>{{ $position->previousDepartment->value }}</td>
                                <th>{{ __('rrhh.previous_position') }}</th>
                                <td>{{ $position->previousPosition1->value }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('rrhh.new_department') }}</th>
                                <td>{{ $position->newDepartment->value }}</td>
                                <th>{{ __('rrhh.new_position') }}</th>
                                <td>{{ $position->newPosition1->value }}</td>
                            </tr>
                        @endif
        
                        @if ($action->rrhh_required_action_id == 3) {{-- Cambiar salario --}}
                            <tr>
                                <th>{{ __('rrhh.previous_salary') }}</th>
                                <td colspan="3">
                                    @if ($business->currency_symbol_placement == 'after')
                                        {{ @num_format($salary->previous_salary) }} {{ $business->currency->symbol }}
                                    @else
                                        {{ $business->currency->symbol }} {{ @num_format($salary->previous_salary) }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('rrhh.new_salary') }}</th>
                                <td colspan="3">
                                    @if ($business->currency_symbol_placement == 'after')
                                        {{ @num_format($salary->new_salary) }} {{ $business->currency->symbol }}
                                    @else
                                        {{ $business->currency->symbol }} {{ @num_format($salary->new_salary) }}
                                    @endif
                                </td>
                            </tr>
                        @endif
        
                        @if ($action->rrhh_required_action_id == 4) {{-- Cambiar cuenta bancaria --}}
                            <tr>
                                <th>{{ __('rrhh.period') }}</th>
                                <td colspan="3">{{ @format_date($personnelAction[0]->start_date) }} - {{
                                    @format_date($personnelAction[0]->end_date) }}</td>
                            </tr>
                        @endif
        
                        @if ($action->rrhh_required_action_id == 5) {{-- Cambiar cuenta bancaria --}}
                            @if ($personnelAction[0]->status == 'No autorizada (En tramite)')
                            <tr>
                                <th>{{ __('rrhh.bank_account') }}</th>
                                <td colspan="3">{{ $personnelAction[0]->bank_account }}</td>
                            </tr>
                            @elseif ($personnelAction[0]->status == 'Autorizada')
                            <tr>
                                <th>{{ __('rrhh.bank_account') }}</th>
                                <td colspan="3">{{ $employee->bank_account }}</td>
                            </tr>
                            @endif
                        @endif
        
                        @if ($action->rrhh_required_action_id == 6) {{-- Cambiar forma de pago --}}
                            @if ($personnelAction[0]->status == 'No autorizada (En tramite)')
                            <tr>
                                <th>{{ __('rrhh.way_to_pay') }}</th>
                                <td colspan="3">{{ $payment[0]->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('rrhh.bank') }}</th>
                                <td>{{ $bank[0]->name }}</td>
                                <th>{{ __('rrhh.bank_account') }}</th>
                                <td>{{ $personnelAction[0]->bank_account }}</td>
                            </tr>
                            @elseif ($personnelAction[0]->status == 'Autorizada')
                            <tr>
                                <th>{{ __('rrhh.way_to_pay') }}</th>
                                <td colspan="3">{{ $employee->payment->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('rrhh.bank') }}</th>
                                <td>{{ $employee->bank->name }}</td>
                                <th>{{ __('rrhh.bank_account') }}</th>
                                <td>{{ $employee->bank_account }}</td>
                            </tr>
                            @endif
                        @endif
        
                        @if ($action->rrhh_required_action_id == 7) {{-- Seleccionar la fecha en que entra en vigor --}}
                        <tr>
                            <th>{{ __('rrhh.in_force_from') }}</th>
                            <td colspan="3">{{ @format_date($personnelAction[0]->effective_date) }}</td>
                        </tr>
                        @endif
                    @endforeach
                    <tr>
                        <th>{{ __('rrhh.description') }}</th>
                        <td colspan="3">{{ $personnelAction[0]->description }}</td>
                    </tr>
                </tbody>
            </table>

            <h5 class="text-center">{{ mb_strtoupper(__('rrhh.authorizations')) }}</h5>
            <table class="table table-responsive table-bordered table-condensed table-text-center" style="font-size: inherit;">
                <thead>
                    <tr>
                        <th>{{ __('rrhh.user') }}</th>
                        <th>{{ __('rrhh.authorization') }}</th>
                        <th>{{ __('rrhh.authorization_date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $i=>$user)
                    <tr>
                        <td>{{ $user->user->first_name }} {{ $user->user->last_name }}</td>
                        <td class="text-center">
                            @if ($user->authorized == 1)
                                Si
                            @else
                                Aún no
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($user->updated_at != null)
                                {{ @format_date($user->updated_at) }}
                            @else
                                ----
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    function closeModal(){
		$('#modal_action').modal({backdrop: 'static'});
		$('#modal_doc').modal( 'hide' ).data( 'bs.modal', null );
	}
</script>