<table>
    {{-- Header --}}
    <tr>
        <td>
            <strong>{{ mb_strtoupper($business->line_of_business) }}</strong>
        </td>
    </tr>
    <tr>
        <td>
            <strong>{{ mb_strtoupper(__('quote.lost_sale_report')) }}</strong>
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
                <strong>{{ __('quote.due_date') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('quote.lost_date') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('order.ref_no') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('Documento') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('quote.reason') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('customer.customer_code') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('sale.customer_name') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('quote.seller') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('report.quoted_amount') }}</strong>
            </th>
        </tr>
    </thead>

    <tbody>
        @foreach ($quotes as $item)
            <tr>
                <td>
                    {{ @format_date($item->quote_date) }}
                </td>
                <td>
                    {{ @format_date($item->due_date) }}
                </td>
                <td>
                    {{ @format_date($item->lost_date) }}
                </td>
                <td>
                    {{ $item->ref_no }}
                </td>
                <td>
                    {{ $item->document }}
                </td>
                <td>
                    {{ $item->reason }}
                </td>
                <td>
                    {{ $item->customer_id }}
                </td>
                <td>
                    {{ $item->customer_name }}
                </td>
                <td>
                    {{ $item->seller_name }}
                </td>
                <td>
                    {{ !empty($item->total_final) ? $item->total_final : 0 }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
