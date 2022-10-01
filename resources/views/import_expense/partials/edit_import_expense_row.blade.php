@php
    // Number of decimal places to store and use in calculations
    $price_precision = config('app.price_precision');
@endphp

{{-- Table --}}
<div class="table-responsive">
    <table class="table table-striped table-condensed table-th-gray table-text-center" id="import_expenses_table" width="100%">
        <thead>
            <tr>
                <th width="70%">
                    @lang('crm.name')
                </th>
                <th class="text-center" width="20%">
                    @lang('accounting.amount')
                </th>
                @if (! isset($apportionment) || ! $apportionment->is_finished)
                <th class="text-center" width="10%">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php $row_count = 0; @endphp

            @foreach ($import_expenses as $import_expense)
            <tr>
                {{-- Name --}}
                <td>
                    {{ $import_expense->name }}
                    {!! Form::hidden('import_expenses[' . $loop->index . '][import_expense_id]', $import_expense->import_expense_id) !!}
                    {!! Form::hidden('import_expenses[' . $loop->index . '][id]', $import_expense->id) !!}
                </td>
            
                {{-- Amount --}}
                <td>
                    {!! Form::text(
                        'import_expenses[' . $loop->index . '][import_expense_amount]', 
                        number_format($import_expense->amount, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator), 
                        ['class' => 'form-control input-sm import_expense_amount input_number mousetrap', $disabled ? 'readonly' : '']
                    ) !!}
                </td>
            
                {{-- Remove icon --}}
                @if (! isset($apportionment) || ! $apportionment->is_finished)
                <td class="text-center" @if ($disabled) style="pointer-events: none;" @endif>
                    <i class="fa fa-times remove_import_expense_row text-danger" title="{{ __('lang_v1.remove') }}" style="cursor: pointer;"></i>
                </td>
                @endif
            </tr>

            @php $row_count++; @endphp

            @endforeach
        </tbody>
        <tfoot>
            <tr class="active">
                <td class="text-center">
                    <strong>@lang('purchase.total')</strong>
                </th>
                <td class="text-center">
                    <strong><span id="spn_import_expense_total">$ 0.00</span></strong>
                    {!! Form::hidden('import_expense_total', 0, ['id' => 'import_expense_total']) !!}
                </td>
                @if (! isset($apportionment) ||  ! $apportionment->is_finished)
                <td></td>
                @endif
            </tr>
        </tfoot>
    </table>
</div>

<input type="hidden" id="row_count_ie" value="{{ $row_count }}">