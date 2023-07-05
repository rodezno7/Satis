<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('accounting.check')</title>

    <style>
        body {
            font-family: sans-serif;
            color: #000000;
            font-size: 9pt;
            margin: 0;  
            padding: 0;
        }

        .alnright { text-align: right; }
        .alnleft { text-align: left; }
        .alncenter { text-align: center; }

        @page {
            margin: 0;
            padding: 0;
        }

        div.check-content {
            width: 15.25cm;
            height: 7cm;
            position: relative;
            margin: 0 auto;
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
            margin: 1.2cm 1.2cm;
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
            padding: 4px;
            text-align: left;
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
    <div class="content">
        <p class="alncenter" style="font-size: 11pt;">
            <strong>{{ mb_strtoupper($business_name) }}</strong>
        </p>
        <p class="alncenter" style="font-size: 10pt;">
            <strong>{{ mb_strtoupper(__('report.proof_of_transfer')) }}</strong>
        </p>
        <p class="alncenter" style="font-size: 10pt;">
            <strong>{{ mb_strtoupper($bank_name) }}</strong>
        </p>
        <p style="font-size: 10pt;">
            <strong>{{ $person }}</strong>
        </p>
        <p style="font-size: 10pt;">
            <strong>{{ $place_date }}</strong>
        </p>
        <p style="font-size: 10pt;">
            <strong>MONTO: $ {{ $amount }}</strong>
        </p>
        <p style="font-size: 10pt;">
            <strong>{{mb_strtoupper(__('report.concept'))}}: {{ $description }}</strong>
        </p>
        @foreach($datos as $entrie)

        <table class="table1" style="margin-top: 15px;" width="100%">
            <tr>
                <td class="td2">
                    <strong>{{ $entrie_type }} #:</strong> {{ $entrie_no }}
                </td>

                <td class="alnright td2">
                    <strong>{{  __('report.entrie_no') }}:</strong> {{ $reference }}
                </td>
            </tr>
        </table>
        <table style="width: 100%;" class="table2">
            <thead>
            <tr>
                <th style="width: 10%;" class="alncenter">{{ mb_strtoupper(__('accounting.code')) }}</th>
                <th style="width: 60%;" class="alncenter" colspan="2">{{ mb_strtoupper(__('accounting.description')) }}</th>
                <th style="width: 10%;" class="alncenter">{{ mb_strtoupper(__('accounting.partial')) }}</th>
                <th style="width: 10%;" class="alncenter">{{ mb_strtoupper(__('report.debit')) }}</th>
                <th style="width: 10%;" class="alncenter">{{ mb_strtoupper(__('report.credit')) }}</th>
            </tr>
            </thead>
    
            @foreach($entrie->grupos_debe as $grupo)
            
            @if(number_format($grupo->debe, 2) >= 0.00)
            @php($group_debit_label = number_format($grupo->debe, 2))
            @else
            @php($group_debit_label = "(".number_format(($grupo->debe * -1), 2).")")
            @endif
            
            {{-- Accounting entries - Debit --}}
            <tr>
                {{-- Correlative --}}
                <td class="no-bb">{{ $grupo->mayor }}</td>
        
                {{-- Description --}}
                <td class="no-bb" colspan="2">{{ $grupo->nombre }}</td>
        
                {{-- Concept --}}
        
                {{-- Partial --}}
                <td class="alnright no-bb">0.00</td>
        
                {{-- Debit --}}
                <td class="alnright no-bb">{{ $group_debit_label }}</td>
        
                {{-- Credit --}}
                <td class="alnright no-bb">0.00</td>
            </tr>
        
            @foreach($grupo->items as $item)
        
            @if(number_format($item->valor, 2) >= 0.00)
            @php($partial_debit_label = number_format($item->valor, 2))
            @else
            @php($partial_debit_label = "(".number_format(($item->valor * -1), 2).")")
            @endif
        
            {{-- Accounting entries details - Debit --}}
            <tr>
                {{-- Correlative --}}
                <td class="no-bb">{{ $item->code }}</td>
                
                {{-- Description --}}
                <td class="no-bb" colspan="2">{{ $item->name }}</td>
        
                {{-- Concept --}}
                {{-- <td class="no-bl no-bb" colspan="2">{{ $item->description_line }}</td> --}}
        
                {{-- Partial --}}
                <td class="alnright no-bb">{{ $partial_debit_label }}</td>
        
                {{-- Debit --}}
                <td class="alnright no-bb">0.00</td>
        
                {{-- Credit --}}
                <td class="alnright no-bb">0.00</td>
            </tr>

            @if($enable_description_line == 1)
            <tr>
                {{-- Correlative --}}
                <td class="no-bb">&nbsp;</td>
                
                {{-- Description --}}
                <td class="no-bb" colspan="2">{{ $item->description_line }}</td>
        
                {{-- Partial --}}
                <td class="alnright no-bb">&nbsp;</td>
        
                {{-- Debit --}}
                <td class="alnright no-bb">&nbsp;</td>
        
                {{-- Credit --}}
                <td class="alnright no-bb">&nbsp;</td>
            </tr>
            @endif
        
            @endforeach
            @endforeach
        
            @foreach($entrie->grupos_haber as $grupo)
            
            @if(number_format($grupo->haber, 2) >= 0.00)
            @php($group_credit_label = number_format($grupo->haber, 2))
            @else
            @php($group_credit_label = "(".number_format(($grupo->haber * -1), 2).")")
            @endif
        
            {{-- Accounting entries - Credit --}}
            <tr>
                {{-- Correlative --}}
                <td class="no-bb">{{ $grupo->mayor }}</td>
        
                {{-- Concept --}}
                <td class="no-bb" colspan="2">{{ $grupo->nombre }}</td>
        
                {{-- Partial --}}
                <td class="alnright no-bb">0.00</td>
        
                {{-- Debit --}}
                <td class="alnright no-bb">0.00</td>
        
                {{-- Credit --}}
                <td class="alnright no-bb">{{ $group_credit_label }}</td>
            </tr>
        
            @foreach($grupo->items as $item)
            @if(number_format($item->valor, 2) >= 0.00)
            @php($partial_credit_label = number_format($item->valor, 2))
            @else
            @php($partial_credit_label = "(".number_format(($item->valor * -1), 2).")")
            @endif
        
            {{-- Accounting entries details - Credit --}}
            <tr>
                {{-- Correlative --}}
                <td class="@if ($enable_description_line == 1) no-bb @else @if ($loop->last) bb @else no-bb @endif @endif">
                    {{ $item->code }}
                </td>
                
                {{-- Description --}}
                <td class="@if ($enable_description_line == 1) no-bb @else @if ($loop->last) bb @else no-bb @endif @endif" colspan="2">
                    {{ $item->name }}
                </td>
        
                {{-- Partial --}}
                <td class="alnright @if ($enable_description_line == 1) no-bb @else @if ($loop->last) bb @else no-bb @endif @endif">
                    {{ $partial_credit_label }}
                </td>
        
                {{-- Debit --}}
                <td class="alnright @if ($enable_description_line == 1) no-bb @else @if ($loop->last) bb @else no-bb @endif @endif">
                    0.00
                </td>
        
                {{-- Credit --}}
                <td class="alnright @if ($enable_description_line == 1) no-bb @else @if ($loop->last) bb @else no-bb @endif @endif">
                    0.00
                </td>
            </tr>

            @if($enable_description_line == 1)
            <tr>
                {{-- Correlative --}}
                <td class="@if ($loop->last) bb @else no-bb @endif">&nbsp;</td>
                
                {{-- Concep --}}
                <td class="@if ($loop->last) bb @else no-bb @endif" colspan="2">{{ $item->description_line }}</td>
        
                {{-- Partial --}}
                <td class="alnright @if ($loop->last) bb @else no-bb @endif">&nbsp;</td>
        
                {{-- Debit --}}
                <td class="alnright @if ($loop->last) bb @else no-bb @endif">&nbsp;</td>
        
                {{-- Credit --}}
                <td class="alnright @if ($loop->last) bb @else no-bb @endif">&nbsp;</td>
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
                <td colspan="3" class="alncenter br bt"><strong>{{ mb_strtoupper(__('report.totals')) }}</strong></td>
                <td class="alnright br bt">0.00</td>
                <td class="alnright br bt">{{ $total_debit_label }}</td>
                <td class="alnright bt">{{ $total_credit_label }}</td>
            </tr>
        </table>

        @endforeach
        <table class="table1 footer" width="100%">
            <tr>
                <td width="23%"></td>
                <td width="23%"></td>
                <td width="23%"></td>
                <td width="31%" class="no-bt no-bb no-br pl-15">
                    {{ __('lang_v1.received') }}:
                    <br>
                    <br>
                    F. ____________________________
                    <br>
                    <br>
                    {{ __('accounting.name') }}:
                </td>
            </tr>

            <tr>
                <td class="alncenter">{{ __('report.elaborated') }}</td>
                <td class="alncenter">{{ __('report.reviewed') }}</td>
                <td class="alncenter">{{ __('report.authorized') }}</td>
                <td class="no-bb no-br pl-15">{{ __('business.dui') }}:</td>
            </tr>

            <tr>
                <td class="no-bl no-br no-bb"></td>
                <td class="no-br no-bb"></td>
                <td class="no-br no-bb"></td>
                <td class="alncenter no-br no-bb">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sello</td>
            </tr>
        </table>
    </div>
</body>
</html>