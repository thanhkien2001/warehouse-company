@extends('layouts.app')
@section('title', 'Phiếu Giao Hàng')
@section('page-title', 'Phiếu Giao Hàng')
@section('page-subtitle', 'Quản lý, thêm mới, in ấn và theo dõi tiến độ phiếu giao hàng.')

@section('content')
<div class="card" style="padding: 24px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 2.5px solid #cbd5e1; margin-bottom: 25px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: #f5f3ff; border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(139, 92, 246, 0.15); flex-shrink: 0;">
                <i class="fas fa-file-export" style="font-size: 24px; color: #8b5cf6;"></i>
            </div>
            <div>
                <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; letter-spacing: -0.5px;">Phiếu Giao Hàng</h2>
                <p style="margin: 0; color: #64748b; font-size: 13.5px;">Quản lý và xuất kho hàng hóa.</p>
            </div>
        </div>
        @if(auth()->user()->canDo('phieugiao', 'edit') || auth()->user()->isAdmin())
        <button onclick="openCreateDNModal()" style="background: #0070D2; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
            <i class="fas fa-plus"></i> Tạo Phiếu Giao Hàng
        </button>
        @endif
    </div>

    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 15px;">
        <div style="display: flex; gap: 10px; align-items: center;">
            <div style="display: flex; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 3px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <a href="{{ route('deliveries.index', ['filter'=>'all']) }}" class="dn-filter-btn {{ $filter=='all'?'active':'' }}">Tất cả</a>
                <a href="{{ route('deliveries.index', ['filter'=>'7days']) }}" class="dn-filter-btn {{ $filter=='7days'?'active':'' }}">7 ngày qua</a>
                <a href="{{ route('deliveries.index', ['filter'=>'month']) }}" class="dn-filter-btn {{ $filter=='month'?'active':'' }}">Tháng này</a>
            </div>
            
            <div style="display: flex; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 7px 10px; gap: 8px; align-items: center;">
                <input type="date" id="date_start" value="{{ request('date_start') }}" style="border:none; outline:none; font-size:13px; color:#475569;">
                <span style="color:#94a3b8;">-</span>
                <input type="date" id="date_end" value="{{ request('date_end') }}" style="border:none; outline:none; font-size:13px; color:#475569;">
                <button onclick="applyCustomDate()" style="background:#0070D2; color:#fff; border:none; border-radius:50%; width:24px; height:24px; cursor:pointer;"><i class="fas fa-arrow-right" style="font-size:10px;"></i></button>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; flex: 1; justify-content: flex-end;">
            <div style="height: 35px; position: relative; width: 260px;">
                <form method="GET">
                    <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nhập mã Phiếu, Đơn hàng..." style="width: 100%; height: 35px; box-sizing: border-box; padding: 0 15px 0 38px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; outline: none; transition: 0.3s;">
                </form>
            </div>
            
            <div style="height: 35px; display: flex; align-items: center; gap: 6px; background: #fff; padding: 0 14px; border-radius: 6px; border: 1px solid #cbd5e1;">
                <span style="font-size: 13px;">Hiển thị:</span>
                <select onchange="window.location.href=this.value" style="border: none; outline: none; background: transparent; font-weight: 600; cursor: pointer; color: #0f172a; font-size: 13px;">
                    <option value="{{ request()->fullUrlWithQuery(['limit'=>10]) }}" {{ request('limit')==10?'selected':'' }}>10</option>
                    <option value="{{ request()->fullUrlWithQuery(['limit'=>20]) }}" {{ request('limit')==20?'selected':'' }}>20</option>
                    <option value="{{ request()->fullUrlWithQuery(['limit'=>50]) }}" {{ request('limit')==50?'selected':'' }}>50</option>
                </select>
            </div>
        </div>
    </div>

    <div class="legacy-table-container" style="overflow-x: auto;">
        <table class="legacy-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">STT</th>
                    <th style="width: 8%; text-align: center;">NGÀY TẠO</th>
                    <th style="width: 15%; text-align: center;">MÃ PHIẾU</th>
                    <th style="width: 15%; text-align: center;">MÃ ĐƠN HÀNG</th>
                    <th style="width: 30%;">THÔNG TIN KHÁCH HÀNG</th>
                    <th style="width: 15%; text-align: center;">TRẠNG THÁI</th>
                    <th style="width: 12%; text-align: center;">THAO TÁC</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveries as $idx => $dn)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 14px 15px; text-align: center;">{{ $deliveries->firstItem() + $idx }}</td>
                    <td style="padding: 14px 15px; text-align: center; white-space: nowrap;">{{ $dn->delivery_date ? $dn->delivery_date->format('d/m/Y') : '---' }}</td>
                    <td style="padding: 14px 15px; text-align: center; font-weight: 800; color: #0070D2; white-space: nowrap;">{{ $dn->dn_code }}</td>
                    <td style="padding: 14px 15px; text-align: center; font-weight: 700; color: #2563eb; white-space: nowrap;">{{ $dn->cto_code }}</td>
                    <td style="padding: 14px 15px;">
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
                    <td style="padding: 14px 15px; text-align: center; white-space: nowrap;">
                        <a href="{{ route('deliveries.show', $dn->id) }}" class="action-btn btn-view-pro" title="Xem chi tiết"><i class="fas fa-eye"></i></a>
                        @if(auth()->user()->isAdmin())
                        <button onclick="deleteDN({{ $dn->id }}, '{{ $dn->dn_code }}')" class="action-btn btn-del-pro"><i class="fas fa-trash-alt"></i></button>
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
            <h3 style="margin: 0; color: #0f172a; font-size: 20px; font-weight: 800;"><i class="fas fa-truck" style="color: #8b5cf6;"></i> Tạo Phiếu Giao Hàng</h3>
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
                    <label class="modal-pro-label">Mã Phiếu (Tự động)</label>
                    <input type="text" id="dn_code_preview" class="modal-pro-input" readonly style="background:#f8fafc; color:#10b981; font-weight:800; text-align:center;">
                </div>
                <div>
                    <label class="modal-pro-label">Hạn thanh toán (Ngày)</label>
                    <input type="number" id="dn_han_tt" class="modal-pro-input" value="7">
                </div>
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

    .modal-pro-label { font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 8px; display: block; }
    .modal-pro-input { width: 100%; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 12px 14px; font-size: 14px; outline: none; background: #fafafa; box-sizing: border-box; }
</style>
@endsection

@push('scripts')
<script>
    const availableOrders = @json($availableOrders ?? []);

    function openCreateDNModal() {
        document.getElementById('dn_cto_code').value = '';
        document.getElementById('dn_search_cto').value = '';
        document.getElementById('dn_code_preview').value = 'DN-' + Date.now().toString().slice(-6);
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
            han_thanh_toan: document.getElementById('dn_han_tt').value
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
