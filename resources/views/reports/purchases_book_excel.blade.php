<table>
    <tr>
        <td><strong>{{ mb_strtoupper($business->line_of_business) }}</strong></td>
    </tr>

    <tr>
        <td><strong>{{ mb_strtoupper(__('accounting.purchases_book')) }}</strong></td>
    </tr>

    <tr></tr>

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
        <td style="width: 25%">
            <strong>{{ mb_strtoupper(__('business.nrc')) }}:</strong>
        </td>
        <td>{{ $business->nrc }}</td>
        <td></td>
        <td></td>
        <td>
            <strong>{{ mb_strtoupper(__('business.nit')) }}:</strong>
        </td>
        <td>{{ $business->nit }}</td>
        <td></td>
        <td></td>
    </tr>

    <thead>
        <tr>
            <th rowspan="2" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.no_tag')) }}</strong>
            </th>
            <th rowspan="2" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.date_of_issue')) }}</strong>
            </th>
            <th rowspan="2" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.document_no')) }}</strong>
            </th>
            <th rowspan="2" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('business.nrc')) }}</strong>
            </th>
            <th rowspan="2" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.dui_excluded_subject')) }}</strong>
            </th>
            <th rowspan="2" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('business.nit')) }}</strong>
            </th>
            <th rowspan="2" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.supplier_name')) }}</strong>
            </th>
            <th colspan="2" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.exempt_purchases')) }}</strong>
            </th>
            <th colspan="3" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.taxed_purchases')) }}</strong>
            </th>
            <th rowspan="2" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.total_purchases')) }}</strong>
            </th>
            <th rowspan="2" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.iva_withheld_third_parties')) }}</strong>
            </th>
            <th rowspan="2" style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.purchases_excluded_subjects')) }}</strong>
            </th>
        </tr>
        <tr>
            <th style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.local_interns')) }}</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.imports_or_internationals')) }}</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.local_interns')) }}</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.imports_or_internationals')) }}</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('document_type.fiscal_credit')) }}</strong>
            </th>
        </tr>
    </thead>
    <tbody>
        @php
        $total_internal = 0;
        $total_imports = 0;
        $total_fiscal_credit = 0;
        $total_purchases = 0;
        $total_withheld = 0;
        $total_internal_exempt = 0;
        $total_imports_exempt = 0;
        $total_excluded_subject = 0;
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
                {{ $item->nrc }}
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->dui }}
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->nit }}
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->supplier }}
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->internal_exempt }}
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->imports_exempt }}
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->internal }}
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->imports }}
            </td>
            <td style="border: 0.25px solid black;">
                @if ($item->organization_type != 'natural')
                {{ $item->fiscal_credit }}
                @endif
            </td>
            <td style="border: 0.25px solid black;">
                @if ($item->organization_type != 'natural')
                {{ $item->total_purchases }}
                @endif
            </td>
            <td style="border: 0.25px solid black;">
                @if ($item->organization_type != 'natural')
                {{ $item->withheld_amount }}
                @endif
            </td>
            <td style="border: 0.25px solid black;">
                @if ($item->organization_type == 'natural')
                    {{ $item->total_purchases }}
                @endif
            </td>
        </tr>
        @php
        $total_internal += $item->internal;
        $total_imports += $item->imports;
        $total_fiscal_credit += $item->fiscal_credit;
        if($item->organization_type != 'natural'){
            $total_purchases += $item->total_purchases;
        }else{
            $total_excluded_subject += $item->total_purchases;
        }
        $total_withheld += $item->withheld_amount;
        $total_internal_exempt += $item->internal_exempt;
        $total_imports_exempt += $item->imports_exempt;
        @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7" style="border: 0.25px solid black; text-align: center;">
                <strong>{{ mb_strtoupper(__('accounting.totals')) }}</strong>
            </td>
            <td style="border: 0.25px solid black; text-align: center;">
                @if ($total_internal_exempt > 0)
                <strong>{{ $total_internal_exempt }}</strong>
                @endif
            </td>
            <td style="border: 0.25px solid black; text-align: center;">
                @if ($total_imports_exempt > 0)
                <strong>{{ $total_imports_exempt }}</strong>
                @endif
            </td>
            <td style="border: 0.25px solid black; text-align: center;">
                @if ($total_internal > 0)
                <strong>{{ $total_internal }}</strong>
                @endif
            </td>
            <td style="border: 0.25px solid black; text-align: center;">
                @if ($total_imports > 0)
                <strong>{{ $total_imports }}</strong>
                @endif
            </td>
            <td style="border: 0.25px solid black; text-align: center;">
                <strong>{{ $total_fiscal_credit }}</strong>
            </td>
            <td style="border: 0.25px solid black; text-align: center;">
                <strong>{{ $total_purchases }}</strong>
            </td>
            <td style="border: 0.25px solid black; text-align: center;">
                <strong>{{ $total_withheld }}</strong>
            </td>
            <td style="border: 0.25px solid black; text-align: center;">
                <strong>{{ $total_excluded_subject }}</strong>
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