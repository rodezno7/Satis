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
                        <strong>@lang('accounting.date'):</strong> {{ $util->format_date($binnacle->created_at, true) }}
                    </p>
                    <p>
                        <strong>@lang('accounting.user'):</strong> {{ $binnacle->user->first_name . ' ' . $binnacle->user->last_name }}
                    </p>
                </div>

                <div class="col-sm-6">
                    <p style="margin-bottom: 1px;">
                        <strong>@lang('role.module'):</strong> {{ __('binnacle.' . $binnacle->module) }}
                    </p>
                    <p>
                        <strong>@lang('accounting.action'):</strong> {{ __('binnacle.' . $binnacle->action) }}
                    </p>
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
