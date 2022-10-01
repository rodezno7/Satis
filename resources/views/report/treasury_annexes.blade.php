@extends('layouts.app')

@section('title', __('report.annexes'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.annexes')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="col-xs-12 pos-tab-container">
                <div class="col-xs-2 pos-tab-menu">
                    <div class="list-group">
                        @can('treasury_annexes.annex_1')
                        <a href="#" class="list-group-item text-center active">
                            @lang('report.annex_1')
                        </a>
                        @endcan

                        @can('treasury_annexes.annex_2')
                        <a href="#" class="list-group-item text-center">
                            @lang('report.annex_2')
                        </a>
                        @endcan

                        @can('treasury_annexes.annex_3')
                        <a href="#" class="list-group-item text-center">
                            @lang('report.annex_3')
                        </a>
                        @endcan

                        @can('treasury_annexes.annex_5')
                        <a href="#" class="list-group-item text-center">
                            @lang('report.annex_5')
                        </a>
                        @endcan

                        @can('treasury_annexes.annex_6')
                        <a href="#" class="list-group-item text-center">
                            @lang('report.annex_6')
                        </a>
                        @endcan

                        @can('treasury_annexes.annex_7')
                        <a href="#" class="list-group-item text-center">
                            @lang('report.annex_7')
                        </a>
                        @endcan

                        @can('treasury_annexes.annex_8')
                        <a href="#" class="list-group-item text-center">
                            @lang('report.annex_8')
                        </a>
                        @endcan

                        @can('treasury_annexes.annex_9')
                        <a href="#" class="list-group-item text-center">
                            @lang('report.annex_9')
                        </a>
                        @endcan
                    </div>
                </div>

                <div class="col-xs-10 pos-tab">
                    <div class="pos-tab-content active">
                        @include('report.partials.annex_form', [
                            'title' => __('report.annex_1'),
                            'action' => 'ReporterController@exportAnnex1',
                            'hide_location' => false,
                            'annex_number' => 1
                        ])
                    </div>

                    <div class="pos-tab-content">
                        @include('report.partials.annex_form', [
                            'title' => __('report.annex_2'),
                            'action' => 'ReporterController@exportAnnex2',
                            'hide_location' => false,
                            'annex_number' => 2
                        ])
                    </div>

                    <div class="pos-tab-content">
                        @include('report.partials.annex_form', [
                            'title' => __('report.annex_3'),
                            'action' => 'ReporterController@exportAnnex3',
                            'hide_location' => false,
                            'annex_number' => 3
                        ])
                    </div>

                    <div class="pos-tab-content">
                        @include('report.partials.annex_form', [
                            'title' => __('report.annex_5'),
                            'action' => 'ReporterController@exportAnnex5',
                            'hide_location' => false,
                            'annex_number' => 5
                        ])
                    </div>

                    <div class="pos-tab-content">
                        @include('report.partials.annex_form', [
                            'title' => __('report.annex_6'),
                            'action' => 'ReporterController@exportAnnex6',
                            'hide_location' => false,
                            'annex_number' => 6
                        ])
                    </div>

                    <div class="pos-tab-content">
                        @include('report.partials.annex_form', [
                            'title' => __('report.annex_7'),
                            'action' => 'ReporterController@exportAnnex7',
                            'hide_location' => true,
                            'annex_number' => 7
                        ])
                    </div>

                    <div class="pos-tab-content">
                        @include('report.partials.annex_form', [
                            'title' => __('report.annex_8'),
                            'action' => 'ReporterController@exportAnnex8',
                            'hide_location' => false,
                            'annex_number' => 8
                        ])
                    </div>

                    <div class="pos-tab-content">
                        @include('report.partials.annex_form', [
                            'title' => __('report.annex_9'),
                            'action' => 'ReporterController@exportAnnex9',
                            'hide_location' => false,
                            'annex_number' => 9
                        ])
                    </div>
                </div>
            </div>
            <!--  </pos-tab-container> -->
        </div>
    </div>
</section>
<!-- /.content -->
@endsection

@section('javascript')
@endsection