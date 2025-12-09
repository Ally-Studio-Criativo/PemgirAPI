<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::orderBy('created_at', 'desc')->get();
        return response()->json($campaigns);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'campaign_text_pt' => 'nullable|string|max:255',
            'campaign_text_en' => 'nullable|string|max:255',
            'campaign_text_es' => 'nullable|string|max:255',
            'launch_text_pt' => 'nullable|string|max:255',
            'launch_text_en' => 'nullable|string|max:255',
            'launch_text_es' => 'nullable|string|max:255',
            'title_pt' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_es' => 'nullable|string|max:255',
            'season_pt' => 'nullable|string|max:255',
            'season_en' => 'nullable|string|max:255',
            'season_es' => 'nullable|string|max:255',
            'button_text_pt' => 'nullable|string|max:255',
            'button_text_en' => 'nullable|string|max:255',
            'button_text_es' => 'nullable|string|max:255',
            'youtube_url' => 'nullable|string|max:500',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $campaign = Campaign::create($request->all());
        return response()->json($campaign, 201);
    }

    public function show(string $id)
    {
        $campaign = Campaign::findOrFail($id);
        return response()->json($campaign);
    }

    public function update(Request $request, string $id)
    {
        $campaign = Campaign::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'campaign_text_pt' => 'nullable|string|max:255',
            'campaign_text_en' => 'nullable|string|max:255',
            'campaign_text_es' => 'nullable|string|max:255',
            'launch_text_pt' => 'nullable|string|max:255',
            'launch_text_en' => 'nullable|string|max:255',
            'launch_text_es' => 'nullable|string|max:255',
            'title_pt' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_es' => 'nullable|string|max:255',
            'season_pt' => 'nullable|string|max:255',
            'season_en' => 'nullable|string|max:255',
            'season_es' => 'nullable|string|max:255',
            'button_text_pt' => 'nullable|string|max:255',
            'button_text_en' => 'nullable|string|max:255',
            'button_text_es' => 'nullable|string|max:255',
            'youtube_url' => 'nullable|string|max:500',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $campaign->update($request->all());
        return response()->json($campaign);
    }

    public function destroy(string $id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->delete();
        return response()->json(['message' => 'Campanha deletada com sucesso'], 200);
    }
}
