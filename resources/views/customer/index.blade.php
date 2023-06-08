@extends('layouts.app')

@section('title', __('customer.customers'))

<style>
  hr {
    margin-top: 1rem;
    margin-bottom: 1rem;
    border: 2;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
  }

  i.edit-glyphicon {
    position: inherit;
    line-height: inherit;
  }

  .select2-container--open {
    z-index: 1061;
  }
</style>

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang('customer.customers' )</h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="boxform_u box-solid_u">

    <div class="box-header" id="header_customer">
      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
          <div class="form-group">
            <label for="">@lang('customer.customer_portfolio')</label>
            {!! Form::select("customer_portfolio_id", $customer_portfolios, null,
            ["class" => "form-control select2", "id" => "portfolio_id", "placeholder" => __('customer.all_customers')])
            !!}
          </div>
        </div>
      </div>
      <div class="box-tools">
        <button type="button" class="btn btn-block btn-primary btn-modal"
          data-href="{{ action('CustomerController@create') }}" data-container=".customer_modal" data-backdrop="static"
          id="btn-new-customer">
          <i class="fa fa-plus"></i> @lang('messages.add')
        </button>

        <button type="button" class="btn btn-block btn-success btn-modal"
          data-href="{{ action('CustomerController@export') }}" id="btn-report">
          <i class="fa fa-download"></i>&nbsp; @lang('report.export')
        </button>
      </div>
    </div>

    <div class="box-header" id="header_follow" style="display: none;">
      <h3 class="box-title">@lang('crm.customer')</h3>
      <div class="box-tools">
        <button type="button" class="btn btn-block btn-primary" id="back">
          @lang('crm.back')
        </button>
      </div>
    </div>

    {{-- Index datatable --}}
    <div class="box-body">
      <div id="div_customers">
        <div class="table-responsive" style="margin-top: 5px;">
          <table id="customers-table" class="table table-striped table-condensed table-hover table-text-center"
            width="100%" style="font-size: inherit;">
            <thead>
              <th>@lang('customer.name')</th>
              <th>@lang('customer.business_name')</th>
              <th>@lang('business.dui')</th>
              <th>@lang('business.nit')</th>
              <th>@lang('business.nrc')</th>
              <th>@lang('customer.phone')</th>
              <th class="text-center">@lang('messages.actions')</th>
            </thead>
          </table>
        </div>
      </div>

      {{-- View detail --}}
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="div_follows" style="display: none;">
        <div class="row" style="margin-top: 5px;">
          <div class="box box-default" id="customer_details_box">
            <div class="box-header with-border">
              <h3 class="box-title">@lang('crm.customer_details')</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"
                    id="icon-collapsed"></i>
                </button>
              </div>
              <!-- /.box-tools -->
            </div>

            <!-- /.box-header -->
            <div class="box-body" id="customer_details_box_body">
              <div class="col-sm-4">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <i class="fa fa-folder"></i> &nbsp;<strong>@lang('crm.general')</strong>
                  </div>
                  <div class="panel-body">
                    <strong>@lang('customer.name')</strong>
                    <p class="text-muted" id="lbl_name"></p>

                    <strong>@lang('customer.portfolios')</strong>
                    <p class="text-muted" id="lbl_customer_portfolio_id"></p>

                    <strong>@lang('customer.customer_group')</strong>
                    <p class="text-muted" id="lbl_customer_group_id"></p>

                    <strong>@lang('customer.allowed_credit')</strong>
                    <p class="text-muted" id="lbl_allowed_credit"></p>

                    <strong>@lang('customer.opening_balance')</strong>
                    <p class="text-muted" id="lbl_opening_balance"></p>

                    <strong>@lang('customer.credit_limit')</strong>
                    <p class="text-muted" id="lbl_credit_limit"></p>

                    <strong>@lang('customer.credit_balance')</strong>
                    <p class="text-muted" id="lbl_credit_balance"></p>

                    <strong>@lang('customer.payment_terms')</strong>
                    <p class="text-muted" id="lbl_payment_terms_id"></p>
                  </div>
                </div>
              </div>

              <div class="col-sm-4">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <i class="fa fa-phone"></i> &nbsp;<strong>@lang('crm.contact')</strong>
                  </div>
                  <div class="panel-body">
                    <strong>@lang('customer.contact_mode')</strong>
                    <p class="text-muted" id="lbl_contact_mode_id"></p>

                    <strong>@lang('crm.email')</strong>
                    <p class="text-muted" id="lbl_email"></p>

                    <strong>@lang('customer.phone')</strong>
                    <p class="text-muted" id="lbl_telphone"></p>

                    <strong>@lang('customer.address')</strong>
                    <p class="text-muted" id="lbl_address"></p>

                    <strong>@lang('customer.country')</strong>
                    <p class="text-muted" id="lbl_country"></p>

                    <strong>@lang('customer.state')</strong>
                    <p class="text-muted" id="lbl_state"></p>

                    <strong>@lang('customer.city')</strong>
                    <p class="text-muted" id="lbl_city"></p>

                    <strong>@lang('customer.zone')</strong>
                    <p class="text-muted" id="lbl_zone"></p>

                    <strong>@lang('customer.latitude')</strong>
                    <p class="text-muted" id="lbl_latitude"></p>

                    <strong>@lang('customer.length')</strong>
                    <p class="text-muted" id="lbl_length"></p>
                  </div>
                </div>
              </div>

              <div class="col-sm-4">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <i class="fa fa-user"></i> &nbsp;<strong>@lang('business.business')</strong>
                  </div>
                  <div class="panel-body">
                    <strong>@lang('customer.business_name')</strong>
                    <p class="text-muted" id="lbl_business_name"></p>

                    <strong>@lang('customer.business_line')</strong>
                    <p class="text-muted" id="lbl_business_line"></p>

                    <strong>@lang('customer.business_type')</strong>
                    <p class="text-muted" id="lbl_business_type"></p>

                    <strong>@lang('customer.dui')</strong>
                    <p class="text-muted" id="lbl_dni"></p>

                    <strong>@lang('customer.is_taxpayer')</strong>
                    <p class="text-muted" id="lbl_is_taxpayer"></p>

                    <strong>@lang('customer.reg_number')</strong>
                    <p class="text-muted" id="lbl_reg_number"></p>

                    <strong>@lang('customer.tax_number')</strong>
                    <p class="text-muted" id="lbl_tax_number"></p>

                  </div>
                </div>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>

        <div class="row">
          <div class="panel panel-default">
            <div class="panel-heading">Contactos del cliente</div>
            <div class="panel-body">
              <table id="example" class="display" style="width:100%">
                <thead>
                  <tr>
                    <th>Nombre</th>
                    <th>Telefono</th>
                    <th>Linea Fija</th>
                    <th>Correo</th>
                    <th>Cargo</th>
                  </tr>
                </thead>
                <tbody id="DataResult">

                </tbody>
              </table>

            </div>
          </div>
        </div>


        <div class="row">
          <h4>@lang('crm.manage_follows')</h4>
        </div>

        <div class="row">

          <div class="form-group float-right col-lg-11 col-md-11 col-sm-11 col-xs-12">
            <input type="hidden" name="c_id" id="c_id">
            <input type="hidden" name="c_name" id="c_name">
          </div>

          <div class="form-group float-right col-lg-1 col-md-1 col-sm-1 col-xs-12">

            <button type="button" class="btn btn-block btn-primary" id="btn_add_follow">
              @lang('messages.add')
            </button>
          </div>
        </div>

        <div class="row">

          <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover" id="follow_customers_table"
              width="100%">
              <thead>
                <tr>
                  <th>@lang('crm.date')</th>
                  <th>@lang('crm.contact_type')</th>
                  <th>@lang('crm.contactreason')</th>
                  <th>@lang('crm.contact_mode')</th>
                  <th>@lang('customer.customer')</th>
                  <th>@lang('crm.register_by')</th>
                  <th>@lang('messages.actions')</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Modals --}}
  <div tabindex="-1" class="modal fade customer_modal" role="dialog" aria-labelledby="exampleModalCenterTitle" id="modalCustomer1"
    aria-hidden="true"></div>

  <div class="modal fade" id="modal_add_follow" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    @include('customer.follow_customers.create')
  </div>

  <div class="modal fade" id="modal_edit_follow" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    @include('customer.follow_customers.edit')
  </div>

  <div class="modal fade" id="modal_view_follow" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    @include('customer.follow_customers.show')
  </div>

  <div class="modal fade" id="modal_contact" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    @include('customer.follow_customers.edit')
  </div>

  <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

  <div class="modal fade edit_payment_modal" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="gridSystemModalLabel"></div>

