<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@lang('quote.quote')</title>
    @if($quote->tax_detail == 1)
    @php($message = $legend)
    @else
    @php($message = $legend.' - '.__('quote.iva_included'))
    @endif
    <style>
        body
        {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000000;
            font-size: 9pt;
            margin-top:    1.5cm;
            margin-bottom: 2cm;
            margin-left:   1cm;
            margin-right:  1cm;
        }
        h3, h4
        {
            text-align: center;
        }
        #watermark {
            position: fixed;
            bottom:   0px;
            left:     0px;

            width:    21.6cm;
            height:   27.9cm;

            z-index:  -1000;
        }
        .table1
        {
            border: 0px;
            border-collapse: collapse;
        }
        .table2
        {
            border-collapse: collapse;
            border: 0.25px solid black;
        }
        .td2
        {
            border: 0px;            
        }
        td
        {
            border: 0.25px solid black;
            padding: 4px;
            text-align: left;
        }
        th
        {
            border: 0.25px solid black;
            padding: 4px;
            text-align: center;
        }
        .alnright { text-align: right; }
        .alnleft { text-align: left; }
        .alncenter { text-align: center; }
        @page{

            margin: 1cm 1cm;

            margin-left: 0cm;
            margin-right: 0cm;

            
        }

        #header,
        #footer {
          position: fixed;
          left: 1cm;
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
          content: "{{ $message }}";
      }
  </style>   
