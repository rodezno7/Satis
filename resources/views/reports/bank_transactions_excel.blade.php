@if($type_transaction == 'outflow')

<table>
  <tr>
    <td colspan="6">
      <strong>{{ mb_strtoupper($business_name) }}</strong>
    </td>
  </tr>

  <tr>
    <td colspan="6">
      <strong>{{ mb_strtoupper(__('accounting.accountant_report_values')) }}</strong>
    </td>
  </tr>

  <tr>
    <td colspan="6">
      <strong>{{ mb_strtoupper($header1) }}</strong>
    </td>
  </tr>

  <tr>
    <td colspan="6">
      <strong>{{ mb_strtoupper($header2) }}</strong>
    </td>
  </tr>

  <tr>
    <td colspan="6">
    </td>
  </tr>

  <tr>
    <td colspan="6">
      <strong>{{ mb_strtoupper(__('accounting.outflow')) }}</strong>
    </td>
  </tr> 

  <tr>
    <th><strong>{{mb_strtoupper(__('accounting.date'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.bank_account'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.description'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.type'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.check_number'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.amount'))}}</strong></th>
  </tr>
  @php($total_credit = 0.00)
  @foreach($transactions_credit as $item)
  @php($total_credit = $total_credit + $item->amount)
  <tr>
    <td>{{ $item->date_transaction }}</td>
    <td>{{ $item->bank }} {{ $item->number_account }}</td>
    <td>{{ $item->description }}</td>
    <td>{{ $item->type_transaction }}</td>
    <td>{{ $item->check_number }}</td>
    <td>{{ $item->amount }}</td>
  </tr>
  @endforeach
  <tr>
    <td colspan="5"><strong>{{ mb_strtoupper(__('accounting.totals')) }}</strong></td>
    <td><strong>{{ $total_credit }}</strong></td>
  </tr>
</table>

@endif
@if($type_transaction == 'all')


<table>
  <tr>
    <td colspan="6">
      <strong>{{ mb_strtoupper($business_name) }}</strong>
    </td>
  </tr>

  <tr>
    <td colspan="6">
      <strong>{{ mb_strtoupper(__('accounting.accountant_report_values')) }}</strong>
    </td>
  </tr>

  <tr>
    <td colspan="6">
      <strong>{{ mb_strtoupper($header1) }}</strong>
    </td>
  </tr>

  <tr>
    <td colspan="6">
      <strong>{{ mb_strtoupper($header2) }}</strong>
    </td>
  </tr>

  <tr>
    <td colspan="6">
    </td>
  </tr>

  <tr>
    <td colspan="6">
      <strong>{{ mb_strtoupper(__('accounting.inflow')) }}</strong>
    </td>
  </tr> 

  <tr>
    <th><strong>{{mb_strtoupper(__('accounting.date'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.bank_account'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.description'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.type'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.reference'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.amount'))}}</strong></th>
  </tr>
  @php($total_debit = 0.00)
  @foreach($transactions_debit as $item)
  @php($total_debit = $total_debit + $item->amount)
  <tr>
    <td>{{ $item->date_transaction }}</td>
    <td>{{ $item->bank }} {{ $item->number_account }}</td>
    <td>{{ $item->description }}</td>
    <td>{{ $item->type_transaction }}</td>
    <td>{{ $item->reference }}</td>
    <td>{{ $item->amount }}</td>
  </tr>
  @endforeach
  <tr>
    <td colspan="5"><strong>{{ mb_strtoupper(__('accounting.totals')) }}</strong></td>
    <td><strong>{{ $total_debit }}</strong></td>
  </tr>


  <tr>
    <td colspan="6">
    </td>
  </tr>

  <tr>
    <td colspan="6">
      <strong>{{ mb_strtoupper(__('accounting.outflow')) }}</strong>
    </td>
  </tr> 

  <tr>
    <th><strong>{{mb_strtoupper(__('accounting.date'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.bank_account'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.description'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.type'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.reference'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.amount'))}}</strong></th>
  </tr>
  @php($total_credit = 0.00)
  @foreach($transactions_credit as $item)
  @php($total_credit = $total_credit + $item->amount)
  <tr>
    <td>{{ $item->date_transaction }}</td>
    <td>{{ $item->bank }} {{ $item->number_account }}</td>
    <td>{{ $item->description }}</td>
    <td>{{ $item->type_transaction }}</td>
    <td>{{ $item->reference }}</td>
    <td>{{ $item->amount }}</td>
  </tr>
  @endforeach
  <tr>
    <td colspan="5"><strong>{{ mb_strtoupper(__('accounting.totals')) }}</strong></td>
    <td><strong>{{ $total_credit }}</strong></td>
  </tr>
</table>

@endif



@if($type_transaction == 'inflow')


<table>
  <tr>
    <td colspan="6">
      <strong>{{ mb_strtoupper($business_name) }}</strong>
    </td>
  </tr>

  <tr>
    <td colspan="6">
      <strong>{{ mb_strtoupper(__('accounting.accountant_report_values')) }}</strong>
    </td>
  </tr>

  <tr>
    <td colspan="6">
      <strong>{{ mb_strtoupper($header1) }}</strong>
    </td>
  </tr>

  <tr>
    <td colspan="6">
      <strong>{{ mb_strtoupper($header2) }}</strong>
    </td>
  </tr>

  <tr>
    <td colspan="6">
    </td>
  </tr>

  <tr>
    <td colspan="6">
      <strong>{{ mb_strtoupper(__('accounting.inflow')) }}</strong>
    </td>
  </tr> 

  <tr>
    <th><strong>{{mb_strtoupper(__('accounting.date'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.bank_account'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.description'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.type'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.reference'))}}</strong></th>
    <th><strong>{{mb_strtoupper(__('accounting.amount'))}}</strong></th>
  </tr>
  @php($total_debit = 0.00)
  @foreach($transactions_debit as $item)
  @php($total_debit = $total_debit + $item->amount)
  <tr>
    <td>{{ $item->date_transaction }}</td>
    <td>{{ $item->bank }} {{ $item->number_account }}</td>
    <td>{{ $item->description }}</td>
    <td>{{ $item->type_transaction }}</td>
    <td>{{ $item->reference }}</td>
    <td>{{ $item->amount }}</td>
  </tr>
  @endforeach
  <tr>
    <td colspan="5"><strong>{{ mb_strtoupper(__('accounting.totals')) }}</strong></td>
    <td><strong>{{ $total_debit }}</strong></td>
  </tr>


 
</table>

@endif