{!! Form::open(['method' => 'post', 'id' => 'form_add_absence_inability' ]) !!}
<div class="modal-header">
  <h4 class="modal-title" id="formModal">@lang('rrhh.absence_inability')
    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
      <span aria-hidden="true">&times;</span>
    </button>
  </h4>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('rrhh.option')</label> <span class="text-danger">*</span>
        <select name="type" id="type" class="form-control form-control-sm">
          <option value="1">Ausencia</option>
          <option value="2">Incapacidad</option>
        </select>
      </div>
    </div>
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12" id="div_type_inability">
      <div class="form-group">
        <label>@lang('rrhh.types_inabilities')</label> <span class="text-danger">*</span>
        <select name="type_inability_id" id="type_inability_id" class="form-control form-control-sm select2"
          placeholder="{{ __('rrhh.types_inabilities') }}" style="width: 100%;">
          <option value="">{{ __('rrhh.types_inabilities') }}</option>
          @foreach ($typeInabilities as $type)
            <option value="{{ $type->id }}">{{ $type->value }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12" id="div_type_absence">
      <div class="form-group">
        <label>@lang('rrhh.types_absences')</label> <span class="text-danger">*</span>
        <select name="type_absence_id" id="type_absence_id" class="form-control form-control-sm select2"
          placeholder="{{ __('rrhh.types_absences') }}" style="width: 100%;">
          <option value="">{{ __('rrhh.types_absences') }}</option>
          @foreach ($typeAbsences as $type)
            <option value="{{ $type->id }}">{{ $type->value }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('rrhh.start_date')</label> <span class="text-danger">*</span>
        {!! Form::text("start_date", null, ['class' => 'form-control form-control-sm', 'id' => 'start_date',
        'required'])!!}
      </div>
    </div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12" id="div_end_date">
      <div class="form-group">
        <label>@lang('rrhh.end_date')</label> <span class="text-danger">*</span>
        {!! Form::text("end_date", null, ['class' => 'form-control form-control-sm', 'id' => 'end_date',
        'required'])!!}
      </div>
    </div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12" id="div_amount">
      <div class="form-group">
        <label>@lang('rrhh.amount')</label> <span class="text-danger">*</span>
        {!! Form::number("amount", null,
        ['class' => 'form-control form-control-sm', 'placeholder' =>  __('rrhh.amount'), 'id' => 'amount']) !!}
      </div>
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="form-group">
        <label>@lang('rrhh.description')</label> <span class="text-danger">*</span>
        {!! Form::textarea('description', null, ['id' => 'description', 'placeholder' => __('rrhh.description'), 'class' => 'form-control', 'rows' => 4]);
        !!}
      </div>
    </div>
  </div>

</div>
<div class="modal-footer">
  <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
  <input type="hidden" name="employee_id" value="{{ $employee_id }}" id="employee_id">
  <button type="button" class="btn btn-primary" id="btn_add_absence_inability">@lang('rrhh.add')</button>
  <button type="button" class="btn btn-danger" data-dismiss="modal" onClick="closeModal()">@lang( 'messages.cancel' )</button>
</div>
{!! Form::close() !!}

<script>
  $( document ).ready(function() {
    console.log($('#type').val());
    //$('#type option:eq(1)').attr('selected', 'selected')

		$.fn.modal.Constructor.prototype.enforceFocus = function() {};
    select2 = $('.select2').select2();

    $('#start_date').datepicker({
      autoclose: true,
      format: datepicker_date_format,
    });

    $('#end_date').datepicker({
      autoclose: true,
      format: datepicker_date_format,
    });

    typeOption();
	});

  $('#type').on('change', function() {
		typeOption();
	});

  function typeOption(){
    let type = $('#type').val();

    $('#div_amount').hide();
    $("#amount").prop('required', false);

    $('#div_end_date').hide();
		$("#end_date").prop('required', false);

    $('#div_type_inability').hide();
		$("#type_inability_id").prop('required', false);

    $('#div_type_absence').hide();
    $("#type_absence_id").prop('required', false);
		
    //Evaluando si la accion de personal requiere autorizacion
		if (type == 1) { //Ausencia
			$('#div_amount').show();
			$("#amount").prop('required', true);

      $('#div_type_absence').show();
      $("#type_absence_id").prop('required', true);
		}else{//Incapacidad
			$('#div_end_date').show();
			$("#end_date").prop('required', true);

      $('#div_type_inability').show();
      $("#type_inability_id").prop('required', true);
		}

  }

	$("#btn_add_absence_inability").click(function() {
		route = "/rrhh-absence-inability";    
		token = $("#token").val();
    employee_id = $('#employee_id').val();

		var form = $("#form_add_absence_inability");
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
					getAbsenceInability(employee_id);
          $('#employee_id').val('');
					Swal.fire
					({
						title: result.msg,
						icon: "success",
						timer: 1000,
						showConfirmButton: false,
					});
					$('#modal_doc').modal( 'hide' ).data( 'bs.modal', null );
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
					title: "@lang('rrhh.error_list')",
					icon: "error",
					html: "<ul>"+ errormessages+ "</ul>",
				});
			}
		});
  });

	function closeModal(){
		$('#modal_action').modal({backdrop: 'static'});
		$('#modal_doc').modal( 'hide' ).data( 'bs.modal', null );
	}
</script>