<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\LeaveRequest;
use App\Notifications\LeaveRequestStatusNotification;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveController extends Controller
{
    public function index(Request $request): View
    {
        $leaveRequests = LeaveRequest::with(['user', 'approver'])
            ->when($request->user()->role === RoleEnum::EMPLOYEE, fn ($query) => $query->where('user_id', $request->user()->id))
            ->latest()
            ->paginate();

        return view('pages.leave.index', compact('leaveRequests'));
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'leave_type' => ['required', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string'],
        ]);

        $leaveRequest = LeaveRequest::create($payload + [
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ]);

        AuditLogger::forRequest($request)->created($leaveRequest);

        return back()->with('status', 'Leave request submitted.');
    }

    public function update(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $payload = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected,cancelled'],
        ]);

        $original = $leaveRequest->getOriginal();
        $leaveRequest->update($payload + [
            'approver_id' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $leaveRequest->user->notify(new LeaveRequestStatusNotification($leaveRequest));
        AuditLogger::forRequest($request)->updated($leaveRequest, $original);

        return back()->with('status', 'Leave request updated.');
    }
}
