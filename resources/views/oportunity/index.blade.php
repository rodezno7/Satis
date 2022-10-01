@extends('layouts.app')
@section('title', __('crm.oportunities'))
    <style>
        td.details-control {
            background: url('../resources/details_open.png') no-repeat center center;
            cursor: pointer;
        }

        tr.shown td.details-control {
            background: url('../resources/details_close.png') no-repeat center center;
        }

        i.edit-glyphicon {
            position: inherit;
            line-height: inherit;
        }

    </style>
    <script th:src="@{/js/datatables.min.js}"></script>
@section('content')
    <section class="content-header">
        <h1>@lang( 'crm.oportunities' )</h1>
    </section>
    <section class="content">
        <div class="boxform_u box-solid_u">
            <div class="box-header" id="header_oportunity">
                <h3 class="box-title">@lang('crm.manage_oportunities')</h3>
                @can('crm-oportunities.create')
                    <div class="box-tools">
                        <button type="button" class="btn btn-block btn-primary btn-modal"
                            data-href="{{ action('OportunityController@create') }}" data-container=".oportunity_modal">
                            <i class="fa fa-plus"></i> @lang('messages.add')
                        </button>
                    </div>
                @endcan
            </div>

            <div class="box-header" id="header_follow" style="display: none;">
                <h3 class="box-title">@lang('crm.oportunity')</h3>
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary" id="back">
                        @lang('crm.back')
                    </button>
                </div>
            </div>
            <div class="box-body">
                @can('crm-contactreason.view')
                    <div class="row">
                        <div class="col-sm-12" id="date-filter">
                            <div class="form-group">
                                <div class="input-group">
                                    <button type="button" class="btn btn-primary" id="daterange-btn">
                                        <span>
                                            <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                                        </span>
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="type" id="type" value="{{ $type }}">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="div_oportunities">
                        <div class="table-responsive">
                            <table class="table table-stripe table-bordered table-condensed table-hover" id="oportunity_table"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>@lang('crm.contact_date')</th>
                                        <th>@lang('crm.contact_type')</th>
                                        <th>@lang('crm.contactreason')</th>
                                        <th>@lang('crm.name')</th>
                                        <th>@lang('crm.created_by')</th>
                                        <th>@lang('crm.actions')</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                @endcan
            </div>
        </div>

        {{-- Div para renderizar el modal --}}
        <div class="modal fade oportunity_modal" data-backdrop="static" tabindex="-1" role="dialog"
            aria-labelledby="gridSystemModalLabel"></div>
        <div class="modal fade oportunities_modal" data-backdrop="static" tabindex="-1" role="dialog"
            aria-labelledby="gridSystemModalLabel"></div>

    </section>
@endsection
@section('javascript')
    <script src="{{ asset('js/oportunity.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    </script>
@endsection
