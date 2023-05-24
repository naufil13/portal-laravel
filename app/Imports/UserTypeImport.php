<?php

namespace App\Imports;

use App\UserType;
use Maatwebsite\Excel\Concerns\ToModel;

class UserTypeImport implements ToModel
{
    public function model(array $row)
    {
        $c = 0;
        return new UserType([
                    'id' => $row[$c++],
                        'user_type' => $row[$c++],
                        'for' => $row[$c++],
                        'level' => $row[$c++],
                        'created_at' => $row[$c++],
                        'updated_at' => $row[$c++],
                    ]);
    }

    public function headingRow(): int
    {
        return 1;
    }
}