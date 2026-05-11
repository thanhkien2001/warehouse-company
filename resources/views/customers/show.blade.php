@extends('layouts.app')
@section('title', 'Chi tiết Khách Hàng')
@section('page-title', 'Hồ sơ Khách Hàng')
@section('content')
<div style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); padding: 25px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 25px;">
    <div style="margin-bottom: 25px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 15px;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="background: #e0e7ff; color: #002B6B; width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fas fa-user-tie"></i>
            </div>
            <div>
                <h2 id="ctkh-ten" style="margin: 0; font-size: 20px; color: #000000; font-weight: 800;">{{ $customer->ten_cty }}</h2>
                <div style="color: #64748b; font-weight: 700; font-size: 13px; margin-top: 4px;">MÃ KHÁCH HÀNG: <span id="ctkh-ma" style="color: #002B6B;">{{ $customer->ma_kh }}</span></div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 13.5px; color: #000000;">
        <div>
            <p style="margin: 0 0 12px 0;"><span style="font-weight: 600; display: inline-block; width: 120px;">Mã số thuế:</span> <b id="ctkh-mst" style="color: #000000; font-size: 14px;">{{ $customer->ma_so_thue }}</b></p>
            <p style="margin: 0 0 12px 0;"><span style="font-weight: 600; display: inline-block; width: 120px;">Email:</span> <span id="ctkh-email">{{ $customer->email ?: '---' }}</span></p>
            <p style="margin: 0 0 12px 0;"><span style="font-weight: 600; display: inline-block; width: 120px;">Địa chỉ xuất HĐ:</span> <span id="ctkh-diachi">{{ $customer->dia_chi ?: '---' }}</span></p>
            <p style="margin: 0 0 12px 0;">
                <span style="font-weight: 600; display: inline-block; width: 120px;">Trạng thái:</span> 
                @if($customer->tinh_trang == 'active')
                    <span style="background: #eefdf3; color: #27AE60; padding: 4px 12px; border-radius: 6px; font-weight: 800; font-size: 12px; border: 1px solid #27AE60;">
                        <i class="fas fa-check-circle"></i> ĐANG HOẠT ĐỘNG
                    </span>
                @else
                    <span style="background: #fff5f5; color: #E74C3C; padding: 4px 12px; border-radius: 6px; font-weight: 800; font-size: 12px; border: 1px solid #E74C3C;">
                        <i class="fas fa-times-circle"></i> NGƯNG GIAO DỊCH
                    </span>
                @endif
            </p>
        </div>
        <div>
            <p style="margin: 0 0 12px 0;"><span style="font-weight: 600; display: inline-block; width: 130px;">Người liên hệ:</span> <b id="ctkh-nguoilienhe" style="color: #000000;">{{ $customer->nguoi_lien_he ?: '---' }}</b></p>
            <p style="margin: 0 0 12px 0;"><span style="font-weight: 600; display: inline-block; width: 130px;">Số điện thoại:</span> <b id="ctkh-sdt" style="color: #000000; font-size: 14px;">{{ $customer->sdt ?: '---' }}</b></p>
            <p style="margin: 0 0 12px 0;"><span style="font-weight: 600; display: inline-block; width: 130px;">SĐT nhận hàng:</span> <span id="ctkh-sdtnhan">{{ $customer->sdt_nhan ?: '---' }}</span></p>
            <p style="margin: 0 0 12px 0;"><span style="font-weight: 600; display: inline-block; width: 130px;">Địa chỉ giao hàng:</span> <span id="ctkh-diachinhan">{{ $customer->dia_chi_nhan ?: '---' }}</span></p>
        </div>
    </div>
</div>

