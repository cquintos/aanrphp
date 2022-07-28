<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use App\Consortia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class UsersController extends Controller
{
    public function get(Request $request){
        $user_id = Auth::id();
        $user = User::find($user_id);
        return $user;
    }

    public function createUser(Request $request){
        $this->validate($request, [
            'first_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'contact_number' => 'required|digits:10',
            'select_org' => 'required',
            'g-recaptcha-response' => 'required',
            'terms_condition' => 'required',
        ]);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'secret' => '6Ldcxx4hAAAAACIARW1bpo-SYbPZCcLqTFw0Qn5h',
            'response' => $_POST['g-recaptcha-response'],
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ]);
        
        $resp = json_decode(curl_exec($ch));
        curl_close($ch);
        if(!$_POST['g-recaptcha-response']){
            echo '<h2>Please check the the captcha form.</h2>';
            return redirect('/register');
        }
        if ($resp->success) {
            $user = new User;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->password = Hash::make($request->password);
            if($request->select_org == 'other'){
                $user->is_organization_other = 1;
                $user->organization = $request->others_org;
            } else {
                $user->is_organization_other = 0;
                $user->organization = $request->select_org;
            }
            $user->email = $request->email;
            $user->age_range = $request->age_range;
            $user->gender = $request->gender;
            $user->contact_number = $request->contact_number;
            $user->save();
            Auth::loginUsingId($user->id);



            Http::post('community.aanr.ph/user/register?_format=json', [
                "name" => ["value" => $user->first_name],
                "mail" => ["value" => $user->email],
                "pass" => ["value" => $user->password]
            ]);
            return redirect('/')->with('success','Registration Success! Welcome.');
        } else {
            // failure
            exit;
        }
    }

    public function editUser(Request $request, $user_id){
        $this->validate($request, array(
            'first_name' => 'required|max:200',
            'last_name' => 'required|max:200',            
            'contact_number' => 'required|digits:10',

        ));

        $user = User::find($user_id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->birthdate = $request->birthdate;
        $user->region = $request->region;
        $user->city = $request->city;
        $user->zip_code = $request->zipcode;
        $user->contact_number = $request->contact_number;
        $user->age_range = $request->age_range;
        $user->gender = $request->gender;
        $user->subscribed = $request->subscribe;
        if(!empty(($request->interest)) && $request->interest != "null" && $request->interest != "NULL") {
            $user->interest = json_encode($request->interest);
        } else {
            $user->interest = null;
        }
        if($request->select_org == 'other'){
            $user->is_organization_other = 1;
            $user->organization = $request->others_org;
        } else {
            $user->is_organization_other = 0;
            $user->organization = $request->select_org;
        }
        $user->save();
        Auth::loginUsingId($user->id);
        $request->session()->flash('status', 'Task was successful!');
        return redirect()->back()->with('success','User account changes saved.');
    }

    public function sendConsortiaAdminRequest(Request $request, $user_id){

        $user = User::find($user_id);
        $user->consortia_admin_request = 1;
        $user->consortia_admin_id = $request->consortia_admin_id;
        $user->save();

        return redirect()->back()->with('success','Request Sent. Please wait for admin approval.');
    }

    public function consortiaAdminRequestApprove(Request $request, $user_id){

        $user = User::find($user_id);
        $user->consortia_admin_request = 2;
        $user->role = 2;
        $user->save();

        return redirect()->back()->with('success','Request approved.');
    }

    public function consortiaAdminRequestDecline(Request $request, $user_id){

        $user = User::find($user_id);
        $user->consortia_admin_request = 0;
        $user->role = 1;
        $user->consortia_admin_id = null;
        $user->save();

        return redirect()->back()->with('success','Request declined.');
    }

    public function deleteUser( $user_id){
        $user = User::find($user_id);
        $user->delete();

        return redirect()->back()->with('success','User Account Deleted.');
    }
}
