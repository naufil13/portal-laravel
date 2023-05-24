<div class="mt-3"></div>

<div class="form-group row">
    <label class="col-2 col-form-label">Site Title:</label>
    <div class="col-lg-10">
        <input type="text" name="opt[site_title]" class="form-control" value="<?php echo opt('site_title'); ?>">
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
<div class="form-group row">
    <label class="col-2 col-form-label">Site URL:</label>
    <div class="col-lg-10">
        <input type="text" name="opt[site_url]" class="form-control" value="<?php echo opt('site_url'); ?>">
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
<div class="form-group row">
    <label class="col-2 col-form-label">Tag Line:</label>
    <div class="col-lg-10">
        <input type="text" name="opt[tag_line]" class="form-control" value="<?php echo opt('tag_line'); ?>">
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
<div class="form-group row">
    <label for="site_env" class="col-2 col-form-label">Site ENV:</label>
    <div class="col-6">
        <select name="opt[site_env]" class="form-control m_selectpicker">
            <option value="Production" {{opt('site_env') == 'Production' ? 'selected' : ''}}> Production </option>
            <option value="Development" {{opt('site_env') == 'Development' ? 'selected' : ''}}> Development </option>
        </select>
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
<div class="form-group row">
    <label for="site_debug" class="col-2 col-form-label">Site Debug:</label>
    <div class="col-6">
        <select name="opt[site_debug]" class="form-control m_selectpicker">
            <option value="Yes" {{opt('site_debug') == 'Yes' ? 'selected' : ''}}> Yes </option>
            <option value="No" {{opt('site_debug') == 'No' ? 'selected' : ''}}> No </option>
        </select>
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

<!--<div class="form-group row">
    <label class="col-2 col-form-label">Top Text:</label>
    <div class="col-lg-10">
        <textarea name="opt[top_text]" cols="" rows="5" class="small_editor col-sm-12"><?php /*echo opt('top_text'); */?></textarea>
    </div>
</div>-->

<div class="form-group row">
    <label class="col-2 col-form-label">Copyright Text:</label>
    <div class="col-lg-10">
        <textarea name="opt[copyright_text]" cols="" rows="5" class="form-control col-sm-12"><?php echo opt('copyright_text'); ?></textarea>
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

<div class="form-group row">
    <label class="col-2 col-form-label">Timezone:</label>
    <div class="col-lg-10">
        <input type="text" name="opt[site_timezone]" class="form-control" value="<?php echo opt('site_timezone'); ?>">
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

<div class="form-group row">
    <label class="col-2 col-form-label">Session Time (In Minutes):</label>
    <div class="col-lg-10">
        <input type="text" name="opt[site_session]" class="form-control" value="<?php echo opt('site_session'); ?>">
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
<div class="form-group row">
    <label class="col-2 col-form-label">Currency:</label>
    <div class="col-lg-2">
        <input type="text" name="opt[currency]" class="form-control" value="<?php echo opt('currency'); ?>">
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

<div class="form-group row">
    <label for="maintenance_mode" class="col-2 col-form-label">Maintenance Mode:</label>
    <div class="col-6">
        <select name="opt[maintenance_mode]" class="form-control m_selectpicker">
            <option value="Inactive" {{opt('maintenance_mode') == 'Inactive' ? 'selected' : ''}}> Inactive </option>
            <option value="Active" {{opt('maintenance_mode') == 'Active' ? 'selected' : ''}}> Active </option>
        </select>
    </div>
</div>
