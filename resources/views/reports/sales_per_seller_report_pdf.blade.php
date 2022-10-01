<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('report.sales_per_seller_report')</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000000;
            font-size: 7pt;
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
            border: 0px;
        }

        .table2 thead tr th,
        .table2 thead tr td,
        .table2 tbody tr th,
        .table2 tbody tr td,
        .table2 tfoot tr th,
        .table2 tfoot tr td {
            border: 0px;
            font-size: 6pt;
        }

        .td2 {
            border: 0px;
        }

        td {
            border: 0.25px solid #555;
            padding: 4px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            border: 0.25px solid #555;
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

        .bt { border-top: 1px solid black; }
        .bb { border-bottom: 1px solid black; }
        .br { border-right: 0.25px solid black; }
        .bl { border-left: 0.25px solid black; }
        .no-bt { border-top: 0.25px solid white; }
        .no-bb { border-bottom: 0.25px solid white; }
        .no-br { border-right: 0.25px solid white; }
        .no-bl { border-left: 0.25px solid white; }
        .dbb { border-bottom: 1px double black; }
  </style>   
</head>

<body>
    {{-- Footer --}}
    <div id="footer">
        <table class="table2" style="width: 100%;">
            <tbody>
                <tr>
                    <td style="width: 50%;">{{ $business->business_full_name }}</td>
                    <td style="text-align: right; width: 50%;" class="page-number"></td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Header --}}
    <table class="table1" style="width: 100%;">
        <tr>
            <td class="td2 alncenter" style="font-size: 10pt;">
                <strong>{{ mb_strtoupper($business->business_full_name) }}</strong>
            </td>
        </tr>
        <tr>
            <td class="td2 alncenter" style="font-size: 8pt;">
                <strong>{{ mb_strtoupper(__('report.sales_per_seller_report')) }}</strong>
            </td>
        </tr>
        <tr>
            <td class="td2 alncenter">
                {{ mb_strtoupper(__('accounting.from_date')) . ' ' . @format_date($start) . ' ' . mb_strtoupper(__('accounting.to_date')) . ' ' . @format_date($end) }}
            </td>
        </tr>
    </table>
    <br>

    {{-- Table --}}
    <table class="table1" style=" width: 100%;">
        @php
            $total_before_tax_total = 0;
            $discount_amount_total = 0;
            $tax_amount_total = 0;
            $final_total_total = 0;
            $flag = 0;
            $i = 1;
        @endphp

        @foreach ($records as $record)
            @if (count($record['sales']) > 0)
                <tr>
                    <td class="alnright"><strong>{{ $i }}</strong></td>
                    <td colspan="6"><strong>{{ $record['seller']->first_name . ' ' . $record['seller']->last_name }}</strong></td>
                </tr>

                <tr>
                    <td class="alncenter"><strong>@lang('messages.date')</strong></td>
                    <td style="width: 35%;"><strong>@lang('contact.customer')</strong></td>
                    <td class="alncenter"><strong>@lang('sale.document_no')</strong></td>
                    <td class="alncenter"><strong>@lang('sale.subtotal')</strong></td>
                    <td class="alncenter"><strong>@lang('sale.discount')</strong></td>
                    <td class="alncenter"><strong>@lang('tax_rate.taxes')</strong></td>
                    <td class="alncenter"><strong>@lang('sale.total_amount')</strong></td>
                </tr>

                @php
                    $total_before_tax_total_seller = 0;
                    $discount_amount_total_seller = 0;
                    $tax_amount_total_seller = 0;
                    $final_total_total_seller = 0;
                @endphp

                @foreach ($record['sales'] as $sale)
                    <tr>
                        <td class="alncenter">{{ @format_date($sale->transaction_date) }}</td>
                        <td>{{ $sale->customer_name }}</td>
                        <td class="alncenter">{{ $sale->correlative }}</td>
                        <td class="alnright">{{ @num_format($sale->total_before_tax) }}</td>
                        <td class="alnright">{{ @num_format($sale->discount_amount) }}</td>
                        <td class="alnright">{{ @num_format($sale->tax_amount) }}</td>
                        <td class="alnright">{{ @num_format($sale->final_total) }}</td>
                    </tr>

                    @php
                        $total_before_tax_total_seller += $sale->total_before_tax;
                        $discount_amount_total_seller += $sale->discount_amount;
                        $tax_amount_total_seller += $sale->tax_amount;
                        $final_total_total_seller += $sale->final_total;
                    @endphp
                @endforeach

                <tr>
                    <td class="alnright" colspan="3"><strong>@lang('report.total_per_seller')</strong></td>
                    <td class="alnright"><strong>{{ @num_format($total_before_tax_total_seller) }}</strong></td>
                    <td class="alnright"><strong>{{ @num_format($discount_amount_total_seller) }}</strong></td>
                    <td class="alnright"><strong>{{ @num_format($tax_amount_total_seller) }}</strong></td>
                    <td class="alnright"><strong>{{ @num_format($final_total_total_seller) }}</strong></td>
                </tr>

                @php
                    $total_before_tax_total += $total_before_tax_total_seller;
                    $discount_amount_total += $discount_amount_total_seller;
                    $tax_amount_total += $tax_amount_total_seller;
                    $final_total_total += $final_total_total_seller;
                    $flag = 1;
                    $i++;
                @endphp
            @endif
        @endforeach

        @if ($flag)
            <tr>
                <td class="alnright" colspan="3"><strong>@lang('accounting.total_general')</strong></td>
                <td class="alnright"><strong>{{ @num_format($total_before_tax_total) }}</strong></td>
                <td class="alnright"><strong>{{ @num_format($discount_amount_total) }}</strong></td>
                <td class="alnright"><strong>{{ @num_format($tax_amount_total) }}</strong></td>
                <td class="alnright"><strong>{{ @num_format($final_total_total) }}</strong></td>
            </tr>
        @else
            <p class="alncenter">@lang('report.no_data_available')</p>
        @endif
    </table>
</body>
</html>