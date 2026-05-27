<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('asset_tag', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
        }
        
        $assets = $query->paginate(15);
        return view('admin.assets.index', compact('assets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_tag' => 'required|string|unique:assets,asset_tag',
            'name' => 'required|string',
            'type' => 'required|string',
            'location' => 'nullable|string',
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        Asset::create($request->all());

        return back()->with('success', 'Asset created successfully.');
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'asset_tag' => 'required|string|unique:assets,asset_tag,' . $asset->id,
            'name' => 'required|string',
            'type' => 'required|string',
            'location' => 'nullable|string',
            'assigned_user_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $asset->update($request->all());

        return back()->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return back()->with('success', 'Asset deleted successfully.');
    }
}
