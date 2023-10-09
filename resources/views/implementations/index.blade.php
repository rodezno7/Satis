@extends('layouts.app')
@section('title', __('home.implementations'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> @lang('home.implementations')
            <small></small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="boxform_u box-solid_u">
            <div class="box-header">
                <h3 class="box-title">@lang('lang_v1.modules') </h3> @show_tooltip(__('tooltip.module_sidebar'))
                <div class="box-tools">

                </div>
            </div>
            {!! Form::open(['url' => action('ImplementationController@store'), 'method' => 'post', 'files' => true]) !!}
            <div class="box-body">
                <div class="row">
                    @if (!empty($modules))
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-sm-12">

                                    <div class="list-group form-row check_group">
                                        <div class="col-lg-4 col-md-4 col-sm-6">
                                            <div class="form-check form-check-inline">
                                                <label class="list-group-item" style="background: #67a9ff3d">
                                                    <input type="checkbox" class="check_all form-check-input me-1"> {{ __('role.select_all')}} <br>
                                                    <small style="font-weight: normal">Gestionar todos los módulos</small>
                                                </label>
                                            </div>
                                        </div>
                                        @foreach ($modules as $k => $v)
                                            <div class="col-lg-4 col-md-4 col-sm-6">
                                                <div class="form-check form-check-inline">
                                                    <label class="list-group-item">
                                                        {!! Form::checkbox('enabled_modules[]', $k, in_array($k, $enabled), ['class' => 'form-check-input me-1']) !!} {{ $v['name'] }} <br>
                                                        <small style="font-weight: normal">{{ $v['description'] }}</small>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="box-footer text-right">
                <button type="submit" class="btn btn-primary" id="btn_edit_item">@lang('rrhh.update')</button>

                <a href="{!! URL::to('/home') !!}">
                    <button id="cancel_product" type="button" class="btn btn-danger">@lang('messages.cancel')</button>
                </a>
            </div>
            {!! Form::close() !!}
        </div>
    </section>
@endsection

@section('javascript')
    <script type="text/javascript">
        // $('input[type="checkbox"]').removeClass('check_all');
    </script>
@endsection
