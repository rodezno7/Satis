<div class="modal-header">
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="modalTitle"> @lang('expense.expense_details') (<b>@lang('purchase.ref_no'):</b> #{{ $expense->ref_no }})
    </h4>
</div>
<div class="row">
    <div class="col-sm-12">
        <h4 class="pull-left" style="margin-left: 12px;"><b>@lang('expense.type_expense'):</b> {{ ucfirst($expense->name) }}</h4>
    </div>
</div>
<div class="col-sm-4 invoice-col">
    @lang('purchase.supplier'):
    <address>
        <strong>{{ $expense->contact->supplier_business_name }}</strong><br>
        {{ $expense->contact->name }}
        @if (!empty($expense->contact->landmark))
            <br>{{ $expense->contact->landmark }}
        @endif
        @if (!empty($expense->contact->city) || !empty($expense->contact->state) || !empty($expense->contact->country))
            <br>{{ implode(',', array_filter([$expense->contact->city, $expense->contact->state, $expense->contact->country])) }}
        @endif
        @if (!empty($expense->contact->tax_number))
            <br>@lang('contact.tax_no'): {{ $expense->contact->tax_number }}
        @endif
        @if (!empty($expense->contact->mobile))
            <br>@lang('contact.mobile'): {{ $expense->contact->mobile }}
        @endif
        @if (!empty($expense->contact->email))
            <br>Email: {{ $expense->contact->email }}
        @endif
    </address>
    @if ($expense->document_path)
        <a href="{{ $expense->document_path }}" download="{{ $expense->document_name }}"
            class="btn btn-sm btn-success pull-left no-print">
            <i class="fa fa-download"></i>
            &nbsp;{{ __('purchase.download_document') }}
        </a>
    @endif
</div>
<div class="col-sm-4 invoice-col">
    @lang('business.business'):
    <address>
        <strong>{{ $expense->business->name }}</strong>
        {{ $expense->location->name }}
        @if (!empty($expense->location->landmark))
            <br>{{ $expense->location->landmark }}
        @endif
        @if (!empty($expense->location->city) || !empty($expense->location->state) || !empty($expense->location->country))
            <br>{{ implode(',', array_filter([$expense->location->city, $expense->location->state, $expense->location->country])) }}
        @endif

        @if (!empty($expense->business->tax_number_1))
            <br>{{ $expense->business->tax_label_1 }}: {{ $expense->business->tax_number_1 }}
        @endif

        @if (!empty($expense->business->tax_number_2))
            <br>{{ $expense->business->tax_label_2 }}: {{ $expense->business->tax_number_2 }}
        @endif

        @if (!empty($expense->location->mobile))
            <br>@lang('contact.mobile'): {{ $expense->location->mobile }}
        @endif
        @if (!empty($expense->location->email))
            <br>@lang('business.email'): {{ $expense->location->email }}
        @endif
    </address>
</div>

<div class="col-sm-4 invoice-col">
    <b>@lang('purchase.ref_no'):</b> #{{ $expense->ref_no }}<br />
    <b>@lang('messages.date'):</b> {{ @format_date($expense->transaction_date) }}<br />
    <b>@lang('expense.expense_status'):</b> {{ ucfirst($expense->status) }}<br>
    <b>@lang('purchase.payment_status'):</b> {{ __('expense.expense_'.$expense->payment_status) }}<br>
    <b>@lang('expense.type_doc'):</b> {{ ucfirst($expense->short_name) }}<br>
</div>
</div>
<br>
<div class="row">
    <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <th>@lang('purchase.net_total_amount'): </th>
                    <td></td>
                    <td><span class="display_currency pull-right" data-currency_symbol="true"
                            data-precision="2">{{ $expense->total_before_tax }}</span></td>
                </tr>
                <tr>
                    <th>@lang('expense.tax_expense'):</th>
                    <td><b>(+)</b></td>
                    <td class="text-right">
                        @foreach ($taxes as $t)
                            <strong><small>{{ $t['tax_name'] }}</small></strong>&nbsp;
                            <span class="display_currency pull-right" data-currency_symbol="true"
                                data-precision="2">{{ $t['tax_amount'] }}</span><br>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <th>@lang('expense.total_expense'):</th>
                    <td></td>
                    <td><span class="display_currency pull-right" data-currency_symbol="true" data-precision="2">
                            {{ $expense->final_total }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
