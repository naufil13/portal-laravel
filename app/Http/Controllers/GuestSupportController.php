<?php

namespace App\Http\Controllers;

use DB;
use Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuestSupportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('guest_support.form');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function dummy()
    {
        try {
            Mail::send(['dummy'], [], function ($message) {
                $message->from("muhammad.muneeb@lathran.com", "muhammad.muneeb@lathran.com");
                $message->to("mohammadmuneeb02@gmail.com")->subject("Dummy Email");
            });
            return "done";
           } catch(\Swift_TransportException $e){
              if($e->getMessage()) {
                 dd($e->getMessage());
              }
           }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {

        $validator = Validator::make(request()->all(), [
            'name' => 'required|max:255',
            'company_name' => 'required|max:255',
            'company_email' => 'required|max:255',
            'phone_no' => 'required|max:255',
            'issue' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $name = explode(" ", request()->name);
        $first_name = $name[0];
        $last_name = $name[1];
        $email = request()->company_email;
        $guest_issue = request()->issue;
        $desc = request()->issue_desc;
        $contact = request()->phone_no;
        $files = request()->file;
        $ticket_number = 'TN-' . random_int(100000, 999999);

        $get_issue = DB::table('guest_support_issues')
            ->select('name')
            ->where('id', $guest_issue)
            ->first();

        $curr_id = DB::table('help_desks')->insertGetId([
            'first_name' => $first_name,
            'last_name' =>  $last_name,
            'ticket_source' => '1',
            'ticket_number' => $ticket_number,
            'ticket_creator' => $email,
            'ticket_owner' => '1',
            'contact' => $contact,
            'ticket_type' => 'guest_ticket',
            'ticket_title' => $get_issue->name,
            'ticket_description' =>  $desc,
            'guest_issue_id' => $guest_issue,
            'created_by' => $email
        ]);

        /**‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
         * | File Upload
         *‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒*/
        if (request()->hasfile('file')) {

            foreach (request()->file('file') as $col => $file) {

                $name = time() . '.' . $file->extension();
                $name = $file->getClientOriginalName();
                $file->move('assets/admin/media/helpdesk', $name);
                $uploaded_file[$col] = $name;

                DB::table('helpdesk_files')->insert([
                    'submitted_from' => 'guest_form',
                    'ticket_id' => $curr_id,
                    'ticket_number' => $ticket_number,
                    'submitted_from' => 'ticket_form',
                    'filename' =>  $name,
                    'created_by' => $email,
                ]);
            }
        }
        /**‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
         * | File Upload Ends
         *‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒*/
        if ($curr_id) {
            return view('guest_support.thanks');
        } else {
            return redirect()->back()->withErrors("Some Issue Occured During Submission")->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
