<?php

namespace App\Http\Controllers;

use App\Jobs\MintNftJob;
use App\Models\DigitalAsset;
use App\Models\MintRequest;
use App\Services\SuiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DigitalAssetController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:10240',
        ]);


        $path = $request->file('file')->store('digital-assets', 'public');

        $asset = DigitalAsset::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
        ]);

        return response()->json([
            'message' => 'Digital asset uploaded successfully.',
            'asset' => $asset
        ], 201);
    }

    public function index(Request $request)
    {
        $assets = $request->user()->digitalAssets()->latest()->get()->map(function ($asset) {
            $asset->file_url = asset('storage/' . $asset->file_path);
            return $asset;
        });

        return response()->json([
            'assets' => $assets
        ]);
    }

    public function mint($id, Request $request, SuiService $suiService)
    {
        $asset = $request->user()->digitalAssets()->findOrFail($id);

        if ($asset->is_minted) {
            return response()->json([
                'message' => 'This asset is already minted.'
            ], 400);
        }

        $existing = MintRequest::where('digital_asset_id', $asset->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'A mint request is already pending.'
            ], 400);
        }

        $mintRequest = MintRequest::create([
            'digital_asset_id' => $asset->id,
        ]);


        MintNftJob::dispatch($mintRequest);

        return response()->json([
            'message' => 'Mint request registered. It will be processed soon.',
        ], 201);
    }


}
