@extends('layouts.app')
@section('title', 'Lịch Sử Thanh Toán')
@section('page-title', 'Lịch Sử Thanh Toán')
@section('page-subtitle', 'Quản lý và tra cứu toàn bộ giao dịch thanh toán công nợ.')

@section('content')
@push('styles')
<style>
    /* Filter row 2 */
    .ord-filter-card { padding: 14px; border-bottom: 1.5px solid #f1f5f9; margin-bottom: 15px; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; }
    .ord-filter-grid { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; }
    .ord-filter-item { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 120px; }
    .ord-filter-item label { font-size: 13px; font-weight: 700; color: #1e293b; }
    .ord-filter-input { height: 36px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0 12px; font-size: 13px; outline: none; width: 100%; box-sizing: border-box; color: #1e293b; background: #fff; }
    .ord-filter-input:focus { border-color: #002B6B; box-shadow: 0 0 0 3px rgba(0,112,210,0.1); }
    .ord-search-wrapper { position: relative; }
    .ord-search-wrapper i { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; pointer-events: none; }
    .ord-search-wrapper .ord-filter-input { padding-right: 36px; }
    .ord-btn-search { height: 36px; padding: 0 16px; background: #002B6B; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px; white-space: nowrap; }
    .ord-btn-search:hover { background: #005bb5; }
    .ord-btn-clear { height: 36px; padding: 0 16px; background: #fff; color: #ef4444; border: 1px solid #e2e8f0; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px; text-decoration: none; white-space: nowrap; }
    .ord-btn-clear:hover { background: #fef2f2; border-color: #ef4444; }
</style>
@endpush
<div class="card" style="padding: 24px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 16px; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 54px; height: 54px; background: #ecfdf5; border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.15); flex-shrink: 0;">
                <i class="fas fa-history" style="font-size: 24px; color: #10b981;"></i>
            </div>
            <div>
                <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0;text-transform: uppercase;">Lịch Sử Giao Dịch</h2>
                <p style="margin: 0; color: #64748b; font-size: 13px;text-transform: uppercase;text-transform: uppercase;">Tra cứu nhật ký thu tiền của hệ thống.</p>
            </div>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="location.reload()" class="ui-btn ui-btn-outline"><i class="fas fa-sync-alt"></i> Làm mới</button>
            @if(auth()->user()->canDo('thanhtoan', 'edit'))
            <button onclick="openModalPayment()" class="ui-btn ui-btn-primary" style="background: #002B6B;"><i class="fas fa-plus"></i> Tạo Thanh Toán Mới</button>
            @endif
        </div>
    </div>

    <!-- <div id="tonkho-tabs" style="display: flex; gap: 20px; border-bottom: 1px solid #cbd5e1; margin-bottom: 25px;">
        <a href="{{ route('debt.index') }}" class="prem-tab">Quản Lý Công Nợ</a>
        <a href="{{ route('payments.index') }}" class="prem-tab active">Lịch Sử Thanh Toán</a>
    </div> -->

    <div class="ord-filter-card">
        <form method="GET" action="{{ route('payments.index') }}">
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
                    <select name="customer" class="ord-filter-input">
                        <option value="">Tất cả khách hàng</option>
                        @foreach($allCustomers as $c)
                            <option value="{{ $c->ma_kh }}" {{ request('customer') == $c->ma_kh ? 'selected' : '' }}>
                                [{{ $c->ma_kh }}] {{ $c->ten_cty }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="ord-filter-item" style="flex: 2;">
                    <label>Tìm kiếm</label>
                    <div class="ord-search-wrapper">
                        <input type="text" name="search" class="ord-filter-input" placeholder="Tìm mã TT, mã đơn, khách hàng..." value="{{ request('search') }}">
                        <i class="fas fa-search"></i>
                    </div>
                </div>

                <div class="ord-filter-item" style="flex: none; display:flex; align-items:center; gap:8px;">
                    <label>&nbsp;</label>
                    <div style="display: flex; gap: 8px;">
                        <button type="submit" class="ord-btn-search"><i class="fas fa-search"></i> Tìm kiếm</button>
                        <a href="{{ route('payments.index') }}" class="ord-btn-clear"><i class="fas fa-times"></i> Xóa lọc</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="legacy-table-container">
        <table class="legacy-table">
            <thead>
                <tr>
                    <th style="padding: 16px 10px; text-align: center; width: 3%;">STT</th>
                    <th style="padding: 16px 10px; text-align: center; width: 8%;">NGÀY TẠO</th>
                    <th style="padding: 16px 10px; text-align: center; width: 9%;">MÃ TT</th>
                    <th style="width: 5%; text-align: center;">HÓA ĐƠN</th>
                    <th style="padding: 16px 10px; text-align: center; width: 9%;">MÃ ĐƠN HÀNG</th>
                    <th style="padding: 16px 10px; text-align: center; width: 25%;">THÔNG TIN KHÁCH HÀNG</th>
                    <th style="padding: 16px 10px; text-align: center; width: 8%;">KHU VỰC</th>
                    <th style="padding: 16px 10px; text-align: center; width: 10%;">TỔNG ĐƠN</th>
                    <th style="padding: 16px 10px; text-align: center; width: 10%;">TIỀN TRẢ</th>
                    <th style="padding: 16px 10px; text-align: center; width: 10%;">CÒN LẠI</th>
                    <th style="padding: 16px 10px; text-align: center; width: 8%;">GHI CHÚ</th>
                    <th style="padding: 16px 10px; text-align: center; width: 5%;">HĐ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $idx => $p)
                <tr>
                    <td style="text-align: center;">{{ $payments->firstItem() + $idx }}</td>
                    <td style="text-align: center;">{{ $p->payment_date->format('d/m/Y') }}</td>
                    <td style="text-align: center; font-weight: 700; color: #10b981;">{{ $p->ma_tt }}</td>
                    <td style="text-align: center; font-weight: 700; color: #059669;">{{ $p->order->hd_code ?? '' }}</td>
                    <td style="text-align: center; font-weight: 700; color: #2563eb;">{{ $p->cto_code }}</td>
                    <td style="text-align: left;">
                        <div style="font-weight: 600; text-transform: uppercase;">{{ $p->ten_kh }}</div>
                        <div style="font-size: 11px; color: #94a3b8;">Mã KH: {{ $p->ma_kh }}</div>
                    </td>
                    <td style="text-align: center; white-space: nowrap;">
                        @if($p->khu_vuc)
                            <span class="badge-region {{ Str::slug($p->khu_vuc) }}">{{ $p->khu_vuc }}</span>
                        @else
                            ---
                        @endif
                    </td>
                    
                    <td style="text-align: right;">{{ number_format($p->tong_don) }}</td>
                    <td style="text-align: right; color: #10b981; font-weight: 800;">+{{ number_format($p->so_tien) }}</td>
                    <td style="text-align: right; color: #ef4444; font-weight: 800;">{{ number_format($p->con_lai) }}</td>
                    <td style="font-size: 11px; color: #64748b; text-align: left;">{{ $p->ghi_chu ?: '---' }}</td>
                    <td style="text-align: center;">
                        @if(auth()->user()->canDo('thanhtoan', 'delete'))
                        <i class="fas fa-trash-alt" style="color:#ef4444; cursor:pointer; font-size: 14px;" title="Xóa thanh toán" onclick="deletePayment({{ $p->id }}, '{{ $p->ma_tt }}')"></i>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" style="padding: 40px; text-align: center; color: #94a3b8;">Chưa có lịch sử thanh toán.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
        <div style="color: #64748b; font-size: 13px;">Hiển thị {{ $payments->firstItem() ?? 0 }} - {{ $payments->lastItem() ?? 0 }} mục (Tổng: {{ $payments->total() }})</div>
        <div>{{ $payments->appends(request()->all())->links('pagination::bootstrap-4') }}</div>
    </div>
</div>

{{-- MODAL PAYMENT --}}
<div id="modal-payment" class="modal-overlay">
    <div class="modal-box" style="max-width: 500px; padding: 0;">
        <div style="background: #f8fafc; padding: 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; border-radius: 16px 16px 0 0;">
            <h3 style="margin: 0; color: #0f172a; font-weight: 800; font-size: 18px;"><i class="fas fa-hand-holding-usd" style="color: #10b981;"></i> Nhập Thanh Toán Mới</h3>
            <i class="fas fa-times" style="cursor: pointer; color: #94a3b8; font-size: 20px;" onclick="closeModal('modal-payment')"></i>
        </div>
        <div style="padding: 24px;">
            <label class="modal-pro-label">Chọn Đơn Hàng (CTO) *</label>
            <div style="position: relative;">
                <input type="text" id="tt_search_cto" placeholder="Nhấn vào để tìm đơn hàng..." class="modal-pro-input" autocomplete="off" onfocus="showOrderList()" oninput="filterOrderList()">
                <div id="order-list-dropdown" style="display:none; position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid #cbd5e1; border-radius:8px; max-height:200px; overflow-y:auto; z-index:1001; box-shadow: 0 10px 25px rgba(0,0,0,0.1);"></div>
            </div>
            <input type="hidden" id="tt_cto_code">

            <div id="order-hint" style="display:none; background: #eff6ff; padding: 15px; border-radius: 8px; margin: 15px 0; border: 1px dashed #bfdbfe;">
                <p style="margin: 0 0 5px 0; color: #3b82f6; font-size: 13px;">Khách hàng: <b id="tt_view_kh" style="color: #1e3a8a;">---</b></p>
                <p style="margin: 0; color: #ef4444; font-size: 14px; font-weight: bold;">Cần thu của đơn này: <span id="tt_view_debt">0 VNĐ</span></p>
            </div>

            <label class="modal-pro-label" style="margin-top: 15px;">Ngày thanh toán *</label>
            <input type="date" id="tt_date" class="modal-pro-input" value="{{ date('Y-m-d') }}">

            <label class="modal-pro-label" style="margin-top: 15px;">Số tiền khách trả đợt này (VNĐ) *</label>
            <input type="text" id="tt_amount" class="modal-pro-input" style="font-size: 18px; font-weight: bold; color: #10b981;" oninput="this.value = this.value.replace(/[^0-9]/g,'').replace(/\B(?=(\d{3})+(?!\d))/g, '.')">

            <label class="modal-pro-label" style="margin-top: 15px;">Ghi chú</label>
            <input type="text" id="tt_note" class="modal-pro-input" placeholder="Chuyển khoản, tiền mặt...">

            <button onclick="submitNewPayment()" style="width: 100%; margin-top: 20px; padding: 14px; border-radius: 8px; border: none; background: #10b981; color: #fff; font-weight: 800; font-size: 15px; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 10px rgba(16,185,129,0.2);"><i class="fas fa-check-circle"></i> Xác nhận Đã Thu Tiền</button>
        </div>
    </div>
</div>

<style>
    .prem-tab { padding: 12px 15px; color: #64748b; font-weight: 600; font-size: 14px; text-decoration: none; border-bottom: 3px solid transparent; transition: 0.3s; }
    .prem-tab.active { color: #002B6B; border-bottom-color: #002B6B; }
    .legacy-table-container { border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; margin-bottom: 20px; }
    .legacy-table { width: 100%; border-collapse: collapse; background: #fff; font-size: 13px; }
    .legacy-table th { background: #eff6ff; color: #000; font-weight: 800; padding: 12px 10px; border: 1px solid #e2e8f0; text-transform: uppercase; text-align: center; }
    .legacy-table td { padding: 10px; border: 1px solid #e2e8f0; color: #1e293b; text-align: center; vertical-align: middle; }
    .legacy-table tr:hover { background-color: #f8fafc; }
    .modal-pro-label { font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 8px; display: block; }
    .modal-pro-input { width: 100%; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 12px 14px; font-size: 14px; outline: none; background: #f8fafc; box-sizing: border-box; }

    /* Region Badges */
    .badge-region { padding: 4px 12px !important; border-radius: 20px !important; color: #fff !important; border: none !important; font-size: 11px !important; white-space: nowrap !important; min-width: 85px !important; display: inline-block !important; text-align: center !important; font-weight: 700 !important; }
    .badge-region.mien-bac { color: #3b82f6 !important; background: #eff6ff !important; }
    .badge-region.mien-trung { color: #f97316 !important; background: #fff7ed !important; }
    .badge-region.mien-nam { color: #10b981 !important; background: #ecfdf5 !important; }
</style>
@endsection

@push('scripts')
<script>
    const unpaidOrders = @json($unpaidOrders ?? []);

    function toggleCustomDate() {
        const box = document.getElementById('custom-date-box');
        box.style.display = box.style.display === 'none' ? 'flex' : 'none';
    }

    function openModalPayment() {
        document.getElementById('tt_cto_code').value = '';
        document.getElementById('tt_search_cto').value = '';
        document.getElementById('order-hint').style.display = 'none';
        openModal('modal-payment');
    }

    function showOrderList() {
        filterOrderList();
    }

    function filterOrderList() {
        const q = document.getElementById('tt_search_cto').value.toLowerCase();
        const filtered = unpaidOrders.filter(o => o.ma_don.toLowerCase().includes(q) || (o.customer && o.customer.ten_cty.toLowerCase().includes(q)));
        
        const dd = document.getElementById('order-list-dropdown');
        dd.innerHTML = '';
        if (filtered.length > 0) {
            filtered.slice(0, 10).forEach(o => {
                const item = document.createElement('div');
                item.style.padding = '8px 12px';
                item.style.cursor = 'pointer';
                item.style.fontSize = '12px';
                item.style.borderBottom = '1px solid #f1f5f9';
                item.innerHTML = `<b style="color:#2563eb; font-size:13px;">${o.ma_don}</b> <span style="color:#059669; font-weight:bold;">[${o.hd_code || ''}]</span> - ${o.customer ? o.customer.ten_cty : '---'}`;
                item.onclick = () => selectOrder(o);
                dd.appendChild(item);
            });
            dd.style.display = 'block';
        } else {
            dd.style.display = 'none';
        }
    }

    function selectOrder(o) {
        document.getElementById('tt_cto_code').value = o.ma_don;
        document.getElementById('tt_search_cto').value = o.ma_don;
        document.getElementById('tt_view_kh').innerText = o.customer ? o.customer.ten_cty : '---';
        document.getElementById('tt_view_debt').innerText = o.con_lai.toLocaleString('vi-VN') + ' VNĐ';
        document.getElementById('tt_amount').value = o.con_lai.toLocaleString('vi-VN');
        document.getElementById('order-hint').style.display = 'block';
        document.getElementById('order-list-dropdown').style.display = 'none';
    }

    async function submitNewPayment() {
        const cto = document.getElementById('tt_cto_code').value;
        const amount = document.getElementById('tt_amount').value.replace(/\./g, '');
        const date = document.getElementById('tt_date').value;
        if (!cto || !amount || !date) return alert('Vui lòng chọn đơn hàng, ngày và nhập số tiền!');

        const res = await apiPost('{{ route("payments.store") }}', {
            cto_code: cto,
            so_tien: amount,
            payment_date: date,
            ghi_chu: document.getElementById('tt_note').value
        });
        if (res.success) { 
            await showToast(res.message); 
            location.reload();
        }
        else { alert(res.message); }
    }

    async function deletePayment(id, maTT) {
        showConfirm('Xóa Giao Dịch', `Bạn có chắc muốn xóa bản ghi thanh toán [${maTT}]? Hành động này không thể hoàn tác.`, async () => {
            const res = await apiDelete(`/thanh-toan/${id}`);
            if (res.success) {
                await showToast(res.message);
                location.reload();
            } else {
                alert(res.message || 'Lỗi khi xóa!');
            }
        });
    }

    // Đóng dropdown khi click ngoài
    document.addEventListener('click', function(e) {
        const dd = document.getElementById('order-list-dropdown');
        const input = document.getElementById('tt_search_cto');
        if (dd && !dd.contains(e.target) && e.target !== input) {
            dd.style.display = 'none';
        }
    });
</script>
@endpush
