<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>@lang('accounting.result_status')</title>    
  <style>
    body
    {
      font-family: 'Helvetica', 'Arial', sans-serif;
      color: #000000;
      font-size: {{$size}}pt;       
    }
    h3, h4
    {
      text-align: center;        
    }
    .table1
    {
      border: 0px;
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
    .td3
    {
      border-bottom: 0.25px solid black;
      border-top: none;
      border-left: none;
      border-right: none;
    }
    .td5
    {
      border-top: 1px solid black;
      border-bottom: 0.25px solid black;
    }
    td
    {
      border: 0px;
      padding: 2px;
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
      margin-left: 50px;
      margin-right: 50px;
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
  </style>   
</head>
<body>
  <div id="footer">
    <div class="page-number"></div>
  </div>
  <table class="table1" style="width: 100%;">
    <tr>
      <td class="td2">
        <center><strong>{{ mb_strtoupper($business_name) }}</strong></center>
        <center><strong>{{ mb_strtoupper(__('accounting.result_title')) }}</strong></center>
        <center><strong>{{ mb_strtoupper($header) }}</strong></center>
        <center><strong>{{ mb_strtoupper(__('accounting.accountant_report_values')) }}</strong></center>
      </td>
    </tr>
  </table>

  <table class="table1" style="width: 100%;">









    @if(number_format($ordinary_income, 2) != 0.00)
    @if(number_format($ordinary_income, 2) >= 0.00)
    @php($ordinary_income_label = number_format($ordinary_income, 2))
    @else
    @php($ordinary_income_label = "(".number_format(($ordinary_income * -1), 2).")")
    @endif
    <tr>
      <td style="width: 70%"><strong>{{ mb_strtoupper(__('accounting.result_income_ordinary')) }}</strong></td>
      <td style="width: 15%"></td>
      <td style="width: 15%" class="alnright">{{ $ordinary_income_label }}</td>
    </tr>











    @php($sum_ordinary_income = 0.00)
    @foreach($ordinary_income_accounts as $item)
    @if(number_format($item->balance, 2) != 0.00)
    @php($sum_ordinary_income = $sum_ordinary_income + $item->balance)
    @if(number_format($item->balance, 2) >= 0.00)
    @php($balance_label = number_format($item->balance, 2))
    @else
    @php($balance_label = "(".number_format(($item->balance * -1), 2).")")
    @endif
    <tr>
      <td style="width: 70%">{{ $item->name }}</td>
      @if(number_format($sum_ordinary_income, 2) == number_format($ordinary_income, 2))
      <td style="width: 15%" class="alnright td3">{{ $balance_label }}</td>
      @else
      <td style="width: 15%" class="alnright">{{ $balance_label }}</td>
      @endif
      <td style="width: 15%"></td>
    </tr>
    @endif
    @endforeach











    
    @endif









    @if(number_format($sell_cost_q->balance, 2) != 0.00)
    <tr>
      <td style="width: 70%"><strong>{{ __('accounting.result_less') }}:</strong></td>
      <td style="width: 15%"></td>
      <td style="width: 15%"></td>
    </tr>
    <tr>
      <td colspan="3"></td>
    </tr>
    @if(number_format($sell_cost_q->balance, 2) >= 0.00)
    @php($sell_cost_label = number_format($sell_cost_q->balance, 2))
    @else
    @php($sell_cost_label = "(".number_format(($sell_cost_q->balance * -1), 2).")")
    @endif
    <tr>
      <td style="width: 70%"><strong>{{ mb_strtoupper(__('accounting.result_cost')) }}</strong></td>
      <td style="width: 15%"></td>
      <td style="width: 15%" class="alnright">{{ $sell_cost_label }}</td>
    </tr>
    <tr>
      <td colspan="3"></td>
    </tr>
    @endif









    @if(number_format(($ordinary_income - $sell_cost_q->balance), 2) != 0.00)
    @if(number_format(($ordinary_income - $sell_cost_q->balance), 2) >= 0.00) 
    @php($utility_gross = $ordinary_income - $sell_cost_q->balance)
    @php($utility_gross_label = number_format(($ordinary_income - $sell_cost_q->balance), 2))
    @else
    @php($utility_gross_label = "(".number_format((($ordinary_income - $sell_cost_q->balance) * -1), 2).")")
    @endif
    <tr>
      <td style="width: 70%"><strong>{{ mb_strtoupper(__('accounting.result_utility_gross')) }}</strong></td>
      <td style="width: 15%"></td>
      <td style="width: 15%" class="alnright">{{ $utility_gross_label }}</td>
    </tr>
    <tr>
      <td colspan="3"></td>
    </tr>
    @endif










    @if(number_format(($ordinary_expense), 2) != 0.00)
    <tr>
      <td style="width: 70%"><strong>{{ __('accounting.result_less') }}:</strong></td>
      <td style="width: 15%"></td>
      <td style="width: 15%"></td>
    </tr>
    <tr>
      <td colspan="3"></td>
    </tr>
    @if(number_format(($ordinary_expense), 2) >= 0.00)
    @php($ordinary_expense_label = number_format(($ordinary_expense), 2))
    @else
    @php($ordinary_expense_label = "(".number_format((($ordinary_expense) * -1), 2).")")
    @endif
    <tr>
      <td style="width: 70%"><strong>{{ mb_strtoupper(__('accounting.result_expenses_ordinary')) }}</strong></td>
      <td style="width: 15%"></td>
      <td style="width: 15%" class="alnright">{{ $ordinary_expense_label }}</td>
    </tr>










    @php($sum_ordinary_expense = 0.00)
    @foreach($ordinary_expense_accounts as $item)
    @if(number_format($item->balance, 2) != 0.00)
    @php($sum_ordinary_expense = $sum_ordinary_expense + $item->balance)
    @if(number_format($item->balance, 2) >= 0.00)
    @php($balance_label = number_format($item->balance, 2))
    @else
    @php($balance_label = "(".number_format(($item->balance * -1), 2).")")
    @endif
    <tr>
      <td style="width: 70%">{{ $item->name }}</td>
      @if(number_format($sum_ordinary_expense, 2) == number_format(($ordinary_expense), 2))
      <td style="width: 15%" class="alnright td3">{{ $balance_label }}</td>
      <td style="width: 15%" class="alnright td3"></td>
      @else
      <td style="width: 15%" class="alnright">{{ $balance_label }}</td>
      <td style="width: 15%"></td>
      @endif      
    </tr>
    @endif
    @endforeach
    @endif









    @if(number_format(($ordinary_income - ($sell_cost_q->balance + ($ordinary_expense))), 2) != 0.00)
    @if(number_format(($ordinary_income - ($sell_cost_q->balance + ($ordinary_expense))), 2) >= 0.00)
    @php($utility_operation_label = number_format(($ordinary_income - ($sell_cost_q->balance + ($ordinary_expense))), 2))
    @else
    @php($utility_operation_label = "(".number_format((($ordinary_income - ($sell_cost_q->balance + ($ordinary_expense))) * -1), 2).")")
    @endif
    <tr>
      <td style="width: 70%"><strong>{{ mb_strtoupper(__('accounting.result_utility_operation')) }}</strong></td>
      <td style="width: 15%"></td>
      <td style="width: 15%" class="alnright">{{ $utility_operation_label }}</td>
    </tr>
    <tr>
      <td colspan="3"></td>
    </tr>
    @endif










    @if(number_format($extra_income, 2) != 0.00)
    <tr>
      <td style="width: 70%"><strong>{{ __('accounting.result_more') }}:</strong></td>
      <td style="width: 15%"></td>
      <td style="width: 15%"></td>
    </tr>
    <tr>
      <td colspan="3"></td>
    </tr>
    @if(number_format($extra_income, 2) >= 0.00)
    @php($extra_income_label = number_format($extra_income, 2))
    @else
    @php($extra_income_label = "(".number_format(($extra_income * -1), 2).")")
    @endif
    <tr>
      <td style="width: 70%"><strong>{{ mb_strtoupper(__('accounting.result_income_no_ordinary')) }}</strong></td>
      <td style="width: 15%"></td>
      <td style="width: 15%" class="alnright">{{ $extra_income_label }}</td>
    </tr>










    @php($sum_extra_income = 0.00)
    @foreach($extra_income_accounts as $item)
    @if(number_format($item->balance, 2) != 0.00)
    @php($sum_extra_income = $sum_extra_income + $item->balance)
    @if(number_format($item->balance, 2) >= 0.00)
    @php($balance_label = number_format($item->balance, 2))
    @else
    @php($balance_label = "(".number_format(($item->balance * -1), 2).")")
    @endif
    <tr>
      <td style="width: 70%">{{ $item->name }}</td>
      @if(number_format($sum_extra_income, 2) == number_format($extra_income, 2))
      <td style="width: 15%" class="alnright td3">{{ $balance_label }}</td>
      @else
      <td style="width: 15%" class="alnright">{{ $balance_label }}</td>
      @endif
      <td style="width: 15%"></td>
    </tr>
    @endif
    @endforeach
    <tr>
      <td colspan="3"></td>
    </tr>
    @endif










    @if(number_format($extra_expense, 2) != 0.00)
    <tr>
      <td style="width: 70%"><strong>{{ __('accounting.result_less') }}:</strong></td>
      <td style="width: 15%"></td>
      <td style="width: 15%"></td>
    </tr>
    <tr>
      <td colspan="3"></td>
    </tr>
    @if(number_format($extra_expense, 2) >= 0.00)
    @php($extra_expense_label = number_format($extra_expense, 2))
    @else
    @php($extra_expense_label = "(".number_format(($extra_expense * -1), 2).")")
    @endif
    <tr>
      <td style="width: 70%"><strong>{{ mb_strtoupper(__('accounting.result_expenses_no_ordinary')) }}</strong></td>
      <td style="width: 15%"></td>
      <td style="width: 15%" class="alnright">{{ number_format($extra_expense, 2) }}</td>
    </tr>











    @php($sum_extra_expense = 0.00)
    @foreach($extra_expense_accounts as $item)
    @if(number_format($item->balance, 2) != 0.00)
    @php($sum_extra_expense = $sum_extra_expense + $item->balance)
    @if(number_format($item->balance, 2) >= 0.00)
    @php($balance_label = number_format($item->balance, 2))
    @else
    @php($balance_label = "(".number_format(($item->balance * -1), 2).")")
    @endif
    <tr>
      <td style="width: 70%">{{ $item->name }}</td>
      @if(number_format($sum_extra_expense, 2) == number_format($extra_expense, 2))
      <td style="width: 15%" class="alnright td3">{{ $balance_label }}</td>
      <td style="width: 15%" class="td3"></td>
      @else
      <td style="width: 15%" class="alnright">{{ $balance_label }}</td>
      <td style="width: 15%"></td>
      @endif
    </tr>
    @endif
    @endforeach
    @endif










    @if(number_format(($ordinary_income + $extra_income - ($sell_cost_q->balance + ($ordinary_expense) + $extra_expense)), 2) != 0.00)
    @if(number_format(($ordinary_income + $extra_income - ($sell_cost_q->balance + ($ordinary_expense) + $extra_expense)), 2) >= 0.00)
    @php($utility_before_label = number_format(($ordinary_income + $extra_income - ($sell_cost_q->balance + ($ordinary_expense) + $extra_expense)), 2))
    @else
    @php($utility_before_label = "(".number_format((($ordinary_income + $extra_income - ($sell_cost_q->balance + ($ordinary_expense) + $extra_expense)) * -1), 2).")")
    @endif
    <tr>
      <td style="width: 70%"><strong>{{ mb_strtoupper(__('accounting.result_utility_exercise')) }}</strong></td>
      <td style="width: 15%"></td>
      <td style="width: 15%" class="alnright td5">{{ $utility_before_label }}</td>
    </tr>
    <tr>
      <td colspan="3"></td>
    </tr>
    @endif



    {{--










    @if(number_format((($ordinary_income + $extra_income - ($sell_cost_q->balance + ($ordinary_expense) + $extra_expense)) *(0.07)), 2) != 0.00)
    <tr>
      <td style="width: 70%"><strong>{{ __('accounting.result_less') }}:</strong></td>
      <td style="width: 15%"></td>
      <td style="width: 15%"></td>
    </tr>
    <tr>
      <td colspan="3"></td>
    </tr>
    @if(number_format((($ordinary_income + $extra_income - ($sell_cost_q->balance + ($ordinary_expense) + $extra_expense)) *(0.07)), 2) >= 0.00)
    @php($legal_reserve_label = number_format((($ordinary_income + $extra_income - ($sell_cost_q->balance + ($ordinary_expense) + $extra_expense)) *(0.07)), 2))
    @else
    @php($legal_reserve_label = "(".number_format(((($ordinary_income + $extra_income - ($sell_cost_q->balance + ($ordinary_expense) + $extra_expense)) *(0.07)) * -1), 2).")")
    @endif
    <tr>
      <td style="width: 70%"><strong>{{ mb_strtoupper(__('accounting.result_legal_reserve')) }}</strong></td>
      <td style="width: 15%"></td>
      <td style="width: 15%" class="alnright">{{ $legal_reserve_label }}</td>
    </tr>
    <tr>
      <td colspan="3"></td>
    </tr>


    <tr>
      <td style="width: 70%"><strong>{{ mb_strtoupper(__('accounting.result_income_tax')) }}</strong></td>
      <td style="width: 15%"></td>
      <td style="width: 15%"></td>
    </tr>
    @endif










    @php($utility_exercise = ($ordinary_income + $extra_income - ($sell_cost_q->balance + ($ordinary_expense) + $extra_expense)) - (($ordinary_income + $extra_income - ($sell_cost_q->balance + ($ordinary_expense) + $extra_expense)) *(0.07)))
    @if(number_format($utility_exercise, 2) != 0.00)
    @if(number_format($utility_exercise, 2) >= 0.00)
    @php($utility_exercise_label = number_format($utility_exercise, 2))
    @else
    @php($utility_exercise_label = "(".number_format(($utility_exercise * -1), 2).")")
    @endif
    <tr>
      <td style="width: 70%"><strong>{{ mb_strtoupper(__('accounting.result_utility_exercise')) }}</strong></td>
      <td style="width: 15%"></td>
      <td style="width: 15%" class="alnright td5">{{ $utility_exercise_label }}</td>
    </tr>
    @endif

    --}}



  </table>
  <br>
  <br>
  <br>
  <table class="table1" width="100%">
   <tr>
    <td style="width: 35%" class="alncenter">
      ______________________________<br>
      {{ mb_strtoupper($business->legal_representative) }}<br>
      {{ mb_strtoupper(__('accounting.owner')) }}
    </td>
    <td style="width: 30%" class="alncenter">
    </td>
    <td style="width: 35%" class="alncenter">
      ______________________________<br>
      {{ mb_strtoupper($business->accountant) }}<br>
      {{ mb_strtoupper(__('accounting.accountant')) }} 
    </td>
  </tr>

  <tr>
    <td style="width: 35%" class="alncenter">
    </td>
    <td style="width: 30%" class="alncenter">
      ______________________________<br>
      {{ mb_strtoupper($business->auditor) }}<br>
      {{ mb_strtoupper(__('accounting.auditor')) }}<br>
      {{ mb_strtoupper(__('accounting.inscription_number')) }}: {{ mb_strtoupper($business->inscription_number_auditor) }}
    </td>
    <td style="width: 35%" class="alncenter">
    </td>
  </tr>
</table>

</body>
</html>