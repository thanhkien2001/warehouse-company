@extends('layouts.app')
@section('title', 'Phiếu Giao Hàng')
@section('page-title', 'Phiếu Giao Hàng')
@section('page-subtitle', 'Quản lý, thêm mới, in ấn và theo dõi tiến độ phiếu giao hàng.')

@push('styles')
<style>
    /* DN tabs */
    .dn-tabs-row { padding-bottom: 12px; border-bottom: 1.5px solid #f1f5f9; }
    .dn-tabs { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    .dn-tab { padding: 7px 14px; border-radius: 50px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; color: #64748b; background: #f1f5f9; display: flex; align-items: center; gap: 6px; transition: 0.2s; border: 1px solid transparent; }
    .dn-tab:hover { background: #e2e8f0; color: #0f172a; }
    .dn-tab.active { background: #002B6B; color: #fff; box-shadow: 0 4px 10px rgba(0,112,210,0.2); border-color: #002B6B; }
    
    .dn-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 18px; height: 18px; border-radius: 50%; font-size: 10px; color: #fff; font-weight: 800; padding: 0 4px; }
    .dn-badge.cho-giao { background: #F39C12; }
    .dn-badge.dang-giao { background: #8E44AD; }
    .dn-badge.da-giao { background: #27AE60; }
    .dn-badge.da-huy { background: #E74C3C; }
    .dn-badge.default { background: #94a3b8; }

    /* Filter row 2 */
    .dn-filter-card { padding: 14px 0 6px; border-bottom: 1.5px solid #f1f5f9; }
    .dn-filter-grid { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; }
    .dn-filter-item { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 120px; }
    .dn-filter-item label { font-size: 13px; font-weight: 700; color: #1e293b; }
    .dn-filter-input { height: 36px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0 12px; font-size: 13px; outline: none; width: 100%; box-sizing: border-box; color: #1e293b; background: #fff; }
    .dn-filter-input:focus { border-color: #002B6B; box-shadow: 0 0 0 3px rgba(0,112,210,0.1); }
    .dn-search-wrapper { position: relative; }
    .dn-search-wrapper i { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; pointer-events: none; }
    .dn-search-wrapper .dn-filter-input { padding-right: 36px; }
    .dn-btn-search { height: 36px; padding: 0 16px; background: #002B6B; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px; white-space: nowrap; }
    .dn-btn-search:hover { background: #005bb5; }
    .dn-btn-clear { height: 36px; padding: 0 16px; background: #fff; color: #ef4444; border: 1px solid #e2e8f0; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px; text-decoration: none; white-space: nowrap; }
    .dn-btn-clear:hover { background: #fef2f2; border-color: #ef4444; }

    /* Table */
    .dn-table-card { border-top: 1.5px solid #f1f5f9; overflow: hidden; margin-top: 10px; }
    .dn-table { width: 100%; border-collapse: collapse; }
    .dn-table thead th { background: #EFF6FF; padding: 10px 8px; font-size: 13px; font-weight: 700; color: #000; text-align: center; border: 1px solid #e2e8f0; white-space: nowrap; }
    .dn-table tbody td { padding: 9px 8px; font-size: 13px; color: #1e293b; text-align: center; border: 1px solid #e2e8f0; vertical-align: middle; }
    .dn-table tbody tr:hover { background: #f0f7ff; cursor: pointer; }
    .dn-text-bold { font-weight: 700; color: #002B6B; }
    .dn-text-left { text-align: left !important; }

    /* Action buttons */
    .dn-action-buttons { display: flex; justify-content: center; gap: 6px; }
    .dn-action-buttons .btn-view, .dn-action-buttons .btn-delete { width: 28px; height: 28px; border-radius: 4px; border: 1px solid #e2e8f0; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 13px; transition: all 0.2s; }
    .dn-action-buttons .btn-view { color: #3b82f6; }
    .dn-action-buttons .btn-view:hover { background: #3b82f6; color: #fff; border-color: #3b82f6; }
    .dn-action-buttons .btn-delete { color: #ef4444; }
    .dn-action-buttons .btn-delete:hover { background: #ef4444; color: #fff; border-color: #ef4444; }

    .modal-pro-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block; }
    .modal-pro-input { width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; font-size: 13px; outline: none; background: #fafafa; box-sizing: border-box; height: 36px; transition: border-color 0.2s, box-shadow 0.2s; }
    .modal-pro-input:focus { border-color: #002B6B; background: #fff; box-shadow: 0 0 0 3px rgba(0,112,210,0.1); }

    @media (max-width: 1200px) {
        .dn-filter-grid { flex-wrap: wrap; }
        .dn-table-card { overflow-x: auto; }
        .dn-table { min-width: 1100px; }
    }
    @media (max-width: 768px) {
        .card { padding: 15px !important; }
        .page-header-row { flex-direction: column; align-items: flex-start !important; gap: 15px; }
    }
</style>
@endpush

@section('content')
<div class="card" style="padding: 24px;">
    
    <div class="page-header-row" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 2.5px solid #cbd5e1; margin-bottom: 25px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: #eff6ff; border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(139, 92, 246, 0.15); flex-shrink: 0;">
                <i class="fas fa-file-export" style="font-size: 24px; color: #002B6B;"></i>
            </div>
            <div>
                <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; letter-spacing: -0.5px;text-transform: uppercase;">Phiếu Giao Hàng</h2>
                <p style="margin: 0; color: #64748b; font-size: 13px;text-transform: uppercase;text-transform: uppercase;">Quản lý và xuất kho hàng hóa.</p>
            </div>
        </div>
        @if(auth()->user()->canDo('phieugiao', 'edit') || auth()->user()->isAdmin())
        <button onclick="openCreateDNModal()" style="background: #002B6B; color: white; border: none; padding: 8px 18px; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
            <i class="fas fa-plus"></i> Tạo Phiếu Giao Hàng
        </button>
        @endif
    </div>

    {{-- ROW 1: STATUS TABS --}}
    <div class="dn-tabs-row">
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
    </div>

    {{-- ROW 2: FILTERS --}}
    <div class="dn-filter-card">
        <form method="GET" id="dn-filter-form">
            <input type="hidden" name="status" value="{{ request('status', 'all') }}">
            <div class="dn-filter-grid">
                <div class="dn-filter-item">
                    <label>Từ ngày</label>
                    <input type="date" name="date_start" class="dn-filter-input" value="{{ request('date_start') }}">
                </div>
                <div class="dn-filter-item">
                    <label>Đến ngày</label>
                    <input type="date" name="date_end" class="dn-filter-input" value="{{ request('date_end') }}">
                </div>
                <div class="dn-filter-item" style="flex: 2;">
                    <label>Tìm kiếm</label>
                    <div class="dn-search-wrapper">
                        <input type="text" name="search" class="dn-filter-input" placeholder="Tìm theo mã phiếu, đơn, khách hàng..." value="{{ request('search') }}">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="dn-filter-item">
                    <label>Sắp xếp</label>
                    <select name="sort" class="dn-filter-input" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort','newest')=='newest'?'selected':'' }}>Mới nhất</option>
                        <option value="oldest" {{ request('sort')=='oldest'?'selected':'' }}>Cũ nhất</option>
                        <option value="az" {{ request('sort')=='az'?'selected':'' }}>Tên KH (A-Z)</option>
                        <option value="za" {{ request('sort')=='za'?'selected':'' }}>Tên KH (Z-A)</option>
                    </select>
                </div>
                <div class="dn-filter-item" style="flex: none;">
                    <label>&nbsp;</label>
                    <div style="display: flex; gap: 8px;">
                        <button type="submit" class="dn-btn-search"><i class="fas fa-search"></i> Lọc</button>
                        <a href="{{ route('deliveries.index') }}" class="dn-btn-clear"><i class="fas fa-times"></i> Xóa lọc</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="dn-table-card">
        <div class="table-responsive">
            <table class="dn-table">
                <thead>
                    <tr>
                        <th width="5%">STT</th>
                        <th width="12%">NGÀY TẠO</th>
                        <th width="15%">MÃ PHIẾU</th>
                        <th width="15%">MÃ ĐƠN HÀNG</th>
                        <th>THÔNG TIN KHÁCH HÀNG</th>
                        <th width="15%">TRẠNG THÁI</th>
                        <th width="6%">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveries as $idx => $dn)
                    <tr onclick="window.location.href='{{ route('deliveries.show', $dn->id) }}'">
                        <td>{{ $deliveries->firstItem() + $idx }}</td>
                        <td>{{ $dn->delivery_date ? $dn->delivery_date->format('d/m/Y') : '---' }}</td>
                        <td class="dn-text-bold">{{ $dn->dn_code }}</td>
                        <td class="dn-text-bold">{{ $dn->cto_code }}</td>
                        <td class="dn-text-left">
                            <div style="font-weight: 700; color: #0f172a; text-transform: uppercase;">{{ $dn->ten_kh }}</div>
                            <div style="display: flex; gap: 15px; margin-top: 4px;">
                                <span style="font-size: 11px; color: #94a3b8;"><i class="fas fa-id-card"></i> Mã KH: {{ $dn->ma_kh }}</span>
                                <span style="font-size: 11px; color: #64748b;"><i class="fas fa-user-edit"></i> Người tạo: {{ $dn->nguoi_tao }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge-status {{ Str::slug($dn->trang_thai) }}">
                                {{ $dn->trang_thai }}
                            </span>
                        </td>
                        <td onclick="event.stopPropagation()">
                            <div class="dn-action-buttons">
                                <button onclick="window.location.href='{{ route('deliveries.show', $dn->id) }}'" class="btn-view" title="Xem chi tiết"><i class="fas fa-eye"></i></button>
                                @if(auth()->user()->isAdmin())
                                <button onclick="deleteDN({{ $dn->id }}, '{{ $dn->dn_code }}')" class="btn-delete" title="Xóa phiếu"><i class="fas fa-trash-alt"></i></button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="padding: 40px; text-align: center; color: #94a3b8;"><i class="fas fa-inbox fa-2x" style="margin-bottom:10px; display:block;"></i>Chưa có phiếu giao hàng nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
            <h3 style="margin: 0; color: #0f172a; font-size: 20px; font-weight: 800;"><i class="fas fa-truck" style="color: #002B6B;"></i> Tạo Phiếu Giao Hàng</h3>
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
                <input type="text" id="dn_code_preview" class="modal-pro-input" readonly style="background:#f8fafc; color:#002B6B; font-weight:800; text-align:center;">
            </div>

        </div>
        <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 20px; margin-top: 20px;">
            <button class="ui-btn ui-btn-outline" onclick="closeModal('modal-tao-dn')">Hủy</button>
            <button class="ui-btn ui-btn-primary" style="background:#002B6B;" onclick="submitCreateDN()">Tạo Phiếu</button>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
    const availableOrders = @json($availableOrders ?? []);

    function openCreateDNModal() {
        document.getElementById('dn_cto_code').value = '';
        document.getElementById('dn_search_cto').value = '';
        document.getElementById('dn_code_preview').value = 'DN-XXXXXX-XXXX';
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
                item.innerHTML = `<b style="color:#002B6B">${o.cto_code}</b> - <span style="color:#475569">${o.ten_kh}</span>`;
                item.onclick = () => selectDNOrder(o);
                dd.appendChild(item);
            });
            dd.style.display = 'block';
        } else { dd.style.display = 'none'; }
    }

    async function selectDNOrder(o) {
        document.getElementById('dn_cto_code').value = o.cto_code;
        document.getElementById('dn_search_cto').value = `[${o.cto_code}] ${o.ten_kh}`;
        document.getElementById('dn_view_kh').value = o.ten_kh || '---';
        document.getElementById('dn-order-dropdown').style.display = 'none';

        // Tự động lấy mã DN mới cho khách hàng này
        if (o.ma_kh) {
            try {
                const res = await fetch(`{{ route('deliveries.next-code') }}?ma_kh=${o.ma_kh}`).then(r => r.json());
                if (res.success) {
                    document.getElementById('dn_code_preview').value = res.code;
                }
            } catch (err) { console.error('Lỗi lấy mã DN:', err); }
        }
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
