<div class="modal-dialog modal-md" role="document">
    <div class="modal-content" style="border-radius: 20px;">
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">@lang('carrousel.add_image')</h4>
            </div>
            <div class="modal-body">
                {!! Form::open([
                    'url' => action('SliderController@store'),
                    'method' => 'post',
                    'id' => 'image_add_form',
                    'files' => true,
                ]) !!}
                <div class="row">
                    <div class="form-group col-xs-12">
                        <label>@lang('carrousel.image')</label>&nbsp;<span class="text-danger">*</span><br>
                        <small class="text-danger">Max: 5MB | Dimensiones: 1500 x 300 px max. | Formatos:
                            PNG, JPG, JPEG</small>
                        <input type="file" name="image_slide" id="image_slide" class="form-control w-100"
                            required>
                        @if ($errors->has('image_slide'))
                            <div class="invalid-feedback text-danger" role="alert">
                                {{ $errors->first('image_slide') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-xs-12">
                        <label>@lang('carrousel.description')</label>
                        {!! Form::text('description', null, ['class' => 'form-control', 'placeholder' => __('carrousel.description')]) !!}
                    </div>
                    <div class="form-group col-xs-12">
                        <label>@lang('carrousel.start_date')</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" placeholder ="{{ __('carrousel.start_date')}}">
                    </div>
                    <div class="form-group col-xs-12">
                        <label>@lang('carrousel.end_date')</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" placeholder ="{{ __('carrousel.end_date')}}">
                    </div>
                    <div class="form-group col-xs-12">
                        <label>@lang('carrousel.link')</label>
                        {!! Form::text('slide_link', null, ['class' => 'form-control', 'placeholder' => __('carrousel.link')]) !!}
                    </div>
                </div>
                <div class="modal-footer" style="border-top-color: white;">
                    <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
