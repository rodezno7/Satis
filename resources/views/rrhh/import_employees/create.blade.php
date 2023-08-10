@extends('layouts.app')

@section('title', __('rrhh.import_employees'))

@section('content')

{{-- Content Header (Page header) --}}
<section class="content-header">
    <h1>
        @lang('rrhh.import_employees')
    </h1>
</section>

{{-- Main content --}}
<section class="content">
    <div class="row">
        <div class="col-sm-12">
        	<div class="box box-solid box-default">
                <div class="box-body">
                    {!! Form::open([
                        'url' => action('ImportEmployeesController@checkFile'),
                        'method' => 'post',
                        'enctype' => 'multipart/form-data',
                        'id' => 'send_form'
                    ]) !!}

                        <div class="row">
                            {{-- file_xlsx --}}
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {!! Form::label('file_xlsx', __( 'product.file_to_import' ) . ':') !!}
                                    {!! Form::file('employees_xlsx', ['accept'=> '.xlsx', 'required']) !!}
                                  </div>
                            </div>

                            {{-- button --}}
                            <div class="col-sm-4">
                                <button type="submit" class="btn btn-primary" id="submit_send" style="margin-top: 12px;">
                                    @lang('messages.submit')
                                </button>
                            </div>
                        </div>

                    {!! Form::close() !!}

                    {{-- Download template --}}
                    <div class="row" style="margin-top: 5px;">
                        <div class="col-sm-4">
                            <a href="
                                @if (auth()->user()->language == 'es' || auth()->user()->language == 'en')
                                    {{ asset('uploads/files/import_products_xlsx_template_' . auth()->user()->language . '.xlsx') }}
                                @else
                                    {{ asset('uploads/files/import_products_xlsx_template_en.xlsx') }}
                                @endif
                                " class="btn btn-success">
                                <i class="fa fa-download"></i>
                                @lang('accounting.download_xlsx_file_template')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   
    {{-- Instructions --}}
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-header">
                  <h3 class="box-title">@lang('lang_v1.instructions')</h3>
                </div>
                <div class="box-body">
                    <strong>@lang('lang_v1.instruction_line1')</strong><br>
                    <ol style="margin-left: 20px;">
                        <li style="list-style-type: decimal;">@lang('lang_v1.instruction_line3')</li>
                        <li style="list-style-type: decimal;">@lang('lang_v1.instruction_line4')</li>
                        <li style="list-style-type: decimal;">@lang('lang_v1.instruction_line2')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="panel with-nav-tabs panel-default boxform_u box-solid_u">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab-products" id="link-products" data-toggle="tab">
                        @lang('product.products')
                    </a>
                </li>
                <li>
                    <a href="#tab-services" id="link-services" data-toggle="tab">
                        @lang('product.services')
                    </a>
                </li>
            </ul>
        </div>

        <div class="panel-body">
            <div class="tab-content">
                {{-- Products tab --}}
                <div class="tab-pane fade in active" id="tab-products">
                    @include('import_products.partials.products_tab')
                </div>

                {{-- Services tab --}}
                <div class="tab-pane" id="tab-services">
                    @include('import_products.partials.services_tab')
                </div>
            </div>
        </div>
    </div>
</section>

@endsection