</section>
<!-- /.content -->
@endsection

@section('javascript')
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>

{{-- FileSaver --}}
<script src="{{ asset('plugins/filesaver/FileSaver.min.js?v=' . $asset_v) }}"></script>

{{-- SheetJs --}}
<script src="{{ asset('plugins/sheetjs/xlsx.full.min.js?v=' . $asset_v) }}"></script>

{{-- jsPDF --}}
<script src="{{ asset('plugins/jspdf/jspdf.min.js?v=' . $asset_v) }}"></script>

{{-- jsPDF-AutoTable --}}
<script src="{{ asset('plugins/jspdf-autotable/jspdf-autotable.min.js?v=' . $asset_v) }}"></script>

<script type="text/javascript">
  function addReference() {
    var newtr1 = newtr1 + '<tr><input name="contactid[]" type="hidden" value="0">';
    newtr1 = newtr1 + '<td><input class="form-control input-sm" name="contactname[]" value="" required /></td>';
    newtr1 = newtr1 + '<td><input class="form-control input-sm input_number" name="contactphone[]" value="" required /></td>';  
    newtr1 = newtr1 + '<td><input class="form-control input-sm input_number" name="contactlandline[]" value="" required /></td>';
    newtr1 = newtr1 + '<td><input type="email" class="form-control input-sm" name="contactemail[]" value="" required /></td>';
    newtr1 = newtr1 + '<td><input class="form-control input-sm" name="contactcargo[]"  value="" required /></td>';
    newtr1 = newtr1 + '<td><button type="button" class="btn btn-danger btn-xs remove-item"><i class="fa fa-times"></i></button></td></tr>';

    $('#referencesItems').append(newtr1); //Agrego el contacto al tbody de la Tabla con el id=ProSelected
    $('#dele').addClass("show");

    
  }

  $(document).ready(function() {
    $(document).on('click', '.remove-item', function (e) {
      Swal.fire({
        title: LANG.sure,
        text: '{{ __('messages.delete_content') }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "{{ __('messages.accept') }}",
        cancelButtonText: "{{ __('messages.cancel') }}"
      }).then((willDelete) => {
        if (willDelete.isConfirmed) {
          $(this).parent('td').parent('tr').slideDown(300, function() {
              $(this).remove(); //En accion elimino el contacto de la Tabla
          });
        }
      });
    });
  });
  

