<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGroupRequest;
use App\Http\Requests\UpdateGroupUsers;
use App\Repositories\GroupRepository;
use App\Repositories\UserRepository;
use App\Services\GroupService;
use Illuminate\Support\Str;
use Mpdf\Mpdf;

class GroupController extends Controller
{
    public function __construct(
        protected GroupRepository $groupRepository,
        protected UserRepository $userRepository,
        protected GroupService $groupService,
    ) {}

    public function index()
    {
        $groups = $this->groupRepository->index();
        $totalGroups = $this->groupRepository->getTotalCount();

        return view('groups.index', compact('groups', 'totalGroups'));
    }

    public function create()
    {
        $users = $this->userRepository->dropdown();

        return view('groups.create', compact('users'));
    }

    public function store(CreateGroupRequest $request)
    {
        $this->groupRepository->store($request->name, $request->abbreviation, $request->users);

        return redirect()->route('groups.index')->with('success', 'Group created successfully');
    }

    public function update($id, CreateGroupRequest $request)
    {
        $group = $this->groupRepository->update($id, $request->name, $request->abbreviation);

        return redirect()->route('groups.index')->with('success', "Group $group->name updated successfully");
    }

    public function updateUsers($id, UpdateGroupUsers $request)
    {
        $group = $this->groupRepository->updateUsers($id, $request->users);

        return redirect()->route('groups.index')->with('success', "Group $group->name updated successfully");
    }

    public function usersedit($id)
    {
        $group = $this->groupRepository->show($id);
        $users = $this->userRepository->dropdown();

        return view('groups.usersEdit', compact('users', 'group'));
    }

    public function edit($id)
    {
        $group = $this->groupRepository->show($id);
        $users = $this->userRepository->dropdown();

        return view('groups.edit', compact('users', 'group'));
    }

    public function competitions()
    {
        $groupId = request()->integer('group_id');
        $viewData = $this->groupService->getCompetitionsFlowchartData($groupId);

        return view('groups.competitions', $viewData);
    }

    public function competitionsExportPdf()
    {
        $groupId = request()->integer('group_id');

        abort_unless($groupId, 422, 'Group is required');

        $viewData = $this->groupService->getCompetitionsFlowchartData($groupId);
        $selectedGroup = $viewData['selectedGroup'];

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'arial',
        ]);

        $html = view('groups.competitions-flowchart-pdf', $viewData)->render();
        $mpdf->WriteHTML($html);

        $pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
        $downloadName = Str::slug(($selectedGroup?->name ?? 'group').'-competitions-flowchart');

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$downloadName.'.pdf"',
        ]);
    }
}
