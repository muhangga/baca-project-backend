<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ],
        [
            'username.required' => 'Username harus diisi',
            'password.required' => 'Password harus diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => $validator->errors()->first()
            ], 401);
        }

        $credentials = $request->only('username', 'password');

        $user = User::where('username', $request->username)->first();
        if (!$user) {
            return response()->json([
                'status' => 'false',
                'message' => 'Username tidak ditemukan'
            ], 401);
        }

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'false',
                'message' => 'Password salah'
            ], 401);
        }

        return response()->json([
            'status' => 'true',
            'message' => 'Login berhasil',
            'data' => $user
        ], 200);
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users|min:4',
            'password' => 'required|min:6',
            'full_name' => 'required',
        ],
        [
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah terdaftar',
            'username.min' => 'Username minimal 4 karakter',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'full_name.required' => 'Nama lengkap harus diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 401);
        }

        $register = User::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'full_name' => $request->full_name,
        ]);

        if (!$register) {
            return response()->json([
                'success' => false,
                'message' => 'Registrasi gagal',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => $register,
        ], 200);
    }
}
