<table>
    {{-- Header --}}
    <tr>
        <td>
            <strong>{{ mb_strtoupper($business->line_of_business) }}</strong>
        </td>
    </tr>
    <tr>
        <td>
            <strong>{{ mb_strtoupper(__('report.detailed_commissions_report')) }}</strong>
        </td>
    </tr>

    <tr></tr>

    {{-- Table --}}
    <thead>
        <tr>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('accounting.year') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('accounting.month') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('report.day') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('accounting.date') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('report.document_no') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('document_type.title') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('lang_v1.payment_condition') }}</strong>
            </th>
            @if (config('app.business') != 'optics')
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('customer.customer_code') }}</strong>
            </th>
            @endif
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('contact.customer') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('category.category') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('product.sub_category') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('product.brand') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>SKU</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('business.product') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('lang_v1.quantity') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('report.price_inc_tax') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('report.price_exc_tax') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('quote.seller') }}</strong>
            </th>
            @if (config('app.business') == 'optics')
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('graduation_card.optometrist') }}</strong>
            </th>
            @endif
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('report.unit_cost') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('report.total_cost') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('customer.customer_portfolio') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('geography.state') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('geography.city') }}</strong>
            </th>
        </tr>
    </thead>
    
    <tbody>
        @foreach ($commissions as $item)
        <tr>
            <td>
                {{ $item->year }}
            </td>
            <td>
                {{ $item->month }}
            </td>
            <td>
                {{ $item->day }}
            </td>
            <td>
                {{ @format_date($item->transaction_date) }}
            </td>
            <td>
                {{ $item->doc_no }}
            </td>
            <td>
                {{ $item->doc_type }}
            </td>
            <td>
                {{ $item->payment_condition }}
            </td>
            @if (config('app.business') != 'optics')
            <td>
                {{ $item->customer_id }}
            </td>
            @endif
            <td>
                {{ $item->customer_name }}
            </td>
            <td>
                {{ $item->category }}
            </td>
            <td>
                {{ $item->sub_category }}
            </td>
            <td>
                {{ $item->brand_name }}
            </td>
            <td>
                {{ $item->sku }}
            </td>
            <td>
                {{ $item->product_name }}
            </td>
            <td>
                {{ $item->quantity }}
            </td>
            <td>
                {{ $item->price_inc }}
            </td>
            <td>
                {{ $item->price_exc }}
            </td>
            <td>
                {{ $item->seller_name }}
            </td>
            @if (config('app.business') == 'optics')
            <td>
                {{ $item->optometrist }}
            </td>
            @endif
            <td>
                {{ $item->unit_cost }}
            </td>
            <td>
                {{ $item->total_cost }}
            </td>
            <td>
                {{ $item->portfolio_name }}
            </td>
            <td>
                {{ $item->state }}
            </td>
            <td>
                {{ $item->city }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>