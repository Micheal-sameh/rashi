<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendNotificationRequest;
use App\Models\Group;
use App\Models\User;
use App\Repositories\NotificationRepository;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationRepository $notificationRepository
    ) {}

    public function index()
    {
        $notifications = $this->notificationRepository->index();

        return view('notifications.index', compact('notifications'));
    }

    public function create()
    {
        $users = User::select('id', 'name')->get();
        $groups = Group::select('id', 'name')->get();

        return view('notifications.create', compact('users', 'groups'));
    }

    public function store(SendNotificationRequest $request)
    {
        $userIds = [];

        if ($request->target_type === 'user') {
            $userIds = [$request->user_id];
        } elseif ($request->target_type === 'group') {
            $group = Group::findOrFail($request->group_id);
            $userIds = $group->users->pluck('id')->toArray();
        }

        $this->notificationRepository->createNotificationForUsers($userIds, 'manual send '.$request->message);

        return redirect()->route('notifications.index')->with('success', 'Notification sent successfully');
    }
}
