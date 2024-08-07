<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\VariationType;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VariationTypeController extends Controller
{
    public function getVariationTypes()
    {
        try {
            $status = 1;
            $message = "Success";
            $variationTypes = VariationType::get();

            if ($variationTypes->isEmpty()) {
                $status = 0;
                $message = "No Variation Types found.";
            }

            $response = [
                'status' => $status,
                'message' => $message,
                'data' => $variationTypes,
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error retrieving variation types: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function addVariationType(Request $request)
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

            $variationType = new VariationType;
            $variationType->name = $request->name;
            $variationType->save();

            $response = [
                'status' => 1,
                'message' => "Variation Type Created Successfully!",
                'data' => $variationType,
            ];
            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error creating variation types: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function updateVariationType($variationTypeId, Request $request)
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

            $variationType = VariationType::find($variationTypeId);

            if (!$variationType) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Variation Type not found!',
                ]);
            }

            $variationType->name = $request->name;

            if ($variationType->isDirty()) {
                $status = 1;
                $message = 'Variation Type Updated Successfully!';
            } else {
                $status = 0;
                $message = 'No Changes Made!';
            }

            $variationType->save();

            $response = [
                'status' => $status,
                'message' => $message,
                'data' => $variationType,
            ];

            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error updating variation type: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }

    public function statusUpdateVariationType(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:0,1',
                'variation_type_id' => 'required',
            ]);

            if (!$validator->passes()) {
                return response()->json([
                    'status' => 0,
                    'message' => $validator->errors()->toArray(),
                ]);
            }

            $variationType = VariationType::find($request->variation_type_id);

            if (!$variationType) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Variation Type not found!',
                ]);
            }

            if ($variationType->status != $request->status) {
                $variationType->status = $request->status;
                $variationType->save();

                $status = 1;
                $message = 'Variation Type status updated successfully!';
            } else {
                $status = 0;
                $message = 'No Changes Made!';
            }

            $response = [
                'status' => $status,
                'message' => $message,
                'data' => $variationType,
            ];

            return response()->json($response);
        } catch (\Throwable $th) {
            Log::error('Error updating variation type status: ' . $th->getMessage());
            return response()->json([
                'status' => 0,
                'message' => $th->getMessage(),
            ]);
        }
    }
}
