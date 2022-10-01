<table>
    {{-- Header --}}
    <tr>
        <td>
            <strong>{{ mb_strtoupper($business->business_full_name) }}</strong>
        </td>
    </tr>

    <tr>
        <td>
            {{ mb_strtoupper(__('report.range_cost_of_sale_detail', ['from' => @format_date($start), 'to' => @format_date($end)])) }}
        </td>
    </tr>

    <tr></tr>

    {{-- Table --}}
    <thead>
        <tr>
            <th style="width: 10%; border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.date')) }}</strong>
            </th>
            <th style="width: 10%; border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.code')) }}</strong>
            </th>
            <th style="width: 25%; border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.description')) }}</strong>
            </th>
            <th style="width: 20%; border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('credit.observations')) }}</strong>
            </th>
            <th style="width: 5%; border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.type')) }}</strong>
            </th>
            <th style="width: 10%; border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.reference')) }}</strong>
            </th>
            <th style="width: 10%; border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.inflow')) }}</strong>
            </th>
            <th style="width: 10%; border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.outflow')) }}</strong>
            </th>
            <th style="width: 10%; border-bottom: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('accounting.annulled')) }}</strong>
            </th>
        </tr>
    </thead>
    
    <tbody>
        @php
        $total_input = 0;
        $total_output = 0;
        $total_annulled = 0;
        @endphp
        @foreach ($query as $item)
        <tr>
            <td>{{ $item->transaction_date }}</td>
            <td>{{ $item->code }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->observation }}</td>
            <td>{{ $item->document_type }}</td>
            <td>{{ $item->reference }}</td>
            <td>
                {{ $item->input }}
            </td>
            <td>
                {{ $item->output }}
            </td>
            <td>
                {{ $item->annulled }}
            </td>
        </tr>
        @php
        $total_input += $item->input;
        $total_output += $item->output;
        $total_annulled += $item->annulled;
        @endphp
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="6">&nbsp;</td>
            <td style="border-top: 0.25px solid black; border-bottom: 0.25px double black;">
                <strong>{{ $total_input }}</strong>
            </td>
            <td style="border-top: 0.25px solid black; border-bottom: 0.25px double black;">
                <strong>{{ $total_output }}</strong>
            </td>
            <td style="border-top: 0.25px solid black; border-bottom: 0.25px double black;">
                <strong>{{ $total_annulled }}</strong>
            </td>
        </tr>
    </tfoot>
</table>