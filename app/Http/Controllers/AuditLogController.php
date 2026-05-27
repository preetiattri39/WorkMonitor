<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Contracts\View\View;

class AuditLogController extends Controller
{
    public function __invoke(): View
    {
        $logs = AuditLog::with('actor')->latest('created_at')->paginate();

        return view('pages.audit.index', compact('logs'));
    }
}
