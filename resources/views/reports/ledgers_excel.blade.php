<table>
    <tr>
        <td style="text-align: center;">
            <strong>{{ mb_strtoupper($business_name) }}</strong>
        </td>
    </tr>
    <tr>
        <td style="text-align: center;">
            <b>{{ mb_strtoupper($report_name) }}</b>
        </td>
    </tr>
    <tr>
        <td style="text-align: center;">
            <strong>{{ mb_strtoupper($date_range) }}</strong>
        </td>
    </tr>
    <tr>
        <td style="text-align: center;">
            <strong>{{ mb_strtoupper(__('accounting.accountant_report_values')) }}</strong>
        </td>
    </tr>
    <tr>
        <th style="text-align: center;"><b>{{ mb_strtoupper(__('accounting.date')) }}</b></th>
        <th style="text-align: center;"><b>{{ mb_strtoupper(__('accounting.concept')) }}</b></th>
        <th style="text-align: center;"><b>{{ mb_strtoupper(__('accounting.debit')) }}</b></th>
        <th style="text-align: center;"><b>{{ mb_strtoupper(__('accounting.credit')) }}</b></th>
        <th style="text-align: center;"><b>{{ mb_strtoupper(__('accounting.balance')) }}</b></th>
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
                    <td>
                        <b>{{ $account->code }}</b>
                    </td>
                    <td colspan="4">
                        <b>{{ $account->name }}</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: right; border-bottom: 1px solid black;">
                        {{ mb_strtoupper(__('accounting.previous_balance')) }}
                    </td>
                    <td style="border-bottom: 1px solid black;">
                        {{ $balance }}
                    </td>
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
                                <td>{{ mb_strtoupper(__('accounting.movements_day')) }}</td>
                                <td>{{ $detail->debit }}</td>
                                <td>{{ $detail->credit }}</td>
                                <td>{{ $balance }}</td>
                            </tr>
                        @endif
                    @endif
                @endforeach
                @if((number_format($account->debit_range, 2) == 0.00) && (number_format($account->credit_range, 2) == 0.00))
                    <tr>
                        <td>&nbsp;</td>
                        <td>{{ mb_strtoupper(__('accounting.out_moves')) }}</td>
                        <td colspan="3"></td>
                    </tr>
                @endif
                <tr>
                    <td colspan="2" style="border-top: 1px solid black; border-bottom: 1px solid black;">
                        {{ mb_strtoupper(__('accounting.totals')) }}
                    </td>
                    <td style="border-top: 1px solid black; border-bottom: 1px solid black;">
                        <strong>{{ $account->debit_range }}</strong></td>
                    <td style="border-top: 1px solid black; border-bottom: 1px solid black;">
                        <strong>{{ $account->credit_range }}</strong></td>
                    <td style="border-top: 1px solid black; border-bottom: 1px solid black;">
                        <strong>{{ $balance_final }}</strong></td>
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
        <td colspan="2">
            <b>{{ mb_strtoupper(__('accounting.total_general')) }}</b>
        </td>
        <td style="border-top: 1px solid black;">
            <strong>{{ $total_debit }}</strong></td>
        <td style="border-top: 1px solid black;">
            <strong>{{ $total_credit }}</strong></td>
        <td style="border-top: 1px solid black;">&nbsp;</td>
    </tr>
</table>