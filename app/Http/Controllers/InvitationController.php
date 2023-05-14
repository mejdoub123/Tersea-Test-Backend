<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailInvitation;
use App\Models\Company;
use App\Models\History;
use Exception;

class InvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admin = auth()->user();
        $invitations = $admin->invitations()->get();
        return response()->json([
            'invitations' => $invitations,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "email" => "required|email|unique:users,email|unique:invitations,employee_email",
            "name" => "required|string",
            "link" => "required|string",
            "company_id" => "required|exists:companies,id",
        ]);

        $data = [
            "email" => $request->input("email"),
            "name" => $request->input("name"),
            "link" => $request->input("link"),
            "company_id" => $request->input("company_id"),
        ];

        $invitation = new Invitation;
        $invitation->employee_name = $request->input("name");
        $invitation->employee_email = $request->input("email");
        $invitation->sender()->associate(auth()->user()->id);
        $invitation->company()->associate($request->input("company_id"));

        $company = Company::where('id', $request->input("company_id"))->first();

        $history = new History;
        $history->name = 'Invitation';
        $history->content = 'Admin "' . auth()->user()->name .
            '" has invited the employee "' .
            $request->input("name") .
            '" to join the company "' .
            $company->name . '"';
        $history->user()->associate(auth()->user()->id);
        try {
            Mail::to($data['email'])->send(new MailInvitation($data));
            $invitation->save();
            $history->save();
            return response()->json([
                'invitation' => $invitation,
                'history' => $history
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Sorry! Please try again latter'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invitation $invitation)
    {
        //
    }
}
