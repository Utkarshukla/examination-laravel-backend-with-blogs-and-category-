<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactForm extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validateData=$request->validate([
            'name'=>'required',
            'email'=>'required|email',
            'message'=>'required|string'
        ]);
        $adminMail = 'shukla@phantasm.co.in';
        $data= $validateData;
        // print_r($data);
        try {
            Mail::to($adminMail)->send(new ContactMail($data));
            return response()->json(['status'=>'success', 'message'=>'Your Massage Successfully Recieved']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failure','message'=>'Fail to send your Message']);
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
