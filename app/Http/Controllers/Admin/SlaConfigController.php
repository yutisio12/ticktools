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
            'resolution_hours' => 'required|integer|min:1',
        ]);

        // Check if config already exists
        $exists = SlaConfig::where('category_id', $request->category_id)
            ->where('priority', $request->priority)
            ->exists();
            
        if ($exists) {
            return back()->with('error', 'SLA Config for this category and priority already exists.');
        }

        SlaConfig::create($request->only(['category_id', 'priority', 'resolution_hours']));

        return back()->with('success', 'SLA Config created successfully.');
    }

    public function destroy(SlaConfig $sla)
    {
        $sla->delete();
        return back()->with('success', 'SLA Config deleted successfully.');
    }
}
