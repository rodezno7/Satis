 <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="width: 60%">
    <div class="modal-content" style="border-radius: 15px;">
        <div class="modal-header">
            <h3>@lang('lang_v1.name_ins'): {{$kit_name}}</h3>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped ajax_view table-text-center" id="kit_table" width="100%">
                            <thead>
                                <tr>
                                  <th style="width: 20%;">@lang('sale.product')</th>
                                  <th style="width: 16%;">@lang('product.sku')</th>
                                  <th style="width: 16%;">@lang('product.brand')</th>
                                  <th style="width: 16%;">@lang('product.unit')</th>
                                  <th style="width: 16%;">@lang('product.price')</th>
                                  <th style="width: 16%;">@lang('product.product_quantity')</th>
                              </tr>
                          </thead>
                          <tbody>
                            @foreach($data as $item)
                            <tr>
                                @if(($item->sku) == ($item->sub_sku))
                                <td>{{ $item->name_product }}</td>
                                @else
                                <td>{{ $item->name_product }} {{ $item->name_variation }}</td>
                                @endif
                                <td>{{ $item->sub_sku }}</td>
                                @if($item->brand == null)
                                <td>N/A</td>
                                @else
                                <td>{{ $item->brand }}</td>
                                @endif
                                
                                <td>{{ $item->unit }}</td>
                                <td>{{ $item->default_purchase_price }}</td>
                                <td>{{ $item->quantity }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal" id="btnClose">Cerrar</button>
        </div>
    </div>
</div>
</div>