<?php

namespace App\Imports;

use App\Module;
use Maatwebsite\Excel\Concerns\ToModel;

class ModuleImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $c = 0;
        return new Module([
            'module' => $row[$c++],
            'title' => $row[$c++],
            'parent_id' => $row[$c++],
            'icon' => $row[$c++],
            'image' => $row[$c++],
            'ordering' => $row[$c++],
            'show_on_menu' => $row[$c++],
            'actions' => $row[$c++],
            'status' => $row[$c++],

            //'password' => Hash::make($row[2]),
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }
}
