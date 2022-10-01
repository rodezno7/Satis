<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@lang('accounting.entries_menu')</title>
    <style>
        body
        {
            counter-reset: page {{ $numero }};
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000000;
            font-size: 8pt;
        }
        h3, h4
        {
            text-align: center;
        }
        .print
        {
            page-break-after: always;
        }
        .print:last-child
        {
            page-break-after: auto;
        }
        .table1
        {
            border: 0px;
            border-collapse: collapse;
        }
        .table2
        {
            border-collapse: collapse;
            border: 0.25px solid black;
        }
        .td2
        {
            border: 0px;            
        }
        .td4
        {
            border-bottom: 0.25px solid black;
            border-top: 0.25px solid black;
            border-left: 0.25px solid black;
            border-right: 0.25px solid black;
            padding: 4px;
            text-align: left;
        } 
        .td3
        {
            border-bottom: 0.25px solid black;
            border-top: none;
            border-left: 0.25px solid black;
            border-right: 0.25px solid black;
            padding: 4px;
            text-align: left;
        }
        .td5
        {
            border-top: 1px solid black;
            border-bottom: 1px solid black;
            border-left: none;
            border-right: none;
            text-align: left;
        }
        td
        {
            border-bottom: none;
            border-top: none;
            border-left: 0.25px solid black;
            border-right: 0.25px solid black;
            padding: 4px;
            text-align: left;
        }
        th
        {
            border: 0.25px solid black;
            padding: 4px;
            text-align: center;
        }
        .alnright { text-align: right; }
        .alnleft { text-align: left; }
        .alncenter { text-align: center; }
        @page{
            margin-bottom: 75px;
        }

        #header,
        #footer {
          position: fixed;
          left: 0;
          right: 0;
          color: #000000;
          font-size: 0.9em;
      }
      #header {
          top: 0;
          border-bottom: 0.1pt solid #aaa;
      }
      #footer {
          bottom: 0;
          border-top: 0.1pt solid #aaa;
      }
      .page-number:before {
          content: "Página " counter(page);
      }
      .bb {
          border-bottom: 1px solid black;
      }
      .bbd {
        border-bottom: 1px double black;
      }
      hr {
          border: 0;
          height: 1px;
          background-color: black;
      }
  </style>
