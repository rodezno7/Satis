<table>
    <tr>
        <td><strong>{{ mb_strtoupper($business->business_full_name) }}</strong></td>
    </tr>

    <tr>
        <td><strong>{{ mb_strtoupper(__('accounting.book_sales_taxpayer')) }}</strong></td>
    </tr>

    <tr>
        <td colspan="3">
            <strong>{{ mb_strtoupper(__('accounting.property_name')) }}:</strong>
        </td>
        <td>
            {{ $business->business_full_name }}
        </td>
    </tr>

    <tr>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.month')) }}:</strong>
        </td>
        <td>
            @if ($initial_month == $final_month)
            {{ $initial_month }}
            @else
            {{ $initial_month }} - {{ $final_month }}
            @endif
        </td>
        <td></td>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.year')) }}:</strong>
        </td>
        <td>
            @if ($initial_year == $final_year)
            {{ $initial_year }}
            @else
            {{ $initial_year }} - {{ $final_year }}
            @endif
        </td>
        <td></td>
        <td>
            <strong>{{ mb_strtoupper(__('business.nit')) }}:</strong>
        </td>
        <td>{{ $business->nit }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td style="width: 25%">
            <strong>{{ mb_strtoupper(__('accounting.record_no')) }}:</strong>
        </td>
        <td>{{ $business->nrc }}</td>
        <td></td>
    </tr>

    <tr></tr>

    <thead>
        <tr>
            <th rowspan="3" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.no_tag')) }}</strong>
            </th>
            <th rowspan="3" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.date_of_issue')) }}</strong>
            </th>
            <th rowspan="3" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.pre-printed_serial_number')) }}</strong>
            </th>
            <th rowspan="3" style="border: 0.25px solid black;">
                <strong>SERIE</strong>
            </th>
            <th rowspan="3" style="border: 0.25px solid black;">
                <strong>N° RESOLUCIÓN</strong>
            </th>
            <th rowspan="3" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.control_single_computer_system')) }}</strong>
            </th>
            <th rowspan="3" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('business.nrc')) }}</strong>
            </th>
            <th rowspan="3" style="border: 0.25px solid black;">
                <strong>DUI</strong>
            </th>
            <th rowspan="3" style="border: 0.25px solid black;">
                <strong>NIT</strong>
            </th>
            <th rowspan="3" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.name_client_principal_agent')) }}</strong>
            </th>
            <th colspan="6" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.own_sales_operations_and_third_parties')) }}</strong>
            </th>
            <th rowspan="3" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.tax_withheld')) }} 1%</strong>
            </th>
            <th rowspan="3" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.total_sales')) }}</strong>
            </th>
        </tr>
        <tr>
            <th colspan="3" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.own_sales')) }}</strong>
            </th>
            <th colspan="3" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.third_party_account')) }}</strong>
            </th>
        </tr>
        <tr>
            <th style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.exempt')) }}</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.internal')) }}</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.fiscal_debit')) }}</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.exempt')) }}</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.internal')) }}</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.fiscal_debit')) }}</strong>
            </th>
        </tr>
    </thead>
    <tbody>
        @php
        $total_internal = 0;
        $total_fiscal_debit = 0;
        $sum_total_sales = 0;
        $total_tax_amount = 0;
        @endphp
        @foreach ($lines as $item)
        <tr>
            <td style="border: 0.25px solid black;">
                {{ $loop->iteration }}
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->transaction_date }}
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->correlative }}
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->serie }}
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->resolution }}
            </td>

            <td style="border: 0.25px solid black;"></td>
            <td style="border: 0.25px solid black;">
                @if ($item->status != 'annulled')
                {{ $item->nrc }}
                @endif
            </td>
            <td style="border: 0.25px solid black;">
                @if ($item->status != 'annulled')
                {{ $item->dui }}
                @endif
            </td>
            <td style="border: 0.25px solid black;">
                @if ($item->status != 'annulled')
                {{ $item->nit }}
                @endif
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->customer }}
                @if ($item->status == 'annulled')
                - {{ mb_strtoupper(__('accounting.annulled')) }}
                @endif
            </td>
            <td style="border: 0.25px solid black;"></td>
            <td style="border: 0.25px solid black;">
                @if ($item->status != 'annulled')
                {{ $item->internal }}
                @else
                0.00
                @endif
            </td>
            <td style="border: 0.25px solid black;">
                @if ($item->status != 'annulled')
                {{ $item->fiscal_debit }}
                @else
                0.00
                @endif
            </td>
            <td style="border: 0.25px solid black;"></td>
            <td style="border: 0.25px solid black;"></td>
            <td style="border: 0.25px solid black;"></td>
            <td style="border: 0.25px solid black;">
                @if ($item->status != 'annulled')
                {{ $item->tax_amount }}
                @else
                0.00
                @endif
            </td>
            <td style="border: 0.25px solid black;">
                @if ($item->status != 'annulled')
                {{ $item->total_sales }}
                @else
                0.00
                @endif
            </td>
        </tr>
        @php
        if ($item->status != 'annulled') {
            $total_internal += $item->internal;
            $total_fiscal_debit += $item->fiscal_debit;
            $sum_total_sales += $item->total_sales;
            $total_tax_amount += $item->tax_amount;
        }
        @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10" style="border: 0.25px solid black; text-align: center;">
                <strong>{{ mb_strtoupper(__('accounting.totals')) }}</strong>
            </td>
            <td style="border: 0.25px solid black;"></td>
            <td style="border: 0.25px solid black;">
                <strong>{{ $total_internal }}</strong>
            </td>
            <td style="border: 0.25px solid black;">
                <strong>{{ $total_fiscal_debit }}</strong>
            </td>
            <td style="border: 0.25px solid black;"></td>
            <td style="border: 0.25px solid black;"></td>
            <td style="border: 0.25px solid black;"></td>
            <td style="border: 0.25px solid black;">
                <strong>{{ number_format($total_tax_amount, 2) }}</strong>
            </td>
            <td style="border: 0.25px solid black;">
                <strong>{{ $sum_total_sales }}</strong>
            </td>
        </tr>
    </tfoot>

    <tr></tr>
    <tr></tr>
    <tr></tr>

    <tr>
        <td colspan="4" style="border-bottom: 0.25px solid black;">F.</td>
    </tr>

    <tr>
        <td colspan="4">Contador</td>
    </tr>
</table>