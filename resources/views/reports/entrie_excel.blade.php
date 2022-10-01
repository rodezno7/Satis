<table>
    {{-- Header --}}
    <tr>
        <td colspan="6" style="border-top: 0.25px double black;">
            <strong>"{{ mb_strtoupper( $business_name ) }}"</strong>
        </td>
    </tr>
    <tr>
        <td colspan="6" style="border-bottom: 0.25px double black;">
            <strong>{{ mb_strtoupper(__('accounting.daily_entries')) }}</strong>
        </td>
    </tr>
    <tr colspan="6"></tr>

    @foreach($datos as $entrie)

    {{-- Description --}}
    <tr>
        <td>@lang('accounting.entrie_number')</td>
        <td colspan="5">{{ $entrie->correlative }}</td>
    </tr>
    <tr>
        <td>@lang('accounting.entrie_date')</td>
        <td colspan="5">{{ date('Y-m-d', strtotime($entrie->date)) }}</td>
    </tr>
    <tr>
        <td>@lang('accounting.entrie_type')</td>
        <td colspan="5">{{ mb_strtoupper($entrie->type_entrie) }}</td>
    </tr>
    <tr>
        <td>@lang('accounting.general_concept')</td>
        <td colspan="5">{{ mb_strtoupper($entrie->description) }}</td>
    </tr>
    <tr colspan="6"></tr>

    {{-- Table --}}
    <tr>
        <th colspan="2" style="border-top: 0.25px solid black; border-bottom: 0.25px solid black;">
            <strong>{{ mb_strtoupper(__('accounting.accounting_account')) }}</strong>
        </th>
        <th style="border-top: 0.25px solid black; border-bottom: 0.25px solid black;"></th>
        <th style="border-top: 0.25px solid black; border-bottom: 0.25px solid black;">
            <strong>{{ mb_strtoupper(__('accounting.partial')) }}</strong>
        </th>
        <th style="border-top: 0.25px solid black; border-bottom: 0.25px solid black;">
            <strong>{{ mb_strtoupper(__('accounting.charges')) }}</strong>
        </th>
        <th style="border-top: 0.25px solid black; border-bottom: 0.25px solid black;">
            <strong>{{ mb_strtoupper(__('accounting.payments')) }}</strong>
        </th>
    </tr>

    @foreach($entrie->grupos_debe as $grupo)
    
    {{-- Accounting entries - Debit --}}
    <tr>
        {{-- Correlative --}}
        <td><strong>{{ $grupo->mayor }}</strong></td>

        {{-- Description --}}
        <td colspan="2"><strong>{{ $grupo->nombre }}</strong></td>

        {{-- Partial --}}
        <td></td>

        {{-- Charges --}}
        <td><strong>{{ $grupo->debe }}</strong></td>

        {{-- Payments --}}
        <td></td>
    </tr>

    @foreach($grupo->items as $item)

    {{-- Accounting entries details - Debit --}}
    <tr>
        {{-- Correlative --}}
        <td>{{ $item->code }}</td>

        {{-- Description --}}
        <td colspan="2">{{ $item->name }}</td>

        {{-- Partial --}}
        @if($enable_description_line != 1)
            @if($loop->last)
                <td style="border-bottom: 0.25px solid black;">{{ $item->valor }}</td>
            @else
                <td>{{ $item->valor }}</td>
            @endif
        @else
            <td></td>
        @endif

        {{-- Charges --}}
        <td></td>

        {{-- Payments --}}
        <td></td>
    </tr>

    {{-- Concept --}}
    @if($enable_description_line == 1)
    <tr>
        <td></td>

        <td colspan="2">{{ $item->description_line }}</td>

        {{-- Partial --}}
        @if($loop->last)
        <td style="border-bottom: 0.25px solid black;">{{ $item->valor }}</td>
        @else
        <td>{{ $item->valor }}</td>
        @endif

        <td></td>

        <td></td>
    </tr>
    @endif

    @endforeach

    @endforeach

    @foreach($entrie->grupos_haber as $grupo)

    {{-- Accounting entries - Credit --}}
    <tr>
        {{-- Correlative --}}
        <td><strong>{{ $grupo->mayor }}</strong></td>

        {{-- Description --}}
        <td colspan="2"><strong>{{ $grupo->nombre }}</strong></td>

        {{-- Partial --}}
        <td></td>

        {{-- Charges --}}
        <td></td>

        {{-- Payments --}}
        <td><strong> {{ $grupo->haber }}</strong></td>
    </tr>

    @foreach($grupo->items as $item)

    {{-- Accounting entries details - Credit --}}
    <tr>
        {{-- Correlative --}}
        <td>{{ $item->code }}</td>
        
        {{-- Description --}}
        <td colspan="2">{{ $item->name }}</td>

        {{-- Partial --}}
        @if($enable_description_line != 1)
            @if($loop->last)
                <td style="border-bottom: 0.25px solid black;">{{ $item->valor }}</td>
            @else
                <td>{{ $item->valor }}</td>
            @endif
        @else
            <td class="alnright td2"></td>
        @endif

        {{-- Charges --}}
        <td></td>

        {{-- Payments --}}
        <td></td>
    </tr>

    {{-- Concept --}}
    @if($enable_description_line == 1)
    <tr>
        <td></td>

        <td colspan="2">{{ $item->description_line }}</td>

        {{-- Partial --}}
        @if($loop->last)
        <td style="border-bottom: 0.25px solid black;">{{ $item->valor }}</td>
        @else
        <td>{{ $item->valor }}</td>
        @endif

        <td></td>

        <td></td>
    </tr>
    @endif

    @endforeach

    @endforeach

    {{-- Total --}}
    <tr>
        <td></td>
        <td></td>
        <td><strong>{{ mb_strtoupper(__('accounting.total_charges_and_payments')) }}</strong></td>
        <td></td>
        <td style="border-bottom: 0.25px double black;"><strong>{{ $entrie->total_debe }}</strong></td>
        <td style="border-bottom: 0.25px double black;"><strong>{{ $entrie->total_haber }}</strong></td>
    </tr>

    {{-- Footer --}}
    <tr></tr>
    <tr></tr>
    <tr>
        <td style="border-top: 0.25px solid black;">@lang('accounting.made_by')</td>
        <td></td>
        <td style="border-top: 0.25px solid black;">@lang('accounting.reviewed_by')</td>
        <td></td>
        <td style="border-top: 0.25px solid black;">@lang('accounting.approved_by')</td>
        <td></td>
    </tr>

    @endforeach
</table>