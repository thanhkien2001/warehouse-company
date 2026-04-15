@extends('layouts.app')
@section('title', 'Quản Lý Công Nợ')
@section('page-title', 'Quản Lý Công Nợ')
@section('page-subtitle', 'Theo dõi các khoản chưa thanh toán và thu hồi nợ quá hạn.')

@section('content')
<div class="card" style="padding: 24px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 16px; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 54px; height: 54px; background: #fef2f2; border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.15); flex-shrink: 0;">
                <i class="fas fa-exclamation-triangle" style="font-size: 24px; color: #ef4444;"></i>
            </div>
            <div>
                <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0;">Công Nợ Khách Hàng</h2>
                <p style="margin: 0; color: #64748b; font-size: 13.5px;">Quản lý và nhắc nợ các đơn hàng chưa hoàn tất thanh toán.</p>
            </div>
        </div>
        <button onclick="location.reload()" class="ui-btn ui-btn-outline"><i class="fas fa-sync-alt"></i> Làm mới</button>
    </div>

    <div id="tonkho-tabs" style="display: flex; gap: 20px; border-bottom: 1px solid #cbd5e1; margin-bottom: 25px;">
        <a href="{{ route('debt.index') }}" class="prem-tab active">Quản Lý Công Nợ</a>
        <a href="{{ route('payments.index') }}" class="prem-tab">Lịch Sử Thanh Toán</a>
    </div>

    <!-- QUÁ HẠN -->
    <h3 style="color: #ef4444; font-weight: 800; font-size: 16px; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-exclamation-circle"></i> CÁC KHOẢN NỢ QUÁ HẠN CHƯA THU
    </h3>
    <div class="legacy-table-container" style="border-left: none; border-right: none; box-shadow: none; margin-bottom: 35px;">
        <table class="legacy-table">
            <thead>
                <tr>
                    <th style="text-align: center; color: #991b1b; border-bottom-color: #fca5a5;">MÃ ĐƠN</th>
                    <th style="text-align: left; color: #991b1b; border-bottom-color: #fca5a5;">KHÁCH HÀNG</th>
                    <th style="text-align: right; color: #991b1b; border-bottom-color: #fca5a5;">TỔNG ĐƠN</th>
                    <th style="text-align: right; color: #991b1b; border-bottom-color: #fca5a5;">ĐÃ TRẢ</th>
                    <th style="text-align: right; color: #991b1b; border-bottom-color: #fca5a5;">CÒN LẠI</th>
                    <th style="text-align: center; color: #991b1b; border-bottom-color: #fca5a5;">NGÀY GIAO</th>
                    <th style="text-align: center; color: #991b1b; border-bottom-color: #fca5a5;">TÌNH TRẠNG</th>
                    <th style="text-align: center; color: #991b1b; border-bottom-color: #fca5a5;">THAO TÁC</th>
                </tr>
            </thead>
            <tbody>
                @forelse($overdueDebts as $od)
                <tr style="background: #fffcfc;">
                    <td style="text-align: center; font-weight: 800; color: #dc2626; border-bottom-color: #fecaca;">{{ $od['cto_code'] }}</td>
                    <td style="font-weight: 700; color: #0f172a; text-transform: uppercase; border-bottom-color: #fecaca;">{{ $od['ten_kh'] }}</td>
                    <td style="text-align: right; border-bottom-color: #fecaca;">{{ number_format($od['tong_don']) }}</td>
                    <td style="text-align: right; color: #059669; border-bottom-color: #fecaca;">{{ number_format($od['tong_don'] - $od['con_lai']) }}</td>
                    <td style="text-align: right; color: #dc2626; font-weight: 800; border-bottom-color: #fecaca;">{{ number_format($od['con_lai']) }}</td>
                    <td style="text-align: center; border-bottom-color: #fecaca;">{{ $od['ngay_giao'] ?? '---' }}</td>
                    <td style="text-align: center; border-bottom-color: #fecaca;"><span style="background:#fee2e2; color:#dc2626; padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700;">{{ $od['tinh_trang'] }}</span></td>
                    <td style="text-align: center; border-bottom-color: #fecaca;">
                        <button onclick="openDebtModal('{{ $od['cto_code'] }}', '{{ $od['ten_kh'] }}', {{ $od['con_lai'] }})" class="ui-btn ui-btn-primary" style="padding: 6px 12px; font-size: 12px; background: #ef4444;"><i class="fas fa-wallet"></i> Thu tiền</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="padding: 30px; text-align: center; color: #94a3b8; font-style: italic;">Không có nợ quá hạn.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- TẤT CẢ -->
    <h3 style="color: #1e293b; font-weight: 800; font-size: 16px; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-list-ul" style="color: #3b82f6;"></i> TẤT CẢ CÔNG NỢ ĐANG CHỜ THU
    </h3>
    <div class="legacy-table-container" style="border-left: none; border-right: none; box-shadow: none;">
        <table class="legacy-table">
            <thead>
                <tr>
                    <th style="text-align: center;">MÃ ĐƠN</th>
                    <th style="text-align: left;">KHÁCH HÀNG</th>
                    <th style="text-align: right;">TỔNG ĐƠN</th>
                    <th style="text-align: right;">ĐÃ TRẢ</th>
                    <th style="text-align: right;">CÒN LẠI</th>
                    <th style="text-align: center;">NGÀY GIAO</th>
                    <th style="text-align: center;">HẠN (NGÀY)</th>
                    <th style="text-align: center;">THAO TÁC</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allDebts as $d)
                <tr>
                    <td style="text-align: center; font-weight: 700; color: #10568f;">{{ $d['cto_code'] }}</td>
                    <td style="font-weight: 700; color: #0f172a; text-transform: uppercase;">{{ $d['ten_kh'] }}</td>
                    <td style="text-align: right; color: #475569;">{{ number_format($d['tong_don']) }}</td>
                    <td style="text-align: right; color: #059669; font-weight: 800;">{{ number_format($d['tong_don'] - $d['con_lai']) }}</td>
                    <td style="text-align: right; color: #ef4444; font-weight: 800;">{{ number_format($d['con_lai']) }}</td>
                    <td style="text-align: center;">{{ $d['ngay_giao'] ?? '---' }}</td>
                    <td style="text-align: center;"><span style="color:#64748b; font-size:12px;">{{ $d['deadline'] ?? '---' }}</span></td>
                    <td style="text-align: center;">
                        <button onclick="openDebtModal('{{ $d['cto_code'] }}', '{{ $d['ten_kh'] }}', {{ $d['con_lai'] }})" class="ui-btn ui-btn-primary" style="padding: 6px 12px; font-size: 12px; background: #059669;"><i class="fas fa-hand-holding-usd"></i> Thu tiền</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="padding: 30px; text-align: center; color: #94a3b8;">Không có công nợ chờ xử lý.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 25px;">
        <div style="color: #64748b; font-size: 14px;">Đang hiển thị {{ $allDebts->firstItem() ?? 0 }} - {{ $allDebts->lastItem() ?? 0 }} trong tổng số {{ $allDebts->total() }} bản ghi</div>
        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 8px; color: #64748b; font-size: 13px;">
                <span>Hiển thị:</span>
                <select onchange="window.location.href=this.value" style="border: none; outline: none; background: transparent; font-weight: 700; cursor: pointer; color: #0f172a; font-size: 14px;">
                    @foreach([5, 10, 15, 20, 50] as $size)
                        <option value="{{ request()->fullUrlWithQuery(['limit' => $size]) }}" {{ request('limit', 10) == $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
                <span>mục</span>
            </div>
            <div>{{ $allDebts->links('pagination::bootstrap-4') }}</div>
        </div>
    </div>
</div>

{{-- MODAL NHẬP THANH TOÁN --}}
<div id="modal-pay" class="modal-overlay">
    <div class="modal-box" style="max-width: 450px; padding: 0;">
        <div style="background: #f8fafc; padding: 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; border-radius: 16px 16px 0 0;">
            <h3 style="margin: 0; color: #0f172a; font-weight: 800; font-size: 18px;"><i class="fas fa-hand-holding-usd" style="color: #10b981;"></i> Nhập Thanh Toán</h3>
            <i class="fas fa-times" style="cursor: pointer; color: #94a3b8; font-size: 20px;" onclick="closeModal('modal-pay')"></i>
        </div>
        <div style="padding: 24px;">
            <input type="hidden" id="p_cto">
            <div style="background: #eff6ff; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px dashed #bfdbfe;">
                <p style="margin: 0 0 5px 0; color: #3b82f6; font-size: 13px;">Khách hàng: <b id="p_view_kh" style="color: #1e3a8a;"></b></p>
                <p style="margin: 0 0 5px 0; color: #3b82f6; font-size: 13px;">Mã đơn: <b id="p_view_cto" style="color: #1e3a8a;"></b></p>
                <p style="margin: 0; color: #ef4444; font-size: 14px; font-weight: bold;">Số tiền còn thiếu: <span id="p_view_debt"></span> đ</p>
            </div>
            
            <label style="display: block; font-weight: bold; margin-bottom: 8px; font-size: 14px; color: #0f172a;">Số tiền khách trả đợt này (VNĐ) *</label>
            <input type="text" id="p_amount" style="width: 100%; box-sizing: border-box; padding: 14px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; font-size: 18px; font-weight: bold; color: #10b981; margin-bottom: 20px;" oninput="this.value = this.value.replace(/[^0-9]/g,'').replace(/\B(?=(\d{3})+(?!\d))/g, '.')">
            
            <button onclick="submitPay()" style="width: 100%; padding: 14px; border-radius: 8px; border: none; background: #10b981; color: #fff; font-weight: 800; font-size: 15px; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 10px rgba(16,185,129,0.2);"><i class="fas fa-check-circle"></i> Xác nhận Đã Thu Tiền</button>
        </div>
    </div>
</div>

<style>
    .prem-tab { padding: 12px 15px; color: #64748b; font-weight: 600; font-size: 14px; text-decoration: none; border-bottom: 3px solid transparent; transition: 0.3s; }
    .prem-tab.active { color: #10568f; border-bottom-color: #10568f; }
</style>
@endsection

@push('scripts')
<script>
    function openDebtModal(cto, kh, debt) {
        document.getElementById('p_cto').value = cto;
        document.getElementById('p_view_kh').innerText = kh;
        document.getElementById('p_view_cto').innerText = cto;
        document.getElementById('p_view_debt').innerText = debt.toLocaleString('vi-VN');
        document.getElementById('p_amount').value = debt.toLocaleString('vi-VN');
        openModal('modal-pay');
    }

    async function submitPay() {
        const amount = document.getElementById('p_amount').value.replace(/\./g, '');
        const cto = document.getElementById('p_cto').value;
        
        if (!amount || amount <= 0) return alert('Vui lòng nhập số tiền hợp lệ!');

        const res = await apiPost('{{ route("payments.store") }}', {
            cto_code: cto,
            so_tien: amount,
            ghi_chu: 'Thanh toán từ màn hình công nợ'
        });
        
        if (res.success) {
            showToast(res.message);
            location.reload();
        } else {
            alert(res.message);
        }
    }
</script>
@endpush
