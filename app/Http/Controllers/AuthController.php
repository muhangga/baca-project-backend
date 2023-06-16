<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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

        $user = User::where('username', $request->username)->first();
        if (!$user) {
            return response()->json([
                'status' => 'false',
                'message' => 'Username tidak ditemukan'
            ], 401);
        }

        $credentials = $request->only('username', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'false',
                'message' => 'Password salah'
            ], 401);
        }

        $token = $user->createToken($user->username)->plainTextToken;
        return response()->json([
            'status' => 'true',
            'message' => 'Login berhasil',
            'data' => $user,
            'token' => $token
        ], 200);
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users|min:4',
            'password' => 'required|min:6',
            'full_name' => 'required',
            'address' => 'required',
            'school_name' => 'required',
        ],
        [
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah terdaftar',
            'username.min' => 'Username minimal 4 karakter',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'full_name.required' => 'Nama lengkap harus diisi',
            'address.required' => 'Alamat harus diisi',
            'school_name.required' => 'Nama sekolah harus diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 401);
        }

        $dt = Carbon::now();

        $register = User::create([
            'full_name' => $request->full_name,
            'address' => $request->address,
            'school_name' => $request->school_name,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'created_at' => $dt->toDateTimeString(),
            'updated_at' => $dt->toDateTimeString(),
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
        ], 201);
    }
}
