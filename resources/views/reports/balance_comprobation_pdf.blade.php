<!DOCTYPE html>

<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>@lang('accounting.tittle_balances')</title>
  <style>
    body
    {
      font-family: 'Helvetica', 'Arial', sans-serif;
      color: #000000;
      font-size: {{$size}}pt;
    }
    h3, h4, h5
    {
      text-align: center;
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
      border-bottom: none;
      border-top: none;
      border-left: none;
      border-right: 0.25px solid black;
    } 
    .td3
    {
      border-bottom: 0.25px solid black;
      border-top: none;
      border-left: none;
      border-right: none;
    }
    .td5
    {
      border-top: 0.25px solid black;
      border-bottom: 0.25px solid black;
      border-left: none;
      border-right: none;
      text-align: left;
    }
    td
    {
      border: 0px;
    }
    th
    {
      border: 0px;
      padding: 4px;
      text-align: center;
    }
    .alnright { text-align: right; }
    .alnleft { text-align: left; }
    .alncenter { text-align: center; }
    @page{
      margin-top: 50px;
      margin-bottom: 50px;
      margin-left: 75px;
      margin-right: 75px;
    }
  </style>
</head>
<body>  
  <table width="100%" class="table1">

    <thead>
      <tr>
        <td colspan="6">
          <center><strong>{{ mb_strtoupper($business_name) }}</strong></center>
          <center><strong>{{ $report_name }}</strong></center>
          <center>{{ $date_range }}</center>
          <center>@lang('accounting.accountant_report_values')</center>
          <br>
        </td>
      </tr>

      <tr>
        <td class="alncenter td5" style="width: 12%"></td>
        <td class="alnleft td5" style="width: 36%"><strong> {{ __('accounting.accounts') }} </strong></td>
        <td class="alnright td5" style="width: 13%"><strong> {{ __('accounting.previous_balance')}} </strong></td>
        <td class="alnright td5" style="width: 13%"><strong> {{ __('accounting.charges') }} </strong></td>
        <td class="alnright td5" style="width: 13%"><strong> {{ __('accounting.payments') }} </strong></td>
        <td class="alnright td5" style="width: 13%"><strong> {{ __('accounting.final_balance') }} </strong></td>
      </tr>

    </thead>
    
    @php
    $total_initial_debtor = 0;
    $total_debit_debtor = 0;
    $total_credit_debtor = 0;
    $total_final_debtor = 0;
    @endphp

    @foreach($accounts_debit as $item)
    @if (($item->clasification >= $account_from) && ($item->clasification <= $account_to))
    @php
    $balance_initial = $item->debit_initial - $item->credit_initial;
    $balance_final = $item->debit_final - $item->credit_final;
    
    if($item->level == 1) {
      $total_initial_debtor = $total_initial_debtor + $balance_initial;
      $total_debit_debtor = $total_debit_debtor + $item->debit_range;
      $total_credit_debtor = $total_credit_debtor + $item->credit_range;
      $total_final_debtor = $total_final_debtor + $balance_final;
    }    
    @endphp

    @if(number_format($balance_initial, 2) >= 0.00)
    @php($balance_initial_label = number_format($balance_initial, 2))
    @else
    @php($balance_initial_label = "(".number_format(($balance_initial * -1), 2).")")
    @endif

    @if(number_format($balance_final, 2) >= 0.00)
    @php($balance_final_label = number_format($balance_final, 2))
    @else
    @php($balance_final_label = "(".number_format(($balance_final * -1), 2).")")
    @endif

    @if(number_format($item->debit_range, 2) >= 0.00)
    @php($debit_range_label = number_format($item->debit_range, 2))
    @else
    @php($debit_range_label = "(".number_format(($item->debit_range * -1), 2).")")
    @endif

    @if(number_format($item->credit_range, 2) >= 0.00)
    @php($credit_range_label = number_format($item->credit_range, 2))
    @else
    @php($credit_range_label = "(".number_format(($item->credit_range * -1), 2).")")
    @endif


    @if($enable_empty_values == 'active')
    <tr>
      @if($item->level <= 3)
      <td class="alnleft"><strong>{{ $item->code }}</strong></td>
      <td class="alnleft"><strong>{{ $item->name }}</strong></td>
      <td class="alnright"><strong>{{ $balance_initial_label }}</strong></td>
      <td class="alnright"><strong>{{ $debit_range_label }}</strong></td>
      <td class="alnright"><strong>{{ $credit_range_label }}</strong></td>
      <td class="alnright"><strong>{{ $balance_final_label }}</strong></td>
      @else
      <td class="alnleft">{{ $item->code }}</td>
      <td class="alnleft">{{ $item->name }}</td>
      <td class="alnright">{{ $balance_initial_label }}</td>
      <td class="alnright">{{ $debit_range_label }}</td>
      <td class="alnright">{{ $credit_range_label }}</td>
      <td class="alnright">{{ $balance_final_label }}</td>
      @endif
    </tr>
    @else
    @if(($balance_initial != 0.00) || ($item->debit_range != 0.00) || ($item->credit_range != 0.00) || ($balance_final != 0.00))

    <tr>
      @if($item->level <= 3)
      <td class="alnleft"><strong>{{ $item->code }}</strong></td>
      <td class="alnleft"><strong>{{ $item->name }}</strong></td>
      <td class="alnright"><strong>{{ $balance_initial_label }}</strong></td>
      <td class="alnright"><strong>{{ $debit_range_label }}</strong></td>
      <td class="alnright"><strong>{{ $credit_range_label }}</strong></td>
      <td class="alnright"><strong>{{ $balance_final_label }}</strong></td>
      @else
      <td class="alnleft">{{ $item->code }}</td>
      <td class="alnleft">{{ $item->name }}</td>
      <td class="alnright">{{ $balance_initial_label }}</td>
      <td class="alnright">{{ $debit_range_label }}</td>
      <td class="alnright">{{ $credit_range_label }}</td>
      <td class="alnright">{{ $balance_final_label }}</td>
      @endif
    </tr>
    @endif
    @endif
    
    @endif
    @endforeach

    @if((number_format($total_initial_debtor, 2) != 0.00) || (number_format($total_debit_debtor, 2) != 0.00) || (number_format($total_credit_debtor, 2) != 0.00) || (number_format($total_final_debtor, 2) != 0.00))

    @if(number_format($total_initial_debtor, 2) >= 0.00)
    @php($total_initial_debtor_label = number_format($total_initial_debtor, 2))
    @else
    @php($total_initial_debtor_label = "(".number_format(($total_initial_debtor * -1), 2).")")
    @endif

    @if(number_format($total_debit_debtor, 2) >= 0.00)
    @php($total_debit_debtor_label = number_format($total_debit_debtor, 2))
    @else
    @php($total_debit_debtor_label = "(".number_format(($total_debit_debtor * -1), 2).")")
    @endif

    @if(number_format($total_credit_debtor, 2) >= 0.00)
    @php($total_credit_debtor_label = number_format($total_credit_debtor, 2))
    @else
    @php($total_credit_debtor_label = "(".number_format(($total_credit_debtor * -1), 2).")")
    @endif

    @if(number_format($total_final_debtor, 2) >= 0.00)
    @php($total_final_debtor_label = number_format($total_final_debtor, 2))
    @else
    @php($total_final_debtor_label = "(".number_format(($total_final_debtor * -1), 2).")")
    @endif


    <tr>
      <td colspan="2"></td>
      <td class="alnright" colspan="3" style="border-top: 0.25px solid black;"><strong>{{ mb_strtoupper(__('accounting.total_active_report')) }}</strong></td>
      <td class="alnright" style="border-top: 0.25px solid black;"><strong>{{ $total_final_debtor_label }}</strong></td>
    </tr>
    @endif

    <tr>
      <td colspan="6">&nbsp;</td>
    </tr>

    <tr>
      <td colspan="6">&nbsp;</td>
    </tr>

    
    @php($total_initial_creditor = 0)
    @php($total_debit_creditor = 0)
    @php($total_credit_creditor = 0)
    @php($total_final_creditor = 0)

    @foreach($accounts_credit as $item)
    
    @if (($item->clasification >= $account_from) && ($item->clasification <= $account_to))
    @php($balance_initial = $item->credit_initial - $item->debit_initial)
    @php($balance_final = $item->credit_final - $item->debit_final)
    @if($item->level == 1)
    @php($total_initial_creditor = $total_initial_creditor + $balance_initial)
    @php($total_debit_creditor = $total_debit_creditor + $item->debit_range)
    @php($total_credit_creditor = $total_credit_creditor + $item->credit_range)
    @php($total_final_creditor = $total_final_creditor + $balance_final)
    @endif
    

    @if(number_format($balance_initial, 2) >= 0.00)
    @php($balance_initial_label = number_format($balance_initial, 2))
    @else
    @php($balance_initial_label = "(".number_format(($balance_initial * -1), 2).")")
    @endif

    @if(number_format($balance_final, 2) >= 0.00)
    @php($balance_final_label = number_format($balance_final, 2))
    @else
    @php($balance_final_label = "(".number_format(($balance_final * -1), 2).")")
    @endif

    @if(number_format($item->debit_range, 2) >= 0.00)
    @php($debit_range_label = number_format($item->debit_range, 2))
    @else
    @php($debit_range_label = "(".number_format(($item->debit_range * -1), 2).")")
    @endif

    @if(number_format($item->credit_range, 2) >= 0.00)
    @php($credit_range_label = number_format($item->credit_range, 2))
    @else
    @php($credit_range_label = "(".number_format(($item->credit_range * -1), 2).")")
    @endif


    
    @if($enable_empty_values == 'active')
    <tr>
      @if($item->level <= 3)
      <td class="alnleft"><strong>{{ $item->code }}</strong></td>
      <td class="alnleft"><strong>{{ $item->name }}</strong></td>
      <td class="alnright"><strong>{{ $balance_initial_label }}</strong></td>
      <td class="alnright"><strong>{{ $debit_range_label }}</strong></td>
      <td class="alnright"><strong>{{ $credit_range_label }}</strong></td>
      <td class="alnright"><strong>{{ $balance_final_label }}</strong></td>
      @else
      <td class="alnleft">{{ $item->code }}</td>
      <td class="alnleft">{{ $item->name }}</td>
      <td class="alnright">{{ $balance_initial_label }}</td>
      <td class="alnright">{{ $debit_range_label }}</td>
      <td class="alnright">{{ $credit_range_label }}</td>
      <td class="alnright">{{ $balance_final_label }}</td>
      @endif
    </tr>

    @else

    @if(($balance_initial != 0.00) || ($item->debit_range != 0.00) || ($item->credit_range != 0.00) || ($balance_final != 0.00))
    <tr>
      @if($item->level <= 3)
      <td class="alnleft"><strong>{{ $item->code }}</strong></td>
      <td class="alnleft"><strong>{{ $item->name }}</strong></td>
      <td class="alnright"><strong>{{ $balance_initial_label }}</strong></td>
      <td class="alnright"><strong>{{ $debit_range_label }}</strong></td>
      <td class="alnright"><strong>{{ $credit_range_label }}</strong></td>
      <td class="alnright"><strong>{{ $balance_final_label }}</strong></td>
      @else
      <td class="alnleft">{{ $item->code }}</td>
      <td class="alnleft">{{ $item->name }}</td>
      <td class="alnright">{{ $balance_initial_label }}</td>
      <td class="alnright">{{ $debit_range_label }}</td>
      <td class="alnright">{{ $credit_range_label }}</td>
      <td class="alnright">{{ $balance_final_label }}</td>
      @endif
    </tr>

    @endif
    
    @endif
    @endif
    @endforeach

    @if((number_format($total_initial_creditor, 2) != 0.00) || (number_format($total_debit_creditor, 2) != 0.00) || (number_format($total_credit_creditor, 2) != 0.00) || (number_format($total_final_creditor, 2) != 0.00))

    @if(number_format($total_initial_creditor, 2) >= 0.00)
    @php($total_initial_creditor_label = number_format($total_initial_creditor, 2))
    @else
    @php($total_initial_creditor_label = "(".number_format(($total_initial_creditor * -1), 2).")")
    @endif

    @if(number_format($total_debit_creditor, 2) >= 0.00)
    @php($total_debit_creditor_label = number_format($total_debit_creditor, 2))
    @else
    @php($total_debit_creditor_label = "(".number_format(($total_debit_creditor * -1), 2).")")
    @endif

    @if(number_format($total_credit_creditor, 2) >= 0.00)
    @php($total_credit_creditor_label = number_format($total_credit_creditor, 2))
    @else
    @php($total_credit_creditor_label = "(".number_format(($total_credit_creditor * -1), 2).")")
    @endif

    @if(number_format($total_final_creditor, 2) >= 0.00)
    @php($total_final_creditor_label = number_format($total_final_creditor, 2))
    @else
    @php($total_final_creditor_label = "(".number_format(($total_final_creditor * -1), 2).")")
    @endif


    <tr>
      <td colspan="2"></td>
      <td colspan="3" class="alnright" style="border-top: 0.25px solid black;"><strong>{{ mb_strtoupper(__('accounting.total_pasive_report')) }}</strong></td>
      <td class="alnright" style="border-top: 0.25px solid black;"><strong>{{ $total_final_creditor_label }}</strong></td>
    </tr>
    @endif
  </table>
</body>
</html>