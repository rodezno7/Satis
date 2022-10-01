@extends('layouts.app')

@section('title', __('Nombrar imágenes'))

@section('content')
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>@lang('Nombrar imágenes')
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
            {!! Form::open([
                'url' => action('Optics\ProductController@postNameImages'),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            ]) !!}
            <div class="row">
              <div class="col-sm-6">
                <div class="col-sm-8">
                  <div class="form-group">
                    {!! Form::label('name', __('product.file_to_import') . ':') !!}
                    {!! Form::file('file_csv', ['accept' => '.csv']) !!}
                  </div>
                </div>
                <div class="col-sm-4">
                  <br>
                  <button type="submit" class="btn btn-primary">@lang('messages.submit')</button>
                </div>
              </div>
            </div>
            {!! Form::close() !!}
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /.content -->
@endsection
