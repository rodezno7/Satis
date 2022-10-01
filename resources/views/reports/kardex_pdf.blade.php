<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@lang('kardex.kardex')</title>    
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
        .td_none
        {
            border-bottom: none;
            border-top: none;
            border-left: none;
            border-right: none;
        }
        .td_total
        {
            border-bottom: 0.25px solid black;
            border-top: none;
            border-left: none;
            border-right: none;
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
                <center><strong>{{ mb_strtoupper(__('kardex.kardex')) }} {{ mb_strtoupper($header_date) }}</strong></center>
            </td>
        </tr>
    </table>
    <br>

    <strong>{{ mb_strtoupper(__('kardex.product')) }}:</strong> {{ mb_strtoupper($product) }} <br>
    <strong>{{ mb_strtoupper(__('kardex.warehouse')) }}:</strong> {{ $warehouse }} <br>
    <strong>{{ mb_strtoupper(__('kardex.initial_quantity')) }}:</strong> {{ $initial_quantity }}

    <br>
    <br>
    <table class="table2" width="100%">
        <tr>
            <td class="alncenter" style="width: 5%;"><strong>{{ mb_strtoupper(__('kardex.number')) }}</strong></td>
            <td class="alncenter" style="width: 19%;"><strong>{{ mb_strtoupper(__('kardex.date')) }}</strong></td>
            <td class="alncenter" style="width: 19%;"><strong>{{ mb_strtoupper(__('kardex.initial_quantity')) }}</strong></td>
            <td class="alncenter" style="width: 20%;"><strong>{{ mb_strtoupper(__('kardex.type')) }}</strong></td>
            <td class="alncenter" style="width: 19%;"><strong>{{ mb_strtoupper(__('kardex.document')) }}</strong></td>
            <td class="alncenter" style="width: 19%;"><strong>{{ mb_strtoupper(__('kardex.quantity')) }}</strong></td>
            <td class="alncenter" style="width: 19%;"><strong>{{ mb_strtoupper(__('kardex.final_quantity')) }}</strong></td>
        </tr>
        @php($final_quantity = $initial_quantity)
        @php($cont = 1)
        @foreach($lines as $item)
        <tr>
            <td class="alnright">{{ $cont }}</td>
            @php($cont = $cont + 1)
            <td class="alnleft">{{ $item->date }}</td>
            <td class="alnright">{{ number_format($final_quantity, 4) }}</td>
            <td>
                @if($item->type == 'opening_stock')
                @if($item->status != 'annulled')
                @lang('kardex.opening_stock')
                @php($final_quantity = $final_quantity + $item->quantity)
                @else
                @lang('kardex.opening_stock_annulled')
                @php($final_quantity = $final_quantity)
                @endif                
                @endif

                @if($item->type == 'purchase')
                @if($item->status != 'annulled')
                @lang('kardex.purchase')
                @php($final_quantity = $final_quantity + $item->quantity)
                @else
                @lang('kardex.purchase_annulled')
                @php($final_quantity = $final_quantity)
                @endif
                @endif

                @if($item->type == 'purchase_return')
                @if($item->status != 'annulled')
                @lang('kardex.purchase_return')
                @php($final_quantity = $final_quantity)
                @else
                @lang('kardex.purchase_return_annulled')
                @php($final_quantity = $final_quantity)
                @endif
                @endif

                @if($item->type == 'purchase_transfer')
                @if($item->status != 'annulled')
                @lang('kardex.purchase_transfer')
                @php($final_quantity = $final_quantity)
                @else
                @lang('kardex.purchase_transfer_annulled')
                @php($final_quantity = $final_quantity)
                @endif                
                @endif

                @if($item->type == 'sell')
                @if($item->status != 'annulled')
                 @lang('kardex.sell')
                @php($final_quantity = $final_quantity - $item->quantity)
                @else
                 @lang('kardex.sell_annulled')
                @php($final_quantity = $final_quantity)
                @endif
                @endif

                @if($item->type == 'sell_return')
                @if($item->status != 'annulled')
                @lang('kardex.sell_return')
                @php($final_quantity = $final_quantity)
                @else
                @lang('kardex.sell_return_annulled')
                @php($final_quantity = $final_quantity)
                @endif
                @endif

                @if($item->type == 'sell_transfer')
                @if($item->status != 'annulled')
                @lang('kardex.sell_tranfer')
                @php($final_quantity = $final_quantity)
                @else
                @lang('kardex.sell_tranfer_annulled')
                @php($final_quantity = $final_quantity)
                @endif
                @endif

                @if($item->type == 'ADJUSTMENT_IN')
                @if($item->status != 'annulled')
                @lang('kardex.adjustment_in')
                @php($final_quantity = $final_quantity + $item->quantity)
                @else
                @lang('kardex.adjustment_in_annulled')
                @php($final_quantity = $final_quantity)
                @endif
                @endif

                @if($item->type == 'ADJUSTMENT_OUT')
                @if($item->status != 'annulled')
                @lang('kardex.adjustment_out')
                @php($final_quantity = $final_quantity - $item->quantity)
                @else
                @lang('kardex.adjustment_out_annulled')
                @php($final_quantity = $final_quantity)
                @endif
                @endif

                @if($item->type == 'kit_out')
                @if($item->status != 'annulled')
                @lang('kardex.kit_out')
                @php($final_quantity = $final_quantity - $item->quantity)
                @else
                @lang('kardex.kit_out_annulled')
                @php($final_quantity = $final_quantity)
                @endif
                @endif

            </td>
            <td class="alnleft">
                @if($item->document != null)
                {{ $item->document }}
                @else
                N/A
                @endif
            </td>
            <td class="alnright">{{ number_format($item->quantity, 4) }}</td>
            <td class="alnright">{{ number_format($final_quantity, 4) }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>