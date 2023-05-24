<?php

class Multilevel
{

    var $query;
    var $selected;
    var $type = 'menu';
    var $flat_array = true; // Single level
    var $parent = 0;
    var $url;
    var $level_spacing = 15;
    var $spacing_str = '&nbsp;';

    var $id_Column = 'id';
    var $parent_Column = 'parent_id';
    var $title_Column = 'title';
    var $link_Column = 'slug';

    var $call_func;

    var $title_position = 'right';
    var $attrs;

    var $option_html = '<option {selected} value="{key}">{val}</option>';

    var $parent_li_start = "<li class='{active_class}'>\n  <a class='expand' href='{href}'><i class='icon-th-large'></i>{title}{has_child}</a>\n";
    var $parent_li_end = '</li>';

    var $child_ul_start = "<ul>\n";
    var $child_ul_end = "</ul>\n";
    var $child_li_start = "<li class='{active_class}'>\n  <a href='{href}'><i class='icon-th-large'></i>{title}{has_child}</a>\n";
    var $child_li_end = '</li>';

    var $default_active = 'home';
    var $active_class = '';
    var $active_link = '';

    var $has_child_html = '';

    var $search = array();
    var $replace = array();

    public function __construct()
    {
        //$this->url = site_url(getUri(1) . "/" . getUri(2));
    }


    function build()
    {
        $rows = json_decode(json_encode(DB::select($this->query)), true);
        $menu = [
            'items' => [],
            'parents' => []
        ];

        foreach ($rows as $items) {
            $menu['items'][$items[$this->id_Column]] = $items;
            $menu['parents'][$items[$this->parent_Column]][] = $items[$this->id_Column];
        }

        if ($this->type == 'select') {
            return $this->buildSelect($this->parent, $menu, 0);
        } elseif (in_array($this->type, ['checkbox', 'jstree'])) {
            return $this->buildCheckBox($this->parent, $menu, 0);
        } elseif (in_array($this->type, ['tree'])) {
            return $this->tree_nodes($this->parent, $menu, 0);
        } elseif ($this->type == 'child') {
            return $this->getChild($this->parent, $menu, 1);
        } elseif ($this->type == 'array') {
            return $menu;
        } else {
            return $this->buildMenu($this->parent, $menu);
        }
    }

    function tree_nodes($parent, $menu)
    {
        $nodes = [];
        if (isset($menu['parents'][$parent])) {
            foreach ($menu['parents'][$parent] as $itemId) {
                if (!isset($menu['parents'][$itemId])) {
                    $nodes[$itemId] = $menu['items'][$itemId];
                }
                if (isset($menu['parents'][$itemId])) {
                    $nodes[$itemId] = $menu['items'][$itemId];
                    $nodes[$itemId]['children'] = $this->tree_nodes($itemId, $menu);
                }
            }
        }

        return $nodes;
    }

    function getChild($parent, $menu)
    {

        $ids = array();
        if (isset($menu['parents'][$parent])) {
            foreach ($menu['parents'][$parent] as $itemId) {
                if (!isset($menu['parents'][$itemId])) {
                    if($this->flat_array){
                        $ids[] =  $menu['items'][$itemId][$this->id_Column];
                    } else
                    $ids[$itemId] =  $menu['items'][$itemId][$this->id_Column];
                }
                if (isset($menu['parents'][$itemId])) {
                    if($this->flat_array){
                        $ids[] = $menu['items'][$itemId][$this->id_Column];
                        $ids[] = $this->getChild($itemId, $menu);
                    } else {
                        $ids[$itemId] = $menu['items'][$itemId][$this->id_Column];
                        $ids[$itemId] = $this->getChild($itemId, $menu);
                    }

                }
            }
        }

        if($this->flat_array){
            return $this->array_flatten($ids);
        }
        return $ids;

    }

