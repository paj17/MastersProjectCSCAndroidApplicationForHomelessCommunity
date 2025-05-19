<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Validator;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login()
    {
        return view('adminpanel.login');
    }

    public function submitlogin(Request $request) {
    	try {
            $validator = Validator::make( $request->all(), $this->getRules('Add', $request->all()), $this->messages());
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->messages());
            }
            if (Auth::guard('admin')->attempt(['username' => $request->username, 'password' => $request->password], $request->get('remember'))) {
                return redirect()->route('adminpanel.dashboard');
            }

            return redirect()->back()->withInput()->withErrors(['Invalid username or password.']);
        } catch (RuntimeException $ex) {
            return redirect()->back()->withInput()->withErrors([$ex->getMessage()]);
        }
    }
    public function logout() {
        Session::flush();
        Auth::logout();
     
      
        return redirect()->route('adminpanel.login');
    }


    public function forgotpassword(Request $req)

	{

		return view('adminpanel.forgotpassword');

	}
    public function submitforgotpassword(Request $request) 
    {
        $input = $request->all();
        
        /*$validator = Validator::make( $input, $this->getRules('Add', $input), $this->messages());
        if ($validator->fails()) { 
            $data = array('type'=>'add', 'input'=>$input, 'agenc_id'=>Auth::id(), 'error'=>$validator->messages());
            return view('agencypanel.addagent', compact('data'));
            exit();            
        }*/

        $email = $input['forgotEmail'];
        
        $new_password = "admin";//rand(100000,999999);
       $change_pass['password'] = bcrypt($new_password); 
       // $change_pass['password'] = "admin"; 

        $checkMail = Admin::where('email', '=', $email)->get();

        if (count($checkMail) > 0) 
        {
            $change = Admin::where('email', '=', $email)->update($change_pass);

            $to = $email;
            $from = "kitest@keshavinfotechdemo2.com";
            $line1 = "You forgot your password ?";
            $line2 = "No problem, use this temporary Password : ".$new_password;
            $subject = "Forgot password";

            $headers = "From: $from";
            $headers = "From: " . $from . "\r\n";
            $headers .= "Reply-To: ". $from . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            $body = "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Express Mail</title></head><body>";
            $body .= "<table style='width: 100%;'>";
            $body .= "<thead style='text-align: center;'><tr><td style='border:none;' colspan='2'>";
            $body .= "</td></tr></thead><tbody>";
            $body .= "<tr><td style='border:none;' align = 'center'>{$line1}</td></tr>";
            $body .= "<tr><td></td></tr>";
            $body .= "<tr><td style='border:none;'> </td></tr>";
            $body .= "<tr><td></td></tr>";
            $body .= "<tr><td colspan='2' style='border:none;' align = 'center'>{$line2}</td></tr>";
            $body .= "</tbody></table>";
            $body .= "</body></html>";

            mail($to, $subject, $body, $headers);
        
            return redirect()->back()->withInput()->withErrors(['Password Sent to mail successfully.']);
        }
        else
        {
            return redirect()->back()->withInput()->withErrors(['Email not found in our System.']);
        }
    }
    private function getRules($type, $input) {
        $return = array();
        $return['username'] = 'required|max:50';
        $return['password'] = 'required|max:20';
        return $return;
    }

    private function messages() {
        return [
            // 'question.required'  => 'The question field is required.'
        ];
    }
}
