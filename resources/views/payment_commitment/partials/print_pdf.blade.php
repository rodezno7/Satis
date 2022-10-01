<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <style>
        @page{ margin: 1.5cm 1.5cm; }
        body{ font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif; font-size: 10pt; }
        h1{ margin: 0; text-align: center; font-size: 16pt; }
        h2{ text-align: center; font-size: 12pt; width: 50%; display: inline-block; vertical-align: top; }
        div.ref_no{ display: inline-block; margin-left: 1cm; width: 15%; }
        span.ref_no_text { font-size: 1.4em; font-weight: bold; }
        span.ref_no { font-size: 1.2em; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 0.2cm; }
        table.main td, table.main th { border: 1px solid #000; padding: 0.1cm 0; }
        table.main tfoot td { font-weight: bold; }
        table.signature td { padding: 0.2cm 0; }
        table.signature td.border-bottom { border-bottom: 1px solid #000; }
    </style>
</head>
<body>
    <h1>{{ $pc->business_name }}</h1>
    <div>
        <div style="width: 25%; height: 1cm; display: inline-block;"></div>
        <h2>{{ $pc->location_name }}</h2>
        <div class="ref_no">
            <span class="ref_no_text" style="float: right;">{{ __("contact.payment_commitment") }}</span><br>
            <span class="ref_no" style="float: right; clear: both; margin-top: 0.1cm;">N° {{ $pc->ref_no }}</span>
        </div>
    </div><br><br><br>
    <b>{{ __("lang_v1.date") }}:</b> {{ @format_date($pc->date) }}<br>
    <b>{{ __("contact.supplier") }}:</b> {{ $pc->supplier_name }}<br>
    <b>Quedan en nuestro poder los siguiente documentos para su revisión y pago</b><br>
    <table class="main">
        <thead>
            <tr>
                <th style="text-align: center;">@lang('document_type.document_type')</th>
                <th style="text-align: center;">@lang('lang_v1.reference')</th>
                <th style="text-align: center;">@lang('lang_v1.amount')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pcl as $line)
                <tr>
                    <td style="padding-left: 0.75cm;">{{ $line->document_name }}</td>
                    <td style="padding-left: 0.75cm;">{{ $line->reference }}</td>
                    <td style="text-align: right; padding-right: 0.75cm;">$ {{ number_format($line->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align: right; border-right: 0;">Total documentos </td>
                <td style="text-align: right; padding-right: 0.75cm; border-left: 0;">$ {{ number_format($pc->total, 2) }}</td>
            </tr>
        </tfoot>
    </table><br><br>
    <table class="signature">
        <tr>
            <td style="width: 15%;"><b>Recibido por</b>:</td>
            <td style="width: 32.5%;" class="border-bottom"></td>
            <td style="width: 5%;">&nbsp;</td>
            <td style="width: 15%;"><b>Proveedor</b>:</td>
            <td class="border-bottom"></td>
        </tr>
        <tr>
            <td><b>Firma</b>:</td>
            <td class="border-bottom"></td>
            <td></td>
            <td><b>Firma</b>:</td>
            <td class="border-bottom"></td>
        </tr>
    </table>
</body>
</html>