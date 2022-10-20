<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use App\Consortia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Auth\Events\Registered;
use App\Events\NewSubscriberEvent; 

// This file contains request handling logic for the AANR page.
// functions included are:
//
//     get(Request $request){
//     createUser(Request $request){
//     editUser(Request $request, $user_id){
//     sendConsortiaAdminRequest(Request $request, $user_id){
//     consortiaAdminRequestApprove(Request $request, $user_id){
//     consortiaAdminRequestDecline(Request $request, $user_id){
//     deleteUser( $user_id){
// 
// Certain data are validated.

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
            'select_country' => ['required'],
            'contact_number' => ['nullable', 'digits:10'],
            'select_org' => 'required',
            'g-recaptcha-response' => 'required',
            'terms_condition' => 'required',
            'interest' => $request->subscription_check === 'on' ? 'required': 'nullable',
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
        
        if ($resp->success) {
            $user = new User;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->password = Hash::make($request->password);
            $user->interest = null;
            $user->email = $request->email;
            $user->gender = $request->gender;
            $user->is_organization_other = 0;
            $user->age_range = $request->age_range;
            $user->organization = $request->select_org;
            $user->country_id = $request->select_country;
            $user->contact_number = $request->contact_number;
            $user->subscribed = $request->subscription_check === "on" ? 1 : 0;

            if($request->select_org == 'other'){
                $user->is_organization_other = 1;
                $user->organization = $request->others_org;
            }

            if($user->subscribed) {
                $user->interest = json_encode($request->interest);
            }

            $user->save();
            Auth::loginUsingId($user->id);
            event(new Registered($user));

            return redirect('/')->with('success','Registration Success! Welcome.');
        } else {
            // failure
            return redirect('/register')->with('error','Something went wrong with the registration. Please try again later.');
        }
    }

    public function editUser(Request $request, $user_id){
        $this->validate($request, array(
            'first_name' => 'required|max:200',
            'last_name' => 'required|max:200',    
            'select_country' => ['required'],     
            'contact_number' => ['nullable', 'digits:10'],
            'interest' => $request->subscribe == 1 ? 'required': 'nullable',
        ));

        $user = User::find($user_id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->birthdate = $request->birthdate;
        $user->region = $request->region;
        $user->city = $request->city;
        $user->zip_code = $request->zipcode;
        $user->country_id = $request->select_country;
        $user->contact_number = $request->contact_number;
        $user->age_range = $request->age_range;
        $user->gender = $request->gender;
        
        //from unsubscribed to subscribed - send subscription email
        if(!$user->subscribed) {
            if($request->subscribe) {
                event(new NewSubscriberEvent($user));
            }
        }

        $user->subscribed = $request->subscribe;
        $user->interest = null;
        $user->is_organization_other = 0;
        $user->organization = $request->select_org;

        if($request->interest) {
            $user->interest = json_encode($request->interest);
        }

        if($request->select_org == 'other'){
            $user->is_organization_other = 1;
            $user->organization = $request->others_org;
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

    public function unsubscribeUser() {
        $user = auth()->user();
        $user->subscribed = 0;
        $user->save();

        return redirect('/')->with('success','Unsubscription Success.');
    }
}
