<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function updateProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'user_id'=>'required|integer',
                'name'=>'required|string',
                'email'=>'required|email|unique:users,email,'.$request->user_id,
            ]);

            if(!$validator->passes()){
                $response = [
                    'status' => 0,
                    'error' => $validator->errors()->toArray(),
                ];
                return response()->json($response);
            }
            else{
                $user = User::find($request->user_id);
                $user->name = $request->name;
                $user->email = $request->email;
                if($user->isDirty()){
                    $response = [
                        'status' => 1,
                        'message' => 'Changes Updated Successfully!'
                    ];
                }else{
                    $response = [
                        'status' => 2,
                        'message' => 'No Changes Made!'
                    ];
                }
                $user->save();
                return response()->json($response);
            }
        } catch (\Throwable $th) {
            if(config('app.debug')){
                $message = $th->getMessage();
            }
            else{
                $message = config('app.errorMessage');
            }
            $response = [
                'status' => 2,
                'message' => $message
            ];
            return response()->json($response);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'user_id'=>'required|integer',
                'password'=>['required', Password::min(8)->mixedCase()->letters()->numbers()->symbols()],
                'confirm_password'=>'required|same:password',
            ]);

            if(!$validator->passes()){
                $response = [
                    'status' => 0,
                    'error' => $validator->errors()->toArray(),
                ];
                return response()->json($response);
            }
            else{
                $user = User::find($request->user_id);
                $user->password = Hash::make($request->password);
                $user->save();
                $response = [
                    'status' => 1,
                    'message' => "Passowrd changed successfully!",
                ];
                return response()->json($response);
            }
        } catch (\Throwable $th) {
            if(config('app.debug')){
                $message = $th->getMessage();
            }
            else{
                $message = config('app.errorMessage');
            }
            $response = [
                'status' => 2,
                'message' => $message
            ];
            return response()->json($response);
        }
    }

}

