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
            'name', 'start_at', 'end_at',
        ));

        $this->competitionService->store($input, $request->image);

        return redirect()->route('competitions.index')->with('success', 'Competition created successfully');

    }

    public function edit($id)
    {
        $competition = $this->competitionService->show($id);

        return view('competitions.edit', compact('competition'));
    }

    public function update($id, CreateCompetitionRequest $request)
    {
        $input = new CompetitionCreateDTO(...$request->only(
            'name', 'start_at', 'end_at',
        ));

        $this->competitionService->update($id, $input, $request->image);

        return redirect()->route('competitions.index')->with('success', 'Competition updated successfully');
    }

    public function cancel($id)
    {
        $this->competitionService->cancel($id);

        return redirect()->route('competitions.index')->with('success', 'Competition cancelled successfully');
    }

    public function changeStatus($id)
    {
        $data = $this->competitionService->changeStatus($id);

        return response()->json([
            'competition' => $data['status'],
            'status_class' => $data['statusClass'],
        ]);
    }
}
