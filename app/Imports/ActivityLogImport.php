<?php

namespace App\Imports;

use App\ActivityLog;
use Maatwebsite\Excel\Concerns\ToModel;

class ActivityLogImport implements ToModel
{
    public function model(array $row)
    {
        $c = 0;
        return new ActivityLog([
                    'id' => $row[$c++],
                        'activity' => $row[$c++],
                        'table' => $row[$c++],
                        'user_id' => $row[$c++],
                        'user_ip' => $row[$c++],
                        'user_agent' => $row[$c++],
                        'rel_id' => $row[$c++],
                        'current_URL' => $row[$c++],
                        'description' => $row[$c++],
                        'created_at' => $row[$c++],
                        'updated_at' => $row[$c++],
                    ]);
    }

    public function headingRow(): int
    {
        return 1;
    }
}