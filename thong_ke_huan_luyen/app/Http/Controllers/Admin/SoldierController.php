<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Soldier;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SoldierController extends Controller
{
    public function __construct()
    {
        // Kiểm tra quyền dựa trên role
        $this->middleware('auth');
    }

    // Hiển thị danh sách quân nhân (có phân trang)
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Soldier::with('unit');

        // Phân quyền xem: Chỉ huy xem tất cả, các cấp khác chỉ xem đơn vị mình và cấp dưới
        if (!$user->hasRole('chi-huy')) {
            $query->whereIn('unit_id', $user->getAccessibleUnitIds());
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%')
                  ->orWhere('rank', 'like', '%' . $request->search . '%');
            });
        }

        // Lọc theo đơn vị
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        // Lọc theo cấp đơn vị (bao gồm cấp hiện tại và các cấp thấp hơn)
        if ($request->filled('level')) {
            $levels = ['chi-huy', 'trung-doan', 'tieu-doan', 'dai-doi', 'trung-doi'];
            $currentIndex = array_search($request->level, $levels);
            
            if ($currentIndex !== false) {
                $targetLevels = array_slice($levels, $currentIndex);
                $query->whereHas('unit', function($q) use ($targetLevels) {
                    $q->whereIn('level', $targetLevels);
                });
            }
        }

        $soldiers = $query->orderBy('unit_id')->orderBy('full_name')->get();
        $units = $user->getAccessibleUnits();

        return view('backend.soldiers.index', compact('soldiers', 'units'));
    }

    // Form thêm mới
    public function create()
    {
        $this->authorize('create', Soldier::class);
        $units = Auth::user()->getAccessibleUnits();
        return view('backend.soldiers.create', compact('units'));
    }

    // Lưu quân nhân mới
    public function store(Request $request)
    {
        $this->authorize('create', Soldier::class);

        $validated = $request->validate([
            'code' => 'required|unique:soldiers',
            'full_name' => 'required',
            'rank' => 'required',
            'position' => 'required',
            'birth_date' => 'required|date',
            'enlistment_date' => 'required|date',
            'party_join_date' => 'nullable|date',
            'education' => 'required',
            'foreign_language' => 'nullable',
            'professional_level' => 'nullable',
            'permanent_residence' => 'required',
            'emergency_contact_name' => 'required',
            'emergency_contact_address' => 'required',
            'notes' => 'nullable',
            'unit_id' => 'required|exists:units,id'
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        $soldier = Soldier::create($validated);

        // Tự động tạo bản ghi vũ khí trang bị cho quân nhân mới
        \App\Models\WeaponEquipment::create([
            'soldier_id' => $soldier->id,
            'unit_id' => $soldier->unit_id,
            'status' => 'dang-su-dung',
            'receive_date' => now(),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Tự động tạo bản ghi khen thưởng cho quân nhân mới
        \App\Models\Reward::create([
            'type' => 'unit',
            'soldier_id' => $soldier->id,
            'soldier_name_at_time' => $soldier->full_name,
            'unit_id' => $soldier->unit_id,
            'unit_name_at_time' => $soldier->unit->name ?? 'N/A',
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Tự động tạo bản ghi nhật ký huấn luyện cho quân nhân mới
        \App\Models\TrainingLog::create([
            'soldier_id' => $soldier->id,
            'soldier_name_at_time' => $soldier->full_name,
            'unit_id' => $soldier->unit_id,
            'unit_name_at_time' => $soldier->unit->name ?? 'N/A',
            'training_date' => now(),
            'day_of_week' => $this->getVietnameseDayOfWeek(now()),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Tự động tạo bản ghi kỷ luật cho quân nhân mới
        \App\Models\Discipline::create([
            'soldier_id' => $soldier->id,
            'soldier_name_at_time' => $soldier->full_name,
            'soldier_rank_at_time' => $soldier->rank,
            'unit_id' => $soldier->unit_id,
            'unit_name_at_time' => $soldier->unit->name ?? 'N/A',
            'status' => 'da-thi-hanh-xong',
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('soldiers.index')
            ->with('success', 'Thêm quân nhân thành công!');
    }

    // Xem chi tiết
    public function show(Soldier $soldier)
    {
        $this->authorize('view', $soldier);
        return view('backend.soldiers.show', compact('soldier'));
    }

    // Form sửa
    public function edit(Soldier $soldier)
    {
        $this->authorize('update', $soldier);
        $units = Auth::user()->getAccessibleUnits();
        return view('backend.soldiers.edit', compact('soldier', 'units'));
    }

    // Cập nhật
    public function update(Request $request, Soldier $soldier)
    {
        $this->authorize('update', $soldier);

        $validated = $request->validate([
            'code' => 'required|unique:soldiers,code,' . $soldier->id,
            'full_name' => 'required',
            'rank' => 'required',
            'position' => 'required',
            'birth_date' => 'required|date',
            'enlistment_date' => 'required|date',
            'party_join_date' => 'nullable|date',
            'education' => 'required',
            'foreign_language' => 'nullable',
            'professional_level' => 'nullable',
            'permanent_residence' => 'required',
            'emergency_contact_name' => 'required',
            'emergency_contact_address' => 'required',
            'notes' => 'nullable',
            'unit_id' => 'required|exists:units,id'
        ]);

        $validated['updated_by'] = Auth::id();
        $soldier->update($validated);

        return redirect()->route('soldiers.index')
            ->with('success', 'Cập nhật quân nhân thành công!');
    }

    // Xóa quân nhân
    public function destroy(Soldier $soldier)
    {
        $this->authorize('delete', $soldier);
        $soldier->delete();

        return redirect()->route('soldiers.index')
            ->with('success', 'Xóa quân nhân thành công!');
    }

    private function getVietnameseDayOfWeek($date)
    {
        $days = [
            'Monday' => 'Thứ 2',
            'Tuesday' => 'Thứ 3',
            'Wednesday' => 'Thứ 4',
            'Thursday' => 'Thứ 5',
            'Friday' => 'Thứ 6',
            'Saturday' => 'Thứ 7',
            'Sunday' => 'Chủ nhật'
        ];
        return $days[$date->format('l')];
    }

    // Lấy danh sách đơn vị người dùng có quyền tiếp cận

    private function getAccessibleUnits()
    {
        return Auth::user()->getAccessibleUnits();
    }

    public function search(Request $request)
    {
        $units = $this->getAccessibleUnits();
        $query = Soldier::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                // Tìm kiếm thông tin quân nhân (tất cả các trường văn bản)
                $sub->where('full_name', 'like', "%$q%")
                    ->orWhere('code', 'like', "%$q%")
                    ->orWhere('rank', 'like', "%$q%")
                    ->orWhere('position', 'like', "%$q%")
                    ->orWhere('education', 'like', "%$q%")
                    ->orWhere('foreign_language', 'like', "%$q%")
                    ->orWhere('professional_level', 'like', "%$q%")
                    ->orWhere('permanent_residence', 'like', "%$q%")
                    ->orWhere('emergency_contact_name', 'like', "%$q%")
                    ->orWhere('emergency_contact_address', 'like', "%$q%")
                    ->orWhere('notes', 'like', "%$q%")
                    // Tìm kiếm theo số hiệu vũ khí hoặc loại vũ khí
                    ->orWhereHas('weapons', function($w) use ($q) {
                        $w->where('status', 'dang-su-dung')
                          ->where(function($query) use ($q) {
                              $weaponFields = [
                                  'ak', 'rpd', 'b41', 'm79', 'cleaning_rod', 'spare_parts', 
                                  'gun_strap', 'gun_accessories', 'magazine_box', 'oil_can', 
                                  'bag', 'gun_cover', 'muzzle_cover', 'sight', 'grenade', 
                                  'infantry_shovel', 'infantry_pickaxe'
                              ];
                              
                              $lowerQ = mb_strtolower($q);
                              
                              // 1. Tìm theo số hiệu (like %q%)
                              foreach ($weaponFields as $field) {
                                  $query->orWhere($field, 'like', "%$q%");
                              }
                              
                              // 2. Nếu từ khóa là tên loại vũ khí (ak, rpd...), tìm tất cả ai có biên chế loại đó
                              if (in_array($lowerQ, ['ak', 'rpd', 'b41', 'm79'])) {
                                  $query->orWhereNotNull($lowerQ)->where($lowerQ, '!=', '');
                              }
                              
                              // 3. Hỗ trợ từ khóa tiếng Việt hoặc có dấu
                              if (str_contains($lowerQ, 'súng ak') || str_contains($lowerQ, 'sung ak')) {
                                  $query->orWhereNotNull('ak')->where('ak', '!=', '');
                              }
                              if (str_contains($lowerQ, 'súng rpd') || str_contains($lowerQ, 'sung rpd')) {
                                  $query->orWhereNotNull('rpd')->where('rpd', '!=', '');
                              }
                              if (str_contains($lowerQ, 'súng b41') || str_contains($lowerQ, 'sung b41')) {
                                  $query->orWhereNotNull('b41')->where('b41', '!=', '');
                              }
                              if (str_contains($lowerQ, 'xẻng') || str_contains($lowerQ, 'xeng')) {
                                  $query->orWhereNotNull('infantry_shovel')->where('infantry_shovel', '!=', '');
                              }
                              if (str_contains($lowerQ, 'cuốc') || str_contains($lowerQ, 'cuoc')) {
                                  $query->orWhereNotNull('infantry_pickaxe')->where('infantry_pickaxe', '!=', '');
                              }
                          });
                    });
            });
        }

        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        $soldiers = $query->with(['unit', 'weapons' => function($q) {
            $q->where('status', 'dang-su-dung');
        }])->paginate(20);

        return view('backend.search.index', compact('soldiers', 'units'));
    }
}
