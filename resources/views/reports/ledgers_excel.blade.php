<table>
    <tr>
        <td>
            <center><strong>{{ mb_strtoupper($business_name) }}</strong></center>
        </td>
    </tr>
    <tr>
        <td>
            <strong>@lang('accounting.accountant_report_values')</strong>
        </td>
    </tr>
    <tr>
        <td>
            <strong>{{ mb_strtoupper($header) }} {{ mb_strtoupper($header3) }}</strong>
        </td>
    </tr>
    
    
    <tr>
        <th></th>
        <th></th>
        <th><strong>{{mb_strtoupper(__('accounting.debit'))}}</strong></th>
        <th><strong>{{mb_strtoupper(__('accounting.credit'))}}</strong></th>
        <th><strong>{{mb_strtoupper(__('accounting.balance'))}}</strong></th>
    </tr>

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

    

    @if((number_format($balance, 2) != 0.00) || (number_format($balance_final, 2) != 0.00) || (number_format($account->debit_range, 2) != 0.00) || (number_format($account->credit_range, 2) != 0.00))


    <tr>
        <td colspan="2"><strong>{{ $account->code }}  {{ $account->name }}</strong></td>
        <td colspan="2"><strong>@lang('accounting.previous_balance'):</strong></td>
        <td>{{ $balance }}</td>
    </tr>
    @foreach($lines as $detail)
    @if($account->code == $detail->code)
    @if($account->type == 'debtor')
    @php($balance = $balance + $detail->debit - $detail->credit)
    @else
    @php($balance = $balance - $detail->debit + $detail->credit)
    @endif

    @if((number_format($account->debit_range, 2) != 0.00) || (number_format($account->credit_range, 2) != 0.00))


    <tr>
        <td>{{ date('d/m/y', strtotime($detail->date)) }}</td>
        <td>{{ __('accounting.movements_day') }}</td>
        <td>{{ $detail->debit }}</td>
        <td>{{ $detail->credit }}</td>
        <td>{{ $balance }}</td>
    </tr>
    @endif



    @endif

    @endforeach
    @if((number_format($account->debit_range, 2) == 0.00) && (number_format($account->credit_range, 2) == 0.00))

    <tr>
        <td colspan="2"><strong>@lang('accounting.out_moves')</strong></td>
        <td colspan="2"></td>
        <td></td>
    </tr>



    @endif


    <tr>
        <td colspan="2"></td>
        <td><strong>{{ $account->debit_range }}</strong></td>
        <td><strong>{{ $account->credit_range }}</strong></td>
        <td><strong>{{ $balance_final }}</strong></td>
    </tr>

    <tr>
        <td colspan="5"></td>
    </tr>

    @php($total_debit = $total_debit + $account->debit_range)
    @php($total_credit = $total_credit + $account->credit_range)

    @endif





    @endif
    @endforeach
    <tr>
        <td colspan="2"></td>
        <td><strong>{{ $total_debit }}</strong></td>
        <td><strong>{{ $total_credit }}</strong></td>
        <td></td>
    </tr>
</table>