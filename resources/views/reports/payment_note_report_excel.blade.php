<table>
    {{-- Header --}}
    <tr>
        <td>
            <strong>{{ mb_strtoupper($business->line_of_business) }}</strong>
        </td>
    </tr>
    <tr>
        <td>
            <strong>{{ mb_strtoupper(__('report.payment_notes_report')) }}</strong>
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
                <strong>{{ __('lang_v1.payment_note') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('contact.customer') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('inflow_outflow.document_no') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('document_type.title') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('accounting.amount') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('accounting.balance') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('accounting.status') }}</strong>
            </th>
        </tr>
    </thead>
    
    <tbody>
        @php
        $total_amount = 0;
        @endphp
        @foreach ($payments as $item)
        <tr>
            <td style="text-align: center;">
                {{ $item->paid_on }}
            </td>
            <td style="text-align: center;">
                {{ $item->note }}
            </td>
            <td>
                {{ $item->customer_name }}
            </td>
            <td style="text-align: center;">
                {{ $item->correlative }}
            </td>
            <td style="text-align: center;">
                {{ $item->document_name }}
            </td>
            <td style="text-align: center;">
                {{ $item->amount }}
            </td>
            <td style="text-align: center;">
                {{ $item->balance }}
            </td>
            <td style="text-align: center;">
                @php
                    $status = 'partial';
                    if ($item->balance == 0) {
                        $status = 'paid';
                    } else if ($item->balance == $item->final_total) {
                        $status = 'due';
                    }
                @endphp
                {{ __('lang_v1.' . $status) }}
            </td>
        </tr>
        @php
        $total_amount += $item->amount;
        @endphp
        @endforeach
    </tbody>
    <tr>
        <td colspan="5" style="border-top: 0.25px solid black; text-align: center;">
            <strong>{{ __('accounting.totals') }}</strong>
        </td>
        <td style="border-top: 0.25px solid black;">
            {{ $total_amount }}
        </td>
        <td style="border-top: 0.25px solid black; text-align: center;"></td>
        <td style="border-top: 0.25px solid black; text-align: center;"></td>
    </tr>
</table>