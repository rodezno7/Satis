<table>
    {{-- Header --}}
    <tr>
        <td>
            <strong>{{ mb_strtoupper($business->line_of_business) }}</strong>
        </td>
    </tr>
    <tr>
        <td>
            <strong>{{ mb_strtoupper(__('report.lab_orders_report')) }}</strong>
        </td>
    </tr>

    <tr></tr>

    {{-- Table --}}
    <thead>
        <tr>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('lab_order.no_order') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('document_type.document') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('accounting.location') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('contact.customer') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('graduation_card.patient') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('accounting.status') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('business.register') }}</strong>
            </th>
            <th style="border-bottom: 0.25px solid black;">
                <strong>{{ __('lab_order.delivery') }}</strong>
            </th>
        </tr>
    </thead>
    
    <tbody>
        @php
        $util = new \App\Utils\Util;
        @endphp
        @foreach ($lab_orders as $item)
        <tr>
            <td style="text-align: center;">
                {{ $item->no_order }}
            </td>
            <td style="text-align: center;">
                {{ $item->correlative }}
                <br>
                <small>{{ $item->document }}</small>
            </td>
            <td style="text-align: center;">
                {{ $item->location }}
            </td>
            <td>
                {{ $item->customer }}
            </td>
            <td>
                {{ $item->patient }}
            </td>
            <td style="text-align: center;">
                {{ $item->status }}
            </td>
            <td style="text-align: center;">
                {{ $item->created_at }}
            </td>
            <td style="text-align: center;">
                {{ $item->delivery }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>