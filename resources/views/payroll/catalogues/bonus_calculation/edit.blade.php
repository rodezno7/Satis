{!! Form::open(['method' => 'put', 'id' => 'form_edit_bonus_calculation' ]) !!}
<div class="modal-header">
  <h4 class="modal-title" id="formModal">@lang('payroll.bonus_calculations_table')
    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
      <span aria-hidden="true">&times;</span>
    </button>
  </h4>
</div>

<div class="modal-body">
  <div class="row">

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('payroll.from_year')</label> <span class="text-danger">*</span>
        {!! Form::number("from", $bonusCalculation->from, ['class' => 'form-control form-control-sm', 'placeholder' => __('payroll.from'), 
        'id' => 'from', 'required'])!!}
      </div>
    </div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('payroll.until_year')</label>
        {!! Form::number("until", $bonusCalculation->until, ['class' => 'form-control form-control-sm', 'placeholder' => __('payroll.until'), 
        'id' => 'until'])!!}
      </div>
    </div>

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">
		  <label>@lang('payroll.days_to_pay')</label> <span class="text-danger">*</span>
		  {!! Form::number("days", $bonusCalculation->days, ['class' => 'form-control form-control-sm', 'placeholder' => __('payroll.days_to_pay'), 
		  'id' => 'days', 'required'])!!}
		</div>
	</div>
  
	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		  <div class="form-group">
			  <label>@lang('payroll.proportional')</label>
			  {!! Form::select('proportional', [1 => __('messages.yes'), 0 => __('messages.no')], $bonusCalculation->proportional, ['class' => 'form-control select2', 'id' => 'proportional', 'required', 'style' => 'width: 100%;' ]) !!}
		  </div>
	</div>

	<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">
			<label>@lang('rrhh.status')</label>
			{!! Form::select('status', [1 => __('rrhh.active'), 0 => __('rrhh.inactive')], $bonusCalculation->status, ['class' => 'form-control select2', 'id' => 'status', 'required', 'style' => 'width: 100%;' ]) !!}
		</div>
	</div>

  </div>
</div>
<div class="modal-footer">
  <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
  <input type="hidden" name="id" value="{{ $bonusCalculation->id }}" id="id">
  <button type="button" class="btn btn-primary" id="btn_edit_bonus_calculation">@lang('payroll.edit')</button>
  <button type="button" class="btn btn-danger" data-dismiss="modal" onClick="closeModal()">@lang( 'messages.cancel'
    )</button>
</div>
{!! Form::close() !!}
<script>
  $( document ).ready(function() {
		$.fn.modal.Constructor.prototype.enforceFocus = function() {};
		$('.select2').select2();
	});


	$("#btn_edit_bonus_calculation").click(function() {
		id = $("#id").val();
		route = "/bonus-calculation/"+id;   
		token = $("#token").val();

		var form = $("#form_edit_bonus_calculation");
		var formData = new FormData(form[0]);

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
					$("#bonus-calculation-table").DataTable().ajax.reload(null, false);
					$('#modal_edit').modal( 'hide' ).data( 'bs.modal', null );
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
					title: "@lang('payroll.error_list')",
					icon: "error",
					html: "<ul>"+ errormessages+ "</ul>",
				});
			}
		});
  	});

	function closeModal(){
		$('#modal_edit').modal( 'hide' ).data( 'bs.modal', null );
	}
</script>