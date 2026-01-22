<?php

namespace App\Http\Controllers;

use App\DTOs\UsersFilterDTO;
use App\Http\Requests\LeaderBoardRequest;
use App\Http\Requests\UpdateUserGroupRequest;
use App\Http\Resources\UserResource;
use App\Repositories\GroupRepository;
use App\Repositories\PointHistoryRepository;
use App\Services\UserService;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected GroupRepository $groupRepository,
        protected PointHistoryRepository $pointHistoryRepository,
    ) {
        $this->middleware('permission:can_generate_qr_code')->only(['qrCode']);
    }

    public function index(Request $request)
    {
        $input = new UsersFilterDTO(...$request->only(
            'name', 'group_id', 'sort_by', 'direction'
        ));
        $users = $this->userService->index($input);
        $groups = $this->groupRepository->dropdown();
        // $users = UserResource::collection($this->userService->index($input));
        if ($request->is_filter) {
            return view('users.user-table', compact('users'))->render();
        }

        return view('users.index', compact('users', 'groups'));
    }

    public function admins(Request $request)
    {
        $admins = $this->userService->getAdmins($request->search);

        return view('users.admins', compact('admins'));
    }

    public function show($id)
    {
        $user = $this->userService->show($id);
        $points = $this->pointHistoryRepository->userHistory($id);
        $groups = $this->groupRepository->dropdown();

        return view('users.show', compact('user', 'points', 'groups'));
    }

    public function updateGroups(UpdateUserGroupRequest $request, $id)
    {
        $user = $this->userService->updateGroups($request->groups, $id);

        $groupNames = $user->groups->pluck('name')->join(', ') ?: __('messages.not_assigned');

        return redirect()->back()->with('message', 'sucess updated');
    }

    public function leaderboard(LeaderBoardRequest $request)
    {
        $users = $this->userService->leaderboard($request->group_id);
        $groups = $this->groupRepository->dropdown();

        return view('users.leaderboard', compact('users', 'groups'));
    }

    public function exportLeaderboard(LeaderBoardRequest $request)
    {
        $users = $this->userService->leaderboard($request->group_id);

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'arial',
        ]);

        $html = view('users.leaderboard_pdf', compact('users'))->render();

        $mpdf->WriteHTML($html);

        return $mpdf->Output('leaderboard.pdf', 'D');
    }

    public function qrCode($id)
    {
        $user = $this->userService->show($id);

        return view('users.qrcode', compact('user'));
    }
}
