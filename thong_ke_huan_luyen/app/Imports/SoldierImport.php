<?php

namespace App\Imports;

use App\Models\Soldier;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SoldierImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $defaultUnitId;

    public function __construct($defaultUnitId = null)
    {
        $this->defaultUnitId = $defaultUnitId;
    }

    public function model(array $row)
    {
        // Tìm đơn vị theo tên nếu có cung cấp, nếu không dùng đơn vị mặc định
        $unitId = $this->defaultUnitId;
        if (! empty($row['don_vi'])) {
            $unit = Unit::where('name', 'like', '%'.$row['don_vi'].'%')->first();
            if ($unit) {
                $unitId = $unit->id;
            }
        }

        // Nếu vẫn không có unit_id, chúng ta không thể tạo soldier (vì unit_id là khóa ngoại bắt buộc)
        if (! $unitId) {
            // Đây là trường hợp hiếm nếu form đã bắt buộc chọn unit_id mặc định
            return null;
        }

        return new Soldier([
            'code' => $row['so_hieu'],
            'full_name' => $row['ho_va_ten'],
            'rank' => $row['cap_bac'] ?? 'Binh nhì',
            'position' => $row['chuc_vu'] ?? 'Chiến sỹ',
            'birth_date' => $this->transformDate($row['ngay_sinh']),
            'enlistment_date' => $this->transformDate($row['ngay_nhap_ngu']),
            'party_join_date' => $this->transformDate($row['ngay_vao_dang_doan'] ?? null),
            'education' => $row['hoc_van'] ?? '12/12',
            'foreign_language' => $row['ngoai_ngu'] ?? null,
            'professional_level' => $row['trinh_do_chuyen_mon'] ?? null,
            'permanent_residence' => $row['ho_khau_thuong_tru'] ?? 'N/A',
            'emergency_contact_name' => $row['nguoi_bao_tin'] ?? 'N/A',
            'emergency_contact_address' => $row['dia_chi_bao_tin'] ?? 'N/A',
            'notes' => $row['ghi_chu'] ?? null,
            'unit_id' => $unitId,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'so_hieu' => 'required|unique:soldiers,code',
            'ho_va_ten' => 'required|string|max:255',
            'ngay_sinh' => 'required',
            'ngay_nhap_ngu' => 'required',
            'hoc_van' => 'required',
            'ho_khau_thuong_tru' => 'required',
            'nguoi_bao_tin' => 'required',
            'dia_chi_bao_tin' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'so_hieu.required' => 'Cột "Số hiệu" không được để trống.',
            'so_hieu.unique' => 'Số hiệu ":input" đã tồn tại trong hệ thống.',
            'ho_va_ten.required' => 'Cột "Họ và tên" không được để trống.',
            'ngay_sinh.required' => 'Cột "Ngày sinh" không được để trống.',
            'ngay_nhap_ngu.required' => 'Cột "Ngày nhập ngũ" không được để trống.',
        ];
    }

    private function transformDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value);
            }

            // Thử parse các định dạng phổ biến ở VN
            return Carbon::createFromFormat('d/m/Y', $value);
        } catch (\Exception $e) {
            try {
                return Carbon::parse($value);
            } catch (\Exception $e2) {
                return null;
            }
        }
    }
}
