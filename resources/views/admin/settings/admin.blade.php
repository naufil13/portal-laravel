<div class="mt-3"></div>
<div class="form-group row">
    <label for="meta_title" class="col-2 col-form-label text-right">admin logo:</label>
    <div class="col-6">
        <div class="custom-file">
            <input type="file" name="opt[admin_logo]" class="custom-file-input" id="customFile">
            <label class="custom-file-label" for="customFile">Choose file</label>
            <span class="form-text text-muted">jpg, png only allow & max 1mb size</span>
        </div>
    </div>
    <div class="col-2">
        <div class="img44">
            <img src="{{asset_url('images/' . opt('admin_logo'), 1) }}" width="60" class="img-fluid img-thumbnail img_center" alt="img">
            <button type="button" class="setting_img_delete btn-danger" data-skin="dark" data-toggle="kt-tooltip" title="remove image">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

<div class="form-group row">
    <label for="admin_titile" class="col-2 col-form-label text-right">copyright text:</label>
    <div class="col-6">
        <input name="opt[admin_copyright_text]" class="form-control" type="text" value="<?php echo opt('admin_copyright_text');?>" placeholder="Enter copyright text" id="admin_titile">
    </div>
</div>
