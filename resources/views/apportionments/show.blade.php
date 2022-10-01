<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header no-print">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="modalTitle">
                @lang('apportionment.apportionment_details')
            </h4>
        </div>

        <div class="modal-body">
            {{-- Header --}}
            <div class="row">
                <div class="col-sm-3">
                    @if (! empty(Session::get('business.logo')))
                    <img src="{{ url( '/uploads/business_logos/' . Session::get('business.logo') ) }}" alt="Logo">
                    @endif
                </div>

                <div class="col-sm-9">
                    <h4><strong>{{ $apportionment->name }}</strong></h4>
                    <strong>{{ @format_date($apportionment->created_at) }}</strong>
                    <br>
                    <strong>DUCA: {{ $apportionment->reference }}</strong>
                </div>
            </div>

            {{-- Purchases --}}
            @php
            $total_invoice = 0;
            $total_import = 0;
            $total_dai = 0;
            $total_vat = 0;
            @endphp
            @foreach ($purchases as $purchase)
            <div class="row" style="font-size: 9.5px;">
                <div class="col-sm-12">
                    <br>
                    <strong>{{ $purchase->document_name }}:</strong> {{ $purchase->ref_no }}
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed table-th-gray table-text-center" width="100%">
                            <thead>
                                <tr>
                                    {{-- Code --}}
                                    <th width="9%">
                                        @lang('accounting.code')
                                    </th>
                                    {{-- Quantity --}}
                                    <th class="text-center" width="6%">
                                        @lang('lang_v1.quantity')
                                    </th>
                                    {{-- Description --}}
                                    <th width="25%">
                                        @lang('accounting.description')
                                    </th>
                                    {{-- Weight --}}
                                    <th class="text-center" width="6%">
                                        @lang('lang_v1.weight')
                                    </th>
                                    {{-- FOB --}}
                                    <th class="text-center" width="6%">
                                        FOB
                                    </th>
                                    {{-- Total --}}
                                    <th class="text-center" width="6%">
                                        @lang('accounting.total')
                                    </th>
                                    {{-- Import expenses --}}
                                    <th class="text-center" width="6%">
                                        @lang('import_expense.import_expenses')
                                    </th>
                                    {{-- Other expenses --}}
                                    <th class="text-center" width="6%">
                                        @lang('apportionment.other_expenses')
                                    </th>
                                    {{-- CIF --}}
                                    <th class="text-center" width="6%">
                                        CIF
                                    </th>
                                    {{-- DAI --}}
                                    <th class="text-center" width="6%">
                                        DAI
                                    </th>
                                    {{-- VAT --}}
                                    <th class="text-center" width="6%">
                                        @lang('purchase.vat')
                                    </th>
                                    {{-- Total cost --}}
                                    <th class="text-center" width="6%">
                                        @lang('report.total_cost')
                                    </th>
                                    {{-- Unit cost --}}
                                    <th class="text-center" width="6%">
                                        @lang('purchase.unit_cost')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                // Column totals
                                $fob_col = 0;
                                $total_col = 0;
                                $import_expenses_col = 0;
                                $other_expenses_col = 0;
                                $cif_col = 0;
                                $dai_col = 0;
                                $vat_col = 0;
                                $total_cost_col = 0;
                                @endphp
                                @foreach ($lines[$purchase->id] as $line)
                                    @php
                                    // FOB
                                    $purchase_price = is_null($line->initial_purchase_price) ? $line->purchase_price : $line->initial_purchase_price;

                                    // Total
                                    $total_fob = round($line->quantity * $purchase_price, 4);

                                    // Import expenses
                                    $value = $apportionment->distributing_base == 'weight' ? $line->weight_kg : $line->quantity * $purchase_price;
                                    $total = $apportionment->distributing_base == 'weight' ? $weight_totals : $cost_totals;
                                    $import_expense = round($value * $import_expenses_total / $total, 4);

                                    // Other expenses
                                    $other_expenses_total = round($purchases_p[$line->id]->total_import_expenses, 4);
                                    $value = $line->quantity * $purchase_price;
                                    $total = round($purchases_p[$line->id]->total_purchase, 4);
                                    $other_expenses = round($value * $other_expenses_total / $total, 4);

                                    // CIF
                                    $cif = round($total_fob + $import_expense + $other_expenses, 4);

                                    // DAI
                                    $dai_amount = $line->dai_amount;

                                    // VAT
                                    $value = $apportionment->distributing_base == 'weight' ? $line->weight_kg : $line->quantity * $purchase_price;
                                    $total = $apportionment->distributing_base == 'weight' ? $weight_totals : $cost_totals;
                                    $vat_amount = round($value * $apportionment->vat_amount / $total, 4);

                                    // Total cost
                                    $total_cost = round($cif + $dai_amount + $vat_amount, 4);

                                    // Unit cost
                                    $unit_cost = round($total_cost / $line->quantity, 4);
                                    @endphp
                                    <tr>
                                        {{-- Code --}}
                                        <td>
                                            {{ $line->variations->sub_sku }}
                                        </td>
                                        {{-- Quantity --}}
                                        <td class="text-right">
                                            {{ number_format($line->quantity, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                        </td>
                                        {{-- Description --}}
                                        <td>
                                            {{ $line->product->name }}
                                            {{ ! (empty($line->variations->name) || $line->variations->name == 'DUMMY') ? $line->variations->name : '' }}
                                        </td>
                                        {{-- Weight --}}
                                        <td class="text-right">
                                            {{ number_format($line->weight_kg, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                        </td>
                                        {{-- FOB --}}
                                        <td class="text-right">
                                            {{ number_format($purchase_price, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                        </td>
                                        {{-- Total --}}
                                        <td class="text-right">
                                            {{ number_format($total_fob, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                        </td>
                                        {{-- Import expenses --}}
                                        <td class="text-right">
                                            {{ number_format($import_expense, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                        </td>
                                        {{-- Other expenses --}}
                                        <td class="text-right">
                                            {{ number_format($other_expenses, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                        </td>
                                        {{-- CIF --}}
                                        <td class="text-right">
                                            {{ number_format($cif, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                        </td>
                                        {{-- DAI --}}
                                        <td class="text-right">
                                            {{ number_format($dai_amount, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                        </td>
                                        {{-- VAT --}}
                                        <td class="text-right">
                                            {{ number_format($vat_amount, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                        </td>
                                        {{-- Total cost --}}
                                        <td class="text-right">
                                            {{ number_format($total_cost, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                        </td>
                                        {{-- Unit cost --}}
                                        <td class="text-right">
                                            {{ number_format($unit_cost, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                        </td>
                                    </tr>
                                    @php
                                    $fob_col += $purchase_price;
                                    $total_col += $total_fob;
                                    $import_expenses_col += $import_expense;
                                    $other_expenses_col += $other_expenses;
                                    $cif_col += $cif;
                                    $dai_col += $dai_amount;
                                    $vat_col += $vat_amount;
                                    $total_cost_col += $total_cost;
                                    @endphp
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="font-weight: bold;">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">
                                        {{ number_format($fob_col, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                    </td>
                                    <td class="text-right">
                                        {{ number_format($total_col, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                    </td>
                                    <td class="text-right">
                                        {{ number_format($import_expenses_col, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                    </td>
                                    <td class="text-right">
                                        {{ number_format($other_expenses_col, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                    </td>
                                    <td class="text-right">
                                        {{ number_format($cif_col, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                    </td>
                                    <td class="text-right">
                                        {{ number_format($dai_col, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                    </td>
                                    <td class="text-right">
                                        {{ number_format($vat_col, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                    </td>
                                    <td class="text-right">
                                        {{ number_format($total_cost_col, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            @php
            $total_invoice += $total_col;
            $total_import += $total_cost_col;
            $total_dai += $dai_col;
            $total_vat += $vat_col;
            @endphp
            @endforeach

            {{-- Totals --}}
            <div class="row" style="font-size: 9.5px;">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed table-th-gray table-text-center" width="100%">
                            <thead>
                                <tr>
                                    <th width="9%"></th>
                                    <th width="6%"></th>
                                    <th width="25%"></th>
                                    <th width="6%"></th>
                                    <th class="text-center" width="6%">
                                        {{ __('accounting.totals') }}
                                    </th>
                                    <th class="text-right" width="6%">
                                        {{ number_format($total_invoice, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                    </th>
                                    <th width="6%"></th>
                                    <th width="6%"></th>
                                    <th width="6%"></th>
                                    <th width="6%"></th>
                                    <th width="6%"></th>
                                    <th class="text-right" width="6%">
                                        {{ number_format($total_import, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                    </th>
                                    <th width="6%"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Import expensess --}}
            <div class="row" style="font-size: 9.5px;">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed table-th-gray table-text-center" width="100%">
                            <tbody>
                                {{-- Import expenses of apportionments --}}
                                @php
                                $total_import_expenses = 0;
                                @endphp
                                @foreach ($import_expenses as $import_expense)
                                <tr>
                                    <td width="9%"></td>
                                    <td width="6%"></td>
                                    <td width="37%">
                                        {{ $import_expense->name }}
                                    </td>
                                    <td class="text-right" width="6%">
                                        {{ number_format($import_expense->amount, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                    </td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                </tr>
                                @php
                                $total_import_expenses += $import_expense->amount;
                                @endphp
                                @endforeach

                                {{-- Total expenses --}}
                                <tr>
                                    <td width="9%"></td>
                                    <td width="6%"></td>
                                    <td width="37%">
                                        <strong>
                                            {{ __('apportionment.total_expenses') }}
                                        </strong>
                                    </td>
                                    <td class="text-right" width="6%">
                                        <strong>
                                            {{ number_format($total_import_expenses, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                        </strong>
                                    </td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                </tr>

                                {{-- Sub total --}}
                                <tr>
                                    <td width="9%"></td>
                                    <td width="6%"></td>
                                    <td width="37%">
                                        <strong>
                                            {{ __('lang_v1.sub_total') }}
                                        </strong>
                                    </td>
                                    <td class="text-right" width="6%">
                                        <strong>
                                            {{ number_format($total_import_expenses + $total_invoice, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                        </strong>
                                    </td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                </tr>

                                {{-- Import expenses of purchases --}}
                                @php
                                $total_iep = 0;
                                @endphp
                                @foreach ($import_expenses_purchases as $import_expense)
                                <tr>
                                    <td width="9%"></td>
                                    <td width="6%"></td>
                                    <td width="37%">
                                        {{ $import_expense->name }}
                                    </td>
                                    <td class="text-right" width="6%">
                                        {{ number_format($import_expense->amount, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                    </td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                </tr>
                                @php
                                $total_iep += $import_expense->amount;
                                @endphp
                                @endforeach

                                {{-- DAI --}}
                                @if ($total_dai > 0)
                                <tr>
                                    <td width="9%"></td>
                                    <td width="6%"></td>
                                    <td width="37%">DAI</td>
                                    <td class="text-right" width="6%">
                                        {{ number_format($total_dai, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                    </td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                </tr>
                                @endif

                                {{-- IVA --}}
                                @if ($total_vat > 0)
                                <tr>
                                    <td width="9%"></td>
                                    <td width="6%"></td>
                                    <td width="37%">
                                        @lang('purchase.vat')
                                    </td>
                                    <td class="text-right" width="6%">
                                        {{ number_format($total_vat, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                    </td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                </tr>
                                @endif

                                {{-- Total --}}
                                <tr>
                                    <td width="9%"></td>
                                    <td width="6%"></td>
                                    <td width="37%">
                                        <strong>
                                            @lang('accounting.total')
                                        </strong>
                                    </td>
                                    <td class="text-right" width="6%">
                                        <strong>
                                            {{ number_format($total_import_expenses + $total_invoice + $total_iep + $total_dai + $total_vat, 4, $currency_details->decimal_separator, $currency_details->thousand_separator) }}
                                        </strong>
                                    </td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                    <td width="6%"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Buttons --}}
        <div class="modal-footer no-print">
            <button type="button"
                class="btn btn-primary"
                aria-label="Print"
                onclick="$(this).closest('div.modal-content').printThis();">
                <i class="fa fa-print"></i> @lang('messages.print')
            </button>

            <button type="button"
                class="btn btn-default"
                data-dismiss="modal">
                @lang('messages.close')
            </button>
        </div>
    </div>
</div>
  
<script type="text/javascript">
    $(document).ready(function() {
        var element = $('div.modal-xl');
        __currency_convert_recursively(element);
    });
</script>