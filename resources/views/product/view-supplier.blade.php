{!! Form::open(['url' => action('ProductController@addSupplier', [$product->id]), 'method' => 'post' ]) !!}

<div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="width: 60%">
    <div class="modal-content" style="border-radius: 15px;">
        <div class="modal-header">
            <h3>@lang('lang_v1.name_ins'): {{$product->name}}</h3>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-6 col-sm-12">
                    <div class="form-group">
                      {!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
                      <div class="input-group">
                        <span class="input-group-addon">
                          <i class="fa fa-user"></i>
                        </span>
                        {!! Form::select('contact_id', [], null, ['style' => 'width: 100%', 'class' => 'form-control',
                        'placeholder' => __('messages.please_select'), 'id' => 'supplier_id']); !!}
                        <span class="input-group-btn">
                          <button type="button" class="btn btn-default bg-white btn-flat add_new_supplier" data-name=""><i
                              class="fa fa-plus-circle text-primary fa-lg"></i></button>
                        </span>
                      </div>
                    </div>
                  </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped ajax_view table-text-center" id="supplier_table" width="100%">
                            <thead>
                                <tr>
                                    <th>@lang('business.business_name')</th>
                                    <th>@lang('contact.name')</th>
                                    <th>@lang('contact.contact')</th>

                                    {{-- <th>@lang('contact.catalogue')</th>
                                    <th>@lang('contact.uxc')</th>
                                    <th>@lang('lang_v1.weight')</th>
                                    <th>@lang('contact.dimensions')</th>
                                    <th>@lang('contact.custom')</th>

                                    <th>@lang('contact.last_purchase_date')</th>
                                    <th>@lang('lang_v1.quantity')</th>
                                    <th>@lang('purchase.unit_price')</th>
                                    <th>@lang('purchase.purchase_total')</th> --}}
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="listaSupplier">
                                @for ($i = 0; $i < count($dataSupplier); $i++)
                                <tr class="selected" id="fila{{ $i }}" style="height: 10px">
                                    <td><input type="hidden" name="supplier_ids[]" value="{{ $dataSupplier[$i]->id }}" id="supplier_id-{{ $dataSupplier[$i]->id }}">{{ $dataSupplier[$i]->supplier_business_name }}</td>
                                    <td>{{ $dataSupplier[$i]->name }}</td>
                                    <td>{{ $dataSupplier[$i]->mobile }}</td>

                                    {{-- <td>{{ $supplier->catalogue }}</td>
                                    <td>{{ $supplier->uxc }}</td>
                                    <td>{{ $supplier->weight }}</td>
                                    <td>{{ $supplier->dimensions }}</td>
                                    <td>{{ $supplier->custom_field }}</td> --}}

                                    {{-- <td>{{ $supplier->last_purchase }}</td>
                                    <td>{{ $supplier->quantity }}</td>
                                    <td>{{ $supplier->price }}</td>
                                    @if($supplier->quantity != 'N/A')
                                    <td>{{ number_format($supplier->total, 2) }}</td>
                                    @else
                                    <td>N/A</td>
                                    @endif --}}
                                    <td>
                                        <button id="bitem{{ $i }}" type="button" class="btn btn-danger btn-xs remove-item" onClick="deleteSupplierTr({{ $i }}, {{ $dataSupplier[$i]->id }});">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endfor
                                {{-- @foreach($dataSupplier as $supplier)
                                <tr>
                                    <td>{{ $supplier->supplier_business_name }}</td>
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ $supplier->mobile }}</td>--}}

                                    {{-- <td>{{ $supplier->catalogue }}</td>
                                    <td>{{ $supplier->uxc }}</td>
                                    <td>{{ $supplier->weight }}</td>
                                    <td>{{ $supplier->dimensions }}</td>
                                    <td>{{ $supplier->custom_field }}</td> --}}

                                    {{-- <td>{{ $supplier->last_purchase }}</td>
                                    <td>{{ $supplier->quantity }}</td>
                                    <td>{{ $supplier->price }}</td>
                                    @if($supplier->quantity != 'N/A')
                                    <td>{{ number_format($supplier->total, 2) }}</td>
                                    @else
                                    <td>N/A</td>
                                    @endif --}}
                                    {{--<td>
                                        <button id="bitem'+cont+'" type="button" class="btn btn-danger btn-xs remove-item" onClick="deleteSupplierTr('+cont+', '+supplier_id+');">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" value="{{ $product->id }}" id="product_id">
                <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>
        </div>
    </div>
</div>

{!! Form::close() !!}
<script type="text/javascript">
    
</script>