<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request){

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ],[
            'name.required' => 'nama tidak boleh kosong',
            'name.string' => 'nama harus diisi dengan string',
            'name.max' => 'nama max 255 karakter',
            'email.required' => 'email tidak boleh kosong',
            'email.string' => 'email harus diisi dengan string',
            'email.email' => 'format harus diisi dengan email',
            'email.max' => 'email max 255 karakter',
            'email.unique' => 'email sudah ada',
            'password.required' => 'password tidak boleh kosong',
            'password.string' => 'password harus diisi dengan string',
            'password.min' => 'password min 4 karakter',
            'password.confirmed' => 'password tidak sama'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'User berhasil dibuat',
            'user' => $user
        ], 200);

    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ],[
            'email.required' => 'nama tidak boleh kosong',
            'password.required' => 'alamat tidak boleh kosong'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email dan password salah'    
            ], 401);
        }

        $token = $user->createToken('token')->plainTextToken;
        return response()->json([
            'message' => 'Success Login',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'berhasil logout'
        ], 200);
    }
}
