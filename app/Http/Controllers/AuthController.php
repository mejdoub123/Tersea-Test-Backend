<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
        'name'=> 'required|string',
        'email' => 'required|unique:users',
        'cin' => 'required|unique:users',
        'is_admin' => 'required|boolean',
        'phone' => 'required|string',
        'address' => 'required|string',
        'date_of_birth'=>'required|date',
        'company_id' => 'nullable|exist:companies',
        'password' => 'required|string|confirmed'
        ]);


        $user = new User;
        if($request['company_id']){
            $user->company_id = $request->input('company_id');
        }

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->cin = $request->input('cin');
        $user->phone = $request->input('phone');
        $user->address = $request->input('address');
        $user->date_of_birth = $request->input('date_of_birth');
        $user->is_admin = $request->input('is_admin');
        $user->password = bcrypt($request->input('password'));

        $user->save();

        $token = $user->createToken('crudtoken')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'cin' => $user->cin,
                'is_admin' => $user->is_admin,
                'date_of_birth'=>$user->date_of_bith,
                'phone' => $user->phone,
                'address'=> $user->address
            ],
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
         'email' => 'required|exists:users',
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

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'cin' => $user->cin,
                'is_admin' => $user->is_admin,
                'date_of_birth' => $user->date_of_birth,
                'phone' => $user->phone,
                'address'=> $user->address
            ],
            'token' => $token
        ], 200);
    }

    public function update(Request $request,User $user){
        
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|confirmed'
        ]);

        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return response()->json([
                'error' => 'Old password doesn\'t match!'
            ], 401);
        }

        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Password changed successfully!'
        ], 201);
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
