<div class="modal-dialog modal-md" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('PaymentTermController@store'), 'method' => 'post', 'id' =>
        'payment_terms_add_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('payment.register')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('name', __('payment.name') . ' : ') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' =>
                        __('Nombre')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('descripcion', __('payment.description') . ' : ') !!}
                        {!! Form::text('description', null, ['class' => 'form-control', 'placeholder' =>
                        __('payment.description')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('days', __('payment.days') . ' : ') !!}
                        {!! Form::text('days', null, ['class' => 'form-control input_number', 'required', 'placeholder' =>
                        __('payment.days')]) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
            <button type="button" data-dismiss="modal" aria-label="Close"
                class="btn btn-default">@lang('messages.close')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

@section('javascript')
    <script type="text/javascript" src="{{ asset('js/sweetalert2.js') }}"></script>
@endsection