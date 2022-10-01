<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('DocumentTypeController@store'), 'method' => 'post', 'id' => 'documenttype_add_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'document_type.add_document' )</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                {{-- document_name --}}
                <div class="col-sm-8">
                    <div class="form-group">
                        {!! Form::label('document_name', __('document_type.document_name') . ':*') !!}
                        {!! Form::text('document_name', null, ['class' => 'form-control', 'required', 'placeholder' => __('document_type.document_name')]) !!}
                    </div>
                </div>

                {{-- is_active --}}
                <div class="col-sm-4" style="margin-top: 25px;">
                    <div class="form-group">
                        <div class="form-check">
                            {!! Form::checkbox('is_active', 1, true, ['class' => 'form-check-input']) !!}
                            {!! Form::label('is_active', __('document_type.is_active'), ['class' => 'form-check-label']) !!}
                            @show_tooltip(__('document_type.tooltip_is_active'))
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- short_name --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('short_name', __('document_type.short_name') . ':') !!}
                        {!! Form::text('short_name', null, ['class' => 'form-control', 'placeholder' => __('document_type.short_name')]) !!}
                    </div>
                </div>

                {{-- print_format --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('print_format', __('document_type.print_format') . ':') !!}
                        @show_tooltip(__('document_type.tooltip_print_format'))
                        {!! Form::select('print_format', $print_formats, null, ['class' => 'form-control', 'placeholder' => __('document_type.print_format'), 'required']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- tax_inc --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        <div class="form-check">
                            {!! Form::checkbox('tax_inc', 1, true, ['class' => 'form-check-input']) !!}
                            {!! Form::label('tax_inc', __('document_type.tax_inc'), ['class' => 'form-check-label']) !!}
                            @show_tooltip(__('document_type.tooltip_tax_inc'))
                        </div>
                    </div>
                </div>

                {{-- tax_exempt --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        <div class="form-check">
                            {!! Form::checkbox('tax_exempt', 1, false, ['class' => 'form-check-input']) !!}
                            {!! Form::label('tax_exempt', __('document_type.tax_exempt'), ['class' => 'form-check-label']) !!}
                            @show_tooltip(__('document_type.tooltip_tax_exempt'))

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- is_document_purchase --}}
                <div class="col-sm-5">
                    <div class="form-group">
                        <div class="form-check">
                            {!! Form::checkbox('is_document_purchase', 1, false, ['class' => 'form-check-input', 'id' => 'is_document_purchase']) !!}
                            {!! Form::label('is_document_purchase', __('document_type.is_document_purchase'), ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>

                {{-- is_document_sale --}}
                <div class="col-sm-4">
                    <div class="form-group">
                        <div class="form-check">
                            {!! Form::checkbox('is_document_sale', 1, false, ['class' => 'form-check-input', 'id' => 'is_document_sale']) !!}
                            {!! Form::label('is_document_sale', __('document_type.is_document_sale'), ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>

                {{-- is_default --}}
                <div class="col-sm-3">
                    <div class="form-group">
                        <div class="form-check">
                            <div id="default">
                                {!! Form::checkbox('is_default', 1, false, ['class' => 'form-check-input', 'id' => 'is_default']) !!}
                                {!! Form::label('is_default', __('document_type.default'), ['class' => 'form-check-label']) !!}
                                <input type="hidden" name="document_id" id="document_id">
                                <input type="hidden" name="token" id="token" value="{{ csrf_token() }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- is_return_document --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        <div class="form-check">
                            {!! Form::checkbox('is_return_document', 1, false, ['class' => 'form-check-input']) !!}
                            {!! Form::label('is_return_document', __('document_type.is_return_document'), ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>

                {{-- max_operation --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('max_operation', __('document_type.max_operation') . ':') !!}
                        {!! Form::text('max_operation', null,
                            ['class' => 'form-control input_number', 'placeholder' => __('document_type.max_operation')]) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- document_class_id --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('document_class_id', __('document_type.document_class') . ':') !!}
                        {!! Form::select('document_class_id', $document_classes, null,
                            ['class' => 'form-control', 'placeholder' => __('messages.please_select' )]); !!}
                    </div>
                </div>

                {{-- document_type_number --}}
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('document_type_number', __('document_type.title') . ':') !!}
                        {!! Form::text('document_type_number', null,
                            ['class' => 'form-control input_number', 'placeholder' => '##']) !!}
                    </div>
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

<script>
    $(document).ready(function() {
        $('#default').hide();

        $("#is_document_sale").on('click', function() {
            showDefault();
        });


    });

    function showDefault() {
        if ($("#is_document_sale").is(":checked")) {
            $('#is_document_sale').val('1');
            $('#default').show();
        } else {
            $('#default').hide();
        }
    }

    $("#is_default").on('click', function() {
        if ($("#is_default").is(":checked")) {
            let route = '/documents/default';
            let token = $("#token").val();

            $.ajax({
                url: route,
                headers: {
                    'X-CSRF-TOKEN': token
                },
                type: 'GET',
                dataType: 'json',
                success: function(result) {
                    
                    if (Object.keys(result).length > 0) {
                      let id = result.id;
                        Swal.fire({
                            title: LANG.default_document_already_exists + ": " + result.document_name,
                            text: LANG.want_change_it,
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: "{{ __('messages.accept') }}",
                            cancelButtonText: "{{ __('messages.cancel') }}"
                        }).then((willAccept) => {
                            if (willAccept.isConfirmed) {
                                $("#is_default").val(1);
                                $("#document_id").val(id);
                            } else if (willAccept.dismiss === Swal.DismissReason.cancel) {
                                $("#is_default").prop("checked", false);
                                $("#is_default").val(0);
                            }
                        });
                    }else{
                      $("#is_default").val(1);
                      // $("#document_id").text(id);
                    }
                },
                error: function(msj) {

                }
            });
        }
    });

</script>
