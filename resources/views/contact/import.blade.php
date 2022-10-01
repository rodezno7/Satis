@extends('layouts.app')
@section('title', __('contact.import_suppliers'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('contact.import_suppliers')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    
    @if (session('notification') || !empty($notification))
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    @if(!empty($notification['msg']))
                        {{$notification['msg']}}
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
                    {!! Form::open(['url' => action('ContactController@postImportContacts'), 'method' => 'post', 'enctype' => 'multipart/form-data' ]) !!}
                        <div class="row">
                            <div class="col-sm-6">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    {!! Form::label('name', __( 'product.file_to_import' ) . ':') !!}
                                    {!! Form::file('contacts_xlsx', ['accept'=> '.xlsx']); !!}
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
                            <a href="{{ asset('uploads/files/import_supplier_provider_xlsx_template_es.xlsx') }}" class="btn btn-success" download><i class="fa fa-download"></i> @lang('contact.download_xlsx_file_template')</a>
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
                    <strong>@lang('lang_v1.instruction_line1')</strong><br>
                    @lang('lang_v1.instruction_line2')
                    <br><br>
                    <table class="table table-striped">
                        <tr>
                            <th>@lang('lang_v1.col_no')</th>
                            <th>@lang('lang_v1.col_name')</th>
                            <th>@lang('lang_v1.instruction')</th>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>@lang('contact.name') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>@lang('business.business_name') <br><small class="text-muted">(@lang('lang_v1.required'))</small></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>@lang('lang_v1.code') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                            <td>@lang('lang_v1.code_ins')</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>@lang('contact.tax_no') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>@lang('business.nit') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>6</td>
                            <td>@lang('contact.business_activity') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>7</td>
                            <td>@lang('contact.business_type') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>8</td>
                            <td>@lang('contact.is_exempt') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                            <td><strong>@lang('lang_v1.is_exempt_ins')</strong></td>
                        </tr>
                        <tr>
                            <td>9</td>
                            <td>@lang('lang_v1.opening_balance') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>10</td>
                            <td>@lang('lang_v1.payment_condition') <br><small class="text-muted">(@lang('lang_v1.required'))</small></td>
                            <td><strong>@lang('lang_v1.payment_condition_ins')</strong></td>
                        </tr>
                        <tr>
                            <td>11</td>
                            <td>@lang('contact.pay_term_days') <br><small class="text-muted">(@lang('lang_v1.required_if_condition_credit'))</small></td>
                            <td>&nbsp;</td>
                        </tr>                        
                        <tr>
                            <td>12</td>
                            <td>@lang('lang_v1.credit_limit') <small class="text-muted">(@lang('lang_v1.required_if_condition_credit'))</small></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>13</td>
                            <td>@lang('business.email') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>14</td>
                            <td>@lang('contact.mobile') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                            <td>&nbsp;</td>
                        </tr>                        
                        <tr>
                            <td>15</td>
                            <td>@lang('contact.landline') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>16</td>
                            <td>@lang('business.city') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>17</td>
                            <td>@lang('business.state') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>18</td>
                            <td>@lang('business.country') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>19</td>
                            <td>@lang('business.landmark') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>20</td>
                             <td>@lang('contact.is_supplier') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                             <td><strong>@lang('lang_v1.available_options'): 0 => @lang('lang_v1.no'), 1 => @lang('lang_v1.yes')</strong></td>
                        </tr>
                        <tr>
                            <td>21</td>
                            <td>@lang('contact.is_provider') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                            <td><strong>@lang('lang_v1.available_options'): 0 => @lang('lang_v1.no'), 1 => @lang('lang_v1.yes')</strong></td>
                        </tr>
                        <tr>
                            <td>22</td>
                            <td>@lang('contact.supplier_accounting_account') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>23</td>
                            <td>@lang('contact.provider_accounting_account') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

@endsection