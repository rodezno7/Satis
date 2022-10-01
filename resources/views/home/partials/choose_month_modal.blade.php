<div class="modal fade" tabindex="-1" role="dialog" id="choose_month_modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
            {!! Form::open(['url' => action('HomeController@chooseMonth'), 'method' => 'post', 'id' => 'choose_month_form']) !!}
            
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
				<h4 class="modal-title">@lang('home.choose_month')</h4>
			</div>

			<div class="modal-body">
				<div class="row">
					{{-- Year --}}
					<div class="col-md-6">
				        <div class="form-group">
				            {!! Form::label('year_modal', __('lang_v1.year') . ':' ) !!}
				            <div class="input-group">
				                <span class="input-group-addon">
				                    <i class="fa fa-calendar"></i>
				                </span>
                                {!! Form::number('year_modal', date('Y'), [
                                    'id' => 'year_modal',
                                    'class' => 'form-control',
                                    'step' => 1,
                                    'min' => 2000,
                                    'max' => 3000,
                                    'required'
                                ]) !!}
				            </div>
				        </div>
				    </div>

					{{-- Month --}}
				    <div class="col-md-6">
				        <div class="form-group">
				            {!! Form::label('month_modal', __('lang_v1.month') . ':' ) !!}
				            <div class="input-group">
				                <span class="input-group-addon">
				                    <i class="fa fa-calendar"></i>
				                </span>
                                {!! Form::select('month_modal', $months, date('m'), [
                                    'id' => 'month_modal',
                                    'class' => 'form-control select2',
                                    'style' => 'width: 100%',
                                    'required'
                                ]) !!}
				            </div>
				        </div>
				    </div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="submit" class="btn btn-primary" id="choose_month_btn">@lang('messages.accept')</button>
			    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.cancel')</button>
			</div>

            {!! Form::close() !!}
		</div>
	</div>
</div>