<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainingSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingSubjectController extends Controller
{
    public function index(Request $request)
    {
        $unitLevel = $request->get('unit_level', null);

n        $query = TrainingSubject::whereNull('parent_id');
        if ($unitLevel) {
            $query->where('unit_level', $unitLevel);
        }

        $subjects = $query->with(['children' => function($q) use ($unitLevel) {
            if ($unitLevel) {
                $q->where('unit_level', $unitLevel);
            }
            $q->with(['children' => function($q2) use ($unitLevel) {
                if ($unitLevel) {
                    $q2->where('unit_level', $unitLevel);
                }
            }]);
        }])->get();

        return response()->json($subjects);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:training_subjects,id',
            'unit_level' => 'required|in:dai-doi,trung-doi'
        ]);

        $validated['created_by'] = Auth::id();

        $subject = TrainingSubject::create($validated);

        return response()->json([
            'success' => true,
            'subject' => $subject,
            'message' => 'Đã thêm nội dung huấn luyện thành công.'
        ]);
    }

    public function getChildren(Request $request, $parentId)
    {
        $unitLevel = $request->get('unit_level', null);
        $query = TrainingSubject::where('parent_id', $parentId);
        if ($unitLevel) {
            $query->where('unit_level', $unitLevel);
        }
        $children = $query->get();
        return response()->json($children);
    }
}
