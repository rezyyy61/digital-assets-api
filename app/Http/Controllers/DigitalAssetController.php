<?php

namespace App\Http\Controllers;

use App\Models\DigitalAsset;
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

    public function mint($id, Request $request)
    {
        $asset = $request->user()->digitalAssets()->findOrFail($id);

        if ($asset->is_minted) {
            return response()->json([
                'message' => 'This asset is already minted.'
            ], 400);
        }

        $asset->is_minted = true;
        $asset->minted_url = 'https://explorer.sui.io/fake-nft/' . $asset->id;
        $asset->save();

        return response()->json([
            'message' => 'Asset marked as minted (simulated).',
            'asset' => $asset
        ]);
    }

}
