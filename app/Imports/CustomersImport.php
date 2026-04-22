<?php

namespace App\Imports;

use App\Models\Customer;
use App\Services\CodeGeneratorService;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CustomersImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $tenCty = $row->get('ten_khach_hang') ?? $row->get('ten_cty');
            $mst = $row->get('ma_so_thue') ?? $row->get('mst');
            
            if (!$tenCty || !$mst) {
                continue; 
            }

            $existing = Customer::where('ma_so_thue', $mst)->first();
            if ($existing) {
                $this->updateOrCreateCustomer($existing, $row);
            } else {
                $this->createNewCustomer($row);
            }
        }
    }

    protected function createNewCustomer($row)
    {
        $tenCty = $row->get('ten_khach_hang') ?? $row->get('ten_cty');
        $mst = $row->get('ma_so_thue') ?? $row->get('mst');
        $ngay = $this->transformDate($row->get('ngay'));
        
        $maKH = $row->get('ma_khach_hang') ?? $row->get('ma_kh');
        if (!$maKH) {
            $maKH = CodeGeneratorService::generateMaKH($mst, $ngay);
        }

        Customer::create([
            'ma_kh' => $maKH,
            'ten_cty' => $tenCty,
            'ma_so_thue' => $mst,
            'dia_chi' => $row->get('dia_chi'),
            'nguoi_lien_he' => $row->get('nguoi_lien_he'),
            'sdt' => $row->get('so_dien_thoai') ?? $row->get('sdt'),
            'dia_chi_nhan' => $row->get('dia_chi_nhan_hang') ?? $row->get('dia_chi_nhan'),
            'sdt_nhan' => $row->get('sdt_nguoi_nhan') ?? $row->get('sdt_nhan'),
            'email' => $row->get('invoice_email') ?? $row->get('email'),
            'khu_vuc' => $row->get('khu_vuc'),
            'ghi_chu' => $row->get('ghi_chu'),
            'created_date' => $ngay ?: now(),
            'user_id' => Auth::id(),
            'tinh_trang' => 'active',
        ]);
    }

    protected function updateOrCreateCustomer($existing, $row)
    {
        $existing->update([
            'ten_cty' => $row->get('ten_khach_hang') ?? $row->get('ten_cty') ?? $existing->ten_cty,
            'dia_chi' => $row->get('dia_chi') ?? $existing->dia_chi,
            'nguoi_lien_he' => $row->get('nguoi_lien_he') ?? $existing->nguoi_lien_he,
            'sdt' => $row->get('so_dien_thoai') ?? $row->get('sdt') ?? $existing->sdt,
            'dia_chi_nhan' => $row->get('dia_chi_nhan_hang') ?? $row->get('dia_chi_nhan') ?? $existing->dia_chi_nhan,
            'sdt_nhan' => $row->get('sdt_nguoi_nhan') ?? $row->get('sdt_nhan') ?? $existing->sdt_nhan,
            'email' => $row->get('invoice_email') ?? $row->get('email') ?? $existing->email,
            'khu_vuc' => $row->get('khu_vuc') ?? $existing->khu_vuc,
            'ghi_chu' => $row->get('ghi_chu') ?? $existing->ghi_chu,
        ]);
    }

    protected function transformDate($value)
    {
        if (!$value) return null;

        // If it's a numeric value, try Excel date conversion
        if (is_numeric($value)) {
            try {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
            } catch (\Throwable $e) {
                // Fallback to string parsing if conversion fails
            }
        }

        // Try standard date formats
        try {
            if (str_contains($value, '/')) {
                return Carbon::createFromFormat('d/m/Y', $value);
            }
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
