<div class="box-body">
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-sm-12">
            <div style="display: inline-block">
                <p id="lbl-customer-name" style="margin-bottom: 2px; font-size: 20px;"></p>

                <p style="font-size: 16px; margin-bottom: 0;">
                    @lang('accounting.balance_to_date'):
                    <span id="lbl-total-remaining"></span>
                </p>

                <p style="font-size: 16px;">
                    @lang('customer.credit_limit'):
                    <span id="lbl-credit-limit"></span>
                </p>
            </div>
        
            <div class="pull-right" style="display: inline-block">
                {!! Form::open(['id' => 'form_account_statement', 'action' => 'ReportController@postAccountStatement', 'method' => 'post', 'target' => '_blank', 'style' => 'margin-bottom: 5px;']) !!}
                    {!! Form::hidden('customer_id', null, ['id' => 'customer_id']) !!}
                    {!! Form::hidden('payment_status', 1, ['id' => 'payment_status']) !!}
                    {!! Form::hidden('start_date', null, ['id' => 'start_date']) !!}
                    {!! Form::hidden('end_date', null, ['id' => 'end_date']) !!}
                    {!! Form::hidden('size', '8', ['id' => 'size']) !!}
                    {!! Form::hidden('report_type', 'pdf', ['id' => 'report_type']) !!}

                    <button type="submit" class="btn btn-block btn-primary" id="generate-account-statement">
                        <i class="fa fa-file-text"></i>&nbsp; @lang('customer.generate_account_statement')
                    </button>
                {!! Form::close() !!}
                <div id="send_account_statement" style="display: none">
                    {!! Form::open(['id' => 'form_email_account_statement', 'action' => 'MailController@sendAccountStatement', 'method' => 'post']) !!}
                    {!! Form::hidden('email_customer_id', null, ['id' => 'email_customer_id']) !!}
                    {!! Form::hidden('email_payment_status', 1, ['id' => 'payment_status']) !!}
                    {!! Form::hidden('email_start_date', null, ['id' => 'email_start_date']) !!}
                    {!! Form::hidden('email_end_date', null, ['id' => 'email_end_date']) !!}

                    <button type="submit" class="btn btn-block btn-success" id="send-account-statement">
                        <i class="fa fa-envelope"></i>&nbsp; @lang('customer.send_account_statement')
                    </button>
                {!! Form::close() !!}
                </div>                                  
            </div>
        </div>
    </div>

    <div class="row" style="margin-bottom: 20px;">
        <div class="col-sm-12">
            <div class="btn-group pull-right" data-toggle="buttons">
                {{-- Today --}}
                <label class="btn btn-info">
                    <input
                        type="radio"
                        name="date-filter"
                        data-start="{{ date('Y-m-d') }}" 
                        data-end="{{ date('Y-m-d') }}">
                    {{ __('home.today') }}
                </label>

                {{-- This week --}}
                <label class="btn btn-info">
                    <input
                        type="radio"
                        name="date-filter"
                        data-start="{{ $date_filters['this_week']['start'] }}" 
                        data-end="{{ $date_filters['this_week']['end'] }}">
                    {{ __('home.this_week') }}
                </label>

                {{-- This month --}}
                <label class="btn btn-info">
                    <input
                        type="radio"
                        name="date-filter"
                        data-start="{{ $date_filters['this_month']['start'] }}" 
                        data-end="{{ $date_filters['this_month']['end'] }}">
                    {{ __('home.this_month') }}
                </label>

                {{-- This fiscal year --}}
                <label class="btn btn-info">
                    <input
                        type="radio"
                        name="date-filter" 
                        data-start="{{ $date_filters['this_fy']['start'] }}" 
                        data-end="{{ $date_filters['this_fy']['end'] }}"
                        checked>
                    {{ __('home.this_fy') }}
                </label>

                {{-- Choose month --}}
                <label class="btn btn-info" data-toggle="modal" data-target="#choose_month_modal">
                    {{ __('home.choose_month') }}
                </label>

                {{-- Date range --}}
                <label class="btn btn-info" id="range-date-filter">
                    <span>
                        {{ __('report.date_range') }}
                    </span>
                </label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive">
                <table class="table table-striped table-text-center" style="font-size: inherit;" width="100%" id="account-statement-table">
                    <thead>
                        <tr>
                            <th class="text-center">@lang('messages.date')</th>
                            <th class="text-center">@lang('quote.due_date')</th>
                            <th class="text-center">@lang('invoice.document_type')</th>
                            <th class="text-center">@lang('report.document_no')</th>
                            <th class="text-center" width="35%">@lang('contact.customer')</th>
                            <th class="text-center">@lang('accounting.status')</th>
                            <th class="text-center">@lang('customer.total_invoiced')</th>
                            <th class="text-center">@lang('customer.total_payment')</th>
                            <th class="text-center">@lang('customer.remaining_credit')</th>
                            <th class="text-center">@lang('messages.actions')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray footer-total text-center">
                            <td colspan="6">
                                <strong>@lang('sale.total')</strong>
                            </td>
                            <td>
                                <span class="display_currency" id="footer-final-total-bc" data-currency_symbol="true"></span>
                            </td>
                            <td>
                                <span class="display_currency" id="footer-total-paid" data-currency_symbol="true"></span>
                            </td>
                            <td>
                                <span class="display_currency" id="footer-total-remaining" data-currency_symbol="true"></span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Choose month modal --}}
	@include('home.partials.choose_month_modal', ['months' => $months])
</div>