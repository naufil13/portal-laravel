<?php

class Breadcrumb
{
    public static $crumbs = [];

    public function set_home($text, $link = null)
    {
        self::$crumbs[0] = ['text' => $text, 'link' => $link];
    }

    public static function add_item($text, $link = null)
    {
        $n = count(self::$crumbs) + 1;
        self::$crumbs[$n] = ['text' => $text, 'link' => $link];
    }

    public static function get_items()
    {
        ksort(self::$crumbs);
        return self::$crumbs;
    }

    public static function display($id = '', $class = '')
    {
        $crumbs = self::get_items();
        return view('admin.layouts.inc.breadcrumb', compact('crumbs'));
    }
}

/* End of file breadcrumb.php */
/* Location: ./application/libraries/breadcrumb.php */
