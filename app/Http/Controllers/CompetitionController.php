<?php

namespace App\Http\Controllers;

use App\DTOs\CompetitionCreateDTO;
use App\Http\Requests\CreateCompetitionRequest;
use App\Repositories\GroupRepository;
use App\Services\CompetitionService;

class CompetitionController extends Controller
{
    public function __construct(
        protected CompetitionService $competitionService,
        protected GroupRepository $groupRepository,
    ) {
        //
    }

    public function index()
    {
        $competitions = $this->competitionService->index();

        return view('competitions.index', compact('competitions'));
    }

    public function create()
    {
        $groups = $this->groupRepository->dropdown();

        return view('competitions.create', compact('groups'));
    }

    public function store(CreateCompetitionRequest $request)
    {
        $input = new CompetitionCreateDTO(...$request->only(
            'name', 'start_at', 'end_at', 'groups'
        ));

        $this->competitionService->store($input, $request->image);

        return redirect()->route('competitions.index')->with('success', 'Competition created successfully');

    }

    public function edit($id)
    {
        $competition = $this->competitionService->show($id)->load('groups', 'media');
        $groups = $this->groupRepository->dropdown();

        return view('competitions.edit', compact('competition', 'groups'));
    }

    public function update($id, CreateCompetitionRequest $request)
    {
        $input = new CompetitionCreateDTO(...$request->only(
            'name', 'start_at', 'end_at', 'groups'
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
