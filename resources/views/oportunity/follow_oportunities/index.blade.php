@extends('layouts.app')
@section('title', __('crm.oportunities'))
    <script th:src="@{/js/datatables.min.js}"></script>
@section('content')
    <section class="content-header">
        <h1>@lang( 'crm.oportunities')</h1>
    </section>
    <section class="content">
        <div class="boxform_u box-solid_u">

            <div class="box-body">
                @can('crm-contactreason.view')
                    <div class="row">
                        <div class="col-sm-12" >
                            <div class="form-group pull-right">
                                <div class="input-group">
                                    <a href="/oportunities?type=all_oportunities" type="button" class="btn btn-primary">
                                        @lang('crm.back')
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="div_follows">
                        <div class="row" style="margin-top: 5px;">
                            <div class="box box-default" id="opportunity_details_box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">@lang('crm.opportunity_details')</h3>

                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                class="fa fa-minus" id="icon-collapsed"></i>
                                        </button>
                                    </div>
                                    <!-- /.box-tools -->
                                </div>

                                <!-- /.box-header -->
                                <div class="box-body" id="opportunity_details_box_body">
                                    <div class="col-sm-4">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <i class="fa fa-folder"></i> &nbsp;<strong>@lang('crm.general')</strong>
                                            </div>
                                            <div class="panel-body">
                                                <input type="hidden" name="id" id="oportunity_id"
                                                    value="{{ $oportunity->id }}">
                                                <strong>@lang('crm.contact_type')</strong>
                                                <p class="text-muted" id="lbl_contact_type">{{ $oportunity->contact_type }}
                                                </p>

                                                <strong>@lang('crm.contact_date')</strong>
                                                <p class="text-muted" id="lbl_contact_date">{{ $oportunity->contact_date }}</p>

                                                <strong>@lang('crm.contact_reason')</strong>
                                                <p class="text-muted" id="lbl_contact_reason">{{ $oportunity->reason }}</p>

                                                <strong>@lang('crm.interest')</strong>
                                                <p class="text-muted" id="lbl_interest">{{ $oportunity->category }}</p>

                                                <strong>@lang('crm.known_by')</strong>
                                                <p class="text-muted" id="lbl_known_by">{{ $oportunity->knowned }}</p>

                                                <strong>@lang('crm.refered_by')</strong>
                                                <p class="text-muted" id="lbl_refered_id">{{ $oportunity->customer }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <i class="fa fa-user"></i> &nbsp;<strong>@lang('crm.person')</strong>
                                            </div>
                                            <div class="panel-body">
                                                <strong>@lang('crm.name')</strong>
                                                <p class="text-muted" id="lbl_name">{{ $oportunity->name }}</p>

                                                <strong>@lang('crm.position')</strong>
                                                <p class="text-muted" id="lbl_position">{{ $oportunity->charge }}</p>

                                                <strong>@lang('crm.company')</strong>
                                                <p class="text-muted" id="lbl_company">{{ $oportunity->company }}</p>

                                                <strong>@lang('geography.country')</strong>
                                                <p class="text-muted" id="lbl_country">{{ $oportunity->country }}</p>

                                                <strong>@lang('geography.state')</strong>
                                                <p class="text-muted" id="lbl_state">{{ $oportunity->state }}</p>

                                                <strong>@lang('geography.city')</strong>
                                                <p class="text-muted" id="lbl_city">{{ $oportunity->city }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <i class="fa fa-phone"></i> &nbsp;<strong>@lang('crm.contact')</strong>
                                            </div>
                                            <div class="panel-body">
                                                <strong>@lang('crm.contact_mode')</strong>
                                                <p class="text-muted" id="lbl_contact_mode">{{ $oportunity->mode }}</p>

                                                <strong>@lang('crm.contacts')</strong>
                                                <p class="text-muted" id="lbl_contacts">{{ $oportunity->contacts }}</p>

                                                <strong>@lang('crm.email')</strong>
                                                <p class="text-muted" id="lbl_email">{{ $oportunity->email }}</p>

                                                <strong>@lang('crm.social_user')</strong>
                                                <p class="text-muted" id="lbl_social_user">{{ $oportunity->social_user }}</p>

                                                <strong>&nbsp;</strong>
                                                <p class="text-muted">&nbsp;</p>

                                                <strong>&nbsp;</strong>
                                                <p class="text-muted">&nbsp;</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->

                        </div>
                        <div class="row">
                            <h4>@lang('crm.manage_follows')</h4>
                            <div class="form-group float-right col-lg-10 col-md-10 col-sm-10 col-xs-12">
                                <input type="hidden" name="c_id" id="c_id">
                                <input type="hidden" name="c_name" id="c_name">
                            </div>
                            <div class="form-group float-right col-lg-2 col-md-2 col-sm-2 col-xs-12">

                                <button type="button" class="btn btn-block btn-primary btn-modal"
                                    data-href="{{ action('FollowOportunitiesController@create', $oportunity->id) }}"
                                    data-container=".oportunities_modal">
                                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-condensed table-hover"
                                    id="follow_oportunities_table" width="100%">
                                    <thead>
                                        <tr>
                                            <th>@lang('crm.date')</th>
                                            <th>@lang('crm.contact_type')</th>
                                            <th>@lang('crm.contactreason')</th>
                                            <th>@lang('crm.contact_mode')</th>
                                            <th>@lang('crm.oportunity')</th>
                                            <th>@lang('crm.register_by')</th>
                                            <th>@lang('messages.actions')</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        </div>

        {{-- Div para renderizar el modal --}}
        <div class="modal fade oportunities_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

    </section>
@endsection
@section('javascript')
    <script>
        $(document).ready(function() {
            let id = document.getElementById('oportunity_id').value;
            console.log(id);
            var follows_table = $("#follow_oportunities_table").DataTable();
            follows_table.destroy();
            var follows_table = $('#follow_oportunities_table').DataTable({
                order: [
                    [0, 'asc']
                ],
                processing: true,
                serverSide: false,
                ajax: '/follow-oportunities/getFollowsByOportunity/' + id + '',
                columns: [{
                        data: 'date'
                    },
                    {
                        data: 'contact_type'
                    },
                    {
                        data: 'reason'
                    },
                    {
                        data: 'mode'
                    },
                    {
                        data: 'oportunity'
                    },
                    {
                        data: 'name_register'
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                    "targets": '_all',
                    "className": "text-center"
                }]
            });
        });

        //funcion para agregar una nueva oportunidad 
        $(document).on('submit', 'form#follow_oportunity_add_form', function(e) {
            // debugger;
            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', false);
            var data = $(this).serialize();
            $.ajax({
                method: "POST",
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        $("#follow_oportunities_table").DataTable().ajax.reload();
                        $('div.oportunities_modal').modal('hide');
                        Swal.fire({
                            title: "{{ __('payment.payment_terms_create') }}",
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false,
                        });
                        $("#content").hide();
                    } else {
                        Swal.fire({
                            title: result.msg,
                            icon: "error",
                        });
                    }
                }
            });
        });

        //funcion para editar una oportunidad
        $(document).on('click', 'a.edit_oportunities_button', function() {
            $("div.oportunities_modal").load($(this).data('href'), function() {
                $(this).modal('show');

                $('form#follow_oportunity_edit_form').submit(function(e) {
                    e.preventDefault();
                    $(this).find('button[type="submit"]').attr('disabled', false);
                    var data = $(this).serialize();

                    $.ajax({
                        method: "POST",
                        url: $(this).attr("action"),
                        dataType: "json",
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                $("#follow_oportunities_table").DataTable().ajax
                                .reload();
                                $('div.oportunities_modal').modal('hide');
                                Swal.fire({
                                    title: result.msg,
                                    icon: "success",
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                                $('#content').hide();
                            } else {
                                Swal.fire({
                                    title: result.msg,
                                    icon: "error",
                                });
                            }
                        }
                    });
                });
            });
        });

        //funcion para eliminar una terminal
        function deleteOport(id) {
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
                    route = '/follow-oportunities/' + id;
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
                                $("#follow_oportunities_table").DataTable().ajax.reload(null, false);
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

    </script>
@endsection
