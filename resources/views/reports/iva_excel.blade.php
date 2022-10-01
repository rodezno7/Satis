

@if($type == 'sells')

<table>
    <tr>
        <td colspan="9">
            <strong>{{ mb_strtoupper($header) }}</strong>
        </td>
    </tr>
    <tr>
        <td colspan="9">
            <strong>{{ mb_strtoupper($header_date) }}</strong>
        </td>
    </tr>
    <tr>
        <td colspan="9">
            <strong>@lang('accounting.business_name'): {{ mb_strtoupper($business->name) }}</strong>
        </td>
    </tr>
    <tr>
        <td><strong>@lang('accounting.month'): {{ $month }} </strong></td>
        <td></td>
        <td></td>
        <td><strong>@lang('accounting.year'): {{ $year }} </strong></td>
        <td></td>
        <td><strong>@lang('accounting.nit'): {{ $business->nit }} </strong></td>
        <td></td>
        <td><strong>@lang('accounting.nrc'): {{ $business->nrc }} </strong></td>
        <td></td>
    </tr>
</table>

<table>
    <tr>
        <td rowspan="2">
            <strong>{{ mb_strtoupper(__('accounting.issue_date')) }}</strong>
        </td>
        <td colspan="2">
            <strong>{{ mb_strtoupper(__('accounting.issued_documents')) }}</strong>
        </td>
        <td rowspan="2">
            <strong>{{ mb_strtoupper(__('accounting.computerized_system')) }}</strong>
        </td>
        <td colspan="3">
            <strong>{{ mb_strtoupper(__('accounting.sells_report')) }}</strong>
        </td>
        <td rowspan="2">
            <strong>{{ mb_strtoupper(__('accounting.total_own')) }}</strong>
        </td>
        <td rowspan="2">
            <strong>{{ mb_strtoupper(__('accounting.total_third')) }}</strong>
        </td>           
    </tr>

    <tr>
        <td>
         <strong> {{ mb_strtoupper(__('accounting.from_no')) }}</strong>
     </td>
     <td>
         <strong> {{ mb_strtoupper(__('accounting.to_no')) }}</strong>
     </td>
     <td>
         <strong> {{ mb_strtoupper(__('accounting.exempt')) }}</strong>
     </td>
     <td>
         <strong> {{ mb_strtoupper(__('accounting.internal_taxed')) }}</strong>
     </td>
     <td>
         <strong> {{ mb_strtoupper(__('accounting.exports')) }}</strong>
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
    <td>{{-- {{ $item->exempt_amount }} --}}</td>
    <td>{{ $item->final_total }}</td>
    <td></td>
    <td>{{ $item->final_total }}</td>
    <td></td>

    {{-- @php($total_exempt_amount = $total_exempt_amount + $item->exempt_amount) --}}
    @php($total_total_before_tax = $total_total_before_tax + $item->total_before_tax)
    @php($total_total_final = $item->final_total + $total_total_final)
</tr>
@endforeach
<tr>
    <td colspan="4"><strong> {{ mb_strtoupper(__('accounting.totals')) }}</strong></td>
    <td><strong>{{-- {{ $total_exempt_amount }} --}}</strong></td>
    <td><strong>{{ $total_total_final }}</strong></td>
    <td></td>
    <td><strong>{{ $total_total_final }}</strong></td>
    <td></td>

</tr>


</table>

@endif

@if($type == 'sells_taxpayer')

<table>
    <tr>
        <td colspan="14">
            <strong>{{ mb_strtoupper($header) }}</strong>
        </td>
    </tr>
    <tr>
        <td colspan="14">
            <strong>{{ mb_strtoupper($header_date) }}</strong>
        </td>
    </tr>
    <tr>
        <td colspan="14">
            <strong>@lang('accounting.business_name'): {{ mb_strtoupper($business->name) }}</strong>
        </td>
    </tr>
    <tr>
        <td><strong>@lang('accounting.month'): {{ $month }} </strong></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><strong>@lang('accounting.year'): {{ $year }} </strong></td>        
        <td><strong>@lang('accounting.nit'): {{ $business->nit }} </strong></td>
        <td></td>
        <td></td>
        <td></td>
        <td><strong>@lang('accounting.nrc'): {{ $business->nrc }} </strong></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>


