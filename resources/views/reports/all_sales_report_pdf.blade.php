<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('report.all_sales_report')</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000000;
            font-size: {{ $size }}pt;
        }
        h3, h4 {
            text-align: center;
        }
        .table1 {
            border-collapse: collapse;
            border: 0px;
        }
        .table2 {
            border-collapse: collapse;
            border: 0.25px solid black;
        }
        .td2 {
            border: 0px;
        }
        td {
            border: 0.25px solid black;
            padding: 4px;
            text-align: left;
        }
        th {
            border: 0.25px solid black;
            padding: 4px;
            text-align: center;
        }
        .alnright { text-align: right; }
        .alnleft { text-align: left; }
        .alncenter { text-align: center; }
        @page{
            margin-bottom: 75px;
        }
        #header,
        #footer {
            position: fixed;
            left: 0;
            right: 0;
            color: #000000;
            font-size: 0.9em;
        }
        #header {
            top: 0;
            border-bottom: 0.1pt solid #aaa;
        }
        #footer {
            bottom: 0;
            border-top: 0.1pt solid #aaa;
        }
        .page-number:before {
            content: "Página " counter(page);
        }
        .bt { border-top: 0.25px solid black; }
        .bb { border-bottom: 0.25px solid black; }
        .br { border-right: 0.25px solid black; }
        .bl { border-left: 0.25px solid black; }
        .no-bt { border-top: 0.25px solid white; }
        .no-bb { border-bottom: 0.25px solid white; }
        .no-br { border-right: 0.25px solid white; }
        .no-bl { border-left: 0.25px solid white; }
  </style>   
</head>

