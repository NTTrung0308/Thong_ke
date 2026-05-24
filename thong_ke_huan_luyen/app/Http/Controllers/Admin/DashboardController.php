<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discipline;
use App\Models\Reward;
use App\Models\Soldier;
use App\Models\TrainingResult;
use App\Models\Unit;
use App\Models\WeaponEquipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $accessibleUnitIds = $user->getAccessibleUnitIds();

        // 1. Thống kê tổng quát (Thẻ)
        $totalSoldiers = Soldier::whereIn('unit_id', $accessibleUnitIds)->count();
        $totalUnits = Unit::whereIn('id', $accessibleUnitIds)->count();
        $totalWeapons = WeaponEquipment::whereHas('soldier', function ($q) use ($accessibleUnitIds) {
            $q->whereIn('unit_id', $accessibleUnitIds);
        })->count();
        $totalRewards = Reward::whereIn('unit_id', $accessibleUnitIds)->count();
        $totalDisciplines = Discipline::whereIn('unit_id', $accessibleUnitIds)->count();

        // 2. Dữ liệu biểu đồ Quân số theo Đơn vị
        $unitsForChart = Unit::whereIn('id', $accessibleUnitIds)
            ->whereIn('level', ['dai-doi', 'trung-doi']) // Hiển thị cấp nhỏ để chi tiết
            ->withCount('soldiers')
            ->orderBy('soldiers_count', 'desc')
            ->take(10)
            ->get();

        $chartUnitNames = $unitsForChart->pluck('name');
        $chartUnitCounts = $unitsForChart->pluck('soldiers_count');

        // 3. Dữ liệu biểu đồ Kết quả huấn luyện (Năm nay)
        $currentYear = date('Y');
        $trainingStats = TrainingResult::whereIn('unit_id', $accessibleUnitIds)
            ->whereYear('training_date', $currentYear)
            ->selectRaw('result, count(*) as count')
            ->groupBy('result')
            ->get();

        $resultLabels = ['Xuất sắc', 'Giỏi', 'Khá', 'Trung bình', 'Yếu'];
        $resultData = [0, 0, 0, 0, 0];

        foreach ($trainingStats as $stat) {
            $label = mb_strtolower($stat->result);
            if (str_contains($label, 'xuất sắc')) {
                $resultData[0] += $stat->count;
            } elseif (str_contains($label, 'giỏi')) {
                $resultData[1] += $stat->count;
            } elseif (str_contains($label, 'khá')) {
                $resultData[2] += $stat->count;
            } elseif (str_contains($label, 'trung bình')) {
                $resultData[3] += $stat->count;
            } elseif (str_contains($label, 'yếu')) {
                $resultData[4] += $stat->count;
            }
        }

        // 3b. Thống kê theo tháng cho phân tích (sử dụng cùng dữ liệu năm hiện tại)
        $trainings = TrainingResult::whereIn('unit_id', $accessibleUnitIds)
            ->whereYear('training_date', $currentYear)
            ->get();

        $resultsKeys = ['xuất_sắc', 'giỏi', 'khá', 'trung_bình', 'yếu'];
        $statsByMonth = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthTrainings = $trainings->filter(function ($item) use ($month) {
                return $item->training_date && $item->training_date->month == $month;
            });

            $byResult = [];
            foreach ($resultsKeys as $rk) {
                $byResult[$rk] = $monthTrainings->where('result', $rk)->count();
            }

            $statsByMonth[$month] = [
                'total' => $monthTrainings->count(),
                'total_hours' => $monthTrainings->sum('duration_hours'),
                'avg_passing_rate' => $monthTrainings->avg('passing_rate') ?? 0,
                'by_result' => $byResult,
            ];
        }

        // 4. Nhật ký hoạt động gần đây (Activity Stream)
        $activities = Activity::with(['causer', 'subject'])
            ->latest()
            ->take(10)
            ->get();

        return view('backend.dashboard', compact(
            'totalSoldiers', 'totalUnits', 'totalWeapons', 'totalRewards', 'totalDisciplines',
            'chartUnitNames', 'chartUnitCounts', 'resultLabels', 'resultData', 'activities', 'statsByMonth'
        ));
    }

    public function getTreeData()
    {
        $user = Auth::user();
        $rootUnits = [];

        if ($user->hasRole('chi-huy')) {
            $rootUnits = Unit::whereNull('parent_id')->with('children')->get();
        } else {
            $rootUnits = Unit::where('id', $user->unit_id)->with('children')->get();
        }

        return response()->json($this->formatTree($rootUnits));
    }

    public function getUnitsByLevel(Request $request)
    {
        $level = $request->get('level');
        $parentId = $request->get('parent_id');
        $user = Auth::user();
        $accessibleUnitIds = $user->getAccessibleUnitIds();

        $query = Unit::whereIn('id', $accessibleUnitIds);

        if ($parentId) {
            $query->where('parent_id', $parentId);
        } elseif ($level) {
            $query->where('level', $level);
        }

        $units = $query->withCount('children')
            ->orderBy('name')
            ->get(['id', 'name', 'level']);

        return response()->json($units);
    }

    // API: Dashboard stats for async loading
    public function dashboardStats(Request $request)
    {
        $user = Auth::user();
        $accessibleUnitIds = $user->getAccessibleUnitIds();
        $year = $request->get('year', date('Y'));
        $unitId = $request->get('unit_id');

        $cacheKey = 'dashboard_stats_'.$user->id.'_'.$year.'_'.($unitId ?? 'all');

        $payload = Cache::remember($cacheKey, 60, function () use ($accessibleUnitIds, $year, $unitId) {
            $query = TrainingResult::whereIn('unit_id', $accessibleUnitIds)
                ->whereYear('training_date', $year);

            if ($unitId) {
                $query->where('unit_id', $unitId);
            }

            $trainings = $query->get();

            $resultsKeys = ['xuất_sắc', 'giỏi', 'khá', 'trung_bình', 'yếu'];
            $statsByMonth = [];
            for ($month = 1; $month <= 12; $month++) {
                $monthTrainings = $trainings->filter(function ($item) use ($month) {
                    return $item->training_date && $item->training_date->month == $month;
                });

                $byResult = [];
                foreach ($resultsKeys as $rk) {
                    $byResult[$rk] = $monthTrainings->where('result', $rk)->count();
                }

                $statsByMonth[$month] = [
                    'total' => $monthTrainings->count(),
                    'total_hours' => $monthTrainings->sum('duration_hours'),
                    'avg_passing_rate' => $monthTrainings->avg('passing_rate') ?? 0,
                    'by_result' => $byResult,
                ];
            }

            // Aggregate totals
            $totals = [
                'results' => [
                    'xuất_sắc' => $trainings->where('result', 'xuất_sắc')->count(),
                    'giỏi' => $trainings->where('result', 'giỏi')->count(),
                    'khá' => $trainings->where('result', 'khá')->count(),
                    'trung_bình' => $trainings->where('result', 'trung_bình')->count(),
                    'yếu' => $trainings->where('result', 'yếu')->count(),
                ],
                'total_trainings' => $trainings->count(),
                'total_hours' => $trainings->sum('duration_hours'),
            ];

            return ['statsByMonth' => $statsByMonth, 'totals' => $totals];
        });

        return response()->json($payload);
    }

    private function formatTree($units)
    {
        $data = [];
        foreach ($units as $unit) {
            $node = [
                'text' => $unit->name.' ('.$unit->level.')',
                'id' => $unit->id,
                'tags' => [$unit->soldiers_count ?? 0],
            ];
            if ($unit->children->count() > 0) {
                $node['nodes'] = $this->formatTree($unit->children);
            }
            $data[] = $node;
        }

        return $data;
    }
}
