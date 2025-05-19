<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Pagination\Paginator;

class AdminController extends Controller
{
    public function edit($id)
    {
        $input = Admin::where('id','=', 1)->get();
        $data = array('type'=>'Edit', 'input'=>$input);
        return view('adminpanel.editprofile', compact('data'));
    }

    public function update(Request $request)
    {
        $input  = $request->all();
        $id     = $input['id'];

        $validator = Validator::make( $input, $this->getRules('Edit', $input, $id), $this->messages());
        if ($validator->fails())
        {
            $data = array('type'=>'Edit', 'input'=>$input,'error'=>$validator->messages());
            return view('adminpanel.editprofile', compact('data'));
            exit();
        }
        $update = array();
        $update['name']     = $input['name'];
        $update['username']     = $input['username'];
        $update['email']     = $input['email'];
        if(isset($input['password']))
        {
            $update['password'] = bcrypt($input['password']);
        }



        $admin = Admin::where('id', '=', 1)->update($update);
        return redirect()->back()->with('success', 'Admin Profile Updated successfully.');
    }

    // ========================================================================================
    private function getRules($type, $input, $adminId = '0') {
        $return = array();
        $return['name']       = 'required';
        $return['username']       = 'required';
        $return['email']       = 'required';

        if($type == "Edit")
        {
        
            $return['email'] = 'required|email|unique:admins,email,'.$adminId.'|max:100';
        }
        else
        {
            $return['email']        = 'required|email|unique:admins,email|max:100';
        }
        return $return;
    }

    private function messages()
    {
        return [
            'name.required'             => $this->getRequiredMessage('name'),
            'username.required'         => $this->getRequiredMessage('username'),
            'email.required'            => $this->getRequiredMessage('email')
        ];
    }

    private function getRequiredMessage($string) {
        return 'The ' . $string . ' field is required.';
    }
}
