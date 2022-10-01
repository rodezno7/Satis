<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>@lang('accounting.bank_transactions_report')</title>    
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
    td
    {
      border: 0.25px solid black;
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
    <tr>
      <td class="td2">
        <center><strong>{{ mb_strtoupper($business_name) }}</strong></center>
        <center><strong>{{ mb_strtoupper(__('accounting.accountant_report_values')) }}</strong></center>
        <strong>{{ mb_strtoupper($header1) }}</strong>
        <strong>{{ mb_strtoupper($header2) }}</strong>
      </td>
    </tr>
  </table>


  @if($type_transaction == 'outflow')

  <strong>{{ mb_strtoupper(__('accounting.outflow')) }}</strong>
  <table class="table2" style=" width: 100%;">
    <tr>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.date'))}}</th>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.bank_account'))}}</th>
      <th style="width: 40%;">{{mb_strtoupper(__('accounting.description'))}}</th>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.type'))}}</th>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.check_number'))}}</th>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.amount'))}}</th>
    </tr>
    @php($total_credit = 0.00)
    @foreach($transactions_credit as $item)
    @php($total_credit = $total_credit + $item->amount)

    @if(number_format($item->amount, 2) >= 0.00)
    @php($amount_label = number_format($item->amount, 2))
    @else
    @php($amount_label = number_format(($item->amount * -1), 2))
    @endif
    <tr>
      <td class="alnleft">{{ $item->date_transaction }}</td>
      <td class="alnleft">{{ $item->bank }} {{ $item->number_account }}</td>
      <td class="alnleft">{{ $item->description }}</td>
      <td class="alnleft">{{ $item->type_transaction }}</td>
      <td class="alnleft">{{ $item->check_number }}</td>
      <td class="alnright">{{ $amount_label }}</td>
    </tr>
    @endforeach

    @if(number_format($total_credit, 2) >= 0.00)
    @php($total_credit_label = number_format($total_credit, 2))
    @else
    @php($total_credit_label = number_format(($total_credit * -1), 2))
    @endif


    <tr>
      <td colspan="5" class="alnright"><strong>{{ mb_strtoupper(__('accounting.totals')) }}</strong></td>
      <td class="alnright"><strong>{{ $total_credit_label }}</strong></td>
    </tr>
  </table>

  @endif

  @if($type_transaction == 'all')

  <strong>{{ mb_strtoupper(__('accounting.inflow')) }}</strong>
  <table class="table2" style=" width: 100%;">
    <tr>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.date'))}}</th>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.bank_account'))}}</th>
      <th style="width: 40%;">{{mb_strtoupper(__('accounting.description'))}}</th>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.type'))}}</th>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.reference'))}}</th>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.amount'))}}</th>
    </tr>
    @php($total_debit = 0.00)
    @foreach($transactions_debit as $item)
    @php($total_debit = $total_debit + $item->amount)

    @if(number_format($item->amount, 2) >= 0.00)
    @php($amount_label = number_format($item->amount, 2))
    @else
    @php($amount_label = number_format(($item->amount * -1), 2))
    @endif

    <tr>
      <td class="alnleft">{{ $item->date_transaction }}</td>
      <td class="alnleft">{{ $item->bank }} {{ $item->number_account }}</td>
      <td class="alnleft">{{ $item->description }}</td>
      <td class="alnleft">{{ $item->type_transaction }}</td>
      <td class="alnleft">{{ $item->reference }}</td>
      <td class="alnright">{{ $amount_label }}</td>
    </tr>
    @endforeach

    @if(number_format($total_debit, 2) >= 0.00)
    @php($total_debit_label = number_format($total_debit, 2))
    @else
    @php($total_debit_label = number_format(($total_debit * -1), 2))
    @endif

    <tr>
      <td colspan="5" class="alnright"><strong>{{ mb_strtoupper(__('accounting.totals')) }}</strong></td>
      <td class="alnright"><strong>{{ $total_debit_label }}</strong></td>
    </tr>
  </table>


  <br>

  <strong>{{ mb_strtoupper(__('accounting.outflow')) }}</strong>
  <table class="table2" style=" width: 100%;">
    <tr>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.date'))}}</th>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.bank_account'))}}</th>
      <th style="width: 40%;">{{mb_strtoupper(__('accounting.description'))}}</th>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.type'))}}</th>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.reference'))}}</th>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.amount'))}}</th>
    </tr>
    @php($total_credit = 0.00)
    @foreach($transactions_credit as $item)
    @php($total_credit = $total_credit + $item->amount)

    @if(number_format($item->amount, 2) >= 0.00)
    @php($amount_label = number_format($item->amount, 2))
    @else
    @php($amount_label = number_format(($item->amount * -1), 2))
    @endif

    <tr>
      <td class="alnleft">{{ $item->date_transaction }}</td>
      <td class="alnleft">{{ $item->bank }} {{ $item->number_account }}</td>
      <td class="alnleft">{{ $item->description }}</td>
      <td class="alnleft">{{ $item->type_transaction }}</td>
      <td class="alnleft">{{ $item->reference }}</td>
      <td class="alnright">{{ $amount_label }}</td>
    </tr>
    @endforeach

    @if(number_format($total_credit, 2) >= 0.00)
    @php($total_credit_label = number_format($total_credit, 2))
    @else
    @php($total_credit_label = number_format(($total_credit * -1), 2))
    @endif

    <tr>
      <td colspan="5" class="alnright"><strong>{{ mb_strtoupper(__('accounting.totals')) }}</strong></td>
      <td class="alnright"><strong>{{ $total_credit_label }}</strong></td>
    </tr>
  </table>

  @endif






    @if($type_transaction == 'inflow')

  <strong>{{ mb_strtoupper(__('accounting.inflow')) }}</strong>
  <table class="table2" style=" width: 100%;">
    <tr>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.date'))}}</th>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.bank_account'))}}</th>
      <th style="width: 40%;">{{mb_strtoupper(__('accounting.description'))}}</th>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.type'))}}</th>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.reference'))}}</th>
      <th style="width: 10%;">{{mb_strtoupper(__('accounting.amount'))}}</th>
    </tr>
    @php($total_debit = 0.00)
    @foreach($transactions_debit as $item)
    @php($total_debit = $total_debit + $item->amount)

    @if(number_format($item->amount, 2) >= 0.00)
    @php($amount_label = number_format($item->amount, 2))
    @else
    @php($amount_label = number_format(($item->amount * -1), 2))
    @endif

    <tr>
      <td class="alnleft">{{ $item->date_transaction }}</td>
      <td class="alnleft">{{ $item->bank }} {{ $item->number_account }}</td>
      <td class="alnleft">{{ $item->description }}</td>
      <td class="alnleft">{{ $item->type_transaction }}</td>
      <td class="alnleft">{{ $item->reference }}</td>
      <td class="alnright">{{ $amount_label }}</td>
    </tr>
    @endforeach

    @if(number_format($total_debit, 2) >= 0.00)
    @php($total_debit_label = number_format($total_debit, 2))
    @else
    @php($total_debit_label = number_format(($total_debit * -1), 2))
    @endif

    <tr>
      <td colspan="5" class="alnright"><strong>{{ mb_strtoupper(__('accounting.totals')) }}</strong></td>
      <td class="alnright"><strong>{{ $total_debit_label }}</strong></td>
    </tr>
  </table>
  @endif
</body>
</html>