<table>
    <tr>
        <td><strong>{{ mb_strtoupper($business->business_full_name) }}</strong></td>
    </tr>

    <tr>
        <td><strong>{{ mb_strtoupper(__('accounting.book_sales_final_consumer')) }}</strong></td>
    </tr>

    <tr></tr>

    <tr>
        <td><strong>{{ mb_strtoupper(__('accounting.month')) }}:</strong></td>
        <td>
            @if ($initial_month == $final_month)
            {{ $initial_month }}
            @else
            {{ $initial_month }} - {{ $final_month }}
            @endif
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td><strong>{{ mb_strtoupper(__('accounting.record_no')) }}:</strong></td>
        <td>{{ $business->nrc }}</td>
        <td></td>
        <td></td>
    </tr>

    <tr>
        <td><strong>{{ mb_strtoupper(__('accounting.year')) }}:</strong></td>
        <td>
            @if ($initial_year == $final_year)
            {{ $initial_year }}
            @else
            {{ $initial_year }} - {{ $final_year }}
            @endif
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td><strong>{{ mb_strtoupper(__('accounting.nit_no')) }}:</strong></td>
        <td>{{ $business->nit }}</td>
        <td></td>
        <td></td>
    </tr>

    <tr></tr>

    <thead>
        <tr>
            <th rowspan="2" style="border: 0.25px solid black;">
                <strong>@lang('accounting.date_of_issue')</strong>
            </th>
            <th colspan="4" style="border: 0.25px solid black;">
                <strong>@lang('accounting.documents_issued')</strong>
            </th>
            <th rowspan="2" style="border: 0.25px solid black;">
                <strong>@lang('accounting.cash_or_computerized_system_number')</strong>
            </th>
            <th colspan="3" style="border: 0.25px solid black;">
                <strong>@lang('role.sales')</strong>
            </th>
            <th rowspan="2" style="border: 0.25px solid black;">
                <strong>@lang('accounting.total_daily_own_sales')</strong>
            </th>
            <th rowspan="2" style="border: 0.25px solid black;">
                <strong>@lang('accounting.sales_on_behalf_of_third_parties')</strong>
            </th>
        </tr>
        <tr>
            <th style="border: 0.25px solid black;">
                <strong>@lang('accounting.from_no')</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>@lang('accounting.to_no')</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>SERIE</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>RESOLUCIÓN</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>@lang('accounting.exempt')</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>@lang('accounting.internal_taxed')</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>@lang('accounting.exports')</strong>
            </th>
        </tr>
    </thead>
    <tbody>
        @php
        $total_fcf = 0;
        $total_ticket = 0;
        $total_exports = 0;
        @endphp
        @foreach ($lines as $item)
        <tr>
            <td style="border: 0.25px solid black;">
                {{ $item->transaction_date }}
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->initial_correlative }}
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->final_correlative }}
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->serie }}
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->resolution }}
            </td>
            <td style="border: 0.25px solid black;"></td>
            <td style="border: 0.25px solid black;"></td>
            <td style="border: 0.25px solid black;">
                @if (! empty($item->taxed_sales))
                {{ $item->taxed_sales }}
                @endif
            </td>
            <td style="border: 0.25px solid black;">
                @if (! empty($item->exports))
                {{ $item->exports }}
                @endif
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->taxed_sales + $item->exports }}
            </td>
            <td style="border: 0.25px solid black;"></td>
        </tr>
        @php
            if (config('app.business') == 'optics') {
                if ($item->short_name == 'FACTURA') {
                    $total_fcf += $item->taxed_sales;
                } else if ($item->short_name == 'Ticket') {
                    $total_ticket += $item->taxed_sales;
                }

            } else {
                if ($item->short_name == 'FCF') {
                    $total_fcf += $item->taxed_sales;
                } else if ($item->short_name == 'Ticket') {
                    $total_ticket += $item->taxed_sales;
                }
            }

            $total_exports += $item->exports;
        @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr style="border: 0.25px solid black;">
            <th colspan="6" style="text-align: center; border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.totals')) }}</strong>
            </th>
            <th style="border: 0.25px solid black;"></th>
            <th style="border: 0.25px solid black;">
                <strong>{{ ($total_fcf + $total_ticket) }}</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>{{ $total_exports }}</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>{{ $total_fcf + $total_ticket + $total_exports }}</strong>
            </th>
            <th style="border: 0.25px solid black;"></th>
        </tr>
    </tfoot>

    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>

    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td colspan="2" style="text-align: center; border: 0.25px solid black;">
            <strong>{{ mb_strtoupper(__('accounting.own')) }}</strong>
        </td>
        <td colspan="2" style="text-align: center; border: 0.25px solid black;">
            <strong>{{ mb_strtoupper(__('accounting.third_party_account')) }}</strong>
        </td>
        <td rowspan="2" style="text-align: center; border: 0.25px solid black;">
            <strong>TOTAL</strong>
        </td>
    </tr>

    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="text-align: center; border: 0.25px solid black;">
            <strong>{{ mb_strtoupper(__('accounting.net_worth')) }}</strong>
        </td>
        <td style="text-align: center; border: 0.25px solid black;">
            <strong>{{ mb_strtoupper(__('accounting.fiscal_debit')) }}</strong>
        </td>
        <th style="text-align: center; border: 0.25px solid black;">
            <strong>{{ mb_strtoupper(__('accounting.net_worth')) }}</strong>
        </th>
        <th style="text-align: center; border: 0.25px solid black;">
            <strong>{{ mb_strtoupper(__('accounting.fiscal_debit')) }}</strong>
        </th>
    </tr>

    @php
        $total_fcf_exc_tax = $total_fcf / 1.13;
        $total_fcf_tax = $total_fcf - ($total_fcf / 1.13);

        $total_ticket_exc_tax = $total_ticket / 1.13;
        $total_ticket_tax = $total_ticket - ($total_ticket / 1.13);

        $total_exports_exc_tax = $total_exports;
        $total_exports_tax = 0;
    @endphp

    <tr>
        <td colspan="4">
            {{ mb_strtoupper(__('accounting.consumer_taxed_domestic_sales')) }}
        </td>
        <td style="border: 0.25px solid black;">
            {{ $total_fcf_exc_tax }}
        </td>
        <td style="border: 0.25px solid black;">
            {{ $total_fcf_tax }}
        </td>
        <td style="border: 0.25px solid black;"></td>
        <td style="border: 0.25px solid black;"></td>
        <td style="border: 0.25px solid black;">{{ $total_fcf }}</td>
    </tr>

    <tr>
        <td colspan="4">
            {{ mb_strtoupper(__('accounting.domestic_sales_exempt_consumer')) }}
        </td>
        <td style="border: 0.25px solid black;"></td>
        <td style="border: 0.25px solid black;"></td>
        <td style="border: 0.25px solid black;"></td>
        <td style="border: 0.25px solid black;"></td>
        <td style="border: 0.25px solid black;"></td>
    </tr>
    @if (config('app.business') != 'optics')
    <tr>
        <td colspan="4">VENTAS INTERNAS GRAVADAS CONSUMIDOR TICKET</td>
        <td style="border: 0.25px solid black;">
            {{ $total_ticket_exc_tax }}
        </td>
        <td style="border: 0.25px solid black;">
            {{ $total_ticket_tax }}
        </td>
        <td style="border: 0.25px solid black;"></td>
        <td style="border: 0.25px solid black;"></td>
        <td style="border: 0.25px solid black;">{{ $total_ticket }}</td>
    </tr>
    @endif
    <tr>
        <td colspan="4">
            {{ mb_strtoupper(__('accounting.exports_according_to_invoices')) }}
        </td>
        <td style="border: 0.25px solid black;">
            {{ $total_exports_exc_tax }}
        </td>
        <td style="border: 0.25px solid black;">
            {{ $total_exports_tax }}
        </td>
        <td style="border: 0.25px solid black;"></td>
        <td style="border: 0.25px solid black;"></td>
        <td style="border: 0.25px solid black;">{{ $total_exports }}</td>
    </tr>

    <tr>
        <td colspan="4">TOTALES</td>
        <td style="border: 0.25px solid black;">{{ ($total_fcf_exc_tax + $total_ticket_exc_tax + $total_exports_exc_tax) }}</td>
        <td style="border: 0.25px solid black;">{{ ($total_fcf_tax + $total_ticket_tax + $total_exports_tax) }}</td>
        <td style="border: 0.25px solid black;"></td>
        <td style="border: 0.25px solid black;"></td>
        <td style="border: 0.25px solid black;">{{ ($total_fcf + $total_ticket + $total_exports) }}</td>
    </tr>


    <tr></tr>
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