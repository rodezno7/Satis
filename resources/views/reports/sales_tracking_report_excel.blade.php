<table>
    {{-- Header --}}
    <tr>
        <td>
            <strong>{{ mb_strtoupper($business->line_of_business) }}</strong>
        </td>
    </tr>
    <tr>
        <td>
            <strong>{{ mb_strtoupper(__('report.sales_tracking_report')) }}</strong>
        </td>
    </tr>

    <tr></tr>

    {{-- Table --}}
    <thead>
        <tr>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('order.ref_no') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('messages.date') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('customer.customer_code') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('sale.customer_name') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('order.delivery_type') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('quote.invoiced') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('report.quoted_amount') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('report.invoiced_amount') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('quote.seller') }}</strong>
            </th>
        </tr>
    </thead>
    
    <tbody>
        @foreach ($orders as $item)
        <tr>
            <td>
                {{ $item->code }}
            </td>
            <td>
                {{ $item->quote_date }}
            </td>
            <td>
                {{ $item->customer_id }}
            </td>
            <td>
                {{ $item->customer }}
            </td>
            <td>
                {{ __('order.' . $item->delivery_type) }}
            </td>
            <td>
                {{ __('messages.' . $item->invoiced) }}
            </td>
            <td>
                {{ ! empty($item->quoted_amount) ? $item->quoted_amount : 0 }}
            </td>
            <td>
                {{ ! empty($item->invoiced_amount) ? $item->invoiced_amount : 0 }}
            </td>
            <td>
                {{ $item->seller }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>