<table>
    <tr>
        <td rowspan="3">
            <strong>{{ mb_strtoupper(__('accounting.number')) }}</strong>
        </td>
        <td rowspan="3">
            <strong>{{ mb_strtoupper(__('accounting.issue_date')) }}</strong>
        </td>
        <td rowspan="3">
            <strong>{{ mb_strtoupper(__('accounting.correlative_number')) }}</strong>
        </td>
        <td rowspan="3">
            <strong>{{ mb_strtoupper(__('accounting.unique_form')) }}</strong>
        </td>
        <td rowspan="3">
            <strong>{{ mb_strtoupper(__('accounting.nrc')) }}</strong>
        </td>
        <td rowspan="3">
            <strong>{{ mb_strtoupper(__('accounting.customer_name')) }}</strong>
        </td>
        <td colspan="6">
            <strong>{{ mb_strtoupper(__('accounting.general_operations')) }}</strong>
        </td>
        <td rowspan="3">
            <strong>{{ mb_strtoupper(__('accounting.retent')) }}</strong>
        </td>
        <td rowspan="3">
            <strong>{{ mb_strtoupper(__('accounting.total_sells')) }}</strong>
        </td>
    </tr>

    <tr>
        <td colspan="3">
            <strong>{{ mb_strtoupper(__('accounting.sell_own')) }}</strong>
        </td>
        <td colspan="3">
            <strong>{{ mb_strtoupper(__('accounting.sell_third')) }}</strong>
        </td>
    </tr>

    <tr>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.exempt')) }}</strong>
        </td>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.internal')) }}</strong>
        </td>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.fiscal_debit')) }}</strong>
        </td>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.exempt')) }}</strong>
        </td>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.internal')) }}</strong>
        </td>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.fiscal_debit')) }}</strong>
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
        <td>{{-- {{ number_format($item->exempt_amount, 2) }} --}}</td>
        <td></td>
        <td></td>
        @else
        <td>{{ $item->customer_name }}</td>
        <td>{{-- {{ number_format($item->exempt_amount, 2) }} --}}</td>
        <td>{{ $item->total_before_tax }}</td>
        <td>{{ $item->tax_amount }}</td>
        @endif
        <td></td>
        <td></td>
        <td></td>
        <td>{{-- {{ $item->perc_ret_amount }} --}}</td>
        {{-- @php($total_final = (($item->exempt_amount + $item->total_before_tax + $item->tax_amount) - $item->exempt_amount)) --}}

        @if($item->status == 'annulled')
        <td></td>
        @else
        <td>{{ $item->final_total }}</td>
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
        <td colspan="6"><strong>{{ mb_strtoupper(__('accounting.totals')) }}</strong></td>

        <td><strong>{{-- {{ $total_exempt_amount }} --}}</strong></td>
        <td><strong>{{ $total_total_before_tax }}</strong></td>
        <td><strong>{{ $total_tax_amount }}</strong></td>
        <td></td>
        <td></td>
        <td></td>
        <td><strong>{{-- {{ $total_perc_ret_amount }} --}}</strong></td>
        <td><strong>{{ $total_total_final }}</strong></td>
    </tr>
</table>
@endif


@if($type == 'sells_exports')

<table>
    <tr>
        <td colspan="8">
            <strong>{{ mb_strtoupper($header) }}</strong>
        </td>
    </tr>
    <tr>
        <td colspan="8">
            <strong>{{ mb_strtoupper($header_date) }}</strong>
        </td>
    </tr>
    <tr>
        <td colspan="8">
            <strong>@lang('accounting.business_name'): {{ mb_strtoupper($business->name) }}</strong>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><strong>@lang('accounting.month'): {{ $month }} </strong></td>
        <td></td>        
        <td><strong>@lang('accounting.year'): {{ $year }} </strong></td>
        <td></td>        
        <td><strong>@lang('accounting.nit'): {{ $business->nit }} </strong></td>
        <td colspan="2"><strong>@lang('accounting.nrc'): {{ $business->nrc }} </strong></td>
    </tr>
</table>


<table>
    <tr>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.number')) }}</strong>
        </td>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.document')) }}</strong>
        </td>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.date')) }}</strong>
        </td>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.nit')) }}</strong>
        </td>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.nrc')) }}</strong>
        </td>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.customers')) }}</strong>
        </td>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.sells')) }}</strong>
        </td>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.total')) }}</strong>
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
        <td></td>
        <td></td>
        @else
        <td>{{ $item->final_total }}</td>
        <td>{{ $item->final_total }}</td>
        @endif

        @if($item->status != 'annulled')
        @php($total_total_before_tax = $total_total_before_tax + $item->total_before_tax)
        @php($total_total_final = $total_total_final + $item->final_total)
        @endif

    </tr>
    @endforeach
    <tr>
        <td></td>
        <td></td>
        <td colspan="4">{{ __('accounting.description') }}</td>
        <td>{{ __('accounting.subtotal') }}</td>
        <td>{{ __('accounting.total') }}</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="4">{{ __('accounting.export_invoice') }}</td>
        <td>{{ $total_total_final }}</td>
        <td>{{ $total_total_final }}</td>
    </tr>
