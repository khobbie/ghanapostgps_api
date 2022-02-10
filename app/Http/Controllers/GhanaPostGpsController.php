<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GhanaPostGPS\GhanaPostGPS;
use Illuminate\Support\Facades\Validator;

class GhanaPostGpsController extends Controller
{
    private $asaaseUser;

    private $deviceId;

    private $aesKey;

    public function gpsName(Request $request)
    {

        // Ghana Code
        $gpsName = $request->address;

        $validator = Validator::make($request->all(), [
            'address' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => '422',
                'message' => 'Validation Error(s)',
                'data' => $validator->errors()
            ], 422);
        }

        try {
            $longitude = $request->longitude;
            $latitude = $request->latitude;

            $asaaseUser = env('ASAASE_USER_ID');
            $deviceId = env('DEVICE_ID');
            $aesKey = env('AES_KEY_FOR_DEVICE');

            $gps = new GhanaPostGPS($asaaseUser, $deviceId, $aesKey);

            $location = $gps->getLocation($gpsName);


            if (is_null($location)) {

                return response()->json([
                    'code' => '404',
                    'message' => 'Address not found ',
                    'data' => NULL
                ], 404);
            } else {

                return response()->json([
                    'code' => '000',
                    'message' => 'Address found ',
                    'data' => $location
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => '422',
                'message' => $e->getMessage(),
                'data' => NULL
            ], 500);
        }
    }


    public function geoLocation(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => '422',
                'message' => 'Validation Error(s)',
                'data' => $validator->errors()
            ], 422);
        }

        try {
            $longitude = $request->longitude;
            $latitude = $request->latitude;

            $asaaseUser = env('ASAASE_USER_ID');
            $deviceId = env('DEVICE_ID');
            $aesKey = env('AES_KEY_FOR_DEVICE');

            $gps = new GhanaPostGPS($asaaseUser, $deviceId, $aesKey);

            $location = [
                'lat' => $latitude,
                'lng' => $longitude
            ];

            // The GPS info contains postcode, region, area and street info
            // for the provided location.
            $gpsInfo = $gps->getGps($location);

            if (is_null($gpsInfo)) {

                return response()->json([
                    'code' => '404',
                    'message' => 'Address not found ',
                    'data' => NULL
                ], 404);
            } else {

                return response()->json([
                    'code' => '000',
                    'message' => 'Address found ',
                    'data' => $gpsInfo
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => '422',
                'message' => $e->getMessage(),
                'data' => NULL
            ], 500);
        }
    }
}
