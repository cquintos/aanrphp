<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Commodity;
use App\Log;
use App\CommoditySubtype;

// This file contains request handling logic for Commodities.
// functions included are:
//     addCommodity(Request $request)
//     editCommodity(Request $request, $commodity_id)
//     deleteCommodity($commodity_id, Request $request)
//
// if ($user->role != 5 && $user->role != 2)
//     Means only a SUPER ADMIN (role = 5) and a CONSORTIA ADMIN (role = 2) may use the function.      
//
// Certain data are validated.
//
// all changes are logged in a new Log object


class CommoditiesController extends Controller
{
    public function log($details) 
    {
        $log = new log ();
        $log->user_id = $details[0];
        $log->user_email = $details[1];
        $log->changes = $details[2];
        $log->action = $details[3];
        $log->IP_address = $details[4];
        $log->resource = $details[5];
        $log->save();
    }

    public function add(Request $request)
    {
        $this->validate($request, array(
            'name' => 'required|max:40',
        ));

        if(Commodity::where('name', $request->name)->first() != null) {
            return redirect()->back()->with('error', 'Commodity name already taken.');
        }
        
        if($request->subtypes != null) {
            foreach($request->subtypes as $var){
                if( CommoditySubtype::where('name', $var)->first() != null ) {
                    return redirect()->back()->with('error', 'Subcommodity already under a commodity.');
                }
            }
        }

        $commodity = new Commodity();
        $commodity->name = ucwords($request->name);
        $temp_changes = '<strong>Added </strong>\''. $commodity->name.'\' <strong>to commodities</strong><br>';
        $user = auth()->user();
        $commodity->description = $request->description;
        $commodity->save();
        $id = Commodity::where('name', $request->name)->first()->id;

        if($request->subtypes != null) {
            foreach($request->subtypes as $var){
                $temp_changes = $temp_changes.'<strong>Added Subtype:</strong> '.$var.'<br>';
                $subtype = new CommoditySubtype();
                $subtype->name = $var;
                $subtype->commodity_id = $id;
                $subtype->save();
            }
        }
        
        $this->log([
            $user->id, 
            $user->email, 
            $temp_changes,
            'Added \''. $commodity->name.'\' to commodities',
            $request->ip(), 
            'Commodities'
        ]);

        return redirect()->back()->with('success', 'Commodity Added.');
    }

    public function edit(Request $request, $commodity_id)
    {
        $this->validate($request, array(
            'name' => 'required|max:100',
            'industry' => 'required',
            'description' => 'max:2000',
        ));

        if(Commodity::where('name', $request->name)->where('id', '!=', $commodity_id)->first() != null) {
            return redirect()->back()->with('error', 'Commodity name already taken.');
        }

        $user = auth()->user();
        $temp_changes = '';
        $commodity = Commodity::findOrFail($commodity_id);
        
        if ($commodity->name != $request->name) {
            $temp_changes = $temp_changes.'<strong>Name:</strong> '.$commodity->name.' <strong>-></strong> '.$request->name.'<br>';
            $commodity->name = ucwords($request->name);
        }

        if ($commodity->isp_id != $request->isp_id) {
            $commodity->isp_id = $request->isp;
            $temp_changes = $temp_changes.'<strong>ISP ID:</strong> '.$commodity->isp_id.' <strong>-></strong> '.$request->isp.'<br>';
        }

        if ($commodity->description != $request->description) {
            $commodity->description = $request->description;
            $temp_changes = $temp_changes.'<strong>Description:</strong> '.$commodity->description.' <strong>-></strong> '.$request->description.'<br>';
        }

        if($request->subtypes == null) {
            foreach($commodity->subtypes->all() as $var) {
                CommoditySubtype::find($var->id)->delete();
                $temp_changes = $temp_changes.'<strong>Deleted Subtype:</strong> '.$var->name.'<br>';
            }
        }

        if($commodity->industry_id != $request->industry) {
            $commodity->industry_id = $request->industry;
            $temp_changes = $temp_changes.'<strong>Industry ID:</strong> '.$commodity->industry_id.' <strong>-></strong> '.$request->industry.'<br>';
        }

        if($request->subtypes != null) {
            foreach($request->subtypes as $var){
                if( CommoditySubtype::where('name', $var)->where('commodity_id', '!=', $commodity_id)->first() != null ) {
                    return redirect()->back()->with('error', 'Subcommodity already under a commodity.');
                }
            }

            foreach($request->subtypes as $var) {
                if( CommoditySubtype::where('name', $var)->first() == null ) {
                    CommoditySubtype::create([
                        'commodity_id' => $commodity_id,
                        'name' => $var,
                    ]);
                    $temp_changes = $temp_changes.'<strong>Added Subtype:</strong> '.$var.'<br>';
                }
            }
            
            foreach($commodity->subtypes->all() as $var) {
                if(!in_array($var->name, $request->subtypes)) {
                    CommoditySubtype::find($var->id)->delete();
                    $temp_changes = $temp_changes.'<strong>Deleted Subtype:</strong> '.$var->name.'<br>';
                }
            }
        }
            
        $commodity->save();

        $this->log([
            $user->id,
            $user->email,
            $temp_changes,
            'Edited \''. $commodity->name.'\' details',
            $request->ip(),
            'Commodities'
        ]);
        
        return redirect()->back()->with('success', 'Commodity Updated.');
    }

    public function delete($commodity_id, Request $request)
    {
        $user = auth()->user();
        $commodity = Commodity::findOrFail($commodity_id);
        $commodity->delete();

        $this->log([
            $user->id,
            $user->email,
            '<strong>Deleted </strong>\''. $commodity->name,
            'Deleted \''. $commodity->name.'\'',
            $request->ip(),
            'Commodities'
        ]);
        
        return redirect()->back()->with('success', 'Commodity Deleted.');
    }

    public function editPage()
    {
        return view('pages.commodityEdit');
    }

    public function addPage()
    {
        return view('pages.commodityAdd');
    }
}
