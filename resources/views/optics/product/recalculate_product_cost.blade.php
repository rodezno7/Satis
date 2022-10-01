@extends('layouts.app')

@section('title', __('product.recalculate_cost'))

@section('content')
{{-- Content Header (Page header) --}}
<section class="content-header">
    <h1>
        @lang('product.recalculate_cost')
    </h1>
</section>

{{-- Main content --}}
<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">
                @lang('product.recalculate_cost')
            </h3>
        </div>
        
        <div class="box-body">
            {!! Form::open([
                'url' => action('ProductController@postRecalculateCost'),
                'method' => 'post',
                'id' => 'recalculate_cost_form'
            ]) !!}

            <div class="row">
                {{-- start --}}
                <div class="col-sm-3 col-sm-offset-2">
                    <div class="form-group">
                        {!! Form::text(
                            'start',
                            null,
                            ['class' => 'form-control', 'placeholder' => __('lang_v1.start')]
                        ) !!}
                    </div>
                </div>

                {{-- end --}}
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::text(
                            'end',
                            null,
                            ['class' => 'form-control input_number', 'placeholder' => __('lang_v1.end')]
                        ) !!}
                    </div>
                </div>

                {{-- submit --}}
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-success btn-block">
                        @lang('kardex.start')
                    </button>
                </div>
            </div>

            {!! Form::close() !!}

            <div class="row" style="margin-top: 25px;">
                <div id="progress" class="col-sm-8 col-sm-offset-2" style="display: none;">
                    <p id="progress-percentage" class="text-center">0%</p>

                    <div class="progress progress-sm active">
                        <div id="progress-bar" class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                            <span id="span-progress-bar" class="sr-only">0%</span>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div id="product-list" class="panel-body">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
    $(document).ready(function() {
        // On submit of recalculate_cost_form form
        $(document).on('submit', 'form#recalculate_cost_form', function (e) {
            e.preventDefault();

            Swal.fire({
                title: LANG.sure,
                text: LANG.recalculate_cost_text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelmButtonText: LANG.cancel,
                confirmButtonText: LANG.yes
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#progress').show();
                    $(this).find('button[type="submit"]').attr('disabled', true);

                    $.ajax({
                        method: 'post',
                        url: $(this).attr('action'),
                        dataType: 'json',
                        data: $(this).serialize(),
                        success: function (response) {
                            if (response.success == 1) {
                                let array_size = response.variations.length;
                                let progress = 0;

                                if (array_size > 0) {
                                    $.each(response.variations, function (i, val) {
                                        $.ajax({
                                            type: 'get',
                                            url: '/products/recalculate-product-cost/' + val,
                                            dataType: 'json',
                                            success: function (res) {
                                                if (res.success == 1) {
                                                    let line = `<p style="color: #008d4c;">${res.msg_massive}</p>`;
                                                    $('#product-list').append(line);

                                                } else {
                                                    let line = `<p style="color: #d73925;">${res.msg_massive}</p>`;
                                                    $('#product-list').append(line);
                                                }
                                            }

                                        }).done(function () {
                                            progress++;
                                            
                                            let percentage = parseFloat(progress * 100 / array_size).toFixed(0);

                                            $('#progress-percentage').html(percentage + '%');
                                            $('#span-progress-bar').html(percentage + '%');
                                            $('#progress-bar').css('width', percentage + '%');
                                        });
                                    });
                                }

                            } else {
                                Swal.fire({
                                    title: result.msg,
                                    icon: 'error',
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