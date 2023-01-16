<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Consortia;
use App\User;
use App\Log;

// This file contains request handling logic for Consortia.
// functions included are:
//     addConsortia(Request $request)
//     editConsortia(Request $request, $consortia_id)
//     deleteConsortia(Request $request, $consortia_id)
//     setUserAdmin(Request $request, $user_id)
//
// This file also contains request handling logic for the CMS of the Consortia page.
// functions included are:
//     editConsortiaBanner(Request $request, $consortia_id)
//     editConsortiaLandingPageBanner(Request $request, $consortia_id)
//     editConsortiaDetails(Request $request, $consortia_id)
//     editConsortiaFeaturedPublicationsSection(Request $request, $consortia_id)
//     editConsortiaFeaturedVideosSection(Request $request, $consortia_id)
//     editConsortiaLatestAANRSection(Request $request, $consortia_id)
//
// Certain data are validated.
//
// all changes are logged in a new Log object

class ConsortiaController extends Controller
{
    public function addConsortia(Request $request)
    {
        $this->validate($request, array(
            'short_name' => 'required|max:255',
            'full_name' => 'required',
            'image' => ['mimes:jpeg,jpg,png,gif', 'max:10240', 'required']
        ));

        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $consortia = new Consortia();

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageName = uniqid().$imageFile->getClientOriginalName();
            $imageFile->move(public_path('/storage/page_images/'), $imageName);
            $consortia->thumbnail = $imageName;
        }
        if ($request->link) {
            if (!preg_match("~^(?:f|ht)tps?://~i", $request->link)) {
                $consortia->link = "http://" . $request->link;
            }
        }

        $consortia->short_name = $request->short_name;
        $consortia->full_name = $request->full_name;
        $consortia->region = $request->region;
        $consortia->profile = $request->profile;
        $consortia->banner_color = "#000000";
        $consortia->button_text = "Link to website";
        $consortia->welcome_message = $request->welcome;
        $consortia->contact_name = $request->contact_name;
        $consortia->contact_details = $request->contact_details;
        $consortia->save();

        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = '<strong>Added </strong>\''. $consortia->short_name.'\' <strong>to consortia</strong>';
        $log->action = 'Added \''. $consortia->short_name.'\' to consortia';
        $log->IP_address = $request->ip();
        $log->resource = 'Consortia';
        $log->save();

