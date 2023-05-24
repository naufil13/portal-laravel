    <div class="mt-3"></div>

    <?php
    $inputs = ['Facebook', 'Twitter', 'Youtube', 'googleplus' => 'Google+', 'Linkedin', 'Instagram',  'Pinterest', 'Skype', 'Whatsapp'];
    $values = json_decode(opt('social'));
    foreach ($inputs as $key => $title) {
    $name = (is_int($key) ? Str::slug($title, '_', true) : $key);
    ?>
    <div class="form-group row">
        <label for="contact_no" class="col-2 col-form-label">{{ $title }}:</label>
        <div class="col-lg-10">
            <input type="text" name="opt[social][{{ $name }}]" placeholder="{{ __($title) }}" class="form-control" value="{{ $values->{$title} }}">
        </div>
    </div>
    <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
    <?php } ?>

