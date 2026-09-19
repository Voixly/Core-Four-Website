<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GuideController extends Controller
{
    public function index(): View
    {
        $guides = Guide::query()->withCount('downloads')->orderBy('title')->get();

        return view('admin.guides.index', compact('guides'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'excerpt' => ['nullable', 'string'],
            'audience' => ['required', 'in:residential,commercial'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        $filename = null;
        if ($request->hasFile('file')) {
            $filename = $request->file('file')->store('', 'guides');
        }

        Guide::query()->create([
            'title' => $data['title'],
            'slug' => Str::slug($data['title']),
            'excerpt' => $data['excerpt'] ?? null,
            'audience' => $data['audience'],
            'filename' => $filename,
            'is_active' => true,
        ]);

        return back()->with('success', 'Guide added.');
    }

    public function toggle(Guide $guide): RedirectResponse
    {
        $guide->update(['is_active' => ! $guide->is_active]);

        return back();
    }
}
