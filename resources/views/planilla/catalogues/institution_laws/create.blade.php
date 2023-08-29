{!! Form::open(['method' => 'post', 'id' => 'form_add_institution_law' ]) !!}
<div class="modal-header">
  <h4 class="modal-title" id="formModal">@lang('planilla.institution_laws')
    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="closeModal()">
      <span aria-hidden="true">&times;</span>
    </button>
  </h4>
</div>

<div class="modal-body">
  <div class="row">

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('planilla.name')</label> <span class="text-danger">*</span>
        {!! Form::text("name", null, ['class' => 'form-control form-control-sm', 'placeholder' => __('planilla.name'), 
        'id' => 'name', 'required'])!!}
      </div>
    </div>

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="form-group">
        <label>@lang('planilla.employeer_number')</label>
        {!! Form::text("employeer_number", null, ['class' => 'form-control form-control-sm', 'placeholder' => __('planilla.employeer_number'), 
        'id' => 'employeer_number', 'pattern' => '[0-9]+'])!!}
      </div>
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="form-group">
        <label>@lang('planilla.description')</label> <span class="text-danger">*</span>
        {!! Form::text("description", null, ['class' => 'form-control form-control-sm', 'placeholder' => __('planilla.description'), 
        'id' => 'description', 'required'])!!}
      </div>
    </div>

  </div>
</div>
<div class="modal-footer">
  <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
  <button type="button" class="btn btn-primary" id="btn_add_institution_law">@lang('planilla.add')</button>
  <button type="button" class="btn btn-danger" data-dismiss="modal" onClick="closeModal()">@lang( 'messages.cancel'
    )</button>
</div>
{!! Form::close() !!}
<script>
  $( document ).ready(function() {
		$.fn.modal.Constructor.prototype.enforceFocus = function() {};
	});


	$("#btn_add_institution_law").click(function() {
		route = "/institution-law";    
		token = $("#token").val();

		var form = $("#form_add_institution_law");
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
          $("#institution-law-table").DataTable().ajax.reload(null, false);
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