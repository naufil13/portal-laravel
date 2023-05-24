<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
<div class="mt-3"></div>
<div class="form-group row">
    <label class="col-2 col-form-label">Company Email Address:</label>
    <div class="col-lg-10">
        <input type="text" name="opt[company_email]" class="form-control" value="<?php echo opt('company_email'); ?>">
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

<div class="form-group row">
    <label class="col-2 col-form-label">Support Emails:</label>
    <div class="col-lg-10">
        <input  class="form-control"  name='opt[support_emails]' id="support_emails" value='<?php echo opt('support_emails'); ?>'>
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

<div class="form-group row">
    <label class="col-2 col-form-label">Phone Number:</label>
    <div class="col-lg-10">
        <input type="text" name="opt[company_phone]" class="form-control" value="<?php echo opt('company_phone'); ?>">
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

<div class="form-group row">
    <label class="col-2 col-form-label">Fax Number:</label>
    <div class="col-lg-10">
        <input type="text" name="opt[company_fax_number]" class="form-control" value="<?php echo opt('company_fax_number'); ?>">
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

<div class="form-group row">
    <label class="col-2 col-form-label">Address:</label>
    <div class="col-lg-10">
        <textarea name="opt[company_address]" cols="" rows="3" class="form-control col-sm-12"><?php echo opt('company_address'); ?></textarea>
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>


<div class="form-group row">
    <label class="col-2 col-form-label">Latitude:</label>
    <div class="col-sm-4">
        <input type="text" name="opt[company_latitude]" class="form-control" value="<?php echo opt('company_latitude'); ?>">
    </div>
    <label class="col-2 col-form-label">Longitude:</label>
    <div class="col-sm-4">
        <input type="text" name="opt[company_longitude]" class="form-control" value="<?php echo opt('company_longitude'); ?>">
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

<script>
    var input = document.getElementById('support_emails');

// initialize Tagify on the above input node reference
new Tagify(input)
</script>
