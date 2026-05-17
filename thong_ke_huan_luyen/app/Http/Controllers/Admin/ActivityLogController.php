<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])->latest();

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', 'like', '%' . $request->subject_type);
        }

        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->causer_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $activities = $query->paginate(20)->withQueryString();

        $users = \App\Models\User::orderBy('name')->get();
        
        $subjectTypes = Activity::groupBy('subject_type')
            ->whereNotNull('subject_type')
            ->pluck('subject_type')
            ->map(function($type) {
                return str_replace('App\\Models\\', '', $type);
            })->unique();

        return view('backend.activity_logs.index', compact('activities', 'users', 'subjectTypes'));
    }
}
