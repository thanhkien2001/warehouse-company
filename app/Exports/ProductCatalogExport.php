<?php

namespace App\Exports;

use App\Models\ProductCatalog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ProductCatalogExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Danh sách sản phẩm';
    }

    public function query()
    {
        $query = ProductCatalog::with('category')->orderBy('ma_hang');

        if (!empty($this->filters['search'])) {
            $kw = $this->filters['search'];
            $query->where(function ($q) use ($kw) {
                $q->where('ma_hang', 'like', "%$kw%")
                  ->orWhere('ten_hang', 'like', "%$kw%");
            });
        }
        if (!empty($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }
        if (!empty($this->filters['nha_cung_cap'])) {
            $query->where('nha_cung_cap', 'like', '%' . $this->filters['nha_cung_cap'] . '%');
        }
        if (!empty($this->filters['trang_thai'])) {
            $query->where('trang_thai', $this->filters['trang_thai']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'STT', 'Mã hàng', 'Tên hàng', 'Nhóm hàng', 'Quy cách',
            'ĐVT', 'Giá bán (VNĐ)', 'VAT (%)', 'Nhà cung cấp', 'Mã NCC',
            'Trạng thái', 'Ghi chú',
        ];
    }

    public function map($item): array
    {
        static $index = 0;
        $index++;
        return [
            $index,
            $item->ma_hang,
            $item->ten_hang,
            $item->category?->name ?? '',
            $item->quy_cach,
            $item->don_vi_tinh,
            $item->gia_ban,
            $item->vat,
            $item->nha_cung_cap,
            $item->ma_ncc,
            $item->trang_thai,
            $item->ghi_chu,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0070D2']],
            ],
        ];
    }
}
