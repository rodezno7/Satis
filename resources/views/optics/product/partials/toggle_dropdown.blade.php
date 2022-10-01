@if ($clasification != 'service')
    @if ($clasification != "material")
    <li>
        <a href="{{ action('LabelsController@show', ['product_id' => $id]) }}" data-toggle="tooltip" title="Print Barcode/Label">
            <i class="fa fa-barcode"></i> @lang('barcode.labels')
        </a>
    </li>
    @endif
@endif

@if (auth()->user()->can('product.view'))
    <li>
        <a href="{{ action('Optics\ProductController@view', [$id]) }}" class="view-product">
            <i class="fa fa-eye"></i> @lang("messages.view")
        </a>
    </li>

    @if ($clasification == "product" || $clasification == "material")
    <li>
        <a href="{{ action('Optics\ProductController@viewSupplier', [$id]) }}" class="view-supplier" >
            <i class="fa fa-eye"></i> @lang("product.view_suppliers")
        </a>
    </li>
    @endif

    @if ($clasification == "kits")
    <li>
        <a href="{{ action('Optics\ProductController@viewKit', [$id]) }}" class="view-kit" >
            <i class="fa fa-eye"></i> @lang("product.view_kit")
        </a>
    </li>
    @endif
@endif

@if (auth()->user()->can('product.update'))
<li>
    <a href="{{ action('Optics\ProductController@edit', [$id]) }}">
        <i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")
    </a>
</li>
@endif

@if (auth()->user()->can('product.delete'))
<li>
    <a href="{{ action('Optics\ProductController@destroy', [$id]) }}" class="delete-product">
        <i class="fa fa-trash"></i> @lang("messages.delete")
    </a>
</li>
@endif

<li class="divider"></li>

@if (auth()->user()->can('product.create'))
    @if ($clasification != 'service')
        @if (empty($opening_stock))
        <li>
            <a href="#" data-href="{{ action('OpeningStockController@add', ['product_id' => $id, 'action' => 'create']) }}" class="add-opening-stock">
                <i class="fa fa-database"></i> @lang("lang_v1.add_opening_stock")
            </a>
        </li>
        @else
        <li>
            <a href="#" data-href="{{ action('OpeningStockController@add', ['product_id' => $id, 'action' => 'view']) }}" class="add-opening-stock">
                <i class="fa fa-database"></i> @lang("lang_v1.view_opening_stock")
            </a>
        </li>
        @endif
    @endif

    @if ($selling_price_group_count > 0)
    <li>
        <a href="{{ action('Optics\ProductController@addSellingPrices', [$id]) }}">
            <i class="fa fa-money"></i> @lang("lang_v1.add_selling_price_group_prices")
        </a>
    </li>
    @endif

    <li>
        <a href="{{ action('Optics\ProductController@create', ["d" => $id]) }}">
            <i class="fa fa-copy"></i> @lang("lang_v1.duplicate_product")
        </a>
    </li>
@endif