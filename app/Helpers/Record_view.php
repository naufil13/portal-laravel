<?php

use Illuminate\Database\Eloquent\Collection;

class Record_view
{
    var $row;
    var $url = '';
    var $id_field = '';

    var $image_fields = [];
    var $hidden_fields = [];
    var $is_front = FALSE;
    var $css_class = '';
    var $custom_func = [];
    var $attributes = [];
    var $table_config = [];

    private $ci;

    function __construct()
    {
        if(empty($this->url)){
            $this->url = admin_url('', true);
        }

        $this->table_config = [
            'table_class' => '',
            'th_width' => '25%',
            'th_class' => 'bg-light',
        ];
    }


    function showView()
    {

        $id_checkbox = FALSE;
        ob_start();
        ?>
<table class="table m-table table-view grid-table kt-margin-0 <?php echo $this->table_config['table_class'];?>">
    <tbody>
        <?php
            $s = -1;
            foreach (collect($this->row)->toArray() as $field => $val) {
                
                if(in_array($field, $this->hidden_fields)) continue;
                $s++;
                if($field == 'response' || $field == 'data' || $field == 'request' ){
                    $json2 = json_encode(json_decode($val), JSON_PRETTY_PRINT);
                    $val =  "<pre>" . $json2 ."<pre/>";
                }
                $attr = $this->attributes[$field];
                $css_class = '';
                if(stripos($attr['th_attr'], 'class=') === false){
                    $css_class = 'class="bg-light"';
                }
                if (stripos($attr['tr_attr'], 'class=') !== false) {
                    $css_class = '';
                }

                ?>
        <tr id="<?php echo $field;?>" <?php echo replace_columns($attr['tr_attr'], $this->row);?>>
            <?php
                    if($attr['hide']) continue;
                    ?>
            <th width="<?php echo ($s == 0) ? $this->table_config['th_width'] : '';?>" <?php echo $css_class;?>
                <?php echo replace_columns($attr['th_attr'], $this->row);?>>
                <?php if(!empty($attr['title'])){
                            echo $attr['title'];
                        }else {
                            echo ucwords(str_replace('_', ' ', $field));
                        } ?>
            </th>
            <td <?php echo replace_columns($attr['td_attr'], $this->row);?>>
                <?php
                        if ($s == 0 && $this->id_field == '') {
                            $this->id_field = $field;
                        }
                        if ($this->id_field == $field && !$id_checkbox) {
                            echo '<input style="display:none;" type="checkbox" name="ids[]" value="' . intval($val) . '" class="chk-id" checked>';
                            $id_checkbox = TRUE;
                        }

                        if (is_array($this->image_fields[$field])) {
                            $image_size = '200x200';
                            if(!empty($this->image_fields[$field]['size'])) {
                                $image_size = $this->image_fields[$field]['size'];
                            }
                            $image_size = explode('x', $image_size);
                            $val = "<a href='" . $this->image_fields[$field]['path'] . "/" . $val . "' data-fancybox=\"gallery\"><img src='" . _img($this->image_fields[$field]['path'] . "/" . $val, $image_size[0], $image_size[1]) . "' alt='{$val}' title='{$val}' class='img-fluid'></a>";
                        }
                        if(isset($attr['wrap']) && is_callable($attr['wrap'])){
                            $val = call_user_func($attr['wrap'], $val, $field, $this->row, $this);
                        }

                        if (array_key_exists($field, $this->custom_func)) {
                            $val = call_user_func($this->custom_func[$field], $val, $this->row, $field, $this);
                        }

                        echo nl2br(stripslashes($val)); ?>
            </td>
        </tr>
        <?php
            }
            ?>
    </tbody>
</table>


<?php
        return $html = ob_get_clean();
    }
}