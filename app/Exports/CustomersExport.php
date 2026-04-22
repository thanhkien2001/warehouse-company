<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CustomersExport implements FromQuery, WithMapping, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $query;
    protected $rowNumber = 0;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Ngày',
            'Mã khách hàng',
            'Tên khách hàng',
            'Mã số thuế',
            'Địa chỉ',
            'Người liên hệ',
            'Số điện thoại',
            'Địa chỉ nhận hàng',
            'SĐT người nhận',
            'INVOICE EMAIL',
            'Khu vực',
            'Ghi chú',
        ];
    }

    public function map($customer): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $customer->created_date ? $customer->created_date->format('d/m/Y') : '',
            $customer->ma_kh,
            $customer->ten_cty,
            $customer->ma_so_thue,
            $customer->dia_chi,
            $customer->nguoi_lien_he,
            $customer->sdt,
            $customer->dia_chi_nhan,
            $customer->sdt_nhan,
            $customer->email,
            $customer->khu_vuc,
            $customer->ghi_chu,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => 'center'],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0'],
                ],
            ],
        ];
    }
}
