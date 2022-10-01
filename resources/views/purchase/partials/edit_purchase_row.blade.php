@php
    // Number of decimal places to store and use in calculations
    $price_precision = config('app.price_precision');
@endphp

<div class="table-responsive">
    <table class="table table-striped table-condensed table-th-gray table-text-center" id="purchases_table" width="100%">
        <thead>
            <tr>
                <th width="20%">
                    @lang('accounting.reference')
                </th>
                <th width="50%">
                    @lang('purchase.supplier')
                </th>
                <th width="20%">
                    @lang('accounting.amount')
                </th>
                @if (! $apportionment->is_finished)
                <th class="text-center" width="10%">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php $row_count = 0; @endphp

            @foreach ($purchases as $purchase)
            <tr>
                {{-- Reference --}}
                <td>
                    {{ $purchase->ref_no }}

                    {!! Form::hidden('purchases[' . $loop->index . '][transaction_id]', $purchase->id, ['class' => 'purchase_id']) !!}
                    {!! Form::hidden('purchases[' . $loop->index . '][id]', $purchase->aht_ids) !!}
                </td>
            
                {{-- Supplier --}}
                <td>
                    {{ $purchase->name }}
                </td>
            
                {{-- Amount --}}
                <td>
                    <input
                        type="text"
                        class="form-control input-sm purchase_amount"
                        value="{{ number_format($purchase->total, $price_precision, $currency_details->decimal_separator, $currency_details->thousand_separator) }}"
                        readonly>
                </td>
            
                {{-- Remove icon --}}
                @if (! $apportionment->is_finished)
                <td class="text-center">
                    <i class="fa fa-times remove_purchase_row text-danger" title="{{ __('lang_v1.remove') }}" style="cursor: pointer;"></i>
                </td>
                @endif
            </tr>
            
            @php $row_count++; @endphp

            @endforeach
        </tbody>
        <tfoot>
            <tr class="active">
                <td class="text-center" colspan="2">
                    <strong>@lang('purchase.total')</strong>
                </th>
                <td class="text-center">
                    <strong><span id="spn_purchase_total">0.0000</span></strong>
                    {!! Form::hidden('purchase_total', 0, ['id' => 'purchase_total']) !!}
                </td>
                @if (! $apportionment->is_finished)
                <td></td>
                @endif
            </tr>
        </tfoot>
    </table>
</div>

<input type="hidden" id="row_count_p" value="{{ $row_count }}">