<body onload="window.print()">
    {{-- <div id="footer">
        <div class="page-number"></div>
    </div> --}}
    <table class="table1" style="width: 100%;">
        <tr>
            <td class="td2 alncenter">
                <strong>{{ mb_strtoupper($business->business_full_name) }}</strong>
            </td>
        </tr>
        <tr>
            <td class="td2 alncenter">
                <strong>{{ mb_strtoupper(__('report.all_sales_report')) }}</strong>
            </td>
        </tr>
    </table>
    <br>

    <table class="table1" style="width: 100%;">
        <thead>
            <th class="td2 bb">@lang('messages.date')</th>
            <th class="td2 bb">@lang('sale.document_no')</th>
            <th class="td2 bb">@lang('document_type.title')</th>
            @if (config('app.business') != 'optics')
            <th class="td2 bb">@lang('customer.customer_code')</th>
            @endif
            <th class="td2 bb">@lang('contact.customer')</th>
            @if (config('app.business') == 'optics')
            <th class="td2 bb">@lang('sale.location')</th>
            <th class="td2 bb">@lang('sale.payment_status')</th>
            <th class="td2 bb">@lang('sale.total_invoice')</th>
            <th class="td2 bb">@lang('lang_v1.payment_note')</th>
            <th class="td2 bb">@lang('sale.total_paid')</th>
            <th class="td2 bb">@lang('sale.total_balance_due')</th>
            @else
            <th class="td2 bb">@lang('sale.payment_status')</th>
            <th class="td2 bb">@lang('lang_v1.payment_method')</th>
            <th class="td2 bb">@lang('sale.subtotal')</th>
            <th class="td2 bb">@lang('sale.discount')</th>
            <th class="td2 bb">@lang('tax_rate.taxes')</th>
            <th class="td2 bb">@lang('sale.total_amount')</th>
            @endif
        </thead>
        <tbody>
            @php
            $total_status = [];

            if (config('app.business') == 'optics') {
                $total_invoice = 0;
                $total_paid = 0;
                $total_due = 0;

            } else {
                $total_subtotal = 0;
                $total_discount = 0;
                $total_taxes = 0;
                $total_amount = 0;
            }
            @endphp
            @foreach ($sales as $item)
            @php
            if ($item->status == 'annulled') {
                $total_remaining = 0;
            } else {
                $total_remaining = $item->final_total - $item->total_paid;
            }
            @endphp
            <tr>
                <td class="alncenter td2">
                    {{ @format_date($item->transaction_date) }}
                </td>
                <td class="alncenter td2">
                    {{ $item->correlative }}
                </td>
                <td class="alncenter td2">
                    {{ $item->document_name }}
                </td>
                @if (config('app.business') != 'optics')
                <td class="alncenter td2">
                    {{ $item->customer_id }}
                </td>
                @endif
                <td class="td2">
                    {{ $item->customer_name }}
                    @if ($item->status == 'annulled')
                    - {{ __('lang_v1.annulled') }}
                    @endif
                </td>
                @if (config('app.business') == 'optics')
                <td class="td2">
                    {{ $item->location }}
                </td>
                <td class="alncenter td2">
                    @if ($item->status != 'annulled')
                    {{ __('lang_v1.' . $item->payment_status) }}
                    @endif
                </td>
                <td class="alncenter td2">
                    $ {{ @num_format($item->final_total) }}
                </td>
                <td class="alncenter td2">
                    {{ $item->note }}
                </td>
                <td class="alncenter td2">
                    $ {{ @num_format($item->total_paid) }}
                </td>
                <td class="alncenter td2">
                    $ {{ @num_format($total_remaining) }}
                </td>
                @else
                <td class="alncenter td2">
                    @if ($item->status != 'annulled')
                    {{ __('lang_v1.' . $item->payment_status) }}
                    @endif
                </td>
                @php
                $method = '';

                if ($item->status != 'annulled') {
                    if ($item->payment_condition == 'cash') {
                        if ($item->count_payments > 1) {
                            $method = __('lang_v1.checkout_multi_pay');
                        } else {
                            $method = ! empty($item->method) ? __('lang_v1.' . $item->method) : '';
                        }
                    } else {
                        if (! empty($item->payment_condition)) {
                            $method = ! empty($item->payment_condition) ? __('lang_v1.' . $item->payment_condition) : '';
                        } else {
                            $method = ! empty($item->method) ? __('lang_v1.' . $item->method) : '';
                        }
                    }
                }
                @endphp
                <td class="alncenter td2">
                    {{ $method }}
                </td>
                <td class="alncenter td2">
                    $ {{ @num_format($item->total_before_tax) }}
                </td>
                <td class="alncenter td2">
                    $ {{ @num_format($item->discount) }}
                </td>
                <td class="alncenter td2">
                    $ {{ @num_format($item->tax) }}
                </td>
                <td class="alncenter td2">
                    $ {{ @num_format($item->final_total) }}
                </td>
                @endif
            </tr>
            @php
            if (config('app.business') == 'optics') {
                $total_invoice += $item->final_total;
                $total_paid += $item->total_paid;
                $total_due += $total_remaining;

            } else {
                $total_subtotal += $item->total_before_tax;
                $total_discount += $item->discount;
                $total_taxes += $item->tax;
                $total_amount += $item->final_total;
    
                if (! empty($item->payment_status)) {
                    if (! array_key_exists(__('lang_v1.' . $item->payment_status), $total_status)) {
                        $total_status[__('lang_v1.' . $item->payment_status)] = 1;
                    } else {
                        $total_status[__('lang_v1.' . $item->payment_status)] += 1;
                    }
                }
            }
            @endphp
            @endforeach
        </tbody>
        <tr>
            <td colspan="5" class="alncenter td2 bt">
                <strong>{{ __('accounting.totals') }}</strong>
            </td>
            <td class="alncenter td2 bt">
                @foreach ($total_status as $status => $quantity)
                <small>{{ $status }} - {{ $quantity }}</small>
                <br>
                @endforeach
            </td>
            @if (config('app.business') == 'optics')
            <td class="alncenter td2 bt">
                <strong>$ {{ @num_format($total_invoice) }}</strong>
            </td>
            <td class="td2 bt"></td>
            <td class="alncenter td2 bt">
                <strong>$ {{ @num_format($total_paid) }}</strong>
            </td>
            <td class="alncenter td2 bt">
                <strong>$ {{ @num_format($total_due) }}</strong>
            </td>
            @else
            <td class="td2 bt"></td>
            <td class="alncenter td2 bt">
                <strong>$ {{ @num_format($total_subtotal) }}</strong>
            </td>
            <td class="alncenter td2 bt">
                <strong>$ {{ @num_format($total_discount) }}</strong>
            </td>
            <td class="alncenter td2 bt">
                <strong>$ {{ @num_format($total_taxes) }}</strong>
            </td>
            <td class="alncenter td2 bt">
                <strong>$ {{ @num_format($total_amount) }}</strong>
            </td>
            @endif
        </tr>
    </table>
</body>
</html>