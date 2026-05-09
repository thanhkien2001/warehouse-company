@extends('layouts.app')
@section('title', 'Quản Lý Công Nợ')
@section('page-title', 'Quản Lý Công Nợ')
@section('page-subtitle', 'Theo dõi các khoản chưa thanh toán và thu hồi nợ quá hạn.')

@section('content')
<style>
            /* Filter row 2 */
        .ord-filter-card { padding: 14px; border-bottom: 1.5px solid #f1f5f9; margin-bottom: 15px; border: 1px solid #e2e8f0;}
        .ord-filter-grid { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; }
        .ord-filter-item { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 120px; }
        .ord-filter-item label { font-size: 13px; font-weight: 700; color: #1e293b; }
        .ord-filter-input { height: 36px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0 12px; font-size: 13px; outline: none; width: 100%; box-sizing: border-box; color: #1e293b; background: #fff; }
        .ord-filter-input:focus { border-color: #0070D2; box-shadow: 0 0 0 3px rgba(0,112,210,0.1); }
        .ord-search-wrapper { position: relative; }
        .ord-search-wrapper i { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; pointer-events: none; }
        .ord-search-wrapper .ord-filter-input { padding-right: 36px; }
        .ord-btn-search { height: 36px; padding: 0 16px; background: #0070D2; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px; white-space: nowrap; }
        .ord-btn-search:hover { background: #005bb5; }
        .ord-btn-clear { height: 36px; padding: 0 16px; background: #fff; color: #ef4444; border: 1px solid #e2e8f0; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px; text-decoration: none; white-space: nowrap; }
        .ord-btn-clear:hover { background: #fef2f2; border-color: #ef4444; }
        #debt-table th{
            background-color:#fef2f2 !important;
            color: #dc2626 !important;
        }
</style>
<div class="card" style="padding: 24px;">
    
    <div class="page-header-row" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 16px; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px;">
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

    <!-- <div id="tonkho-tabs" style="display: flex; gap: 20px; border-bottom: 1px solid #cbd5e1; margin-bottom: 25px;">
        <a href="{{ route('debt.index') }}" class="prem-tab active">Quản Lý Công Nợ</a>
        <a href="{{ route('payments.index') }}" class="prem-tab">Lịch Sử Thanh Toán</a>
    </div> -->
    <div class="ord-filter-card">
        <form method="GET" id="debt-filter-form">
            <input type="hidden" name="status" value="{{ request('status', 'all') }}">
            <div class="ord-filter-grid">
                <div class="ord-filter-item">
                    <label>Từ ngày</label>
                    <input type="date" name="date_start" class="ord-filter-input" value="{{ request('date_start') }}">
                </div>
                <div class="ord-filter-item">
                    <label>Đến ngày</label>
                    <input type="date" name="date_end" class="ord-filter-input" value="{{ request('date_end') }}">
                </div>

                <div class="ord-filter-item" style="flex: 2;">
                    <label>Khách hàng</label>

                    <select name="customer" id="filter-customer" class="ord-filter-input" style="margin-top:6px;">
                        <option value="">Tất cả khách hàng</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ request('customer') == $c->id ? 'selected' : '' }}>
                                [{{ $c->ma_kh }}] {{ $c->ten_cty }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="ord-filter-item" style="flex: 2;">
                    <label>Tìm kiếm</label>
                    <div class="ord-search-wrapper">
                        <input type="text" name="search" class="ord-filter-input" placeholder="Tìm mã đơn (CTO), khách hàng..." value="{{ request('search') }}">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="ord-filter-item">
                    <label>Trạng thái</label>
                    <select name="sort" class="ord-filter-input">
                        <option value="all" {{ request('sort','all')=='all'?'selected':'' }}>Tất cả</option>
                        <option value="con-han" {{ request('sort')=='con-han'?'selected':'' }}>Còn hạn</option>
                        <option value="sap-het-han" {{ request('sort')=='sap-het-han'?'selected':'' }}>Sắp hết hạn</option>
                        <option value="qua-han" {{ request('sort')=='qua-han'?'selected':'' }}>Quá hạn</option>
                    </select>
                </div>
                <div class="ord-filter-item" style="flex: none; display:flex; align-items:center; gap:8px;">
                    <label>&nbsp;</label>
                    <div style="display: flex; gap: 8px;">
                        <button type="submit" class="ord-btn-search"><i class="fas fa-search"></i> Lọc</button>
                        <a href="{{ route('debt.index') }}" class="ord-btn-clear"><i class="fas fa-times"></i> Xóa lọc</a>
                    </div>
                </div>
            </div>
        </form>
        <!-- EXPORT: single button, floated right -->
        <div class="export-wrapper" style="margin-top: 15px; display: flex; justify-content: flex-end;">
            <button type="button" id="export-btn" class="debt-btn-export" title="Xuất dữ liệu" onclick="exportDebtExcel()">
                <i class="fas fa-file-export"></i> Xuất Excel
            </button>
        </div>
    </div>

    <!-- QUÁ HẠN -->
    <h3 style="color: #ef4444; font-weight: 800; font-size: 15px; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-exclamation-circle"></i> CÔNG NỢ QUÁ HẠN
    </h3>
    <div class="legacy-table-container" style="border-left: none; border-right: none; box-shadow: none; margin-bottom: 35px;">
        <table class="legacy-table" id="debt-table">
            <thead>
                <tr>
                    <th style="width: 3%; text-align: center;border-bottom-color: #fca5a5;"><input type="checkbox" id="check-all-overdue" onclick="toggleCheckAll(this, '.check-overdue')"></th>
                    <th style="width: 10%; text-align: center; color: #991b1b; border-bottom-color: #fca5a5;">MÃ CTO</th>
                    <th style="width: 10%; text-align: center; color: #991b1b; border-bottom-color: #fca5a5;">HÓA ĐƠN</th>
                    <th style="width: 20%; text-align: center; color: #991b1b; border-bottom-color: #fca5a5;">KHÁCH HÀNG</th>
                    <th style="width: 11%; text-align: center; color: #991b1b; border-bottom-color: #fca5a5;">TỔNG ĐƠN</th>
                    <th style="width: 11%; text-align: center; color: #991b1b; border-bottom-color: #fca5a5;">ĐÃ TRẢ</th>
                    <th style="width: 11%; text-align: center; color: #991b1b; border-bottom-color: #fca5a5;">CÒN LẠI</th>
                    <th style="width: 10%; text-align: center; color: #991b1b; border-bottom-color: #fca5a5;">NGÀY GIAO</th>
                    <th style="width: 7%; text-align: center; color: #991b1b; border-bottom-color: #fca5a5;">HẠN (NGÀY)</th>
                    <th style="width: 12%; text-align: center; color: #991b1b; border-bottom-color: #fca5a5;">TÌNH TRẠNG</th>
                    <th style="width: 8%; text-align: center; color: #991b1b; border-bottom-color: #fca5a5;">THAO TÁC</th>
                </tr>
            </thead>
            <tbody id="overdue-tbody">
                @forelse($overdueDebts as $od)
                <tr style="background: #fffcfc;">
                    <td style="text-align: center;"><input type="checkbox" class="check-overdue" value="{{ $od['cto_code'] }}"></td>
                    <td style="text-align: center; font-weight: 800; color: #dc2626; border-bottom-color: #fecaca;">{{ $od['cto_code'] }}</td>
                    <td></td>
                    <td style="font-weight: 700;text-align: left; color: #0f172a; text-transform: uppercase; border-bottom-color: #fecaca;">{{ $od['ten_kh'] }}</td>
                    <td style="text-align: right; border-bottom-color: #fecaca;">{{ number_format($od['tong_don']) }}</td>
                    <td style="text-align: right; color: #059669; border-bottom-color: #fecaca;">{{ number_format($od['tong_don'] - $od['con_lai']) }}</td>
                    <td style="text-align: right; color: #dc2626; font-weight: 800; border-bottom-color: #fecaca;">{{ number_format($od['con_lai']) }}</td>
                    <td style="text-align: center; border-bottom-color: #fecaca;">{{ $od['ngay_giao'] ?? '---' }}</td>
                    <td style="text-align: center; border-bottom-color: #fecaca;"><span style="color:#64748b; font-size:12px;">{{ $od['so_ngay_han'] ?? '0' }} ngày</span></td>
                    <td style="text-align: center; border-bottom-color: #fecaca;"><span style="background:#fee2e2; color:#dc2626; padding:4px 10px; border-radius:6px; font-size:11px; font-weight:700; white-space: nowrap; display: inline-block;">{{ $od['tinh_trang'] }}</span></td>
                    <td style="text-align: center; border-bottom-color: #fecaca;">
                        <button onclick="openDebtModal({{ json_encode($od['cto_code']) }}, {{ json_encode($od['ten_kh']) }}, {{ $od['con_lai'] }})" class="ui-btn ui-btn-primary" style="padding: 6px 12px; font-size: 12px;color:white !important; background: #0070D2 !important;"><i class="fas fa-wallet btn-icon"></i> Thu tiền</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" style="padding: 30px; text-align: center; color: #94a3b8; font-style: italic;">Không có nợ quá hạn.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- TẤT CẢ -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
        <h3 style="color: #1e293b; font-weight: 800; font-size: 15px; margin: 0; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-list-ul" style="color: #3b82f6;"></i> TẤT CẢ CÔNG NỢ ĐANG CHỜ THU
        </h3>
    </div>
    <div class="legacy-table-container" style="border-left: none; border-right: none; box-shadow: none;">
        <table class="legacy-table">
            <thead>
                <tr>
                    <th style="width: 3%; text-align: center;"><input type="checkbox" id="check-all-debt" onclick="toggleCheckAll(this, '.check-debt')"></th>
                    <th style="width: 10%; text-align: center;">MÃ ĐƠN</th>
                    <th style="width: 5%; text-align: center;">HÓA ĐƠN</th>
                    <th style="width: 18%; text-align: center;">KHÁCH HÀNG</th>
                    <th style="width: 11%; text-align: center;">TỔNG ĐƠN</th>
                    <th style="width: 11%; text-align: center;">ĐÃ TRẢ</th>
                    <th style="width: 11%; text-align: center;">CÒN LẠI</th>
                    <th style="width: 9%; text-align: center;">NGÀY GIAO</th>
                    <th style="width: 7%; text-align: center;">HẠN (NGÀY)</th>
                    <th style="width: 7%; text-align: center;">TÌNH TRẠNG</th>
                    <th style="width: 11%; text-align: center;">THAO TÁC</th>
                </tr>
            </thead>
            <tbody id="all-debt-tbody">
                @forelse($allDebts as $d)
                <tr>
                    <td style="text-align: center;"><input type="checkbox" class="check-debt" value="{{ $d['cto_code'] }}"></td>
                    <td style="text-align: center; font-weight: 700; color: #0070D2;">{{ $d['cto_code'] }}</td>
                    <td></td>
                    <td style="font-weight: 700; text-align: left; color: #0f172a; text-transform: uppercase;">{{ $d['ten_kh'] }}</td>
                    <td style="text-align: right; color: black; font-weight:bold">{{ number_format($d['tong_don']) }}</td>
                    <td style="text-align: right; color: #059669; font-weight: 800;">{{ number_format($d['tong_don'] - $d['con_lai']) }}</td>
                    <td style="text-align: right; color: #ef4444; font-weight: 800;">{{ number_format($d['con_lai']) }}</td>
                    <td style="text-align: center;">{{ $d['ngay_giao'] ?? '---' }}</td>
                    <td style="text-align: center;"><span style="color:#64748b; font-size:12px;">{{ $d['so_ngay_han'] ?? '0' }} ngày</span></td>
                    <td style="text-align: center;">
                        @php
                            $statusColor = '#64748b';
                            $statusBg = '#f1f5f9';
                            if (str_contains($d['tinh_trang'], 'Quá hạn')) { $statusColor = '#dc2626'; $statusBg = '#fee2e2'; }
                            elseif (str_contains($d['tinh_trang'], 'Đến hạn')) { $statusColor = '#d97706'; $statusBg = '#fef3c7'; }
                            elseif (str_contains($d['tinh_trang'], 'Còn')) { $statusColor = '#059669'; $statusBg = '#d1fae5'; }
                        @endphp
                        <span style="background:{{ $statusBg }}; color:{{ $statusColor }}; padding:4px 10px; border-radius:6px; font-size:11px; font-weight:700; white-space: nowrap; display: inline-block;">{{ $d['tinh_trang'] }}</span>
                    </td>
                    <td style="text-align: center;">
                        <button onclick="openDebtModal({{ json_encode($d['cto_code']) }}, {{ json_encode($d['ten_kh']) }}, {{ $d['con_lai'] }})" class="ui-btn ui-btn-primary" style="padding: 6px 12px; font-size: 12px;color:white !important; background: #0070D2 !important;">Thu tiền</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" style="padding: 30px; text-align: center; color: #94a3b8;">Không có công nợ chờ xử lý.</td></tr>
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
    .prem-tab.active { color: #0070D2; border-bottom-color: #0070D2; }

    /* Responsive adjustments */
    .filter-row { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    
    @media (max-width: 1420px) {
        .legacy-table-container { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .legacy-table { min-width: 1200px; }
    }

    @media (max-width: 768px) {
        .card { padding: 15px !important; }
        .page-header-row { flex-direction: column; align-items: flex-start !important; gap: 15px; }
    }
    @media (max-width: 1919px) {
        .btn-icon { display: none; }
    }
    .debt-btn-export, .debt-btn-export-selected {
        padding: 7px 14px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: all 0.2s;
        color: #475569;
        background: #f5f7fa;
    }
    .debt-btn-export-selected {
        background: #ecfdf5;
        color: #059669;
    }
    .debt-btn-export i, .debt-btn-export-selected i { font-size: 13px; }
    .debt-btn-export:hover, .debt-btn-export-selected:hover { background: #f8fafc; border-color: #cbd5e1; }
</style>
@endsection

@push('scripts')
<script>
    function toggleCheckAll(source, selector) {
        const checkboxes = document.querySelectorAll(selector);
        checkboxes.forEach(cb => cb.checked = source.checked);
    }

    async function exportDebtExcel() {
        // nếu có checkbox được chọn => xuất selected, ngược lại xuất theo bộ lọc
        const checkedAll = document.querySelectorAll('.check-overdue:checked, .check-debt:checked');
        const ctoList = Array.from(checkedAll).map(cb => cb.value);
        const mode = ctoList.length > 0 ? 'selected' : 'all';

        if (mode === 'selected' && ctoList.length === 0) {
            return alert('Vui lòng chọn ít nhất một bản ghi!');
        }

        const params = new URLSearchParams();
        params.append('date_start', document.querySelector('[name=date_start]').value || '');
        params.append('date_end', document.querySelector('[name=date_end]').value || '');
        params.append('customer', document.querySelector('[name=customer]').value || '');
        params.append('search', document.querySelector('[name=search]').value || '');
        params.append('sort', document.querySelector('[name=sort]').value || '');
        params.append('export_mode', mode);

        if (ctoList.length > 0) {
            params.append('cto_codes', ctoList.join(','));
        }

        // navigate to export route
        window.location.href = '{{ route("debt.export") }}?' + params.toString();
    }

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
