<?php

namespace App\Imports;

use App\Association;
use Maatwebsite\Excel\Concerns\ToModel;

class AssociationImport implements ToModel
{
    public function model(array $row)
    {
        $c = 0;
        return new Association([
                    'id' => $row[$c++],
                        'name' => $row[$c++],
                        'logo' => $row[$c++],
                        'joining_date' => $row[$c++],
                        'website' => $row[$c++],
                        'address' => $row[$c++],
                        'country' => $row[$c++],
                        'city' => $row[$c++],
                        'email' => $row[$c++],
                        'phone' => $row[$c++],
                        'created_at' => $row[$c++],
                        'updated_at' => $row[$c++],
                    ]);
    }

    public function headingRow(): int
    {
        return 1;
    }
}