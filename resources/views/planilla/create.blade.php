{!! Form::open(['method' => 'post', 'id' => 'form_add_planilla' ]) !!}
<div class="modal-header">
  <h4 class="modal-title" id="formModal">@lang('planilla.planilla')
    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
      <span aria-hidden="true">&times;</span>
    </button>
  </h4>
</div>

<div class="modal-body">
  <div class="row">

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">
		  <label>@lang('planilla.year')</label> <span class="text-danger">*</span>
		  {!! Form::text("year", null, ['class' => 'form-control form-control-sm', 'id' => 'year', 'required'])!!}
		</div>
	</div>

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">
		  <label>@lang('planilla.month')</label> <span class="text-danger">*</span>
		  <select name="month" id="month" class="form-control form-control-sm select2" 
				placeholder="{{ __('planilla.payment_periods') }}" style="width: 100%;">
				<option value="1">{{ __('planilla.january') }}</option>
				<option value="2">{{ __('planilla.febrary') }}</option>
				<option value="3">{{ __('planilla.march') }}</option>
				<option value="4">{{ __('planilla.april') }}</option>
				<option value="5">{{ __('planilla.may') }}</option>
				<option value="6">{{ __('planilla.june') }}</option>
				<option value="7">{{ __('planilla.july') }}</option>
				<option value="8">{{ __('planilla.august') }}</option>
				<option value="9">{{ __('planilla.september') }}</option>
				<option value="10">{{ __('planilla.october') }}</option>
				<option value="11">{{ __('planilla.november') }}</option>
				<option value="12">{{ __('planilla.december') }}</option>
			</select>
		</div>
	</div>

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">
			<label>@lang('planilla.payment_period')</label> <span class="text-danger">*</span>
			<select name="payment_period_id" id="payment_period_id" class="form-control form-control-sm select2" 
				placeholder="{{ __('planilla.payment_periods') }}" style="width: 100%;">
				<option value="">{{ __('planilla.payment_period') }}</option>
				@foreach ($paymentPeriods as $paymentPeriod)
					<option value="{{ $paymentPeriod->id }}">{{ $paymentPeriod->name }}</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">
			<label>@lang('planilla.calculation_types')</label> <span class="text-danger">*</span>
			<select name="calculation_type_id" id="calculation_type_id" class="form-control form-control-sm select2" 
				placeholder="{{ __('planilla.calculation_types') }}" style="width: 100%;">
				@foreach ($calculationTypes as $calculationType)
					<option value="{{ $calculationType->id }}">{{ $calculationType->name }}</option>
				@endforeach
			</select>
		</div>
	</div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('planilla.start_date')</label> <span class="text-danger">*</span>
        {!! Form::number("start_date", null, ['class' => 'form-control form-control-sm', 'placeholder' => __('planilla.start_date'), 
        'id' => 'start_date', 'readonly' => 'readonly'])!!}
      </div>
    </div>

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">
		  <label>@lang('planilla.end_date')</label> <span class="text-danger">*</span>
		  {!! Form::number("end_date", null, ['class' => 'form-control form-control-sm', 'placeholder' => __('planilla.end_date'), 
		  'id' => 'end_date', 'readonly' => 'readonly'])!!}
		</div>
	</div>
  
	  <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">
		  <label>@lang('planilla.days')</label>
		  {!! Form::number("days", null, ['class' => 'form-control form-control-sm', 'placeholder' => __('planilla.days'), 
		  'id' => 'days', 'readonly' => 'readonly'])!!}
		</div>
	  </div>	  

  </div>
</div>
<div class="modal-footer">
  <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
  <button type="button" class="btn btn-primary" id="btn_add_planilla">@lang('planilla.add')</button>
  <button type="button" class="btn btn-danger" data-dismiss="modal" onClick="closeModal()">@lang( 'messages.cancel'
    )</button>
</div>
{!! Form::close() !!}
<script>
  $( document ).ready(function() {
		$.fn.modal.Constructor.prototype.enforceFocus = function() {};
		$('.select2').select2();

		$("#year").datepicker( {
			format: "yyyy",
			viewMode: "years", 
			minViewMode: "years"
		});
	});


	$("#btn_add_planilla").click(function() {
		route = "/planilla";    
		token = $("#token").val();

		var form = $("#form_add_planilla");
		var formData = new FormData(form[0]);
		console.log(formData);
		$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': token},
			type: 'POST',
			processData: false,
			contentType: false,       
			data: formData,
			success:function(result) {
				if(result.success == true) {
					Swal.fire
					({
						title: result.msg,
						icon: "success",
						timer: 1000,
						showConfirmButton: false,
					});
					$("#planilla-table").DataTable().ajax.reload(null, false);
					$('#modal_add').modal( 'hide' ).data( 'bs.modal', null );
				}
				else {
					Swal.fire
					({
						title: result.msg,
						icon: "error",
					});
				}
			},
			error:function(msj){
				errormessages = "";
				$.each(msj.responseJSON.errors, function(i, field){
					errormessages+="<li>"+field+"</li>";
				});
				Swal.fire
				({
					title: "@lang('planilla.error_list')",
					icon: "error",
					html: "<ul>"+ errormessages+ "</ul>",
				});
			}
		});
  });

	function closeModal(){
		$('#modal_add').modal( 'hide' ).data( 'bs.modal', null );
	}
</script>