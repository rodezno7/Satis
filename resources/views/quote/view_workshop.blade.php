<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('quote.quote')</title>
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
            border: 0.25px solid #767171;
        }

        .table1 thead tr th,
        .table1 thead tr td,
        .table1 tbody tr th,
        .table1 tbody tr td,
        .table1 tfoot tr th,
        .table1 tfoot tr td {
            border: 0px;
            font-size: 7pt;
            padding: 5px;
        }

        .table2 thead tr th,
        .table2 thead tr td,
        .table2 tbody tr th,
        .table2 tbody tr td,
        .table2 tfoot tr th,
        .table2 tfoot tr td {
            font-size: 7pt;
            padding: 5px;
        }

        td {
            border: 0.25px solid #767171;
            padding: 2px;
            text-align: left;
        }

        th {
            border: 0.25px solid #767171;
            padding: 2px;
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
            font-size: 6pt;
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

        .row {
            margin-right: 0px;
            margin-left: 0px;
        }

        .row:before,
        .row:after {
            display: table;
            content: " ";
        }

        .row:after {
            clear: both;
        }

        .col {
            position: relative;
            min-height: 1px;
            padding-right: 2px;
            padding-left: 2px;
        }

        .col {
            float: left;
        }

        .bt { border-top: 0.25px solid #767171; }
        .bb { border-bottom: 0.25px solid #767171; }
        .br { border-right: 0.25px solid #767171; }
        .bl { border-left: 0.25px solid #767171; }
        .no-bt { border-top: 0.25px solid white; }
        .no-bb { border-bottom: 0.25px solid white; }
        .no-br { border-right: 0.25px solid white; }
        .no-bl { border-left: 0.25px solid white; }
  </style>
</head>

<body>
    <div id="footer">
        <table class="table1" style="width: 100%;">
            <tbody>
                <tr>
                    <td style="width: 50%;">{{ $business->business_full_name }}</td>
                    <td style="text-align: right; width: 50%;" class="page-number"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="row alncenter" style="margin-bottom: 0;">
        <div class="col" style="width: 20%;">
            @if (! empty(Session::get('business.logo')) && file_exists(public_path('uploads/business_logos/' . Session::get('business.logo'))))
            <img src="{{ public_path('/uploads/business_logos/' . Session::get('business.logo')) }}" alt="Logo" style="margin: 0; width: 130px;">
            @else
                <img src="{{ public_path('img/logo.png') }}" alt="Logo" style="margin: 0; width: 130px; margin-top: -40px;">
            @endif
        </div>

        <div class="col" style="width: 60%;">
            <p style="font-size: 10pt; margin-top: 0; margin-bottom: 2px;">
                <strong>{{ mb_strtoupper($business->business_full_name) }}</strong>
            </p>

            @if (! empty($business->line_of_business))
                <p style="font-size: 6pt; margin-top: 0; margin-bottom: 2px;">
                    {{ mb_strtoupper($business->line_of_business) }}
                </p>
            @endif

            @if (! empty($business->nit) || ! empty($business->nrc))
                <p style="font-size: 6pt; margin-top: 0; margin-bottom: 2px;">
                    @if (! empty($business->nit))
                        NIT: {{ mb_strtoupper($business->nit) }}
                    @endif
                    @if (! empty($business->nrc))
                        &nbsp;&nbsp;&nbsp; NRC: {{ mb_strtoupper($business->nrc) }}
                    @endif
                </p>
            @endif

            <p style="font-size: 6pt; margin-top: 0; margin-bottom: 2px;">
                {{ mb_strtoupper($business->landmark) }}
                @if (! empty($business->city))
                    , {{ mb_strtoupper($business->city) }}
                @endif
                @if (! empty($business->state))
                    , {{ mb_strtoupper($business->state) }}
                @endif
                @if (! empty($business->country))
                    , {{ mb_strtoupper($business->country) }}
                @endif
            </p>

            @if (! empty($business->alternate_number) || ! empty($business->mobile))
                <p style="font-size: 6pt; margin-top: 0; margin-bottom: 2px;">
                    @if (! empty($business->alternate_number))
                        TEL: {{ mb_strtoupper($business->alternate_number) }}
                    @endif
                    @if (! empty($business->mobile))
                        &nbsp;&nbsp;&nbsp; {{ mb_strtoupper(__('business.mobile')) }}: {{ mb_strtoupper($business->mobile) }}
                    @endif
                </p>
            @endif

            @if (! empty($business->email))
                <p style="font-size: 6pt; margin-top: 0; margin-bottom: 2px;">
                    {{ mb_strtoupper(__('business.email')) }}: {{ $business->email }}
                </p>
            @endif
        </div>

        <div class="col" style="width: 20%;">
            &nbsp;
        </div>
    </div>

    <p style="text-align: center; font-size: 9pt; margin-top: 5px; margin-bottom: 5px;">
        <strong>{{ mb_strtoupper(__('quote.budget')) }}: {{ $quote->quote_ref_no }}</strong>
    </p>

    <p style="text-align: center; font-size: 8pt; margin-top: 15px; margin-bottom: 5px;">
        <strong>
            {{ mb_strtoupper('Identificación de cliente y vehículo') }}
        </strong>
    </p>

    <table class="table1" style="width: 100%;">
        <tr>
            <td>
                <strong>{{ mb_strtoupper(__('contact.customer')) }}: </strong>
                {{ $customer->business_name ?? $customer->name }}
            </td>
            <td>
                <strong>{{ mb_strtoupper(__('customer.license_plate_short')) }}: </strong>
                {{ $customer_vehicle->license_plate }}
            </td>
        </tr>

        <tr>
            <td>
                <strong>{{ mb_strtoupper(__('brand.brand')) }}: </strong>
                {{ $customer_vehicle->brand->name }}
            </td>
            <td>
                <strong>{{ mb_strtoupper(__('card_pos.model')) }}: </strong>
                {{ $customer_vehicle->model }}
            </td>
        </tr>

        <tr>
            <td>
                <strong>{{ mb_strtoupper(__('accounting.year')) }}: </strong>
                {{ $customer_vehicle->year }}
            </td>
            <td>
                <strong>{{ mb_strtoupper(__('customer.mi_km')) }}: </strong>
                {{ $customer_vehicle->mi_km }}
            </td>
        </tr>

        <tr>
            <td>
                <strong>{{ mb_strtoupper(__('customer.engine')) }}: </strong>
                {{ $customer_vehicle->engine_number }}
            </td>
            <td>
                <strong>{{ mb_strtoupper(__('customer.chassis')) }}: </strong>
                {{ $customer_vehicle->vin_chassis }}
            </td>
        </tr>
    </table>

    <p style="text-align: center; font-size: 8pt; margin-top: 15px; margin-bottom: 5px;">
        <strong>
            {{ mb_strtoupper('Trabajo a realizar') }}
        </strong>
    </p>

    <table class="table2" style="width: 100%;">
        <thead>
            <tr style="background-color: #d2d6de;">
                <th colspan="2">
                    {{ mb_strtoupper(__('accounting.description')) }}
                </th>
                <th style="width: 8%;">
                    {{ mb_strtoupper('Cant.') }}
                </th>
                <th class="alncenter" style="width: 8%;">
                    {{ mb_strtoupper(__('product.price')) }}
                </th>
                <th style="width: 8%;">
                    {{ mb_strtoupper(('Desc')) }}
                </th>
                <th style="width: 8%;">
                    {{ mb_strtoupper(__('accounting.subtotal')) }}
                </th>
                <th style="width: 8%;">
                    {{ mb_strtoupper(__('accounting.total')) }}
                </th>
            </tr>
        </thead>
        <tbody>
            @php
                $sums = 0;
            @endphp
            @foreach ($service_blocks as $service_block)
                @php
                    $total_service = 0;
                @endphp
                
                @foreach ($service_block['spare_rows'] as $product)
                    @php
                        $quantity = $product['quantity'];

                        if ($quote->tax_detail == 1) {
                            $unit_price = $product['unit_price_exc_tax'];

                        } else {
                            $unit_price = $product['unit_price_inc_tax'];
                        }

                        $total = $unit_price * $quantity;

                        if ($product['discount_type'] == 'fixed') {
                            $discount = $product['discount_amount'] * $quantity;
                            $discount_single = $product['discount_amount'];

                        } else {
                            $discount = (($product['discount_amount'] / 100 ) * $unit_price) * $quantity;
                            $discount_single = (($product['discount_amount'] / 100 ) * $unit_price);
                        }

                        $total_final = $total - $discount;

                        $unit_price_final = $unit_price - $discount_single;
                    @endphp

                    <tr>
                        {{-- Description --}}
                        @if ($service_block['variation_id'] != $product['variation_id'])
                        <td style="width: 10px; border-right: 0.25px solid #fff;">&nbsp;</td>
                        @endif
                        <td @if ($service_block['variation_id'] == $product['variation_id']) colspan="2" @endif>
                            {{ $product['name_product'] }}
                            @if ($product['type_product'] != 'single')
                                {{ $product['name_variation'] }}
                            @endif
                            @if ($service_block['variation_id'] == $product['variation_id'] && ! empty($service_block['note']))
                            <br>
                            {{ $service_block['note'] }}
                            @endif
                        </td>

                        {{-- Quantity --}}
                        <td class="alnright">
                            {{ @num_format($quantity) }}
                        </td>

                        {{-- Price --}}
                        <td class="alnright">
                            {{ @num_format($unit_price) }}
                        </td>

                        {{-- Discount --}}
                        <td class="alnright">
                            {{ @num_format($discount_single) }}
                        </td>

                        {{-- Subtotal --}}
                        <td class="alnright">
                            {{ @num_format($total_final) }}
                        </td>

                        {{-- Total --}}
                        <td class="alnright">&nbsp;</td>
                    </tr>

                    @php
                        $total_service += $total_final;
                    @endphp
                @endforeach

                <tr style="background-color: #f5f5f5;">
                    <td class="alnright" colspan="6">
                        <strong>{{ mb_strtoupper(__('accounting.total')) }}</strong>
                    </td>
                    <td class="alnright">
                        <strong>{{ @num_format($total_service) }}</strong>
                    </td>
                </tr>

                @php
                    $sums += $total_service;
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            @if ($quote->tax_detail == 1)
                {{-- Sum --}}
                <tr style="background-color: #d2d6de;">
                    <td class="alnright" colspan="6">
                        <strong>{{ mb_strtoupper(__('quote.sum')) }}</strong>
                    </td>
                    <td class="alnright">
                        <strong>
                            @if ($quote->discount_amount > 0)
                                {{ @num_format($sums) }}
                            @else
                                {{ @num_format($quote->total_before_tax) }}
                            @endif
                        </strong>
                    </td>
                </tr>

                @if ($quote->discount_amount > 0)
                    {{-- Discount --}}
                    <tr style="background-color: #d2d6de;">
                        <td class="alnright" colspan="6">
                            <strong>{{ mb_strtoupper(__('order.discount')) }}</strong>
                        </td>
                        <td class="alnright">
                            <strong>{{ @num_format($quote->discount_amount) }}</strong>
                        </td>
                    </tr>

                    {{-- Subtotal --}}
                    <tr style="background-color: #d2d6de;">
                        <td class="alnright" colspan="6">
                            <strong>{{ mb_strtoupper(__('quote.subtotal')) }}</strong>
                        </td>
                        <td class="alnright">
                            <strong>{{ @num_format($quote->total_before_tax) }}</strong>
                        </td>
                    </tr>
                @endif

                {{-- VAT --}}
                <tr style="background-color: #d2d6de;">
                    <td class="alnright" colspan="6">
                        <strong>{{ mb_strtoupper(__('purchase.vat')) }}</strong>
                    </td>
                    <td class="alnright">
                        <strong>{{ @num_format($quote->tax_amount) }}</strong>
                    </td>
                </tr>
            @else
                @if ($quote->discount_amount > 0)
                    {{-- Sum --}}
                    <tr style="background-color: #d2d6de;">
                        <td class="alnright" colspan="6">
                            <strong>{{ mb_strtoupper(__('quote.sum')) }}</strong>
                        </td>
                        <td class="alnright">
                            <strong>
                                {{ @num_format($sums) }}
                            </strong>
                        </td>
                    </tr>

                    {{-- Discount --}}
                    <tr style="background-color: #d2d6de;">
                        <td class="alnright" colspan="6">
                            <strong>{{ mb_strtoupper(__('order.discount')) }}</strong>
                        </td>
                        <td class="alnright">
                            <strong>{{ @num_format($quote->discount_amount) }}</strong>
                        </td>
                    </tr>
                @endif
            @endif

            <tr style="background-color: #d2d6de;">
                <td class="alnright" colspan="6">
                    <strong>{{ mb_strtoupper(__('accounting.total_general')) }}</strong>
                </td>
                <td class="alnright">
                    <strong>{{ @num_format($quote->total_final) }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>

    @if ($quote->note)
    <table class="table2" style="width: 100%; margin-top: 10px;">
        <tr>
            <td style="border-bottom: 0.25px solid #fff;">
                {{ mb_strtoupper(__('brand.note')) }}
            </td>
        </tr>
        <tr>
            <td>
                {{ $quote->note }}
            </td>
        </tr>
    </table>
    @endif

    @if ($quote->terms_conditions)
    <table class="table2" style="width: 100%; margin-top: 10px;">
        <tr>
            <td style="border-bottom: 0.25px solid #fff;">
                {{ mb_strtoupper(__('quote.terms_conditions')) }}
            </td>
        </tr>
        <tr>
            <td>
                {{ $quote->terms_conditions }}
            </td>
        </tr>
    </table>
    @endif

    <table class="table1" style="width: 100%; margin-top: 60px;">
        <tr>
            <td style="width: 50%;">&nbsp;</td>
            <td class="alnright" style="width: 50%;">
                F. ________________________________________
            </td>
        </tr>
    </table>
</body>
</html>