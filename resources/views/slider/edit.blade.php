<div class="modal-dialog modal-md" role="document">
    <div class="modal-content" style="border-radius: 20px;">
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">@lang('carrousel.edit_image')</h4>
            </div>
            <div class="modal-body">
                {!! Form::open([
                    'url' => action('SliderController@update', [$image->id]),
                    'method' => 'PATCH',
                    'id' => 'image_update_form'
                ]) !!}
                <div class="row">
                    <div class="form-group col-xs-12">
                        <label>@lang('carrousel.description')</label>
                        {!! Form::text('description', $image->description, ['class' => 'form-control', 'placeholder' => __('carrousel.description')]) !!}
                    </div>
                    <div class="form-group col-xs-12">
                        <label>@lang('carrousel.start_date')</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" placeholder ="{{ __('carrousel.start_date')}}" value="{{$image->start_date}}">
                    </div>
                    <div class="form-group col-xs-12">
                        <label>@lang('carrousel.end_date')</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" placeholder ="{{ __('carrousel.end_date')}}" value="{{$image->end_date}}">
                    </div>
                    <div class="form-group col-xs-12">
                        <label>@lang('carrousel.link')</label>
                        {!! Form::text('slide_link', $image->link, ['class' => 'form-control', 'placeholder' => __('carrousel.link')]) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
