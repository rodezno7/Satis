<div class="modal-header">
    <h4 class="modal-title" id="formModal">@lang('rrhh.income_discount') 
		<button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
			<span aria-hidden="true">&times;</span>
		</button>
	</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <table class="table table-responsive table-condensed table-text-center" style="font-size: inherit;">
                <tbody>
                    <tr>
                        <th width="15%">{{ __('rrhh.type') }}</th>
                        <td>
                            @if ($incomeDiscount->rrhhTypeIncomeDiscount->type == 1)
                                {{ __('rrhh.income') }}
                            @else
                                {{ __('rrhh.discount') }}  
                            @endif
                        </td>
                        <th width="20%">{{ __('rrhh.name') }}</th>
                        <td>{{ $incomeDiscount->rrhhTypeIncomeDiscount->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('rrhh.apply_in') }}</th>
                        <td>{{ $incomeDiscount->paymentPeriod->name }}</td>
                        <th>{{ __('rrhh.quota') }}</th>
                        <td>{{ $incomeDiscount->quota }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('rrhh.total_value') }}</th>
                        <td>
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($incomeDiscount->total_value) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($incomeDiscount->total_value) }}
                            @endif
                        </td>
                        <th>{{ __('rrhh.quota_value') }}</th>
                        <td>
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($incomeDiscount->quota_value) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($incomeDiscount->quota_value) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('rrhh.start_date') }}</th>
                        <td>{{ @format_date($incomeDiscount->start_date) }}</td>
                        <th>{{ __('rrhh.end_date') }}</th>
                        <td>{{ @format_date($incomeDiscount->end_date) }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('rrhh.quotas_applied') }}</th>
                        <td>{{ $incomeDiscount->quotas_applied }}</td>
                        <th>{{ __('rrhh.balance_to_date') }}</th>
                        <td>
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($incomeDiscount->balance_to_date) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($incomeDiscount->balance_to_date) }}
                            @endif
                        </td>
                    </tr>
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