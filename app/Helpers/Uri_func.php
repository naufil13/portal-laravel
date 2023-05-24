<?php

class Uri_func
{
    public static function apostrpheReplace($where)
    {
        // dd($where);
        //capture position of first quote from like query
        preg_match_all("/'/", $where, $matches, PREG_OFFSET_CAPTURE);

        //from like capture string
        $sub = substr($where, $matches[0][0][1], -1);

        //Remove first and last char which is '
        $string = substr($sub, 0, -3);
        $string = substr($string, 1);

        //replace it with ''
        $new_string = str_replace("'", "''", $string);

        //Taking the orginal string
        $old_string = substr($where, 0, 22);

        //Merge both strings
        $merge = $old_string . " " . $new_string;
        $merge = $merge . " ";

        // Add " in LIKE
        $rmv = str_replace(" %", " '%", $merge);
        $where = str_replace("% ", "%'", $rmv);
        // dd($where);
        return $where;
    }
}
