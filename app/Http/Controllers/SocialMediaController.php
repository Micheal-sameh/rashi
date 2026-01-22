<?php

namespace App\Http\Controllers;

use App\Http\Requests\SocialMediaUpdateRequest;
use App\Models\SocialMedia;

class SocialMediaController extends Controller
{
    public function index()
    {
        $socialMedia = SocialMedia::all();

        return view('social-media.index', compact('socialMedia'));
    }

    public function edit($id)
    {
        $socialMedia = SocialMedia::findOrFail($id);

        return view('social-media.edit', compact('socialMedia'));
    }

    public function update(SocialMediaUpdateRequest $request, $id)
    {
        $socialMedia = SocialMedia::findOrFail($id);
        $socialMedia->update([
            'link' => $request->link,
        ]);

        return redirect()->route('social-media.index')->with('success', 'Social media link updated successfully');
    }
}
