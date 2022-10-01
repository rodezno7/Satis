<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@lang('accounting.ledgers_menu')</title>    
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
            border-top: 0.25px double black;
            padding: 4px;
        } 
        .td3
        {
            border-bottom: none;
            border-top: none;
            border-left: 0.25px solid black;
            border-right: 0.25px solid black;
            padding: 4px;
            text-align: left;
        }
        .td5
        {
            border-top: 0.25px solid black;
            border-bottom: 0.25px solid black;
            border-left: none;
            border-right: none;
            text-align: left;
        }
        .td6
        {
            border-top: 0.25px solid black;
            border-bottom: none;
            border-left: none;
            border-right: none;
        }
        td
        {
            
            padding: 4px;
            
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
  </style>   
</head>
<body>
    <div id="footer">
        <div class="page-number"></div>
    </div>
    
    <table class="table1" style="width: 100%;">
        <thead>
            <tr>
                <td colspan="5" class="td2">
                    <center><strong> {{ mb_strtoupper($business_name) }} </strong></center>
                    <center><strong> {{ $report_name }} </strong></center>
                    <center> {{ $date_range }} </center>
                    <center> {{  mb_strtoupper(__('accounting.accountant_report_values')) }} </center>
                </td>
            </tr>
        </thead>

        @php
        $total_debit = 0.00;
        $total_credit = 0.00;
        @endphp

        @foreach($accounts as $account)

        @if((number_format($account->debit_initial, 2) != 0.00) || (number_format($account->credit_initial, 2) != 0.00) || (number_format($account->debit_range, 2) != 0.00) || (number_format($account->credit_range, 2) != 0.00) || (number_format($account->debit_final, 2) != 0.00) || (number_format($account->credit_final, 2) != 0.00))

        @if($account->type == 'debtor')
        @php($balance = $account->debit_initial - $account->credit_initial)
        @php($balance_final = $account->debit_final - $account->credit_final)
        @else
        @php($balance = $account->credit_initial - $account->debit_initial)
        @php($balance_final = $account->credit_final - $account->debit_final)
        @endif

        @if(number_format($balance, 2) >= 0.00)
        @php($balance_label = number_format($balance, 2))
        @else
        @php($balance_label = "(".number_format(($balance * -1), 2).")")
        @endif

        @if((number_format($balance, 2) != 0.00) || (number_format($balance_final, 2) != 0.00) || (number_format($account->debit_range, 2) != 0.00) || (number_format($account->credit_range, 2) != 0.00))

        <tr>
            <td colspan="5" class="td2">
                <strong> @lang('accounting.account'): </strong>{{ $account->code }}<br>
                <strong> @lang('accounting.account_name'): </strong>{{ $account->name }}
            </td>
        </tr>

        <tr>
            <td style="width: 15%;" class="td4 alncenter"><strong> @lang('accounting.date') </strong></td>
            <td style="width: 49%;" class="td4 alncenter"><strong> @lang('accounting.concept') </strong></td>
            <td style="width: 12%;" class="td4 alncenter"><strong> {{ __('accounting.debit') }} </strong></td>
            <td style="width: 12%;" class="td4 alncenter"><strong> {{ __('accounting.credit') }} </strong></td>
            <td style="width: 12%;" class="td4 alncenter"><strong> {{ __('accounting.balance') }} </strong></td>
        </tr>


        <tr>
            <td colspan="4" class="alnright td2"><strong>@lang('accounting.previous_balance'):</strong></td>
            <td class="alnright td2">{{ $balance_label }}</td>
        </tr>

        @foreach($lines as $detail)
        @if($account->code == $detail->code)

        @if($account->type == 'debtor')
        @php($balance = $balance + $detail->debit - $detail->credit)
        @else
        @php($balance = $balance - $detail->debit + $detail->credit)
        @endif

        @if(number_format($balance, 2) >= 0.00)
        @php($balance_label_line = number_format($balance, 2))
        @else
        @php($balance_label_line = "(".number_format(($balance * -1), 2).")")
        @endif

        @if((number_format($account->debit_range, 2) != 0.00) || (number_format($account->credit_range, 2) != 0.00))

        @if(number_format($detail->debit, 2) >= 0.00)
        @php($debit_label = number_format($detail->debit, 2))
        @else
        @php($debit_label = "(".number_format(($detail->debit * -1), 2).")")
        @endif

        @if(number_format($detail->credit, 2) >= 0.00)
        @php($credit_label = number_format($detail->credit, 2))
        @else
        @php($credit_label = "(".number_format(($detail->credit * -1), 2).")")
        @endif


        <tr>
            <td class="td2">{{ date('d/m/y', strtotime($detail->date)) }}</td>
            <td class="td2">{{ mb_strtoupper(__('accounting.movements_day')) }}</td>
            <td class="alnright td2">{{ $debit_label }}</td>
            <td class="alnright td2">{{ $credit_label }}</td>
            <td class="alnright td2">{{ $balance_label_line }}</td>
        </tr>
        @endif



        @endif

        @endforeach

        @if((number_format($account->debit_range, 2) == 0.00) && (number_format($account->credit_range, 2) == 0.00))

        <tr>
            <td colspan="2" class="td2"><strong>@lang('accounting.out_moves')</strong></td>
            <td colspan="2" class="td2"></td>
            <td class="td2"></td>

        </tr>



        @endif

        @if(number_format($account->debit_range, 2) >= 0.00)
        @php($debit_range_label = number_format($account->debit_range, 2))
        @else
        @php($debit_range_label = "(".number_format(($account->debit_range * -1), 2).")")
        @endif

        @if(number_format($account->credit_range, 2) >= 0.00)
        @php($credit_range_label = number_format($account->credit_range, 2))
        @else
        @php($credit_range_label = "(".number_format(($account->credit_range * -1), 2).")")
        @endif

        @if(number_format($balance_final, 2) >= 0.00)
        @php($balance_final_label = number_format($balance_final, 2))
        @else
        @php($balance_final_label = "(".number_format(($balance_final * -1), 2).")")
        @endif


        <tr>
            <td colspan="2" class="alnright td2"><strong>@lang('accounting.total_account')</strong></td>
            <td class="alnright td6"><strong>{{ $debit_range_label }}</strong></td>
            <td class="alnright td6"><strong>{{ $credit_range_label }}</strong></td>
            <td class="alnright td6"><strong>{{ $balance_final_label }}</strong></td>
        </tr>

        @php($total_debit = $total_debit + $account->debit_range)
        @php($total_credit = $total_credit + $account->credit_range)

        <tr>
            <td colspan="5">&nbsp;</td>
        </tr>





        @endif
        @endif
        @endforeach

    </table>


    <table style="width: 100%;" class="table1">

        @if(number_format($total_debit, 2) >= 0.00)
        @php($total_debit_label = number_format($total_debit, 2))
        @else
        @php($total_debit_label = "(".number_format(($total_debit * -1), 2).")")
        @endif

        @if(number_format($total_credit, 2) >= 0.00)
        @php($total_credit_label = number_format($total_credit, 2))
        @else
        @php($total_credit_label = "(".number_format(($total_credit * -1), 2).")")
        @endif


        <tr>
            <td style="width: 64%" class="td2"><strong> @lang('accounting.total_general') </strong></td>
            <td style="width: 12%"class="alnright td6"><strong>{{ $total_debit_label }}</strong></td>
            <td style="width: 12%"class="alnright td6"><strong>{{ $total_credit_label }}</strong></td>
            <td style="width: 12%" class="td6"></td>
        </tr>
    </table>
</body>
</html>