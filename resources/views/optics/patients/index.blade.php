@extends('layouts.app')

@section('title', __('customer.patients'))

<script th:src="@{/js/datatables.min.js}"></script>

@section('content')
<section class="content-header">
    <h1>@lang('customer.patients')</h1>
</section>

<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">
                @lang('customer.manage_patients')
            </h3>

            <div class="box-tools">
                <button
                    class="btn btn-block btn-primary btn-modal"
                    data-href="{{ action('Optics\PatientController@create') }}"
                    data-container=".patients_modal">
                    <i class="fa fa-plus"></i> @lang('messages.add')
                </button>
            </div>
        </div>

        <div class="box-body">
            @can('patients.view')
            <div class="table-responsive">
                <table
                    id="patients_table"
                    class="table table-striped table-condensed table-text-center"
                    style="font-size: inherit;"
                    width="100%">
                    <thead>
                        <tr>
                            <th>@lang('customer.code')</th>
                            <th>@lang('customer.full_name')</th>
                            <th>@lang('customer.age')</th>
                            <th>@lang('customer.location')</th>
                            <th>@lang('customer.employee')</th>
                            <th>@lang('messages.actions')</th>
                        </tr>
                    </thead>
                </table>
            </div>
            @endcan
        </div>
    </div>

    {{-- Div para renderizar el modal --}}
    <div class="modal fade patients_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
@endsection

@section('javascript')
    <script type="text/javascript">
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
        $(document).ready(function() {
            var patients_table = $('#patients_table').DataTable({
                processing: true,
                serverSide: true,
                type: 'GET',
                ajax: '/patients/getPatientsData',
                columnDefs: [{
                    "targets": [0, 1, 2, 3],
                    "className": "text-center",
                    "searchable": true
                },
                {
                    "targets": [4,5],
                    "className": "text-center",
                    "orderable": false,
                    "searchable": false
                }]
            });

            // Add Form
            $(document).on('submit', 'form#patient_add_form', function(e) {
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
                            $('div.patients_modal').modal('hide');
                            Swal.fire({
                                title: ""+result.msg+"",
                                icon: "success",
                                timer: 2000,
                                showConfirmButton: false,
                            });
                            patients_table.ajax.reload();
                        } else {
                            Swal.fire
                            ({
                                title: ""+result.msg+"",
                                icon: "error",
                                timer: 2000,
                                showConfirmButton: false,
                            });
                        }
                    }
                });
            });

            // Edit Form
            $(document).on('click', 'button.edit_patients_button', function() {
                $("div.patients_modal").load($(this).data('href'), function() {
                    $(this).modal('show');
                    $('form#patient_edit_form').submit(function(e) {
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
                                    $('div.patients_modal').modal('hide');
                                    Swal.fire({
                                        title: ""+result.msg+"",
                                        icon: "success",
                                        timer: 2000,
                                        showConfirmButton: false,
                                    });
                                    patients_table.ajax.reload();
                                } else {
                                    Swal.fire
                                    ({
                                        title: ""+result.msg+"",
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
            $(document).on('click', 'button.delete_patients_button', function() {
                swal({
                    title: LANG.sure,
                    text: LANG.confirm_delete_patient,
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
                                        title: ""+result.msg+"",
                                        icon: "success",
                                        timer: 2000,
                                        showConfirmButton: false,
                                    });
                                    patients_table.ajax.reload();
                                } else {
                                    Swal.fire
                                    ({
                                        title: ""+result.msg+"",
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

        function showgraduationbox() {
            if ($("#chkhas_glasses").is(":checked")) {
                $("#graduation_box").show();
				$("#glasses_graduation").show();
				$("#glasses_graduation").val('');
				$("#glasses_graduation").prop('required', true);
            } else {
				$("#glasses_graduation").val('');
				$("#glasses_graduation").prop('required', false);
				$("#glasses_graduation").hide();
                $("#graduation_box").hide();
            }
        }

        function SearchEmployee(){
            var code = $("#employee_code").val();

            if (code == '') {
                $('#employee_name').val('');
            } else {
                var route = "/patients/getEmployeeByCode/"+code;
                $.get(route, function(res) {
                    if (res.success) {
                        if (res.emp) {
                            $('#employee_name').val(res.msg);
                        } else {
                            $('#employee_name').val('');
                            $('#employee_code').val('');
                            Swal.fire
                            ({
                                title: ""+res.msg+"",
                                icon: "error",
                                timer: 2000,
                                showConfirmButton: false,
                            });
                        }
                    } else {
                        $('#employee_name').val('');
                        $('#employee_code').val('');
                        Swal.fire
                        ({
                            title: ""+res.msg+"",
                            icon: "error",
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                });
            }
        }
    </script>
@endsection
