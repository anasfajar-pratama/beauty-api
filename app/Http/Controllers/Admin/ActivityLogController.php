<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('admin:id,username,name');

        if ($request->admin_id) {
            $query->where('admin_id', $request->admin_id);
        }
        if ($request->action) {
            $query->where('action', $request->action);
        }
        if ($request->subject) {
            $query->where('subject_type', 'like', '%' . $request->subject . '%');
        }
        if ($request->from) {
            $query->where('created_at', '>=', $request->from);
        }
        if ($request->to) {
            $query->where('created_at', '<=', $request->to . ' 23:59:59');
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 30);
        return response()->json($logs);
    }
}
