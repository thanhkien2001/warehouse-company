@extends('layouts.app')
@section('title', 'Chi tiết Phiếu: ' . $delivery->dn_code)
@section('page-title', 'Chi Tiết Phiếu Giao Hàng')
@section('page-subtitle', 'Xem thông tin xuất kho và cập nhật tiến độ giao hàng.')

@section('content')
<div class="card" style="padding: 0; overflow: hidden; position: relative;">
    <div style="position: absolute; top: 0; right: 0; background: #0070D2 !important; color: #fff; padding: 6px 20px; font-weight: 800; font-size: 13px; letter-spacing: 1px; border-bottom-left-radius: 12px; box-shadow: -2px 2px 10px rgba(0,0,0,0.1); z-index: 10;">
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
            {{-- BLOCK LEFT: THÔNG TIN GIAO HÀNG --}}
            <div style="flex: 1; min-width: 350px; font-size: 14px; color: #0f172a; line-height: 2.2;">
                <h3 style="margin: 0 0 10px 0; font-size: 14px; font-weight: 800; text-transform: uppercase; border-bottom: 1px dashed #cbd5e1; padding-bottom: 5px;">
                    <i class="fas fa-map-marker-alt" style="margin-right: 5px;"></i> THÔNG TIN GIAO HÀNG
                </h3>
                <div style="display: flex;"><span style="color: #64748b; width: 130px;">Khách hàng:</span> <b style="flex: 1;">{{ $delivery->ten_kh }}</b></div>
                <div style="display: flex;"><span style="color: #64748b; width: 130px;">Mã khách hàng:</span> <span style="flex: 1; font-weight: 600;">{{ $delivery->ma_kh }}</span></div>
                <div style="display: flex;"><span style="color: #64748b; width: 130px;">Địa chỉ giao:</span> <span style="flex: 1;">{{ $delivery->customer->dia_chi_nhan ?: $delivery->customer->dia_chi }}</span></div>
                <div style="display: flex;"><span style="color: #64748b; width: 130px;">Người nhận:</span> <span style="flex: 1;">{{ $delivery->order->nguoi_mua ?? '---' }}</span></div>
                <div style="display: flex;"><span style="color: #64748b; width: 130px;">SĐT Người nhận:</span> <span style="flex: 1;">{{ $delivery->order->sdt_mua ?? '---' }}</span></div>
            </div>

            {{-- BLOCK RIGHT: CHỨNG TỪ THAM CHIẾU --}}
            <div style="flex: 1; min-width: 300px; font-size: 14px; color: #0f172a; line-height: 2.2;">
                <h3 style="margin: 0 0 10px 0; font-size: 14px; font-weight: 800; text-transform: uppercase; border-bottom: 1px dashed #cbd5e1; padding-bottom: 5px;">
                    <i class="fas fa-receipt" style="margin-right: 5px;"></i> CHỨNG TỪ & LIÊN HỆ
                </h3>
                <div style="display: flex;"><span style="color: #64748b; width: 130px;">Mã Phiếu Giao:</span> <b style="flex: 1; color: #10b981; font-size: 16px;">{{ $delivery->dn_code }}</b></div>
                <div style="display: flex;"><span style="color: #64748b; width: 130px;">Đơn tham chiếu:</span> <b style="flex: 1; color: #3b82f6;">{{ $delivery->cto_code }}</b></div>
                <div style="display: flex;"><span style="color: #64748b; width: 130px;">Ngày xuất / Hạn:</span> <span style="flex: 1;">{{ $delivery->delivery_date->format('d/m/Y') }} / <b style="color:#ef4444">{{ $delivery->han_thanh_toan }} ngày</b></span></div>
                <div style="display: flex;"><span style="color: #64748b; width: 130px;">Người bán:</span> <span style="flex: 1;">{{ $delivery->order->nguoi_ban ?? '---' }}</span></div>
                <div style="display: flex;"><span style="color: #64748b; width: 130px;">SĐT Người bán:</span> <span style="flex: 1;">{{ $delivery->order->sdt_ban ?? '---' }}</span></div>
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
                    <button class="ui-btn ui-btn-pdf" onclick="exportDNToPDF()"><i class="fas fa-file-pdf"></i> Xuất PDF</button>
                    <button class="ui-btn ui-btn-save" onclick="updateDNStatus()"><i class="fas fa-save"></i> Lưu</button>
                </div>
            </div>

            <div class="legacy-table-container">
                <table class="legacy-table">
                    <thead>
                        <tr style="background:#f1f5f9">
                            <th style="width:3%; text-align:center">STT</th>
                            <th style="width:8%; text-align:center">MÃ HÀNG</th>
                            <th style="width:20%; text-align:center">MÔ TẢ HÀNG HÓA</th>
                            <th style="width:8%; text-align:center">SỐ LÔ</th>
                            <th style="width:8%; text-align:center">HSD</th>
                            <th style="width:8%; text-align:center">QUY CÁCH</th>
                            <th style="width:8%; text-align:center">SỐ LƯỢNG</th>
                            <th style="width:6%; text-align:center">ĐVT</th>
                            <th style="width:8%; text-align:center">QUY ĐỔI</th>
                            <th style="width:23%; text-align:center">GHI CHÚ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($delivery->order->items ?? [] as $i => $item)
                        <tr class="dn-item-row" data-id="{{ $item->id }}" style="transition: 0.2s;" onmouseover="this.style.background='#fcfcfc'" onmouseout="this.style.background='transparent'">
                            <td style="text-align:center; color: #94a3b8; font-weight: 600;">{{ $i + 1 }}</td>
                            <td style="text-align:center; font-weight:700; color: #0070D2;">{{ $item->ma_hang }}</td>
                            <td style="text-align:left">
                                <div style="font-weight:700; color: #0f172a; margin-bottom: 2px;">{{ $item->ten_hang }}</div>
                                @if($item->mo_ta_phu)<div style="font-size:11px; color:#64748b; font-style: italic;">{{ $item->mo_ta_phu }}</div>@endif
                            </td>
                            <td style="text-align:center; padding: 4px;">
                                <input type="text" class="in-lot" value="{{ $item->ma_lot }}" placeholder="Số lô..." style="width:100%; border:1px solid #e2e8f0; border-radius:6px; padding:6px; text-align:center; font-size:13px; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='#0070D2'; this.style.boxShadow='0 0 0 3px rgba(0,112,210,0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                            </td>
                            <td style="text-align:center; padding: 4px;">
                                <input type="date" class="in-hsd" value="{{ $item->han_su_dung ? $item->han_su_dung->format('Y-m-d') : '' }}" style="width:100%; border:1px solid #e2e8f0; border-radius:6px; padding:5px; font-size:12.5px; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='#0070D2'; this.style.boxShadow='0 0 0 3px rgba(0,112,210,0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                            </td>
                            <td style="text-align:center; padding: 4px;">
                                <input type="text" class="in-qc" value="{{ $item->quy_cach }}" placeholder="Quy cách..." style="width:100%; border:1px solid #e2e8f0; border-radius:6px; padding:6px; text-align:center; font-size:13px; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='#0070D2'; this.style.boxShadow='0 0 0 3px rgba(0,112,210,0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                            </td>
                            <td style="text-align:right; font-weight:800; color:#0f172a; padding-right: 15px;">{{ number_format($item->so_luong, 2, ',', '.') }}</td>
                            <td style="text-align:center; color: #64748b; font-weight: 600;">{{ $item->don_vi_tinh }}</td>
                            <td style="text-align:center; padding: 4px;">
                                <input type="number" step="any" class="in-qdoi" value="{{ $item->quy_doi }}" placeholder="0" style="width:100%; border:1px solid #e2e8f0; border-radius:6px; padding:6px; text-align:center; font-size:13px; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='#0070D2'; this.style.boxShadow='0 0 0 3px rgba(0,112,210,0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                            </td>
                            <td style="text-align:left; padding: 4px;">
                                <input type="text" class="in-note" value="{{ $item->ghi_chu }}" placeholder="Ghi chú..." style="width:100%; border:1px solid #e2e8f0; border-radius:6px; padding:6px; font-size:13px; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='#0070D2'; this.style.boxShadow='0 0 0 3px rgba(0,112,210,0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="10" style="padding:40px; text-align:center; color:#94a3b8; font-style: italic;">Không có dữ liệu hàng hóa.</td></tr>
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
        // Thu thập dữ liệu items
        const rows = document.querySelectorAll('.dn-item-row');
        const items = [];
        rows.forEach(row => {
            items.push({
                id: row.getAttribute('data-id'),
                ma_lot: row.querySelector('.in-lot').value,
                han_su_dung: row.querySelector('.in-hsd').value,
                quy_cach: row.querySelector('.in-qc').value,
                quy_doi: row.querySelector('.in-qdoi').value,
                ghi_chu: row.querySelector('.in-note').value
            });
        });

        const s = document.getElementById('dn_status').value;
        const data = {
            trang_thai: s,
            items: items
        };

        const btn = document.querySelector('.ui-btn-save');
        const oldHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
        btn.disabled = true;

        try {
            const res = await apiPost('{{ route("deliveries.save-items", $delivery->id) }}', data);
            if (res.success) { 
                showToast(res.message); 
                // location.reload(); // Không cần thiết nếu chỉ muốn trải nghiệm mượt, nhưng tốt để sync data hiển thị
            } else { 
                alert(res.message); 
            }
        } catch (e) {
            alert('Có lỗi xảy ra khi lưu dữ liệu!');
        } finally {
            btn.innerHTML = oldHtml;
            btn.disabled = false;
        }
    }

    function exportDNToPDF() {
        window.open('{{ route("deliveries.pdf", $delivery->id) }}', '_blank');
    }
</script>
@endpush
