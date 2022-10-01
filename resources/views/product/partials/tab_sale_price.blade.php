<br>
<div class="row">
  {!! Form::open(['url' => action('ProductController@storeSalePriceScale'), 'method' => 'post', 'id' =>
  'sale_price_add_form']) !!}
  <div class="col-sm-4">
    <!-- Product -->
    {!! Form::hidden('product_id', $product->id, ['id' => 'product_id']) !!}

    <!-- Variation -->
    @if ($product->type != 'single')
      <div class="form-group">
        {!! Form::label('variation_id', 'Variación' . ': ') !!}
        <div class="wrap-inputform">
          {!! Form::select('variation_id', $variations_list, '', ['class' => 'inputform2']) !!}
        </div>
      </div>
    @else
      {!! Form::hidden('variation_id', null, ['id' => 'variation_id']) !!}
    @endif

    <!-- Desde -->
    <div id="fg_from" class="form-group">
      {!! Form::label('from', __('product.from') . ' (> 1)' . ': ') !!}
      <div class="wrap-inputform">
        {!! Form::number('from', null, ['class' => 'inputform2', 'required', 'placeholder' => __('product.from'),  'min' => '2']) !!}
      </div>
      <span id="span_from" class="help-block"></span>
    </div>

    <!-- Hasta -->
    <div class="form-group">
      {!! Form::label('to', __('product.to') . ': ') !!}
      <div class="wrap-inputform">
        {!! Form::number('to', null, ['class' => 'inputform2', 'required', 'placeholder' => __('product.to'), 'min' => '1']) !!}
      </div>
    </div>

    <!-- $ Bruto Un. -->
    <div class="form-group">
      {!! Form::label('price', '$ ' . __('product.gross') . ' Un: ') !!} <i class="fa fa-info-circle text-info"
        data-toggle="tooltip" data-placement="bottom" data-html="true"
        data-original-title="{{ __('product.gross_tooltip') }}" aria-hidden="true"></i>
      <div class="wrap-inputform">
        {!! Form::number('price', null, ['class' => 'inputform2', 'required', 'placeholder' => __('product.gross'), 'min' => '0', 'step' => '0.01'])
        !!}
      </div>
    </div>

    <div id="button_area">
      <button type="submit" id="btn_add_sps" class="btn btn-primary">@lang('product.add')</button>
    </div>

  </div>
  {!! Form::close() !!}

  <div class="col-sm-8">
    <div class="table-responsive">
      <table id="prices_table" class="table table-condensed bg-gray">
        <thead>
          <tr class="bg-green">
            @if ($product->type != 'single')
              <th>@lang('lang_v1.variation')</th>
            @endif
            <th>@lang('product.from')</th>
            <th>@lang('product.to')</th>
            <th>@lang('product.gross')</th>
            <th>@lang('messages.action')</th>
          </tr>
        </thead>
        <tbody id="prices_list">
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
  table#prices_table tbody#prices_list tr { cursor: pointer; }
</style>

<script>
  // Datatable settings in view-modal

  // Validate
  function validateForm() {
    if (parseInt($('#from').val()) >= parseInt($('#to').val())) {
      $('#fg_from').addClass('has-error');
      $('#span_from').html('@lang("product.wrong_range")');
      return true;
    }
    return false;
  }

  // Save
  $(document).on('submit', 'form#sale_price_add_form', function(e) {
    e.preventDefault();

    if (validateForm()) {
      return;
    }

    clearStyles()

    var data = $(this).serialize();

    $.ajax({
      method: "POST",
      url: $(this).attr("action"),
      dataType: "json",
      data: data,
      beforeSend: function () {
        $("#btn_add_sps").prop("disabled", true);
      },
      success: function(result) {
        if (result.success === true) {
          $("#btn_add_sps").prop("disabled", false);
          $('#prices_table').DataTable().ajax.reload();
          Swal.fire({
            title: "" + result.msg + "",
            icon: "success",
          });
        } else {
          Swal.fire({
            title: "" + result.msg + "",
            icon: "error",
          });
        }
      }
    });
    $(this).clear();
    return false;
  });

  // Delete
  $(document).on('click', 'button.delete_sale_price_button', function() {
    clearStyles()

    swal({
      title: LANG.sure,
      text: LANG.confirm_delete_sps,
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
              cancelEdit();
              $('#prices_table').DataTable().ajax.reload();
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

  // Add buttons to edit when selecting a row
  $(document).on('click', 'table#prices_table tbody#prices_list tr', function() {
    clearStyles()

    if ($(this).find('td').eq(0).hasClass('dataTables_empty')) {
      $(this).removeClass('selected')
      return;
    }

    $(this).addClass('selected').siblings().removeClass('selected');
    
    var indexTd = 0;
    
    @if ($product->type != 'single')
      var variationTd = $(this).find('td').eq(indexTd).html();
      $('input[name=variation_id]').val(variationTd);
      indexTd = 1;
    @endif
    
    var fromTd = $(this).find('td').eq(indexTd).html();
    var toTd = $(this).find('td').eq(indexTd + 1).html();
    var priceTd = $(this).find('td').eq(indexTd + 2).html();

    $('input[name=from]').val(fromTd);
    $('input[name=to]').val(toTd);
    $('input[name=price]').val(priceTd);

    $('#button_area').html('<button onclick="editSalePriceScale(' + $(this).attr('id') + ')" type="button" id="btn_edit_sps" class="btn btn-primary">@lang("messages.edit")</button>&nbsp;' +
      '<button onclick="cancelEdit()" type="button" id="btn_cancel_sps" class="btn btn-default">@lang("messages.cancel")</button>');
  });

  // Edit
  function editSalePriceScale(id) {
    if (validateForm()) {
      return false;
    }

    clearStyles()

    var data = $('form#sale_price_add_form').serialize();
    
    $.ajax({
        method: "POST",
        url: "/edit_sale_price_scale/" + id,
        dataType: "json",
        data: data,
        success: function(result) {
            if (result.success == true) {
                Swal.fire({
                    title: ""+result.msg+"",
                    icon: "success",
                });
                $('#prices_table').DataTable().ajax.reload();
            } else {
                Swal.fire
                ({
                    title: ""+result.msg+"",
                    icon: "error",
                });
            }
        }
    });

    cancelEdit();
  }

  // Cancel edit and reset inputs
  function cancelEdit() {
    clearStyles()
    @if ($product->type != 'single')
      $('input[name=variation_id]').val('');
    @endif
    $('input[name=from]').val('');
    $('input[name=to]').val('');
    $('input[name=price]').val('');
    $('#prices_list tr.selected').removeClass('selected');
    $('#button_area').html('<button type="submit" id="btn_add_sps" class="btn btn-primary">@lang("product.add")</button>');
  }

  // Clear styles
  function clearStyles() {
    $('#fg_from').removeClass('has-error');
    $('#span_from').html('');
  }

  /*
  $(document).on('chage', '#from', function() {
    $('#fg_from').removeClass('has-error');
    $('#span_from').html('');
  });
  */
</script>