</head>
<body>
    <main>

        <div id="footer">
            <div class="page-number"></div>
        </div>

        <div id="watermark">
            <img src="{{ public_path("img/quote_background.jpg") }}" height="100%" width="100%" />
        </div>


        <table class="table1" style="width: 100%;">
            <tr>

                <td style="width: 30%" class="td2">

                </td>

                <td style="width: 50%" class="td2">
                    <img src="{{ public_path("img/quote_logo.jpg") }}" height="115px" width="215px" />
                </td>

                <td style="width: 20%" class="alncenter">
                    <strong>
                        {{ mb_strtoupper(__('quote.quote')) }}<br>
                        {{ $quote->quote_ref_no }}

                    </strong>
                </td>
            </tr>
        </table>
        <br>
        

        <table class="table1" style="width: 100%;">
            <tr>
                <td style="width: 70%;" class="td2">
                </td>

                <td style="width: 10%" class="td2">

                </td>

                <td style="width: 20%" class="alncenter td2">
                    <strong>
                        {{ mb_strtoupper(__('quote.date')) }}: 
                        {{ $quote->quote_date }}
                    </strong>
                </td>
            </tr>

        </table>

        
        
        
        <br>


        <table class="table1" style="width: 100%;">
            <tr>
                <td style="width: 70%; background-color: #DCDCDC;" class="alnleft">

                    {{ mb_strtoupper(__('quote.name')) }}: 
                    {{ $quote->customer_name }}<br>

                    {{ mb_strtoupper(__('quote.address')) }}: 
                    {{ $quote->address }}<br>

                    {{ mb_strtoupper(__('quote.phone')) }}: 
                    {{ $quote->mobile }}<br>

                </td>

                <td style="width: 10%" class="td2">

                </td>

                <td style="width: 20%" class="alncenter td2">

                </td>
            </tr>

        </table>
        <br>
        
        
        <table class="table1" style="width: 100%;">
            <tr>
                <td style="width: 25%" class="alncenter">
                    {{ mb_strtoupper(__('quote.seller')) }}
                </td>

                <td style="width: 25%" class="alncenter">
                    {{ mb_strtoupper(__('quote.conditions')) }}
                </td>

                <td style="width: 25%" class="alncenter">
                    {{ mb_strtoupper(__('quote.validity_report')) }}
                </td>

                <td style="width: 25%" class="alncenter">
                    {{ mb_strtoupper(__('quote.delivery_time')) }}
                </td>
            </tr>

            <tr>
                <td style="width: 25%" class="alncenter">
                    {{ $quote->short_name }}
                </td>

                <td style="width: 25%" class="alncenter">
                    {{ $quote->terms_conditions }}
                </td>

                <td style="width: 25%" class="alncenter">
                    {{ $quote->validity }}
                </td>

                <td style="width: 25%" class="alncenter">
                    {{ $quote->delivery_time }}
                </td>
            </tr>
        </table>

        <br>

        @if(count($lines) >= 16)
        <div id="div_body">
            @else
            <div id="div_body" style="height: 388px;">
                @endif





                <table class="table1" style="width: 100%;">
                    <tr>
                        <td class="alncenter" style="width: 10%">
                            <strong>{{ mb_strtoupper(__('quote.quantity')) }}</strong>
                        </td>

                        <td class="alncenter" style="width: 66%">
                            <strong>{{ mb_strtoupper(__('quote.description')) }}</strong>
                        </td>

                        <td class="alncenter" style="width: 12%">
                            <strong>{{ mb_strtoupper(__('quote.unit_price')) }}</strong>
                        </td>

                        <td class="alncenter" style="width: 12%">
                            <strong>{{ mb_strtoupper(__('quote.affected')) }}</strong>
                        </td>
                    </tr>

                    @foreach($lines as $item)

                    <tr>
                        <td class="alnleft td2">
                            {{ $item->quantity }}
                            @php($quantity = $item->quantity)
                        </td>

                        <td class="alnleft td2">
                            @if($item->sku == $item->sub_sku)
                            {{ $item->name_product }}
                            @else
                            {{ $item->name_product }} {{ $item->name_variation }}
                            @endif
                            @if($item->warranty != null)
                            . <strong>@lang('quote.warranty')</strong>:{{ $item->warranty }}
                            @endif
                        </td>

                        <td class="alnright td2">
                            @if($quote->tax_detail == 1)
                            @php($unit_price = $item->unit_price_exc_tax)
                            @else
                            @php($unit_price = $item->unit_price_inc_tax)
                            @endif

                            @php($total = $unit_price * $quantity)
                            @if($item->discount_type == "fixed")
                            @php($discount = $item->discount_amount * $quantity)
                            @php($discount_single = $item->discount_amount)
                            @else
                            @php($discount = (($item->discount_amount / 100 ) * $unit_price) * $quantity)
                            @php($discount_single = (($item->discount_amount / 100 ) * $unit_price))
                            @endif
                            @php($total_final = $total - $discount )
                            @php($unit_price_final = $unit_price - $discount_single )
                            {{ number_format($unit_price_final, 4) }}


                        </td>

                        <td class="alnright td2">


                            {{ number_format($total_final, 2) }}
                        </td>
                    </tr>

                    @endforeach
                </table>
            </div>

            <table class="table1" style="width: 100%;">       

                <tr>
                    @if($quote->tax_detail == 1)
                    <td style="width: 60%" rowspan="4">{{ $value_letters }}</td>
                    @else
                    <td style="width: 60%" rowspan="2">{{ $value_letters }}</td>
                    @endif
                    @if($quote->discount_type == "fixed")
                    <td style="width: 20%" class="alnleft">{{ mb_strtoupper(__('quote.discount')) }}</td>
                    @else
                    <td style="width: 20%" class="alnleft">{{ mb_strtoupper(__('quote.discount_percent')) }}</td>
                    @endif
                    <td style="width: 20%" class="alnright">{{ number_format($quote->discount_amount, 2) }}</td>
                </tr>



                

                @if($quote->tax_detail == 1)
                <tr>

                    <td style="width: 20%" class="alnleft">{{ mb_strtoupper(__('quote.subtotal')) }}</td>
                    <td style="width: 20%" class="alnright">{{ number_format($quote->total_before_tax, 2) }}</td>

                </tr>

                <tr>
                    <td style="width: 20%" class="alnleft">{{ mb_strtoupper(__('quote.iva')) }}</td>
                    <td style="width: 20%" class="alnright">{{ number_format($quote->tax_amount, 2) }}</td>
                </tr>
                @endif

                <tr>
                    <td style="width: 20%" class="alnleft">{{ mb_strtoupper(__('quote.total')) }}</td>
                    <td style="width: 20%" class="alnright">{{ number_format($quote->total_final, 2) }}</td>
                </tr>

            </table>
            <br>


            <table class="table1" style="width: 100%;">       
                <tr>                

                    <td class="alnleft">
                        {{ mb_strtoupper(__('quote.notes')) }}
                    </td>

                </tr>

                <tr>
                    <td class="alnleft">
                        {{ $quote->note }}
                    </td>
                </tr>
            </table>
            <br>

            <strong>{{ mb_strtoupper(__('quote.authorized')) }}</strong> ________________________________


        </main>
    </body>
    </html>