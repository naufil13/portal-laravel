<?php

namespace App\Imports;

use App\Member;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

//class MemberImport implements ToModel
class MemberImport implements ToCollection, WithHeadingRow, ToModel
{
    public function collection(Collection $rows)
    {
        dd($rows);
        foreach ($rows as $c => $row) {
            if($c > ($this->headingRow() - 1)){
                return $this->model($row->toArray());
            }
        }
    }

    public function headingRow(): int
    {
        return 1;
    }

    /**
     * @inheritDoc
     */
    public function model(array $row)
    {


        $row['association_id'] = \App\Association::where('name', $row['association_id'])->first()->id;
        $row['data'] = json_decode($row['data'], true);
        $row['data']['from'] = 'csv';
        $row['data'] = json_encode($row['data']);
        if(empty($row['data'])) {
            $row['data'] = "[]";
        }

        $row['emails'] = (empty($row['emails']) ? "[]" : str_replace("'", '"', $row['emails']));
        $row['phones'] = (empty($row['phones']) ? "[]" : str_replace("'", '"', $row['phones']));

        return new Member([
            'association_id' => intval($row['association_id']),
            'name' => $row['name'],
            'logo' => $row['logo'],
            'joining_date' => ($row['joining_date']),
            'company' => ($row['company'] != '' ? $row['company'] : $row['name']),
            'website' => $row['website'],
            'address' => $row['address'],
            'country' => $row['country'],
            'city' => $row['city'],
            'data' => $row['data'],
            'emails' => $row['emails'],
            'phones' => $row['phones'],
            'created_at' => sqlDtaTime(),
            'updated_at' => sqlDtaTime(),
        ]);
    }
}
