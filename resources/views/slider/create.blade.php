@extends('layouts.app')
@section('title', __('carrousel.add_image'))
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>@lang('carrousel.add_image')</h1>
</section>
<!-- Main content -->
<section class="content">
	{!! Form::open(['url' => action('SliderController@store'), 'method' => 'post', 'id' => 'image_add_form', 'files' => true]) !!}
    <div class="boxform_u box-solid_u">
        <div class="box-body">
            <div class="row">
                <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>@lang('carrousel.image')</label>
                    <input type="file" name="image_slide" id="image_slide" class="form-control w-100" required>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                        <a href="{{url('slider')}}" class="btn btn-default">@lang('messages.close')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</section>
<!-- /.content -->
@endsection