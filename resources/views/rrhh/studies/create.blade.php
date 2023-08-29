{!! Form::open(['method' => 'post', 'id' => 'form_add_study' ]) !!}
<div class="modal-header">
  <h4 class="modal-title" id="formModal">@lang('rrhh.studies')
    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
      <span aria-hidden="true">&times;</span>
    </button>
  </h4>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('rrhh.types_studies')</label> <span class="text-danger">*</span>
        <select name="type_study_id" id="type_study_id" class="form-control form-control-sm select2"
          placeholder="{{ __('rrhh.types_studies') }}" style="width: 100%;">
          <option value="">{{ __('rrhh.types_studies') }}</option>
          @foreach ($typeStudies as $type)
            <option value="{{ $type->id }}">{{ $type->value }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
          <label>@lang('rrhh.institution')</label> <span class="text-danger">*</span>
          {!! Form::text('institution', null, [
              'class' => 'form-control form-control-sm',
              'placeholder' => __('rrhh.institution'),
              'id' => 'institution',
              'required',
          ]) !!}
      </div>
    </div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('rrhh.title')</label> <span class="text-danger">*</span>
        {!! Form::text("title", null, ['class' => 'form-control form-control-sm', 'placeholder' => __('rrhh.title'), 'id'
        => 'title', 'required'])!!}
      </div>
    </div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('rrhh.year_graduation')</label> <span class="text-danger">*</span>
        {!! Form::text("year_graduation", null, ['class' => 'form-control form-control-sm', 'id' => 'year_graduation',
        'required'])!!}
      </div>
    </div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('rrhh.study_status')</label> <span class="text-danger">*</span>
        <select name="study_status" id="study_status" class="form-control form-control-sm select2"
          placeholder="{{ __('rrhh.types_studies') }}" style="width: 100%;">
          <option value="en_curso">{{ __('rrhh.in_progress') }}</option>
          <option value="finalizado">{{ __('rrhh.finalized') }}</option>
        </select>
      </div>
    </div>
  </div>
</div>
<div class="modal-footer">
  <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
  <input type="hidden" name="employee_id" value="{{ $employee_id }}" id="employee_id_st">
  <button type="button" class="btn btn-primary" id="btn_add_study">@lang('rrhh.add')</button>
  <button type="button" class="btn btn-danger" data-dismiss="modal" onClick="closeModal()">@lang('messages.cancel')</button>
</div>
{!! Form::close() !!}
<script>
  $( document ).ready(function() {
		$.fn.modal.Constructor.prototype.enforceFocus = function() {};
    select2 = $('.select2').select2();
    $("#year_graduation").datepicker( {
        format: "yyyy",
        viewMode: "years", 
        minViewMode: "years"
    });
	});

	$("#btn_add_study").click(function() {
		route = "/rrhh-study";    
		token = $("#token").val();
    employee_id = $('#employee_id_st').val();

		var form = $("#form_add_study");
		var formData = new FormData(form[0]);
    formData.append('employee_id', employee_id);
		
		$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': token},
			type: 'POST',
			processData: false,
			contentType: false,       
			data: formData,
			success:function(result) {
				if(result.success == true) {
					getStudy(employee_id);
          //$('#employee_id_st').val('');
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