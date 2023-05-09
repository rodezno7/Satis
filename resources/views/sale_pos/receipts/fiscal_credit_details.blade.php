<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
</head>
<style>
    @page {
        size: letter portrait;
    }
    div#receipt-content {
        font-size: 10pt;
        font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
        margin: 1.5cm 1cm;
        padding: 0;
    }
    div#receipt-content h1 { text-align: center; font-size: 18pt; margin-top: 0; }
    div#receipt-content h2 { text-align: center; font-size: 16pt; margin-top: 0; }
    div#receipt-content table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 0.5cm; }
    div#receipt-content table thead th { border-bottom: 1px solid #000; text-align: center; }
    div#receipt-content table#head th,
    div#receipt-content table#head td,
    div#receipt-content table#foot th,
    div#receipt-content table#foot td { padding: 2px; }
    .cutter { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .underline { border-bottom: 1px solid #000; }
    .text-right { text-align: right; }
</style>
<body>
    <div id="receipt-content">
        <h1>{{ $business_name }}</h1>
        <h2>Detalle de Crédito Fiscal</h2>
        <table id="head">
            <tr>
                <th style="width: 2cm;">Número:</td>
                <td style="width: 8cm;">{{ $transaction->correlative }}</td>
                <th style="width: 2.5cm;">Fecha:</td>
                <td style="width: 3.5cm;">{{ @format_date($transaction->date) }}</td>
                <th style="width: 1.5cm">Hora:</td>
                <td>{{ @format_time($transaction->date) }}</td>
            </tr>
            <tr>
                <th>Cliente:</td>
                <td class="cutter">{{ $transaction->customer_name }}</td>
                <th>Vendedor:</td>
                <td colspan="3">{{ $transaction->seller_name }}</td>
            </tr>
        </table>
        <table id="body">
            <thead>
                <tr>
                    <th style="width: 12%;">Cantidad</th>
                    <th style="width: 15%;">Código</th>
                    <th>Descripción</th>
                    <th style="width: 15%;">P. Unitario</th>
                    <th style="width: 15%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction_sell_lines as $tsl)
                    <tr>
                        <td style="text-align: center;">
                            <span class="display_currency" data-currency_symbol="false" data-precision="0">
                                {{ $tsl->quantity }}
                            </span>
                        </td>
                        <td class="cutter">{{ $tsl->sku }}</td>
                        <td class="cutter">{{ $tsl->product }}</td>
                        <td class="text-right">
                            <span class="display_currency" data-currency_symbol="false" data-precision="4">
                                {{ $tsl->unit_exc_tax }}
                            </span>
                        </td>
                        <td class="text-right">
                            <span class="display_currency" data-currency_symbol="false" data-precision="2">
                                {{ $tsl->line_total_exc_tax }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <table id="foot">
            <tr>
                <th style="width: 4cm;">Facturado</th>
                <td style="width: 5cm;">__________________________</td>
                <td style="width: 4.5cm">&nbsp;</td>
                <th class="text-right" style="width: 3cm;">Suma</th>
                <td class="text-right">
                    <span class="display_currency" data-currency_symbol="true">
                        {{ $transaction->subtotal }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Revisado</th>
                <td>__________________________</td>
                <td>&nbsp;</td>
                <th class="text-right" style="width: 2cm;">IVA</th>
                <td class="text-right">
                    <span class="display_currency" data-currency_symbol="true">
                        {{ $transaction->tax_amount }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Encargado sucursal</th>
                <td>__________________________</td>
                <td>&nbsp;</td>
                <th class="text-right" style="width: 2cm;">Total</th>
                <td class="text-right">
                    <span class="display_currency" data-currency_symbol="true">
                        {{ $transaction->final_total }}
                    </span>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>