function loadContacts(id){
  var route = "/customers/get_contacts/" + id; // show
    
      $.ajax({
          url: route,
          dataType: 'JSON',
          method: "get"
        }).done(function(data) {
          var html = '';
          var i;
         for (i = 0; i < data.length; i++) {
           html += '<tr>' +
             '<td>' + data[i].name + '</td>' +
             '<td>' + data[i].phone + '</td>' +
             '<td>' + data[i].landline + '</td>' +
             '<td>' + data[i].email + '</td>' +
             '<td>' + data[i].cargo + '</td>' +
             '</tr>';
         }
         $('#DataResult').html(html);
        });
    
}
  /* CUSTOMERS */
  $.fn.modal.Constructor.prototype.enforceFocus = function() {};

  $(document).ready(function() {
    loadCustomersData();
    $.fn.dataTable.ext.errMode = 'none';
    $(document).on("change", "select#portfolio_id", function(){
      $("table#customers-table").DataTable().ajax.reload();
    });

    // On click of btn-report button
    $(document).on('click', '#btn-report', function (e) {
      e.preventDefault();

      $('#btn-report').prop('disabled', true);

      $.ajax({
        method: 'post',
        url: $('#btn-report').data('href'),
        dataType: 'json',
        data: { format: 'excel' },
        success: function (result) {
          if (result.success === true) {
            if (result.type === 'pdf') {
              export_pdf(result.data, result.header_data, result.headers);
            } else {
              export_excel(result.data);
            }

            $('#btn-report').prop('disabled', false);
              
          } else {
            $('#btn-report').prop('disabled', false);

            Swal.fire({
              title: result.msg,
              icon: 'error',
            });
          }
        }
      });
    });
  });

  function loadCustomersData() {
    var customer_table = $("#customers-table").DataTable({
      pageLength: 25,
        //deferRender: true,
        processing: true,
        serverSide: true,
        ajax: {
          url: "/customers/getCustomersData",
          data: function(d){
            d.portfolio_id = $("select#portfolio_id").val();
          }
        },
        columns: [
        { data: 'name' },
        { data: 'business_name' },
        { data: 'dni' },
        { data: 'tax_number' },
        { data: 'reg_number' },
        { data: 'telphone' },
        { data: 'actions',className: 'text-center', orderable: false, searchable: false }
        ]
      });

      /*
      $('#customers-table').on('dblclick', 'tr', function() {
        var data = customer_table.row(this).data();
        if (typeof data.id != "undefined") {
          var url = '{!!  URL::to(' / customers /: id ') !!}';
          url = url.replace(':id', data.id);
          $("div.customer_modal").load(url, function() {
            $(this).modal({
              backdrop: 'static'
            });
          });
        }
      });
      */

      $('#customers-table').on('dblclick', 'tr', function () {
        var data = customer_table.row(this).data();
        if (typeof data.id != "undefined") {
          viewCustomer(data.id);
        }
      });
  }

    function showTaxPayer() {
      if ($("#is_taxpayer").is(":checked")) {
        $('#div_taxpayer').show();
        $("#reg_number").val('');
        $("#tax_number").val('');
        $("#business_line").val('');
        setTimeout(function() {
          $('#reg_number').focus();
        },
        800);
      } else {
        $('#div_taxpayer').hide();
        $("#reg_number").val('');
        $("#tax_number").val('');
        $("#business_line").val('');
      }
    }

    function showCredit() {
      if ($("#allowed_credit").is(":checked")) {
        $('#div_credit').show();
        $("#opening_balance").val('');
        $("#credit_limit").val('');
        $("#payment_terms_id").val('').change();
        setTimeout(function() {
          $('#opening_balance').focus();
        },
        800);
      } else {
        $('#div_credit').hide();
        $("#opening_balance").val('');
        $("#credit_limit").val('');
        $("#payment_terms_id").val('').change();
      }
    }

    function getStatesByCountry(id) {
      $("#state_id").empty();
      var route = "/states/getStatesByCountry/" + id;
      $.get(route, function(res) {
        $("#state_id").append('<option value="0" disabled selected>@lang('messages.please_select')</option>');
        $(res).each(function(key, value) {
          $("#state_id").append('<option value="' + value.id + '">' + value.name + '</option>');
        });
      });
    }

    function getCitiesByState(id) {
      $("#city_id").empty();
      var route = "/cities/getCitiesByState/" + id;
      $.get(route, function(res) {
        $("#city_id").append('<option value="0" disabled selected>@lang('messages.please_select')</option>');
        $(res).each(function(key, value) {
          $("#city_id").append('<option value="' + value.id + '">' + value.name + '</option>');
        });
      });
    }

    $(document).on('change', '#country_id', function(e) {
      id = $("#country_id").val();
      if (id) {
        getStatesByCountry(id);
      } else {
        $("#state_id").empty();
        $("#state_id").append('<option value="0" disabled selected>@lang('messages.please_select')</option>');
        $("#city_id").empty();
        $("#city_id").append('<option value="0" disabled selected>@lang('messages.please_select')</option>');
      }
    });

    $(document).on('change', '#state_id', function(e) {
      id = $("#state_id").val();
      if (id) {
        getCitiesByState(id);
      } else {
        $("#city_id").empty();
        $("#city_id").append('<option value="0" disabled selected>@lang('messages.please_select')</option>');
      }
    });

    $(document).on('submit', 'form#form-add-customer', function(e) {
      e.preventDefault();
      $("#btn-add-customer").prop('disabled', true);
      $("#btn-close-modal-add-customer").prop('disabled', true);
      var data = $("#form-add-customer").serialize();
      route = "/customers";
      token = $("#token").val();
      $.ajax({
        url: route,
        headers: {
          'X-CSRF-TOKEN': token
        },
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function(result) {
          if (result.success == true) {
            $("#btn-add-customer").prop('disabled', false);
            $("#btn-close-modal-add-customer").prop('disabled', false);
            $('div.customer_modal').modal('hide');
            $("#customers-table").DataTable().ajax.reload(null, false);
            Swal.fire({
              title: result.msg,
              icon: "success",
              timer: 3000,
              showConfirmButton: false,
            });
          } else {
            $("#btn-add-customer").prop('disabled', false);
            $("#btn-close-modal-add-customer").prop('disabled', false);
            Swal.fire({
              title: result.msg,
              icon: "error",
            });
          }
        },
        error: function(msj) {
          $("#btn-add-customer").prop('disabled', false);
          $("#btn-close-modal-add-customer").prop('disabled', false);
          var errormessages = "";
          $.each(msj.responseJSON.errors, function(i, field) {
            errormessages += "<li>" + field + "</li>";
          });
          Swal.fire({
            title: "{{ __('customer.errors') }}",
            icon: "error",
            html: "<ul>" + errormessages + "</ul>",
          });
        }
      });
    });

    // $(document).on('submit', 'form#form-edit-customer', function(e) {
    //   e.preventDefault();
    //   $("#btn-edit-customer").prop('disabled', true);
    //   $("#btn-close-modal-edit-customer").prop('disabled', true);
    //   var data = $(this).serialize();
    //   id = $("#customer_id").val();
    //   route = "/customers/" + id;
    //   token = $("#token").val();
    //   $.ajax({
    //     url: route,
    //     headers: {
    //       'X-CSRF-TOKEN': token
    //     },
    //     type: 'PUT',
    //     dataType: 'json',
    //     data: data,
    //     success: function(result) {
    //       if (result.success == true) {
    //         $("#btn-edit-customer").prop('disabled', false);
    //         $("#btn-close-modal-edit-customer").prop('disabled', false);
    //         $("#customers-table").DataTable().ajax.reload();
    //         $('div.customer_modal').modal('hide');

    //         Swal.fire({
    //           title: result.msg,
    //           icon: "success",
    //           timer: 3000,
    //           showConfirmButton: false,
    //         });
    //       } else {
    //         $("#btn-edit-customer").prop('disabled', false);
    //         $("#btn-close-modal-edit-customer").prop('disabled', false);
    //         Swal.fire({
    //           title: result.msg,
    //           icon: "error",
    //         });
    //       }
    //     },
    //     error: function(msj) {
    //       $("#btn-edit-customer").prop('disabled', false);
    //       $("#btn-close-modal-edit-customer").prop('disabled', false);
    //       var errormessages = "";
    //       $.each(msj.responseJSON.errors, function(i, field) {
    //         errormessages += "<li>" + field + "</li>";
    //       });
    //       Swal.fire({
    //         title: "{{ __('customer.errors') }}",
    //         icon: "error",
    //         html: "<ul>" + errormessages + "</ul>",
    //       });
    //     }
    //   });
    // });

    $(document).on('click', 'a.contact_button', function() {
      $("div.customer_modal").load($(this).data('href'), function() {
        $(this).modal({
          backdrop: 'static'
        });
      });
    });

    $(document).on('click', 'a.view_customer_button', function() {
      $("div.customer_modal").load($(this).data('href'), function() {
        $(this).modal({
          backdrop: 'static'
        });
      });
    });

    $('.customer_modal').on('shown.bs.modal', function() {
      $('#country_id').select2();
      $('#state_id').select2();
      $('#city_id').select2();
      $('#business_type_id').select2();
      $('#customer_portfolio_id').select2();
      $('#customer_group_id').select2();
      $('#payment_terms_id').select2();
      $('#contact_mode_id').select2();
      $('#first_purchase_location').select2();
      $('select.select2').select2();

      check_foreign_customer();

      $(document).on('click', '#is_foreign', function () {
        check_foreign_customer();
      });
    })

    function check_foreign_customer() {
			if ($('#is_foreign').is(':checked')) {
        $('.check-foreign').text('{{ __('accounting.document') }}');
        $('#dni').unmask();
				$('#dni').attr('placeholder', '{{ __('sale.passport_residence_card') }}');

			} else {
        $('.check-foreign').text('{{ __('customer.dui') }}');
        $('#dni').mask("00000000-0");
				$('#dni').attr('placeholder', '{{ __('customer.dui') }}');
			}
		}

    // function saveContact(){
		//   var id =  $("input#customer_id").val();
    //   var count_contact = $('input#count_contact').val();
    //   var cont_fila = $('#customer_table >tbody >tr').length;
    //   var route = "/customers/store_contacts/"+id;
    //     var respuesta = $.post(route, $( "#form_add_contact" ).serialize());
    //     console.log(respuesta);
    //     // if(count_contact == 0 && cont_fila == 0){
    //     //     $('#modalCustomer1').modal('hide');
    //     // }else if(count_contact == 0 && cont_fila == 1){
    //     //   var contactname = $('input#1').val();
    //     //   console.log(contactname);
    //     // }
    //     // else{
    //     //     toastr.success("{{ __('customer.contact_added_success') }}");
    //     //     $('#modalCustomer1').modal('hide');
    //     // }
    // }

    function deleteCustomer(id) {
      Swal.fire({
        title: LANG.sure,
        text: "{{ __('messages.delete_content') }}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "{{ __('messages.accept') }}",
        cancelButtonText: "{{ __('messages.cancel') }}"
      }).then((willDelete) => {
        if (willDelete.value) {
          route = '/customers/' + id;
          $.ajax({
            url: route,
            type: 'DELETE',
            dataType: 'json',
            success: function(result) {
              if (result.success == true) {
                Swal.fire({
                  title: result.msg,
                  icon: "success",
                  timer: 3000,
                  showConfirmButton: false,
                });
                $("#customers-table").DataTable().ajax.reload(null, false);
              } else {
                Swal.fire({
                  title: result.msg,
                  icon: "error",
                });
              }
            }
          });
        }
      });
    }

