@extends('layouts.app')
@section('title', __('diagnostic.diagnostics'))

@section('content')

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>@lang( 'diagnostic.diagnostics' )
      <small>@lang( 'diagnostic.manage_diagnostic' )</small>
    </h1>
  </section>

  <!-- Main content -->
  <section class="content">

    <div class="box">
      <div class="box-header">
        <h3 class="box-title">@lang('diagnostic.all_diagnostics')</h3>
        @can('diagnostic.create')
          <div class="box-tools">
            <button type="button" class="btn btn-block btn-primary btn-modal"
              data-href="{{ action('Optics\DiagnosticController@create') }}" data-container=".diagnostics_modal">
              <i class="fa fa-plus"></i> @lang('messages.add')
            </button>
          </div>
        @endcan
      </div>
      <div class="box-body">
        @can('diagnostic.view')
          <div class="table-responsive">
            <table class="table table-bordered table-striped" id="diagnostics_table">
              <thead>
                <tr>
                  <th>@lang('cashier.name')</th>
                  <th>@lang('messages.action')</th>
                </tr>
              </thead>
            </table>
          </div>
        @endcan
      </div>
    </div>

    <div class="modal fade diagnostics_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
  </section>
  <!-- /.content -->
@endsection

@section('javascript')
  <script>
    $(document).ready(function() {

      // Data Table
      var diagnostics_table = $('#diagnostics_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/diagnostic',
        columnDefs: [
          {
            "targets": "_all",
            "className": "text-center"
          },
          {
            "targets": [1],
            "orderable": false,
            "searchable": false
          }
        ]
      });

      // Add Form
      $(document).on('submit', 'form#diagnostic_add_form', function(e) {
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
              $('div.diagnostics_modal').modal('hide');
              Swal.fire({
                title: "" + result.msg + "",
                icon: "success",
                timer: 2000,
                showConfirmButton: false,
              });
              diagnostics_table.ajax.reload();
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
      $(document).on('click', 'button.edit_diagnostics_button', function() {
        $("div.diagnostics_modal").load($(this).data('href'), function() {
          $(this).modal('show');
          $('form#diagnostics_edit_form').submit(function(e) {
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
                  $('div.diagnostics_modal').modal('hide');
                  Swal.fire({
                    title: "" + result.msg + "",
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false,
                  });
                  diagnostics_table.ajax.reload();
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
      $(document).on('click', 'button.delete_diagnostics_button', function() {
        swal({
          title: LANG.sure,
          text: LANG.confirm_delete_diagnostic,
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
                  diagnostics_table.ajax.reload();
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
