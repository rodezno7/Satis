<div class="pos-tab-content">
 <div class="row">
    <div class="col-sm-4">
        <div class="form-group">
            @php
            $purchase_prefix = '';
            if(!empty($business->ref_no_prefixes['purchase'])){
                $purchase_prefix = $business->ref_no_prefixes['purchase'];
            }
            @endphp
            {!! Form::label('ref_no_prefixes[purchase]', __('lang_v1.purchase_order') . ':') !!}
            {!! Form::text('ref_no_prefixes[purchase]', $purchase_prefix, ['class' => 'form-control']); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            @php
            $purchase_return = '';
            if(!empty($business->ref_no_prefixes['purchase_return'])){
                $purchase_return = $business->ref_no_prefixes['purchase_return'];
            }
            @endphp
            {!! Form::label('ref_no_prefixes[purchase_return]', __('lang_v1.purchase_return') . ':') !!}
            {!! Form::text('ref_no_prefixes[purchase_return]', $purchase_return, ['class' => 'form-control']); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            @php
            $stock_transfer_prefix = '';
            if(!empty($business->ref_no_prefixes['stock_transfer'])){
                $stock_transfer_prefix = $business->ref_no_prefixes['stock_transfer'];
            }
            @endphp
            {!! Form::label('ref_no_prefixes[stock_transfer]', __('lang_v1.stock_transfer') . ':') !!}
            {!! Form::text('ref_no_prefixes[stock_transfer]', $stock_transfer_prefix, ['class' => 'form-control']); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            @php
            $stock_adjustment_prefix = '';
            if(!empty($business->ref_no_prefixes['stock_adjustment'])){
                $stock_adjustment_prefix = $business->ref_no_prefixes['stock_adjustment'];
            }
            @endphp
            {!! Form::label('ref_no_prefixes[stock_adjustment]', __('stock_adjustment.stock_adjustment') . ' (' . __('accounting.inflow') . '):') !!}
            {!! Form::text('ref_no_prefixes[stock_adjustment]', $stock_adjustment_prefix, ['class' => 'form-control']); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            @php
            $stock_adjustment_out_prefix = '';
            if(!empty($business->ref_no_prefixes['stock_adjustment_out'])){
                $stock_adjustment_out_prefix = $business->ref_no_prefixes['stock_adjustment_out'];
            }
            @endphp
            {!! Form::label('ref_no_prefixes[stock_adjustment_out]', __('stock_adjustment.stock_adjustment') . ' (' . __('accounting.outflow') . '):') !!}
            {!! Form::text('ref_no_prefixes[stock_adjustment_out]', $stock_adjustment_out_prefix, ['class' => 'form-control']); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            @php
            $sell_return_prefix = '';
            if(!empty($business->ref_no_prefixes['sell_return'])){
                $sell_return_prefix = $business->ref_no_prefixes['sell_return'];
            }
            @endphp
            {!! Form::label('ref_no_prefixes[sell_return]', __('lang_v1.sell_return') . ':') !!}
            {!! Form::text('ref_no_prefixes[sell_return]', $sell_return_prefix, ['class' => 'form-control']); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            @php
            $expenses_prefix = '';
            if(!empty($business->ref_no_prefixes['expense'])){
                $expenses_prefix = $business->ref_no_prefixes['expense'];
            }
            @endphp
            {!! Form::label('ref_no_prefixes[expense]', __('expense.expenses') . ':') !!}
            {!! Form::text('ref_no_prefixes[expense]', $expenses_prefix, ['class' => 'form-control']); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            @php
            $contacts_prefix = '';
            if(!empty($business->ref_no_prefixes['contacts'])){
                $contacts_prefix = $business->ref_no_prefixes['contacts'];
            }
            @endphp
            {!! Form::label('ref_no_prefixes[contacts]', __('contact.contacts') . ':') !!}
            {!! Form::text('ref_no_prefixes[contacts]', $contacts_prefix, ['class' => 'form-control']); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('portfolio_prefix', __('customer.portfolios') . ':') !!}
            {!! Form::text('portfolio_prefix', $business->portfolio_prefix, ['class' => 'form-control text-uppercase']); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            @php
            $purchase_payment = '';
            if(!empty($business->ref_no_prefixes['purchase_payment'])){
                $purchase_payment = $business->ref_no_prefixes['purchase_payment'];
            }
            @endphp
            {!! Form::label('ref_no_prefixes[purchase_payment]', __('lang_v1.purchase_payment') . ':') !!}
            {!! Form::text('ref_no_prefixes[purchase_payment]', $purchase_payment, ['class' => 'form-control']); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            @php
            $sell_payment = '';
            if(!empty($business->ref_no_prefixes['sell_payment'])){
                $sell_payment = $business->ref_no_prefixes['sell_payment'];
            }
            @endphp
            {!! Form::label('ref_no_prefixes[sell_payment]', __('lang_v1.sell_payment') . ':') !!}
            {!! Form::text('ref_no_prefixes[sell_payment]', $sell_payment, ['class' => 'form-control']); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            @php
            $expense_payment = '';
            if(!empty($business->ref_no_prefixes['expense_payment'])){
                $expense_payment = $business->ref_no_prefixes['expense_payment'];
            }
            @endphp
            {!! Form::label('ref_no_prefixes[expense_payment]', __('lang_v1.expense_payment') . ':') !!}
            {!! Form::text('ref_no_prefixes[expense_payment]', $expense_payment, ['class' => 'form-control']); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            @php
            $business_location_prefix = '';
            if(!empty($business->ref_no_prefixes['business_location'])){
                $business_location_prefix = $business->ref_no_prefixes['business_location'];
            }
            @endphp
            {!! Form::label('ref_no_prefixes[business_location]', __('business.business_location') . ':') !!}
            {!! Form::text('ref_no_prefixes[business_location]', $business_location_prefix, ['class' => 'form-control']); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            @php
            $username_prefix = !empty($business->ref_no_prefixes['username']) ? $business->ref_no_prefixes['username'] : '';
            @endphp
            {!! Form::label('ref_no_prefixes[username]', __('business.username') . ':') !!}
            {!! Form::text('ref_no_prefixes[username]', $username_prefix, ['class' => 'form-control']); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('claim_prefix', __('business.claim') . ':') !!}
            {!! Form::text('claim_prefix', $business->claim_prefix, ['class' => 'form-control']); !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('cashier_prefix', __('business.cashier') . ':') !!}
            {!! Form::text('cashier_prefix', $business->cashier_prefix, ['class' => 'form-control']); !!}
        </div>
    </div>

    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('credit_prefix', __('business.credit') . ':') !!}
            {!! Form::text('credit_prefix', $business->credit_prefix, ['class' => 'form-control']); !!}
        </div>
    </div>

    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label( __('business.status_claim') . ':') !!}
            {!! Form::text('status_claim_prefix', $business->status_claim_prefix, ['class' => 'form-control']); !!}
        </div>
    </div>

    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label( __('business.claim_type') . ':') !!}
            {!! Form::text('claim_type_prefix', $business->claim_type_prefix, ['class' => 'form-control']); !!}
        </div>
    </div>

    <!-- Warehouse -->
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('warehouse_prefix', __('warehouse.warehouses') . ':') !!}
            {!! Form::text('warehouse_prefix', $business->warehouse_prefix, ['class' => 'form-control']); !!}
        </div>
    </div>

    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label( __('quote.quote') . ':') !!}
            {!! Form::text('quote_prefix', $business->quote_prefix, ['class' => 'form-control']); !!}
        </div>
    </div>

    {{-- Physical inventory --}}
    <div class="col-sm-4">
        <div class="form-group">
            @php
            $physical_inventory_prefix = '';
            if(!empty($business->ref_no_prefixes['physical_inventory'])){
                $physical_inventory_prefix = $business->ref_no_prefixes['physical_inventory'];
            }
            @endphp
            {!! Form::label('ref_no_prefixes[physical_inventory]', __('physical_inventory.physical_inventory') . ':') !!}
            {!! Form::text('ref_no_prefixes[physical_inventory]', $physical_inventory_prefix, ['class' => 'form-control']); !!}
        </div>
    </div>

    {{-- Apportionment --}}
    <div class="col-sm-4">
        <div class="form-group">
            @php
            $apportionment_prefix = '';
            if (! empty($business->ref_no_prefixes['apportionment'])) {
                $apportionment_prefix = $business->ref_no_prefixes['apportionment'];
            }
            @endphp
            {!! Form::label('ref_no_prefixes[apportionment]', __('apportionment.apportionments') . ':') !!}
            {!! Form::text('ref_no_prefixes[apportionment]', $apportionment_prefix, ['class' => 'form-control']); !!}
        </div>
    </div>
    <!-- fixed asset -->
    <div class="col-sm-4">
        <div class="form-group">
            {!! Form::label('fixed_asset_prefix', __('fixed_asset.fixed_asset') . ':') !!}
            {!! Form::text('fixed_asset_prefix', $business->fixed_asset_prefix, ['class' => 'form-control']); !!}
        </div>
    </div>

    {{-- Reservation payment --}}
    <div class="col-sm-4">
        <div class="form-group">
            @php
            $reservation_payment = '';

            if (! empty($business->ref_no_prefixes['reservation_payment'])) {
                $reservation_payment = $business->ref_no_prefixes['reservation_payment'];
            }
            @endphp

            {!! Form::label('ref_no_prefixes[reservation_payment]', __('lang_v1.reservation_payment') . ':') !!}
            {!! Form::text('ref_no_prefixes[reservation_payment]', $reservation_payment, ['class' => 'form-control']); !!}
        </div>
    </div>
</div>
</div>