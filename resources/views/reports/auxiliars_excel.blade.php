<table>
    <tr>
        <td colspan="6" style="text-align: center;">
            <strong>{{ mb_strtoupper($business_name) }}</strong>
        </td>
    </tr>
    <tr>
        <td colspan="6" style="text-align: center;">
            <strong>{{ $report_name }}</strong>
        </td>
    </tr>

    <tr>
        <td colspan="6" style="text-align: center;">
            {{ $date_range }}
        </td>
    </tr>

    <tr>
        <td colspan="6" style="text-align: center;">
            {{ mb_strtoupper(__('accounting.accountant_report_values')) }}
        </td>
    </tr>
    <tr>
        <td style="text-align: center; border-top: 0.25px solid black; border-bottom: 0.25px solid black;"><strong> {{ __('accounting.entrie_number') }} </strong></td>
        <td style="text-align: center; border-top: 0.25px solid black; border-bottom: 0.25px solid black;"><strong> {{ __('accounting.date') }} </strong></td>
        <td style="text-align: center; border-top: 0.25px solid black; border-bottom: 0.25px solid black;"><strong> {{ __('accounting.description') }} </strong></td>
        <td style="text-align: center; border-top: 0.25px solid black; border-bottom: 0.25px solid black;"><strong> {{ __('accounting.debit') }} </strong></td>
        <td style="text-align: center; border-top: 0.25px solid black; border-bottom: 0.25px solid black;"><strong> {{ __('accounting.credit') }} </strong></td>
        <td style="text-align: center; border-top: 0.25px solid black; border-bottom: 0.25px solid black;"><strong> {{ __('accounting.balance') }} </strong></td>
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
        <td colspan="6"></td>
    </tr>

    
    <tr>
        <td colspan="3" style="border-top: 0.25px solid black;"><strong>{{ $account->code }}  {{ $account->name }}</strong></td>
        <td colspan="2" style="border-top: 0.25px solid black;"><strong>@lang('accounting.initial_balance'):</strong></td>
        <td style="border-top: 0.25px solid black;">{{ $balance }}</td>
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

    @if((number_format($account->debit_range, 2) != 0.00) || (number_format($account->credit_range, 2) != 0.00))


    <tr>
        <td>{{ $detail->short_name }}</td>
        <td>{{ date('d/m/y', strtotime($detail->date)) }}</td>
        <td>{{ $detail->description }}</td>
        <td>{{ $detail->debit }}</td>
        <td>{{ $detail->credit }}</td>
        <td>{{ $balance }}</td>
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
        <td colspan="6"><strong>@lang('accounting.out_moves')</strong></td>
    </tr>
    @endif

    <tr>
        <td colspan="3" style="text-align: right; border-bottom: 0.25px solid black;"><strong>{{ mb_strtoupper(__('accounting.total_account')) }}</strong></td>
        <td style="border-bottom: 0.25px solid black;"><strong>{{ $total_debit_single }}</strong></td>
        <td style="border-bottom: 0.25px solid black;"><strong>{{ $total_credit_single }}</strong></td>
        <td style="border-bottom: 0.25px solid black;"></td>
    </tr>

    @endif

    <tr>
        <td colspan="6"></td>
    </tr>

    @endif

    
    
    @endforeach

    <tr>
        <td colspan="3" style="text-align: right; border-bottom: 0.25px solid black;"><strong>{{ mb_strtoupper(__('accounting.total_general')) }}</strong></td>
        <td style="border-bottom: 0.25px solid black;"><strong>{{ $total_debit }}</strong></td>
        <td style="border-bottom: 0.25px solid black;"><strong>{{ $total_credit }}</strong></td>
        <td style="border-bottom: 0.25px solid black;"></td>
    </tr>

</table>
