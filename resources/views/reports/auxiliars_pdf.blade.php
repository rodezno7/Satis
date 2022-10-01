<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@lang('accounting.auxiliars_menu')</title>
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
            border-top: 0.25px solid black;
            border-left: 0.25px solid black;
            border-right: 0.25px solid black;
            padding: 4px;
            text-align: left;
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
            border-top: none;
            border-bottom: 0.25px solid black;
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
  </style>
</head>
<body>
    <div id="footer">
        <div class="page-number"></div>
    </div>
    <table class="table1" style="width: 100%;">
        <thead>
            <tr>
                <td class="td2" colspan="6">
                    <center><strong>{{ mb_strtoupper($business_name) }}</strong></center>
                    <center><strong>{{ $report_name }} </strong></center>
                    <center>{{ $date_range }} </center>
                    <center>{{ mb_strtoupper(__('accounting.accountant_report_values')) }} </center>
                </td>
            </tr>
            <tr>
                <th class="td5" style="width: 18%;">{{ __('accounting.entrie_number') }}</th>
                <th class="td5" style="width: 10%;">{{ __('accounting.date') }}</th>
                <th class="td5" style="width: 27%;">{{ __('accounting.description') }}</th>
                <th class="td5 alnright" style="width: 15%;">{{ __('accounting.debit') }}</th>
                <th class="td5 alnright" style="width: 15%;">{{ __('accounting.credit') }}</th>
                <th class="td5 alnright" style="width: 15%;">{{ __('accounting.balance') }}</th>
            </tr>
        </thead>

        @php($total_debit = 0.00)
        @php($total_credit = 0.00)
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
            <td colspan="6" class="td2">&nbsp;</td>
        </tr>
        <tr>
            <td class="td6" colspan="3"><strong style="color:black;">{{ $account->code }}  {{ $account->name }}</strong></td>
            <td colspan="2" class="alnright td6"><strong>@lang('accounting.initial_balance'):</strong></td>
            <td class="alnright td6">{{ $balance_label }}</td>
        </tr>
        @php($total_debit_single = 0.00)
        @php($total_credit_single = 0.00)
        @foreach($details as $detail)
        @if($account->id == $detail->account_id)


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
            <td class="td2">{{ $detail->short_name }}</td>
            <td class="td2">{{ date('d/m/y', strtotime($detail->date)) }}</td>

            @if($detail->description != '')
            <td class="td2">{{ $detail->description }}</td>
            @else
            <td class="td2">{{ $detail->entrie_description }}</td>
            @endif
            <td class="alnright td2">{{ $debit_label }}</td>
            <td class="alnright td2">{{ $credit_label }}</td>
            <td class="alnright td2">{{ $balance_label_line }}</td>
        </tr>
        @php($total_debit = $total_debit + $detail->debit)
        @php($total_credit = $total_credit + $detail->credit)
        @php($total_debit_single = $total_debit_single + $detail->debit)
        @php($total_credit_single = $total_credit_single + $detail->credit)
        @endif

        @endif
        @endforeach

        @if((number_format($account->debit_range, 2) == 0.00) && (number_format($account->credit_range, 2) == 0.00))
        <tr>
            <td colspan="3" class="alnleft td2"><strong>@lang('accounting.out_moves')</strong></td>
            <td class="td2" colspan="2"></td>
            <td class="td2"></td>
        </tr>
        @endif

        @if(number_format($total_debit_single, 2) >= 0.00)
        @php($total_debit_single_label = number_format($total_debit_single, 2))
        @else
        @php($total_debit_single_label = "(".number_format(($total_debit_single * -1), 2).")")
        @endif

        @if(number_format($total_credit_single, 2) >= 0.00)
        @php($total_credit_single_label = number_format($total_credit_single, 2))
        @else
        @php($total_credit_single_label = "(".number_format(($total_credit_single * -1), 2).")")
        @endif

        <tr>
            <td colspan="3" class="alnright td5"><strong>{{ __('accounting.total_account') }}</strong></td>
            <td class="alnright td5"><strong>{{ $total_debit_single_label }}</strong></td>
            <td class="alnright td5"><strong>{{ $total_credit_single_label }}</strong></td>
            <td class="td5"></td>
        </tr>
        @endif

        <tr>
            <td colspan="6" class="td2">&nbsp;</td>
        </tr>

        @endif
        @endforeach

        @if (number_format($total_debit, 2) >= 0.00)
            @php($total_debit_label = number_format($total_debit, 2))
        @else
            @php($total_debit_label = "(".number_format(($total_debit * -1), 2).")")
        @endif

        @if (number_format($total_credit, 2) >= 0.00)
            @php($total_credit_label = number_format($total_credit, 2))
        @else
            @php($total_credit_label = "(".number_format(($total_credit * -1), 2).")")
        @endif

        <tr>
            <td colspan="3" class="alnright td5"><strong>{{ __('accounting.total_general') }}</strong></td>
            <td class="alnright td5"><strong>{{ $total_debit_label }}</strong></td>
            <td class="alnright td5"><strong>{{ $total_credit_label }}</strong></td>
            <td class="td5"></td>
        </tr>
    </table>
</body>
</html>
