<table>
    <tr>
        <td colspan="8" style="text-align: center; font-size: 18px;">
            <h1 class="mrgB"><strong>{{ mb_strtoupper($business->line_of_business) }}</strong></h1>
        </td>
    </tr>
    <tr>
        <td colspan="8" style="text-align: center; font-size: 14px;">
            <h3 class="mrgB1"><strong>{{ mb_strtoupper(__('Historial de productos comprados por clientes')) }}</strong></h3>
        </td>
    </tr>
    <tr>
        <td colspan="8" style="text-align: center; font-size: 12px;">
            <h5 style="font-weight: bold;">
                DEL 
                {{ $initial_month == $final_month ? date('j', strtotime($initial_date)) .
                    ' AL '. date('j', strtotime($final_date)) . ' DE '. mb_strtoupper($initial_month) : 
                    date('j', strtotime($initial_date)) . ' DE '. mb_strtoupper($initial_month) . 
                    ' AL '. date('j', strtotime($final_date)) . ' DE ' . mb_strtoupper($final_month)}} DEL
                {{ $initial_year == $final_year ? $initial_year : $initial_year . ' - ' . $final_year }}
            </h5>
        </td>
    </tr>
</table>
<br>
<table>
    <thead style="font-size: 0.5em;">
        <tr>
            <th style="font-weight: bold; border: 0.25px solid black;">{{ mb_strtoupper(__('Fecha')) }}</th>
            <th style="font-weight: bold; border: 0.25px solid black;">{{ mb_strtoupper(__('Cliente')) }}
            </th>
            <th style="font-weight: bold; border: 0.25px solid black;">{{ mb_strtoupper(__('Documento')) }}</th>
            <th style="font-weight: bold; border: 0.25px solid black;">{{ mb_strtoupper(__('Producto')) }}
            </th>
            <th style="font-weight: bold; border: 0.25px solid black;">{{ mb_strtoupper(__('Cantidad')) }}</th>
            <th style="font-weight: bold; border: 0.25px solid black;">{{ mb_strtoupper(__('Precio')) }}</th>
            <th style="font-weight: bold; border: 0.25px solid black;">{{ mb_strtoupper(__('Total')) }}</th>
            <th style="font-weight: bold; border: 0.25px solid black;">{{ mb_strtoupper(__('Estado de pago')) }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($lines as $item)
            <tr>
                <td style="text-align: center; border: 0.25px solid black;">{{ $item->transaction_date }}</td>
                <td style="border: 0.25px solid black;">{{ $item->name_customer }}</td>
                <td style="border: 0.25px solid black;">{{ $item->document }}</td>
                <td style="border: 0.25px solid black;">{{ $item->product_name }}</td>
                <td style="border: 0.25px solid black;">{{ $item->quantity }}</td>
                <td style="border: 0.25px solid black;">{{ $item->unit_price }}</td>
                <td style="border: 0.25px solid black;">{{ $item->total }}</td>
                <td style="border: 0.25px solid black; text-align: right;">@lang('lang_v1.'.$item->status.'')</td>
            </tr>
        @endforeach
    </tbody>
</table>

