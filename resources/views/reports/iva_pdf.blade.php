<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@lang('accounting.iva_books')</title>    
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
            <td class="td2" colspan="4">
                <center><strong>{{ mb_strtoupper($header) }}</strong></center>
                <center><strong>{{ mb_strtoupper($header_date) }}</strong></center>
            </td>
        </tr>
        <tr>
            <td class="td2 alnleft" colspan="4">
                <strong>@lang('accounting.business_name'): {{ mb_strtoupper($business->name) }}</strong>
            </td>
        </tr>
        <tr>
            <td style="width: 25%" class="td2 alncenter"><strong>@lang('accounting.month'): {{ $month }} </strong></td>
            <td style="width: 25%" class="td2 alncenter"><strong>@lang('accounting.year'): {{ $year }} </strong></td>
            <td style="width: 25%" class="td2 alncenter"><strong>@lang('accounting.nit'): {{ $business->nit }} </strong></td>
            <td style="width: 25%" class="td2 alncenter"><strong>@lang('accounting.nrc'): {{ $business->nrc }} </strong></td>
        </tr>
    </table>

    @if($type == 'sells')
    <table class="table1" style="width: 100%;">
        <tr>
            <td class="alncenter" rowspan="2" style="width: 11%;">
                {{ mb_strtoupper(__('accounting.issue_date')) }}
            </td>
            <td class="alncenter" colspan="2">
                {{ mb_strtoupper(__('accounting.issued_documents')) }}
            </td>
            <td class="alncenter" rowspan="2" style="width: 11%;">
                {{ mb_strtoupper(__('accounting.computerized_system')) }}
            </td>
            <td class="alncenter" colspan="3">
                {{ mb_strtoupper(__('accounting.sells_report')) }}
            </td>
            <td class="alncenter" rowspan="2" style="width: 11%;">
                {{ mb_strtoupper(__('accounting.total_own')) }}
            </td>
            <td class="alncenter" rowspan="2" style="width: 11%;">
                {{ mb_strtoupper(__('accounting.total_third')) }}
            </td>           
        </tr>

        <tr>
            <td class="alncenter" style="width: 11%;">
                {{ mb_strtoupper(__('accounting.from_no')) }}
            </td>
            <td class="alncenter" style="width: 11%;">
                {{ mb_strtoupper(__('accounting.to_no')) }}
            </td>
            <td class="alncenter" style="width: 11%;">
                {{ mb_strtoupper(__('accounting.exempt')) }}
            </td>
            <td class="alncenter" style="width: 11%;">
                {{ mb_strtoupper(__('accounting.internal_taxed')) }}
            </td>
            <td class="alncenter" style="width: 11%;">
                {{ mb_strtoupper(__('accounting.exports')) }}
            </td>
        </tr>

        @php($cont = 0)
        @php($total_exempt_amount = 0)
        @php($total_total_before_tax = 0)
        @php($total_tax_amount = 0)
        @php($total_perc_ret_amount = 0)
        @php($total_total_final = 0)
        @foreach($lines as $item)
        @php($cont = $cont + 1)
        <tr>
            <td>{{ $item->date_transaction }}</td>
            <td>{{ $item->start }}</td>
            <td>{{ $item->end }}</td>
            <td></td>
            <td class="alnright">{{--  {{ number_format($item->exempt_amount, 2) }} --}}</td>
            <td class="alnright">{{ number_format($item->final_total, 2) }}</td>
            <td></td>
            <td class="alnright">{{ number_format($item->final_total, 2) }}</td>
            <td></td>
            
            {{-- @php($total_exempt_amount = $total_exempt_amount + $item->exempt_amount) --}}
            @php($total_total_before_tax = $total_total_before_tax + $item->total_before_tax)
            @php($total_total_final = $item->final_total + $total_total_final)
        </tr>
        @endforeach
        <tr>
            <td colspan="4"> {{ mb_strtoupper(__('accounting.totals')) }}</td>
            <td class="alnright"><strong>{{-- {{ number_format($total_exempt_amount, 2) }} --}}</strong></td>
            <td class="alnright"><strong>{{ number_format($total_total_final, 2) }}</strong></td>
            <td></td>
            <td class="alnright"><strong>{{ number_format($total_total_final, 2) }}</strong></td>
            <td></td>
            
        </tr>


    </table>

    @endif

    @if($type == 'sells_taxpayer')
    <table class="table1" style="width: 100%;">
        <tr>
            <td class="alncenter" rowspan="3" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.number')) }}
            </td>
            <td class="alncenter" rowspan="3" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.issue_date')) }}
            </td>
            <td class="alncenter" rowspan="3" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.correlative_number')) }}
            </td>
            <td class="alncenter" rowspan="3" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.unique_form')) }}
            </td>
            <td class="alncenter" rowspan="3" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.nrc')) }}
            </td>
            <td class="alncenter" rowspan="3" style="width: 35%;">
                {{ mb_strtoupper(__('accounting.customer_name')) }}
            </td>
            <td class="alncenter" colspan="6">
                {{ mb_strtoupper(__('accounting.general_operations')) }}
            </td>
            <td class="alncenter" rowspan="3" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.retent')) }}
            </td>
            <td class="alncenter" rowspan="3" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.total_sells')) }}
            </td>
        </tr>

        <tr>
            <td class="alncenter" colspan="3">
                {{ mb_strtoupper(__('accounting.sell_own')) }}
            </td>
            <td class="alncenter" colspan="3">
                {{ mb_strtoupper(__('accounting.sell_third')) }}
            </td>
        </tr>

        <tr>
            <td class="alncenter" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.exempt')) }}
            </td>
            <td class="alncenter" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.internal')) }}
            </td>
            <td class="alncenter" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.fiscal_debit')) }}
            </td>
            <td class="alncenter" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.exempt')) }}
            </td>
            <td class="alncenter" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.internal')) }}
            </td>
            <td class="alncenter" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.fiscal_debit')) }}
            </td>           
        </tr>
        @php($cont = 0)
        @php($total_exempt_amount = 0)
        @php($total_total_before_tax = 0)
        @php($total_tax_amount = 0)
        @php($total_perc_ret_amount = 0)
        @php($total_total_final = 0)
        @foreach($lines as $item)
        @php($cont = $cont + 1)
        <tr>
            <td>{{ $cont }}</td>
            <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->transaction_date)->format('d/m/Y') }}</td>
            <td>{{ $item->document }}</td>
            <td></td>
            <td>{{ $item->nrc }}</td>
            
            @if($item->status == 'annulled')
            <td>{{ mb_strtoupper(__('accounting.annulled')) }}</td>
            <td class="alnright">{{-- {{ number_format($item->exempt_amount, 2) }} --}}</td>
            <td class="alnright"></td>
            <td class="alnright"></td>
            @else
            <td>{{ $item->customer_name }}</td>
            <td class="alnright">{{-- {{ number_format($item->exempt_amount, 2) }} --}}</td>
            <td class="alnright">{{ number_format($item->total_before_tax, 2) }}</td>
            <td class="alnright">{{ number_format($item->tax_amount, 2) }}</td>
            @endif
            
            <td></td>
            <td></td>
            <td></td>
            <td class="alnright">{{-- {{ number_format($item->perc_ret_amount, 2) }} --}}</td>
            {{-- @php($total_final = (($item->exempt_amount + $item->total_before_tax + $item->tax_amount) - $item->exempt_amount)) --}}

            @if($item->status == 'annulled')
            <td class="alnright"></td>
            @else
            <td class="alnright">{{ number_format($item->final_total, 2) }}</td>
            @endif

            
            
            {{-- @php($total_exempt_amount = $total_exempt_amount + $item->exempt_amount) --}}

            @if($item->status != 'annulled')
            @php($total_total_before_tax = $total_total_before_tax + $item->total_before_tax)
            @php($total_tax_amount = $total_tax_amount + $item->tax_amount)
            @php($total_total_final = $total_total_final + $item->final_total)
            @endif


            {{-- @php($total_perc_ret_amount = $total_perc_ret_amount + $item->perc_ret_amount) --}}
            
        </tr>
        @endforeach
        <tr>
            <td colspan="6"> {{ mb_strtoupper(__('accounting.totals')) }}</td>
            
            <td class="alnright"><strong>{{-- {{ number_format($total_exempt_amount, 2) }} --}}</strong></td>
            <td class="alnright"><strong>{{ number_format($total_total_before_tax, 2) }}</strong></td>
            <td class="alnright"><strong>{{ number_format($total_tax_amount, 2) }}</strong></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="alnright"><strong>{{-- {{ number_format($total_perc_ret_amount, 2) }} --}}</strong></td>
            <td class="alnright"><strong>{{ number_format($total_total_final, 2) }}</strong></td>
        </tr>
    </table>
    @endif

    @if($type == 'sells_exports')

    <table class="table1" style="width: 100%;">
        <tr>
            <td class="alncenter" style="width: 8%;">
                {{ mb_strtoupper(__('accounting.number')) }}
            </td>
            <td class="alncenter" style="width: 8%;">
                {{ mb_strtoupper(__('accounting.document')) }}
            </td>
            <td class="alncenter" style="width: 8%;">
                {{ mb_strtoupper(__('accounting.date')) }}
            </td>
            <td class="alncenter" style="width: 12%;">
                {{ mb_strtoupper(__('accounting.nit')) }}
            </td>
            <td class="alncenter" style="width: 8%;">
                {{ mb_strtoupper(__('accounting.nrc')) }}
            </td>
            <td class="alncenter" style="width: 40%;">
                {{ mb_strtoupper(__('accounting.customers')) }}
            </td>
            <td class="alncenter" style="width: 8%;">
                {{ mb_strtoupper(__('accounting.sells')) }}
            </td>
            <td class="alncenter" style="width: 8%;">
                {{ mb_strtoupper(__('accounting.total')) }}
            </td>
            
        </tr>

        @php($cont = 0)
        @php($total_total_before_tax = 0)
        @php($total_tax_amount = 0)
        @php($total_total_final = 0)
        @foreach($lines as $item)
        @php($cont = $cont + 1)
        <tr>
            <td>{{ $cont }}</td>
            <td>{{ $item->document }}</td>
            <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->transaction_date)->format('d/m/Y') }}</td>            
            <td>{{ $item->nit }}</td>
            <td>{{ $item->nrc }}</td>
            
            @if($item->status == 'annulled')
            <td>{{ mb_strtoupper(__('accounting.annulled')) }}</td>
            @else
            <td>{{ $item->customer_name }}</td>
            @endif

            @if($item->status == 'annulled')
            <td class="alnright"></td>
            <td class="alnright"></td>
            @else
            <td class="alnright">{{ number_format($item->final_total, 2) }}</td>
            <td class="alnright">{{ number_format($item->final_total, 2) }}</td>
            @endif

            @if($item->status != 'annulled')
            @php($total_total_before_tax = $total_total_before_tax + $item->total_before_tax)
            @php($total_total_final = $total_total_final + $item->final_total)
            @endif
            
        </tr>
        @endforeach
        <tr>
            <td class="td_none"></td>
            <td class="td_none"></td>
            <td class="td_none" colspan="4"><ins> {{ __('accounting.description') }}</ins> </td>
            <td class="td_none alnright"><ins>{{ __('accounting.subtotal') }}</ins></td>
            <td class="td_none alnright"><ins>{{ __('accounting.total') }}</ins></td>
        </tr>
        <tr>
            <td class="td_none"></td>
            <td class="td_none"></td>
            <td class="td_none" colspan="4"><ins>{{ __('accounting.export_invoice') }}</ins></td>
            <td class="td_total alnright">{{ number_format($total_total_final, 2) }}</td>
            <td class="td_total alnright">{{ number_format($total_total_final, 2) }}</td>
        </tr>
    </table>

    @endif

    @if($type == 'purchases')
    <table class="table1" style="width: 100%;">
        <tr>
            <td class="alncenter" rowspan="2" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.number')) }}
            </td>
            <td class="alncenter" rowspan="2" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.issue_date')) }}
            </td>
            <td class="alncenter" rowspan="2" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.document_number')) }}
            </td>
            <td class="alncenter" rowspan="2" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.nrc')) }}
            </td>
            <td class="alncenter" rowspan="2" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.excluded_dui')) }}
            </td>
            <td class="alncenter" rowspan="2" style="width: 35%;">
                {{ mb_strtoupper(__('accounting.supplier_name')) }}
            </td>
            <td class="alncenter" colspan="2">
                {{ mb_strtoupper(__('accounting.exempt_purchases')) }}
            </td>
            <td class="alncenter" colspan="3">
                {{ mb_strtoupper(__('accounting.taxed_purchases')) }}
            </td>
            <td class="alncenter" rowspan="2" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.total_purchases')) }}
            </td>
            <td class="alncenter" rowspan="2" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.recept')) }}
            </td>
            <td class="alncenter" rowspan="2" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.purchases_excluded')) }}
            </td>
        </tr>       

        <tr>
            <td class="alncenter" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.internal_locals')) }}
            </td>
            <td class="alncenter" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.imported_or_internationals')) }}
            </td>
            <td class="alncenter" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.internal_locals')) }}
            </td>
            <td class="alncenter" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.imported_or_internationals')) }}
            </td>
            <td class="alncenter" style="width: 5%;">
                {{ mb_strtoupper(__('accounting.fiscal_credit')) }}
            </td>                    
        </tr>

        @php($cont = 0)
        @php($total_exempt_amount = 0)
        @php($total_total_before_tax = 0)
        @php($total_tax_amount = 0)
        @php($total_perc_ret_amount = 0)
        @php($total_total_final = 0)

        @foreach($lines as $item)
        @php($cont = $cont + 1)
        <tr>
            <td>{{ $cont }}</td>
            <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->transaction_date)->format('d/m/Y') }}</td>
            <td>{{ $item->document }}</td>
            <td>{{ $item->nrc }}</td>
            <td></td>
            <td>{{ $item->customer_name }}</td>
            <td class="alnright">{{-- {{ number_format($item->exempt_amount, 2) }} --}}</td>
            <td></td>
            <td class="alnright">{{ number_format($item->total_before_tax, 2) }}</td>
            <td></td>
            <td class="alnright">{{ number_format($item->tax_amount, 2) }}</td>

            {{-- @php($total_final = (($item->exempt_amount + $item->total_before_tax + $item->tax_amount) - $item->exempt_amount)) --}}

            @php($total_final = $item->total_before_tax + $item->tax_amount)
            <td class="alnright">{{ number_format($item->final_total, 2) }}</td>



            <td class="alnright">{{-- {{ number_format($item->perc_ret_amount, 2) }} --}}</td>

            <td></td>

            {{-- @php($total_exempt_amount = $total_exempt_amount + $item->exempt_amount) --}}
            @php($total_total_before_tax = $total_total_before_tax + $item->total_before_tax)
            @php($total_tax_amount = $total_tax_amount + $item->tax_amount)
            {{-- @php($total_perc_ret_amount = $total_perc_ret_amount + $item->perc_ret_amount) --}}
            @php($total_total_final = $item->final_total + $total_total_final)
        </tr>
        @endforeach
        <tr>
            <td colspan="6"> {{ mb_strtoupper(__('accounting.totals')) }}</td>

            <td class="alnright"><strong>{{-- {{ number_format($total_exempt_amount, 2) }} --}}</strong></td>


            <td></td>

            <td class="alnright"><strong>{{ number_format($total_total_before_tax, 2) }}</strong></td>

            <td></td>


            <td class="alnright"><strong>{{ number_format($total_tax_amount, 2) }}</strong></td>

            <td class="alnright"><strong>{{ number_format($total_total_final, 2) }}</strong></td>
            <td class="alnright"><strong>{{-- {{ number_format($total_perc_ret_amount, 2) }} --}}</strong></td>
            <td></td>
        </tr>


    </table>
    @endif


</body>
</html>