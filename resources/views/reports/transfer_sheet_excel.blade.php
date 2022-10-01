<table>
    {{-- Header --}}
    <tr>
        @if ($enable_signature_column == 1)
        <td colspan="7">
        @else
        <td colspan="5">
        @endif
            <strong>{{ mb_strtoupper($business->business_full_name) }}</strong>
        </td>
    </tr>
    <tr>
        @if ($enable_signature_column == 1)
        <td colspan="7">
        @else
        <td colspan="5">
        @endif
            <strong>{{ mb_strtoupper(__('lab_order.transfers_sheet')) }}</strong>
        </td>
    </tr>

    <tr></tr>

    {{-- Table --}}
    <thead>
        <tr>
            <th style="border: 0.25px solid black;">
                <strong>No.</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>{{ __('accounting.location') }}</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>{{ __('payment.transfer_ref_no') }}</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>{{ __('lang_v1.quantity') }}</strong>
            </th>
            <th style="border: 0.25px solid black;">
                <strong>{{ __('accounting.description') }}</strong>
            </th>
            @if ($enable_signature_column == 1)
            <th colspan="2" style="border: 0.25px solid black;">
                <strong>{{ __('report.received') }}</strong>
            </th>
            @endif
        </tr>
    </thead>
    
    <tbody>
        @foreach ($lines as $line)
        @php
        $correlative = $loop->iteration;
        $index_reference = 0;
        $counts = array_count_values(array_column($line->toArray(), 'reference'));
        @endphp
        @foreach ($line as $key => $item)
        <tr>
            @if ($key == 0 || $key % $line->count() == 0)
            <td style="border: 0.25px solid black; text-align: center;" rowspan="{{ $line->count() }}">
                {{ $correlative }}
            </td>

            <td style="border: 0.25px solid black; text-align: center;" rowspan="{{ $line->count() }}">
                {{ $item->location }}
            </td>
            @endif

            @if ($index_reference % $counts[$item->reference] == 0)
            <td style="border: 0.25px solid black; text-align: center;" rowspan="{{ $counts[$item->reference] }}">
                {{ $item->reference }}
            </td>
            @endif
            
            <td style="border: 0.25px solid black; text-align: center;">
                {{ $item->quantity }}
            </td>
            
            <td style="border: 0.25px solid black;">
                {{ $item->description }}
            </td>
            
            @if ($enable_signature_column == 1)    
            <td style="border: 0.25px solid black;"></td>
            <td style="border: 0.25px solid black;"></td>
            @endif
        </tr>
        @php
        $index_reference++;

        if ($index_reference % $counts[$item->reference] == 0) {
            $index_reference = 0;
        }
        @endphp
        @endforeach
        @endforeach
    </tbody>

    <tfoot>
        <tr></tr>
        <tr></tr>
        <tr></tr>

        <tr>
            <td></td>

            <td colspan="2" style="border-bottom: 0.25px solid black;"></td>

            <td></td>

            <td style="border-bottom: 0.25px solid black;"></td>

            <td></td>

            <td></td>
        </tr>

        @if (! empty($delivers) || ! empty($receives))
        <tr>
            <td></td>

            <td colspan="2" style="text-align: center;">
                {{ $delivers }}
            </td>

            <td></td>

            <td style="text-align: center;">
                {{ $receives }}
            </td>

            <td></td>

            <td></td>
        </tr>
        @endif

        <tr>
            <td></td>

            <td colspan="2" style="text-align: center;">
                {{ __('report.delivers')  }}
            </td>

            <td></td>

            <td style="text-align: center;">
                {{ __('report.receives')  }}
            </td>

            <td></td>

            <td></td>
        </tr>
    </tfoot>
</table>