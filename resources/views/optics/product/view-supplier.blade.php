 <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="width: 60%">
    <div class="modal-content" style="border-radius: 15px;">
        <div class="modal-header">
            <h3>@lang('lang_v1.name_ins'): {{$product_name}}</h3>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped ajax_view table-text-center" id="product_table" width="100%">
                            <thead>
                                <tr>
                                    <th>@lang('business.business_name')</th>
                                    <th>@lang('contact.name')</th>
                                    <th>@lang('contact.contact')</th>

                                    @if ($clasification != 'material')    
                                    <th>@lang('contact.catalogue')</th>
                                    <th>@lang('contact.uxc')</th>
                                    <th>@lang('lang_v1.weight')</th>
                                    <th>@lang('contact.dimensions')</th>
                                    <th>@lang('contact.custom')</th>
                                    @endif

                                    <th>@lang('contact.last_purchase_date')</th>
                                    <th>@lang('lang_v1.quantity')</th>
                                    <th>@lang('purchase.unit_price')</th>
                                    <th>@lang('purchase.purchase_total')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataSupplier as $supplier)
                                <tr>
                                    <td>{{ $supplier->supplier_business_name }}</td>
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ $supplier->mobile }}</td>

                                    @if ($clasification != 'material')
                                    <td>{{ $supplier->catalogue }}</td>
                                    <td>{{ $supplier->uxc }}</td>
                                    <td>{{ $supplier->weight }}</td>
                                    <td>{{ $supplier->dimensions }}</td>
                                    <td>{{ $supplier->custom_field }}</td>
                                    @endif

                                    <td>{{ $supplier->last_purchase }}</td>
                                    <td>{{ $supplier->quantity }}</td>
                                    <td>{{ $supplier->price }}</td>
                                    @if($supplier->quantity != 'N/A')
                                    <td>{{ number_format($supplier->total, 2) }}</td>
                                    @else
                                    <td>N/A</td>
                                    @endif
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