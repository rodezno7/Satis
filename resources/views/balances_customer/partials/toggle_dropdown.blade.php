@if ($status != "annulled")
    @if (auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.access"))
        <li>
            <a href="#" data-href="{{ action('SellController@show', [$id]) }}" class="btn-modal" data-container=".view_modal">
                <i class="fa fa-external-link" aria-hidden="true"></i> @lang("messages.view")
            </a>
        </li>
    @endif
    @if (auth()->user()->can("sell.annul"))
        @if ($status == 'final')
            @php
            $transaction_date = date('Y-m-d', strtotime($transaction_date));
            $now = date('Y-m-d', strtotime(now()));
            $now = \Carbon\Carbon::parse($now);
            $transaction_date = \Carbon\Carbon::parse($transaction_date);
            @endphp
            {{-- It is validated that the transaction date is the same as now --}}
            @if ($transaction_date->eq($now))
                <li>
                    <a href="{{ action('SellPosController@annul', [$id]) }}" class="annul-sale">
                        <i class="fa fa-ban"></i> @lang("messages.annul")
                    </a>
                </li>
            @elseif ($business->annull_sale_expiry)
                <li>
                    <a href="{{ action('SellPosController@annul', [$id]) }}" class="annul-sale">
                        <i class="fa fa-ban"></i> @lang("messages.annul")
                    </a>
                </li>
            @endif
        @endif
    @endif
    @if (auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.access"))
        <li>
            <a href="#" class="print-invoice" data-href="{{ route('sell.printInvoice', [$id]) }}">
                <i class="fa fa-print" aria-hidden="true"></i> @lang("messages.print")
            </a>
        </li>
    @endif
    <li class="divider"></li> 
    @if ($payment_status != 'paid')
        @if (auth()->user()->can("sell.create") || auth()->user()->can("direct_sell.access"))
            <li>
                <a href="{{ action('TransactionPaymentController@addPayment', [$id]) }}" class="add_payment_modal">
                    <i class="fa fa-money"></i> @lang("purchase.add_payment")
                </a>
            </li>
        @endif
    @endif
    <li>
        <a href="{{ action('TransactionPaymentController@show', [$id]) }}" class="view_payment_modal">
            <i class="fa fa-money"></i> @lang("purchase.view_payments")
        </a>
    </li>
    @if (auth()->user()->can('send_notification'))
        <li>
            <a href="#" data-href="{{ action('NotificationController@getTemplate', ["transaction_id" => $id, "template_for" => "new_sale"]) }}" class="btn-modal" data-container=".view_modal">
                <i class="fa fa-envelope" aria-hidden="true"></i> @lang("lang_v1.new_sale_notification")
            </a>
        </li>
    @endif
@else
    @if (auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.access"))
        <li>
            <a href="#" data-href="{{ action('SellController@show', [$id]) }}" class="btn-modal" data-container=".view_modal">
                <i class="fa fa-external-link" aria-hidden="true"></i> @lang("messages.view")
            </a>
        </li>
        <li>
            <a data-href="{{ action('SellController@editInvoiceTrans', [$id]) }}" class="edit_transaction_button">
                <i class="glyphicon glyphicon-edit"></i> @lang("messages.edit_invoice")
            </a>
        </li>
    @endif
    @if (auth()->user()->can("sell.delete"))
        <li>
            <a href="{{ action('SellPosController@destroy', [$id]) }}" class="delete-sale">
                <i class="fa fa-trash"></i> @lang("messages.delete")
            </a>
        </li>
    @endif
@endif