// On click of btn-add-vehicle button
$(document).on('click', '#btn-add-vehicle', function () {
  $(this).prop('disabled', true);
  addVehicle();
  $(this).prop('disabled', false);
});

// On click of remove_vehicle_row button
$(document).on('click', '.remove_vehicle_row', function () {
  swal({
    title: LANG.sure,
    icon: 'warning',
    buttons: true,
    dangerMode: true,
  }).then((value) => {
    if (value) {
      $(this).closest('tr').remove();
    }
  });
});

function addVehicle() {
  let row_count = $('#row_count_veh').val();

  $.ajax({
    method: 'post',
    url: '/get-vehicle-row',
    dataType: 'html',
    data: {
      row_count: row_count,
    },
    success: function (res) {
      $(res).find('.vehicle_license_plate').each(function () {
        let row = $(this).closest('tr');
        $('#customer-vehicles tbody').append(row);
        $('.vehicle_brand_id').select2();
      });

      if ($(res).find('.vehicle_license_plate').length) {
        $('#row_count_veh').val($(res).find('.vehicle_license_plate').length + parseInt(row_count));
      }
    }
  });
}

/**
 * Get report in Excel format.
 * 
 * @param  array  data
 * @return void
*/
function export_excel(data) {
  var wb = XLSX.utils.book_new();

  wb.Props = {
    Title: LANG.customer_list
  };

  wb.SheetNames.push(LANG.customer_list);

  var ws_data = data;
  var ws = XLSX.utils.aoa_to_sheet(ws_data);

  wb.Sheets[LANG.customer_list] = ws;

  var wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'binary' });

  saveAs(new Blob([s2ab(wbout)], { type: "application/octet-stream" }), LANG.customer_list + '.xlsx');
}

