<table>

  <tr>
    <td colspan="6" style="text-align: center;">
      <strong>{{ mb_strtoupper($business_name) }}</strong>
    </td>
  </tr>

  <tr>
    <td colspan="6" style="text-align: center;">
      <strong>{{ mb_strtoupper($report_name) }}</strong>
    </td>
  </tr>
  <tr>
    <td colspan="6" style="text-align: center;">
      {{ $date_range }}
    </td>
  </tr>
  <tr>
    <td colspan="6" style="text-align: center;">
      @lang('accounting.accountant_report_values')
    </td>
  </tr>


  <tr>
    <td colspan="2" style="text-align: center; border-top: 0.25px solid black; border-bottom: 0.25px solid black;"><strong>{{ __('accounting.accounts') }}</strong></td>
    <td style="text-align: center; border-top: 0.25px solid black; border-bottom: 0.25px solid black;"><strong>{{ __('accounting.previous_balance') }}</strong></td>
    <td style="text-align: center; border-top: 0.25px solid black; border-bottom: 0.25px solid black;"><strong>{{ __('accounting.charges') }}</strong></td>
    <td style="text-align: center; border-top: 0.25px solid black; border-bottom: 0.25px solid black;"><strong>{{ __('accounting.payments') }}</strong></td>
    <td style="text-align: center; border-top: 0.25px solid black; border-bottom: 0.25px solid black;"><strong>{{ __('accounting.final_balance') }}</strong></td>
  </tr>

  @php
  $total_initial_debtor = 0;
  $total_debit_debtor = 0;
  $total_credit_debtor = 0;
  $total_final_debtor = 0;
  @endphp

  @foreach($accounts_debit as $item)
  @if (($item->clasification >= $account_from) && ($item->clasification <= $account_to))

  @php  
  $balance_initial = $item->debit_initial - $item->credit_initial;
  $balance_final = $item->debit_final - $item->credit_final;

  if($item->level == 1) {
    $total_initial_debtor = $total_initial_debtor + $balance_initial;
    $total_debit_debtor = $total_debit_debtor + $item->debit_range;
    $total_credit_debtor = $total_credit_debtor + $item->credit_range;
    $total_final_debtor = $total_final_debtor + $balance_final;
  }
  @endphp

  @if($enable_empty_values == 'active')

  <tr>
    @if($item->level <= 2)
    <td><strong>{{ $item->code }}</strong></td>
    <td><strong>{{ $item->name }}</strong></td>
    <td><strong>{{ $balance_initial }}</strong></td>
    <td><strong>{{ $item->debit_range }}</strong></td>
    <td><strong>{{ $item->credit_range }}</strong></td>
    <td><strong>{{ $balance_final }}</strong></td>
    @else
    <td>{{ $item->code }}</td>
    <td>{{ $item->name }}</td>
    <td>{{ $balance_initial }}</td>
    <td>{{ $item->debit_range }}</td>
    <td>{{ $item->credit_range }}</td>
    <td>{{ $balance_final }}</td>
    @endif
  </tr>


  @else

  @if(($balance_initial != 0.00) || ($item->debit_range != 0.00) || ($item->credit_range != 0.00) || ($balance_final != 0.00))
  
  <tr>
    @if($item->level <= 2)
    <td><strong>{{ $item->code }}</strong></td>
    <td><strong>{{ $item->name }}</strong></td>
    <td><strong>{{ $balance_initial }}</strong></td>
    <td><strong>{{ $item->debit_range }}</strong></td>
    <td><strong>{{ $item->credit_range }}</strong></td>
    <td><strong>{{ $balance_final }}</strong></td>
    @else
    <td>{{ $item->code }}</td>
    <td>{{ $item->name }}</td>
    <td>{{ $balance_initial }}</td>
    <td>{{ $item->debit_range }}</td>
    <td>{{ $item->credit_range }}</td>
    <td>{{ $balance_final }}</td>
    @endif
  </tr>
  @endif

  @endif
  
  


  @endif
  @endforeach


  @if((number_format($total_initial_debtor, 2) != 0.00) || (number_format($total_debit_debtor, 2) != 0.00) || (number_format($total_credit_debtor, 2) != 0.00) || (number_format($total_final_debtor, 2) != 0.00))
  <tr>
    <td colspan="5" style="text-align: right; border-top: 0.25px solid black; border-bottom: 0.250x solid black;"><strong>{{ mb_strtoupper(__('accounting.total_active_report')) }}</strong></td>    
    <td style="border-top: 0.25px solid black; border-bottom: 0.250x solid black;"><strong>{{ $total_final_debtor }}</strong></td>
  </tr>
  @endif

  <tr>
    <td colspan="6"></td>    
  </tr>

  <tr>
    <td colspan="6"></td>    
  </tr>

  @php
  $total_initial_creditor = 0;
  $total_debit_creditor = 0;
  $total_credit_creditor = 0;
  $total_final_creditor = 0;
  @endphp  
  @foreach($accounts_credit as $item)
  @if (($item->clasification >= $account_from) && ($item->clasification <= $account_to))
  @php  
  $balance_initial = $item->credit_initial - $item->debit_initial;
  $balance_final = $item->credit_final - $item->debit_final;  

  if($item->level == 1) {
    $total_initial_creditor = $total_initial_creditor + $balance_initial;
    $total_debit_creditor = $total_debit_creditor + $item->debit_range;
    $total_credit_creditor = $total_credit_creditor + $item->credit_range;
    $total_final_creditor = $total_final_creditor + $balance_final;
  }

  @endphp

  @if($enable_empty_values == 'active')

  <tr>
    @if($item->level <= 2)
    <td><strong>{{ $item->code }}</strong></td>
    <td><strong>{{ $item->name }}</strong></td>
    <td><strong>{{ $balance_initial }}</strong></td>
    <td><strong>{{ $item->debit_range }}</strong></td>
    <td><strong>{{ $item->credit_range }}</strong></td>
    <td><strong>{{ $balance_final }}</strong></td>
    @else
    <td>{{ $item->code }}</td>
    <td>{{ $item->name }}</td>
    <td>{{ $balance_initial }}</td>
    <td>{{ $item->debit_range }}</td>
    <td>{{ $item->credit_range }}</td>
    <td>{{ $balance_final }}</td>
    @endif
  </tr>

  @else

  @if(($balance_initial != 0.00) || ($item->debit_range != 0.00) || ($item->credit_range != 0.00) || ($balance_final != 0.00))  
  <tr>
    @if($item->level <= 2)
    <td><strong>{{ $item->code }}</strong></td>
    <td style="width: 40%"><strong>{{ $item->name }}</strong></td>
    <td><strong>{{ $balance_initial }}</strong></td>
    <td><strong>{{ $item->debit_range }}</strong></td>
    <td><strong>{{ $item->credit_range }}</strong></td>
    <td><strong>{{ $balance_final }}</strong></td>
    @else
    <td>{{ $item->code }}</td>
    <td style="width: 40%">{{ $item->name }}</td>
    <td>{{ $balance_initial }}</td>
    <td>{{ $item->debit_range }}</td>
    <td>{{ $item->credit_range }}</td>
    <td>{{ $balance_final }}</td>
    @endif
  </tr>
  @endif

  @endif

  


  @endif
  @endforeach

  @if((number_format($total_initial_creditor, 2) != 0.00) || (number_format($total_debit_creditor, 2) != 0.00) || (number_format($total_credit_creditor, 2) != 0.00) || (number_format($total_final_creditor, 2) != 0.00))
  <tr>
    <td colspan="5" style="text-align: right; border-top: 0.25px solid black; border-bottom: 0.250x solid black;"><strong>{{ mb_strtoupper(__('accounting.total_pasive_report')) }}</strong></td>
    <td style="border-top: 0.25px solid black; border-bottom: 0.250x solid black;"><strong>{{ $total_final_creditor }}</strong></td>
  </tr>
  @endif

</table>
