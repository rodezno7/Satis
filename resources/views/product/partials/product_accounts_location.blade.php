<div class="modal-dialog modal-lg">
    <div class="modal-content">
        {!! Form::open(['url' => action('ProductController@postProductAccountsLocation', [$product->id]), 'method' => 'post', 'id' => 'product_accounts_location_form' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'product.accounting_accounts_location' )</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12">
                    <strong>{{ mb_strtoupper(__('product.product')) }}: </strong>{{ $product->name }}
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default" style="margin-top: 15px;">
                        <input type="hidden" id="creditor_account_code" value="{{ $creditor_account_code }}">
                        <div class="panel-heading">{{ mb_strtoupper(__('product.input_account')) }}
                            <div class="panel-tools pull-right">
                                <button type="button" style="padding-top: 0; padding-bottom: 0;" class="btn btn-panel-tool"  data-toggle="collapse" data-target="#input-accounts-box" id="btn-collapse-gi">
                                    <i class="fa fa-minus" id="create-icon-collapsed-gi"></i>
                                </button>
                            </div>
                        </div>
                        <div class="panel-body collapse in" id="input-accounts-box" aria-expanded="true">
                            <table class="table" style="margin-bottom: 0;">
                                <thead>
                                    <tr>
                                        <th style="width: 35%;">@lang('business.location')</th>
                                        <th>@lang('accounting.accounting_account')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($locations as $location_id => $location_name)
                                        @if (array_search($location_id, array_column($product_accounts_input, 'location_id')) !== false)
                                            @php $index = array_search($location_id, array_column($product_accounts_input, 'location_id')); @endphp
                                            <tr>
                                                <td>
                                                    {!! Form::hidden("input_accounts[$loop->index][pal_id]", $product_accounts_input[$index]['pal_id']) !!}
                                                    {!! Form::hidden("input_accounts[$loop->index][location_id]", $product_accounts_input[$index]['location_id']) !!}
                                                    {{ $product_accounts_input[$index]['location_name'] }}
                                                </td>
                                                <td>
                                                    {!! Form::select("input_accounts[$loop->index][account_id]",
                                                        [$product_accounts_input[$index]['account_id'] => $product_accounts_input[$index]['account_name']],
                                                        $product_accounts_input[$index]['account_id'], ["class" => "form-control location_account",
                                                        "style" => 'width: 100%;']) !!}
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td>
                                                    {!! Form::hidden("input_accounts[$loop->index][pal_id]", null) !!}
                                                    {!! Form::hidden("input_accounts[$loop->index][location_id]", $location_id) !!}
                                                    {{ $location_name }}
                                                </td>
                                                <td>
                                                    {!! Form::select("input_accounts[$loop->index][account_id]", [], null,
                                                        ["class" => "form-control location_account", "style" => 'width: 100%;']) !!}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($product->clasification == 'product')
                        <div class="panel panel-default">
                            <div class="panel-heading">{{ mb_strtoupper(__('product.inventory_account')) }}
                                <div class="panel-tools pull-right">
                                    <button type="button" style="padding-top: 0; padding-bottom: 0;" class="btn btn-panel-tool"  data-toggle="collapse" data-target="#inventory-accounts-box" id="btn-collapse-fi">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="panel-body collapse" id="inventory-accounts-box" aria-expanded="false">
                                <table class="table" style="margin-bottom: 0;">
                                    <thead>
                                        <tr>
                                            <th style="width: 35%;">@lang('business.location')</th>
                                            <th>@lang('accounting.accounting_account')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($locations as $location_id => $location_name)
                                            @if (array_search($location_id, array_column($product_accounts_inventory, 'location_id')) !== false)
                                                @php $index = array_search($location_id, array_column($product_accounts_inventory, 'location_id')); @endphp
                                                <tr>
                                                    <td>
                                                        {!! Form::hidden("inventory_accounts[$loop->index][pal_id]", $product_accounts_inventory[$index]['pal_id']) !!}
                                                        {!! Form::hidden("inventory_accounts[$loop->index][location_id]", $product_accounts_inventory[$index]['location_id']) !!}
                                                        {{ $product_accounts_inventory[$index]['location_name'] }}
                                                    </td>
                                                    <td>
                                                        {!! Form::select("inventory_accounts[$loop->index][account_id]",
                                                            [$product_accounts_inventory[$index]['account_id'] => $product_accounts_inventory[$index]['account_name']],
                                                            $product_accounts_inventory[$index]['account_id'], ["class" => "form-control location_account",
                                                            "style" => 'width: 100%;']) !!}
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td>
                                                        {!! Form::hidden("inventory_accounts[$loop->index][pal_id]", null) !!}
                                                        {!! Form::hidden("inventory_accounts[$loop->index][location_id]", $location_id) !!}
                                                        {{ $location_name }}
                                                    </td>
                                                    <td>
                                                        {!! Form::select("inventory_accounts[$loop->index][account_id]", [], null,
                                                            ["class" => "form-control location_account", "style" => 'width: 100%;']) !!}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">{{ mb_strtoupper(__('product.cost_account')) }}
                                <div class="panel-tools pull-right">
                                    <button type="button" style="padding-top: 0; padding-bottom: 0;" class="btn btn-panel-tool"  data-toggle="collapse" data-target="#cost-accounts-box" id="btn-collapse-fi">
                                        <i class="fa fa-plus" id="create-icon-collapsed"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="panel-body collapse" id="cost-accounts-box" aria-expanded="false">
                                <table class="table" style="margin-bottom: 0;">
                                    <thead>
                                        <tr>
                                            <th style="width: 35%;">@lang('business.location')</th>
                                            <th>@lang('accounting.accounting_account')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($locations as $location_id => $location_name)
                                            @if (array_search($location_id, array_column($product_accounts_cost, 'location_id')) !== false)
                                                @php $index = array_search($location_id, array_column($product_accounts_cost, 'location_id')); @endphp
                                                <tr>
                                                    <td>
                                                        {!! Form::hidden("cost_accounts[$loop->index][pal_id]", $product_accounts_cost[$index]['pal_id']) !!}
                                                        {!! Form::hidden("cost_accounts[$loop->index][location_id]", $product_accounts_cost[$index]['location_id']) !!}
                                                        {{ $product_accounts_cost[$index]['location_name'] }}
                                                    </td>
                                                    <td>
                                                        {!! Form::select("cost_accounts[$loop->index][account_id]",
                                                            [$product_accounts_cost[$index]['account_id'] => $product_accounts_cost[$index]['account_name']],
                                                            $product_accounts_cost[$index]['account_id'], ["class" => "form-control location_account",
                                                            "style" => 'width: 100%;']) !!}
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td>
                                                        {!! Form::hidden("cost_accounts[$loop->index][pal_id]", null) !!}
                                                        {!! Form::hidden("cost_accounts[$loop->index][location_id]", $location_id) !!}
                                                        {{ $location_name }}
                                                    </td>
                                                    <td>
                                                        {!! Form::select("cost_accounts[$loop->index][account_id]", [], null,
                                                            ["class" => "form-control location_account", "style" => 'width: 100%;']) !!}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
        {!! Form::close() !!}
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->