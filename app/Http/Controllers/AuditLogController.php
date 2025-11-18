<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Contracts\View\View;

class AuditLogController extends Controller
{
    public function index(): View
    {
        $logs = AuditLog::with('user')
            ->latest()
            ->paginate(25);

        return view('audit_logs.index', compact('logs'));
    }
}
