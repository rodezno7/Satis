@extends('layouts.app')

@section('title', __('retention.retention_notes'))

@section('content')
{{-- Content Header (Page header) --}}
<section class="content-header">
    <h1>@lang('retention.retention_notes')</h1>
</section>

{{-- Main content --}}
<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">
                @lang('retention.manage_retention_notes')
            </h3>
            @can('retentions.create')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{ action('RetentionController@create') }}" 
                    data-container=".retention_modal">
                    <i class="fa fa-plus"></i> @lang('messages.add')
                </button>
            </div>
            @endcan
        </div>
        
        <div class="box-body">
            @can('retention.view')
            <div class="table-responsive">
                <table class="table table-striped table-text-center" id="retentions_table" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center">@lang('messages.date')</th>
                            <th class="text-center">@lang('accounting.type')</th>
                            <th class="text-center">@lang('sale.document_no')</th>
                            <th width="28%">@lang('crm.customer')</th>
                            <th width="28%">@lang('accounting.description')</th>
                            <th class="text-center">@lang('accounting.amount')</th>
                            <th class="text-center">@lang('messages.actions')</th>
                        </tr>
                    </thead>
                </table>
            </div>
            @endcan
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade retention_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    {{-- Notification --}}
    @if (! empty($output))
    {!! Form::hidden('notification', $output['success'], ['id' => 'notification', 'data-msg' => $output['msg']]) !!}
    @endif
</section>
@endsection

@section('javascript')
<script src="{{ asset('js/retention.js?v=' . $asset_v) }}"></script>
@endsection