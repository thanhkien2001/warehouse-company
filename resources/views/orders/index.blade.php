@extends('layouts.app')
@section('title', 'Đơn hàng (CTO)')
@section('page-title', 'Quản Lý Đơn Hàng')
@section('page-subtitle', 'Quản lý, theo dõi và cập nhật trạng thái đơn hàng của bạn.')

@section('content')
<div class="card" style="padding: 24px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 2.5px solid #cbd5e1; margin-bottom: 25px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: #eff6ff; border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.15); flex-shrink: 0;">
                <i class="fas fa-cart-plus" style="font-size: 24px; color: #3b82f6;"></i>
            </div>
            <div>
                <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; letter-spacing: -0.5px;">Đơn Hàng (CTO)</h2>
                <p style="margin: 0; color: #64748b; font-size: 13.5px;">Tạo và quản lý các đơn hàng Booking.</p>
            </div>
        </div>
        @if(auth()->user()->canDo('donhang', 'edit') || auth()->user()->isAdmin())
        <button onclick="openCreateModal()" style="background: #0070D2; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
            <i class="fas fa-plus"></i> Tạo Đơn Hàng Mới
        </button>
        @endif
    </div>

    <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 15px;">
        <div style="display: flex; background: #fff; border: 1px solid #cbd5e1; border-radius: 50px; padding: 3px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <a href="{{ route('orders.index', array_merge(request()->all(), ['time'=>'all'])) }}" class="don-filter-btn {{ request('time','all')=='all'?'active':'' }}">Tất cả thời gian</a>
            <a href="{{ route('orders.index', array_merge(request()->all(), ['time'=>'month'])) }}" class="don-filter-btn {{ request('time')=='month'?'active':'' }}">Tháng này</a>
            <a href="{{ route('orders.index', array_merge(request()->all(), ['time'=>'quarter'])) }}" class="don-filter-btn {{ request('time')=='quarter'?'active':'' }}">Quý này</a>
            <a href="{{ route('orders.index', array_merge(request()->all(), ['time'=>'year'])) }}" class="don-filter-btn {{ request('time')=='year'?'active':'' }}">Năm nay</a>
            <a id="btn-custom-time" onclick="toggleCustomDate()" class="don-filter-btn {{ request('time')=='custom'?'active':'' }}">Tùy chỉnh</a>
        </div>
        <div id="custom-date-box" style="display: {{ request('time')=='custom'?'flex':'none' }}; align-items: center; gap: 8px; background: #fff; padding: 0 15px; border-radius: 6px; border: 1px solid #cbd5e1; height: 35px;">
            <input type="date" id="date_start" value="{{ request('date_start') }}" style="border:none; outline:none; font-size:13px; color:#475569;">
            <span style="color:#94a3b8;">-</span>
            <input type="date" id="date_end" value="{{ request('date_end') }}" style="border:none; outline:none; font-size:13px; color:#475569;">
            <button onclick="applyCustomDate()" style="background:#0070D2; color:#fff; border:none; border-radius:50%; width:24px; height:24px; cursor:pointer;"><i class="fas fa-arrow-right" style="font-size:10px;"></i></button>
        </div>
    </div>

    <!-- Status Tabs -->
    <div style="display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; overflow-x: auto;">
        <a href="{{ route('orders.index', array_merge(request()->all(), ['status'=>'all'])) }}" class="prem-tab {{ request('status','all')=='all'?'active':'' }}">Tất cả <span class="badge">{{ $counts['all'] ?? 0 }}</span></a>
        <a href="{{ route('orders.index', array_merge(request()->all(), ['status'=>'Chờ xác nhận'])) }}" class="prem-tab {{ request('status')=='Chờ xác nhận'?'active':'' }}">Chờ xác nhận <span class="badge">{{ $counts['Chờ xác nhận'] ?? 0 }}</span></a>
        <a href="{{ route('orders.index', array_merge(request()->all(), ['status'=>'Đang xử lý'])) }}" class="prem-tab {{ request('status')=='Đang xử lý'?'active':'' }}">Đang xử lý <span class="badge">{{ $counts['Đang xử lý'] ?? 0 }}</span></a>
        <a href="{{ route('orders.index', array_merge(request()->all(), ['status'=>'Đang vận chuyển'])) }}" class="prem-tab {{ request('status')=='Đang vận chuyển'?'active':'' }}">Đang vận chuyển <span class="badge">{{ $counts['Đang vận chuyển'] ?? 0 }}</span></a>
        <a href="{{ route('orders.index', array_merge(request()->all(), ['status'=>'Hoàn thành'])) }}" class="prem-tab {{ request('status')=='Hoàn thành'?'active':'' }}">Hoàn thành <span class="badge">{{ $counts['Hoàn thành'] ?? 0 }}</span></a>
        <a href="{{ route('orders.index', array_merge(request()->all(), ['status'=>'Đã hủy'])) }}" class="prem-tab {{ request('status')=='Đã hủy'?'active':'' }}">Đã hủy <span class="badge">{{ $counts['Đã hủy'] ?? 0 }}</span></a>
    </div>

    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 15px;">
        
        <div style="height: 35px; display: flex; align-items: center; gap: 8px; background: #f8fafc; padding: 0 12px; border-radius: 6px; border: 1px solid #cbd5e1;">
            <i class="fas fa-coins" style="color: #3b82f6;"></i>
            <span style="font-size: 13px; font-weight: 600; color: #475569;">Tỷ giá:</span>
            <input type="text" id="ty-gia-val" value="{{ number_format($ty_gia ?? 25000) }}" style="width: 85px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 10px; font-size: 13px; outline: none; text-align: right; color: #0f172a; font-weight: bold;">
            <button onclick="saveTyGia()" style="background: #0070D2; color: #fff; border: none; width: 26px; height: 26px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i class="fas fa-save" style="font-size: 12px;"></i></button>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; flex: 1; justify-content: flex-end;">
            <div style="height: 35px; position: relative; width: 260px;">
                <form method="GET">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="time" value="{{ request('time') }}">
                    <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm mã đơn, khách hàng..." style="width: 100%; height: 35px; box-sizing: border-box; padding: 0 15px 0 38px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; outline: none;">
                </form>
            </div>
            
            <div style="height: 35px; display: flex; align-items: center; gap: 6px; background: #fff; padding: 0 14px; border-radius: 6px; border: 1px solid #cbd5e1;">
                <span style="font-size: 13px;">Hiển thị:</span>
                <select onchange="window.location.href=this.value" style="border: none; outline: none; background: transparent; font-weight: 600; cursor: pointer; font-size: 13px;">
                    <option value="{{ request()->fullUrlWithQuery(['limit'=>10]) }}" {{ request('limit')==10?'selected':'' }}>10</option>
                    <option value="{{ request()->fullUrlWithQuery(['limit'=>20]) }}" {{ request('limit')==20?'selected':'' }}>20</option>
                    <option value="{{ request()->fullUrlWithQuery(['limit'=>50]) }}" {{ request('limit')==50?'selected':'' }}>50</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="legacy-table-container" style="overflow-x: auto;">
        <table class="legacy-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">STT</th>
                    <th style="width: 100px; text-align: center;">Ngày tạo</th>
                    <th style="text-align: center;">Mã Đơn</th>
                    <th style="width: 90px; text-align: center;">Mã KH</th>
                    <th>Tên Khách Hàng</th>
                    <th style="text-align: center;">MST</th>
                    <th style="text-align: center;">Khu Vực</th>
                    <th style="text-align: center;">Trạng thái</th>
                    <th>Ghi chú</th>
                    <th style="width: 80px; text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $idx => $order)
                <tr>
                    <td style="padding: 14px 15px; text-align: center;">{{ $orders->firstItem() + $idx }}</td>
                    <td style="padding: 14px 15px; text-align: center;">{{ $order->order_date ? $order->order_date->format('d/m/Y') : '---' }}</td>
                    <td style="padding: 14px 15px; text-align: center; font-weight: 700; color: var(--primary);"><a href="{{ route('orders.show', $order->id) }}" style="font-weight: 800; color: #0070D2; text-decoration: none;">{{ $order->cto_code }}</a></td>
                    <td style="padding: 14px 15px; text-align: center; font-weight: 800; color: #0070D2;">{{ $order->customer->ma_kh ?? '---' }}</td>
                    <td style="padding: 14px 15px; font-weight: 600;">{{ $order->customer->ten_cty ?? '---' }}</td>
                    <td style="padding: 14px 15px; text-align: center;">{{ $order->customer->ma_so_thue ?? '---' }}</td>
                    <td style="padding: 14px 15px; text-align: center;">
                        @if($order->customer?->khu_vuc == 'Miền Bắc') <span class="badge-region mien-bac">Miền Bắc</span>
                        @elseif($order->customer?->khu_vuc == 'Miền Trung') <span class="badge-region mien-trung">Miền Trung</span>
                        @elseif($order->customer?->khu_vuc == 'Miền Nam') <span class="badge-region mien-nam">Miền Nam</span>
                        @endif
                    </td>
                    <td style="padding: 14px 15px; text-align: center;">
                        <span class="badge-status {{ Str::slug($order->trang_thai) }}">
                            {{ $order->trang_thai }}
                        </span>
                    </td>
                    <td style="padding: 14px 15px; font-style: italic; color: #94a3b8; font-size: 12px;">{{ $order->ghi_chu ?: '---' }}</td>
                    <td style="padding: 14px 15px; text-align: center; white-space: nowrap;">
                        <a href="{{ route('orders.show', $order->id) }}" class="action-btn btn-view-pro" title="Xem chi tiết"><i class="fas fa-eye"></i></a>
                        <button onclick="editOrder({{ $order->id }})" class="action-btn btn-edit-pro" title="Sửa thông tin"><i class="fas fa-edit"></i></button>
                        @if(auth()->user()->isAdmin())
                        <button onclick="deleteOrder({{ $order->id }})" class="action-btn btn-del-pro" title="Xóa đơn"><i class="fas fa-trash-alt"></i></button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" style="padding: 40px; text-align: center; color: #94a3b8;">Không có đơn hàng nào khớp điều kiện lọc.</td></tr>
                @endforelse
            </tbody>
        </table>
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

    .modal-pro-label { font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 8px; display: block; }
    .modal-pro-input { width: 100%; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 10px 14px; font-size: 14px; outline: none; background: #f8fafc; box-sizing: border-box; }
    .modal-pro-input:focus { border-color: #3b82f6; background: #fff; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15); }

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
        document.getElementById('order_ma_don').value = 'CTO-' + Date.now().toString().slice(-6);
        document.getElementById('order_ghi_chu').value = '';
        document.getElementById('order_nguoi_ban').value = '';
        document.getElementById('order_sdt_ban').value = '';
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

    function selectKH(c) {
        document.getElementById('order_kh_id').value = c.id;
        searchInp.value = c.ten_cty;
        dropdown.style.display = 'none';
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
