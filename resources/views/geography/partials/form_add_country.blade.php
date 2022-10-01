<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h3>@lang('geography.add_country')</h3>
        <div class="row">
            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>@lang('geography.name')</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="@lang('geography.name')">
            </div>

            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>@lang('geography.short_name')</label>
                <input type="text" name="short_name" id="short_name" class="form-control" placeholder="@lang('geography.short_name')">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>@lang('geography.code')</label>
                <input type="text" name="code" id="code" class="form-control"  placeholder="@lang('geography.code')">
            </div>

            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>@lang('geography.flag')</label>
                <input type="file" name="flag" id="flag" placeholder="@lang('geography.flag')" style="width: 100%">
            </div>
        </div>
    </div>
</div>