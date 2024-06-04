<?php

namespace App\Http\Controllers\API\Services;

use App\Http\Controllers\API\BaseController;
use App\Models\Gallery;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\user_info;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;


class Service extends BaseController
{
    public static function ImageUploader($img, $params)
    {
        $filename = Str::random(10) . '_' . time() . '.' . $img->getClientOriginalExtension();
        switch ($params) {
            case 'profile':
                $imagePath = $img->storeAs('images', $filename, 'public');
                $encoded_path = base64_encode($imagePath);
                break;
            case 'gallery':
                $imagePath = $img->storeAs('gallery', $filename, 'public');
                $encoded_path = base64_encode($imagePath);
                break;
        }
        return $encoded_path;
    }
    public static function deleteImage($encryptedfilename)
    {
        $decodedPath = base64_decode($encryptedfilename);

        // Attempt to delete the file using the Storage facade
        if (\Illuminate\Support\Facades\Storage::disk('public')->delete($decodedPath)) {
            // File was successfully deleted
            return true;
        } else {
            // File could not be deleted
            return false;
        }
    }
    public function doRegister($request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'firstname' => 'required|string|max:255',
            'middlename' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'birthday' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'baranggay' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $request->all();
        if ($request->file('image')->isValid()) {
            $image = $this->ImageUploader($input['image'], 'profile');
            $user = User::create([
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);
            // Save the image path to the database
            user_info::create([
                'user_id' => $user->id,
                'image_path' => $image,
                'firstname' => $input['firstname'],
                'middlename' => $input['middlename'],
                'lastname' => $input['lastname'],
                'birthday' => $input['birthday'],
                'gender' => $input['gender'],
                'street' => $input['street'],
                'baranggay' => $input['baranggay'],
                'city' => $input['city'],
                'province' => $input['province'],
            ]);
            $success['registered'] =  'This user has been Created';

            return $this->sendResponse($success, 'User register successfully.');
        }
    }
    public function doLogin($request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $userData = DB::table('users')->select(['email', 'password', 'is_deactivated'])->where('email', $request->email)->first();
            if (!$userData) {
                return $this->sendError('User Not Found.', ['error' => 'User not Found']);
            } else if (!Hash::check($request->password, $userData->password)) {
                return $this->sendError('Incorrect Password', ['error' => 'Incorrect Password']);
            } else {
                if ($userData->is_deactivated !== 0) {
                    return $this->sendError('User Deactivated', ['error' => 'This User is currently Deactivated by the Head Admin Please call the Head admin directly']);
                } else {
                    $response = Auth::attempt(['email' => $request->email, 'password' => $request->password]);
                    if ($response) {
                        $user = Auth::user();
                        User::whereIn('id', [$user->id])->update(['status' => 'online', 'last_login' => now()]);
                        $user_info = DB::table('user_infos')
                            ->select(DB::raw('CONCAT_WS(" ", user_infos.firstname,user_infos.middlename,user_infos.lastname) AS fullname'), 'user_infos.image_path')
                            ->where('user_id', $user->id)
                            ->get();
                        $success['accessToken'] = $user->createToken('access_token', ['*'])->plainTextToken;
                        // $success['refreshToken'] = $user->createToken('refresh_token', ['*'])->plainTextToken;
                        $userJSON = [
                            'image' => $user_info[0]->image_path,
                            'email' => $user['email'],
                            'fullname' => $user_info[0]->fullname,
                        ];
                        $success['user'] = $userJSON;
                        return $this->sendResponse($success, 'User login successfully.');
                    } else {
                        return $this->sendError('Unauthorised.', ['error' => 'Error Login']);
                    }
                }
            }
        } catch (Exception $th) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'details' => $th->getMessage(),
                'success' => false
            ], 500);;
        }
    }
    public function doLogout($request)
    {
        try {
            // Attempt to logout the user using Laravel's built-in logout method
            $user = Auth::user();
            if ($user) {
                User::whereIn('id', [$user->id])->update(['status' => 'offline', 'last_login' => now()]);
                $user->tokens()->where('tokenable_id', $user->id)->delete();
            }

            return $this->sendResponse([], 'User logged out successfully.');
        } catch (Exception $error) {
            return $this->sendError('Logout failed.', ['error' => $error->getMessage()]);
        }
    }

    public function dogetImage($path)
    {
        $decodedPath = base64_decode($path);
        $image = Storage::disk('public')->get($decodedPath); // Use 'public' or your storage disk name
        $mimeType = Storage::disk('public')->mimeType($decodedPath);

        return response($image, 200)->header('Content-Type', $mimeType);
    }
    public function doCheckUserifExist($request)
    {
        try {
            $userAuth = Auth::user();
            if ($userAuth === null) {
                return response()->json([
                    'message' => 'This is User is not Authenticated',
                    'success' => false
                ], 404);
            } else {
                $user = DB::table('users')
                    ->select(['id', 'email'])
                    ->where('email', '=', $request->email)
                    ->first();
                return response()->json([
                    'user' => $user,
                    'success' => true
                ], 200);
            }
        } catch (Exception $th) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'details' => $th->getMessage(),
                'success' => false
            ], 500);
        }
    }
    public function dogetSelfInfo()
    {
        try {
            $Authuser = Auth::user();
            $user = DB::table('users')
                ->select(
                    'users.id',
                    'users.email',
                    'user_infos.image_path',
                    'user_infos.firstname',
                    'user_infos.middlename',
                    'user_infos.lastname',
                    'user_infos.street',
                    'user_infos.baranggay',
                    'user_infos.city',
                    'user_infos.province',
                    'user_infos.birthday',
                    'user_infos.gender'
                )
                ->join('user_infos', 'users.id', '=', 'user_infos.user_id')
                ->where('users.id', $Authuser->id)
                ->first();

            return response()->json([
                'info' => $user,
                'success' => true
            ], 200);
        } catch (Exception $th) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'details' => $th->getMessage(),
                'success' => false
            ], 500);
        }
    }
    public function dochangeProfile($request)
    {
        try {
            $authUser = Auth::user();
            $userInfo = user_info::where('user_id', $authUser->id)->get();
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $this->deleteImage($authUser->image_path);
                $image = $this->ImageUploader($request->image, 'profile');
                $userInfo->toQuery()->update([
                    'image_path' => $image
                ]);
            }
            return response()->json([
                'message' => 'Profile has been Change Successfully',
                'success' => true
            ], 200);
        } catch (Exception $th) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'details' => $th->getMessage(),
                'success' => false
            ], 500);
        }
    }
    public function doupdateUserInfo($request)
    {
        try {
            $authUser = Auth::user();
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|string|max:255',
                'middlename' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'birthday' => 'required|string|max:255',
                'gender' => 'required|string|max:255',
                'street' => 'required|string|max:255',
                'baranggay' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'province' => 'required|string|max:255',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }
            $input = $request->all();
            $userInfo = user_info::where('user_id', $authUser->id)->get();
            $userInfo->toQuery()->update([
                'firstname' => $input['firstname'],
                'middlename' => $input['middlename'],
                'lastname' => $input['lastname'],
                'birthday' => $input['birthday'],
                'gender' => $input['gender'],
                'street' => $input['street'],
                'baranggay' => $input['baranggay'],
                'city' => $input['city'],
                'province' => $input['province'],
            ]);

            return response()->json([
                'message' => 'Info has been Change Successfully',
                'success' => true
            ], 200);
        } catch (Exception $th) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'details' => $th->getMessage(),
                'success' => false
            ], 500);
        }
    }
    public function dochangePass($request)
    {
        try {
            $authUser = Auth::user();
            $validator = Validator::make($request->all(), [
                'password' => 'required',
                'cpassword' => 'required|same:password',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }
            if ($request->password != $request->cpassword) {
                return $this->sendError('Password doesnt Match', 'Please Check your password');
            }
            $user = User::find($authUser->id);
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'message' => 'you have Change Your Password Successfully',
                'success' => true
            ], 200);
        } catch (Exception $th) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'details' => $th->getMessage(),
                'success' => false
            ], 500);
        }
    }
    public function doinsertImages($request)
    {
        try {
            $user = Auth::user();
            $validator = Validator::make($request->all(), [
                'images' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $input = $request->all();
            foreach ($input['images'] as $imageArray) {
                $image = $this->ImageUploader($imageArray, 'gallery');
                $gallery = Gallery::create([
                    'user_id' => $user->id,
                    'image_path' => $image
                ]);
            }
            return response()->json([
                'message' => 'Images Inserted',
                'success' => true
            ]);
        } catch (Exception $th) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'details' => $th->getMessage(),
                'success' => false
            ], 500);
        }
    }
    public function dogetImages()
    {
        try {
            $user = Auth::user();
            $gallery = Gallery::where('user_id', $user->id)->orderByDesc('id')->get();
            return response()->json([
                'images' => $gallery,
                'success' => true
            ]);
        } catch (Exception $th) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'details' => $th->getMessage(),
                'success' => false
            ], 500);
        }
    }
    public function dodeleteImage($request)
    {
        try {
            $gallery = Gallery::find($request->image_id);
            $this->deleteImage($gallery->image_path);
            $gallery->delete();
            return response()->json([
                'message' => 'image Deleted Successfully',
                'success' => true
            ]);
        } catch (Exception $th) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'details' => $th->getMessage(),
                'success' => false
            ], 500);
        }
    }
    public function doupdateImage($request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'image_id' => 'required|integer',
                'image' => 'required|file',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }

            $gallery = Gallery::find($request->image_id);
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $this->deleteImage($gallery->image_path);
                $image = $this->ImageUploader($request->image, 'gallery');
                $gallery->image_path = $image;
                $gallery->save();
                return response()->json([
                    'message' => 'image updated Successfully',
                    'success' => true
                ]);
            } else {
                return response()->json([
                    'message' => 'image updated Error',
                    'success' => false
                ]);
            }
        } catch (Exception $th) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'details' => $th->getMessage(),
                'success' => false
            ], 500);
        }
    }
}