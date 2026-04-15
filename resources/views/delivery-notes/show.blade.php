@extends('layouts.app')
@section('title', 'Chi tiết Phiếu: ' . $delivery->dn_code)
@section('page-title', 'Chi Tiết Phiếu Giao Hàng')
@section('page-subtitle', 'Xem thông tin xuất kho và cập nhật tiến độ giao hàng.')

@section('content')
<div class="card" style="padding: 0; overflow: hidden; position: relative;">
    <div style="position: absolute; top: 0; right: 0; background: #8b5cf6; color: #fff; padding: 6px 20px; font-weight: 800; font-size: 13px; letter-spacing: 1px; border-bottom-left-radius: 12px; box-shadow: -2px 2px 10px rgba(0,0,0,0.1); z-index: 10;">
        <i class="fas fa-truck"></i> <span>{{ $delivery->dn_code }}</span>
    </div>

    <div style="padding: 24px;">
        <div style="margin-bottom: 25px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 15px; padding-right: 120px;">
            <a href="{{ route('deliveries.index') }}" class="ui-btn ui-btn-outline" style="padding: 6px 12px; font-size: 13px; margin-bottom: 15px;">
                <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Quay lại danh sách
            </a>
            <h2 style="font-size: 24px; font-weight: 900; color: #0f172a; margin: 0 0 5px 0;">Chi Tiết Phiếu Giao Hàng</h2>
            <p style="margin: 0; color: #64748b; font-size: 14px;">Mã Đơn Tham Chiếu: <b style="color:#3b82f6">{{ $delivery->cto_code }}</b></p>
        </div>

        <div style="background: #f8fafc; border-radius: 12px; padding: 20px; margin-bottom: 25px; border: 1px solid #e2e8f0; display: flex; gap: 40px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 350px; font-size: 14px; color: #0f172a; line-height: 2.2;">
                <h3 style="margin: 0 0 10px 0; font-size: 14px; font-weight: 800; color: #8b5cf6; text-transform: uppercase; border-bottom: 1px dashed #cbd5e1; padding-bottom: 5px;">
                    <i class="fas fa-map-marker-alt" style="margin-right: 5px;"></i> THÔNG TIN GIAO HÀNG
                </h3>
                <div style="display: flex;"><span style="color: #64748b; width: 130px;">Khách hàng:</span> <b style="flex: 1;">{{ $delivery->ten_kh }}</b></div>
                <div style="display: flex;"><span style="color: #64748b; width: 130px;">Mã khách hàng:</span> <span style="flex: 1; font-weight: 600;">{{ $delivery->ma_kh }}</span></div>
                <div style="display: flex;"><span style="color: #64748b; width: 130px;">Đ/c nhận:</span> <span style="flex: 1;">{{ $delivery->customer->dia_chi_nhan ?: $delivery->customer->dia_chi }}</span></div>
                <div style="display: flex;"><span style="color: #64748b; width: 130px;">SĐT nhận:</span> <span style="flex: 1;">{{ $delivery->customer->sdt_nhan ?: $delivery->customer->sdt }}</span></div>
            </div>

            <div style="flex: 1; min-width: 300px; font-size: 14px; color: #0f172a; line-height: 2.2;">
                <h3 style="margin: 0 0 10px 0; font-size: 14px; font-weight: 800; color: #0ea5e9; text-transform: uppercase; border-bottom: 1px dashed #cbd5e1; padding-bottom: 5px;">
                    <i class="fas fa-receipt" style="margin-right: 5px;"></i> CHỨNG TỪ THAM CHIẾU
                </h3>
                <div style="display: flex;"><span style="color: #64748b; width: 130px;">Ngày xuất:</span> <b style="flex:1">{{ $delivery->delivery_date->format('d/m/Y') }}</b></div>
                <div style="display: flex;"><span style="color: #64748b; width: 130px;">Hạn thanh toán:</span> <b style="flex:1; color:#ef4444">{{ $delivery->han_thanh_toan }} ngày</b></div>
                <div style="display: flex;"><span style="color: #64748b; width: 130px;">Người lập:</span> <span style="flex: 1;">{{ $delivery->nguoi_tao }}</span></div>
            </div>
        </div>

        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 15px;">
                <h3 style="margin: 0; font-size: 15px; font-weight: 800; color: #0f172a; text-transform: uppercase;">
                    <i class="fas fa-boxes" style="color: #f59e0b; margin-right: 5px;"></i> DANH SÁCH HÀNG HÓA XUẤT KHO
                </h3>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <span style="font-size: 14px; font-weight: 600; color: #475569;">Trạng thái:</span>
                    <select id="dn_status" style="padding: 8px 15px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; font-weight: 600; color: #0f172a; cursor: pointer; font-size: 14px;">
                        <option value="Chờ giao hàng" {{ $delivery->trang_thai=='Chờ giao hàng'?'selected':'' }}>Chờ giao hàng</option>
                        <option value="Đang giao" {{ $delivery->trang_thai=='Đang giao'?'selected':'' }}>Đang giao</option>
                        <option value="Đã giao xong" {{ $delivery->trang_thai=='Đã giao xong'?'selected':'' }}>Đã giao xong</option>
                        <option value="Đã hủy" {{ $delivery->trang_thai=='Đã hủy'?'selected':'' }}>Đã hủy</option>
                    </select>
                    <button class="ui-btn ui-btn-outline" style="border-color:#10b981; color:#10b981;" onclick="exportDNToPDF()"><i class="fas fa-file-pdf"></i> Xuất PDF</button>
                    <button class="ui-btn ui-btn-primary" style="background:#10b981;" onclick="updateDNStatus()"><i class="fas fa-save"></i> Lưu Trạng Thái</button>
                </div>
            </div>

            <div class="legacy-table-container">
                <table class="legacy-table">
                    <thead>
                        <tr style="background:#f1f5f9">
                            <th style="width:50px; text-align:center">STT</th>
                            <th style="width:120px">Mã Hàng</th>
                            <th>Tên Sản Phẩm</th>
                            <th style="width:100px; text-align:center">ĐVT</th>
                            <th style="width:120px; text-align:right">SL Giao</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($delivery->order->items ?? [] as $i => $item)
                        <tr>
                            <td style="text-align:center">{{ $i + 1 }}</td>
                            <td style="font-weight:700">{{ $item->ma_hang }}</td>
                            <td>
                                <div style="font-weight:600">{{ $item->ten_hang }}</div>
                                @if($item->mo_ta_phu)<div style="font-size:12px; color:#94a3b8; margin-top:2px">{{ $item->mo_ta_phu }}</div>@endif
                            </td>
                            <td style="text-align:center">{{ $item->don_vi_tinh }}</td>
                            <td style="text-align:right; font-weight:800; font-size:15px; color:#8b5cf6;">{{ rtrim(rtrim(number_format($item->so_luong, 3, ',', '.'), '0'), ',') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="padding:20px; text-align:center; color:#94a3b8;">Không có dữ liệu hàng hóa.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function updateDNStatus() {
        const s = document.getElementById('dn_status').value;
        const res = await apiPatch('{{ route("deliveries.status", $delivery->id) }}', { trang_thai: s });
        if (res.success) { showToast(res.message); }
        else { alert(res.message); }
    }

    function exportDNToPDF() {
        window.open('{{ route("deliveries.pdf", $delivery->id) }}', '_blank');
    }
</script>
@endpush
