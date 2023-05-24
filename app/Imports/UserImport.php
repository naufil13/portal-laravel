<?php

namespace App\Imports;

use App\User;
use Maatwebsite\Excel\Concerns\ToModel;

class UserImport implements ToModel
{
    public function model(array $row)
    {
        $c = 0;
        return new User([
                    'id' => $row[$c++],
                        'user_type_id' => $row[$c++],
                        'first_name' => $row[$c++],
                        'last_name' => $row[$c++],
                        'email' => $row[$c++],
                        'phone' => $row[$c++],
                        'address' => $row[$c++],
                        'city' => $row[$c++],
                        'photo' => $row[$c++],
                        'status' => $row[$c++],
                        'email_verified_at' => $row[$c++],
                        'username' => $row[$c++],
                        'password' => $row[$c++],
                        'data' => $row[$c++],
                        'remember_token' => $row[$c++],
                        'created_at' => $row[$c++],
                        'updated_at' => $row[$c++],
                    ]);
    }

    public function headingRow(): int
    {
        return 1;
    }
}