/**
 * Convert string to array buffer.
 * 
 * @param  workbook  s
 * @return void
*/
function s2ab(s) {
  var buf = new ArrayBuffer(s.length);
  var view = new Uint8Array(buf);
  for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
  return buf;
}

/**
 * Get report in PDF format.
 * 
 * @param  array  data
 * @param  array  header_data
 * @param  array  headers
 * @return void
*/
function export_pdf(data, header_data, headers) {
  window.jsPDF = window.jspdf.jsPDF;

  var doc = new jsPDF('l', 'mm', 'a4');

  doc.setFontSize(8);
  doc.text(header_data.business_name, doc.internal.pageSize.getWidth() / 2, 14, null, null, 'center');
  doc.text(header_data.report_name, doc.internal.pageSize.getWidth() / 2, 20, null, null, 'center');

  doc.autoTable({
    styles: { fontSize: 7 },
    head: headers,
    body: data,
    startY: 24,
    theme: 'plain'
  });

  doc.save(LANG.customer_list + '.pdf');
}

/* CUSTOMERS */

/* FOLLOW CUSTOMERS */
function viewCustomer(id) {
      $("#div_customers").hide();
      $("#header_customer").hide();
      //$("#date-filter").hide();
      $("#header_follow").show();
      $("#div_follows").show();

      var route = "/customers/" + id; // show
      $.get(route, function(res) {        

        $("#c_id").val(res.id);
        $("#c_name").val(res.name);


        if (res.name != null) {
          name = res.name;
        } else {
          name = "N/A";
        }

        if (res.business_name != null) {
          business_name = res.business_name;
        } else {
          business_name = "N/A";
        }

        if (res.email != null) {
          email = res.email;
        } else {
          email = "N/A";
        }

        if (res.telphone != null) {
          telphone = res.telphone;
        } else {
          telphone = "N/A";
        }

        if (res.dni != null) {
          dni = res.dni;
        } else {
          dni = "N/A";
        }

        if (res.is_taxpayer != null) {
          if (res.is_taxpayer == 1) {
            is_taxpayer = "{{ __('accounting.yes') }}";
          } else {
            is_taxpayer = "{{ __('accounting.not') }}";
          }
        } else {
          is_taxpayer = "N/A";
        }

        if (res.reg_number != null) {
          reg_number = res.reg_number;
        } else {
          reg_number = "N/A";
        }

        if (res.tax_number != null) {
          tax_number = res.tax_number;
        } else {
          tax_number = "N/A";
        }

        if (res.business_line != null) {
          business_line = res.business_line;
        } else {
          business_line = "N/A";
        }

        if (res.business_type_value != null) {
          business_type_value = res.business_type_value;
        } else {
          business_type_value = "N/A";
        }

        if (res.customer_portfolio_value != null) {
          customer_portfolio_value = res.customer_portfolio_value;
        } else {
          customer_portfolio_value = "N/A";
        }

        if (res.customer_group_value != null) {
          customer_group_value = res.customer_group_value;
        } else {
          customer_group_value = "N/A";
        }

        if (res.address != null) {
          address = res.address;
        } else {
          address = "N/A";
        }

        if (res.country_value != null) {
          country_value = res.country_value;
        } else {
          country_value = "N/A";
        }

        if (res.state_value != null) {
          state_value = res.state_value;
        } else {
          state_value = "N/A";
        }

        if (res.city_value != null) {
          city_value = res.city_value;
        } else {
          city_value = "N/A";
        }

        if (res.zone_value != null) {
          zone_value = res.zone_value;
        } else {
          zone_value = "N/A";
        }

        if (res.allowed_credit != null) {
          if (res.allowed_credit == 1) {
            allowed_credit = "{{ __('accounting.yes') }}";
          } else {
            allowed_credit = "{{ __('accounting.not') }}";
          }
        } else {
          allowed_credit = "N/A";
        }

        if (res.opening_balance != null) {
          opening_balance = res.opening_balance;
        } else {
          opening_balance = "N/A";
        }

        if (res.credit_limit != null) {
          credit_limit = res.credit_limit;
        } else {
          credit_limit = "N/A";
        }

        if (res.credit_balance != null) {
          credit_balance = res.credit_balance;
        } else {
          credit_balance = "N/A";
        }

        if (res.payment_terms_value != null) {
          payment_terms_value = res.payment_terms_value;
        } else {
          payment_terms_value = "N/A";
        }

        if (res.contact_mode_value != null) {
          contact_mode_value = res.contact_mode_value;
        } else {
          contact_mode_value = "N/A";
        }

        if (res.location_value != null) {
          location_value = res.location_value;
        } else {
          location_value = "N/A";
        }

        if(res.latitude){
        $("#lbl_latitude").text("" + res.latitude + "");
        }
        if(res.length){
          $("#lbl_length").text("" + res.length + "");
        }

        $("#lbl_name").text("" + name + "");
        $("#lbl_business_name").text("" + business_name + "");
        $("#lbl_email").text("" + email + "");
        $("#lbl_telphone").text("" + telphone + "");
        $("#lbl_dni").text("" + dni + "");
        $("#lbl_is_taxpayer").text("" + is_taxpayer + "");
        $("#lbl_reg_number").text("" + reg_number + "");
        $("#lbl_tax_number").text("" + tax_number + "");
        $("#lbl_business_line").text("" + business_line + "");
        $("#lbl_business_type").text("" + business_type_value + "");
        $("#lbl_customer_portfolio_id").text("" + customer_portfolio_value + "");
        $("#lbl_customer_group_id").text("" + customer_group_value + "");
        $("#lbl_address").text("" + address + "");
        $("#lbl_country").text("" + country_value + "");
        $("#lbl_state").text("" + state_value + "");
        $("#lbl_city").text("" + city_value + "");
        $("#lbl_zone").text("" + zone_value + "");
        $("#lbl_allowed_credit").text("" + allowed_credit + "");
        $("#lbl_opening_balance").text("" + opening_balance + "");
        $("#lbl_credit_limit").text("" + credit_limit + "");
        $("#lbl_credit_balance").text("" + credit_balance + "");
        $("#lbl_payment_terms_id").text("" + payment_terms_value + "");
        $("#lbl_contact_mode_id").text("" + contact_mode_value + "");        
        loadFollows(id);
        loadContacts(id);
      });
}

