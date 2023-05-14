<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\History;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'cin' => 'required|unique:users',
            'is_admin' => 'required|boolean',
            'phone' => 'required|string',
            'address' => 'required|string',
            'date_of_birth' => 'required|date',
            'company_id' => 'nullable|exists:companies,id',
            'password' => 'required|string|confirmed'
        ]);


        $user = new User;

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->cin = $request->input('cin');
        $user->phone = $request->input('phone');
        $user->address = $request->input('address');
        $user->date_of_birth = $request->input('date_of_birth');
        $user->is_admin = $request->input('is_admin');
        $user->password = bcrypt($request->input('password'));

        if ($request['company_id']) {

            $user->company_id = $request->input('company_id');

            $invitation = Invitation::where('employee_email', $request->input('email'))->first();

            $invitationConfirmation = new History;
            $profileConfirmation = new History;

            if ($invitation) {
                $request->validate([
                    'name' => 'required|exists:invitations,employee_name',
                    'email' => 'required|exists:invitations,employee_email',
                ]);
                $invitation->is_accepted = true;
                $invitation->accepted_at = now();

                $invitationConfirmation->name = "Invitation Confirmation";
                $invitationConfirmation->content = '"' . $request->input('name') . '" has accepted the invitation.';

                $user->email_verified_at = now();
                $user->save();

                $company = Company::where('id', $request->input('company_id'))->first();

                $invitationConfirmation->user()->associate($company->admin_id);

                $invitation->employee()->associate($user->id);

                $invitation->save();

                $invitationConfirmation->save();

                $profileConfirmation->name = "Profile Confirmation";
                $profileConfirmation->content = '"' . $request->input('name') . '" has confirm his profile.';
                $profileConfirmation->user()->associate($company->admin_id);
                $profileConfirmation->save();
            } else {
                return response()->json([
                    'message' => 'This invitation are not valid !'
                ], 400);
            }
        } else {
            $user->save();
        }

        $token = $user->createToken('crudtoken')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'cin' => $user->cin,
                'is_admin' => $user->is_admin,
                'date_of_birth' => $user->date_of_birth,
                'phone' => $user->phone,
                'address' => $user->address
            ],
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $email = $request->input("email");
        $password = $request->input("password");
        //Check email
        $user = User::where('email', $email)->first();
        //Check password
        if (!Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'Please enter a valid email or password'
            ], 401);
        }
        $token = $user->createToken('crudtoken')->plainTextToken;
        if (!$user->is_admin) {
            $company = $user->company()->first();
            $admin = $company->admin()->first();
            $employees = $company->employees()->get();
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'cin' => $user->cin,
                    'company' => [
                        'company_infos' => $company,
                        'company_admin' => $admin,
                        'company_employees' => $employees,
                    ],
                    'is_admin' => $user->is_admin,
                    'date_of_birth' => $user->date_of_birth,
                    'phone' => $user->phone,
                    'address' => $user->address
                ],
                'token' => $token
            ], 200);
        } else
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'cin' => $user->cin,
                    'is_admin' => $user->is_admin,
                    'date_of_birth' => $user->date_of_birth,
                    'phone' => $user->phone,
                    'address' => $user->address
                ],
                'token' => $token
            ], 200);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email',
            'cin' => 'nullable|unique:users,cin',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        if ($request->input('name')) {
            $user->name = $request->input('name');
        }
        if ($request->input('email')) {
            $user->email = $request->input('email');
        }
        if ($request->input('cin')) {
            $user->cin = $request->input('cin');
        }
        if ($request->input('address')) {
            $user->address = $request->input('address');
        }
        if ($request->input('phone')) {
            $user->phone = $request->input('phone');
        }

        $user->save();

        return response()->json([
            'user' => $user,
        ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logged out'
        ], 201);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->noContent();
    }
}
