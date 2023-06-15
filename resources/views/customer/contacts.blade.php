{!! Form::open(['url' => action('CustomerController@addContact', [$customer->id]), 'method' => 'post', 'id' => 'form_add_contact']) !!}
<div class="modal-dialog modal-lg" role="document" id="modalContact">
    <form id="form-edit-customer">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('customer.Contacts'): {{ $customer->name }}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <h4>
                                <button type="button" onclick="addReference()" class="btn btn-info"
                                    title="@lang('contact.add_contact')"
                                    style="padding: 5px 8px; margin-right: 5px; margin-top: -2px;">
                                    <i class="fa fa-plus"></i>
                                </button>
                                <b>@lang('customer.contactMult')</b>
                            </h4>
                            <table class="table table-responsive table-condensed table-text-center" id="customer_table"
                                style="font-size: inherit;">
                                <thead>
                                    <tr class="active">
                                        <th width="35%">@lang('contact.name')</th>
                                        <th class="text-center" width="15%">@lang('contact.mobile')</th>
                                        <th class="text-center" width="15%">@lang('contact.landline')</th>
                                        <th class="text-center" width="20%">@lang('lang_v1.email_address')</th>
                                        <th class="text-center" width="15%">@lang('customer.charge')</th>
                                        <th id="dele">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody id="referencesItems">
                                    @foreach ($contacts as $item)
                                    <tr>
                                        {{-- Se recorren todos los contactos pertenecientes a el cliente seleccionado
                                        --}}
                                        {!! Form::hidden('contactid[]', $item->id) !!}
                                        <td><input type="text" name="contactname[]" class="form-control input-sm input_name" id="1"
                                                value="{{ $item->name }}" required></td>
                                        <td><input type="text" name="contactphone[]"
                                                class="form-control input-sm input_number input_phone" id="2"
                                                value="{{ $item->phone }}" required></td>
                                        <td><input type="text" name="contactlandline[]"
                                                class="form-control input-sm input_number" id="3"
                                                value="{{ $item->landline }}" required></td>
                                        <td><input type="text" name="contactemail[]" class="form-control input-sm"
                                                id="4" value="{{ $item->email }}" required></td>
                                        <td><input type="text" name="contactcargo[]" class="form-control input-sm"
                                                id="4" value="{{ $item->cargo }}" required></td>
                                        <td><button type="button" class="btn btn-danger btn-xs remove-item"><i
                                                    class="fa fa-times"></i></button></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" value="{{ count($contacts) }}" id="count_contact">
                <input type="hidden" value="{{ $customer->id }}" id="customer_id">
                <input type="button" class="btn btn-primary" value="@lang('messages.save')" onClick="saveContact()">
                <button type="button" class="btn btn-danger" data-dismiss="modal"
                    id="btn-close-modal-edit-customer">@lang('messages.cancel')</button>
            </div>
        </div>
    </form>
</div>
{!! Form::close() !!}