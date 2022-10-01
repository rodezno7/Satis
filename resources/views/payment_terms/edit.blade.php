<div class="modal-dialog modal-md" role="dialog">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('PaymentTermController@update', [$payment_term->id]), 'method' => 'PUT', 'id'
        => 'payment_term_edit_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('payment.edit')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('name', __('payment.name') . ' : ') !!}
                        {!! Form::text('name', $payment_term->name, ['class' => 'form-control', 'required', 'placeholder' =>
                        __('payment.name')]) !!}
                        <input type="hidden" name="id" id="payment_term_id"
                        value="{{ $payment_term->id }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('descripcion', __('payment.description') . ' : ') !!}
                        {!! Form::text('description', $payment_term->description, ['class' => 'form-control', 'placeholder' =>
                        __('Descripcion')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('Dias', __('payment.days') . ' : ') !!}
                        {!! Form::text('days', $payment_term->days, ['class' => 'form-control', 'required', 'placeholder' =>
                        __('payment.days')]) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" id="btn-edit-payment_term" class="btn btn-primary">@lang('messages.save')</button>
            <button type="button" data-dismiss="modal" aria-label="Close" id="btn-close-modal-edit-payment_term"
                class="btn btn-default">@lang('messages.close')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

@section('javascript')
    <script type="text/javascript" src="{{ asset('js/sweetalert2.js') }}"></script>
@endsection