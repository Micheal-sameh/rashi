<?php

namespace App\Http\Controllers;

use App\DTOs\CompetitionCreateDTO;
use App\Http\Requests\CreateCompetitionRequest;
use App\Services\CompetitionService;

class CompetitionController extends Controller
{
    public function __construct(protected CompetitionService $competitionService)
    {
        //
    }

    public function index()
    {
        $competitions = $this->competitionService->index();

        return view('competitions.index', compact('competitions'));
    }

    public function create()
    {
        return view('competitions.create');
    }

    public function store(CreateCompetitionRequest $request)
    {
        $input = new CompetitionCreateDTO(...$request->only(
            'name', 'start_at', 'end_at', 'status',
        ));

        $this->competitionService->store($input, $request->image);

        return redirect()->route('competitions.index')->with('success', 'Competition created successfully');

    }
}
