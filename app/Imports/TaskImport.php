<?php

namespace App\Imports;

use App\Task;
use Maatwebsite\Excel\Concerns\ToModel;

class TaskImport implements ToModel
{
    public function model(array $row)
    {
        $c = 0;
        return new Task([
                    'id' => $row[$c++],
                        'task' => $row[$c++],
                        'project_id' => $row[$c++],
                        'start_date' => $row[$c++],
                        'end_date' => $row[$c++],
                        'estimate_time' => $row[$c++],
                        'status' => $row[$c++],
                        'creator_id' => $row[$c++],
                        'assign_to' => $row[$c++],
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