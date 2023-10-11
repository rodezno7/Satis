<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@lang('quote.quote')</title>
    @if($quote->tax_detail == 1)
    @php($message = $legend.' - '.__('quote.iva_included'))
    @else
    @php($message = $legend)
    @endif
    <style>
        body
        {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000000;
            font-size: 9pt;
            margin-top:    1.10cm;
            margin-bottom: 1.75cm;
            margin-left:   1.5cm;
            margin-right:  1.5cm;
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
        .td3
        {
            border: 1px;            
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

        {{-- <div id="watermark">
            <img src="{{ public_path("img/quote_background.jpg") }}" height="100%" width="100%" />
        </div> --}}


        <table class="table1" style="width: 100%;">
            <tr>
                <td style="width: 50%" class="td2">
                    <img src="{{ public_path("img/logo - RECIELSA.png") }}" height="85.2px" width="140px" />
                </td>
                <td style="width: 25%" class="td2">

                </td>
                <td style="width: 35%" class="td2">
                    <strong>
                        San Salvador, {{ $quote_date }}<br>
                    </strong>
                    {{ mb_strtoupper(__('quote.quote')) }} {{ $quote->quote_ref_no }}
                </td>
            </tr>
        </table>
        <table class="table1" style="width: 100%;">
            <tr>
                
                <td style="width: 70%;" class="td2">
                    <strong>
                        {{ $customer_name }}<br>
                        {{ $quote->address }}<br>
                        {{ $quote->mobile }}<br>
                    </strong>
                </td>
                <td style="width: 10%" class="td2">
                </td>
                <td style="width: 20%" class="alncenter td2">
                </td>
            </tr>
        </table>
        <br>
        <p>Estimado(a) {{ $customer_name }}</p>
        <p style="margin-left: 60px;">Sírvase encontrar la siguiente oferta de productos:</p>

        @if(count($lines) <= 3)
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
                            <strong>{{ mb_strtoupper('Total') }}</strong>
                        </td>
                    </tr>

                    @foreach($lines as $item)
                    <tr>
                        <td class="alnleft td3">
                            {{ $item->quantity }}
                            @php($quantity = $item->quantity)
                        </td>
                        <td class="alnleft td3">
                            @if($item->sku == $item->sub_sku)
                            {{ $item->name_product }}
                            @else
                            {{ $item->name_product }} {{ $item->name_variation }}
                            @endif
                            @if($item->warranty != null)
                            . <strong>@lang('quote.warranty')</strong>:{{ $item->warranty }}
                            @endif
                        </td>
                        <td class="alnright td3">
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

                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($unit_price_final) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($unit_price_final) }}
                            @endif
                        </td>
                        <td class="alnright td3">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($total_final) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($total_final) }}
                            @endif
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
                    <td style="width: 20%" class="alnright">
                        @if ($business->currency_symbol_placement == 'after')
                            {{ @num_format($quote->discount_amount) }} {{ $business->currency->symbol }}
                        @else
                            {{ $business->currency->symbol }} {{ @num_format($quote->discount_amount) }}
                        @endif
                    </td>
                </tr>                
                @if($quote->tax_detail == 1)
                    <tr>

                        <td style="width: 15%" class="alnleft">{{ mb_strtoupper(__('quote.subtotal')) }}</td>
                        <td style="width: 25%" class="alnright">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($quote->total_before_tax) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($quote->total_before_tax) }}
                            @endif
                        </td>

                    </tr>
                    <tr>
                        <td style="width: 15%" class="alnleft">{{ mb_strtoupper(__('quote.iva')) }}</td>
                        <td style="width: 25%" class="alnright">
                            @if ($business->currency_symbol_placement == 'after')
                                {{ @num_format($quote->tax_amount) }} {{ $business->currency->symbol }}
                            @else
                                {{ $business->currency->symbol }} {{ @num_format($quote->tax_amount) }}
                            @endif
                        </td>
                    </tr>
                @endif
                <tr>
                    <td style="width: 15%" class="alnleft">{{ mb_strtoupper(__('quote.total')) }}</td>
                    <td style="width: 25%" class="alnright">
                        @if ($business->currency_symbol_placement == 'after')
                            {{ @num_format($quote->total_final) }} {{ $business->currency->symbol }}
                        @else
                            {{ $business->currency->symbol }} {{ @num_format($quote->total_final) }}
                        @endif
                    </td>
                </tr>
            </table>
            <br>
            <p>
                <strong>{{ __('quote.conditions') }}:</strong> <br>
                {{ $quote->terms_conditions }}
            </p>
            <p>
                <strong>{{ __('quote.validity_report') }}:</strong> {{ $quote->validity }}
            </p>
            <p>
                <strong>{{ __('quote.delivery_time') }}:</strong> {{ $quote->delivery_time }}
            </p>
            <p>
                <strong>{{ __('quote.notes') }}:</strong> {{ $quote->note }}
            </p>

            <table class="table1" style="width: 100%;">
                <tr>
                    
                    <td style="width: 15%; text-align: center;" class="td2">
                        <br><br><br> ________________________________ <br>
                        <strong >{{ mb_strtoupper(__('quote.authorized')) }}</strong>
                    </td>
                    <td style="width: 10%" class="td2">
                    </td>
                    <td style="width: 20%" class="alncenter td2">
                    </td>
                </tr>
            </table>
        </main>
    </body>
    </html>