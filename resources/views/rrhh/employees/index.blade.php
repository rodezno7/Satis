@extends('layouts.app')
@section('title', __('rrhh.rrhh'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('rrhh.overall_payroll')
        <small></small>
    </h1>
</section>

<!-- Main content -->
<section class="content">

    <div id='div_content'>
        
    </div>

    <div class="modal fade" id="modal" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="modal_content">

            </div>
        </div>
    </div>


</section>
<!-- /.content -->
@endsection
@section('javascript')
<script type="text/javascript">

    $( document ).ready(function() {
        sendRequest();
    });

    function sendRequest() {
        var url = '{!!URL::to('/rrhh-employees-getEmployeesData')!!}';
        $.get(url, function(data){
          $("#div_content").html(data);
      });
    }

    


</script>
@endsection