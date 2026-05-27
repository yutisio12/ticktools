<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SlaConfig;
use App\Enums\Priority;
use App\Models\Category;
use Illuminate\Http\Request;

class SlaConfigController extends Controller
{
    public function index()
    {
        $slaConfigs = SlaConfig::with('category')->get();
        $categories = Category::all();
        $priorities = Priority::cases();
        
        return view('admin.sla.index', compact('slaConfigs', 'categories', 'priorities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|string',
            'resolve_within_minutes' => 'required|integer|min:1',
            'response_within_minutes' => 'nullable|integer|min:1',
        ]);

        // Check if config already exists
        $exists = SlaConfig::where('category_id', $request->category_id)
            ->where('priority', $request->priority)
            ->exists();
            
        if ($exists) {
            return back()->with('error', 'SLA Config for this category and priority already exists.');
        }

        SlaConfig::create($request->all());

        return back()->with('success', 'SLA Config created successfully.');
    }

    public function destroy(SlaConfig $slaConfig)
    {
        $slaConfig->delete();
        return back()->with('success', 'SLA Config deleted successfully.');
    }
}
