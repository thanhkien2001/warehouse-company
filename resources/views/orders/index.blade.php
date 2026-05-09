@extends('layouts.app')
@section('title', 'Quản Lý Đơn Hàng')
@section('page-title', 'Quản Lý Đơn Hàng')
@section('page-subtitle', 'Quản lý, theo dõi và cập nhật trạng thái đơn hàng của bạn.')

@section('content')
<div class="card" style="padding: 24px;">
    
    <div class="page-header-row" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 2.5px solid #cbd5e1; margin-bottom: 25px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: #eff6ff; border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.15); flex-shrink: 0;">
                <i class="fas fa-cart-plus" style="font-size: 24px; color: #3b82f6;"></i>
            </div>
            <div>
                <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; letter-spacing: -0.5px;">Quản Lý Đơn Hàng</h2>
                <p style="margin: 0; color: #64748b; font-size: 13.5px;">Tạo và quản lý các đơn hàng Booking.</p>
            </div>
        </div>
        @if(auth()->user()->canDo('donhang', 'edit') || auth()->user()->isAdmin())
        <button onclick="openCreateModal()" style="background: #0070D2; color: white; border: none; padding: 8px 18px; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
            <i class="fas fa-plus"></i> Tạo Đơn Hàng Mới
        </button>
        @endif
    </div>

    <style>
        /* Order tabs */
        .ord-tabs-row { padding-bottom: 12px; border-bottom: 1.5px solid #f1f5f9; }
        .order-tabs { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .order-tab { padding: 7px 14px; border-radius: 50px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; color: #64748b; background: #f1f5f9; display: flex; align-items: center; gap: 6px; transition: 0.2s; border: 1px solid transparent; }
        .order-tab:hover { background: #e2e8f0; color: #0f172a; }
        .order-tab.active { background: #0070D2; color: #fff; box-shadow: 0 4px 10px rgba(0,112,210,0.2); border-color: #0070D2; }
        .order-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 18px; height: 18px; border-radius: 50%; font-size: 10px; color: #fff; font-weight: 800; padding: 0 4px; }
        .order-badge.pending { background: #E67E22; } .order-badge.processing { background: #3498DB; }
        .order-badge.shipping { background: #8E44AD; } .order-badge.canceled { background: #E74C3C; }
        .order-badge.completed { background: #27AE60; } .order-badge.default { background: #94a3b8; }

        /* Filter row 2 */
        .ord-filter-card { padding: 14px 0 6px; border-bottom: 1.5px solid #f1f5f9; }
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

        /* Table */
        .ord-table-card { border-top: 1.5px solid #f1f5f9; overflow: hidden; }
        .ord-table { width: 100%; border-collapse: collapse; margin-top: 15px;}
        .ord-table thead th { background: #EFF6FF; padding: 10px 8px; font-size: 13px; font-weight: 700; color: #000; text-align: center; border: 1px solid #e2e8f0; white-space: nowrap; text-transform: uppercase; }
        .ord-table tbody td { padding: 9px 8px; font-size: 13px; color: #1e293b; text-align: center; border: 1px solid #e2e8f0; vertical-align: middle; }
        .ord-table tbody tr:hover { background: #f0f7ff; }
        .ord-text-bold { font-weight: 700; color: #0070D2; }
        .ord-text-left { text-align: left !important; }

        /* Action buttons */
        .ord-action-buttons { display: flex; justify-content: center; gap: 6px; }
        .ord-action-buttons .btn-edit, .ord-action-buttons .btn-delete { width: 28px; height: 28px; border-radius: 4px; border: 1px solid #e2e8f0; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 13px; transition: all 0.2s; }
        .ord-action-buttons .btn-edit { color: #3b82f6; }
        .ord-action-buttons .btn-edit:hover { background: #3b82f6; color: #fff; border-color: #3b82f6; }
        .ord-action-buttons .btn-delete { color: #ef4444; }
        .ord-action-buttons .btn-delete:hover { background: #ef4444; color: #fff; border-color: #ef4444; }

        @media (max-width: 1200px) {
            .ord-filter-grid { flex-wrap: wrap; }
            .ord-table-card { overflow-x: auto; }
            .ord-table { min-width: 1100px; }
        }
        @media (max-width: 768px) {
            .card { padding: 15px !important; }
            .page-header-row { flex-direction: column; align-items: flex-start !important; gap: 15px; }
        }
    </style>


    {{-- Row 1: Tabs trạng thái --}}
    <div class="ord-tabs-row">
        <div class="order-tabs">
            <a href="{{ request()->fullUrlWithQuery(['status' => 'all']) }}" class="order-tab {{ request('status', 'all') == 'all' ? 'active' : '' }}">
                Tất cả <span style="opacity: 0.7; font-weight: normal;">({{ $counts['all'] ?? 0 }})</span>
            </a>
            @php
                $orderStatusList = ['Chờ xác nhận', 'Đang xử lý', 'Đang vận chuyển', 'Hoàn thành', 'Đã hủy'];
                $statusClasses = ['Chờ xác nhận'=>'pending','Đang xử lý'=>'processing','Đang vận chuyển'=>'shipping','Hoàn thành'=>'completed','Đã hủy'=>'canceled'];
            @endphp
            @foreach($orderStatusList as $st)
                <a href="{{ request()->fullUrlWithQuery(['status' => $st]) }}" class="order-tab {{ request('status') == $st ? 'active' : '' }}">
                    {{ $st }} <span class="order-badge {{ $statusClasses[$st] ?? 'default' }}">{{ $counts[$st] ?? 0 }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Row 2: Ngày + Search + Sort + Lọc/Xóa --}}
    <div class="ord-filter-card">
        <form method="GET" id="ord-filter-form">
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
                    <label>Tìm kiếm</label>
                    <div class="ord-search-wrapper">
                        <input type="text" name="search" class="ord-filter-input" placeholder="Tìm mã đơn (CTO), khách hàng..." value="{{ request('search') }}">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="ord-filter-item">
                    <label>Sắp xếp</label>
                    <select name="sort" class="ord-filter-input">
                        <option value="newest" {{ request('sort','newest')=='newest'?'selected':'' }}>Mới nhất</option>
                        <option value="oldest" {{ request('sort')=='oldest'?'selected':'' }}>Cũ nhất</option>
                        <option value="az" {{ request('sort')=='az'?'selected':'' }}>Tên KH (A-Z)</option>
                        <option value="za" {{ request('sort')=='za'?'selected':'' }}>Tên KH (Z-A)</option>
                    </select>
                </div>
                <div class="ord-filter-item" style="flex: none;">
                    <label>&nbsp;</label>
                    <div style="display: flex; gap: 8px;">
                        <button type="submit" class="ord-btn-search"><i class="fas fa-search"></i> Lọc</button>
                        <a href="{{ route('orders.index') }}" class="ord-btn-clear"><i class="fas fa-times"></i> Xóa lọc</a>
                    </div>
                </div>
            </div>
        </form>
    </div>


    {{-- TABLE --}}
    <div class="ord-table-card">
        <div class="table-responsive">
            <table class="ord-table">
                <thead>
                    <tr>
                        <th width="4%">STT</th>
                        <th width="9%">Ngày tạo</th>
                        <th width="10%">Mã Đơn</th>
                        <th width="8%">Mã KH</th>
                        <th width="22%">Tên Khách Hàng</th>
                        <th width="10%">MST</th>
                        <th width="10%">Khu Vực</th>
                        <th width="12%">Trạng thái</th>
                        <th width="9%">Ghi chú</th>
                        <th width="6%">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $idx => $order)
                    <tr onclick="window.location.href='{{ route('orders.show', $order->id) }}'" style="cursor: pointer;">
                        <td>{{ $orders->firstItem() + $idx }}</td>
                        <td>{{ $order->order_date ? $order->order_date->format('d/m/Y') : '---' }}</td>
                        <td class="ord-text-bold">{{ $order->cto_code }}</td>
                        <td class="ord-text-bold">{{ $order->customer->ma_kh ?? '---' }}</td>
                        <td class="ord-text-left" style="font-weight: 600; text-transform: uppercase;">{{ $order->customer->ten_cty ?? '---' }}</td>
                        <td>{{ $order->customer->ma_so_thue ?? '---' }}</td>
                        <td>
                            @if($order->customer?->khu_vuc == 'Miền Bắc') <span class="badge-region mien-bac">Miền Bắc</span>
                            @elseif($order->customer?->khu_vuc == 'Miền Trung') <span class="badge-region mien-trung">Miền Trung</span>
                            @elseif($order->customer?->khu_vuc == 'Miền Nam') <span class="badge-region mien-nam">Miền Nam</span>
                            @else ---
                            @endif
                        </td>
                        <td><span class="badge-status {{ Str::slug($order->trang_thai) }}">{{ $order->trang_thai }}</span></td>
                        <td class="ord-text-left" style="font-style: italic; color: #94a3b8; font-size: 12px;">{{ $order->ghi_chu ?: '---' }}</td>
                        <td onclick="event.stopPropagation()">
                            <div class="ord-action-buttons">
                                <button onclick="editOrder({{ $order->id }})" class="btn-edit" title="Sửa"><i class="fas fa-edit"></i></button>
                                @if(auth()->user()->isAdmin())
                                <button onclick="deleteOrder({{ $order->id }})" class="btn-delete" title="Xóa"><i class="fas fa-trash-alt"></i></button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" style="text-align:center; padding:40px; color:#94a3b8;"><i class="fas fa-inbox fa-2x" style="margin-bottom:10px; display:block;"></i>Không có đơn hàng nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>




    <!-- Pagination -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 25px;">
        <div style="color: #64748b; font-size: 14px;">Đang hiển thị {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} trong tổng số {{ $orders->total() }} đơn hàng</div>
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
            <div>{{ $orders->appends(request()->all())->links('pagination::bootstrap-4') }}</div>
        </div>
    </div>
</div>

{{-- MODAL CREATE/EDIT ORDER --}}
<div id="modal-taodon" class="modal-overlay">
    <div class="modal-box" style="max-width: 600px;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 20px;">
            <h3 id="modal-title" style="margin: 0; color: #0f172a; font-size: 20px; font-weight: 800;">Tạo Đơn Hàng Mới (CTO)</h3>
            <i class="fas fa-times" style="cursor: pointer; color: #94a3b8;" onclick="closeModal('modal-taodon')"></i>
        </div>
        <input type="hidden" id="order_id">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div style="grid-column: span 2;">
                <label class="modal-pro-label">Tên khách hàng <span style="color:#ef4444">*</span></label>
                <div style="position: relative;">
                    <input type="text" id="inp-search-kh" class="modal-pro-input" placeholder="Nhập tên hoặc mã KH để tìm..." autocomplete="off">
                    <div id="kh-dropdown" style="display:none; position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid #cbd5e1; border-radius:8px; max-height:200px; overflow-y:auto; z-index:1001; box-shadow: 0 10px 25px rgba(0,0,0,0.1);"></div>
                </div>
                <input type="hidden" id="order_kh_id">
            </div>
            <div>
                <label class="modal-pro-label">Mã CTO <span style="font-size: 11px; color: #94a3b8;">(Tự động)</span></label>
                <input type="text" id="order_ma_don" class="modal-pro-input" readonly placeholder="CTO-..." style="background:#f1f5f9; color:#2563eb; font-weight:700;">
            </div>
            <div>
                <label class="modal-pro-label">Ngày tạo</label>
                <input type="date" id="order_date" class="modal-pro-input" value="{{ date('Y-m-d') }}">
            </div>
            <div style="grid-column: span 2;">
                <label class="modal-pro-label">Ghi chú</label>
                <input type="text" id="order_ghi_chu" class="modal-pro-input" placeholder="Nhập ghi chú...">
            </div>

            <div style="grid-column: span 2; border-top: 1px dashed #cbd5e1; padding-top: 15px; margin-top: 5px;">
                <span style="font-size: 13px; color: #3b82f6; font-weight: 700;"><i class="fas fa-file-invoice"></i> THÔNG TIN TRÊN FILE PDF</span>
            </div>
            <div>
                <label class="modal-pro-label">Người bán (Đại diện)</label>
                <input type="text" id="order_nguoi_ban" class="modal-pro-input" placeholder="Tên người bán...">
            </div>
            <div>
                <label class="modal-pro-label">SĐT Người bán</label>
                <input type="text" id="order_sdt_ban" class="modal-pro-input" placeholder="SĐT...">
            </div>
            <div>
                <label class="modal-pro-label">Người mua (Đại diện)</label>
                <input type="text" id="order_nguoi_mua" class="modal-pro-input" placeholder="Tên người mua...">
            </div>
            <div>
                <label class="modal-pro-label">SĐT Người mua</label>
                <input type="text" id="order_sdt_mua" class="modal-pro-input" placeholder="SĐT...">
            </div>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 20px; margin-top: 20px;">
            <button class="ui-btn ui-btn-outline" onclick="closeModal('modal-taodon')">Hủy bỏ</button>
            <button class="ui-btn ui-btn-primary" id="btn-submit-order" onclick="submitOrder()">Lưu Đơn Hàng</button>
        </div>
    </div>
</div>

<style>
    .don-filter-btn { padding: 8px 16px; border-radius: 50px; font-size: 13px; color: #64748b; font-weight: 600; text-decoration: none; transition: 0.2s; cursor: pointer; }
    .don-filter-btn:hover { background: #f8fafc; color: #0f172a; }
    .don-filter-btn.active { background: #0070D2; color: white !important; box-shadow: 0 2px 6px rgba(79, 70, 229, 0.3); }

    .prem-tab { padding: 12px 15px; color: #64748b; font-weight: 600; font-size: 14px; text-decoration: none; border-bottom: 3px solid transparent; display: flex; align-items: center; gap: 8px; transition: 0.3s; white-space: nowrap; }
    .prem-tab:hover { color: #0070D2; }
    .prem-tab.active { color: #0070D2; border-bottom-color: #0070D2; }
    .prem-tab .badge { background: #e2e8f0; color: #475569; padding: 2px 8px; border-radius: 6px; font-size: 12px; font-weight: 700; }
    .prem-tab.active .badge { background: #e0e7ff; color: #0070D2; }

    .action-btn { width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; border: none; cursor: pointer; transition: 0.2s; }
    .btn-view-pro { background: #f1f5f9; color: #64748b; }
    .btn-view-pro:hover { background: #64748b; color: #fff; }
    .btn-edit-pro { background: #eff6ff; color: #3b82f6; margin: 0 4px; }
    .btn-edit-pro:hover { background: #3b82f6; color: #fff; }
    .btn-del-pro { background: #fef2f2; color: #ef4444; }
    .btn-del-pro:hover { background: #ef4444; color: #fff; }

    .modal-pro-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block; }
    .modal-pro-input { width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; font-size: 13px; outline: none; background: #f8fafc; box-sizing: border-box; height: 36px; transition: border-color 0.2s, box-shadow 0.2s; }
    .modal-pro-input:focus { border-color: #0070D2; background: #fff; box-shadow: 0 0 0 3px rgba(0,112,210,0.1); }

    .badge-blue { background: #eff6ff; color: #1d4ed8; }
    .badge-yellow { background: #fffbeb; color: #92400e; }
    .badge-green { background: #f0fdf4; color: #166534; }
</style>
@endsection

@push('scripts')
<script>
    const customers = @json($customers);
    
    function toggleCustomDate() {
        const box = document.getElementById('custom-date-box');
        const btn = document.getElementById('btn-custom-time');
        if (box.style.display === 'none') {
            box.style.display = 'flex';
            btn.classList.add('active');
        } else {
            box.style.display = 'none';
            // Chỉ remove active nếu request hiện tại không phải là custom
            if ("{{ request('time') }}" !== 'custom') {
                btn.classList.remove('active');
            }
        }
    }

    function applyCustomDate() {
        const start = document.getElementById('date_start').value;
        const end = document.getElementById('date_end').value;
        if (!start || !end) return alert('Vui lòng chọn đầy đủ ngày bắt đầu và kết thúc');
        const url = new URL(window.location.href);
        url.searchParams.set('time', 'custom');
        url.searchParams.set('date_start', start);
        url.searchParams.set('date_end', end);
        window.location.href = url.toString();
    }

    function openCreateModal() {
        document.getElementById('modal-title').innerText = 'Tạo Đơn Hàng Mới (CTO)';
        document.getElementById('order_id').value = '';
        document.getElementById('order_kh_id').value = '';
        document.getElementById('inp-search-kh').value = '';
        document.getElementById('order_ma_don').value = 'CTO-XXXXXX-XXXX';
        document.getElementById('order_ghi_chu').value = '';
        document.getElementById('order_nguoi_ban').value = '{{ auth()->user()->display_name ?? auth()->user()->username }}';
        document.getElementById('order_sdt_ban').value = '{{ \App\Models\SystemSetting::get("sdt_cong_ty", "0368 301 305") }}';
        document.getElementById('order_nguoi_mua').value = '';
        document.getElementById('order_sdt_mua').value = '';
        openModal('modal-taodon');
    }

    async function editOrder(id) {
        const res = await fetch(`/don-hang/${id}/edit`).then(r => r.json());
        if (!res.success) return alert('Lỗi: ' + res.message);
        
        const o = res.data;
        document.getElementById('modal-title').innerText = 'Chỉnh Sửa Đơn Hàng';
        document.getElementById('order_id').value = o.id;
        document.getElementById('order_kh_id').value = o.customer_id;
        document.getElementById('inp-search-kh').value = o.customer.ten_cty;
        document.getElementById('order_ma_don').value = o.cto_code;
        document.getElementById('order_date').value = o.order_date.split('T')[0];
        document.getElementById('order_ghi_chu').value = o.ghi_chu || '';
        
        document.getElementById('order_nguoi_ban').value = o.meta?.seller_name || '';
        document.getElementById('order_sdt_ban').value = o.meta?.seller_phone || '';
        document.getElementById('order_nguoi_mua').value = o.meta?.buyer_name || '';
        document.getElementById('order_sdt_mua').value = o.meta?.buyer_phone || '';
        
        openModal('modal-taodon');
    }

    // Autocomplete for Customer
    const searchInp = document.getElementById('inp-search-kh');
    const dropdown = document.getElementById('kh-dropdown');

    searchInp.addEventListener('focus', filterKH);
    searchInp.addEventListener('input', filterKH);

    function filterKH() {
        const q = searchInp.value.toLowerCase();
        const filtered = customers.filter(c => c.ten_cty.toLowerCase().includes(q) || c.ma_kh.toLowerCase().includes(q));
        
        dropdown.innerHTML = '';
        if (filtered.length > 0) {
            filtered.slice(0, 10).forEach(c => {
                const item = document.createElement('div');
                item.style.padding = '10px 15px';
                item.style.cursor = 'pointer';
                item.style.borderBottom = '1px solid #f1f5f9';
                item.innerHTML = `<b style="color:var(--primary)">[${c.ma_kh}]</b> ${c.ten_cty}`;
                item.onclick = () => selectKH(c);
                dropdown.appendChild(item);
            });
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
    }

    async function selectKH(c) {
        document.getElementById('order_kh_id').value = c.id;
        searchInp.value = c.ten_cty;
        dropdown.style.display = 'none';

        // Tự động điền thông tin Người mua từ khách hàng
        document.getElementById('order_nguoi_mua').value = c.nguoi_lien_he || '';
        document.getElementById('order_sdt_mua').value = c.sdt || '';
        
        // Tự động điền thông tin Người bán (Mặc định user hiện tại)
        document.getElementById('order_nguoi_ban').value = '{{ auth()->user()->display_name ?? auth()->user()->username }}';
        document.getElementById('order_sdt_ban').value = '{{ \App\Models\SystemSetting::get("sdt_cong_ty", "0368 301 305") }}';

        // Tự động lấy mã CTO mới cho khách hàng này
        try {
            const res = await fetch(`{{ route('orders.next-code') }}?ma_kh=${c.ma_kh}`).then(r => r.json());
            if (res.success) {
                document.getElementById('order_ma_don').value = res.code;
            }
        } catch (err) { console.error('Lỗi lấy mã CTO:', err); }
    }

    document.addEventListener('click', (e) => {
        if (!searchInp.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    async function submitOrder() {
        const id = document.getElementById('order_id').value;
        const kh_id = document.getElementById('order_kh_id').value;
        if (!kh_id) return alert('Vui lòng chọn khách hàng!');

        const data = {
            customer_id: kh_id,
            ma_don: document.getElementById('order_ma_don').value,
            ngay_tao: document.getElementById('order_date').value,
            ghi_chu: document.getElementById('order_ghi_chu').value,
            seller_name: document.getElementById('order_nguoi_ban').value,
            seller_phone: document.getElementById('order_sdt_ban').value,
            buyer_name: document.getElementById('order_nguoi_mua').value,
            buyer_phone: document.getElementById('order_sdt_mua').value,
            trang_thai: 'Chờ xác nhận'
        };

        const url = id ? `/don-hang/${id}` : '{{ route("orders.store") }}';
        const method = id ? 'PUT' : 'POST';
        
        // Use standard fetch for complex PUT/POST if apiPost doesn't support specific method
        const res = await fetch(url, {
            method: method,
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        }).then(r => r.json());

        if (res.success) {
            await showToast(res.message);
            location.reload();
        } else {
            alert(res.message || 'Lỗi khi lưu đơn hàng');
        }
    }

    function deleteOrder(id) {
        showConfirm('Xóa Đơn Hàng', 'Bạn có chắc chắn muốn xóa đơn hàng này? Hành động này không thể hoàn tác.', async () => {
            const res = await fetch(`/don-hang/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(r => r.json());
            
            if (res.success) { 
                await showToast(res.message); 
                location.reload(); 
            } else { 
                alert(res.message); 
            }
        });
    }

    async function saveTyGia() {
        const v = document.getElementById('ty-gia-val').value.replace(/[^0-9]/g,'');
        const res = await apiPost('{{ route("admin.settings.save") }}', { key_name: 'ty_gia', value: v });
        if (res.success) await showToast('Đã lưu tỷ giá!');
    }
</script>
@endpush
