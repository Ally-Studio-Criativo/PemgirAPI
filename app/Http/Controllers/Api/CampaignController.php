<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    /**
     * Get the active campaign for the landing page
     */
    public function active()
    {
        $campaign = Campaign::where('active', true)->first();

        if (!$campaign) {
            return response()->json(null, 404);
        }

        return response()->json($campaign);
    }
}
