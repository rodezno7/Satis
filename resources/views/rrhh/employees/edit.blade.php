<div class="boxform_u box-solid_u">
  <div class="box-header">
    <h3 class="box-title">{{ $employee->code }} - {{ $employee->name }} {{ $employee->last_name }}</h3>
    <div class="box-tools"><button type="button" class="btn btn-primary btn-sm" id="btn_undo">@lang( 'rrhh.back' )</button></div>
  </div>

  <div class="box-body">
    {!! Form::model($employee, ['route' => ['rrhh-employees.update', $employee->id], 'method' => 'patch', 'id' => 'form_edit']) !!}
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12">
        @include('rrhh.employees.fields')
        <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
      </div>
    </div>
  </div>

  <div class="box-footer">
    <button type="button" class="btn btn-success btn-sm" id="btn_edit_item">@lang('rrhh.update')</button>

    <button type="button" class="btn btn-primary btn-sm" id="btn_undo2">@lang( 'rrhh.back' )</button>
  </div>
</div>


<div class="modal fade" id="modal_doc" tabindex="-1">
  <div class="modal-dialog" role="document" style="width: 30%;">
    <div class="modal-content" id="modal_content_document">

    </div>
  </div>
</div>




{!! Form::close() !!}



<script type="text/javascript">

  $( document ).ready(function() {
    select2 = $('.select2').select2();
    showBankInformation();
    getPhoto();
    getDocuments();
  });

  function getDocuments() {
    id = {{ $employee->id }}
    var url = '{!!URL::to('/rrhh-documents-getByEmployee/:id')!!}';
    url = url.replace(':id', id);
    $.get(url, function(data){
      $("#div_documents").html(data);
    });
  }


  function getPhoto() {
    id = {{ $employee->id }}
    var url = '{!!URL::to('/rrhh-employees-getPhoto/:id')!!}';
    url = url.replace(':id', id);
    $.get(url, function(data){
      $("#div_photo").html(data);
    });
  }

  $("#btn_edit_item").click(function() {
    $("#btn_edit_item").prop("disabled", true);
    id = {{ $employee->id }}
    route = "/rrhh-employees/"+id;
    datastring = $("#form_edit").serialize();
    token = $("#token").val();
    $.ajax({
      url: route,
      headers: {'X-CSRF-TOKEN': token},
      type: 'PUT',
      dataType: 'json',
      data: datastring,
      success:function(result){
        if(result.success == true) {
          $("#btn_edit_item").prop("disabled", false);
          Swal.fire
          ({
            title: result.msg,
            icon: "success",
            timer: 2000,
            showConfirmButton: false,
          });

          $('#btn_undo').hide();
          $('#btn_add').show();
          var url = '{!!URL::to('/rrhh-employees-getEmployeesData')!!}';
          $.get(url, function(data){
            $("#div_content").html(data);

          });       




        } else {
          $("#btn_edit_item").prop("disabled", false);
          Swal.fire
          ({
            title: result.msg,
            icon: "error",
          });
        }
      },
      error:function(msj){
        $("#btn_edit_item").prop("disabled", false);
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

  $("#btn_undo, #btn_undo2").click(function() {
    sendRequest();
  });

  $('#btn-collapse-ci').click(function(){
    if ($("#commercial-information-fields-box").hasClass("in")) {            
      $("#create-icon-collapsed").removeClass("fa fa-minus");
      $("#create-icon-collapsed").addClass("fa fa-plus");
    }else{
      $("#create-icon-collapsed").removeClass("fa fa-plus");
      $("#create-icon-collapsed").addClass("fa fa-minus"); 
    }
  });

  $('#btn-collapse-fi').click(function(){
    if ($("#fiscal-information-fields-box").hasClass("in")) {            
      $("#create-icon-collapsed-fi").removeClass("fa fa-minus");
      $("#create-icon-collapsed-fi").addClass("fa fa-plus");
    }else{
      $("#create-icon-collapsed-fi").removeClass("fa fa-plus");
      $("#create-icon-collapsed-fi").addClass("fa fa-minus"); 
    }
  });

  $('#btn-collapse-gi').click(function(){
    if ($("#general-information-fields-box").hasClass("in")) {            
      $("#create-icon-collapsed-gi").removeClass("fa fa-minus");
      $("#create-icon-collapsed-gi").addClass("fa fa-plus");
    }else{
      $("#create-icon-collapsed-gi").removeClass("fa fa-plus");
      $("#create-icon-collapsed-gi").addClass("fa fa-minus"); 
    }
  });

  $("#btn_add_documents").click(function() {

    $("#modal_content_document").html('');
    var url = '{!!URL::to('/rrhh-documents-createDocument/:id')!!}';
    id = {{ $employee->id }}
    url = url.replace(':id', id);
    $.get(url, function(data) {
      $("#modal_content_document").html(data);
      $('#modal_doc').modal({backdrop: 'static'});
    });

  });

  function deleteDocument(id) {

    $.confirm({
      title: '@lang('rrhh.confirm_delete')',
      content: '@lang('rrhh.delete_message')',
      icon: 'fa fa-warning',
      theme: 'modern',
      closeIcon: true,
      animation: 'scale',
      type: 'red',
      buttons: {
        confirm:{
          text: '@lang('rrhh.delete')',            
          action: function()
          {
            route = '/rrhh-documents/'+id;
            token = $("#token").val();
            $.ajax({
              url: route,
              headers: {'X-CSRF-TOKEN': token},
              type: 'DELETE',
              dataType: 'json',                       
              success:function(result){
                if(result.success == true) {
                  Swal.fire
                  ({
                    title: result.msg,
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false,
                  });

                  getDocuments();

                } else {
                  Swal.fire
                  ({
                    title: result.msg,
                    icon: "error",
                  });
                }
              }
            });
          }
        },
        cancel:{
          text: '@lang('rrhh.cancel')',
        },
      }
    });
  }


</script>