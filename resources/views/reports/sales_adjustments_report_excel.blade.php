<table>
    {{-- Header --}}
    <tr>
        <td><strong>{{ mb_strtoupper($business->line_of_business) }}</strong></td>
    </tr>
    <tr>
        <td><strong>{{ mb_strtoupper(__('report.consumption_report')) }}</strong></td>
    </tr>
    <tr></tr>
    <tr>
        <td><strong>{{ mb_strtoupper(__('accounting.location')) }}:</strong></td>
        <td>{{ $location->name }}</td>
        <td></td>
        <td></td>
        <td><strong>{{ mb_strtoupper(__('accounting.month')) }}:</strong></td>
        <td>{{ $month_name }}</td>
    </tr>
    <tr></tr>

    {{-- Table --}}
    <thead>
        <tr>
            <th style="border: 0.25px solid black;">
                <strong>SKU</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>@lang('business.product')</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>@lang('sale.unit_price')</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>@lang('report.unit_cost')</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>@lang('report.total_unit_sold')</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>@lang('report.input_adjustment')</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>@lang('report.output_adjustment')</strong>
            </th>
        </tr>
    </thead>

    <tbody>
        @php
        $total_total_sold = [];
        $total_input_adjustment = [];
        $total_output_adjustment = [];
        @endphp
        @foreach ($query as $item)
        <tr>
            <td style="border: 0.25px solid black;">
                {{ $item->sku }}
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->product }}
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->unit_price }}
            </td>
            <td style="border: 0.25px solid black;">
                {{ $item->unit_cost }}
            </td>
            <td style="border: 0.25px solid black;">
                @if ($item->total_sold)
                {{ @num_format($item->total_sold) }}
                @else
                0.00
                @endif
            </td>
            <td style="border: 0.25px solid black;">
                @if ($item->input_adjustment)
                {{ @num_format($item->input_adjustment) }}
                @else
                0.00
                @endif
            </td>
            <td style="border: 0.25px solid black;">
                @if ($item->output_adjustment)
                {{ @num_format($item->output_adjustment) }}
                @else
                0.00
                @endif
            </td>
        </tr>
        @php
        if (empty($item->unit)) {
            $item->unit = 'none';
        }

        if (! empty($item->total_sold)) {
            if (! array_key_exists($item->unit, $total_total_sold)) {
                $total_total_sold[$item->unit] = $item->total_sold;
            } else {
                $total_total_sold[$item->unit] += $item->total_sold;
            }
        }

        if (! empty($item->input_adjustment)) {
            if (! array_key_exists($item->unit, $total_input_adjustment)) {
                $total_input_adjustment[$item->unit] = $item->input_adjustment;
            } else {
                $total_input_adjustment[$item->unit] += $item->input_adjustment;
            }
        }

        if (! empty($item->output_adjustment)) {
            if (! array_key_exists($item->unit, $total_output_adjustment)) {
                $total_output_adjustment[$item->unit] = $item->output_adjustment;
            } else {
                $total_output_adjustment[$item->unit] += $item->output_adjustment;
            }
        }
        @endphp
        @endforeach
    </tbody>

    <tfoot>
        <tr style="border: 0.25px solid black;">
            <td colspan="4" style="text-align: center; border: 0.25px solid black;">
                <strong>{{ mb_strtoupper(__('sale.total')) }}</strong>
            </td>
            <td style="border: 0.25px solid black;">
                @foreach ($total_total_sold as $unit => $quantity)
                {{ @num_format($quantity) }}
                @if ($unit != 'none')
                {{ $unit }}
                @endif
                <br>
                @endforeach
            </td>
            <td style="border: 0.25px solid black;">
                @foreach ($total_input_adjustment as $unit => $quantity)
                {{ @num_format($quantity) }}
                @if ($unit != 'none')
                {{ $unit }}
                @endif
                <br>
                @endforeach
            </td>
            <td style="border: 0.25px solid black;">
                @foreach ($total_output_adjustment as $unit => $quantity)
                {{ @num_format($quantity) }}
                @if ($unit != 'none')
                {{ $unit }}
                @endif
                <br>
                @endforeach
            </td>
        </tr>
    </tfoot>
</table>