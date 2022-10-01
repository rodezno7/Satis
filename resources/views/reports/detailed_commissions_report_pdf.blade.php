<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('report.detailed_commissions_report')</title>
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
                <strong>{{ mb_strtoupper($business->line_of_business) }}</strong>
            </td>
        </tr>
        <tr>
            <td class="td2 alncenter">
                <strong>{{ mb_strtoupper(__('report.detailed_commissions_report')) }}</strong>
            </td>
        </tr>
    </table>
    <br>

    <table class="table1" style="width: 100%;">
        <thead>
            <tr>
                <th class="td2 bb">{{ __('accounting.date') }}</th>
                <th class="td2 bb">{{ __('report.document_no') }}</th>
                <th class="td2 bb">{{ __('document_type.title') }}</th>
                <th class="td2 bb">{{ __('lang_v1.payment_condition') }}</th>
                @if (config('app.business') != 'optics')
                <th class="td2 bb">{{ __('customer.customer_code') }}</th>
                @endif
                <th class="td2 bb">{{ __('contact.customer') }}</th>
                <th>@lang('accounting.location')</th>
                <th class="td2 bb">{{ __('category.category') }}</th>
                <th class="td2 bb">{{ __('product.sub_category') }}</th>
                <th class="td2 bb">{{ __('product.brand') }}</th>
                <th class="td2 bb">SKU</th>
                <th class="td2 bb">{{ __('business.product') }}</th>
                <th class="td2 bb">{{ __('lang_v1.quantity') }}</th>
                <th class="td2 bb">{{ __('report.price_inc_tax') }}</th>
                <th class="td2 bb">{{ __('report.price_exc_tax') }}</th>
                <th class="td2 bb">{{ __('quote.seller') }}</th>
                @if (config('app.business') == 'optics')
                <th class="td2 bb">{{ __('graduation_card.optometrist') }}</th>
                @endif
                <th class="td2 bb">{{ __('report.unit_cost') }}</th>
                <th class="td2 bb">{{ __('report.total_cost') }}</th>
                @if (config('app.business') != 'optics')
                <th class="td2 bb">{{ __('customer.customer_portfolio') }}</th>
                <th class="td2 bb">{{ __('geography.state') }}</th>
                <th class="td2 bb">{{ __('geography.city') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($commissions as $item)
            <tr>
                <td class="alncenter td2">
                    {{ @format_date($item->transaction_date) }}
                </td>
                <td class="alncenter td2">
                    {{ $item->doc_no }}
                </td>
                <td class="alncenter td2">
                    {{ $item->doc_type }}
                </td>
                <td class="alncenter td2">
                    {{ $item->payment_condition }}
                </td>
                @if (config('app.business') != 'optics')
                <td class="alncenter td2">
                    {{ $item->customer_id }}
                </td>
                @endif
                <td class="td2">
                    {{ $item->customer_name }}
                </td>
                <td class="td2">
                    {{ $item->location }}
                </td>
                <td class="alncenter td2">
                    {{ $item->category }}
                </td>
                <td class="alncenter td2">
                    {{ $item->sub_category }}
                </td>
                <td class="alncenter td2">
                    {{ $item->brand_name }}
                </td>
                <td class="alncenter td2">
                    {{ $item->sku }}
                </td>
                <td class="td2">
                    {{ $item->product_name }}
                </td>
                <td class="alncenter td2">
                    {{ @num_format($item->quantity) }}
                </td>
                <td class="alncenter td2">
                    $ {{ @num_format($item->price_inc) }}
                </td>
                <td class="alncenter td2">
                    $ {{ @num_format($item->price_exc) }}
                </td>
                <td class="td2">
                    {{ $item->seller_name }}
                </td>
                @if (config('app.business') == 'optics')
                <td class="td2">
                    {{ $item->optometrist }}
                </td>
                @endif
                <td class="alncenter td2">
                    $ {{ @num_format($item->unit_cost) }}
                </td>
                <td class="alncenter td2">
                    $ {{ @num_format($item->total_cost) }}
                </td>
                @if (config('app.business') != 'optics')
                <td class="td2">
                    {{ $item->portfolio_name }}
                </td>
                <td class="alncenter td2">
                    {{ $item->state }}
                </td>
                <td class="alncenter td2">
                    {{ $item->city }}
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>