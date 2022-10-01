@extends('layouts.app')
@section('title', __('graduation_card.graduation_cards'))

@section('content')

  <style>
    table.vt-align tbody tr td,
    table.vt-align tbody tr th, {
      vertical-align: middle;
    }
  </style>

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>@lang( 'graduation_card.graduation_cards' )
      <small>@lang( 'graduation_card.manage_graduation_cards' )</small>
    </h1>
  </section>

  <!-- Main content -->
  <section class="content">

    <div class="box">
      <div class="box-header">
        <h3 class="box-title">@lang('graduation_card.all_graduation_cards')</h3>
        @can('graduation_card.create')
          <div class="box-tools">
            <button type="button" class="btn btn-block btn-primary btn-modal"
              data-href="{{ action('Optics\GraduationCardController@create') }}" data-container=".graduation_cards_modal">
              <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
          </div>
        @endcan
      </div>
      <div class="box-body">
        @can('graduation_card.view')
          <div class="table-responsive">
            <table class="table table-bordered table-striped" id="graduation_cards_table">
              <thead>
                <tr>
                  <th>@lang('graduation_card.patient')</th>
                  <th>@lang('messages.action')</th>
                </tr>
              </thead>
            </table>
          </div>
        @endcan
      </div>
    </div>

    <div class="modal fade graduation_cards_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

  </section>
  <!-- /.content -->

@endsection


@section('javascript')
  <script>
    $(document).ready(function() {
      $('.di-mask').mask('00/00');

      $.fn.modal.Constructor.prototype.enforceFocus = function() {};
      
      $('.graduation_cards_modal').on('shown.bs.modal', function () {
        $(this).find('.select2').select2();
      });
    
      // Data Table
      var graduation_cards_table = $('#graduation_cards_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/graduation-cards',
        columnDefs: [{
          "targets": [1],
          "orderable": false,
          "searchable": false
        }]
      });

      // Add Form
      $(document).on('submit', 'form#graduation_card_add_form', function(e) {
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
              $('div.graduation_cards_modal').modal('hide');
              Swal.fire({
                title: "" + result.msg + "",
                icon: "success",
              });
              graduation_cards_table.ajax.reload();
            } else {
              Swal.fire({
                title: "" + result.msg + "",
                icon: "error",
              });
            }
          }
        });
      });

      // Edit Form
      $(document).on('click', 'button.edit_graduation_cards_button', function() {
        $("div.graduation_cards_modal").load($(this).data('href'), function() {
          $(this).modal('show');
          $('form#graduation_cards_edit_form').submit(function(e) {
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
                  $('div.graduation_cards_modal').modal('hide');
                  Swal.fire({
                    title: "" + result.msg + "",
                    icon: "success",
                  });
                  graduation_cards_table.ajax.reload();
                } else {
                  Swal.fire({
                    title: "" + result.msg + "",
                    icon: "error",
                  });
                }
              }
            });
          });
        });
      });

      // Delete
      $(document).on('click', 'button.delete_graduation_cards_button', function() {
        swal({
          title: LANG.sure,
          text: LANG.confirm_delete_graduation_card,
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
                  });
                  graduation_cards_table.ajax.reload();
                } else {
                  Swal.fire({
                    title: "" + result.msg + "",
                    icon: "error",
                  });
                }
              }
            });
          }
        });
      });
    });

    // Block optemetrist field
    function optometristBlock() {
      if ($('#is_prescription').is(':checked')) {
        $('#optometrist').val('0');
        $('#optometrist').prop('disabled', true);
      } else {
        $('#optometrist').prop('disabled', false);
        $('#optometrist').val('');
      }
    }

    // Balance for right eye
    function balanceOD() {
      if ($('#balance_od').is(':checked')) {
        $('input[name="sphere_od"]').val('');
        $('input[name="sphere_od"]').prop('readonly', true);

        $('input[name="cylindir_od"]').val('');
        $('input[name="cylindir_od"]').prop('readonly', true);

        $('input[name="axis_od"]').val('');
        $('input[name="axis_od"]').prop('readonly', true);

        $('input[name="base_od"]').val('');
        $('input[name="base_od"]').prop('readonly', true);

        $('input[name="addition_od"]').val('');
        $('input[name="addition_od"]').prop('readonly', true);
      } else {
        $('input[name="sphere_od"]').val('');
        $('input[name="sphere_od"]').prop('readonly', false);

        $('input[name="cylindir_od"]').val('');
        $('input[name="cylindir_od"]').prop('readonly', false);

        $('input[name="axis_od"]').val('');
        $('input[name="axis_od"]').prop('readonly', false);

        $('input[name="base_od"]').val('');
        $('input[name="base_od"]').prop('readonly', false);

        $('input[name="addition_od"]').val('');
        $('input[name="addition_od"]').prop('readonly', false);
      }
    }

    // Balance for left eye
    function balanceOS() {
      if ($('#balance_os').is(':checked')) {
        $('input[name="sphere_os"]').val('');
        $('input[name="sphere_os"]').prop('readonly', true);

        $('input[name="cylindir_os"]').val('');
        $('input[name="cylindir_os"]').prop('readonly', true);

        $('input[name="axis_os"]').val('');
        $('input[name="axis_os"]').prop('readonly', true);

        $('input[name="base_os"]').val('');
        $('input[name="base_os"]').prop('readonly', true);

        $('input[name="addition_os"]').val('');
        $('input[name="addition_os"]').prop('readonly', true);
      } else {
        $('input[name="sphere_os"]').val('');
        $('input[name="sphere_os"]').prop('readonly', false);

        $('input[name="cylindir_os"]').val('');
        $('input[name="cylindir_os"]').prop('readonly', false);

        $('input[name="axis_os"]').val('');
        $('input[name="axis_os"]').prop('readonly', false);

        $('input[name="base_os"]').val('');
        $('input[name="base_os"]').prop('readonly', false);

        $('input[name="addition_os"]').val('');
        $('input[name="addition_os"]').prop('readonly', false);
      }
    }
  </script>
@endsection