    function array_flatten($array)
    {

        if (!is_array($array)) {
            return FALSE;
        }
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result[] = $key;
                $result = array_merge($result, $this->array_flatten($value));

            } else {
                $result[] = $value;
            }
        }
        return $result;
    }

    function buildCheckBox($parent, $menu)
    {
        $html = "";
        if (isset($menu['parents'][$parent])) {
            $html .= "<ul class='multi-ul'>\n";
            foreach ($menu['parents'][$parent] as $itemId) {
                //$level++;
                $tree_options = $option_selected = '';

                if (is_array($this->selected)) {
                    if (in_array($menu['items'][$itemId][$this->id_Column], $this->selected)) {
                        if($this->type == 'jstree'){
                            $tree_options = " data-jstree='{\"opened\": true, \"selected\":true}'";
                        }
                        $option_selected = 'checked';
                    }
                } else {
                    if ($menu['items'][$itemId][$this->id_Column] == $this->selected) {
                        if($this->type == 'jstree'){
                            $tree_options = " data-jstree='{\"opened\": true, \"selected\":true}'";
                        }
                        $option_selected = 'checked';
                    }
                }

                if (!isset($menu['parents'][$itemId])) {
                    $li = "<li ".$tree_options." class='multi_checkbox checkbox_li_" . $menu['items'][$itemId][$this->id_Column] . "'>" . (($this->title_position != 'right') ? $menu['items'][$itemId][$this->title_Column] : '');
                    $li .= "\n  <input " . $this->buildAttributes() . $option_selected . " type='checkbox' value='" . $menu['items'][$itemId][$this->id_Column] . "'> \n";
                    $li .= (($this->title_position == 'right') ? $menu['items'][$itemId][$this->title_Column] : '') . "\n";
                    $li .= "</li>\n";

                    if (is_callable($this->call_func)) {
                        $html .= call_user_func($this->call_func, $menu['items'][$itemId], $li, $this->selected);
                    } else {
                        $html .= $li;
                    }
                }
                if (isset($menu['parents'][$itemId])) {
                    $li = "<li ".$tree_options." class='multi_checkbox has_parent checkbox_li_" . $menu['items'][$itemId][$this->id_Column] . "'>" . (($this->title_position != 'right') ? $menu['items'][$itemId][$this->title_Column] : '');
                    $li .= "\n  <input " . $this->buildAttributes() . $option_selected . " type='checkbox' value='" . $menu['items'][$itemId][$this->id_Column] . "'> \n";
                    $li .= (($this->title_position == 'right') ? $menu['items'][$itemId][$this->title_Column] : '');
                    $li .= $this->buildCheckBox($itemId, $menu);
                    $li .= "</li> \n";

                    if (is_callable($this->call_func)) {
                        $html .= call_user_func($this->call_func, $menu['items'][$itemId], $li, $this->selected);
                    } else {
                        $html .= $li;
                    }
                }
            }
            $html .= "</ul> \n";
        }

        return $html;
    }



    function buildSelect($parent, $menu, $level)
    {
        $html = "";
        if (isset($menu['parents'][$parent])) {
            //$html .= "\n";
            foreach ($menu['parents'][$parent] as $itemId) {
                $option_selected = '';
                if (is_array($this->selected)) {
                    if (in_array($menu['items'][$itemId][$this->id_Column], $this->selected)) {
                        $option_selected = 'selected';
                    }
                } else {
                    if ($menu['items'][$itemId][$this->id_Column] == $this->selected) {
                        $option_selected = 'selected';
                    }
                }
                if (!isset($menu['parents'][$itemId])) {
                    $option = (object) $menu['items'][$itemId];
                    $option->selected = $option_selected;
                    $option->key = $menu['items'][$itemId][$this->id_Column];
                    $option->level = str_repeat ($this->spacing_str, ($level) * $this->level_spacing);
                    $option->val = $option->level . $menu['items'][$itemId][$this->title_Column];
                    $html .= replace_columns($this->option_html, $option);

                    //$html .= "<option $option_selected value='" . $menu['items'][$itemId][$this->id_Column] . "'>" . nbs(($level) * $this->level_spacing) . $menu['items'][$itemId][$this->title_Column] . "</option>\n";
                    if (!empty($this->call_func)) {
                        $html .= call_user_func($this->call_func, $menu['items'][$itemId], $this->selected);
                    }
                }

                if (isset($menu['parents'][$itemId])) {
                    //echo $level;

                    $option = (object) $menu['items'][$itemId];
                    $option->selected = $option_selected;
                    $option->key = $menu['items'][$itemId][$this->id_Column];
                    $option->level = str_repeat ($this->spacing_str, ($level) * $this->level_spacing);
                    $option->val = $option->level . $menu['items'][$itemId][$this->title_Column];
                    $html .= replace_columns($this->option_html, $option);

                    //$html .= "<option $option_selected value='" . $menu['items'][$itemId][$this->id_Column] . "'>" . nbs($level * $this->level_spacing) . $menu['items'][$itemId][$this->title_Column] . "</option>\n";
                    if (!empty($this->call_func)) {
                        $html .= call_user_func($this->call_func, $menu['items'][$itemId], $this->selected);
                    }
                    $html .= $this->buildSelect($itemId, $menu, ($level + 1));
                    //$level--;
                }
            }
            //$html .= "\n";
        }
        return $html;
    }

    // Menu builder function, parentId 0 is the root
    function buildMenu($parent, $menu)
    {
        $html = "";
        if (isset($menu['parents'][$parent])) {
            foreach ($menu['parents'][$parent] as $itemId) {
                $href = '#';
                $menu_title = '';
                $active = '';
                if (!isset($menu['parents'][$itemId])) {

                    /*-------------------------------------------------*/
                    $menu['items'][$itemId]['href'] = $this->url . ($menu['items'][$itemId][$this->link_Column]);
                    $menu['items'][$itemId]['title'] = stripslashes($menu['items'][$itemId][$this->title_Column]);
                    $menu['items'][$itemId]['has_child'] = $this->has_child_html;
                    $menu['items'][$itemId]['params'] = json_decode(html_entity_decode($menu['items'][$itemId]['params'], ENT_QUOTES), true);

                    if (!empty($this->active_link) && $this->active_link == $menu['items'][$itemId][$this->link_Column]) {
                        $active = $this->active_class;
                        //$this->default_active = $menu['items'][$itemId][$this->link_Column];
                    }
                    if(empty($active) && strtolower($menu['items'][$itemId][$this->title_Column]) == $this->default_active){
                        $active = $this->active_class;
                    }
                    $menu['items'][$itemId]['active_class'] = $active;
                    if (count($this->search) > 0) {
                        foreach ($this->search as $k => $s) {
                            $menu['items'][$itemId][$k] = $s;
                        }
                    }

                    /*-------------------------------------------------*/
                    $parent_html = $this->child_li_start;
                    foreach($menu['items'][$itemId] as $key => $val){
                        if(is_array($val)){
                            foreach ($val as $a_key => $a_val) {
                                $parent_html = str_replace('{' . $key . '.' . $a_key . '}', $a_val, $parent_html);
                            }
                        }else {
                            $parent_html = str_replace('{' . $key . '}', $val, $parent_html);
                        }
                    }

                    $html .= $parent_html;
                    if (!empty($this->call_func)) {
                        $html = call_user_func($this->call_func, $menu['items'][$itemId], $this->selected, $html);
                    }
                    $html .= $this->parent_li_end;

                }
                if (isset($menu['parents'][$itemId])) {
                    /*-------------------------------------------------*/
                    $menu['items'][$itemId]['href'] = $this->url . $menu['items'][$itemId][$this->link_Column];
                    $menu['items'][$itemId]['title'] = stripslashes($menu['items'][$itemId][$this->title_Column]);
                    $menu['items'][$itemId]['has_child'] = $this->has_child_html;
                    $menu['items'][$itemId]['params'] = json_decode(html_entity_decode($menu['items'][$itemId]['params'], ENT_QUOTES), true);

                    if (!empty($this->active_link) && $this->active_link == $menu['items'][$itemId][$this->link_Column]) {
                        $active = $this->active_class;
                    }

                    if(empty($active) && strtolower($menu['items'][$itemId][$this->title_Column]) == $this->default_active){
                        $active = $this->active_class;
                    }
                    $menu['items'][$itemId]['active_class'] = $active;
                    if (count($this->search) > 0) {
                        foreach ($this->search as $k => $s) {
                            $menu['items'][$itemId][$k] = $s;
                        }
                    }
                    /*-------------------------------------------------*/

                    $parent_html = $this->parent_li_start;
                    foreach($menu['items'][$itemId] as $key => $val){
                        if(is_array($val)){
                            foreach ($val as $a_key => $a_val) {
                                $parent_html = str_replace('{' . $key . '.' . $a_key . '}', $a_val, $parent_html);
                            }
                        }else {
                            $parent_html = str_replace('{' . $key . '}', $val, $parent_html);
                        }
                    }

                    /*$parent_html = str_replace(array('{href}', '{link_text}', '{title}', '{has_child}', '{active_class}'), array($href, $menu_title, $menu_title, $this->has_child_html, $active), $this->child_li_start);
                    if (count($this->search) > 0) {
                        foreach ($this->search as $k => $s) {
                            $parent_html = str_replace($s, $menu['items'][$itemId][$this->replace[$k]], $parent_html);
                        }
                    }*/
                    $html .= $parent_html;
                    if (!empty($this->call_func)) {
                        $html = call_user_func($this->call_func, $menu['items'][$itemId], $this->selected, $html);
                    }

                    $html .= $this->child_ul_start;
                    $html .= $this->buildMenu($itemId, $menu);
                    $html .= $this->child_ul_end;
                    $html .= $this->child_li_end;

                }
            }
        }
        return $html;
    }


    function buildAttributes()
    {
        if (count($this->attrs) && is_array($this->attrs)) {
            $attributes = '';
            foreach ($this->attrs as $key => $attr) {
                $attributes .= $key . '="' . $attr . '" ';
            }
            return $attributes;
        } else {
            return $this->attrs;
        }
    }
}
