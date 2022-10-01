<table>
    <tr>
        <td><strong>PRODUCTO</strong></td>
        <td><strong>DESCRIPCIÓN</strong></td>
        <td><strong>FAMILIA</strong></td>
        <td><strong>SUBFAMILIA</strong></td>
        <td><strong>MARCA</strong></td>
        <td><strong>CANTIDAD</strong></td>
        <td><strong>PRECIO UNITARIO</strong></td>
        <td><strong>VENTA TOTAL</strong></td>
        <td><strong>COD. VENDEDOR</strong></td>
        <td><strong>NOMBRE VENDEDOR</strong></td>
        <td><strong>TIPO VENTA</strong></td>
        <td><strong>COSTO</strong></td>
        <td><strong>COSTO TOTAL</strong></td>
        <td><strong>UTILIDAD</strong></td>
    </tr>
    @foreach ($transactions as $t)
        <tr>
            <td>{{ $t->sku }}</td>
            <td>{{ $t->product_name }}</td>
            <td>{{ $t->category }}</td>
            <td>{{ $t->sub_category }}</td>
            <td>{{ $t->brand }}</td>
            <td>{{ $t->quantity }}</td>
            <td>{{ $t->unit_price }}</td>
            <td>{{ $t->total_sale }}</td>
            <td>{{ $t->employee_id }}</td>
            <td>{{ $t->employee_name }}</td>
            <td>{{ __("messages." . $t->payment_condition) }}</td>
            <td>{{ $t->cost }}</td>
            <td>{{ $t->total_cost }}</td>
            <td>{{ $t->utility }}</td>
        </tr>
    @endforeach
</table>