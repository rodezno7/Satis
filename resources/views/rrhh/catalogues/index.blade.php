@extends('layouts.app')
@section('title', __('rrhh.catalogues'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('rrhh.catalogues')
        <small>@lang('rrhh.manage_your_catalogues')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="col-xs-12 pos-tab-container">
                <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 pos-tab-menu">
                    <div class="list-group">
                        <a href="#" class="list-group-item text-center active">@lang('rrhh.marital_statuses')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.departments')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.positions')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.afps')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.types')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.nationalities')</a>
                        {{-- <a href="#" class="list-group-item text-center">@lang('rrhh.banks')</a> --}}
                        <a href="#" class="list-group-item text-center">@lang('rrhh.professions_occupations')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.way_to_pays')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.document_types')</a>
                        
                        <a href="#" class="list-group-item text-center">@lang('rrhh.special_capabilities')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.employee_classification')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.types_studies')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.types_absences')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.types_inabilities')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.types_relationships')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.types_income_discounts')</a>

                        <a href="#" class="list-group-item text-center">@lang('rrhh.types_personnel_actions')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.types_wages')</a>
                        <a href="#" class="list-group-item text-center">@lang('rrhh.types_contracts')</a>
                        {{-- <a href="#" class="list-group-item text-center">@lang('rrhh.cost_center')</a> --}}
                    </div>
                </div>
                <div class="col-lg-10 col-md-9 col-sm-9 col-xs-8 pos-tab">

                    <!-- tab 1 start -->
                    @include('rrhh.catalogues.marital_statuses.index')
                    <!-- tab 1 end -->

                    <!-- tab 2 start -->
                    @include('rrhh.catalogues.departments.index')
                    <!-- tab 2 end -->

                    <!-- tab 3 start -->
                    @include('rrhh.catalogues.positions.index')
                    <!-- tab 3 end -->

                    <!-- tab 4 start -->
                    @include('rrhh.catalogues.afps.index')
                    <!-- tab 4 end -->

                    <!-- tab 5 start -->
                    @include('rrhh.catalogues.types.index')
                    <!-- tab 5 end -->

                    <!-- tab 6 start -->
                    @include('rrhh.catalogues.nationalities.index')
                    <!-- tab 6 end -->

                    {{-- <!-- tab 7 start -->
                    @include('rrhh.catalogues.banks.index')
                    <!-- tab 7 end --> --}}

                    <!-- tab 7 start -->
                    @include('rrhh.catalogues.professions.index')
                    <!-- tab 7 end -->

                    <!-- tab 8 start -->
                    @include('rrhh.catalogues.pays.index')
                    <!-- tab 8 end -->

                    <!-- tab 9 start -->
                    @include('rrhh.catalogues.documents.index')
                    <!-- tab 9 end -->

                    <!-- tab 10 start -->
                    @include('rrhh.catalogues.special_capabilities.index')
                    <!-- tab 10 end -->

                    <!-- tab 11 start -->
                    @include('rrhh.catalogues.employee_classification.index')
                    <!-- tab 11 end -->

                    <!-- tab 12 start -->
                    @include('rrhh.catalogues.types_studies.index')
                    <!-- tab 12 end -->

                    <!-- tab 13 start -->
                    @include('rrhh.catalogues.types_absences.index')
                    <!-- tab 13 end -->

                    <!-- tab 14 start -->
                    @include('rrhh.catalogues.types_inabilities.index')
                    <!-- tab 14 end -->
                    
                    <!-- tab 15 start -->
                    @include('rrhh.catalogues.types_relationships.index')
                    <!-- tab 15 end -->

                    <!-- tab 16 start -->
                    @include('rrhh.catalogues.types_income_discounts.index')
                    <!-- tab 16 end -->

                    <!-- tab start -->
                    @include('rrhh.catalogues.types_personnel_actions.index')
                    <!-- tab end -->

                    <!-- tab start -->
                    @include('rrhh.catalogues.types_wages.index')
                    <!-- tab end -->

                    {{-- <!-- tab start -->
                    @include('rrhh.catalogues.cost_center.index')
                    <!-- tab end -->--}}

                    <!-- tab start -->
                    @include('rrhh.catalogues.types_contracts.index')
                    <!-- tab end -->
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content" id="modal_content">

            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_type" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="modal_content_type">

            </div>
        </div>
    </div>
