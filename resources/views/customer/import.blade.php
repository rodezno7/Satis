@extends('layouts.app')
@section('title', __('customer.import_customers'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('customer.import_customers')
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">

        @if (session('notification') || !empty($notification))
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        @if (!empty($notification['msg']))
                            {{ $notification['msg'] }}
                        @elseif(session('notification.msg'))
                            {{ session('notification.msg') }}
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-body">
                        {!! Form::open(['url' => action('CustomerController@postImportCustomers'), 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        {!! Form::label('name', __('product.file_to_import') . ':') !!}
                                        {!! Form::file('customers_xlsx', ['accept' => '.xlsx']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <br>
                                    <button type="submit" class="btn btn-primary">@lang('messages.submit')</button>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                        <br><br>
                        <div class="row">
                            <div class="col-sm-4">
                                <a href="{{ asset('uploads/files/import_customers_xlsx_template_es.xlsx') }}" class="btn btn-success" download><i class="fa fa-download"></i> @lang('contact.download_xlsx_file_template')</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-solid">
                    <div class="box-header">
                        <h3 class="box-title">@lang('lang_v1.instructions')</h3>
                    </div>
                    <div class="box-body">
                        <strong>@lang('lang_v1.instruction_line1')</strong><br><br>
                        <ol style="margin-left: 20px;">
                            <li style="list-style-type: decimal;">@lang('lang_v1.instruction_line3')</li>
                            <li style="list-style-type: decimal;">@lang('lang_v1.instruction_line4')</li>
                            <li style="list-style-type: decimal;">@lang('lang_v1.instruction_line2')</li>
                        </ol>
                        <br>
                        <table class="table table-striped">
                            <tr>
                                <th>@lang('lang_v1.col_no')</th>
                                <th>@lang('lang_v1.col_name')</th>
                                <th>@lang('lang_v1.instruction')</th>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>@lang('customer.name') <small class="text-muted">(@lang('lang_v1.required'))</small>
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>@lang('customer.is_foreign') <small class="text-muted">(@lang('lang_v1.required'))</small>
                                </td>
                                <td>@lang('customer.is_foreign_instructions')</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>@lang('customer.dui') <small
                                        class="text-muted">(@lang('lang_v1.required_or_optional_dui'))</small></td>
                                <td>@lang('lang_v1.unique_field_dui')</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>@lang('customer.email') <small class="text-muted">(@lang('lang_v1.optional'))</small>
                                </td>
                                <td>@lang('customer.email_instructions')</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>@lang('customer.phone') <small class="text-muted">(@lang('lang_v1.required'))</small>
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>@lang('customer.address') <small class="text-muted">(@lang('lang_v1.optional'))</small>
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td>@lang('customer.country') <small class="text-muted">(@lang('lang_v1.optional'))</small>
                                </td>
                                <td>@lang('customer.foreign_key_instructions')</td>
                            </tr>
                            <tr>
                                <td>8</td>
                                <td>@lang('customer.state') <small class="text-muted">(@lang('lang_v1.optional'))</small>
                                </td>
                                <td>@lang('customer.foreign_key_instructions')</td>
                            </tr>
                            <tr>
                                <td>9</td>
                                <td>@lang('customer.city') <small class="text-muted">(@lang('lang_v1.optional'))</small>
                                </td>
                                <td>@lang('customer.foreign_key_instructions')</td>
                            </tr>
                            <tr>
                                <td>10</td>
                                <td>@lang('customer.latitude') <br><small
                                        class="text-muted">(@lang('lang_v1.optional'))</small></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>11</td>
                                <td>@lang('customer.length') <br><small
                                        class="text-muted">(@lang('lang_v1.optional'))</small></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>12</td>
                                <td>@lang('customer.is_exempt') <small class="text-muted">(@lang('lang_v1.required'))</small>
                                </td>
                                <td>@lang('customer.is_exempt_instructions')</td>
                            </tr>
                            <tr>
                                <td>13</td>
                                <td>@lang('customer.is_taxpayer') <small
                                        class="text-muted">(@lang('lang_v1.required'))</small></td>
                                <td>@lang('customer.is_taxpayer_inst')</td>
                            </tr>
                            <tr>
                                <td>14</td>
                                <td>@lang('customer.business_name') <small
                                        class="text-muted">(@lang('lang_v1.required_or_optional_taxpayer'))</small></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>15</td>
                                <td>@lang('customer.tax_number') <small
                                        class="text-muted">(@lang('lang_v1.required_or_optional_taxpayer'))</small></td>
                                <td>@lang('lang_v1.unique_field_tax_number')</td>
                            </tr>                                                                                  
                            <tr>
                                <td>16</td>
                                <td>@lang('customer.reg_number') <small
                                        class="text-muted">(@lang('lang_v1.required_or_optional_taxpayer'))</small></td>
                                <td>@lang('lang_v1.unique_field_reg_number')</td>
                            </tr>                            
                            <tr>
                                <td>17</td>
                                <td>@lang('customer.business_line') <small
                                        class="text-muted">(@lang('lang_v1.required_or_optional_taxpayer'))</small></td>
                                <td>&nbsp;</td>
                            </tr>                            
                            <tr>
                                <td>18</td>
                                <td>@lang('customer.business_type') <small
                                        class="text-muted">(@lang('lang_v1.required_or_optional_taxpayer'))</small></td>
                                <td>@lang('customer.foreign_key_instructions')</td>
                            </tr>
                            <tr>
                                <td>19</td>
                                <td>@lang('customer.accounting_account') 
                                    <small class="text-muted">(@lang('lang_v1.optional'))</small>
                                </td>                                
                            </tr>
                            <tr>
                                <td>20</td>
                                <td>@lang('customer.allowed_credit') <small
                                        class="text-muted">(@lang('lang_v1.required'))</small></td>
                                <td>@lang('customer.is_taxpayer_inst')</td>
                            </tr>

                            <tr>
                                <td>21</td>
                                <td>@lang('customer.opening_balance') <br><small
                                        class="text-muted">(@lang('customer.required_if_is_allowed_credit'))</small></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>22</td>
                                <td>@lang('customer.credit_limit') <br><small
                                        class="text-muted">(@lang('customer.required_if_is_allowed_credit'))</small></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>23</td>
                                <td>@lang('customer.payment_terms') <br><small
                                        class="text-muted">(@lang('customer.required_if_is_allowed_credit'))</small></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                              <td>24</td>
                              <td>@lang('customer.contact_mode') <br><small
                                      class="text-muted">(@lang('lang_v1.optional'))</small></td>
                              <td>@lang('customer.foreign_key_instructions')</td>
                            </tr>                            
                            <tr>
                                <td>25</td>
                                <td>@lang('customer.customer_group') <small
                                    class="text-muted">(@lang('lang_v1.optional'))</small></td>
                                    <td>@lang('customer.foreign_key_instructions')</td>
                                </tr>
                            <tr>
                                <td>26</td>
                                <td>@lang('customer.customer_portfolio') <br><small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                                <td>@lang('customer.foreign_key_instructions')</td>
                            </tr>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->

@endsection