</table>

@endif

@if($type == 'purchases')

<table>
    <tr>
        <td colspan="14">
            <strong>{{ mb_strtoupper($header) }}</strong>
        </td>
    </tr>
    <tr>
        <td colspan="14">
            <strong>{{ mb_strtoupper($header_date) }}</strong>
        </td>
    </tr>
    <tr>
        <td colspan="14">
            <strong>@lang('accounting.business_name'): {{ mb_strtoupper($business->name) }}</strong>
        </td>
    </tr>
    <tr>
        <td><strong>@lang('accounting.month'): {{ $month }} </strong></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><strong>@lang('accounting.year'): {{ $year }} </strong></td>        
        <td><strong>@lang('accounting.nit'): {{ $business->nit }} </strong></td>
        <td></td>
        <td></td>
        <td></td>
        <td><strong>@lang('accounting.nrc'): {{ $business->nrc }} </strong></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>



<table>
    <tr>
        <td rowspan="2">
            <strong>{{ mb_strtoupper(__('accounting.number')) }}</strong>
        </td>
        <td rowspan="2">
            <strong>{{ mb_strtoupper(__('accounting.issue_date')) }}</strong>
        </td>
        <td rowspan="2">
            <strong>{{ mb_strtoupper(__('accounting.document_number')) }}</strong>
        </td>
        <td rowspan="2">
            <strong>{{ mb_strtoupper(__('accounting.nrc')) }}</strong>
        </td>
        <td rowspan="2">
            <strong>{{ mb_strtoupper(__('accounting.excluded_dui')) }}</strong>
        </td>
        <td rowspan="2">
            <strong>{{ mb_strtoupper(__('accounting.supplier_name')) }}</strong>
        </td>
        <td colspan="2">
            <strong>{{ mb_strtoupper(__('accounting.exempt_purchases')) }}</strong>
        </td>
        <td colspan="3">
            <strong>{{ mb_strtoupper(__('accounting.taxed_purchases')) }}</strong>
        </td>
        <td rowspan="2">
            <strong>{{ mb_strtoupper(__('accounting.total_purchases')) }}</strong>
        </td>
        <td rowspan="2">
            <strong>{{ mb_strtoupper(__('accounting.recept')) }}</strong>
        </td>
        <td rowspan="2">
            <strong>{{ mb_strtoupper(__('accounting.purchases_excluded')) }}</strong>
        </td>
    </tr>       

    <tr>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.internal_locals')) }}</strong>
        </td>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.imported_or_internationals')) }}</strong>
        </td>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.internal_locals')) }}</strong>
        </td>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.imported_or_internationals')) }}</strong>
        </td>
        <td>
            <strong>{{ mb_strtoupper(__('accounting.fiscal_credit')) }}</strong>
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
        <td>{{-- {{ $item->exempt_amount }} --}}</td>
        <td></td>
        <td>{{ $item->total_before_tax }}</td>
        <td></td>
        <td>{{ $item->tax_amount }}</td>

        {{-- @php($total_final = (($item->exempt_amount + $item->total_before_tax + $item->tax_amount) - $item->exempt_amount)) --}}

        @php($total_final = $item->total_before_tax + $item->tax_amount)

        <td>{{ $item->final_total }}</td>



        <td>{{-- {{ $item->perc_ret_amount }} --}}</td>

        <td></td>

        {{-- @php($total_exempt_amount = $total_exempt_amount + $item->exempt_amount) --}}
        @php($total_total_before_tax = $total_total_before_tax + $item->total_before_tax)
        @php($total_tax_amount = $total_tax_amount + $item->tax_amount)
        {{-- @php($total_perc_ret_amount = $total_perc_ret_amount + $item->perc_ret_amount) --}}
        @php($total_total_final = $item->final_total + $total_total_final)
    </tr>
    @endforeach
    <tr>
        <td colspan="6"><strong>{{ mb_strtoupper(__('accounting.totals')) }}</strong></td>

        <td><strong>{{-- {{ $total_exempt_amount }} --}}</strong></td>


        <td></td>

        <td><strong>{{ $total_total_before_tax }}</strong></td>

        <td></td>


        <td><strong>{{ $total_tax_amount }}</strong></td>

        <td><strong>{{ $total_total_final }}</strong></td>
        <td><strong>{{-- {{ $total_perc_ret_amount }} --}}</strong></td>
        <td></td>
    </tr>


</table>
@endif