function getContactsCustomer(){
    var d = '<tr>'+
            '<th>ID</th>'+
            '<th>Nombres</th>'+
            '<th>Apellidos</th>'+
            '</tr>';
    
}
function loadFollows(id) {
  var follows_table = $("#follow_customers_table").DataTable();
  follows_table.destroy();
  var follows_table = $('#follow_customers_table').DataTable({
    order: [
    [0, 'asc']
    ],
    processing: true,
    serverSide: true,
    ajax: '/follow-customers/getFollowsByCustomer/' + id + '',
    columns: [
    { data: 'date' },
    { data: 'contact_type' },
    { data: 'reason' },
    { data: 'mode' },
    { data: 'customer' },
    { data: 'name_register' },
    {
      data: null,
      render: function(data) {
        view_button = ' <a class="btn btn-xs btn-info action_buttons" onClick="viewFollow(' + data.id + ')"><i class="glyphicon glyphicon-eye-open edit-glyphicon"></i></a>';

        if (data.register_by == '{{ auth()->user()->id }}') {
          edit_button = ' <a class="btn btn-xs btn-primary action_buttons" onClick="editFollow(' + data.id + ')"><i class="glyphicon glyphicon-edit edit-glyphicon"></i></a>';
          delete_button = ' <a class="btn btn-xs btn-danger action_buttons" onClick="deleteFollow(' + data.id + ')"><i class="glyphicon glyphicon-trash edit-glyphicon"></i></a>';
          return view_button + edit_button + delete_button;
        } else {
          return view_button;
        }
      },
      orderable: false,
      searchable: false
    }
    ],
    columnDefs: [{
      "targets": '_all',
      "className": "text-center"
    }]
  });
}

function editFollow(id) {
  $(".action_buttons").prop('disabled', true);
  var route = "/follow-customers/" + id + "/edit";
  $.get(route, function(res) {
    $('#eelist').empty();
    econt = 0;
    eproduct_ids = [];
    erowCont = [];

    $("#follow_customer_id").val(res.id);
    $("#eecontact_type").val(res.contact_type).change();
    $("#eecontact_reason_id").val(res.contact_reason_id).change();
    $("#econtact_mode_id").val(res.contact_mode_id).change();
    $("#eenotes").val(res.notes);
    $("#edate").val(res.date);
    $("#eelocations").val(0).change();

    if (res.product_not_found == 1) {
      $("#eechk_not_found").prop('checked', true);
      $("#eeproduct_cat_id").val('').change();
      $("#eeproduct_cat_id").prop('disabled', true);

      $("#eeproducts_not_found_desc").show();
      $("#eeproducts_not_found_desc").val(res.products_not_found_desc);
    } else {
      $("#eeproduct_cat_id").prop('disabled', false);
      $("#eeproduct_cat_id").val(res.product_cat_id).change();

      $("#eechk_not_found").prop('checked', false);
      $("#eeproducts_not_found_desc").hide();
      $("#eeproducts_not_found_desc").val('');
    }

    if (res.product_not_stock == 1) {
      $("#eechk_not_stock").prop('checked', true)
      $("#eediv_products").show();

      var route = "/follow-customers/getProductsByFollowCustomer/" + id;
      $.get(route, function(res) {
        $(res).each(function(key, value) {
          if (value.sku != value.sub_sku) {
            name = "" + value.name_product + " " + value.name_variation + "";
          } else {
            name = value.name_product;
          }
          eproduct_ids.push(value.variation_id);
          erowCont.push(econt);
          var erow = '<tr class="selected" id="erow' + econt +
          '" style="height: 10px"><td><button id="bitem' + econt +
          '" type="button" class="btn btn-danger btn-xs" onclick="edeleteProduct(' + econt + ', ' +
          value.variation_id +
          ');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="variation_id[]" value="' +
          value.variation_id + '">' + name + '</td><td>' + value.sku +
          '</td><td><input type="text" name="quantity[]" id="quantity' + econt +
          '" class="form-control form-control-sm" value="' + value.quantity +
          '" readonly></td><td><input type="number" id="required_quantity' + econt +
          '" name="required_quantity[' + econt +
          ']" class="form-control form-control-sm" min=1 value="' + value.required_quantity +
          '" required></td></tr>';
          $("#eelist").append(erow);
          econt++;
        });
      });

    } else {
      $("#eechk_not_stock").prop('checked', false)
      $("#eediv_products").hide();
    }

    $("#modal_edit_follow").modal({
      backdrop: 'static'
    });
    $(".action_buttons").prop('disabled', false);
  });
}

