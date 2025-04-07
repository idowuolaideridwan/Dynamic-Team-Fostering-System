<?php

namespace App\Helpers;

//todo: build an array of error codes and meaning.

class Helper
{

    const MSG_SUCCESS = 200; //called with Helper::MSG_SUCCESS
    const MSG_CREATED = 201;
    const MSG_ERROR = 204; 
    const MSG_REGISTER_CONFLICT = 209;
    const MSG_BAD_REQUEST = 400;
    const MSG_UNAUTHORIZED = 401;
    const MSG_FORBIDDEN = 403;
    const MSG_NOT_FOUND = 404;
    const MSG_HTTP_METHOD_NOT_ALLOWED = 405;
    const MSG_NOT_ACCEPTED = 406;
    const MSG_CONFLICT = 409; 
    const MSG_GONE = 410;
    const MSG_UNSUPPORTED_MEDIA_TYPE = 415;
    const MSG_EXPECTATION_FAILED = 417;
    const MSG_UNPROCESSED_ENTITY = 422;
    const MSG_INTERNAL_SERVER_ERROR = 500;

    public function __construct() 
    {

    }

    // Method to get the API URL
    public static function getApiUrl()
    {
        return config('app.api_base_url');
    }

    public static function BuildJSONResponse($status, $message, $data = null)
    {
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    public static function prepareUserData($overrides = [])
    {
        $default = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        return array_merge($default, $overrides);
    }
}

