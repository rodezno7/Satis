<table>
    {{-- Header --}}
    <tr>
        <td>
            <strong>{{ mb_strtoupper($business->business_full_name) }}</strong>
        </td>
    </tr>
    <tr>
        <td>
            <strong>{{ mb_strtoupper(__('report.all_sales_report')) }}</strong>
        </td>
    </tr>

    <tr></tr>

    {{-- Table --}}
    <thead>
        <tr>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('messages.date') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('sale.document_no') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('document_type.title') }}</strong>
            </th>
            @if (config('app.business') != 'optics')
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('customer.customer_code') }}</strong>
            </th>
            @endif
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('contact.customer') }}</strong>
            </th>
            @if (config('app.business') == 'optics')
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('sale.location') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('sale.payment_status') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('sale.total_invoice') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('lang_v1.payment_note') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('sale.total_paid') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('sale.total_balance_due') }}</strong>
            </th>
            @else
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('sale.payment_status') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('lang_v1.payment_method') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('sale.subtotal') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('sale.discount') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('tax_rate.taxes') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('sale.total_amount') }}</strong>
            </th>
            @endif
        </tr>
    </thead>
    
    <tbody>
        @php
        $total_status = [];

        if (config('app.business') == 'optics') {
            $total_invoice = 0;
            $total_paid = 0;
            $total_due = 0;

        } else {
            $total_subtotal = 0;
            $total_discount = 0;
            $total_taxes = 0;
            $total_amount = 0;
        }
        @endphp
        @foreach ($sales as $item)
        @php
        if ($item->status == 'annulled') {
            $total_remaining = 0;
        } else {
            $total_remaining = $item->final_total - $item->total_paid;
        }
        @endphp
        <tr>
            <td style="text-align: center;">
                {{ @format_date($item->transaction_date) }}
            </td>
            <td style="text-align: center;">
                {{ $item->correlative }}
            </td>
            <td style="text-align: center;">
                {{ $item->document_name }}
            </td>
            @if (config('app.business') != 'optics')
            <td style="text-align: center;">
                {{ $item->customer_id }}
            </td>
            @endif
            <td>
                {{ $item->customer_name }}
                @if ($item->status == 'annulled')
                - {{ __('lang_v1.annulled') }}
                @endif
            </td>
            @if (config('app.business') == 'optics')
            <td>
                {{ $item->location }}
            </td>
            <td style="text-align: center;">
                @if ($item->status != 'annulled')
                {{ __('lang_v1.' . $item->payment_status) }}
                @endif
            </td>
            <td style="text-align: center;">
                {{ $item->final_total }}
            </td>
            <td style="text-align: center;">
                {{ $item->note }}
            </td>
            <td style="text-align: center;">
                {{ $item->total_paid }}
            </td>
            <td style="text-align: center;">
                {{ $total_remaining }}
            </td>
            @else
            <td style="text-align: center;">
                @if ($item->status != 'annulled')
                {{ __('lang_v1.' . $item->payment_status) }}
                @endif
            </td>
            @php
            $method = '';

            if ($item->status != 'annulled') {
                if ($item->payment_condition == 'cash') {
                    if ($item->count_payments > 1) {
                        $method = __('lang_v1.checkout_multi_pay');
                    } else {
                        $method = ! empty($item->method) ? __('lang_v1.' . $item->method) : '';
                    }
                } else {
                    if (! empty($item->payment_condition)) {
                        $method = ! empty($item->payment_condition) ? __('lang_v1.' . $item->payment_condition) : '';
                    } else {
                        $method = ! empty($item->method) ? __('lang_v1.' . $item->method) : '';
                    }
                }
            }
            @endphp
            <td style="text-align: center;">
                {{ $method }}
            </td>
            <td>
                {{ $item->total_before_tax }}
            </td>
            <td>
                {{ $item->discount }}
            </td>
            <td>
                {{ $item->tax }}
            </td>
            <td>
                {{ $item->final_total }}
            </td>
            @endif
        </tr>
        @php
        if (config('app.business') == 'optics') {
            $total_invoice += $item->final_total;
            $total_paid += $item->total_paid;
            $total_due += $total_remaining;

        } else {
            $total_subtotal += $item->total_before_tax;
            $total_discount += $item->discount;
            $total_taxes += $item->tax;
            $total_amount += $item->final_total;

            if (! empty($item->payment_status)) {
                if (! array_key_exists(__('lang_v1.' . $item->payment_status), $total_status)) {
                    $total_status[__('lang_v1.' . $item->payment_status)] = 1;
                } else {
                    $total_status[__('lang_v1.' . $item->payment_status)] += 1;
                }
            }
        }
        @endphp
        @endforeach
    </tbody>
    <tr>
        <td colspan="5" style="border-top: 0.25px solid black; text-align: center;">
            <strong>{{ __('accounting.totals') }}</strong>
        </td>
        <td style="border-top: 0.25px solid black;">
            @foreach ($total_status as $status => $quantity)
            {{ $status }} - {{ $quantity }}
            <br>
            @endforeach
        </td>
        @if (config('app.business') == 'optics')
        <td style="border-top: 0.25px solid black; text-align: center;">
            <strong>{{ $total_invoice }}</strong>
        </td>
        <td style="border-top: 0.25px solid black; text-align: center;">
            &nbsp;
        </td>
        <td style="border-top: 0.25px solid black; text-align: center;">
            <strong>{{ $total_paid }}</strong>
        </td>
        <td style="border-top: 0.25px solid black; text-align: center;">
            <strong>{{ $total_due }}</strong>
        </td>
        @else
        <td style="border-top: 0.25px solid black;"></td>
        <td style="border-top: 0.25px solid black; text-align: center;">
            <strong>{{ $total_subtotal }}</strong>
        </td>
        <td style="border-top: 0.25px solid black; text-align: center;">
            <strong>{{ $total_discount }}</strong>
        </td>
        <td style="border-top: 0.25px solid black; text-align: center;">
            <strong>{{ $total_taxes }}</strong>
        </td>
        <td style="border-top: 0.25px solid black; text-align: center;">
            <strong>{{ $total_amount }}</strong>
        </td>
        @endif
    </tr>
</table>