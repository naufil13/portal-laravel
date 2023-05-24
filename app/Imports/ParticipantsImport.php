<?php

namespace App\Imports;

use App\User;
use App\CsvImport;
use Maatwebsite\Excel\Concerns\ToModel;

class ParticipantsImport implements ToModel
{
    public function model(array $row)
    {
        $c = 0;
        return new CsvImport([
            'first_name' => $row[$c++],
            'last_name' => $row[$c++],
            'date_of_birth' => $row[$c++],
            'phone' => $row[$c++],
            'email' => $row[$c++],
            'type' => $row[$c++],
            'email_hash' => $row[$c++],
            'erx_id' => $row[$c++],
            'subject_id' => $row[$c++],
            'gender' => $row[$c++],
            'sso_tenant_id' => $row[$c++],
            'trial_id' => $row[$c++],
            'token' => $row[$c++],
            'invited_by' => $row[$c++],
            'icf_signed_date' => $row[$c++],
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }
}
