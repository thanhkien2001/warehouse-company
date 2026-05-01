<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\ProductCatalog;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;

class ProductCatalogImport implements ToCollection, WithHeadingRow, SkipsEmptyRows, WithChunkReading
{
    protected $categoryMap = [];

    public function __construct()
    {
        // Pre-load categories for faster lookup
        $this->categoryMap = Category::pluck('id', 'name')->toArray();
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $maHang  = trim($row['ma_hang'] ?? '');
            $tenHang = trim($row['ten_hang'] ?? '');

            // Skip rows without required fields
            if (empty($maHang) || empty($tenHang)) {
                continue;
            }

            $nhomHang   = trim($row['nhom_hang'] ?? '');
            $categoryId = $this->categoryMap[$nhomHang] ?? null;

            // Use firstOrNew then fill + save to avoid batch-insert id conflicts
            $product = ProductCatalog::firstOrNew(['ma_hang' => $maHang]);

            $product->ten_hang    = $tenHang;
            $product->category_id = $categoryId;
            $product->quy_cach    = $row['quy_cach'] ?? null;
            $product->don_vi_tinh = $row['dvt'] ?? null;
            $product->gia_ban     = $this->parseNumber($row['gia_ban_vnd'] ?? 0);
            $product->vat         = $row['vat'] ?? 10;
            $product->nha_cung_cap = $row['nha_cung_cap'] ?? null;
            $product->ma_ncc      = $row['ma_ncc'] ?? null;
            $product->trang_thai  = in_array($row['trang_thai'] ?? '', ['Hoạt động', 'Ngừng hoạt động'])
                                    ? $row['trang_thai'] : 'Hoạt động';
            $product->ghi_chu     = $row['ghi_chu'] ?? null;

            $product->save();
        }
    }

    protected function parseNumber($value): float
    {
        // Remove thousand separators (. and ,) and parse
        $clean = str_replace(['.', ','], ['', '.'], trim($value ?? ''));
        return (float) $clean;
    }

    public function chunkSize(): int
    {
        return 200;
    }
}
