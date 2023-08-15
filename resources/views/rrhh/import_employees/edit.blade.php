@extends('layouts.app')

@section('title', __('rrhh.import_employees'))

@section('content')

{{-- Content Header (Page header) --}}
<section class="content-header">
    <h1> @lang('rrhh.import_employees')</h1>
</section>

{{-- Main content --}}
<section class="content">
    <div class="row">
        <div class="col-sm-12">
        	<div class="box box-solid box-default">
                <div class="box-body">
                    {!! Form::open([
                        'url' => action('RrhhImportEmployeesController@checkEditFile'),
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
                                    @lang('messages.validate_file')
                                </button>
                            </div>
                        </div>

                    {!! Form::close() !!}

                    <hr>
                    {{-- Download template --}}
                    <div class="row">
                        <div class="col-sm-4">
                            <a href="
                                @if (auth()->user()->language == 'es' || auth()->user()->language == 'en')
                                    {{ asset('uploads/files/import_employees_xlsx_template_' . auth()->user()->language . '.xlsx') }}
                                @else
                                    {{ asset('uploads/files/import_employees_xlsx_template_en.xlsx') }}
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

    @if (isset($flag) && isset($exception))
        {{-- Errors --}}
        @if (! $flag)
            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-solid box-danger">
                        <div class="box-header">
                            <h3 class="box-title">@lang('accounting.errors')</h3>
                        </div>

                        <div class="box-body">
                            <table class="table table-condensed table-striped table-text-center table-th-gray" id="errors_table">
                                <thead>
                                    <tr>
                                        <th width="15%">@lang('purchase.row_number')</th>
                                        <th width="60%">@lang('accounting.description')</th>
                                    </tr>
                                </thead>
                                @foreach ($errors as $error)
                                    <tr>
                                        <td>{{ $error['row'] }}</td>
                                        <td>{{ $error['msg'] }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        {{-- Process employee --}}
        @else
            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-solid box-success">
                        <div class="box-header">
                            <h3 class="box-title">@lang('customer.import_data')</h3>
                        </div>

                        <div class="box-body">
                            <h4>{{ __('rrhh.message_file_validated') }}</h4>
                            {!! Form::open([
                                'url' => action('RrhhImportEmployeesController@update'),
                                'method' => 'post',
                                'enctype' => 'multipart/form-data',
                                'id' => 'import_form'
                            ]) !!}
                            <div class="col-sm-12 text-center">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <button type="submit" class="btn btn-success" id="submit_import">
                                        @lang('purchase.import')
                                    </button>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

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
                        <li style="list-style-type: decimal;">@lang('lang_v1.instruction_line5')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">@lang('rrhh.column_information')
            <div class="panel-tools pull-right">
                <button type="button" class="btn btn-panel-tool" data-toggle="collapse"
                    data-target="#general-information-fields-box" id="btn-collapse-gi">
                    <i class="fa fa-minus" id="create-icon-collapsed-gi"></i>
                </button>
            </div>
        </div>

        <div class="panel-body collapse in" id="general-information-fields-box" aria-expanded="true">
            @include('rrhh.import_employees.table')
        </div>
    </div>
</section>

@endsection

@section('javascript')
<script type="text/javascript">
    $('#btn-collapse-gi').click(function(){
        if ($("#general-information-fields-box").hasClass("in")) {            
            $("#create-icon-collapsed-gi").removeClass("fa fa-minus");
            $("#create-icon-collapsed-gi").addClass("fa fa-plus");
        }else{
            $("#create-icon-collapsed-gi").removeClass("fa fa-plus");
            $("#create-icon-collapsed-gi").addClass("fa fa-minus"); 
        }
    });
</script>
@endsection