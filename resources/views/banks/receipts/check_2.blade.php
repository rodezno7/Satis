<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('accounting.check')</title>

    <style>
        body {
            font-family: 'Courier', sans-serif;
            color: #000000;
            font-size: 8.4pt;
            margin: 0;
            padding: 0;
            font-weight: bold;
        }

        .alnright { text-align: right; }
        .alnleft { text-align: left; }
        .alncenter { text-align: center; }

        @page {
            margin: 0;
            padding: 0;
        }

        div.check-content {
            font-family: 'Courier', sans-serif;
            font-size: 9.3pt;
            width: 17.0cm;
            height: 8cm;
            position: relative;
            margin: 0;
        }

        div.entrie {
            width: {{ $entrie_width }}cm;
            position: absolute;
            left: {{ $entrie_left }}cm;
            top: {{ $entrie_top }}cm;
        }

        div.check-content div.place_date {
            position: absolute;
            left: {{ $place_date_x }}cm;
            top: {{ $place_date_y }}cm;
        }

        div.check-content div.amount {
            position: absolute;
            left: {{ $amount_x }}cm;
            top: {{ $amount_y }}cm;
        }

        div.check-content div.person {
            position: absolute;
            left: {{ $person_x }}cm;
            top: {{ $person_y }}cm;
        }

        div.check-content div.value_letters {
            position: absolute;
            left: {{ $value_letters_x }}cm;
            top: {{ $value_letters_y }}cm;
        }

        div.check-content div.asterisks {
            position: absolute;
            left: {{ $asterisks_x }}cm;
            top: {{ $asterisks_y }}cm;
        }

        div.check-content-2 {
            width: 17.0cm;
            height: 8cm;
            position: relative;
            margin: 0;
            /* border: 1px solid black; */
        }

        div.check-content-2 div.place_date {
            position: absolute;
            left: {{ $place_date_x }}cm;
            top: {{ $place_date_y }}cm;
        }

        div.check-content-2 div.amount {
            position: absolute;
            left: {{ $amount_x }}cm;
            top: {{ $amount_y }}cm;
        }

        div.check-content-2 div.person {
            position: absolute;
            left: {{ $person_x }}cm;
            top: {{ $person_y }}cm;
        }

        div.check-content-2 div.value_letters {
            position: absolute;
            left: {{ $value_letters_x }}cm;
            top: {{ $value_letters_y }}cm;
        }

        div.check-content-2 div.asterisks {
            position: absolute;
            left: {{ $asterisks_x }}cm;
            top: {{ $asterisks_y }}cm;
        }

        div.check-content-2 div.pay-to {
            position: absolute;
            left: 0.2cm;
            top: {{ $person_y - 0.4 }}cm;
        }

        div.check-content-2 div.pay-to-2 {
            position: absolute;
            left: 0.2cm;
            top: {{ $person_y }}cm;
        }

        div.check-content-2 div.sum-of {
            position: absolute;
            left: 0.2cm;
            top: {{ $value_letters_y }}cm;
        }

        .bt { border-top: 0.25px solid black; }
        .bb { border-bottom: 0.25px solid black; }
        .br { border-right: 0.25px solid black; }
        .bl { border-left: 0.25px solid black; }
        .no-bt { border-top: 0.25px solid white; }
        .no-bb { border-bottom: 0.25px solid white; }
        .no-br { border-right: 0.25px solid white; }
        .no-bl { border-left: 0.25px solid white; }

        div.page-break {
            page-break-before: always;
        }

        .content {
            position: relative;
            /* margin: 1.2cm 1.2cm; */
            width: 100%;
            height: 25cm;
        }

        .check-border {
            border: 0.25px solid black;
            height: 60mm;
            padding: 5px;
            margin-bottom: 5px;
        }

        .concept {
            border: 0.25px solid black;
            height: 10mm;
            padding: 5px;
            margin-bottom: 5px;
            position: relative;
            margin-top: 0.25cm;
        }

        .concept .concept-label {
            position: absolute;
            left: 0.2cm;
            top: 0.2cm;
        }

        .concept .concept-text {
            position: absolute;
            left: 2.0cm;
            top: 0.2cm;
        }

        p {
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .table1 {
            border: 0px;
            border-collapse: collapse;
        }

        .table2 {
            border-collapse: collapse;
            border: 0.25px solid black;
        }

        .td2 {
            border: 0px;
        }

        .td4 {
            border-bottom: 0.25px solid black;
            border-top: 0.25px solid black;
            border-left: 0.25px solid black;
            border-right: 0.25px solid black;
            padding: 4px;
            text-align: center;
        }

        th, td {
            border: 0.25px solid black;
            text-align: left;
        }

        th {
            padding: 4px;
        }

        td {
            padding: 2px;
        }
        .footer {
            bottom: 3.5cm;
            position: absolute;
        }

        .pl-15 {
            padding-left: 15px;
        }
  </style>
</head>

<body>
    @if ($print == 1)

    {{-- Page 1 --}}
    <div class="content">
        <div class="check-content">
            <div class="place_date">{{ $place_date }}</div>
            <div class="amount">{{ $amount }}</div>
            <div class="person">{{ $person_check }}</div>
            <div class="value_letters">{{ $letters_check }}</div>
            <div class="asterisks">{{ $asterisks }}</div>
        </div>
    </div>

    @else

    {{-- Page 2 --}}
    <div>
        <div class="content">
            <div class="check-content">
                <div class="place_date">{{ $place_date }}</div>
                <div class="amount">{{ $amount }}</div>
                <div class="person">{{ $person_check }}</div>
                <div class="value_letters">{{ $letters_check }}</div>
                <div class="asterisks">{{ $asterisks }}</div>
            </div>

            @foreach($datos as $entrie)

            <div class="entrie">
                <table class="table1" width="100%">
                    <tr>
                        <td class="td2" @if ($format == 'cuscatlan') style="padding-left: 1.5cm;" @endif>
                            @if ($flag_labels)
                                <strong>{{  __('accounting.name') }}:</strong>
                            @endif
                            @if ($format == 'constelacion')
                                &nbsp;&nbsp;&nbsp; {{ $description }}
                            @else
                                {{ $description }}
                            @endif
                            @if ($format == 'agricola' || $format == 'hipotecario')
                                <br><br><br>
                            @elseif ($format == 'cuscatlan')
                                <br><br>
                            @endif
                        </td>

                        @if ($show_check)
                        <td class="alnright td2">
                            @if ($flag_labels)
                                <strong>{{  __('report.check_no') }}:</strong>
                            @endif
                            {{ $check_number }}
                        </td>
                        @endif
                    </tr>
                </table>

                {{-- Table --}}
                <table style="width: 100%;" class="@if ($show_table) table2 @else table1 @endif">
                    <thead>
                    <tr>
                        <th style="width: @if ($format == 'agricola' || $format == 'hipotecario') 2cm @elseif ($format == 'cuscatlan') 2.3cm @else 12% @endif" class="alncenter @if (! $show_table) td2 @endif">
                            @if ($show_table)
                            {{ mb_strtoupper(__('accounting.account')) }}
                            @else
                            &nbsp;
                            @endif
                        </th>
                        <th style="width: @if ($format == 'agricola' || $format == 'hipotecario') 6cm @elseif ($format == 'cuscatlan') 6.5cm @else 64% @endif" class="alncenter @if (! $show_table) td2 @endif">
                            @if ($show_table)
                            {{ mb_strtoupper(__('accounting.description')) }}
                            @else
                            &nbsp;
                            @endif
                        </th>
                        <th style="width: @if ($format == 'agricola' || $format == 'hipotecario') 2cm @elseif ($format == 'cuscatlan') 1.0cm @else 12% @endif" class="alncenter @if (! $show_table) td2 @endif">
                            @if ($show_table)
                            <span @if ($format == 'cuscatlan') style="padding-right: 1cm;" @endif>
                            {{ mb_strtoupper(__('accounting.debit')) }}
                            </span>
                            @else
                            &nbsp;
                            @endif
                        </th>
                        <th style="width: @if ($format == 'agricola' || $format == 'hipotecario') 2cm @elseif ($format == 'cuscatlan') 2cm @else 12% @endif" class="alncenter @if (! $show_table) td2 @endif">
                            @if ($show_table)
                            <span @if ($format == 'cuscatlan') style="padding-right: 1cm;" @endif>
                            {{ mb_strtoupper(__('accounting.credit')) }}
                            </span>
                            @else
                            &nbsp;
                            @endif
                        </th>
                    </tr>
                    </thead>
            
                    @foreach($entrie->grupos_debe as $grupo)
                
                    @foreach($grupo->items as $item)
                
                    @if(number_format($item->valor, 2) >= 0.00)
                    @php($partial_debit_label = number_format($item->valor, 2))
                    @else
                    @php($partial_debit_label = "(".number_format(($item->valor * -1), 2).")")
                    @endif
                
                    {{-- Accounting entries details - Debit --}}
                    <tr>
                        {{-- Correlative --}}
                        <td class="no-bb @if (! $show_table) td2 @endif">
                            {{ $item->code }}
                        </td>
                        
                        {{-- Description --}}
                        <td class="no-bb @if (! $show_table) td2 @endif">
                            {{ $item->name }}
                        </td>
                
                        {{-- Debit --}}
                        <td class="alnright no-bb @if (! $show_table) td2 @endif">
                            <span @if ($format == 'cuscatlan') style="padding-right: 1cm;" @endif>
                            {{ $partial_debit_label }}
                            </span>
                        </td>
                
                        {{-- Credit --}}
                        <td class="alnright no-bb @if (! $show_table) td2 @endif">
                            &nbsp;
                        </td>
                    </tr>

                    @if($enable_description_line == 1 && ! empty($item->description_line))
                    <tr>
                        {{-- Correlative --}}
                        <td class="no-bb @if (! $show_table) td2 @endif">
                            &nbsp;
                        </td>
                        
                        {{-- Description --}}
                        <td class="no-bb @if (! $show_table) td2 @endif">
                            {{ $item->description_line }}
                        </td>
                
                        {{-- Debit --}}
                        <td class="alnright no-bb @if (! $show_table) td2 @endif">
                            &nbsp;
                        </td>
                
                        {{-- Credit --}}
                        <td class="alnright no-bb @if (! $show_table) td2 @endif">
                            &nbsp;
                        </td>
                    </tr>
                    @endif
                
                    @endforeach
                    @endforeach
                
                    @foreach($entrie->grupos_haber as $grupo)
                
                    @foreach($grupo->items as $item)
                    @if(number_format($item->valor, 2) >= 0.00)
                    @php($partial_credit_label = number_format($item->valor, 2))
                    @else
                    @php($partial_credit_label = "(".number_format(($item->valor * -1), 2).")")
                    @endif
                
                    {{-- Accounting entries details - Credit --}}
                    <tr>
                        {{-- Correlative --}}
                        <td class="@if ($enable_description_line == 1) no-bb @else @if ($loop->parent->last) bb @else no-bb @endif @endif @if (! $show_table) td2 @endif">
                            {{ $item->code }}
                        </td>
                        
                        {{-- Description --}}
                        <td class="@if ($enable_description_line == 1) no-bb @else @if ($loop->parent->last) bb @else no-bb @endif @endif @if (! $show_table) td2 @endif">
                            {{ $item->name }}
                        </td>
                
                        {{-- Debit --}}
                        <td class="alnright @if ($enable_description_line == 1) no-bb @else @if ($loop->parent->last) bb @else no-bb @endif @endif @if (! $show_table) td2 @endif">
                            &nbsp;
                        </td>
                
                        {{-- Credit --}}
                        <td class="alnright @if ($enable_description_line == 1) no-bb @else @if ($loop->parent->last) bb @else no-bb @endif @endif @if (! $show_table) td2 @endif">
                            <span @if ($format == 'cuscatlan') style="padding-right: 1cm;" @endif>{{ $partial_credit_label }}</span>
                        </td>
                    </tr>

                    @if($enable_description_line == 1 && ! empty($item->description_line))
                    <tr>
                        {{-- Correlative --}}
                        <td class="@if ($loop->parent->last) bb @else no-bb @endif @if (! $show_table) td2 @endif">
                            &nbsp;
                        </td>
                        
                        {{-- Description --}}
                        <td class="@if ($loop->parent->last) bb @else no-bb @endif @if (! $show_table) td2 @endif">
                            {{ $item->description_line }}
                        </td>
                
                        {{-- Debit --}}
                        <td class="alnright @if ($loop->parent->last) bb @else no-bb @endif @if (! $show_table) td2 @endif">
                            &nbsp;
                        </td>
                
                        {{-- Credit --}}
                        <td class="alnright @if ($loop->parent->last) bb @else no-bb @endif @if (! $show_table) td2 @endif">
                            &nbsp;
                        </td>
                    </tr>
                    @endif
                
                    @endforeach
                    @endforeach
                
                    @if(number_format($entrie->total_debe, 2) >= 0.00)
                    @php($total_debit_label = number_format($entrie->total_debe, 2))
                    @else
                    @php($total_debit_label = "(".number_format(($entrie->total_debe * -1), 2).")")
                    @endif
                
                    @if(number_format($entrie->total_haber, 2) >= 0.00)
                    @php($total_credit_label = number_format($entrie->total_haber, 2))
                    @else
                    @php($total_credit_label = "(".number_format(($entrie->total_haber * -1), 2).")")
                    @endif
                
                    {{-- Total --}}
                    <tr>
                        <td colspan="2" class="alncenter br bt @if (! $show_table) td2 @endif">
                            @if ($show_table)
                            <strong>{{ mb_strtoupper(__('report.totals')) }}</strong>
                            @endif
                        </td>
                        <td class="alnright br bt @if (! $show_table) td2 @endif">
                            <span @if ($format == 'cuscatlan') style="padding-right: 1cm;" @endif>
                            {{ $total_debit_label }}
                            </span>
                        </td>
                        <td class="alnright bt @if (! $show_table) td2 @endif">
                            <span @if ($format == 'cuscatlan') style="padding-right: 1cm;" @endif>{{ $total_credit_label }}</span>
                        </td>
                    </tr>
                </table>
            </div>

            @endforeach
        </div>
    </div>
    @endif
</body>
</html>