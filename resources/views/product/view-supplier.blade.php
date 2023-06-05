{!! Form::open(['url' => action('ProductController@addSupplier', [$product->id]), 'method' => 'post', 'id' => 'form_add_supplier']) !!}
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('lang_v1.name_ins'): {{$product->name}}</h4>
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
                        'placeholder' => __('messages.please_select'), 'id' => 'supplier_id', 'minlength'=>3]); !!}
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
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="listaSupplier">
                                @for ($i = 0; $i < count($dataSupplier); $i++)
                                <tr class="selected" id="fila{{ $i }}" style="height: 10px">
                                    <td><input type="hidden" name="supplier_ids[]" value="{{ $dataSupplier[$i]->id }}" id="supplier_id-{{ $dataSupplier[$i]->id }}">{{ $dataSupplier[$i]->supplier_business_name }}</td>
                                    <td>{{ $dataSupplier[$i]->name }}</td>
                                    <td>{{ $dataSupplier[$i]->mobile }}</td>
                                    <td>
                                        <button id="bitem{{ $i }}" type="button" class="btn btn-danger btn-xs remove-item" onClick="deleteSupplierTr({{ $i }}, {{ $dataSupplier[$i]->id }});">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" value="{{ count($dataSupplier) }}" id="count_supplier">
                <input type="hidden" value="{{ $product->id }}" id="product_id">
                <button type="button" class="btn btn-primary" onClick="saveSupplier()">@lang( 'messages.save' )</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">@lang( 'messages.cancel' )</button>
            </div>
        </div>
    </div>
</div>

{!! Form::close() !!}
<script type="text/javascript">
    
</script>