</section>
<!-- /.content -->
@endsection
@section('javascript')
<script type="text/javascript">
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

    $(document).ready(function() {
        loadMaritalStatuses();
        loadDepartments();
        loadPositions();
        loadAfps();
        loadTypes();
        loadNationalities();
        // loadBanks();
        loadProfessions();
        loadWayToPays();
        loadDocumentTypes();
        loadSpecialCapabilities();
        loadEmployeeClassification();
        //loadTypesProfessionsOccupations();
        loadTypesStudies();
        loadTypesPersonnelActions();
        loadTypesIncomeDiscounts();
        loadTypesAbsences();
        loadTypesinabilities();
        loadKinshipTypes();

        loadTypesWages();
        //loadCostCenter();
        loadTypesContracts();
        //loadTypesClauseContracts();
        $.fn.dataTable.ext.errMode = 'none';
    });


    function loadMaritalStatuses() {
        var table = $("#marital-statuses-table").DataTable();
        table.destroy();
        var table = $("#marital-statuses-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/1",
            columns: [
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });

    }

    function loadDepartments() {
        var table2 = $("#departments-table").DataTable();
        table2.destroy();
        var table2 = $("#departments-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/2",
            columns: [
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });

    }

    function loadPositions() {
        var table3 = $("#positions-table").DataTable();
        table3.destroy();
        var table3 = $("#positions-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/3",
            columns: [
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });

    }

    function loadAfps() {
        var table4 = $("#afps-table").DataTable();
        table4.destroy();
        var table4 = $("#afps-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/4",
            columns: [
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });

    }

    function loadTypes() {
        var table5 = $("#types-table").DataTable();
        table5.destroy();
        var table5 = $("#types-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/5",
            columns: [
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });

    }

    function loadNationalities() {
        var table = $("#nationalities-table").DataTable();
        table.destroy();
        var table = $("#nationalities-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/6",
            columns: [
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });

    }

    function loadProfessions() {
        var table = $("#professions-table").DataTable();
        table.destroy();
        var table = $("#professions-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/7",
            columns: [
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });

    }

    function loadWayToPays() {
        var table = $("#way-to-pays-table").DataTable();
        table.destroy();
        var table = $("#way-to-pays-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/8",
            columns: [
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";

                if (data.value != 'Transferencia bancaria') {

                    @can('rrhh_catalogues.update')
                    html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                    @endcan

                    @can('rrhh_catalogues.delete')
                    html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                    @endcan
                }
                
                return html;
            } , orderable: false, searchable: false}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });

    }

    function loadDocumentTypes() {
        var table = $("#document-types-table").DataTable();
        table.destroy();
        var table = $("#document-types-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/9",
            columns: [
            {data: 'value'},
            {data: 'date_required'},
            {data: 'number_required'},
            {data: 'expedition_place'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";

                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });

    }

    function loadSpecialCapabilities() {
        var table5 = $("#special_capabilities-table").DataTable();
        table5.destroy();
        var table5 = $("#special_capabilities-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/10",
            columns: [
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });

    }


    function loadEmployeeClassification() {
        var table5 = $("#employee_classification-table").DataTable();
        table5.destroy();
        var table5 = $("#employee_classification-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/11",
            columns: [
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });
    }

    function loadTypesStudies() {
        var table5 = $("#types_studies-table").DataTable();
        table5.destroy();
        var table5 = $("#types_studies-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/12",
            columns: [
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });
    }


    function loadTypesAbsences() {
        var table5 = $("#types_absences-table").DataTable();
        table5.destroy();
        var table5 = $("#types_absences-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/13",
            columns: [
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });
    }

    function loadTypesinabilities() {
        var table5 = $("#types_inabilities-table").DataTable();
        table5.destroy();
        var table5 = $("#types_inabilities-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/14",
            columns: [
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });
    }


    function loadKinshipTypes() {
        var table5 = $("#types_relationships-table").DataTable();
        table5.destroy();
        var table5 = $("#types_relationships-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getCataloguesData/15",
            columns: [
            {data: 'value'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });
    }

    function loadTypesIncomeDiscounts() {
        var table5 = $("#types_income_discounts-table").DataTable();
        table5.destroy();
        var table5 = $("#types_income_discounts-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getTypeIncomeDiscountData",
            columns: [
            {data: 'type'},
            {data: 'name'},
            {data: 'planilla_column'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editTypeIncomeDiscount('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteTypeIncomeDiscount('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });
    }


    function loadTypesPersonnelActions() {
        var table5 = $("#types_personnel_actions-table").DataTable();
        table5.destroy();
        var table5 = $("#types_personnel_actions-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getTypePersonnelActionData",
            columns: [
            {data: 'name'},
            {data: 'required_authorization'},
            {data: 'apply_to_many'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editTypePersonnelAction('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteTypePersonnelAction('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });
    }


    function loadTypesWages() {
        var table5 = $("#types_wages-table").DataTable();
        table5.destroy();
        var table5 = $("#types_wages-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getTypeWagesData",
            columns: [
            {data: 'name'},
            {data: 'isss'},
            {data: 'afp'},
            {data: 'type'},
            {data: null, render: function(data){

                html = "";
                
                @can('rrhh_catalogues.update')
                html += '<a class="btn btn-xs btn-primary" onClick="editTypeWage('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteTypeWage('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });
    }


    function loadTypesContracts() {
        var table5 = $("#types_contracts-table").DataTable();
        table5.destroy();
        var table5 = $("#types_contracts-table").DataTable({

            deferRender: true,
            processing: true,
            serverSide: true,
            ajax: "/rrhh/getTypes",
            columns: [
            {data: 'name'},
            {data: 'status'},
            {data: null, render: function(data){

                html = "";
                @can('rrhh_catalogues.view')
                html += '<a href="/rrhh-catalogues/type-contract/'+data.id+'"  target="_blank" class="btn btn-xs btn-primary"><i class="fa fa-file"></i> @lang('messages.view')</a>';
                @endcan

                @can('rrhh_catalogues.update')
                html += ' <a href="/rrhh-catalogues/type-contract/'+data.id+'/edit" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
                @endcan

                @can('rrhh_catalogues.delete')
                html += ' <a class="btn btn-xs btn-danger" onClick="deleteTypeContract('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
                @endcan
                
                return html;
            } , orderable: false, searchable: false}
            ],
            dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
        });
    }

    // function loadCostCenter() {
    //     var table5 = $("#cost_center-table").DataTable();
    //     table5.destroy();
    //     var table5 = $("#cost_center-table").DataTable({

    //         deferRender: true,
    //         processing: true,
    //         serverSide: true,
    //         ajax: "/rrhh/getCataloguesData/13",
    //         columns: [
    //         {data: 'value'},
    //         {data: 'status'},
    //         {data: null, render: function(data){

    //             html = "";
                
    //             @can('rrhh_catalogues.update')
    //             html += '<a class="btn btn-xs btn-primary" onClick="editItem('+data.id+')"><i class="glyphicon glyphicon-edit"></i> @lang('messages.edit')</a>';
    //             @endcan

    //             @can('rrhh_catalogues.delete')
    //             html += ' <a class="btn btn-xs btn-danger" onClick="deleteItem('+data.id+')"><i class="glyphicon glyphicon-trash"></i> @lang('messages.delete')</a>';
    //             @endcan
                
    //             return html;
    //         } , orderable: false, searchable: false}
    //         ],
    //         dom:'<"row margin-bottom-12"<"col-sm-12"<"pull-left"l><"pull-right"fr>>>tip',
    //     });
    // }


    $("#add_marital_status, #add_department, #add_position, #add_afp, #add_type, #add_nationality, #add_profession, #add_way_to_pay, #add_document_type, #add_special_capabilities, #add_employee_classification, #add_types_studies, #add_types_absences, #add_types_inabilities, #add_types_relationships").click(function(){
        $("#modal_content").html('');
        catalogue_id = $(this).val();
        var url = '{!!URL::to('/rrhh/create-item/:catalogue_id')!!}';
        url = url.replace(':catalogue_id', catalogue_id);
        $.get(url, function(data) {

            $("#modal_content").html(data);
            $('#modal').modal({backdrop: 'static'});
            
        });
    });

    $("#add_types_income_discounts").click(function(){
        $("#modal_content").html('');        
        var url = '{!!URL::to('/rrhh-types-income-discounts/create')!!}';
        $.get(url, function(data) {

            $("#modal_content").html(data);
            $('#modal').modal({backdrop: 'static'});
            
        });
    });

    $("#add_types_wages").click(function(){
        $("#modal_content").html('');        
        var url = '{!!URL::to('/rrhh-type-wages/create')!!}';
        $.get(url, function(data) {

            $("#modal_content").html(data);
            $('#modal').modal({backdrop: 'static'});
            
        });
    });

    $("#add_types_personnel_actions").click(function(){
        $("#modal_content_type").html('');        
        var url = '{!!URL::to('/rrhh-type-personnel-action/create')!!}';
        $.get(url, function(data) {
            $("#modal_content_type").html(data);
            $('#modal_type').modal({backdrop: 'static'});
            
        });
    });

    function editItem(id) {
        $("#modal_content").html('');
        var url = '{!!URL::to('/rrhh/edit-item/:id')!!}';
        url = url.replace(':id', id);
        $.get(url, function(data) {

            $("#modal_content").html(data);
            $('#modal').modal({backdrop: 'static'});
            
        });
    }

    function editTypeWage(id) {
        $("#modal_content").html('');
        var url = '{!!URL::to('/rrhh-type-wages/:id/edit')!!}';
        url = url.replace(':id', id);
        $.get(url, function(data) {

            $("#modal_content").html(data);
            $('#modal').modal({backdrop: 'static'});
            
        });
    }


    function editTypeIncomeDiscount(id) {
        $("#modal_content").html('');
        var url = '{!!URL::to('/rrhh-types-income-discounts/:id/edit')!!}';
        url = url.replace(':id', id);
        $.get(url, function(data) {

            $("#modal_content").html(data);
            $('#modal').modal({backdrop: 'static'});
            
        });
    }


    function editTypePersonnelAction(id) {
        $("#modal_content_type").html('');
        var url = '{!!URL::to('/rrhh-type-personnel-action/:id/edit')!!}';
        url = url.replace(':id', id);
        $.get(url, function(data) {

            $("#modal_content_type").html(data);
            $('#modal_type').modal({backdrop: 'static'});
            
        });
    }

    // function editBank(id) {
    //     $("#modal_content").html('');
    //     var url = '{!!URL::to('/rrhh-banks/:id/edit')!!}';
    //     url = url.replace(':id', id);
    //     $.get(url, function(data) {

    //         $("#modal_content").html(data);
    //         $('#modal').modal({backdrop: 'static'});
            
    //     });
    // }

    function deleteItem(id) {
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
                route = '/rrhh-catalogues-data/'+id;
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
                            $("#afps-table").DataTable().ajax.reload(null, false);
                            $("#marital-statuses-table").DataTable().ajax.reload(null, false);
                            $("#departments-table").DataTable().ajax.reload(null, false);
                            $("#positions-table").DataTable().ajax.reload(null, false);
                            $("#types-table").DataTable().ajax.reload(null, false);
                            $("#nationalities-table").DataTable().ajax.reload(null, false);
                            $("#professions-table").DataTable().ajax.reload(null, false);
                            $("#way-to-pays-table").DataTable().ajax.reload(null, false);
                            $("#document-types-table").DataTable().ajax.reload(null, false);
                            $('#modal').modal('hide');
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
        });
    }

    function deleteTypeWage(id) {
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
                route = '/rrhh-type-wages/'+id;
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
                            $("#types_wages-table").DataTable().ajax.reload(null, false);
                            $('#modal').modal('hide');
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
        
        });
    }


    function deleteTypeIncomeDiscount(id) {
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
                route = '/rrhh-types-income-discounts/'+id;
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
                            $("#types_income_discounts-table").DataTable().ajax.reload(null, false);
                            $('#modal').modal('hide');
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
        
        });
    }

    function deleteTypePersonnelAction(id) {
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
                route = '/rrhh-type-personnel-action/'+id;
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
                            $("#types_personnel_actions-table").DataTable().ajax.reload(null, false);
                            $('#modal').modal('hide');
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
        
        });
    }


    function deleteTypeContract(id) {
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
                route = '/rrhh-catalogues/type-contract/'+id;
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
                            $("#types_contracts-table").DataTable().ajax.reload(null, false);
                            $('#modal').modal('hide');
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
        
        });
    }
</script>
@endsection