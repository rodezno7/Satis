@php
    // Number of decimal places to store and use in calculations
    $price_precision = config('app.price_precision');
@endphp

<tr>
    {{-- Name --}}
    <td>
        {{ $import_expense->name }}
        {!! Form::hidden('import_expenses[' . $row_count . '][import_expense_id]', $import_expense->id, ['class' => 'import_expense_id']) !!}
    </td>

    {{-- Amount --}}
    <td>
        {!! Form::text(
            'import_expenses[' . $row_count . '][import_expense_amount]', 
            number_format(0, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), 
            ['class' => 'form-control input-sm import_expense_amount input_number mousetrap']
        ) !!}
    </td>

    {{-- Remove icon --}}
    <td class="text-center">
        <i class="fa fa-times remove_import_expense_row text-danger" title="{{ __('lang_v1.remove') }}" style="cursor: pointer;"></i>
    </td>
</tr>

<input type="hidden" id="row_count_ie" value="{{ $row_count }}">
