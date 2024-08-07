<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ImageUploadController extends Controller
{
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 0,
                'message' => $validator->errors()->toArray(),
            ];
            return response()->json($response);
        }

        try {
            if ($request->hasFile('file')) {

                $file = $request->file('file');
                $destinationPath = public_path('uploads');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move($destinationPath, $fileName);

                $path = 'uploads/' . $fileName;
                $url = url('uploads/' . $fileName);

                $response = [
                    'status' => 1,
                    'message' => "Image Uploaded Successfully!",
                    'url' => $path,
                ];
                return response()->json($response);
            } else {
                $response = [
                    'status' => 0,
                    'message' => "No file uploaded.",
                ];
                return response()->json($response);
            }
        } catch (\Throwable $th) {
            Log::error('Error uploading image: ' . $th->getMessage());
            $response = [
                'status' => 0,
                'message' => $th->getMessage(),
            ];
            return response()->json($response);
        }
    }
}
