<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainingSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingSubjectController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(TrainingSubject::class, 'training_subject');
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $unitLevel = $request->get('unit_level', 'dai-doi');
            
            $query = TrainingSubject::whereNull('parent_id')
                ->with('children.children');

            if ($unitLevel === 'trung-doi') {
                $query->whereIn('unit_level', ['dai-doi', 'trung-doi']);
            } else {
                $query->where('unit_level', $unitLevel);
            }

            $subjects = $query->get();

            return response()->json($subjects);
        }

        $subjects = TrainingSubject::with(['parent', 'creator'])
            ->orderBy('unit_level')
            ->orderBy('parent_id')
            ->get();

        return view('backend.training_subjects.index', compact('subjects'));
    }

    public function create()
    {
        $subjects = TrainingSubject::where(function ($q) {
            $q->whereNull('parent_id')
                ->orWhereHas('parent', function ($sq) {
                    $sq->whereNull('parent_id');
                });
        })->orderBy('unit_level')->get();

        return view('backend.training_subjects.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:training_subjects,id,deleted_at,NULL',
            'unit_level' => 'required|in:dai-doi,trung-doi',
        ]);

        $validated['created_by'] = Auth::id();
        $subject = TrainingSubject::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'subject' => $subject,
                'message' => 'Đã thêm nội dung huấn luyện thành công.',
            ]);
        }

        return redirect()->route('training-subjects.index')->with('success', 'Thêm nội dung huấn luyện thành công.');
    }

    public function edit(TrainingSubject $trainingSubject)
    {
        $subjects = TrainingSubject::where('id', '!=', $trainingSubject->id)
            ->where(function ($q) {
                $q->whereNull('parent_id')
                    ->orWhereHas('parent', function ($sq) {
                        $sq->whereNull('parent_id');
                    });
            })
            ->orderBy('unit_level')
            ->get();

        return view('backend.training_subjects.edit', compact('trainingSubject', 'subjects'));
    }

    public function update(Request $request, TrainingSubject $trainingSubject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:training_subjects,id,deleted_at,NULL',
            'unit_level' => 'required|in:dai-doi,trung-doi',
        ]);

        // Kiểm tra tránh tạo vòng lặp (Cycle)
        if ($request->parent_id) {
            if ($request->parent_id == $trainingSubject->id) {
                return back()->withErrors(['parent_id' => 'Không thể chọn chính nó làm mục cha.'])->withInput();
            }
            
            $parent = TrainingSubject::find($request->parent_id);
            $descendantIds = $trainingSubject->getAllDescendantIds();
            if (in_array($parent->id, $descendantIds)) {
                return back()->withErrors(['parent_id' => 'Không thể chọn mục con làm mục cha (tạo vòng lặp).'])->withInput();
            }
        }

        $trainingSubject->update($validated);

        return redirect()->route('training-subjects.index')->with('success', 'Cập nhật nội dung huấn luyện thành công.');
    }

    public function destroy(TrainingSubject $trainingSubject)
    {
        // Nhờ có TrainingSubjectObserver, việc xóa sẽ tự động xóa mềm các nội dung con.
        $trainingSubject->delete();

        return redirect()->route('training-subjects.index')->with('success', 'Xóa nội dung huấn luyện và các mục con thành công.');
    }

    public function getChildren(Request $request, $parentId)
    {
        $unitLevel = $request->get('unit_level', null);
        $query = TrainingSubject::where('parent_id', $parentId);
        
        if ($unitLevel === 'trung-doi') {
            $query->whereIn('unit_level', ['dai-doi', 'trung-doi']);
        } elseif ($unitLevel) {
            $query->where('unit_level', $unitLevel);
        }
        
        $children = $query->get();

        return response()->json($children);
    }
}
