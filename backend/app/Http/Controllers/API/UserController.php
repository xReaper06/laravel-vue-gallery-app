<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\API\Services\Service;

class UserController extends Controller
{
    public function insertImages(Request $request)
    {
        $service = new Service;
        return $service->doinsertImages($request);
    }
    public function getImages(Request $request)
    {
        $service = new Service;
        return $service->dogetImages($request);
    }
    public function deleteImage(Request $request)
    {
        $service = new Service;
        return $service->dodeleteImage($request);
    }
    public function updateImage(Request $request)
    {
        $service = new Service;
        return $service->doupdateImage($request);
    }
    public function getSelfInfo()
    {
        $service = new Service;
        return $service->dogetSelfInfo();
    }
    public function changeProfile(Request $request)
    {
        $service = new Service;
        return $service->dochangeProfile($request);
    }
    public function updateUserInfo(Request $request)
    {
        $service = new Service;
        return $service->doupdateUserInfo($request);
    }
    public function changePass(Request $request)
    {
        $service = new Service;
        return $service->dochangePass($request);
    }
}