{!! Form::open(['method' => 'post', 'id' => 'form_add_economic_dependence' ]) !!}
<div class="modal-header">
  <h4 class="modal-title" id="formModal">@lang('rrhh.economic_dependencies')
    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
      <span aria-hidden="true">&times;</span>
    </button>
  </h4>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('rrhh.types_relationships')</label> <span class="text-danger">*</span>
        <select name="type_relationship_id" id="type_relationship_id" class="form-control form-control-sm select2"
          placeholder="{{ __('rrhh.types_relationships') }}" style="width: 100%;">
          <option value="">{{ __('rrhh.types_relationships') }}</option>
          @foreach ($typeRelationships as $type)
            <option value="{{ $type->id }}">{{ $type->value }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('rrhh.name')</label> <span class="text-danger">*</span>
        {!! Form::text("name", null, ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.name'), 'id'
        => 'name', 'required'])!!}
      </div>
    </div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('rrhh.birthdate')</label> <span class="text-danger">*</span>
        {!! Form::text("birthdate", null, ['class' => 'form-control form-control-sm', 'id' => 'birthdate',
        'required'])!!}
      </div>
    </div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('rrhh.phone')</label> <span class="text-danger">*</span>
        {!! Form::text("phone", null,
        ['class' => 'form-control form-control-sm', 'placeholder' => '0000-0000', 'id' => 'phone']) !!}
      </div>
    </div>
  </div>

</div>
<div class="modal-footer">
  <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
  <input type="hidden" name="employee_id" value="{{ $employee_id }}" id="employee_id">
  <button type="button" class="btn btn-primary" id="btn_add_economic_dependence">@lang('rrhh.add')</button>
  <button type="button" class="btn btn-danger" data-dismiss="modal" onClick="closeModal()">@lang( 'messages.cancel'
    )</button>
</div>
{!! Form::close() !!}
<script>
  $( document ).ready(function() {
		$.fn.modal.Constructor.prototype.enforceFocus = function() {};
    select2 = $('.select2').select2();

		// $('#birthdate').datepicker({
		// 	autoclose: true,
		// 	format: datepicker_date_format,
		// });

    var fechaMaxima = new Date();
    fechaMaxima = fechaMaxima.toLocaleDateString("es-ES", { day: '2-digit', month: '2-digit', year: 'numeric' });

    var fechaMinima = new Date();
    fechaMinima.setFullYear(fechaMinima.getFullYear() - 99);
    fechaMinima = fechaMinima.toLocaleDateString("es-ES", { day: '2-digit', month: '2-digit', year: 'numeric' });

    $('#birthdate').datepicker({
      autoclose: true,
      format: datepicker_date_format,
      startDate: fechaMinima,
      endDate: fechaMaxima,
    });

    $("#birthdate").datepicker("setDate", fechaMaxima);
	});


	$("#btn_add_economic_dependence").click(function() {
		route = "/rrhh-economic-dependence";    
		token = $("#token").val();

		var form = $("#form_add_economic_dependence");
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
					getEconomicDependence($('#employee_id').val());
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