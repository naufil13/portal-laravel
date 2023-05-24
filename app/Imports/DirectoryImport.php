<?php

namespace App\Imports;

use App\Directory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DirectoryImport implements ToCollection, WithHeadingRow, ToModel
{
    public function collection(Collection $rows)
    {
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

        $row['association_id'] = \App\Association::select('id')->where('name', $row['association_id'])->first()->id;
        $row['member_id'] = \App\Member::select('id')->where('name', trim($row['member_id']))->first()->id;

        $row['data'] = json_decode($row['data'], true);
        $row['data']['from'] = 'csv';
        $row['data'] = json_encode($row['data']);
        if(empty($row['emails'])){
            $row['emails'] = '[]';
        }
        if(empty($row['phones'])){
            $row['phones'] = '[]';
        }
        if(empty($row['mobiles'])){
            $row['mobiles'] = '[]';
        }

        return new Directory([
            'association_id' => intval($row['association_id']),
            'member_id' => intval($row['member_id']),
            'name' => $row['name'],
            'designation' => $row['designation'],
            'comment' => $row['comment'],
            'data' => $row['data'],
            'emails' => str_replace("'", '"', $row['emails']),
            //'mobiles' => str_replace("'", '"', $row['mobiles']),
            'phones' => str_replace("'", '"', $row['phones']),
            //'created_at' => $row[$c++],
            //'updated_at' => $row[$c++],
        ]);
    }
}
