<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\ProductCatalog;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductCatalogImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithBatchInserts, WithChunkReading
{
    protected $categoryMap = [];

    public function __construct()
    {
        // Pre-load categories for faster lookup
        $this->categoryMap = Category::pluck('id', 'name')->toArray();
    }

    public function model(array $row): ?ProductCatalog
    {
        $maHang = trim($row['ma_hang'] ?? '');
        $tenHang = trim($row['ten_hang'] ?? '');

        if (empty($maHang) || empty($tenHang)) {
            return null;
        }

        $nhomHang = trim($row['nhom_hang'] ?? '');
        $categoryId = $this->categoryMap[$nhomHang] ?? null;

        return ProductCatalog::updateOrCreate(
            ['ma_hang' => $maHang],
            [
                'ten_hang'     => $tenHang,
                'category_id'  => $categoryId,
                'quy_cach'     => $row['quy_cach'] ?? null,
                'don_vi_tinh'  => $row['dvt'] ?? null,
                'gia_ban'      => $this->parseNumber($row['gia_ban_vnd'] ?? 0),
                'vat'          => $row['vat'] ?? 10,
                'nha_cung_cap' => $row['nha_cung_cap'] ?? null,
                'ma_ncc'       => $row['ma_ncc'] ?? null,
                'trang_thai'   => in_array($row['trang_thai'] ?? '', ['Hoạt động', 'Ngừng hoạt động'])
                                    ? $row['trang_thai'] : 'Hoạt động',
                'ghi_chu'      => $row['ghi_chu'] ?? null,
            ]
        );
    }

    protected function parseNumber($value): float
    {
        // Remove thousand separators (. and ,) and parse
        $clean = str_replace(['.', ','], ['', '.'], trim($value));
        return (float) $clean;
    }

    public function batchSize(): int
    {
        return 200;
    }

    public function chunkSize(): int
    {
        return 200;
    }
}
