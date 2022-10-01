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
      border-top: 1px solid black;
      border-bottom: 0.25px solid black;
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
      margin-top: 45px;
      margin-bottom: 25px;
    }
  </style>
</head>
<body>
  <table width="100%" class="table1">
    <tr>
      <td colspan="2">
        <center><strong>{{ mb_strtoupper($business_name) }}</strong></center>
        <center><strong>{{ mb_strtoupper($header) }}</strong></center>
        <center>{{ mb_strtoupper(__('accounting.accountant_report_values')) }}</center>
        
      </td>
    </tr>
    <tr>
      <td colspan="2"></td>
    </tr>

    <tr>
      <td class="alncenter"><strong>{{ mb_strtoupper(__('accounting.active_report')) }}</strong></td>
      <td class="alncenter"><strong>{{ mb_strtoupper(__('accounting.pasive_report')) }}</strong></td>
    </tr>
    <tr>
      <td style="width: 50%; vertical-align: top">
        <table width="100%" class="table1">

          @php($sum_level1 = 0.00)
          @php($sum_level2 = 0.00)
          @php($sum_debit = 0.00)

          @foreach($accounts_debit as $item)
          @if(number_format($item->balance, 2) != 0.00)


          @if(number_format($item->balance, 2) >= 0.00)
          @php($balance_label = number_format($item->balance, 2))
          @else
          @php($balance_label = "(".number_format(($item->balance * -1), 2).")")
          @endif


          @if($item->level == 2)
          @php($account_level1 = $item->balance)
          @php($sum_debit = $sum_debit + $item->balance)
          
          <tr>
            <td style="width: 58%"><strong>{{ $item->name }}</strong></td>
            <td style="width: 1%" class="alnright"></td>
            <td style="width: 13%" class="alnright"></td>
            <td style="width: 1%" class="alnright"></td>
            <td style="width: 13%" class="alnright"></td>
            <td style="width: 1%" class="alnright"></td>
            <td style="width: 13%" class="alnright">{{ $balance_label }}</td>
          </tr>
          @endif


          @if($business->balance_debit_levels_number > 1)
          
          @if($item->level == 3)
          @php($account_level2 = $item->balance)
          @php($sum_level1 = $sum_level1 + $item->balance)
          <tr>
            <td style="width: 58%"><strong>{{ $item->name }}</strong></td>
            <td style="width: 1%" class="alnright"></td>
            <td style="width: 13%" class="alnright"></td>
            <td style="width: 1%" class="alnright"></td>

            @if(number_format($account_level1, 2)  ==  number_format($sum_level1, 2))
            @php($account_level1 = 0.00)
            @php($sum_level1 = 0.00)
            <td style="width: 13%" class="alnright td3">{{ $balance_label }}</td>
            @else
            <td style="width: 13%" class="alnright">{{ $balance_label }}</td>
            @endif
            <td style="width: 1%" class="alnright"></td>
            <td style="width: 13%" class="alnright"></td>
          </tr>
          @endif
          @endif


          @if($business->balance_debit_levels_number > 2)

          @if($item->level == 4)
          @php($sum_level2 = $sum_level2 + $item->balance)
          <tr>
            <td style="width: 58%">{{ $item->name }}</td>
            <td style="width: 1%" class="alnright"></td>

            @if(number_format($account_level2, 2) == number_format($sum_level2, 2))
            @php($account_level2 = 0.00)
            @php($sum_level2 = 0.00)
            <td style="width: 13%" class="alnright td3">{{ $balance_label }}</td>
            @else
            <td style="width: 13%" class="alnright">{{ $balance_label }}</td>
            @endif            

            <td style="width: 1%" class="alnright"></td>
            <td style="width: 13%" class="alnright"></td>
            <td style="width: 1%" class="alnright"></td>
            <td style="width: 13%" class="alnright"></td>
          </tr>
          @endif

          
          @endif

          @endif         
          @endforeach
        </table>
      </td>
      <td style="width: 50%; vertical-align: top">
        <table width="100%" class="table1">

          @php($sum_level1 = 0.00)
          @php($sum_level2 = 0.00)
          @php($sum_credit = 0.00)

          @foreach($accounts_credit as $item)                   
          @if(number_format($item->balance, 2) != 0.00)

          @if(number_format($item->balance, 2) >= 0.00)
          @php($balance_label = number_format($item->balance, 2))
          @else
          @php($balance_label = "(".number_format(($item->balance * -1), 2).")")
          @endif

          @if($item->level == 2)
          @php($account_level1 = $item->balance)
          @php($sum_credit = $sum_credit + $item->balance)
          <tr>
            <td style="width: 58%"><strong>{{ $item->name }}</strong></td>
            <td style="width: 1%" class="alnright"></td>
            <td style="width: 13%" class="alnright"></td>
            <td style="width: 1%" class="alnright"></td>
            <td style="width: 13%" class="alnright"></td>
            <td style="width: 1%" class="alnright"></td>
            <td style="width: 13%" class="alnright">{{ $balance_label }}</td>
          </tr>
          @endif


          @if($business->balance_credit_levels_number > 1)

          @if($item->level == 3)
          @php($account_level2 = $item->balance)
          @php($sum_level1 = $sum_level1 + $item->balance)
          <tr>
            <td style="width: 58%"><strong>{{ $item->name }}</strong></td>
            <td style="width: 1%" class="alnright"></td>
            <td style="width: 13%" class="alnright"></td>
            <td style="width: 1%" class="alnright"></td>

            @if(number_format($account_level1, 2)  ==  number_format($sum_level1, 2))
            @php($account_level1 = 0.00)
            @php($sum_level1 = 0.00)
            <td style="width: 13%" class="alnright td3">{{ $balance_label }}</td>
            @else
            <td style="width: 13%" class="alnright">{{ $balance_label }}</td>
            @endif


            <td style="width: 1%" class="alnright"></td>
            <td style="width: 13%" class="alnright"></td>
          </tr>
          @endif
          @endif



          @if($business->balance_credit_levels_number > 2)          

          @if($item->level == 4)
          @php($sum_level2 = $sum_level2 + $item->balance)
          <tr>
            <td style="width: 58%">{{ $item->name }}</td>
            <td style="width: 1%" class="alnright"></td>

            @if(number_format($account_level2, 2) == number_format($sum_level2, 2))
            @php($account_level2 = 0.00)
            @php($sum_level2 = 0.00)
            <td style="width: 13%" class="alnright td3">{{ $balance_label }}</td>
            @else
            <td style="width: 13%" class="alnright">{{ $balance_label }}</td>
            @endif            

            <td style="width: 1%" class="alnright"></td>
            <td style="width: 13%" class="alnright"></td>
            <td style="width: 1%" class="alnright"></td>
            <td style="width: 13%" class="alnright"></td>
          </tr>
          @endif

          @endif
          
          

          @endif         
          @endforeach
          
        </table>
      </td>
    </tr>      
    <tr>

      @if(number_format($sum_debit, 2) >= 0.00)
      @php($sum_debit_label = number_format($sum_debit, 2))
      @else
      @php($sum_debit_label = "(".number_format(($sum_debit * -1), 2).")")
      @endif

      @if(number_format($sum_credit, 2) >= 0.00)
      @php($sum_credit_label = number_format($sum_credit, 2))
      @else
      @php($sum_credit_label = "(".number_format(($sum_credit * -1), 2).")")
      @endif

      <td>
        <table width="100%" class="table1">
          <tr>
            <td colspan="3" class="alncenter"><strong>{{ mb_strtoupper(__('accounting.total_active_report')) }}</strong></td>
            <td style="width: 1%" class="alnright"></td>
            <td style="width: 13%" class="alnright"></td>
            <td style="width: 1%" class="alnright"></td>
            <td style="width: 13%" class="alnright td5">{{ $sum_debit_label }}</td>
          </tr>

        </table>
      </td>
      <td>
        <table width="100%" class="table1">
          <tr>
            <td colspan="3" class="alncenter"><strong>{{ mb_strtoupper(__('accounting.total_pasive_report')) }}</strong></td>
            <td style="width: 1%" class="alnright"></td>
            <td style="width: 13%" class="alnright"></td>
            <td style="width: 1%" class="alnright"></td>
            <td style="width: 13%" class="alnright td5">{{ $sum_credit_label }}</td>
          </tr>

        </table>
      </td>   
    </tr>
  </table>
  <br>
  <br>
  @if($enable_foot_page == 'active')
  <table width="100%" class="table1">
    <tr>
      <td style="width: 35%" class="alncenter">
        ______________________________<br>
        {{ mb_strtoupper($owner) }}<br>
        {{ mb_strtoupper(__('accounting.owner')) }}
      </td>
      <td style="width: 30%" class="alncenter">
        ______________________________<br>
        {{ mb_strtoupper($accountant) }}<br>
        {{ mb_strtoupper(__('accounting.accountant')) }} 
      </td>
      <td style="width: 35%" class="alncenter">
        ______________________________<br>
        {{ mb_strtoupper($auditor) }}<br>
        {{ mb_strtoupper(__('accounting.auditor')) }}
      </td>
    </tr>
  </table>
  @else

  <table width="100%" class="table1">
    <tr>
      <td style="width: 50%" class="alncenter">
        ______________________________<br>
        {{ mb_strtoupper($owner) }}<br>
        {{ mb_strtoupper(__('accounting.owner')) }}
      </td>
      <td style="width: 50%" class="alncenter">
        ______________________________<br>
        {{ mb_strtoupper($accountant) }}<br>
        {{ mb_strtoupper(__('accounting.accountant')) }} 
      </td>
    </tr>
  </table>


  @endif
</body>
</html>