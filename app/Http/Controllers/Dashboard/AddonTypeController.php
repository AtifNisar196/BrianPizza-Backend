<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AddonType;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddonTypeController extends Controller
{
    public function getAddonTypes()
    {
        try {
            $status = 1;
            $message = "Success";
            $addonTypes = AddonType::get();

            if ($addonTypes->isEmpty()) {
                $status = 0;
                $message = "No Addon Types found.";
            }

            $response = [
                'status' => $status,
                'message' => $message,
                'data' => $addonTypes,
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error retrieving addon types: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function addAddonType(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:100',
            ]);

            if (!$validator->passes()) {
                $response = [
                    'status' => 0,
                    'message' => $validator->errors()->toArray(),
                ];
                return response()->json($response);
            }

            $addonType = new AddonType;
            $addonType->name = $request->name;
            $addonType->save();

            $response = [
                'status' => 1,
                'message' => "Addon Type Created Successfully!",
                'data' => $addonType,
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error creating addon types: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function updateAddonType($addonTypeId, Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:100'
            ]);

            if (!$validator->passes()) {
                $response = [
                    'status' => 0,
                    'message' => $validator->errors()->toArray(),
                ];
                return response()->json($response);
            }

            $addonType = AddonType::find($addonTypeId);
            $addonType->name = $request->name;

            if ($addonType->isDirty()) {
                $status = 1;
                $message = 'Addon Type Updated Successfully!';
            } else {
                $status = 0;
                $message = 'No Changes Made!';
            }

            $addonType->save();

            $response = [
                'status' => $status,
                'message' => $message,
                'data' => $addonType,
            ];

            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error updating addon type: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function statusUpdateAddonType(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:0,1',
                'category_id' => 'required|exists:addon_types,id',
            ]);

            if (!$validator->passes()) {
                return response()->json([
                    'status' => 0,
                    'message' => $validator->errors()->toArray(),
                ]);
            }

            $addonType = AddonType::find($request->category_id);

            if (!$addonType) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Addon Type not found!',
                ]);
            }

            if ($addonType->status != $request->status) {
                $addonType->status = $request->status;
                $addonType->save();

                $status = 1;
                $message = 'Addon Type status updated successfully!';
            } else {
                $status = 0;
                $message = 'No Changes Made!';
            }

            $response = [
                'status' => $status,
                'message' => $message,
                'data' => $addonType,
            ];

            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error updating addon type status: ' . $th->getMessage());
            return response()->json([
                'status' => 0,
                'message' => $th->getMessage(),
            ]);
        }
    }
}