</head>
<body>
  <div id="footer">
      <div class="page-number"></div>
  </div>
  {{-- Header --}}
  <hr><hr>
  <table class="table1" style="width: 100%;">
    <tr>
        <td class="td2"  style="width: 100%;">
            <strong><center>"{{ mb_strtoupper( $business_name ) }}"</center></strong>
            <strong><center>{{ mb_strtoupper(__('accounting.daily_entries')) }}</center></strong>
        </td>
    </tr>
  </table>
  <hr><hr>

  @foreach($datos as $entrie)
  
  {{-- Description --}}
  <table style="width: 100%;">
      <tr>
          <td class="td2" style="width: 15%;">@lang('accounting.entrie_number')</td>
          <td class="td2" style="width: 85%;">{{ $entrie->correlative }}</td>
      </tr>
      <tr>
          <td class="td2">@lang('accounting.entrie_date')</td>
          <td class="td2">{{ date('Y-m-d', strtotime($entrie->date)) }}</td>
      </tr>
      <tr>
          <td class="td2">@lang('accounting.entrie_type')</td>
          <td class="td2">{{ mb_strtoupper($entrie->type_entrie) }}</td>
      </tr>
      <tr>
          <td class="td2">@lang('accounting.general_concept')</td>
          <td class="td2">{{ mb_strtoupper($entrie->description) }}</td>
      </tr>
  </table>
  <br>

  <div id="partidas" class="print">
    {{-- Table --}}
    <table style="width: 100%;" class="table1">        
     <thead>
        <tr>
            <th class="td5" colspan="2">{{ mb_strtoupper(__('accounting.accounting_account')) }}</th>
            <th style="width: 30%;" class="td5"></th>
            <th style="width: 10%;" class="td5 alncenter">{{ mb_strtoupper(__('accounting.partial')) }}</th>
            <th style="width: 10%;" class="td5 alncenter">{{ mb_strtoupper(__('accounting.charges')) }}</th>
            <th style="width: 10%;" class="td5 alncenter">{{ mb_strtoupper(__('accounting.payments')) }}</th>
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
        <td class="td2"><strong>{{ $grupo->mayor }}</strong></td>

        {{-- Description --}}
        <td class="td2" colspan="2"><strong>{{ $grupo->nombre }}</strong></td>

        {{-- Partial --}}
        <td class="td2"></td>

        {{-- Charges --}}
        <td class="alnright td2"><strong>{{ $group_debit_label }}</strong></td>

        {{-- Payments --}}
        <td class="td2"></td>
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
        <td class="td2" style="width: 10%;">{{ $item->code }}</td>

        {{-- Description --}}
        <td class="td2" style="width: 60%;" colspan="2">{{ $item->name }}</td>

        {{-- Partial --}}
        @if($enable_description_line != 1)
            @if($loop->last)
                <td class="alnright td2 bb">{{ $partial_debit_label }}</td>
            @else
                <td class="alnright td2">{{ $partial_debit_label }}</td>
            @endif
        @else
            <td class="alnright td2"></td>
        @endif

        {{-- Charges --}}
        <td class="td2"></td>

        {{-- Payments --}}
        <td class="td2"></td>
    </tr>

    {{-- Concept --}}
    @if($enable_description_line == 1)
    <tr>
        <td class="td2"></td>

        <td class="td2" colspan="2">{{ $item->description_line }}</td>

        {{-- Partial --}}
        @if($loop->last)
        <td class="alnright td2 bb">{{ $partial_debit_label }}</td>
        @else
        <td class="alnright td2">{{ $partial_debit_label }}</td>
        @endif

        <td class="td2"></td>

        <td class="td2"></td>
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
        <td class="td2"><strong>{{ $grupo->mayor }}</strong></td>

        {{-- Description --}}
        <td class="td2" colspan="2"><strong>{{ $grupo->nombre }}</strong></td>

        {{-- Partial --}}
        <td class="td2"></td>

        {{-- Charges --}}
        <td class="td2"></td>

        {{-- Payments --}}
        <td class="alnright td2"><strong> {{ $group_credit_label }}</strong></td>
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
        <td class="td2">{{ $item->code }}</td>
        
        {{-- Description --}}
        <td class="td2" colspan="2">{{ $item->name }}</td>

        {{-- Partial --}}
        @if($enable_description_line != 1)
            @if($loop->last)
                <td class="alnright td2 bb">{{ $partial_credit_label }}</td>
            @else
                <td class="alnright td2">{{ $partial_credit_label }}</td>
            @endif
        @else
            <td class="alnright td2"></td>
        @endif

        {{-- Charges --}}
        <td class="td2"></td>

        {{-- Payments --}}
        <td class="td2"></td>
    </tr>

    {{-- Concept --}}
    @if($enable_description_line == 1)
    <tr>
        <td class="td2"></td>

        <td class="td2" colspan="2">{{ $item->description_line }}</td>

        {{-- Partial --}}
        @if($loop->last)
        <td class="alnright td2 bb">{{ $partial_credit_label }}</td>
        @else
        <td class="alnright td2">{{ $partial_credit_label }}</td>
        @endif

        <td class="td2"></td>

        <td class="td2"></td>
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
        <td class="td2"></td>
        <td class="td2"></td>
        <td class="td2"><strong>{{ mb_strtoupper(__('accounting.total_charges_and_payments')) }}</strong></td>
        <td class="td2"></td>
        <td class="alnright td2 bbd"><strong>{{ $total_debit_label }}</strong></td>
        <td class="alnright td2 bbd"><strong>{{ $total_credit_label }}</strong></td>
    </tr>
</table>
<br>
<br>
<br>

{{-- Footer --}}
<table class="table1" style="width: 100%;">
    <tr>
        <td class="td2 alncenter" style="width: 33%;">
            _____________________________
            <br>
            @lang('accounting.made_by')
        </td>
        <td class="td2 alncenter" style="width: 34%;">
            _____________________________
            <br>
            @lang('accounting.reviewed_by')
        </td>
        <td class="td2 alncenter" style="width: 33%;">
            _____________________________
            <br>
            @lang('accounting.approved_by')
        </td>
    </tr>
</table>

</div>
@endforeach
</body>
</html>