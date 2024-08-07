<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getUsers()
    {
        try {
            $status = 1;
            $message = "Success";
            $users = User::get();

            if ($users->isEmpty()) {
                $status = 0;
                $message = "No users found.";
            }

            $response = [
                'status' => $status,
                'message' => $message,
                'data' => $users,
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error retrieving users: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function addUser(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:100',
                'email'=>'required|email|unique:users',
                'password'=>['required', Password::min(8)->mixedCase()->letters()->numbers()->symbols()],
            ]);

            if (!$validator->passes()) {
                $response = [
                    'status' => 0,
                    'message' => $validator->errors()->toArray(),
                ];
                return response()->json($response);
            }

            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password =  Hash::make($request->password);
            $user->save();

            $response = [
                'status' => 1,
                'message' => "User Created Successfully!",
                'data' => $user,
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error creating user: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function updateUser($userId, Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:100',
                'email'=>'required|email|unique:users,email,'.$userId,
                'password'=>[Password::min(8)->mixedCase()->letters()->numbers()->symbols()],
            ]);

            if (!$validator->passes()) {
                $response = [
                    'status' => 0,
                    'message' => $validator->errors()->toArray(),
                ];
                return response()->json($response);
            }

            $user = User::find($userId);

            if (!$user) {
                return response()->json([
                    'status' => 0,
                    'message' => 'User not found!',
                ]);
            }

            $user->name = $request->name;
            $user->email = $request->email;
            if(isset($request->password)){
                $user->password = $request->password;
            }

            if ($user->isDirty()) {
                $status = 1;
                $message = 'User Updated Successfully!';
            } else {
                $status = 0;
                $message = 'No Changes Made!';
            }

            $user->save();

            $response = [
                'status' => $status,
                'message' => $message,
                'data' => $user,
            ];

            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error updating user: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function statusUpdateUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'is_active' => 'required|in:0,1',
                'user_id' => 'required',
            ]);

            if (!$validator->passes()) {
                return response()->json([
                    'status' => 0,
                    'message' => $validator->errors()->toArray(),
                ]);
            }

            $user = User::find($request->user_id);

            if (!$user) {
                return response()->json([
                    'status' => 0,
                    'message' => 'User not found!',
                ]);
            }

            if ($user->is_active != $request->is_active) {
                $user->is_active = $request->is_active;
                $user->save();

                $status = 1;
                $message = 'User status updated successfully!';
            } else {
                $status = 0;
                $message = 'No Changes Made!';
            }

            $response = [
                'status' => $status,
                'message' => $message,
                'data' => $user,
            ];

            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error updating user status: ' . $th->getMessage());
            return response()->json([
                'status' => 0,
                'message' => $th->getMessage(),
            ]);
        }
    }
}
