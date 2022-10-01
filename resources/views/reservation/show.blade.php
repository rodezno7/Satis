<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
  
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang('reservation.reservation_details')</h4>
        </div>

        <div class="modal-body">
            <div class="box-header">
                <h6 class="box-title" style="font-size: 12pt;">
                    <i class="fa fa-address-card-o margin-r-5" aria-hidden="true"></i>
                    @lang('reservation.customer_info')
                </h6>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="well well-sm">
                            <strong>
                                <i class="fa fa-calendar margin-r-5">
                                </i> @lang('accounting.date')
                            </strong>
                            <p class="text-muted">{{ @format_date($reservation->quote_date) }}</p>

                            <strong>
                                <i class="fa fa-hashtag margin-r-5">
                                </i> @lang('accounting.reference')
                            </strong>
                            <p class="text-muted">{{ $reservation->quote_ref_no }}</p>

                            {{-- @if ($reservation->document_name)
                            <strong>
                                <i class="fa fa-file-text-o margin-r-5">
                                </i> @lang('document_type.document')
                            </strong>
                            <p class="text-muted">{{ $reservation->document_name }}</p>
                            @endif --}}
                        </div>
                    </div>

                    <div class="col-sm-8">
                        <div class="well well-sm">
                            <strong>
                                <i class="fa fa-credit-card-alt margin-r-5">
                                </i> @lang('contact.customer')</strong>
                            <p class="text-muted">{{ $reservation->customer_name }}</p>

                            @if($reservation->seller_name)
                            <strong>
                                <i class="fa fa-shopping-cart margin-r-5">
                                </i> @lang('quote.seller_name')</strong>
                            <p class="text-muted">{{ $reservation->seller_name }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-condensed7 table-bordered text-center table-striped" id="reservation_table">
                    <thead style="background-color: #ccc;">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th>@lang('quote.product_name')</th>
                            <th style="width: 10%;">@lang('lang_v1.quantity')</th>
                            <th style="width: 15%;">@lang('sale.unit_price')</th>
                            <th style="width: 10%;">@lang('order.discount')</th>
                            <th style="width: 15%;">@lang('quote.affected_sales')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($quote_lines as $ql)
                        @php
                            $line_total_before_discount = 0;
                            $quantity = $ql->quantity ? $ql->quantity : 1;
                            $unit_price = 0;
                            if ($reservation->tax_detail) {
                                $line_total_before_discount = (double)($quantity * $ql->unit_price_exc_tax);
                                $unit_price = (double)($ql->unit_price_exc_tax);
                            } else{
                                $line_total_before_discount = (double)($quantity * $ql->unit_price_inc_tax);
                                $unit_price = (double)($ql->unit_price_inc_tax);
                            }
                            
                            $discount_calculated_line_amount = 0;
                            $discount_amount = round($ql->discount_amount, 4);
                            if ($ql->discount_type == "fixed"){
                                $discount_calculated_line_amount = $discount_amount * $quantity;
                            } else if ($ql->discount_type == "percentage") {
                                $discount_calculated_line_amount = ($unit_price * ($discount_amount / 100)) * $quantity;
                            }

                            $tax_amount = round(($ql->unit_price_exc_tax * $ql->tax_percent), 4);
                            $line_total = round(($line_total_before_discount - $discount_calculated_line_amount), 4);
                        @endphp
                        <tr>
                            <td>
                                <span id='row_no'>{{ $loop->iteration }}</span>
                                <input type='hidden' id='quote_line_id' value='{{ $ql->id ? $ql->id : 0 }}'>
                                <!-- Discount modal -->
                                <div class="modal fade" id="discount_line_modal_{{ $ql->variation_id}}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog modal-sm" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title">@lang('order.add_edit_discount')</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            {!! Form::label("discount_line_type", __("order.type")) !!}
                                                            {!! Form::select("discount_line_type", $discount_types, $ql->discount_type ? $ql->discount_type : "fixed",
                                                                ["class" => "form-control select2", "id" => "discount_line_type", "style" => "width: 100%;"]) !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            {!! Form::label("discount_line_amount", __("order.amount")) !!}
                                                            {!! Form::text("discount_line_amount", $ql->discount_amount ? number_format($ql->discount_amount, 2) : 0,
                                                                ["class" => "form-control input_number", "id" => "discount_line_amount"]) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: left;">
                                <span>{{ $ql->product_name }}</span>
                                <input type='hidden' id='variation_id' value='{{ $ql->variation_id }}'>
                                <input type='hidden' id='warehouse_id' value='{{ $ql->warehouse_id }}'>
                            </td>
                            <td>
                                <span id='unit_price_text' class='display_currency' data-currency_symbol='true'>
                                    {{ $ql->quantity ? @num_format($ql->quantity) : 1 }}
                                </span>
                                <input type='hidden' id='quantity' value='{{ $ql->quantity ? $ql->quantity : 1 }}'>
                            </td>
                            <td>
                                <span id='unit_price_text' class='display_currency' data-currency_symbol='true'>$
                                    {{ $reservation->tax_detail ? @num_format($ql->unit_price_exc_tax) : @num_format($ql->unit_price_inc_tax) }}
                                </span>
                                <input type='hidden' id='unit_price_exc_tax' value='{{ round($ql->unit_price_exc_tax, 4) }}'>
                                <input type='hidden' id='unit_price_inc_tax' value='{{ round($ql->unit_price_inc_tax, 4) }}'>
                                <input type='hidden' id='tax_percent' value='{{ $ql->tax_percent }}'>
                            </td>
                            <td>
                                <span id='discount_calculated_line_amount_text' class='display_currency' data-currency_symbol='true'>$
                                    {{ $discount_calculated_line_amount ? @num_format($discount_calculated_line_amount) : 0.00 }}
                                </span>
                                {!! Form::hidden("discount_calculated_line_amount", $discount_calculated_line_amount ? $discount_calculated_line_amount : 0,
                                                                ["id" => "discount_calculated_line_amount"]) !!}
                            </td>
                            <td>
                                <span id='line_total_text' class='display_currency' data-currency_symbol='true'>$ {{ @num_format($line_total) }}</span>
                                <input type='hidden' id='tax_line_amount' value='{{ $tax_amount ? $tax_amount : 0.00 }}'>
                                <input type='hidden' id='line_total' value='{{ $line_total }}'>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row">
                {{-- Subtotal  --}}
                <div class="col-md-3 col-md-offset-7 col-sm-3 col-sm-offset-6 col-xs-8 text-right">
                    <b>@lang('lang_v1.sub_total')</b>
                    <input type="hidden" name="subtotal" id="subtotal" value="{{ $reservation->total_before_tax }}">
                </div>
                <div class="col-md-2 col-sm-3 col-xs-4">
                    <span id="subtotal_text" class="display_currency" data-currency_symbol='true'>$ {{ @num_format($reservation->total_before_tax) }}</span>
                </div>
                {{-- Discounts --}}
                <div class="col-md-3 col-md-offset-7 col-sm-3 col-sm-offset-6 col-xs-8 text-right">
                    {!! Form::select("discount_type", $discount_types, $reservation->discount_type ? $reservation->discount_type : "fixed",
                            ["class" => "form-control hidden", "id" => "discount_type", "style" => "width: 100%;"]) !!}
                    {!! Form::text("discount_amount", $reservation->discount_amount, ["class" => "form-control input_number hidden",
                        "id" => "discount_amount", "placeholder" => __("purchase.discount_amount") ]) !!}
                    <b>@lang('purchase.discount')</b>: (-)
                    <input type="hidden" name="discount_calculated_amount" id="discount_calculated_amount">
                </div>
                <div class="col-md-2 col-sm-3 col-xs-4">
                    <span id="discount_calculated_amount_text" class="display_currency" data-currency_symbol='true'>$ {{ @num_format($reservation->discount_amount) }}</span>
                </div>
                {{-- Taxes --}}
                <div class="col-md-3 col-md-offset-7 col-sm-3 col-sm-offset-6 col-xs-8 text-right">
                    <b>@lang('tax_rate.tax_amount'): </b>(+)
                    <input type="hidden" name="tax_amount" id="tax_amount">
                </div>
                <div class="col-md-2 col-sm-3 col-xs-4">
                    <span id="tax_amount_text" data-currency_symbol="true" class="display_currency">$ {{ @num_format($reservation->tax_amount) }}</span>
                </div>
                {{-- Total final --}}
                <div class="col-md-3 col-md-offset-7 col-sm-3 col-sm-offset-6 col-xs-8 text-right">
                    <b>@lang('quote.total_final'):</b>    
                    <input type="hidden" name="total_final" id="total_final">
                </div>
                <div class="col-md-2 col-sm-3 col-xs-4">
                    <span id="total_final_text" class="display_currency" data-currency_symbol='true'>$ {{ @num_format($reservation->total_final) }}</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->