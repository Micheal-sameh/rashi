<?php

namespace App\Http\Controllers;

use App\Services\BonusPenaltyService;
use Illuminate\Http\Request;

class BonusPenaltyController extends Controller
{
    public function __construct(protected BonusPenaltyService $bonusPenaltyService) {}

    public function index(Request $request)
    {
        $bonusPenalties = $this->bonusPenaltyService->index($request->user_id);

        return view('bonus-penalties.index', compact('bonusPenalties'));
    }

    public function create()
    {
        return view('bonus-penalties.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:1,2',
            'points' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $this->bonusPenaltyService->store($request->all());

        return redirect()->route('bonus-penalties.index')->with('success', 'Bonus/Penalty created successfully');
    }

    public function show($id)
    {
        $bonusPenalty = $this->bonusPenaltyService->show($id);

        return view('bonus-penalties.show', compact('bonusPenalty'));
    }
}
