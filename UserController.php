<?php
namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Mail;
use App\Models\User;

use App\Models\UserStep;

use Exception;
use Validator;
use Log;
class UserController extends Controller
{
    
    public function Register(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    'userName'          => 'required',
                    'email'             => 'required',
                    'password'          => 'required|min:6',
                ]);
        if ($validator->fails()) {
                    return response()->json(['error' => $validator->errors()], 401);
                }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['userToken'] = md5(rand(1, 999999));
     
        // if (isset($input["profileImage"])) {
        //     $imagePath              = $input["profileImage"];
        //     $extension              = pathinfo($imagePath->getClientOriginalName(), PATHINFO_EXTENSION);
        //     $filename               = time() . "userImage." . $extension;
        //     $input['profileImage']  = $filename;
        // }
        
        if ($this->isUserExists($input['email'])) {
            return response()->json(['status' => 0, 'message' => 'User already registered with this email']);
        }

        $user = User::create($input);

        if ($user->id > 0)
         {
            if (isset($input["profileImage"])) {
                $upload_dir_path        = public_path() . "/uploads/userImage";
                $imagePath->move($upload_dir_path, $user->profileImage);
            }
           
           
                // $userImage = $user->profileImage ? asset('uploads/userImage/' . $user->profileImage) : asset('uploads/default-user.jpg');

                $success = [
                    'userId'        => $user->id,
                    'userName'      => $user->userName,
                    //'phone'         => $user->phone,
                    'email'         => $user->email,
                   // 'profileImage'  => $userImage,
                    'userToken'     => $user->userToken,
                    //'address'       => $user->address,
                ];
    
                return response()->json(['status' => 1, 'message' => 'User Registered Successfully', 'result' => $success]);
             
           
        } else {
            return response()->json(['status' => 0, 'message' => 'User not registered']);
        }
    }

    private function isUserExists($email)
    {
        return User::where('email', $email)
            //->orWhere('phone', $phone)
            ->where('isActive', 1)
            ->exists();
    }

    /// User Login API ///
    
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    'email'     => 'required',
                    'password'  => 'required'
                ]);
        if ($validator->fails()) {
                    return response()->json(['error'=>$validator->errors()], 401);
                }

        $email = $request->input('email');
        $password = $request->input('password');

        if (Auth::guard('web')->attempt(['email' => $email, 'password' => $password], false)) {
            $user = Auth::user();
            $userData = $this->getUserData($user);
          
                $tkn = md5(rand(1, 999999));
                $user->update([
                    'userToken' => $tkn,                 
                ]);
                $userData['userToken'] = $user->userToken;
                return response()->json(['status' => 1, 'message' =>"Login Successfully",'result'=>$userData]);
                                
            }
          
        return response()->json(['status' => 0, 'message' => "Email or Password is incorrect. Please try again!"]);
    }

private function getUserData($user)
    {
        $userImage = $user->profileImage ? asset('uploads/userImage/' . $user->profileImage) : asset('uploads/default-user.jpg');

        return [
            'userId' => $user->id,
            'userName' => $user->userName,
           // 'phone' => $user->phone,
            'email' => $user->email,
           // 'profileImage' => $userImage,
            'userToken' => $user->userToken,
           // 'isverify' => $user->isVerify,
            
        ];
    }
    /// Forgot password API ///
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    'email' => 'required',
                ]);
        if ($validator->fails()) {
                    return response()->json(['error'=>$validator->errors()], 401);
                }

        $email = $request->input('email');

        $user = User::where('email', $email)->first();

        if ($user) {
            $newPassword = rand(100000, 999999);
            $user->update(['password' => bcrypt($newPassword)]);

            $data = [
                'userName' => $user->userName,
                'newPassword' => $newPassword,
            ];

            $this->sendPasswordResetEmail($user->email, $data);

            $success = [
                'userId' => $user->id,
                'email' => $user->email,
            ];

            return response()->json(['status' => 1, 'message' => "Mail sent Successfully", 'result' => $success]);
        } else {
            return response()->json(['status' => 0, 'message' => "Mail not Found"]);
        }
    }

    private function sendPasswordResetEmail($email, $data)
    {
        try{
                        Mail::send('adminpanel.verifypassword', $data, function ($message) use ($email) {
                        $message->from('kitest@keshavinfotechdemo2.com', 'Rebound');
                        $message->to($email)->subject('Forgot Password  for Rebound');
                    });
            }catch (\Exception $e) {
                Log::error('Email sending error: ' . $e->getMessage());
            }
    }
	    ////change Password
       public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    'userId'            => 'required',
                    'userToken'         => 'required',
                    'oldPassword'       => 'required',
                    'newPassword'       => 'required|min:6',
                    ]);
        if ($validator->fails()) {
                    return response()->json(['error'=>$validator->errors()], 401);
                }
        $input = $request->all();
        $userId = $input['userId'];
        $oldPassword = $input['oldPassword'];
        $newPassword = $input['newPassword'];
            //-------------------Message------------------------------
            $msg1 = "Password Updated Successfully.";
            $msg2 = "Password Not Updated. ";
            $msg4 = "User Old Password Not Match";
            $msg5 = "You are not authorized please login again";
    //     //-------------------Message------------------------------
        $verifiedUserId = Controller::checkToken($userId, $input['userToken']);

        if ($verifiedUserId == $userId) {
            $user = User::find($userId);

            if (!$user) {
                return response()->json(['status' => 0, 'message' => $msg5]);
            }

            // Verify the old password before allowing the change
            if (password_verify($oldPassword, $user->password)) {
                $user->update([
                    'password' => bcrypt($newPassword),
                    'userToken' => '', // Clear the userToken
                ]);

                return response()->json(['status' => 1, 'message' =>$msg1]);
            } else {
                return response()->json(['status' => 0, 'message' => $msg4]);
            }
        } else {
            return response()->json(['status' => 0, 'message' =>$msg5]);
        }
    }

}
