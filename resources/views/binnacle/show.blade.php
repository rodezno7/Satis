<div class="modal-dialog modal-lg" role="document">
	<div class="modal-content" style="border-radius: 20px;">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="modalTitle">{{ __('binnacle.binnacle') }}</h4>
        </div>
  
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-6">
                    @php
                    $util = new \App\Utils\Util;
                    @endphp
                    <p style="margin-bottom: 1px;">
                        <strong>@lang('accounting.date'):</strong> 
                        @if ($binnacle->realized_in != null)
                            {{ $util->format_date($binnacle->realized_in, true) }}
                        @else
                            {{ $util->format_date($binnacle->created_at, true) }}
                        @endif
                    </p>
                    <p>
                        <strong>@lang('binnacle.user1'):</strong> {{ $binnacle->user->first_name . ' ' . $binnacle->user->last_name }}
                    </p>
                    @if ($binnacle->machine_name != null)
                        <p>
                            <strong>@lang('binnacle.machine_name'):</strong> {{ $binnacle->machine_name }}
                        </p>
                    @endif
                    @if ($binnacle->domain != null)
                        <p>
                            <strong>@lang('binnacle.domain'):</strong> {{ $binnacle->domain }}
                        </p>
                    @endif
                </div>

                <div class="col-sm-6">
                    <p style="margin-bottom: 1px;">
                        <strong>@lang('role.module'):</strong> {{ __('binnacle.' . $binnacle->module) }}
                    </p>
                    <p>
                        <strong>@lang('binnacle.action'):</strong> {{ __('binnacle.' . $binnacle->action) }}
                    </p>
                    @if ($binnacle->ip != null)
                        <p>
                            <strong>@lang('binnacle.machine_name'):</strong> {{ $binnacle->ip }}
                        </p>
                    @endif
                    @if ($binnacle->city != null)
                        <p>
                            <strong>@lang('binnacle.country'):</strong> {{ $binnacle->country }} <br>
                            <strong>@lang('binnacle.department'):</strong> {{ $binnacle->city }}<br>
                            <strong>@lang('binnacle.latitude'):</strong> {{ $binnacle->latitude }}<br>
                            <strong>@lang('binnacle.longitude'):</strong> {{ $binnacle->longitude }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="row">
                {{-- Old record --}}
                <div class="col-sm-6">
                    <h4>@lang('binnacle.data_before_action')</h4>

                    <div class="well">
                        @foreach ($old_record as $key => $value)
                        <strong>{{ $key }}:</strong> {{ $value }} <br>
                        @endforeach
                    </div>
                </div>

                {{-- New record --}}
                <div class="col-sm-6">
                    <h4>@lang('binnacle.data_after_action')</h4>

                    <div class="well">
                        @if (! empty($new_record))    
                        @foreach ($new_record as $key => $value)
                        <strong>{{ $key }}:</strong> {{ $value }} <br>
                        @endforeach
                        @else
                        @lang('binnacle.action_no_modify')
                        @endif
                    </div>
                </div>
            </div>
        </div>
  
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>
	</div>
</div>