function viewFollow(id) {
  $(".action_buttons").prop('disabled', true);
  var route = "/follow-customers/" + id;

  $.get(route, function(res) {
    if (res.reason != null) {
      $("#contact_reason_lbl").text(res.reason);
    } else {
      $("#contact_reason_lbl").text("N/A");
    }

    if (res.mode != null) {
      $("#contact_mode_lbl").text(res.reason);
    } else {
      $("#contact_mode_lbl").text("N/A");
    }

    $("#contact_type_lbl").text(res.contact_type);
    $("#notes_lbl").text(res.notes);
    $("#lbl_customer").text(res.customer);

    $("#date_lbl").text(res.date);

    $('#eeelist').empty();

    if (res.product_not_stock == 1) {
      $("#eeediv_products").show();
      if (res.products_not_found_desc != null) {
        $("#interest_lbl").text(res.products_not_found_desc);
      } else {
        $("#interest_lbl").text('N/A');
      }

      var route = "/follow-customers/getProductsByFollowCustomer/" + id;

      $.get(route, function(res) {
        $(res).each(function(key, value) {
          if (value.sku != value.sub_sku) {
            name = "" + value.name_product + " " + value.name_variation + "";
          } else {
            name = value.name_product;
          }

          var erow = '<tr class="selected" id="erow' + econt + '" style="height: 10px"><td>' + name +
          '</td><td>' + value.sku + '</td><td>' + value.quantity + '</td><td>' + value.required_quantity + '</td></tr>';

          $("#eeelist").append(erow);
        });
      });
    } else {
      if (res.category != null) {
        $("#interest_lbl").text(res.category);
      } else {
        $("#interest_lbl").text("N/A");
      }

      $("#eeediv_products").hide();
    }

    $("#modal_view_follow").modal({
      backdrop: 'static'
    });

    $(".action_buttons").prop('disabled', false);
  });
}

$(document).on('submit', 'form#follow_customer_add_form', function(e) {
  e.preventDefault();
  $("#btn-add-follow").prop('disabled', true);
  $("#btn-close-modal-add-follow").prop('disabled', true);
  var data = $(this).serialize();
  $.ajax({
    method: "POST",
    url: $(this).attr("action"),
    datatype: "json",
    data: data,
    success: function(result) {
      if (result.success == true) {
        $("#btn-add-follow").prop('disabled', false);
        $("#btn-close-modal-add-follow").prop('disabled', false);
        //clear();
        $('#modal_add_follow').modal('hide');

        Swal.fire({
          title: result.msg,
          icon: "success",
          timer: 3000,
          showConfirmButton: false,
        });
        $("#follow_customers_table").DataTable().ajax.reload();
      } else {
        $("#btn-add-follow").prop('disabled', false);
        $("#btn-close-modal-add-follow").prop('disabled', false);
        Swal.fire({
          title: result.msg,
          icon: "error",
        });
      }
    }
  });
});

$("#btn-edit-follow-customer").click(function() {
  $("#btn-edit-follow-customer").prop('disabled', true);
  $("#btn-close-modal-edit-follow").prop('disabled', true);
  var data = $("#follow_customer_edit_form").serialize();
  var id = $("#follow_customer_id").val();
  route = "/follow-customers/" + id;
  token = $("#token").val();

  $.ajax({
    url: route,
    headers: {
      'X-CSRF-TOKEN': token
    },
    type: 'PUT',
    dataType: 'json',
    data: data,
    success: function(result) {
      if (result.success == true) {
        $("#btn-edit-follow-customer").prop('disabled', false);
        $("#btn-close-modal-edit-follow").prop('disabled', false);
        $("#follow_customers_table").DataTable().ajax.reload();
        Swal.fire({
          title: result.msg,
          icon: "success",
          timer: 3000,
          showConfirmButton: false,
        });
        $("#modal_edit_follow").modal('hide');
      } else {
        $("#btn-edit-follow-customer").prop('disabled', false);
        $("#btn-close-modal-edit-follow").prop('disabled', false);
        Swal.fire({
          title: result.msg,
          icon: "error",
        });
      }
    }
  });
});

function createFollowCustomer(id, name) {
  $("#modal_add_follow").modal({ backdrop: 'static' });
  $("#customer_id_follow").val(id);
  $("#customer_name").text("" + name + ": ");

  
  clear();
  
}

function clear() {
 $('#list').empty();
 cont = 0;
 product_ids = [];
 rowCont = [];
 $("#contact_type").val('entrante').change();
 $("#contact_reason_id").val('').change();
 $("#contact_mode_id").val('').change();
 $("#product_cat_id").val('').change();
 $("#chk_not_found").prop('checked', false);
 $("#chk_not_stock").prop('checked', false);
 $("#products_not_found_desc").val('');
 $("#products_not_found_desc").hide();
 $("#div_products").hide();
 $("#product_cat_id").prop('disabled', false);
 $("#locations").val(0).change();
 $("#products").val(0).change();
 $("#notes").val('');
}

function showNotFoundDesc() {
  if ($("#chk_not_found").is(":checked")) {
    $('#products_not_found_desc').show();
    $("#product_cat_id").val('').change();
    $("#product_cat_id").prop('disabled', true);
  } else {
    $('#products_not_found_desc').hide();
    $('#products_not_found_desc').val('');
    $("#product_cat_id").prop('disabled', false);
  }
}

function eshowNotFoundDesc() {
  if ($("#echk_not_found").is(":checked")) {
    $('#eproducts_not_found_desc').show();
    $("#eproduct_cat_id").val('').change();
    $("#eproduct_cat_id").prop('disabled', true);
  } else {
    $('#eproducts_not_found_desc').hide();
    $('#eproducts_not_found_desc').val('');
    $("#eproduct_cat_id").prop('disabled', false);
  }
}

function eeshowNotFoundDesc() {
  if ($("#eechk_not_found").is(":checked")) {
    $('#eeproducts_not_found_desc').show();
    $("#eeproduct_cat_id").val('').change();
    $("#eeproduct_cat_id").prop('disabled', true);
  } else {
    $('#eeproducts_not_found_desc').hide();
    $('#eeproducts_not_found_desc').val('');
    $("#eeproduct_cat_id").prop('disabled', false);
  }
}

function showNotStockDesc() {
  if ($("#chk_not_stock").is(":checked")) {
    $('#div_products').show();
  } else {
    $('#div_products').hide();
    $('#list').empty();
    cont = 0;
    product_ids = [];
    rowCont = [];
  }
}

function eshowNotStockDesc() {
  if ($("#eechk_not_stock").is(":checked")) {
    $('#eediv_products').show();
  } else {
    $('#eediv_products').hide();
    $('#eelist').empty();
    econt = 0;
    eproduct_ids = [];
    erowCont = [];
  }
}

