@extends('layouts.app')
@section('title', __('material_type.material_types'))

@section('content')

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>@lang( 'material_type.material_types' )
      <small>@lang( 'material_type.manage_material_type' )</small>
    </h1>
  </section>

  <!-- Main content -->
  <section class="content">

    <div class="box">
      <div class="box-header">
        <h3 class="box-title">@lang('material_type.all_material_types')</h3>
        @can('material_type.create')
          <div class="box-tools">
            <button type="button" class="btn btn-block btn-primary btn-modal"
              data-href="{{ action('Optics\MaterialTypeController@create') }}" data-container=".material_types_modal">
              <i class="fa fa-plus"></i> @lang('messages.add')
            </button>
          </div>
        @endcan
      </div>
      <div class="box-body">
        @can('material_type.view')
          <div class="table-responsive">
            <table class="table table-bordered table-striped" id="material_types_table">
              <thead>
                <tr>
                  <th>@lang('cashier.name')</th>
                  <th>@lang('accounting.description')</th>
                  <th>@lang('messages.action')</th>
                </tr>
              </thead>
            </table>
          </div>
        @endcan
      </div>
    </div>

    <div class="modal fade material_types_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
  </section>
  <!-- /.content -->
@endsection

@section('javascript')
  <script>
    $(document).ready(function() {

      // Data Table
      var material_types_table = $('#material_types_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/material_type',
        columnDefs: [
          {
            "targets": "_all",
            "className": "text-center"
          },
          {
            "targets": [2],
            "orderable": false,
            "searchable": false
          }
        ]
      });

      // Add Form
      $(document).on('submit', 'form#material_type_add_form', function(e) {
        e.preventDefault();
        $(this).find('button[type="submit"]').attr('disabled', true);
        var data = $(this).serialize();

        $.ajax({
          method: "POST",
          url: $(this).attr("action"),
          dataType: "json",
          data: data,
          success: function(result) {
            if (result.success === true) {
              $('div.material_types_modal').modal('hide');
              Swal.fire({
                title: "" + result.msg + "",
                icon: "success",
                timer: 2000,
                showConfirmButton: false,
              });
              material_types_table.ajax.reload();
            } else {
              Swal.fire({
                title: "" + result.msg + "",
                icon: "error",
                timer: 2000,
                showConfirmButton: false,
              });
            }
          }
        });
      });

      // Edit Form
      $(document).on('click', 'button.edit_material_types_button', function() {
        $("div.material_types_modal").load($(this).data('href'), function() {
          $(this).modal('show');
          $('form#material_types_edit_form').submit(function(e) {
            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', true);
            var data = $(this).serialize();
            $.ajax({
              method: "POST",
              url: $(this).attr("action"),
              dataType: "json",
              data: data,
              success: function(result) {
                if (result.success == true) {
                  $('div.material_types_modal').modal('hide');
                  Swal.fire({
                    title: "" + result.msg + "",
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false,
                  });
                  material_types_table.ajax.reload();
                } else {
                  Swal.fire({
                    title: "" + result.msg + "",
                    icon: "error",
                    timer: 2000,
                    showConfirmButton: false,
                  });
                }
              }
            });
          });
        });
      });

      // Delete
      $(document).on('click', 'button.delete_material_types_button', function() {
        swal({
          title: LANG.sure,
          text: LANG.confirm_delete_material_type,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
          if (willDelete) {
            var href = $(this).data('href');
            var data = $(this).serialize();

            $.ajax({
              method: "DELETE",
              url: href,
              dataType: "json",
              data: data,
              success: function(result) {
                if (result.success === true) {
                  Swal.fire({
                    title: "" + result.msg + "",
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false,
                  });
                  material_types_table.ajax.reload();
                } else {
                  Swal.fire({
                    title: "" + result.msg + "",
                    icon: "error",
                    timer: 2000,
                    showConfirmButton: false,
                  });
                }
              }
            });
          }
        });
      });
    });

  </script>
@endsection
