<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h3>@lang('geography.edit_country')</h3>
        <div class="row">
            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>@lang('geography.name')</label>
                <input type="text" name="ename" id="ename" class="form-control" placeholder="@lang('geography.name')">
                <input type="hidden" name="country_id" id="country_id">
            </div>

            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>@lang('geography.short_name')</label>
                <input type="text" name="eshort_name" id="eshort_name" class="form-control" placeholder="@lang('geography.short_name')">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>@lang('geography.code')</label>
                <input type="text" name="ecode" id="ecode" class="form-control"  placeholder="@lang('geography.code')">
            </div>

            <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>@lang('geography.flag')</label>
                <input type="file" name="eflag" id="eflag" placeholder="@lang('geography.flag')" style="width: 100%">
            </div>
        </div>
    </div>
</div>