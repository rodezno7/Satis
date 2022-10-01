<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@lang('Notas de remisión')</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #000000; font-size: 0.9em;}
        h3, h4 { text-align: center;  }
        .table1 { brder: 0px;}
        /* td{border: 1px solid #000;} */
        .td2 { border: 0px; text-align: center;}
        td, th { padding-right: 4px; text-align: left; }
        .alnleft {text-align: left;}
        .alnrigth {  text-align: left;  }
        .alncenter { text-align: center; }
        @page { margin-bottom: 30px; margin-top: 30px;  }

        .locations {
            text-align: right;
            font-size: 0.9em;
        }

        .tran_date {
            text-align: center;
            font-size: 0.9em;
        }

        .mrgB {
            margin-bottom: -1.5%;
            font-size: 18px;
        }

        .mrgB1 {
            margin-bottom: -1.5%;
            font-size: 14px;
        }

        .mrgB2 {
            margin-bottom: -1.5%;
            font-size: 11px;
        }

        .table3 {
            width: 70%;
            border: none;
            border-collapse: collapse;
            font-size: 0.9em;
        }
        .premrg{
            margin-top: -4%;
            margin-left: 20%;
            padding-top: 20px;
        }
        .premrg2{
            margin-top: -8%;
            margin-left: 20%;
        }
        .pdd_b{  padding-bottom: 10px; padding-top: 5px;}
        #container table{ width: 100%; }
        #container{width: 16.4cm; height: 21.5cm; }
        #head table {table-layout: fixed;}
        #head table td pre{ white-space: nowrap; overflow: hidden; text-overflow: ellipsis;}
        #head table td{ line-height: 0.3cm;}
    </style>
</head>

<body>
<div id="container">
    <div id="headerstop" style="height: 2.85cm; width: 100%;"></div>
    <div id="head" style="height: 3.1cm; margin-left: -0.5cm;">
        <table style="width: 14.6cm; font-size: 0.8em; word-spacing: -4px; padding-left: 0.4cm;">
            <tr>
                <td>
                    <pre style="width: 7.20cm; padding-left: 1.1cm;">{{ $business_name->name }}   -   {{ $trans_ware->name }}</pre>
                    <pre style="padding-left: 1.3cm;">{{ $trans_ware->landmark }}</pre>
                    <pre style="padding-left: 1.9cm;">{{ $trans_ware->state }}</pre>
                </td>
                <td>
                    <pre style="width: 7.3cm; padding-left: 1cm;">{{ @format_date($trans_ware->transaction_date) }}</pre>
                    <pre style="padding-left: 1.4cm;">{{ $trans_ware->city }}</pre>
                    <pre>&nbsp;</pre>
                </td>
            </tr>
        </table>
    </div>
    <div id="body" style=" margin-top: 0.4cm; width: 14.5cm; height: 10.5cm; border-collapse: collapse; margin-left: -0.8cm;">
        <table style="border-collapse: collapse;">
            <thead style="font-size: 0.8em;">
                <tr>
                    <th class="alncenter" style="width: 1.9cm;">&nbsp;</th>
                    <th class="alncenter" style="width: 9cm;">&nbsp;</th>
                    <th class="alncenter" style="width: 1.8cm;">&nbsp;</th>
                    <th class="alncenter" style="2cm">&nbsp;</th>
                </tr>
            </thead>
            <tbody style="font-size: 0.75em;">
                @foreach ($transfer as $item)
                <tr>
                    <td class="alncenter">{{ @num_format($item->quantity) }}</td>
                    <td class="alncenter">
                        {{ $item->sub_sku . " - " . $item->product }}
                    </td>
                    <td class="alncenter">$ {{ @num_format($item->unit_price) }}</td>
                    <td class="alncenter">$ {{ $item->quantity * $item->unit_price }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div id="foot" style="height: 2.4cm; font-size: 0.9em; margin-left: -1.2cm;">
        <table style="width: 14.5cm; border-collapse: collapse;">
            <tr>
                <td style="width: 1.9cm">&nbsp;</td>
                <td style="width: 9cm;">
                    <pre>{{ $total_letters }}</pre>
                </td>
                <td style="width: 1.8cm;">&nbsp;</td>
                <td style="width: 2cm;">
                    <pre><b>$ {{ $total }}</b></pre>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>

</html>
