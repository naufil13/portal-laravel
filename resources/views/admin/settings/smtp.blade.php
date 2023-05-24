<div class="mt-3"></div>

<div class="form-group m-form__group row">
    <label class="col-2 col-form-label">SMTP:</label>
    <div class="col-2">
        <select name="opt[smpt]" class="form-control m_selectpicker">
            <option value="No" {{opt('smpt') == 'No' ? 'selected' : ''}}> No </option>
            <option value="Yes" {{opt('smpt') == 'Yes' ? 'selected' : ''}}> Yes </option>
        </select>
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

<div class="form-group row">
    <label class="col-2 col-form-label">Host :</label>
    <div class="col-6">
        <input type="text" name="opt[smtp_host]" class="form-control" placeholder="Host" value="<?php echo opt('smtp_host');?>">
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
<div class="form-group row">
    <label class="col-2 col-form-label">User :</label>
    <div class="col-6">
        <input type="text" name="opt[smtp_user]" class="form-control" placeholder="User" value="<?php echo opt('smtp_user');?>">
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
<div class="form-group row">
    <label class="col-2 col-form-label">Pass :</label>
    <div class="col-6">
        <input type="text" name="opt[smtp_pass]" class="form-control" placeholder="Pass" value="<?php echo opt('smtp_pass');?>">
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
<div class="form-group row">
    <label class="col-2 col-form-label">Port :</label>
    <div class="col-6">
        <input type="text" name="opt[smtp_port]" class="form-control" placeholder="Port" value="<?php echo opt('smtp_port');?>">
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
<div class="form-group row">
    <label class="col-2 col-form-label">Email From Address :</label>
    <div class="col-6">
        <input type="text" name="opt[smtp_email_from_address]" class="form-control" placeholder="Email From Address" value="<?php echo opt('smtp_email_from_address');?>">
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
<div class="form-group row">
    <label class="col-2 col-form-label">Email From Name :</label>
    <div class="col-6">
        <input type="text" name="opt[smtp_email_from_name]" class="form-control" placeholder="Email From Name" value="<?php echo opt('smtp_email_from_name');?>">
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
