<div class="mt-3"></div>
<div class="form-group row">
    <label for="meta_title" class="col-2 col-form-label text-right">Header Logo:</label>
    <div class="col-6">
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="logo" name="opt[header_logo]">
            <label class="custom-file-label" for="customFile">Choose file</label>
            <span class="form-text text-muted">jpg, png only allow & max 1mb size</span>
        </div>
    </div>
    <div class="col-2">
        <div class="img44">
            <a href="{{ asset_url('images/' . opt('header_logo'), 1) }}" data-fancybox="footer_logo">
                <img src="{{ asset_url('images/' . opt('header_logo'), 1) }}" width="60" class="img-fluid img-thumbnail img_center" alt="img">
            </a>
            <button type="button" class="setting_img_delete btn-danger" data-skin="dark" data-toggle="kt-tooltip" title="remove image">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

<div class="form-group row">
    <label for="meta_title" class="col-2 col-form-label text-right">Footer Logo:</label>
    <div class="col-6">
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="footer_logo" name="opt[footer_logo]">
            <label class="custom-file-label" for="customFile">Choose file</label>
            <span class="form-text text-muted">jpg, png only allow & max 1mb size</span>
        </div>
    </div>
    <div class="col-2">
        <div class="img44">
            <a href="{{ asset_url('images/' . opt('footer_logo'), 1) }}" data-fancybox="footer_logo">
                <img src="{{ asset_url('images/' . opt('footer_logo'), 1) }}" width="60" class="img-fluid img-thumbnail img_center" alt="img">
            </a>
            <button type="button" class="setting_img_delete btn-danger" data-skin="dark" data-toggle="kt-tooltip" title="remove image">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

<div class="mt-3"></div>
<div class="form-group row">
    <label for="meta_title" class="col-2 col-form-label text-right">Favicon:</label>
    <div class="col-6">
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="favicon" name="opt[favicon]">
            <label class="custom-file-label" for="customFile">Choose file</label>
            <span class="form-text text-muted">jpg, png only allow & max 1mb size</span>
        </div>
    </div>
    <div class="col-2">
        <div class="img44">
            <a href="{{ asset_url('images/' . opt('favicon'), 1) }}" data-fancybox="favicon">
                <img src="{{ asset_url('images/' . opt('favicon'), 1) }}" width="60" class="img-fluid img-thumbnail img_center" alt="img">
            </a>
            <button type="button" class="setting_img_delete btn-danger" data-skin="dark" data-toggle="kt-tooltip" title="remove image">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>


<div class="form-group row">
    <label for="google_analytics" class="col-lg-2 col-form-label text-right">Google Analytics:</label>
    <div class="col-lg-10">
        <textarea name="opt[google_analytics]" class="form-control" id="kt_autosize_1" rows="6" placeholder="Write past code here" id="google_analytics"><?php echo opt('google_analytics')?></textarea>
    </div>
</div>