<div style="background: #fff; padding: 25px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 25px;">
    <style>
        .purchase-history-tabs { display: flex; gap: 8px; align-items: center; }
        .ph-tab { padding: 8px 16px; border-radius: 50px; font-size: 13.5px; font-weight: 600; cursor: pointer; text-decoration: none; color: #64748b; background: #f1f5f9; display: flex; align-items: center; gap: 6px; transition: 0.2s; }
        .ph-tab:hover { background: #e2e8f0; color: #0f172a; }
        .ph-tab.active { background: #002B6B; color: #fff; box-shadow: 0 4px 10px rgba(0, 112, 210, 0.2); }
        .ph-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 18px; height: 18px; border-radius: 50%; font-size: 10px; color: #fff; font-weight: 800; padding: 0 4px; }
        .ph-badge.pending { background: #E67E22; }
        .ph-badge.processing { background: #3498DB; }
        .ph-badge.shipping { background: #8E44AD; }
        .ph-badge.canceled { background: #E74C3C; }
        .ph-badge.completed { background: #27AE60; }
        .ph-badge.default { background: #94a3b8; }
        
        /* Date filter style */
        .date-filter-group { display: flex; align-items: center; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden; }
        .date-icon-box { display: flex; align-items: center; padding: 0 10px; background: #fff; border-right: 1px solid #cbd5e1; height: 100%; }
        .date-input-field { border: none; outline: none; padding: 8px; font-size: 13px; width: 130px; color: #475569; }
    </style>

    {{-- Row 1: Tiêu đề + Bộ lọc ngày --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-family: 'Inter', sans-serif;">
        <h3 style="margin: 0; color: #000000; font-size: 18px; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-history" style="color: #f59e0b;"></i> Lịch sử mua hàng
        </h3>
        
        <form method="GET" style="display: flex; gap: 8px; align-items: center;">
            <input type="hidden" name="status" value="{{ request('status', 'all') }}">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="sort" value="{{ request('sort', 'newest') }}">
            
            <div style="display: flex; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden; background: #fff;">
                <div style="display: flex; align-items: center; padding: 0 10px; border-right: 1px solid #cbd5e1;">
                    <i class="far fa-calendar-alt" style="color: #64748b; font-size: 14px;"></i>
                </div>
                <input type="date" name="date_start" value="{{ request('date_start') }}" style="padding: 8px; border: none; font-size: 14px; color: #475569; outline: none; width: 130px;">
                <div style="display: flex; align-items: center; padding: 0 10px; border-left: 1px solid #cbd5e1; border-right: 1px solid #cbd5e1; background: #f8fafc;">
                    <i class="far fa-calendar-alt" style="color: #64748b; font-size: 14px;"></i>
                </div>
                <input type="date" name="date_end" value="{{ request('date_end') }}" style="padding: 8px; border: none; font-size: 14px; color: #475569; outline: none; width: 130px;">
            </div>
            
            <button type="submit" style="background: #002B6B; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                <i class="fas fa-search"></i> Lọc
            </button>
            <a href="{{ route('customers.show', $customer->id) }}" style="background: #E74C3C; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600;">Xóa lọc</a>
        </form>
    </div>

    {{-- Row 2: Tabs trạng thái + Search + Sắp xếp --}}
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 15px; margin-bottom: 20px; font-family: 'Inter', sans-serif;">
        <div class="purchase-history-tabs">
            <a href="{{ request()->fullUrlWithQuery(['status' => 'all']) }}" class="ph-tab {{ request('status', 'all') == 'all' ? 'active' : '' }}">
                Tất cả <span style="opacity: 0.7; font-weight: normal;">({{ $allCount }})</span>
            </a>
            @foreach($statusCounts as $st => $count)
                @php
                    $badgeClass = 'default';
                    if($st == 'Chờ xác nhận') $badgeClass = 'pending';
                    elseif($st == 'Đang xử lý') $badgeClass = 'processing';
                    elseif($st == 'Đang vận chuyển' || $st == 'Đang giao') $badgeClass = 'shipping';
                    elseif($st == 'Đã hủy') $badgeClass = 'canceled';
                    elseif($st == 'Hoàn thành' || $st == 'Đã giao xong') $badgeClass = 'completed';
                    elseif($st == 'Chờ giao hàng') $badgeClass = 'pending'; // or different if wanted
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['status' => $st]) }}" class="ph-tab {{ request('status') == $st ? 'active' : '' }}">
                    {{ $st }} <span class="ph-badge {{ $badgeClass }}">{{ $count }}</span>
                </a>
            @endforeach
        </div>

        <div style="display: flex; gap: 10px; align-items: center; flex: 1; max-width: 500px;">
            <div style="position: relative; flex: 1;">
                <form method="GET" style="margin:0;">
                    <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                    <input type="hidden" name="date_start" value="{{ request('date_start') }}">
                    <input type="hidden" name="date_end" value="{{ request('date_end') }}">
                    <input type="hidden" name="sort" value="{{ request('sort', 'newest') }}">
                    <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 13px;"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Gõ mã CTO để tìm..." style="width: 100%; padding: 8px 15px 8px 38px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px; outline: none; background: #f8fafc;">
                </form>
            </div>
            
            <form method="GET" style="margin:0;">
                <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="date_start" value="{{ request('date_start') }}">
                <input type="hidden" name="date_end" value="{{ request('date_end') }}">
                <select name="sort" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px; color: #475569; background: #fff; cursor: pointer; min-width: 130px; outline: none;">
                    <option value="newest" {{ request('sort')=='newest'?'selected':'' }}>Mới nhất</option>
                    <option value="oldest" {{ request('sort')=='oldest'?'selected':'' }}>Cũ nhất</option>
                </select>
            </form>
        </div>
    </div>

    <div class="legacy-table-container" style="overflow-x: auto; background: #fff; font-family: 'Inter', sans-serif;">
        <table class="legacy-table" style="min-width: 800px;">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">STT</th>
                    <th style="width: 12%; text-align: center;">Ngày tạo</th>
                    <th style="width: 13%; text-align: center;">Mã Đơn (CTO)</th>
                    <th style="width: 15%; text-align: center;">SỐ HÓA ĐƠN</th>
                    <th style="width: 40%; text-align: center;">Mô tả đơn hàng</th>
                    <th style="width: 15%; text-align: center;">Trạng thái</th>
                </tr>
            </thead>
            <tbody id="ctkh-ds-donhang">
                @forelse($orders as $key => $order)
                <tr>
                    <td style="padding: 16px 15px;text-align: center;">{{ $loop->iteration }}</td>
                    <td style="padding: 16px 15px;text-align: center;">{{ $order->order_date?->format('d/m/Y') }}</td>
                    <td style="padding: 16px 15px;text-align: center;">
                        <a href="javascript:void(0)" onclick="xemChiTietDon({{ $order->id }}, '{{ $order->cto_code }}')" style="color: #002B6B; text-decoration: none; font-weight: bold;">{{ $order->cto_code }}</a>
                    </td>
                    <td style="padding: 16px 15px;text-align: center; color: #059669; font-weight: bold;">{{ $order->hd_code ?: '' }}</td>
                    <td class="col-left" style="padding: 16px 15px;">{{ $order->ghi_chu ?: '---' }}</td>
                    <td style="padding: 16px 15px; text-align: center;">
                        <span class="badge-status {{ Str::slug($order->trang_thai) }}">
                            {{ $order->trang_thai }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 30px; text-align: center; color: #94a3b8;">Khách hàng này chưa có đơn hàng nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; font-family: 'Inter', sans-serif;">
        <div style="color: #64748b; font-size: 13.5px;">Hiển thị {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} mục (Tổng: {{ $orders->total() }})</div>
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 5px; color: #64748b; font-size: 13px;">
                <span>Hiển thị:</span>
                <select onchange="window.location.href=this.value" style="border: none; outline: none; background: transparent; font-weight: 600; cursor: pointer; color: #000000;">
                    @foreach([5, 10, 15, 20, 50] as $size)
                        <option value="{{ request()->fullUrlWithQuery(['limit' => $size]) }}" {{ request('limit', 10) == $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
                <span>mục</span>
            </div>
            <div style="display: flex; gap: 4px;">
                {{ $orders->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- KHU VỰC CHI TIẾT ĐƠN HÀNG INLINE -->
<div style="clear: both; width: 100%;">
<div id="ctkh-khu-vuc-chitiet-don" style="display: none; font-family: 'Inter', sans-serif; margin-bottom: 20px; background: #fff; padding: 25px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 16px; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px;">
        <h3 style="margin: 0; color: #000000; font-size: 18px; display: flex; align-items: center;">
            <i class="fas fa-box-open" style="color: #10b981; margin-right: 8px;"></i> Chi tiết Đơn: <span id="ctkh-view-ctocode" style="color:#000000; font-weight: bold; margin-left: 5px;">---</span>
        </h3>
        <div style="display: flex; align-items: center; gap: 10px;">
            <div id="ctkh-action-buttons" style="display: flex; gap: 10px;"></div>
            <button onclick="dongXemNhanhDon()" style="background: #fee2e2; color: #ef4444; border: none; padding: 6px 14px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s;"><i class="fas fa-times"></i> Đóng</button>
        </div>
    </div>

    <div class="legacy-table-container" style="overflow-x: auto; background: #fff; font-size: 13px;">
        <table class="legacy-table" style="min-width: 800px;">
            <thead style="background: #f8fafc; color: #000000; font-weight: 700; font-size: 12px; text-transform: uppercase;">
                <tr>
                    <th style="padding: 16px 15px; width: 15%; border-bottom: 2px solid #e2e8f0; text-align: center;">Mã Hàng</th>
                    <th style="padding: 16px 15px; width: 35%; border-bottom: 2px solid #e2e8f0; text-align: center;">Mô tả hàng hóa</th>
                    <th style="padding: 16px 15px; width: 10%; border-bottom: 2px solid #e2e8f0; text-align: center;">Số lượng</th>
                    <th style="padding: 16px 15px; width: 10%; border-bottom: 2px solid #e2e8f0; text-align: center;">ĐVT</th>
                    <th style="padding: 16px 15px; width: 15%; border-bottom: 2px solid #e2e8f0; text-align: center;">Đơn giá</th>
                    <th style="padding: 16px 15px; width: 15%; border-bottom: 2px solid #e2e8f0; text-align: center;">Thành tiền</th>
                </tr>
            </thead>
            <tbody id="ctkh-view-chitiet-body"></tbody>
        </table>
    </div>
    <div style="text-align: right; margin-top: 20px;">
        <span style="color: #475569; font-size: 14px;">Tổng thanh toán: </span>
        <span id="ctkh-view-tongtien" style="color: #000000; font-weight: 800; font-size: 16px;">0</span>
    </div>
</div>
</div>

<script>
function applyDateFilter() {
    const start = document.getElementById('date_start').value;
    const end = document.getElementById('date_end').value;
    if (!start || !end) {
        alert('Vui lòng chọn cả hai ngày để lọc!');
        return;
    }
    window.location.href = `{{ request()->url() }}?filter=custom&date_start=${start}&date_end=${end}`;
}

function xemChiTietDon(orderId, ctoCode) {
    document.getElementById('ctkh-view-ctocode').innerText = ctoCode;
    document.getElementById('ctkh-khu-vuc-chitiet-don').style.display = 'block';
    
    document.getElementById('ctkh-action-buttons').innerHTML = `
        <a href="/don-hang/${orderId}" style="background: #002B6B; color: white; padding: 6px 14px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600;"><i class="fas fa-eye"></i> Xem đơn hàng</a>
    `;

    document.getElementById('ctkh-view-chitiet-body').innerHTML = `<tr><td colspan="6" style="text-align:center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...</td></tr>`;

    fetch(`/don-hang/${orderId}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(res => {
        if(res.success && res.data) {
            let html = '';
            let total = 0;
            if(res.data.items && res.data.items.length > 0) {
                res.data.items.forEach(item => {
                    const lineTotal = (item.so_luong || 0) * (item.don_gia || 0);
                    total += lineTotal;
                    html += `
                    <tr>
                        <td class="col-center" style="padding: 12px 15px; border-bottom: 1px solid #f1f5f9; color: #002B6B; font-weight: 600;">${item.ma_hang || '---'}</td>
                        <td class="col-left" style="padding: 12px 15px; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #0f172a;">${item.ten_hang || '---'}</td>
                        <td class="col-right" style="padding: 12px 15px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #0ea5e9;">${formatQuantity(item.so_luong || 0)}</td>
                        <td class="col-center" style="padding: 12px 15px; border-bottom: 1px solid #f1f5f9;">${item.don_vi_tinh || '---'}</td>
                        <td class="col-right" style="padding: 12px 15px; border-bottom: 1px solid #f1f5f9; font-weight: 600;">${formatMoney(item.don_gia || 0)}</td>
                        <td class="col-right" style="padding: 12px 15px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #ef4444;">${formatMoney(lineTotal)}</td>
                    </tr>
                    `;
                });
            } else {
                html = `<tr><td colspan="6" style="text-align:center; padding: 20px; color: #94a3b8;">Không có sản phẩm nào trong đơn hàng này.</td></tr>`;
            }
            document.getElementById('ctkh-view-chitiet-body').innerHTML = html;
            document.getElementById('ctkh-view-tongtien').innerText = formatMoney(total) + ' VNĐ';
        } else {
            document.getElementById('ctkh-view-chitiet-body').innerHTML = `<tr><td colspan="6" style="text-align:center; color: red;">Lỗi tải dữ liệu.</td></tr>`;
        }
    })
    .catch(err => {
        document.getElementById('ctkh-view-chitiet-body').innerHTML = `<tr><td colspan="6" style="text-align:center; color: red;">Lỗi kết nối API.</td></tr>`;
    });
}

function dongXemNhanhDon() {
    document.getElementById('ctkh-khu-vuc-chitiet-don').style.display = 'none';
}
</script>
@endsection
