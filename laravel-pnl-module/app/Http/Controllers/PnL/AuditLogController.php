<?php

namespace App\Http\Controllers\PnL;

use App\Http\Controllers\Controller;
use App\Models\PnL\PnlAuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $query = PnlAuditLog::forUser($userId)->with('user');

        // Filters
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('model_type')) {
            $query->where('auditable_type', 'like', '%' . $request->model_type . '%');
        }
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50)->withQueryString();
        $actions = PnlAuditLog::getActions();

        return view('pnl.audit.index', compact('logs', 'actions'));
    }

    public function show(PnlAuditLog $auditLog)
    {
        $this->authorize('view', $auditLog);

        $auditLog->load(['user', 'auditable']);

        return view('pnl.audit.show', compact('auditLog'));
    }
}
