<div class="modal-dialog" role="dialog" id="modal-create">
    <div class="modal-content" style="border-radius: 10px;">
        {!! Form::open(['url' => action('ImportExpenseController@store'),
            'method' => 'post', 'id' => 'import_expense_add_form']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang('import_expense.add_import_expense')</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                {{-- name --}}
                <div class="col-sm-8">
                    <div class="form-group">
                        {!! Form::label('name', __('crm.name') . ':*') !!}
                        {!! Form::text('name', null,
                            ['class' => 'form-control', 'required', 'placeholder' => __('crm.name')]) !!}
                    </div>
                </div>

                {{-- type --}}
                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('type', __('crm.type') . ':*') !!}
                        {!! Form::select('type', [
                                'purchase' => __('import_expense.purchase'),
                                'retaceo' => __('import_expense.retaceo')
                            ], '',
                            ['class' => 'form-control select2']) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">
                @lang('messages.save')
            </button>
            <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-default">
                @lang('messages.close')
            </button>
        </div>

        {!! Form::close() !!}
    </div>
</div>
