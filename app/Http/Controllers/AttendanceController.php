<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\Attendance;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $attendances = Attendance::with('user')
            ->when($request->user()->role === RoleEnum::EMPLOYEE, fn ($query) => $query->where('user_id', $request->user()->id))
            ->latest('date')
            ->paginate();

        return view('pages.attendances.index', [
            'attendances' => $attendances,
            'todayAttendance' => Attendance::where('user_id', $request->user()->id)->whereDate('date', today())->first(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'date' => ['required', 'date'],
            'clock_in_at' => ['required', 'date'],
            'status' => ['required', 'in:present,late,absent,half_day,remote,on_leave'],
            'notes' => ['nullable', 'string'],
        ]);

        $attendance = Attendance::create($payload + ['user_id' => $request->user()->id]);
        AuditLogger::forRequest($request)->created($attendance);

        return back()->with('status', 'Attendance logged.');
    }

    public function update(Request $request, Attendance $attendance): RedirectResponse
    {
        $this->authorize('update', $attendance);

        $payload = $request->validate([
            'clock_out_at' => ['nullable', 'date'],
            'worked_minutes' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'in:present,late,absent,half_day,remote,on_leave'],
            'notes' => ['nullable', 'string'],
        ]);

        $original = $attendance->getOriginal();
        $attendance->update($payload);
        AuditLogger::forRequest($request)->updated($attendance, $original);

        return back()->with('status', 'Attendance updated.');
    }
}
