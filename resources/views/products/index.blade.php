@extends('layouts.app')
@section('title', 'Tồn Kho')
@section('page-title', 'Quản Lý Tồn Kho')
@section('page-subtitle', 'Báo cáo số lượng hàng hóa Nhập - Xuất - Tồn thực tế.')

@section('content')
<div class="card" style="padding: 24px;">
    
    <div class="page-header-row" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 2.5px solid #cbd5e1; margin-bottom: 25px;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: #eef2ff; border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.15); flex-shrink: 0;">
                <i class="fas fa-boxes" style="font-size: 24px; color: #002B6B;"></i>
            </div>
            <div>
                <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; letter-spacing: -0.5px;text-transform: uppercase;">Quản Lý Tồn Kho</h2>
                <p style="margin: 0; color: #64748b; font-size: 13px;text-transform: uppercase;text-transform: uppercase;">Theo dõi biến động và số lượng hàng hóa.</p>
            </div>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="location.reload()" class="ui-btn ui-btn-outline" style="border-radius: 6px;"><i class="fas fa-sync-alt"></i> Làm mới</button>
            @if(auth()->user()->canDo('tonkho', 'edit') || auth()->user()->isAdmin())
            <button onclick="openModalNhap()" class="ui-btn ui-btn-primary" style="background:#002B6B; border-radius: 6px; box-shadow: 0 4px 12px rgba(16,185,129,0.25);"><i class="fas fa-plus"></i> Nhập hàng</button>
            @endif
        </div>
    </div>

    <div id="tonkho-tabs" style="display: flex; gap: 20px; border-bottom: 1px solid #cbd5e1; margin-bottom: 20px;">
        <a href="{{ route('products.index', ['tab'=>'tonkho']) }}" class="prem-tab {{ $tab=='tonkho'?'active':'' }}">Báo Cáo Tồn Kho</a>
        <a href="{{ route('products.index', ['tab'=>'lichsu']) }}" class="prem-tab {{ $tab=='lichsu'?'active':'' }}">Lịch Sử Nhập Hàng</a>
    </div>

    @if($tab == 'tonkho')
    <div id="khu-vuc-baocao">
        <div class="filter-row" style="margin-bottom: 20px;">
            <div style="height: 35px; display: flex; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 3px;">
                <button class="don-filter-btn active">Tất cả</button>
                <button class="don-filter-btn">Tuần này</button>
                <button class="don-filter-btn">Tháng này</button>
            </div>
            
            <div style="height: 35px; position: relative; flex: 1; min-width: 250px; max-width: 500px;">
                <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
                <input type="text" id="inp-search-tonkho" placeholder="Tìm mã hoặc tên Sản phẩm..." onkeyup="filterTK()" style="width: 100%; height: 100%; box-sizing: border-box; padding: 0 16px 0 36px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; outline: none;">
            </div>

            <div style="height: 35px; display: flex; align-items: center; gap: 6px; background: #fff; padding: 0 14px; border-radius: 6px; border: 1px solid #cbd5e1;">
                <i class="fas fa-sort-amount-down" style="font-size: 13px;"></i>
                <select id="tk-sort" onchange="sortTK()" style="border: none; outline: none; background: transparent; font-weight: 600; cursor: pointer; font-size: 13px;">
                    <option value="newest">Mới cập nhật</option>
                    <option value="ton_desc">Tồn kho Cao -> Thấp</option>
                    <option value="ton_asc">Tồn kho Thấp -> Cao</option>
                </select>
            </div>
        </div>

        <div class="legacy-table-container" style="border-left: none; border-right: none; box-shadow: none;">
            <table class="legacy-table" id="table-tk">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: center;">STT</th>
                        <th style="width: 15%; text-align: center;">MÃ HÀNG</th>
                        <th style="width: 40%; text-align: left;">TÊN SẢN PHẨM</th>
                        <th style="width: 11%; text-align: right;">TỔNG NHẬP</th>
                        <th style="width: 11%; text-align: right;">TỔNG XUẤT</th>
                        <th style="width: 10%; text-align: center;">TỒN KHO</th>
                        <th style="width: 8%; text-align: center;">ĐVT</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sumTon = 0; @endphp
                    @foreach($inventoryReport as $idx => $tk)
                    @php $sumTon += $tk['con_lai']; @endphp
                    <tr class="row-tk">
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td style="text-align: center;"><b style="color:var(--primary)">{{ $tk['ma_hang'] }}</b></td>
                        <td style="font-weight: 600;">{{ $tk['ten_hang'] }}</td>
                        <td style="text-align: right; color: #3b82f6;">{{ rtrim(rtrim(number_format($tk['tong_nhap'], 3, ',', '.'), '0'), ',') }}</td>
                        <td style="text-align: right; color: #f59e0b;">{{ rtrim(rtrim(number_format($tk['tong_xuat'], 3, ',', '.'), '0'), ',') }}</td>
                        <td style="text-align: center;">
                            <b style="color:{{ $tk['con_lai'] < 0 ? '#ef4444' : '#10b981' }}; font-size: 15px;">{{ rtrim(rtrim(number_format($tk['con_lai'], 3, ',', '.'), '0'), ',') }}</b>
                        </td>
                        <td style="text-align: center;">{{ $tk['don_vi_tinh'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="background: #f8fafc; font-weight: 800;">
                    <tr>
                        <td colspan="5" style="text-align: right; padding: 14px;">TỔNG TỒN HIỆN TẠI:</td>
                        <td style="text-align: center; color: #047857; font-size: 16px;">{{ rtrim(rtrim(number_format($sumTon, 3, ',', '.'), '0'), ',') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 25px;">
            <div style="color: #64748b; font-size: 14px;">Đang hiển thị {{ $inventoryReport->firstItem() ?? 0 }} - {{ $inventoryReport->lastItem() ?? 0 }} trong tổng số {{ $inventoryReport->total() }} bản ghi</div>
            <div style="display: flex; align-items: center; gap: 20px;">
                <div style="display: flex; align-items: center; gap: 8px; color: #64748b; font-size: 13px;">
                    <span>Hiển thị:</span>
                    <select onchange="window.location.href=this.value" style="border: none; outline: none; background: transparent; font-weight: 700; cursor: pointer; color: #0f172a; font-size: 14px;">
                        @foreach([10, 15, 20, 50, 100] as $size)
                            <option value="{{ request()->fullUrlWithQuery(['limit' => $size]) }}" {{ request('limit', 15) == $size ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                    <span>mục</span>
                </div>
                <div>{{ $inventoryReport->links('pagination::bootstrap-4') }}</div>
            </div>
        </div>
    </div>
    @else
    <div id="khu-vuc-lichsu">
        {{-- History Table --}}
        <div class="legacy-table-container" style="border-left: none; border-right: none; box-shadow: none;">
            <table class="legacy-table">
                <thead>
                    <tr>
                        <th style="text-align: center; width: 50px;">STT</th>
                        <th style="text-align: center; width: 120px;">NGÀY NHẬP</th>
                        <th style="width: 150px; text-align: center;">MÃ HÀNG</th>
                        <th>THÔNG TIN SẢN PHẨM</th>
                        <th style="width: 120px; text-align: right;">SỐ LƯỢNG</th>
                        <th style="width: 100px; text-align: center;">ĐƠN VỊ</th>
                        <th style="width: 100px; text-align: center;">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lichSuNhap as $idx => $ls)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td style="text-align: center;">{{ $ls->nhap_date->format('d/m/Y') }}</td>
                        <td style="text-align: center;"><b style="color:var(--primary)">{{ $ls->ma_hang }}</b></td>
                        <td>
                            <div style="font-weight: 600;">{{ $ls->ten_hang }}</div>
                            <div style="font-size: 11px; color: #94a3b8;">{{ $ls->mo_ta ?: '---' }}</div>
                        </td>
                        <td style="text-align: right; color: #10b981; font-weight: 700;">{{ rtrim(rtrim(number_format($ls->so_luong_nhap, 3, ',', '.'), '0'), ',') }}</td>
                        <td style="text-align: center;">{{ $ls->don_vi_tinh }}</td>
                        <td style="text-align: center;">
                            <button onclick="editLS({{ $ls->id }})" class="action-btn btn-edit-pro"><i class="fas fa-edit"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 25px;">
            <div style="color: #64748b; font-size: 14px;">Đang hiển thị {{ $lichSuNhap->firstItem() ?? 0 }} - {{ $lichSuNhap->lastItem() ?? 0 }} trong tổng số {{ $lichSuNhap->total() }} bản ghi nhập hàng</div>
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
                <div>{{ $lichSuNhap->appends(request()->all())->links('pagination::bootstrap-4') }}</div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- MODAL NHẬP --}}
<div id="modal-nhap" class="modal-overlay">
    <div class="modal-box" style="max-width: 600px;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 20px;">
            <h3 style="font-weight: 800; font-size: 20px;"><i class="fas fa-plus-circle" style="color: var(--success)"></i> Khai Báo Nhập Hàng</h3>
            <i class="fas fa-times" style="cursor: pointer;" onclick="closeModal('modal-nhap')"></i>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div style="grid-column: span 2;">
                <label class="modal-pro-label">Tên sản phẩm <span>*</span></label>
                <input type="text" id="p_name" class="modal-pro-input">
            </div>
            <div>
                <label class="modal-pro-label">Mã hàng (Để trống tự tạo)</label>
                <input type="text" id="p_code" class="modal-pro-input" placeholder="Mã hàng...">
            </div>
            <div>
                <label class="modal-pro-label">Đơn vị tính</label>
                <input type="text" id="p_unit" class="modal-pro-input" placeholder="Tấm, Kg...">
            </div>
            <div>
                <label class="modal-pro-label">Số lượng nhập <span>*</span></label>
                <input type="number" id="p_qty" step="0.001" class="modal-pro-input" style="font-weight: 700; color: var(--success);">
            </div>
            <div>
                <label class="modal-pro-label">Ngày nhập</label>
                <input type="date" id="p_date" class="modal-pro-input" value="{{ date('Y-m-d') }}">
            </div>
            <div style="grid-column: span 2;">
                <label class="modal-pro-label">Mô tả / Ghi chú</label>
                <textarea id="p_note" class="modal-pro-input" style="height: 80px;"></textarea>
            </div>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px;">
            <button class="ui-btn ui-btn-outline" onclick="closeModal('modal-nhap')">Hủy</button>
            <button class="ui-btn ui-btn-primary" onclick="submitNhap()">Ghi Nhận Nhập Kho</button>
        </div>
    </div>
</div>

<style>
    .prem-tab { padding: 12px 15px; color: #64748b; font-weight: 600; font-size: 14px; text-decoration: none; border-bottom: 3px solid transparent; transition: 0.3s; }
    .prem-tab:hover { color: #002B6B; }
    .prem-tab.active { color: #002B6B; border-bottom-color: #002B6B; }

    .don-filter-btn { padding: 6px 14px; border-radius: 6px; font-size: 12.5px; border: none; font-weight: 600; cursor: pointer; background: transparent; color: #64748b; transition: 0.2s; font-family: inherit; }
    .don-filter-btn.active { background: #002B6B; color: #fff; }

    .action-btn { width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; border: none; cursor: pointer; transition: 0.2s; }
    .btn-edit-pro { background: #eff6ff; color: #3b82f6; }
    .btn-edit-pro:hover { background: #3b82f6; color: #fff; }

    .modal-pro-label { font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 8px; display: block; }
    .modal-pro-label span { color: #ef4444; }
    .modal-pro-input { width: 100%; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 10px 14px; font-size: 14px; outline: none; background: #f8fafc; box-sizing: border-box; font-family: inherit; }
    .modal-pro-input:focus { border-color: #3b82f6; background: #fff; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15); }

    /* Responsive adjustments */
    .filter-row { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    .filter-group { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    
    @media (max-width: 1400px) {
        .legacy-table-container { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .legacy-table { min-width: 1000px; }
    }

    @media (max-width: 1200px) {
        .filter-row { flex-direction: column; align-items: stretch; }
        .filter-group { justify-content: flex-start; }
    }

    @media (max-width: 768px) {
        .card { padding: 15px !important; }
        .page-header-row { flex-direction: column; align-items: flex-start !important; gap: 15px; }
    }
</style>
@endsection

@push('scripts')
<script>
    function filterTK() {
        const q = document.getElementById('inp-search-tonkho').value.toLowerCase();
        const rows = document.querySelectorAll('.row-tk');
        rows.forEach(r => {
            const txt = r.innerText.toLowerCase();
            r.style.display = txt.includes(q) ? '' : 'none';
        });
    }

    function openModalNhap() {
        document.getElementById('p_name').value = '';
        document.getElementById('p_qty').value = '';
        openModal('modal-nhap');
    }

    async function submitNhap() {
        const data = {
            ten_hang: document.getElementById('p_name').value,
            ma_hang: document.getElementById('p_code').value,
            so_luong_nhap: document.getElementById('p_qty').value,
            don_vi_tinh: document.getElementById('p_unit').value,
            nhap_date: document.getElementById('p_date').value,
            mo_ta: document.getElementById('p_note').value,
            is_new: true
        };
        const res = await apiPost('{{ route("products.store") }}', data);
        if (res.success) { 
            await showToast(res.message); 
            location.reload();
        }
        else { alert(res.message); }
    }
</script>
@endpush
