<?php

namespace App\Http\Controllers;

use App\Enums\AppearanceStatus;
use App\Http\Requests\InfoVideoStoreRequest;
use App\Http\Requests\InfoVideoUpdateRequest;
use App\Models\InfoVideo;
use Illuminate\Http\Request;

class InfoVideoController extends Controller
{
    public function index()
    {
        $infoVideos = InfoVideo::orderBy('rank', 'asc')->get();

        return view('info-videos.index', compact('infoVideos'));
    }

    public function create()
    {
        $appearanceStatuses = AppearanceStatus::all();

        return view('info-videos.create', compact('appearanceStatuses'));
    }

    public function store(InfoVideoStoreRequest $request)
    {
        $infoVideo = InfoVideo::create([
            'name' => $request->name,
            'link' => $request->link,
            'appear' => $request->appear,
        ]);

        return redirect()->route('info-videos.index')->with('success', 'Info video created successfully');
    }

    public function edit($id)
    {
        $infoVideo = InfoVideo::findOrFail($id);
        $appearanceStatuses = AppearanceStatus::all();

        return view('info-videos.edit', compact('infoVideo', 'appearanceStatuses'));
    }

    public function update(InfoVideoUpdateRequest $request, $id)
    {
        $infoVideo = InfoVideo::findOrFail($id);
        $infoVideo->update([
            'name' => $request->name,
            'link' => $request->link,
            'appear' => $request->appear,
        ]);

        return redirect()->route('info-videos.index')->with('success', 'Info video updated successfully');
    }

    public function destroy($id)
    {
        $infoVideo = InfoVideo::findOrFail($id);
        $infoVideo->delete();

        return redirect()->route('info-videos.index')->with('success', 'Info video deleted successfully');
    }

    public function updateRank(Request $request)
    {
        $request->validate([
            'ranks' => 'required|array',
            'ranks.*' => 'required|integer',
        ]);

        foreach ($request->ranks as $id => $rank) {
            InfoVideo::where('id', $id)->update(['rank' => $rank]);
        }

        return response()->json(['success' => true]);
    }
}