        return redirect()->back()->with('success', 'Consortia Added.');
    }

    public function editConsortia(Request $request, $consortia_id)
    {
        $this->validate($request, array(
            'short_name' => 'required|max:255',
            'full_name' => 'required',
        ));

        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $consortia = Consortia::find($consortia_id);

        if ($consortia == null) {
            return redirect()->back()->with('error', 'Consortia not found.');
        }
        if ($consortia->short_name != $request->short_name) {
            $temp_changes = $temp_changes.'<strong>Short Name:</strong> '.$consortia->short_name.' <strong>-></strong> '.$request->short_name.'<br>';
        }
        if ($consortia->full_name != $request->full_name) {
            $temp_changes = $temp_changes.'<strong>Full Name:</strong> '.$consortia->full_name.' <strong>-></strong> '.$request->full_name.'<br>';
        }
        if ($consortia->region != $request->region) {
            $temp_changes = $temp_changes.'<strong>Region:</strong> '.$consortia->region.' <strong>-></strong> '.$request->region.'<br>';
        }
        if ($consortia->link != $request->link) {
            $temp_changes = $temp_changes.'<strong>Link:</strong> '.$consortia->link.' <strong>-></strong> '.$request->link.'<br>';
        }
        if ($consortia->profile != $request->profile) {
            $temp_changes = $temp_changes.'<strong>Profile:</strong> '.$consortia->profile.' <strong>-></strong> '.$request->profile.'<br>';
        }
        if ($consortia->welcome_message != $request->welcome_message) {
            $temp_changes = $temp_changes.'<strong>Welcome Message:</strong> '.$consortia->welcome_message.' <strong>-></strong> '.$request->welcome_message.'<br>';
        }
        if ($consortia->contact_name != $request->contact_name) {
            $temp_changes = $temp_changes.'<strong>Contact Name:</strong> '.$consortia->contact_name.' <strong>-></strong> '.$request->contact_name.'<br>';
        }
        if ($consortia->contact_details != $request->contact_details) {
            $temp_changes = $temp_changes.'<strong>Contact Image:</strong> '.$consortia->contact_details.' <strong>-></strong> '.$request->contact_details.'<br>';
        }
        if ($request->hasFile('image')) {
            if ($consortia->thumbnail != null) {
                $image_path = public_path().'/storage/page_images/'.$consortia->thumbnail;
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            $imageFile = $request->file('image');
            $imageName = uniqid().$imageFile->getClientOriginalName();
            $imageFile->move(public_path('/storage/page_images/'), $imageName);
            $temp_changes = $temp_changes.'<strong>Thumbnail:</strong> '.$consortia->thumbnail.' <strong>-></strong> '.$imageName.'<br>';
            $consortia->thumbnail = $imageName;
        }
        if ($request->link) {
            if (!preg_match("~^(?:f|ht)tps?://~i", $request->link)) {
                $consortia->link = "http://" . $request->link;
            }
        }

        $consortia->welcome_message = "Welcome to ".$request->short_name." consortia page!";

        if ($request->welcome) {
            $consortia->welcome_message = $request->welcome;
        }

        $consortia->region = $request->region;
        $consortia->profile = $request->profile;
        $consortia->short_name = $request->short_name;
        $consortia->full_name = $request->full_name;
        $consortia->contact_name = $request->contact_name;
        $consortia->contact_details = $request->contact_details;
        $consortia->save();

        $log->user_id = $user->id;
        $log->changes = $temp_changes;
        $log->user_email = $user->email;
        $log->action = 'Edited \''. $consortia->short_name.'\' details';
        $log->IP_address = $request->ip();
        $log->resource = 'Consortia';
        $log->save();

        return redirect()->back()->with('success', 'Consortia Updated.');
    }

    public function editConsortiaBanner(Request $request, $consortia_id)
    {
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $consortia = Consortia::find($consortia_id);

        if ($consortia == null) {
            return redirect()->back()->with('error', 'Consortia not found.');
        }
        if ($request->hasFile('image')) {
            if ($consortia->thumbnail != null) {
                $image_path = public_path().'/storage/page_images/'.$consortia->thumbnail;
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            $imageFile = $request->file('image');
            $imageName = uniqid().$imageFile->getClientOriginalName();
            $imageFile->move(public_path('/storage/page_images/'), $imageName);

            $temp_changes = $temp_changes.'<strong>Thumbnail:</strong> '.$consortia->thumbnail.' <strong>-></strong> '.$imageName.'<br>';
            $consortia->thumbnail = $imageName;
        }
        if ($consortia->is_gradient != $request->banner_color_radio) {
            $temp_changes = $temp_changes.'<strong>Is Gradient?:</strong> '.$consortia->is_gradient.' <strong>-></strong> '.$request->banner_color_radio.'<br>';
        }
        if ($consortia->banner_color != $request->banner_color) {
            $temp_changes = $temp_changes.'<strong>Banner Color:</strong> '.$consortia->banner_color.' <strong>-></strong> '.$request->banner_color.'<br>';
        }
        if ($consortia->gradient_first != $request->gradient_first) {
            $temp_changes = $temp_changes.'<strong>First Gradient:</strong> '.$consortia->gradient_first.' <strong>-></strong> '.$request->gradient_first.'<br>';
        }
        if ($consortia->gradient_first != $request->gradient_first) {
            $temp_changes = $temp_changes.'<strong>First Gradient:</strong> '.$consortia->gradient_first.' <strong>-></strong> '.$request->gradient_first.'<br>';
        }
        if ($consortia->button_text != $request->button_text) {
            $temp_changes = $temp_changes.'<strong>Button Text:</strong> '.$consortia->button_text.' <strong>-></strong> '.$request->button_text.'<br>';
        }
        if ($consortia->link != $request->link) {
            $temp_changes = $temp_changes.'<strong>Button Link:</strong> '.$consortia->link.' <strong>-></strong> '.$request->link.'<br>';
        }
        if ($consortia->welcome_message != $request->welcome) {
            $temp_changes = $temp_changes.'<strong>Welcome Message:</strong> '.$consortia->welcome_message.' <strong>-></strong> '.$request->welcome.'<br>';
        }

        $consortia->button_text = "Link to website";

        if ($request->button_text) {
            $consortia->button_text = $request->button_text;
        }
        if ($request->link) {
            $consortia->link = $request->link;

            if (!preg_match("~^(?:f|ht)tps?://~i", $request->link)) {
                $consortia->link = "http://" . $request->link;
            }
        }

        $consortia->is_gradient = $request->banner_color_radio;
        $consortia->banner_color = $request->banner_color;
        $consortia->gradient_first = $request->gradient_first;
        $consortia->gradient_second = $request->gradient_second;
        $consortia->gradient_direction = $request->gradient_direction;
        $consortia->welcome_message = $request->welcome;
        $consortia->save();

        $log->user_id = $user->id;
        $log->changes = $temp_changes;
        $log->user_email = $user->email;
        $log->action = 'Edited \''. $consortia->short_name.'\' banner details';
        $log->IP_address = $request->ip();
        $log->resource = 'Consortia';
        $log->save();

        return redirect()->back()->with('success', 'Consortia Banner Updated.');
    }

    public function editConsortiaLandingPageBanner(Request $request, $consortia_id)
    {
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $consortia = Consortia::find($consortia_id);

        if ($consortia == null) {
            return redirect()->back()->with('error', 'Consortia not found.');
        }
        if ($consortia->landing_page_is_gradient != $request->banner_color_radio) {
            $temp_changes = $temp_changes.'<strong>Is Gradient?:</strong> '.$consortia->landing_page_is_gradient.' <strong>-></strong> '.$request->banner_color_radio.'<br>';
        }
        if ($consortia->landing_page_banner_color != $request->banner_color) {
            $temp_changes = $temp_changes.'<strong>Banner Color:</strong> '.$consortia->landing_page_banner_color.' <strong>-></strong> '.$request->banner_color.'<br>';
        }
        if ($consortia->landing_page_gradient_first != $request->gradient_first) {
            $temp_changes = $temp_changes.'<strong>First Gradient:</strong> '.$consortia->landing_page_gradient_first.' <strong>-></strong> '.$request->gradient_first.'<br>';
        }
        if ($consortia->landing_page_gradient_second != $request->gradient_second) {
            $temp_changes = $temp_changes.'<strong>Second Gradient:</strong> '.$consortia->landing_page_gradient_second.' <strong>-></strong> '.$request->gradient_second.'<br>';
        }
        if ($consortia->landing_page_gradient_direction != $request->gradient_second) {
            $temp_changes = $temp_changes.'<strong>Gradient Direction:</strong> '.$consortia->landing_page_gradient_direction.' <strong>-></strong> '.$request->gradient_direction.'<br>';
        }
        if ($consortia->landing_page_button_text != $request->button_text) {
            $temp_changes = $temp_changes.'<strong>Button Text:</strong> '.$consortia->landing_page_button_text.' <strong>-></strong> '.$request->button_text.'<br>';
        }
        if ($consortia->landing_page_link != $request->link) {
            $temp_changes = $temp_changes.'<strong>Button Link:</strong> '.$consortia->welcome.' <strong>-></strong> '.$request->link.'<br>';
        }
        if ($consortia->landing_page_welcome_message != $request->link) {
            $temp_changes = $temp_changes.'<strong>Welcome Message:</strong> '.$consortia->landing_page_welcome_message.' <strong>-></strong> '.$request->welcome.'<br>';
        }


        $consortia->landing_page_is_gradient = $request->banner_color_radio;
        $consortia->landing_page_banner_color = $request->banner_color;
        $consortia->landing_page_gradient_first = $request->gradient_first;
        $consortia->landing_page_gradient_second = $request->gradient_second;
        $consortia->landing_page_gradient_direction = $request->gradient_direction;
        if ($request->hasFile('image')) {
            if ($consortia->thumbnail != null) {
                $image_path = public_path().'/storage/page_images/'.$consortia->thumbnail;
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            $imageFile = $request->file('image');
            $imageName = uniqid().$imageFile->getClientOriginalName();
            $imageFile->move(public_path('/storage/page_images/'), $imageName);
            $temp_changes = $temp_changes.'<strong>Thumbnail:</strong> '.$consortia->thumbnail.' <strong>-></strong> '.$imageName.'<br>';
            $consortia->thumbnail = $imageName;
        }

        $consortia->landing_page_button_text = "Link to website";

        if ($request->button_text) {
            $consortia->landing_page_button_text = $request->button_text;
        }
        if ($request->link) {
            $consortia->landing_page_link = "http://" . $request->link;

            if (preg_match("~^(?:f|ht)tps?://~i", $request->link)) {
                $consortia->landing_page_link = $request->link;
            }
        }

        $consortia->landing_page_welcome_message = $request->welcome;
        $consortia->save();

        $log->user_id = $user->id;
        $log->changes = $temp_changes;
        $log->user_email = $user->email;
        $log->action = 'Edited \''. $consortia->short_name.'\' landing page banner details';
        $log->IP_address = $request->ip();
        $log->resource = 'Consortia';
        $log->save();

        return redirect()->back()->with('success', 'Consortia Landing Page Banner Updated.');
    }

    public function editConsortiaDetails(Request $request, $consortia_id)
    {
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $consortia = Consortia::find($consortia_id);

        if ($consortia == null) {
            return redirect()->back()->with('error', 'Consortia not found.');
        }
        if ($consortia->region != $request->region) {
            $temp_changes = $temp_changes.'<strong>Region:</strong> '.$consortia->region.' <strong>-></strong> '.$request->region.'<br>';
        }
        if ($consortia->profile != $request->profile) {
            $temp_changes = $temp_changes.'<strong>Profile:</strong> '.strip_tags($consortia->profile).' <strong>-></strong> '.strip_tags($request->profile).'<br>';
        }
        if ($consortia->contact_name != $request->contact_name) {
            $temp_changes = $temp_changes.'<strong>Contact Name:</strong> '.$consortia->contact_name.' <strong>-></strong> '.$request->contact_name.'<br>';
        }
        if ($consortia->contact_details != $request->contact_details) {
            $temp_changes = $temp_changes.'<strong>Contact Details:</strong> '.$consortia->contact_details.' <strong>-></strong> '.$request->contact_details.'<br>';
        }
        if ($consortia->link != $request->link) {
            $temp_changes = $temp_changes.'<strong>Link:</strong> '.$consortia->link.' <strong>-></strong> '.$request->link.'<br>';
        }
        if ($request->link) {
            if (!preg_match("~^(?:f|ht)tps?://~i", $request->link)) {
                $consortia->link = "http://" . $request->link;
            }
        }

        $consortia->region = $request->region;
        $consortia->profile = $request->profile;
        $consortia->contact_name = $request->contact_name;
        $consortia->contact_details = $request->contact_details;
        $consortia->save();

        $log->user_id = $user->id;
        $log->changes = $temp_changes;
        $log->user_email = $user->email;
        $log->action = 'Edited \''. $consortia->short_name.'\' details';
        $log->IP_address = $request->ip();
        $log->resource = 'Consortia';
        $log->save();

        return redirect()->back()->with('success', 'Consortia Details Updated.');
    }

    public function deleteConsortia(Request $request, $consortia_id)
    {
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $consortia = Consortia::find($consortia_id);

        if ($consortia == null) {
            return redirect()->back()->with('error', 'Consortia not found.');
        }
        if ($consortia->thumbnail != null) {
            $image_path = public_path().'/storage/page_images/'.$consortia->thumbnail;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $consortia->delete();

        $log->user_id = $user->id;
        $log->changes = '<strong>Deleted</strong> '.$consortia->full_name.'';
        $log->user_email = $user->email;
        $log->action = 'Deleted \''. $consortia->short_name.'\'';
        $log->IP_address = $request->ip();
        $log->resource = 'Consortia';
        $log->save();

        return redirect()->back()->with('success', 'Consortia Deleted.');
    }

    public function setUserAdmin(Request $request, $user_id)
    {
        $this->validate($request, array(
            'consortia_admin_id' => 'required',
        ));

        $admin = auth()->user();
        $log = new Log();
        $user = User::find($user_id);

        if ($user == null) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $user->role = $request->user_role;

        if ($request->user_role == 5) {
            $user->consortia_admin_id = null;
            $log->changes = 'Set '.$user->email.' as SUPERADMIN';
            $log->action = 'Set \''. $user->email.' as SUPERADMIN\'';
            $mess = "User set as SUPERADMIN!";
        }
        if ($request->user_role == 2) {
            $user->consortia_admin_id = $request->consortia_admin_id;
            $user->organization = Consortia::find($request->consortia_admin_id)->short_name;
            $user->consortia_admin_request = 2;
            $log->changes = 'Set '.$user->email.' as '.$user->organization.' admin';
            $log->action = 'Set \''. $user->email.' as '.$user->organization.' admin\'';
            $mess = 'User set as '.$user->organization.' admin!';
        }
        if ($request->user_role == 1) {
            $user->consortia_admin_id = null;
            $user->consortia_admin_request = 0;
            $log->changes = 'Set '.$user->email.' as regular user';
            $log->action = 'Set \''. $user->email.' as regular user\'';
            $mess = 'User set as regular user!';
        }

        $user->save();

        $log->user_id = $admin->id;
        $log->user_email = $admin->email;
        $log->IP_address = $request->ip();
        $log->resource = 'Users';
        $log->save();

        return redirect()->back()->with('success', $mess);
    }

    public function editConsortiaFeaturedPublicationsSection(Request $request, $consortia_id)
    {
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $consortium = Consortia::find($consortia_id);

        if ($cosortia == null) {
            return redirect()->back()->with('error', 'Consortia not found.');
        }
        if ($consortium->featured_publications_header != $request->featured_publications_header) {
            $temp_changes = $temp_changes.'<strong>Header:</strong> '.$consortium->featured_publications_header.' <strong>-></strong> '.$request->featured_publications_header.'<br>';
        }
        if ($consortium->featured_publications_subheader != $request->featured_publications_subheader) {
            $temp_changes = $temp_changes.'<strong>Subheader:</strong> '.$consortium->featured_publications_subheader.' <strong>-></strong> '.$request->featured_publications_subheader.'<br>';
        }

        $consortium->featured_publications_header = $request->input('featured_publications_header');
        $consortium->featured_publications_subheader = $request->input('featured_publications_subheader');
        $consortium->save();

        $log->user_id = $user->id;
        $log->changes = $temp_changes;
        $log->user_email = $user->email;
        $log->action = 'Edited \'Consortia Featured Publication Section\' details';
        $log->IP_address = $request->ip();
        $log->resource = 'Consortia Landing Page';
        $log->save();

        return redirect()->back()->with('success', 'Featured Publications Section Updated');
    }

    public function editConsortiaFeaturedVideosSection(Request $request, $consortia_id)
    {
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $consortium = Consortia::find($consortia_id);
        
        if ($cosortia == null) {
            return redirect()->back()->with('error', 'Consortia not found.');
        }
        if ($consortium->featured_videos_header != $request->featured_videos_header) {
            $temp_changes = $temp_changes.'<strong>Header:</strong> '.$consortium->featured_videos_header.' <strong>-></strong> '.$request->featured_videos_header.'<br>';
        }
        if ($consortium->featured_videos_subheader != $request->featured_videos_subheader) {
            $temp_changes = $temp_changes.'<strong>Subheader:</strong> '.$consortium->featured_videos_subheader.' <strong>-></strong> '.$request->featured_videos_subheader.'<br>';
        }
        if ($consortium->featured_video_link_1 != $request->first_link) {
            $temp_changes = $temp_changes.'<strong>Video Link 1:</strong> '.$consortium->featured_video_link_1.' <strong>-></strong> '.$request->first_link.'<br>';
        }
        if ($consortium->featured_video_link_2 != $request->second_link) {
            $temp_changes = $temp_changes.'<strong>Video Link 2:</strong> '.$consortium->featured_video_link_2.' <strong>-></strong> '.$request->second_link.'<br>';
        }
        if ($consortium->featured_video_link_3 != $request->third_link) {
            $temp_changes = $temp_changes.'<strong>Video Link 3:</strong> '.$consortium->featured_video_link_3.' <strong>-></strong> '.$request->third_link.'<br>';
        }

        $consortium->featured_videos_header = $request->input('featured_videos_header');
        $consortium->featured_videos_subheader = $request->input('featured_videos_subheader');
        $consortium->featured_video_link_1 = $request->input('first_link');
        $consortium->featured_video_link_2 = $request->input('second_link');
        $consortium->featured_video_link_3 = $request->input('third_link');
        $consortium->save();

        $log->user_id = $user->id;
        $log->changes = $temp_changes;
        $log->user_email = $user->email;
        $log->action = 'Edited \'Consortia Featured Videos Section\' details';
        $log->IP_address = $request->ip();
        $log->resource = 'Consortia Landing Page';
        $log->save();

        return redirect()->back()->with('success', 'Featured Videos Section Updated');
    }

    public function editConsortiaLatestAANRSection(Request $request, $consortia_id)
    {
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $consortium = Consortia::find($consortia_id);
        
        if ($cosortia == null) {
            return redirect()->back()->with('error', 'Consortia not found.');
        }
        if ($consortium->latest_aanr_header != $request->latest_aanr_header) {
            $temp_changes = $temp_changes.'<strong>Header:</strong> '.$consortium->latest_aanr_header.' <strong>-></strong> '.$request->latest_aanr_header.'<br>';
        }
        if ($consortium->latest_aanr_subheader != $request->latest_aanr_subheader) {
            $temp_changes = $temp_changes.'<strong>Subheader:</strong> '.$consortium->latest_aanr_subheader.' <strong>-></strong> '.$request->latest_aanr_subheader.'<br>';
        }
        if ($request->banner_color_radio_latest_aanr == 1) {
            $image_path = public_path().'/storage/page_images/'.$consortium->latest_aanr_bg;
            if ($consortium->latest_aanr_bg != null && file_exists($image_path)) {
                unlink($image_path);
            }
            if ($consortium->latest_aanr_bg != $request->banner_color) {
                $temp_changes = $temp_changes.'<strong>BG Color:</strong> '.$consortium->latest_aanr_bg.' <strong>-></strong> '.$request->banner_color.'<br>';
            }
            $temp_changes = $temp_changes.'<strong>BG Type:</strong> '.$consortium->latest_aanr_bg_type.' <strong>-></strong> 1<br>';

            $consortium->latest_aanr_bg = $request->input('banner_color');
            $consortium->latest_aanr_bg_type = 1;
        } else {
            if ($request->hasFile('image')) {
                if ($consortium->latest_aanr_bg != null) {
                    $image_path = public_path().'/storage/page_images/'.$consortium->latest_aanr_bg;
                    if ($consortium->latest_aanr_bg != null && file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
                $imageFile = $request->file('image');
                $imageName = uniqid().$imageFile->getClientOriginalName();
                $imageFile->move(public_path('/storage/page_images/'), $imageName);
                if ($consortium->latest_aanr_bg != $imageName) {
                    $temp_changes = $temp_changes.'<strong>BG Image:</strong> '.$consortium->latest_aanr_bg.' <strong>-></strong> '.$imageName.'<br>';
                }
                $temp_changes = $temp_changes.'<strong>BG Type:</strong> '.$consortium->latest_aanr_bg_type.' <strong>-></strong> 0<br>';
                $consortium->latest_aanr_bg = $imageName;
            }
            $consortium->latest_aanr_bg_type = 0;
        }

        $consortium->latest_aanr_header = $request->input('latest_aanr_header');
        $consortium->latest_aanr_subheader = $request->input('latest_aanr_subheader');
        $consortium->save();

        $log->user_id = $user->id;
        $log->changes = $temp_changes;
        $log->user_email = $user->email;
        $log->action = 'Edited \'Consortia Latest AANR Section\' details';
        $log->IP_address = $request->ip();
        $log->resource = 'Consortia Landing Page';
        $log->save();

        return redirect()->back()->with('success', 'Latest AANR Section Updated');
    }
}
