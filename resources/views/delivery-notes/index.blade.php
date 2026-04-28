@extends('layouts.app')
@section('title', 'Phiếu Giao Hàng')
@section('page-title', 'Phiếu Giao Hàng')
@section('page-subtitle', 'Quản lý, thêm mới, in ấn và theo dõi tiến độ phiếu giao hàng.')

@push('styles')
<style>
    .dn-tabs { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    .dn-tab { padding: 8px 16px; border-radius: 50px; font-size: 13.5px; font-weight: 600; cursor: pointer; text-decoration: none; color: #64748b; background: #f1f5f9; display: flex; align-items: center; gap: 6px; transition: 0.2s; border: 1px solid transparent; }
    .dn-tab:hover { background: #e2e8f0; color: #0f172a; }
    .dn-tab.active { background: #0070D2; color: #fff; box-shadow: 0 4px 10px rgba(0,112,210,0.2); border-color: #0070D2; }
    
    .dn-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 18px; height: 18px; border-radius: 50%; font-size: 10px; color: #fff; font-weight: 800; padding: 0 4px; }
    .dn-badge.cho-giao { background: #F39C12; }
    .dn-badge.dang-giao { background: #8E44AD; }
    .dn-badge.da-giao { background: #27AE60; }
    .dn-badge.da-huy { background: #E74C3C; }
    .dn-badge.default { background: #94a3b8; }

    .legacy-table tbody tr { cursor: pointer; transition: 0.2s; }
    .legacy-table tbody tr:hover { background: #f0f7ff !important; }

    .search-box-container { position: relative; width: 300px; }
    .search-box-container i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 13px; }
    .search-input { width: 100%; padding: 8px 12px 8px 35px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; outline: none; background: #fff; }
    .search-input:focus { border-color: #0070D2; box-shadow: 0 0 0 3px rgba(0,112,210,0.1); }

    .sort-select { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; color: #475569; background: #fff; cursor: pointer; min-width: 150px; outline: none; }

    /* Responsive adjustments */
    .filter-row { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    .filter-group { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    
    @media (max-width: 1400px) {
        .legacy-table-container { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .legacy-table { min-width: 1200px; }
    }

    @media (max-width: 1200px) {
        .filter-row { flex-direction: column; align-items: stretch; }
        .filter-group { justify-content: flex-start; }
        .search-box-container { width: 100%; }
    }

    @media (max-width: 768px) {
        .card { padding: 15px !important; }
        .page-header-row { flex-direction: column; align-items: flex-start !important; gap: 15px; }
        .date-range-group { width: 100%; display: flex; }
        .date-range-group input { flex: 1; min-width: 0; }
    }
</style>
@endpush

@section('content')
<div class="card" style="padding: 24px;">
    
    <div class="page-header-row" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 2.5px solid #cbd5e1; margin-bottom: 25px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: #eff6ff; border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(139, 92, 246, 0.15); flex-shrink: 0;">
                <i class="fas fa-file-export" style="font-size: 24px; color: #0070D2;"></i>
            </div>
            <div>
                <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; letter-spacing: -0.5px;">Phiếu Giao Hàng</h2>
                <p style="margin: 0; color: #64748b; font-size: 13.5px;">Quản lý và xuất kho hàng hóa.</p>
            </div>
        </div>
        @if(auth()->user()->canDo('phieugiao', 'edit') || auth()->user()->isAdmin())
        <button onclick="openCreateDNModal()" style="background: #0070D2; color: white; border: none; padding: 8px 18px; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
            <i class="fas fa-plus"></i> Tạo Phiếu Giao Hàng
        </button>
        @endif
    </div>

    {{-- ROW 1: STATUS TABS & DATE FILTER --}}
    <div class="filter-row" style="margin-bottom: 20px;">
        <div class="dn-tabs">
            <a href="{{ request()->fullUrlWithQuery(['status' => 'all']) }}" class="dn-tab {{ request('status', 'all') == 'all' ? 'active' : '' }}">
                Tất cả <span style="opacity: 0.7; font-weight: normal;">({{ $counts['all'] ?? 0 }})</span>
            </a>
            @php
                $statusMap = [
                    'Chờ giao hàng' => 'cho-giao',
                    'Đang giao'     => 'dang-giao',
                    'Đã giao xong'  => 'da-giao',
                    'Đã hủy'        => 'da-huy'
                ];
            @endphp
            @foreach($statusMap as $st => $cls)
                <a href="{{ request()->fullUrlWithQuery(['status' => $st]) }}" class="dn-tab {{ request('status') == $st ? 'active' : '' }}">
                    {{ $st }} <span class="dn-badge {{ $cls }}">{{ $counts[$st] ?? 0 }}</span>
                </a>
            @endforeach
        </div>

        <form method="GET" class="filter-group">
            <input type="hidden" name="status" value="{{ request('status', 'all') }}">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="sort" value="{{ request('sort', 'newest') }}">
            
            <div class="date-range-group" style="display: flex; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden; background: #fff;">
                <div style="display: flex; align-items: center; padding: 0 10px; border-right: 1px solid #cbd5e1;">
                    <i class="far fa-calendar-alt" style="color: #64748b; font-size: 14px;"></i>
                </div>
                <input type="date" name="date_start" value="{{ request('date_start') }}" style="padding: 8px; border: none; font-size: 13px; color: #475569; outline: none; width: 130px;">
                <div style="display: flex; align-items: center; padding: 0 10px; border-left: 1px solid #cbd5e1; border-right: 1px solid #cbd5e1; background: #f8fafc;">
                    <i class="far fa-calendar-alt" style="color: #64748b; font-size: 13px;"></i>
                </div>
                <input type="date" name="date_end" value="{{ request('date_end') }}" style="padding: 8px; border: none; font-size: 13px; color: #475569; outline: none; width: 130px;">
            </div>
            
            <button type="submit" style="background: #0070D2; color: white; border: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                <i class="fas fa-search"></i> Lọc
            </button>
            <a href="{{ route('deliveries.index') }}" style="background: #E74C3C; color: white; padding: 10px 18px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center;">Xóa lọc</a>
        </form>
    </div>

    {{-- ROW 2: SEARCH & SORT --}}
    <div class="filter-row" style="margin-bottom: 25px;">
        <div class="filter-group" style="flex: 1;">
            <div class="search-box-container">
                <form method="GET" style="margin:0;">
                    <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                    <input type="hidden" name="date_start" value="{{ request('date_start') }}">
                    <input type="hidden" name="date_end" value="{{ request('date_end') }}">
                    <input type="hidden" name="sort" value="{{ request('sort', 'newest') }}">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo mã phiếu, đơn, khách hàng..." class="search-input">
                </form>
            </div>

            <form method="GET" style="margin:0;">
                <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="date_start" value="{{ request('date_start') }}">
                <input type="hidden" name="date_end" value="{{ request('date_end') }}">
                <select name="sort" onchange="this.form.submit()" class="sort-select">
                    <option value="newest" {{ request('sort')=='newest'?'selected':'' }}>Mới nhất</option>
                    <option value="oldest" {{ request('sort')=='oldest'?'selected':'' }}>Cũ nhất</option>
                    <option value="az" {{ request('sort')=='az'?'selected':'' }}>Tên KH (A-Z)</option>
                    <option value="za" {{ request('sort')=='za'?'selected':'' }}>Tên KH (Z-A)</option>
                </select>
            </form>
        </div>
    </div>

    <div class="legacy-table-container" style="overflow-x: auto;">
        <table class="legacy-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">STT</th>
                    <th style="width: 12%; text-align: center;">NGÀY TẠO</th>
                    <th style="width: 15%; text-align: center;">MÃ PHIẾU</th>
                    <th style="width: 15%; text-align: center;">MÃ ĐƠN HÀNG</th>
                    <th style="width: 33%;">THÔNG TIN KHÁCH HÀNG</th>
                    <th style="width: 15%; text-align: center;">TRẠNG THÁI</th>
                    <th style="width: 5%; text-align: center;">XÓA</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveries as $idx => $dn)
                <tr onclick="window.location.href='{{ route('deliveries.show', $dn->id) }}'" title="Nhấp để xem chi tiết">
                    <td style="padding: 14px 15px; text-align: center;">{{ $deliveries->firstItem() + $idx }}</td>
                    <td style="padding: 14px 15px; text-align: center; white-space: nowrap;">{{ $dn->delivery_date ? $dn->delivery_date->format('d/m/Y') : '---' }}</td>
                    <td style="padding: 14px 15px; text-align: center; font-weight: 800; color: #0070D2; white-space: nowrap;">{{ $dn->dn_code }}</td>
                    <td style="padding: 14px 15px; text-align: center; font-weight: 800; color: #0070D2; white-space: nowrap;">{{ $dn->cto_code }}</td>
                    <td class="col-left" style="padding: 14px 15px;">
                        <div style="font-weight: 600; color: #0f172a;">{{ $dn->ten_kh }}</div>
                        <div style="display: flex; gap: 15px; margin-top: 4px;">
                            <span style="font-size: 11px; color: #94a3b8;"><i class="fas fa-id-card"></i> Mã KH: {{ $dn->ma_kh }}</span>
                            <span style="font-size: 11px; color: #64748b;"><i class="fas fa-user-edit"></i> Người tạo: {{ $dn->nguoi_tao }}</span>
                        </div>
                    </td>
                    <td style="padding: 14px 15px; text-align: center;">
                        <span class="badge-status {{ Str::slug($dn->trang_thai) }}">
                            {{ $dn->trang_thai }}
                        </span>
                    </td>
                    <td style="padding: 14px 15px; text-align: center;" onclick="event.stopPropagation()">
                        @if(auth()->user()->isAdmin())
                        <button onclick="deleteDN({{ $dn->id }}, '{{ $dn->dn_code }}')" class="action-btn btn-del-pro" title="Xóa phiếu"><i class="fas fa-trash-alt"></i></button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="padding: 40px; text-align: center; color: #94a3b8;">Chưa có phiếu giao hàng nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 25px;">
        <div style="color: #64748b; font-size: 14px;">Đang hiển thị {{ $deliveries->firstItem() ?? 0 }} - {{ $deliveries->lastItem() ?? 0 }} trong tổng số {{ $deliveries->total() }} phiếu giao hàng</div>
        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 8px; color: #64748b; font-size: 13px;">
                <span>Hiển thị:</span>
                <select onchange="window.location.href=this.value" style="border: none; outline: none; background: transparent; font-weight: 700; cursor: pointer; color: #0f172a; font-size: 14px;">
                    @foreach([5, 10, 15, 20, 50] as $size)
                        <option value="{{ request()->fullUrlWithQuery(['limit' => $size]) }}" {{ request('limit', 20) == $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
                <span>mục</span>
            </div>
            <div>{{ $deliveries->appends(request()->all())->links('pagination::bootstrap-4') }}</div>
        </div>
    </div>
</div>

{{-- MODAL CREATE DN --}}
<div id="modal-tao-dn" class="modal-overlay">
    <div class="modal-box" style="max-width: 500px;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #0f172a; font-size: 20px; font-weight: 800;"><i class="fas fa-truck" style="color: #0070D2;"></i> Tạo Phiếu Giao Hàng</h3>
            <i class="fas fa-times" style="cursor: pointer; color: #94a3b8;" onclick="closeModal('modal-tao-dn')"></i>
        </div>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <div>
                <label class="modal-pro-label">Chọn Đơn Hàng (CTO) <span style="color:#ef4444">*</span></label>
                <div style="position: relative;">
                    <input type="text" id="dn_search_cto" placeholder="Gõ mã đơn CTO hoặc tên khách..." class="modal-pro-input" autocomplete="off" onfocus="showDNOrderList()" oninput="filterDNOrderList()">
                    <div id="dn-order-dropdown" style="display:none; position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid #cbd5e1; border-radius:8px; max-height:200px; overflow-y:auto; z-index:1001; box-shadow: 0 10px 25px rgba(0,0,0,0.1);"></div>
                </div>
                <input type="hidden" id="dn_cto_code">
            </div>
            <div>
                <label class="modal-pro-label">Khách Hàng</label>
                <input type="text" id="dn_view_kh" class="modal-pro-input" readonly style="background:#f8fafc; color:#475569; font-weight:600;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label class="modal-pro-label">Hạn thanh toán (Ngày)</label>
                    <input type="number" id="dn_han_tt" class="modal-pro-input" value="7">
                </div>
                <div>
                    <label class="modal-pro-label">Ngày Giao Hàng <span style="color:#ef4444">*</span></label>
                    <input type="date" id="dn_delivery_date" class="modal-pro-input">
                </div>
            </div>
            <div>
                <label class="modal-pro-label">Mã Phiếu (Tự động)</label>
                <input type="text" id="dn_code_preview" class="modal-pro-input" readonly style="background:#f8fafc; color:#10b981; font-weight:800; text-align:center;">
            </div>

        </div>
        <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 20px; margin-top: 20px;">
            <button class="ui-btn ui-btn-outline" onclick="closeModal('modal-tao-dn')">Hủy</button>
            <button class="ui-btn ui-btn-primary" style="background:#0070D2;" onclick="submitCreateDN()">Tạo Phiếu</button>
        </div>
    </div>
</div>

<style>
    .dn-filter-btn { padding: 8px 16px; border-radius: 6px; font-size: 13px; color: #64748b; font-weight: 600; text-decoration: none; transition: 0.2s; background: transparent; border: none; cursor: pointer; }
    .dn-filter-btn:hover { color: #0f172a; background: #f8fafc; }
    .dn-filter-btn.active { background: #0070D2; color: white; box-shadow: 0 2px 6px rgba(79, 70, 229, 0.3); }

    .action-btn { width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; border: none; cursor: pointer; transition: 0.2s; }
    .btn-view-pro { background: #f1f5f9; color: #64748b; }
    .btn-view-pro:hover { background: #64748b; color: #fff; }
    .btn-edit-pro { background: #f5f3ff; color: #8b5cf6; margin: 0 4px; }
    .btn-edit-pro:hover { background: #8b5cf6; color: #fff; }
    .btn-del-pro { background: #fef2f2; color: #ef4444; }
    .btn-del-pro:hover { background: #ef4444; color: #fff; }

    .modal-pro-label { font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block; }
    .modal-pro-input { width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; font-size: 13px; outline: none; background: #fafafa; box-sizing: border-box; height: 36px; transition: border-color 0.2s, box-shadow 0.2s; }
    .modal-pro-input:focus { border-color: #0070D2; background: #fff; box-shadow: 0 0 0 3px rgba(0,112,210,0.1); }
</style>
@endsection

@push('scripts')
<script>
    const availableOrders = @json($availableOrders ?? []);

    function openCreateDNModal() {
        document.getElementById('dn_cto_code').value = '';
        document.getElementById('dn_search_cto').value = '';
        document.getElementById('dn_code_preview').value = 'DN-' + Date.now().toString().slice(-6);
        document.getElementById('dn_delivery_date').value = new Date().toISOString().split('T')[0];
        openModal('modal-tao-dn');
    }


    function showDNOrderList() { filterDNOrderList(); }
    function filterDNOrderList() {
        const q = document.getElementById('dn_search_cto').value.toLowerCase();
        const filtered = availableOrders.filter(o => (o.cto_code && o.cto_code.toLowerCase().includes(q)) || (o.ten_kh && o.ten_kh.toLowerCase().includes(q)));
        const dd = document.getElementById('dn-order-dropdown');
        dd.innerHTML = '';
        if (filtered.length > 0) {
            filtered.slice(0, 10).forEach(o => {
                const item = document.createElement('div');
                item.style.padding = '10px 15px';
                item.style.cursor = 'pointer';
                item.style.borderBottom = '1px solid #f1f5f9';
                item.innerHTML = `<b style="color:#0070D2">${o.cto_code}</b> - <span style="color:#475569">${o.ten_kh}</span>`;
                item.onclick = () => selectDNOrder(o);
                dd.appendChild(item);
            });
            dd.style.display = 'block';
        } else { dd.style.display = 'none'; }
    }

    function selectDNOrder(o) {
        document.getElementById('dn_cto_code').value = o.cto_code;
        document.getElementById('dn_search_cto').value = `[${o.cto_code}] ${o.ten_kh}`;
        document.getElementById('dn_view_kh').value = o.ten_kh || '---';
        document.getElementById('dn-order-dropdown').style.display = 'none';
    }

    async function submitCreateDN() {
        const cto = document.getElementById('dn_cto_code').value;
        if (!cto) return alert('Vui lòng chọn đơn hàng!');

        const res = await apiPost('{{ route("deliveries.store") }}', {
            cto_code: cto,
            dn_code: document.getElementById('dn_code_preview').value,
            han_thanh_toan: document.getElementById('dn_han_tt').value,
            delivery_date: document.getElementById('dn_delivery_date').value
        });

        if (res.success) { await showToast(res.message); location.reload(); }
        else { alert(res.message); }
    }

    function deleteDN(id, code) {
        showConfirm('Xóa Phiếu Giao', `Bạn có chắc muốn xóa phiếu? Hành động này không thể hoàn tác.`, () => {
            fetch(`/phieu-giao/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(r => r.json())
            .then(async res => {
                if (res.success) { 
                    await showToast(res.message); 
                    location.reload(); 
                }
                else { alert(res.message); }
            });
        });
    }

    function applyCustomDate() {
        const start = document.getElementById('date_start').value;
        const end = document.getElementById('date_end').value;
        if (!start || !end) {
            alert('Vui lòng chọn cả Từ ngày và Đến ngày!');
            return;
        }
        window.location.href = `{{ route('deliveries.index') }}?filter=custom&date_start=${start}&date_end=${end}`;
    }
</script>
@endpush
