<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        // Get all companies with their addresses that have latitude and longitude
        $companies = Company::with(['address'])
            ->whereHas('address', function($query) {
                $query->whereNotNull('latitude')
                      ->whereNotNull('longitude');
            })
            ->get();

        return view('maps.index', compact('companies'));
        
    }
}

