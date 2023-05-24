<?php

namespace App\Imports;

use App\StaticBlock;
use Maatwebsite\Excel\Concerns\ToModel;

class StaticBlockImport implements ToModel
{
    public function model(array $row)
    {
        $c = 0;
        return new StaticBlock([
                    'id' => $row[$c++],
                        'title' => $row[$c++],
                        'identifier' => $row[$c++],
                        'content' => $row[$c++],
                        'status' => $row[$c++],
                        'created_at' => $row[$c++],
                        'updated_at' => $row[$c++],
                    ]);
    }

    public function headingRow(): int
    {
        return 1;
    }
}