function deleteFollow(id) {
  Swal.fire({
    title: LANG.sure,
    text: '{{ __('messages.delete_content ') }}',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: "{{ __('messages.accept') }}",
    cancelButtonText: "{{ __('messages.cancel') }}"
  }).then((willDelete) => {
    if (willDelete.value) {
      route = '/follow-customers/' + id;
      $.ajax({
        url: route,
        type: 'DELETE',
        dataType: 'json',
        success: function(result) {
          if (result.success == true) {
            Swal.fire({
              title: result.msg,
              icon: "success",
              timer: 3000,
              showConfirmButton: false,
            });
            $("#follow_customers_table").DataTable().ajax.reload(null, false);
          } else {
            Swal.fire({
              title: result.msg,
              icon: "error",
            });
          }
        }
      });
    }
  });
}

$(document).on('change', 'select#products', function(event) {
  id = $("#products").val();
  if (id != 0) {
    addProduct();

    $("#products").val(0).change();
  }
});

$(document).on('change', 'select#eeproducts', function(event) {
  id = $("#eeproducts").val();
  if (id != 0) {
    eeaddProduct();
    $("#eeproducts").val(0).change();
  }
});

var cont = 0;
var product_ids = [];
var rowCont = [];

var econt = 0;
var eproduct_ids = [];
var erowCont = [];

function addProduct() {
  location_id = $("#locations").val();
  if (location_id != null) {
    var route = "/products/showStock/" + id + "/" + location_id;
    $.get(route, function(res) {
      variation_id = res.variation_id;
      product_id = res.product_id;
      if (res.sku == res.sub_sku) {
        name = res.name_product;
      } else {
        name = "" + res.name_product + " " + res.name_variation + "";
      }

      if (res.quantity != null) {
        quantity = res.quantity;
      } else {
        quantity = 0;
      }

      count = parseInt(jQuery.inArray(variation_id, product_ids));
      if (count >= 0) {
        Swal.fire({
          title: "{{ __('product.product_already_added') }}",
          icon: "error",
        });
      } else {
        product_ids.push(variation_id);
        rowCont.push(cont);
        var row = '<tr class="selected" id="row' + cont + '" style="height: 10px"><td><button id="bitem' + cont +
        '" type="button" class="btn btn-danger btn-xs" onclick="deleteProduct(' + cont + ', ' + variation_id +
        ');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="variation_id[]" value="' +
        variation_id + '">' + name + '</td><td>' + res.sku +
        '</td><td><input type="text" name="quantity[]" id="quantity' + cont +
        '" class="form-control form-control-sm" value="' + quantity +
        '" readonly></td><td><input type="number" id="required_quantity' + cont + '" name="required_quantity[' +
        cont + ']" class="form-control form-control-sm" min=1 value="1" required></td></tr>';
        $("#list").append(row);
        cont++;
      }
    });
  } else {
    Swal.fire({
      title: "{{ __('crm.select_location') }}",
      icon: "error",
    });
  }
}

function eeaddProduct() {
  location_id = $("#eelocations").val();
  if (location_id != null) {
    var route = "/products/showStock/" + id + "/" + location_id;
    $.get(route, function(res) {
      variation_id = res.variation_id;
      product_id = res.product_id;
      if (res.sku == res.sub_sku) {
        name = res.name_product;
      } else {
        name = "" + res.name_product + " " + res.name_variation + "";
      }

      if (res.quantity != null) {
        quantity = res.quantity;
      } else {
        quantity = 0;
      }

      count = parseInt(jQuery.inArray(variation_id, eproduct_ids));

      if (count >= 0) {
        Swal.fire({
          title: "{{ __('product.product_already_added') }}",
          icon: "error",
        });
      } else {
        eproduct_ids.push(variation_id);
        erowCont.push(econt);
        var erow = '<tr class="selected" id="erow' + econt + '" style="height: 10px"><td><button id="bitem' +
        econt + '" type="button" class="btn btn-danger btn-xs" onclick="edeleteProduct(' + econt + ', ' +
        variation_id +
        ');"><i class="fa fa-times"></i></button></td><td><input type="hidden" name="variation_id[]" value="' +
        variation_id + '">' + name + '</td><td>' + res.sku +
        '</td><td><input type="text" name="quantity[]" id="quantity' + econt +
        '" class="form-control form-control-sm" value="' + quantity +
        '" readonly></td><td><input type="number" id="required_quantity' + econt +
        '" name="required_quantity[' + econt +
        ']" class="form-control form-control-sm" min=1 value="1" required></td></tr>';
        $("#eelist").append(erow);
        econt++;
      }
    });
  } else {
    Swal.fire({
      title: "{{ __('crm.select_location') }}",
      icon: "error",
    });
  }
}

function deleteProduct(index, id) {
  $("#row" + index).remove();
  product_ids.removeItem(id);
  if (product_ids.length == 0) {
    cont = 0;
    product_ids = [];
    rowCont = [];
  }
}

function edeleteProduct(index, id) {
  $("#erow" + index).remove();
  eproduct_ids.removeItem(id);
  if (eproduct_ids.length == 0) {
    econt = 0;
    eproduct_ids = [];
    erowCont = [];
  }
}

Array.prototype.removeItem = function(a) {
  for (var i = 0; i < this.length; i++) {
    if (this[i] == a) {
      for (var i2 = i; i2 < this.length - 1; i2++) {
        this[i2] = this[i2 + 1];
      }
      this.length = this.length - 1;
      return;
    }
  }
};

    // edit
    $("#back").click(function() {
      $("#div_follows").hide();
      $("#header_follow").hide();
      $("#header_customer").show();
      $("#div_customers").show();
      if ($("#customer_details_box").hasClass("collapsed-box")) {
        $("#customer_details_box").removeClass("collapsed-box");
        $("#customer_details_box_body").css("display", "block");
        $("#icon-collapsed").removeClass("fa fa-plus");
        $("#icon-collapsed").addClass("fa fa-minus");
      }
    });    

    $("#btn_add_follow").click(function(event) {
      id = $("#c_id").val();
      name = $("#c_name").val();
      createFollowCustomer(id, name);
    });
    /* FOLLOW CUSTOMERS */
</script>
@endsection