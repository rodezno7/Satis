<div class="modal-dialog modal-lg" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('SellController@update', [$transaction->id]), 'method' => 'PUT', 'id' => 'transaction_edit_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('invoice.invoice_edit')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                {{-- Transaction date --}}
                <div class="@if (! empty($commission_agent)) col-md-4 col-sm-4 col-lg-4 col-xs-12 @else col-md-6 col-sm-6 col-lg-6 col-xs-12 @endif">
                    <div class="form-group">
                        {!! Form::label('transaction_date', __('invoice.transaction_date')) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text('transaction_date', $transaction->transaction_date, ['class' => 'form-control', 'id' => 'date']) !!}
                        </div>
                    </div>
                </div>

                @php
                    $dis = $transaction->cash_register_id == null ? '' : 'disabled';
                @endphp

                {{-- Document type --}}
                <div class="@if (! empty($commission_agent)) col-md-4 col-sm-4 col-lg-4 col-xs-12 @else col-md-6 col-sm-6 col-lg-6 col-xs-12 @endif">
                    <div class="form-group">
                        {!! Form::label('document', __('invoice.document_type')) !!}
                        {!! Form::select('document_type_id', $document_types, $transaction->document_types_id, ['class' => 'form-control select2', 'style' => 'width:100%;', 'placeholder' => __('seleccione'), config('app.business') != 'optics' ? 'disabled' : '']) !!}
                    </div>
                </div>

                {{-- Correlative --}}
                @if (! empty($commission_agent))
                <div class="col-md-4 col-sm-4 col-lg-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('correlative', __('invoice.correlative')) !!}
                        {!! Form::text('correlative', $transaction->correlative, ['class' => 'form-control', 'required', 'placeholder' => __('Numero de documento')]) !!}
                    </div>
                </div>
                @endif
            </div>

            @if (empty($commission_agent))
            <div class="row">
                <div class="@if ($transaction->parent_correlative) col-sm-4 col-xs-12 @else col-sm-6 col-xs-12 @endif">
                    <div class="form-group">
                        {!! Form::label('correlative', __('invoice.correlative')) !!}
                        {!! Form::text('correlative', $transaction->correlative, ['class' => 'form-control', 'required', 'placeholder' => __('Numero de documento')]) !!}
                    </div>
                </div>
                @if ($transaction->parent_correlative)
                <div class="col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('correlative', __('sale.parent_doc')) !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-file-text-o"></i>
                            </span>
                            {!! Form::select("return_parent_id", [$transaction->return_parent_id => "#".$transaction->parent_correlative. " - ". $parent_doc_date],
                                $transaction->return_parent_id, ["class" => "form-control", "id" => "return_parent_id",
                                    "placeholder" => __("sale.parent_doc")]) !!}
                            <span class="input-group-addon">
                                @show_tooltip(__('tooltip.parent_correlative_text'))
                            </span>
                        </div>
                    </div>
                    <input type="hidden" id="location_id" value="{{ $transaction->location_id }}">
                    <input type="hidden" id="customer_id" value="{{ $transaction->customer_id }}">
                    {!! Form::hidden("parent_correlative", null, ["id" => "parent_correlative"]) !!}
                </div>
                @endif
                <div class="@if ($transaction->parent_correlative) col-sm-4 col-xs-12 @else col-sm-6 col-xs-12 @endif">
                    <div class="form-group">
                        {!! Form::label('correlative', __('invoice.amount')) !!}
                        {!! Form::text('final_total', $transaction->final_total, ['class' => 'form-control', 'readonly', 'placeholder' => __('Monto')]) !!}
                    </div>
                </div>
            </div>
            @else
            <div class="row">
                {{-- Commission agent --}}
                <div class="col-md-8 col-sm-8 col-lg-8 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('commission_agent', __('lang_v1.commission_agent')) !!}
                        {!! Form::select('commission_agent', $commission_agent, $transaction->commission_agent,
                            ['class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.commission_agent')]) !!}
                    </div>
                </div>

                {{-- Final total --}}
                <div class="col-md-4 col-sm-4 col-lg-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('correlative', __('invoice.amount')) !!}
                        {!! Form::text('final_total', $transaction->final_total, ['class' => 'form-control', 'readonly', 'placeholder' => __('Monto')]) !!}
                    </div>
                </div>
            </div>
            @endif

            @if ($pos_settings['show_comment_field'] || $pos_settings['show_order_number_field'])
            <div class="row">
                {{-- Comment --}}
                @if ($pos_settings['show_comment_field'])
                <div class="col-sm-8">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-comment-o"></i>
                            </span>
                            {!! Form::text('staff_note', $transaction->staff_note,
                                ['class' => 'form-control', 'placeholder' => __('accounting.comment'), 'style' => 'width: 100%']); !!}
                        </div>
                    </div>
                </div>
                @endif

                {{-- No. Order --}}
                @if ($pos_settings['show_order_number_field'])
                <div class="col-sm-4">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-hashtag"></i>
                            </span>
                            {!! Form::text('additional_notes', $transaction->additional_notes,
                                ['class' => 'form-control', 'placeholder' => __('sale.no_order'), 'style' => 'width: 100%']); !!}
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            {{-- Payment lines --}}
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped" {{-- style="width: 70%; margin-left: 100px;" --}}>
                        <tr>
                            <th>@lang('messages.date')</th>
                            <th>@lang('purchase.ref_no')</th>
                            <th>@lang('purchase.amount')</th>
                            <th>@lang('purchase.payment_method')</th>
                            <th class="no-print">@lang('messages.actions')</th>
                        </tr>
                        @forelse ($payments as $payment)
                            <tr>
                                <td>{{ @format_date($payment->paid_on) }}
                                </td>
                                <td>{{ $payment->payment_ref_no }}</td>
                                <td><span class="display_currency"
                                        data-currency_symbol="true">{{ $payment->amount }}</span></td>
                                <td>{{ $payment_types[$payment->method] }}</td>
                                <td>
                                    @if((auth()->user()->can('purchase.payments') && (in_array($transaction->type,
                                    ['purchase', 'purchase_return']) )) || (auth()->user()->can('sell.payments') &&
                                    (in_array($transaction->type, ['sell', 'sell_return']))) ||
                                    auth()->user()->can('expense.access'))
                                    <li>
                                        <a aria-hidden="true" class="edit_invoice_payment" style="cursor:pointer; color: rgb(59, 59, 59);"
                                            data-href="{{ action('TransactionPaymentController@editPaymentMethod', [$payment->id]) }}">
                                            <i class="glyphicon glyphicon-edit"></i> {{ __('messages.edit') }}</a>
                                    </li>
                                    </ul>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="text-center">
                                <td colspan="6">@lang('purchase.no_records_found')</td>
                            </tr>
                        @endforelse
                    </table>
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" id="btn-edit-invoice" class="btn btn-primary">@lang('messages.save')</button>
            <button type="button" data-dismiss="modal" aria-label="Close" id="btn-close-modal-edit-payment_term"
                class="btn btn-default">@lang('messages.close')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>


@section('javascript')
    <script type="text/javascript" src="{{ asset('js/sweetalert2.js') }}"></script>
@endsection
<script>
    $('input#date').datetimepicker({
        format: moment_date_format,
        ignoreReadonly: true
    });

    $('input#ignore').datetimepicker({
        format: moment_date_format,
        ignoreReadonly: false
    });

    $(document).ready(function() {
        $('select.select2').select2();
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    });

    $(document).on('click', '.edit_invoice_payment', function(e) {
        e.preventDefault();
        var container = $('.edit_invoice_payment_modal');
        $.ajax({
            url: $(this).data("href"),
            dataType: "html",
            success: function(result) {
                container.html(result).modal('show');
                __currency_convert_recursively(container);
                $('#paid_on').datepicker({
                    autoclose: true,
                    toggleActive: false
                });
                container.find('form#transaction_payment_add_form').validate();
            }
        });
    });

</script>
