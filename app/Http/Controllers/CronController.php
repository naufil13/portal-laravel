<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CronController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function directory()
    {
        $association = getVar('association');
        $company = getVar('company');
        return \App\Directory::join('members', 'members.id', '=', 'directories.member_id')
            ->join('associations', 'associations.id', '=', 'directories.association_id')
            ->where('members.company', $company)
            ->where('associations.name', $association)
            ->get('directories.*');
    }

}
