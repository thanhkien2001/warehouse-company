@extends('layouts.app')

@section('title', 'Nhập kho hàng hóa')

@push('styles')
<style>
    .inbound-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 1600px;
        margin: 0 auto;
        min-height: calc(100vh - 120px);
    }

    .ib-block {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 15px;
        border: 1px solid #e2e8f0;
    }

    .ib-block-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        text-transform: uppercase;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 420px;
        grid-template-rows: repeat(5, 36px);
        gap: 8px 20px;
    }

    .form-group {
        display: flex;
        align-items: center;
        gap: 10px;
        height: 36px;
        align-self: start;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        width: 110px;
        flex-shrink: 0;
    }

    .form-group label span { color: #ef4444; }

    .ib-input {
        padding: 6px 10px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        font-size: 13px;
        color: #000;
        outline: none;
        background: #fff;
        flex: 1;
        height: 34px;
        box-sizing: border-box;
    }

    .ib-input:focus { border-color: var(--primary); }
    .ib-input:read-only { 
        background: #f8fafc; 
        color: #000; 
        pointer-events: none;
    }

    /* DROPZONE */
    .dropzone-container {
        grid-column: 3;
        grid-row: 1 / span 3;
        height: calc(3 * 36px + 2 * 8px);
        border: 1.5px dashed #cbd5e1;
        border-radius: 8px;
        background: #fcfcfc;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 15px;
        text-align: center;
        box-sizing: border-box;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
    }
    .dropzone-container.drag-over { border-color: #0070D2; background: #eff6ff; }
    .dropzone-icon { font-size: 30px; color: #94a3b8; margin-bottom: 8px; }
    .dropzone-text { font-size: 12px; color: #475569; margin-bottom: 10px; }
    .btn-choose-file {
        background: #0070D2;
        color: #fff;
        padding: 6px 15px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        border: none;
    }
    .format-info { font-size: 10px; color: #94a3b8; margin-top: 10px; }

    .attachment-col {
        grid-column: 3;
        grid-row: 4 / span 2;
        height: calc(2 * 36px + 1 * 8px);
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        overflow-y: auto;
        box-sizing: border-box;
    }

    .attachment-table { width: 100%; font-size: 11px; border-collapse: collapse; }
    .attachment-table td { padding: 6px; border-bottom: 1px solid #f1f5f9; }

    /* TABLE ITEM */
    .item-table { width: 100%; min-width: 1300px; border-collapse: collapse; table-layout: fixed; }

    .item-table th {
        background: #EFF6FF;
        padding: 10px 5px;
        font-size: 11px;
        font-weight: 700;
        color: black;
        border: 1px solid #e2e8f0;
        text-align: center;
    }

    .item-table td { padding: 0; border: 1px solid #e2e8f0; }

    .item-table .ib-input {
        border: 1px solid transparent;
        border-radius: 0;
        padding: 8px 6px;
        width: 100%;
        height: 100%;
        background: transparent;
    }
    .item-table .ib-input:focus {
        border-color: var(--primary);
        background: #fff;
        z-index: 10;
        box-shadow: inset 0 0 0 1px var(--primary);
    }
    .item-table select.ib-input { padding-right: 2px; }
    .item-table tr:hover { background: #fcfcfc; }
    .item-table th { position: sticky; top: 0; z-index: 20; }

    /* Autocomplete */
    .prod-autocomplete {
        position: absolute;
        top: 100%;
        left: 0;
        width: 400px;
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 999;
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        display: none;
    }
    .prod-autocomplete .ac-item {
        padding: 8px 10px;
        cursor: pointer;
        font-size: 12px;
        border-bottom: 1px solid #f1f5f9;
    }
    .prod-autocomplete .ac-item:hover { background: #eff6ff; }
    .prod-autocomplete .ac-code { font-weight: 700; color: #0070D2; }

    .btn-add { background: #0070D2; color: #fff; padding: 6px 12px; border-radius: 4px; font-weight: 600; border: none; cursor: pointer; font-size: 12px; }
    .btn-outline-custom { background: #fff; color: #64748b; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 12px; }

    .table-block { flex: 1; display: flex; flex-direction: column; min-height: 0; }
    .table-responsive { flex: 1; overflow-y: auto; overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 4px; min-height: 400px; }
    
    /* PAGINATION BAR */
    .pagination-bar {
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #f1f5f9;
        font-size: 12px;
    }
    .pagination-info { font-size: 12px; color: #64748b; }
    .pagination-right { display: flex; gap: 12px; align-items: center; }
    .pagination-controls { display: flex; gap: 4px; align-items: center; }
    
    .page-btn {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: #fff;
        border-radius: 5px;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        text-decoration: none;
    }
    .page-btn.active {
        background: #0070D2;
        color: #fff;
        border-color: #0070D2;
    }
    .page-btn.disabled { opacity: 0.4; pointer-events: none; }
    .page-btn:hover:not(.active):not(.disabled) { background: #f1f5f9; }

    .pagination-goto {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #64748b;
    }
    .goto-input {
        width: 40px;
        height: 28px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        text-align: center;
        font-size: 12px;
        outline: none;
    }
    .page-size {
        display: flex;
        align-items: center;
    }
    .per-page-select {
        height: 28px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        font-size: 12px;
        color: #475569;
        outline: none;
        padding: 0 4px;
    }
</style>
@endpush

@section('content')
<div class="inbound-container" style="min-height: calc(100vh - 120px); display: flex; flex-direction: column;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
        <div>
            <h2 style="font-weight: 800; font-size: 18px;">NHẬP KHO HÀNG HÓA</h2>
        </div>
    </div>

    <!-- 1/ THÔNG TIN PHIẾU NHẬP -->
    <div class="ib-block">
        <div class="ib-block-title"><i class="fas fa-file-alt"></i> Thông tin phiếu nhập</div>

        <div class="info-grid">
            <div class="form-group">
                <label>Số phiếu<span>*</span></label>
                <input type="text" id="receipt_code" class="ib-input" value="{{ $nextCode }}" placeholder="NK-2025-00001">
            </div>
            <div class="form-group">
                <label>Số hóa đơn</label>
                <input type="text" id="invoice_no" class="ib-input" placeholder="...">
            </div>

            {{-- DROPZONE --}}
            <div class="dropzone-container" id="dropzone" ondragover="event.preventDefault();this.classList.add('drag-over')" ondragleave="this.classList.remove('drag-over')" ondrop="handleDrop(event)">
                <div class="dropzone-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                <div class="dropzone-text">Kéo thả file vào đây hoặc</div>
                <label class="btn-choose-file" onclick="document.getElementById('file-input').click()">
                    <i class="far fa-file-alt"></i> Chọn file
                </label>
                <input type="file" id="file-input" hidden multiple accept=".pdf,.jpg,.jpeg,.png,.xlsx,.xls" onchange="handleFiles(this.files)">
                <div class="format-info">PDF, JPG, PNG, Excel (Max 10MB)</div>
            </div>

            <div class="form-group">
                <label>Ngày nhập<span>*</span></label>
                <input type="date" id="receipt_date" class="ib-input" value="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label>Ngày HĐ</label>
                <input type="date" id="invoice_date" class="ib-input">
            </div>

            <div class="form-group">
                <label>Nhà cung cấp<span>*</span></label>
                <input type="text" id="supplier_name" class="ib-input" placeholder="Nhập tên nhà cung cấp...">
            </div>
            <div class="form-group">
                <label>Kho nhập<span>*</span></label>
                <select id="warehouse" class="ib-input">
                    <option value="Kho Nguyên Liệu">Kho Nguyên Liệu</option>
                    <option value="Kho Lab">Kho Lab</option>
                    <option value="Kho Thành Phẩm">Kho Thành Phẩm</option>
                </select>
            </div>

            <div class="form-group">
                <label>Xuất xứ</label>
                <input type="text" id="origin" class="ib-input" placeholder="Xuất xứ...">
            </div>
            <div class="form-group">
                <label>Người nhập<span>*</span></label>
                <input type="text" id="created_by_name" class="ib-input" value="{{ auth()->user()->display_name }}" readonly>
            </div>

            {{-- ATTACHMENT LIST --}}
            <div class="attachment-col">
                <table class="attachment-table" id="attachment-list">
                    <tbody>
                        <tr id="no-attach-row"><td style="color:#94a3b8; text-align:center; padding:10px;">Chưa có file đính kèm</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="form-group" style="align-items: flex-start; padding-top: 1px;">
                <label style="padding-top: 6px;">Ghi chú</label>
                <textarea id="notes" class="ib-input" style="height: 34px; min-height: 34px; resize: vertical; box-sizing: border-box;" placeholder="..."></textarea>
            </div>
            <div class="form-group">
                <label>Bộ phận</label>
                <input type="text" id="department" class="ib-input" value="Kho" readonly>
            </div>
        </div>
    </div>

    <!-- 2/ DANH SÁCH HÀNG HÓA -->
    <div class="ib-block table-block">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div class="ib-block-title" style="margin-bottom: 0;"><i class="fas fa-table"></i> Danh sách hàng hóa</div>
                <input type="text" id="table-search" class="ib-input" placeholder="Tìm kiếm tên, mã hàng..." style="width: 250px; height: 30px; padding: 2px 10px; font-size: 12px;" oninput="filterTable()">
            </div>
            <div style="display: flex; gap: 8px;">
                <button class="btn-outline-custom" onclick="resetForm()"><i class="fas fa-redo"></i> Nhập mới</button>
                <button class="btn-add" onclick="addRow()"><i class="fas fa-plus"></i> Thêm dòng</button>
                <button class="ui-btn ui-btn-primary" style="height: 28px; font-size: 12px; padding: 0 12px;" id="btn-save" onclick="submitInbound()"><i class="fas fa-check-circle"></i> Lưu & Hoàn tất</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="item-table" id="main-table">
                <thead>
                    <tr>

                        <th width="35">STT</th>
                        <th width="110">Mã hàng</th>
                        <th width="200">Tên hàng</th>
                        <th width="90">Nhóm</th>
                        <th width="50">ĐVT</th>
                        <th width="90">Quy cách</th>
                        <th width="70">SL</th>
                        <th width="100">Đơn giá</th>
                        <th width="110">Thành tiền</th>
                        <th width="90">Số lô</th>
                        <th width="100">NSX</th>
                        <th width="100">HSD</th>
                        <th width="120">Kho</th>
                        <th width="35"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inboundItems as $index => $item)
                        @php $r = $item->receipt; @endphp
                        <tr class="existing-row" data-id="{{ $item->id }}" data-receipt-id="{{ $r ? $r->id : '' }}" 
                            data-receipt-code="{{ $r ? $r->receipt_code : '' }}"
                            data-invoice-no="{{ $r ? $r->invoice_no : '' }}"
                            data-receipt-date="{{ $r && $r->receipt_date ? $r->receipt_date->format('Y-m-d') : '' }}"
                            data-invoice-date="{{ $r && $r->invoice_date ? $r->invoice_date->format('Y-m-d') : '' }}"
                            data-supplier-name="{{ $r ? $r->supplier_name : '' }}"
                            data-warehouse="{{ $r ? $r->warehouse : '' }}"
                            data-origin="{{ $r ? $r->origin : '' }}"
                            data-notes="{{ $r ? $r->notes : '' }}"
                            data-attachments="{{ json_encode($r && $r->attachments ? $r->attachments->toArray() : []) }}"
                            data-initial-category-id="{{ $item->category_id }}"
                            onclick="loadReceiptFromRow(this)"
                            style="cursor: pointer; transition: background 0.2s;">

                            <td style="text-align:center;color:#64748b;font-size:11px;" class="stt-cell">{{ $inboundItems->firstItem() + $index }}</td>
                            <td style="position:relative;">
                                <input type="text" class="ib-input col-ma" placeholder="Mã hàng" value="{{ $item->ma_hang }}" autocomplete="off" readonly>
                            </td>
                            <td><input type="text" class="ib-input col-ten" placeholder="Tên hàng" value="{{ $item->ten_hang }}" readonly></td>
                            <td><input type="text" class="ib-input col-nhom" placeholder="Nhóm" value="{{ $item->category ? $item->category->name : '' }}" readonly></td>
                            <td><input type="text" class="ib-input col-dvt" placeholder="Kg" value="{{ $item->don_vi_tinh }}" readonly></td>
                            <td><input type="text" class="ib-input col-qc" placeholder="Quy cách" value="{{ $item->quy_cach }}" readonly></td>
                            <td><input type="text" class="ib-input col-sl val-qty" style="text-align:center;" value="{{ (float)$item->so_luong }}" oninput="calc(this)"></td>
                            <td><input type="text" class="ib-input col-gia val-price" style="text-align:right;" value="{{ number_format($item->don_gia, 0, ',', '.') }}" oninput="calc(this)"></td>
                            <td><input type="text" class="ib-input col-tt val-total" style="text-align:right;" value="{{ number_format($item->thanh_tien, 0, ',', '.') }}" readonly></td>
                            <td><input type="text" class="ib-input col-lo" style="text-align:center;" placeholder="Số lô" value="{{ $item->so_lo }}"></td>
                            <td><input type="date" class="ib-input col-nsx" value="{{ $item->ngay_san_xuat ? $item->ngay_san_xuat->format('Y-m-d') : '' }}"></td>
                            <td><input type="date" class="ib-input col-hsd" value="{{ $item->han_su_dung ? $item->han_su_dung->format('Y-m-d') : '' }}"></td>
                            <td>
                                <input type="text" class="ib-input col-kho" value="{{ $item->kho_nhap }}" readonly>
                            </td>
                            <td style="text-align:center;">
                                <i class="fas fa-trash-alt" style="color:#ef4444;cursor:pointer;" onclick="event.stopPropagation(); removeRow(this)"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="pagination-bar" style="background:#f8fafc; border:1px solid #e2e8f0; border-top:none; border-radius:0 0 4px 4px;">
            <div class="pagination-info">
                Hiển thị {{ $inboundItems->firstItem() ?? 0 }} đến {{ $inboundItems->lastItem() ?? 0 }} của {{ $inboundItems->total() }} kết quả
            </div>
            <div class="pagination-right">
                <div class="pagination-controls">
                    <a class="page-btn {{ $inboundItems->onFirstPage() ? 'disabled' : '' }}" href="{{ $inboundItems->url(1) }}" title="Trang đầu"><i class="fas fa-angle-double-left"></i></a>
                    <a class="page-btn {{ $inboundItems->onFirstPage() ? 'disabled' : '' }}" href="{{ $inboundItems->previousPageUrl() }}" title="Trang trước"><i class="fas fa-angle-left"></i></a>
                    @foreach($inboundItems->getUrlRange(max(1,$inboundItems->currentPage()-2), min($inboundItems->lastPage(),$inboundItems->currentPage()+2)) as $page => $url)
                        <a class="page-btn {{ $page == $inboundItems->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                    @endforeach
                    <a class="page-btn {{ !$inboundItems->hasMorePages() ? 'disabled' : '' }}" href="{{ $inboundItems->nextPageUrl() }}" title="Trang sau"><i class="fas fa-angle-right"></i></a>
                    <a class="page-btn {{ !$inboundItems->hasMorePages() ? 'disabled' : '' }}" href="{{ $inboundItems->url($inboundItems->lastPage()) }}" title="Trang cuối"><i class="fas fa-angle-double-right"></i></a>
                </div>
                <div class="pagination-goto">
                    Đến trang
                    <input type="number" class="goto-input" id="gotoPage" value="{{ $inboundItems->currentPage() }}" min="1" max="{{ $inboundItems->lastPage() }}">
                </div>
                <div class="page-size">
                    <select class="per-page-select" onchange="changePerPage(this.value)">
                        @foreach([15,30,50,100] as $pp)
                            <option value="{{ $pp }}" {{ request('per_page', 15) == $pp ? 'selected' : '' }}>{{ $pp }} / trang</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>



</div>
@endsection

@push('scripts')
<script>
    const CATEGORIES = @json($categories);
    const PRODUCTS   = @json($products);   // pre-loaded for quick fill
    const CSRF       = '{{ csrf_token() }}';
    const STORE_URL  = '{{ route("inventory.inbound.store") }}';

    let rowCounter = 0;
    let currentReceiptId = null;
    let pendingFiles = []; // files chosen but not yet uploaded (uploaded on submit)

    /* ========== ROWS ========== */
    function addRow() {
        rowCounter++;
        const tbody = document.querySelector('#main-table tbody');
        const row = document.createElement('tr');

        let catOptions = '<option value="">-- Nhóm --</option>';
        CATEGORIES.forEach(c => catOptions += `<option value="${c.id}">${c.name}</option>`);

        let khoOptions = ['Kho Nguyên Liệu','Kho Lab','Kho Thành Phẩm']
            .map(k => `<option>${k}</option>`).join('');

        row.dataset.rid = rowCounter;
        row.innerHTML = `

            <td style="text-align:center;color:#64748b;font-size:11px;" class="stt-cell">${rowCounter}</td>
            <td style="position:relative;">
                <input type="text" class="ib-input col-ma" placeholder="Mã hàng" autocomplete="off"
                    oninput="searchProduct(this)" onfocus="searchProduct(this)">
                <div class="prod-autocomplete"></div>
            </td>
            <td><input type="text" class="ib-input col-ten" placeholder="Tên hàng" readonly></td>
            <td><input type="text" class="ib-input col-nhom" placeholder="Nhóm" readonly></td>
            <td><input type="text" class="ib-input col-dvt" placeholder="Kg" readonly></td>
            <td><input type="text" class="ib-input col-qc" placeholder="Quy cách" readonly></td>
            <td><input type="text" class="ib-input col-sl val-qty" style="text-align:right;" value="0" oninput="calc(this)"></td>
            <td><input type="text" class="ib-input col-gia val-price" style="text-align:right;" value="0" oninput="calc(this)"></td>
            <td><input type="text" class="ib-input col-tt val-total" style="text-align:right;" value="0" readonly></td>
            <td><input type="text" class="ib-input col-lo" placeholder="Số lô"></td>
            <td><input type="date" class="ib-input col-nsx"></td>
            <td><input type="date" class="ib-input col-hsd"></td>
            <td><input class="ib-input col-kho" value="Kho Nguyên Liệu"></td>
            <td style="text-align:center;">
                <i class="fas fa-trash-alt" style="color:#ef4444;cursor:pointer;" onclick="removeRow(this)"></i>
            </td>
        `;

        // Prepend row (thêm ở trên đầu như yêu cầu)
        tbody.insertBefore(row, tbody.firstChild);
        reOrder();

        // Đóng dropdown khi click ngoài
        row.querySelector('.col-ma').addEventListener('blur', function() {
            setTimeout(() => row.querySelector('.prod-autocomplete').style.display = 'none', 200);
        });
    }

    function removeRow(btn) {
        const tr = btn.closest('tr');
        const id = tr.dataset.id;
        
        if (!id) {
            tr.remove();
            reOrder();
            return;
        }

        showConfirm('Xóa dòng hàng hóa', 'Bạn có chắc chắn muốn xóa dòng này?', async () => {
            try {
                const res = await fetch(`/ton-kho/nhap-kho/item/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (!data.success) {
                    alert(data.message || 'Lỗi khi xóa dữ liệu');
                    return;
                }
            } catch (e) {
                alert('Lỗi kết nối máy chủ!');
                return;
            }
            tr.remove();
            reOrder();
            resetForm();
            showToast('Đã xóa dòng hàng hóa thành công!', 'success');
        });
    }

    function deleteCheckedRows() {
        const checkedRows = Array.from(document.querySelectorAll('#main-table tbody tr')).filter(tr => tr.querySelector('.row-ck')?.checked);
        if (!checkedRows.length) {
            alert('Vui lòng chọn ít nhất 1 dòng để xóa!');
            return;
        }

        const unsavedRows = checkedRows.filter(tr => !tr.dataset.id);
        const savedRows   = checkedRows.filter(tr => tr.dataset.id);

        // Xóa nhanh dòng chưa lưu
        unsavedRows.forEach(tr => tr.remove());

        if (savedRows.length > 0) {
            showConfirm('Xóa nhiều dòng', `Bạn có chắc chắn muốn xóa ${savedRows.length} dòng dữ liệu đã lưu?`, async () => {
                for (let tr of savedRows) {
                    const id = tr.dataset.id;
                    try {
                        const res = await fetch(`/ton-kho/nhap-kho/item/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': CSRF,
                                'Accept': 'application/json'
                            }
                        });
                        const data = await res.json();
                        if (!data.success) {
                            alert(data.message || 'Lỗi khi xóa dữ liệu ID: ' + id);
                            continue;
                        }
                    } catch (e) {
                        alert('Lỗi kết nối máy chủ khi xóa dòng!');
                        continue;
                    }
                    tr.remove();
                }
                reOrder();
                resetForm();
                showToast(`Đã xóa thành công ${savedRows.length} dòng hàng hóa!`, 'success');
            });
        } else {
            reOrder();
            resetForm();
            if (unsavedRows.length > 0) {
                showToast(`Đã xóa nhanh ${unsavedRows.length} dòng chưa lưu!`, 'success');
            }
        }
    }

    function toggleAllRows(src) {
        document.querySelectorAll('.row-ck').forEach(c => c.checked = src.checked);
    }

    function reOrder() {
        document.querySelectorAll('#main-table tbody tr').forEach((tr, i) => {
            tr.querySelector('.stt-cell').innerText = i + 1;
        });
    }

    /* ========== CALC ========== */
    function calc(input) {
        const tr = input.closest('tr');

        // Định dạng tiền cho cột đơn giá ngay khi nhập
        if (input.classList.contains('val-price')) {
            let val = input.value.replace(/\D/g, "");
            if (val !== "") {
                input.value = Number(val).toLocaleString('vi-VN');
            }
        }

        // Lấy giá trị để tính toán (loại bỏ dấu chấm phân cách hàng nghìn)
        const qtyStr = tr.querySelector('.val-qty').value.replace(/\./g, '').replace(',', '.');
        const priceStr = tr.querySelector('.val-price').value.replace(/\./g, '').replace(',', '.');
        
        const qty   = parseFloat(qtyStr) || 0;
        const price = parseFloat(priceStr) || 0;
        
        tr.querySelector('.val-total').value = (qty * price).toLocaleString('vi-VN');
    }

    /* ========== PRODUCT AUTOCOMPLETE ========== */
    function searchProduct(input) {
        const q = input.value.trim().toLowerCase();
        const dd = input.nextElementSibling; // .prod-autocomplete

        const filtered = PRODUCTS.filter(p =>
            p.ma_hang.toLowerCase().includes(q) || p.ten_hang.toLowerCase().includes(q)
        ).slice(0, 15);

        if (!filtered.length) { dd.style.display = 'none'; return; }

        dd.innerHTML = filtered.map(p => `
            <div class="ac-item" onmousedown="fillProduct(this)" 
                data-id="${p.id}"
                data-ma="${p.ma_hang}"
                data-ten="${p.ten_hang}"
                data-cat="${p.category_id ?? ''}"
                data-dvt="${p.don_vi_tinh ?? ''}"
                data-qc="${p.quy_cach ?? ''}"
                data-gia="${p.gia_nhap ?? 0}">
                <span class="ac-code">[${p.ma_hang}]</span> ${p.ten_hang}
            </div>`).join('');
        dd.style.display = 'block';
    }

    function fillProduct(item) {
        const tr = item.closest('tr');
        tr.querySelector('.col-ma').value    = item.dataset.ma;
        tr.querySelector('.col-ten').value   = item.dataset.ten;
        tr.querySelector('.col-dvt').value   = item.dataset.dvt;
        tr.querySelector('.col-qc').value    = item.dataset.qc;
        tr.querySelector('.col-gia').value   = Number(item.dataset.gia || 0).toLocaleString('vi-VN');
        tr.dataset.productId = item.dataset.id;

        // Set nhóm
        const catId = item.dataset.cat;
        const catObj = CATEGORIES.find(c => c.id == catId);
        tr.querySelector('.col-nhom').value = catObj ? catObj.name : '';
        tr.dataset.categoryId = catId;

        // Set kho mặc định từ header
        const khoHeader = document.getElementById('warehouse').value;
        const khoSel = tr.querySelector('.col-kho');
        [...khoSel.options].forEach(o => o.selected = (o.value === khoHeader));

        calc(tr.querySelector('.val-qty'));
        item.closest('.prod-autocomplete').style.display = 'none';
    }

    /* ========== FILE HANDLING ========== */
    function handleDrop(e) {
        e.preventDefault();
        document.getElementById('dropzone').classList.remove('drag-over');
        handleFiles(e.dataTransfer.files);
    }

    function handleFiles(files) {
        Array.from(files).forEach(f => {
            if (f.size > 10 * 1024 * 1024) { alert(`File ${f.name} vượt quá 10MB`); return; }
            pendingFiles.push(f);
            renderAttachment(f.name, pendingFiles.length - 1);
        });
        document.getElementById('no-attach-row')?.remove();
    }

    function renderAttachment(name, idx) {
        const tbody = document.querySelector('#attachment-list tbody');
        const ext = name.split('.').pop().toLowerCase();
        const icon = ext === 'pdf' ? 'fa-file-pdf' : ['jpg','jpeg','png'].includes(ext) ? 'fa-file-image' : 'fa-file-excel';
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="color:#0070D2;"><i class="far ${icon}"></i> ${name}</td>
            <td width="30"><i class="fas fa-trash-alt" style="color:#ef4444;cursor:pointer;" onclick="removeFile(this,${idx})"></i></td>`;
        tbody.appendChild(tr);
    }

    function removeFile(btn, idx) {
        pendingFiles[idx] = null;
        btn.closest('tr').remove();
    }

    /* ========== SUBMIT ========== */
    async function submitInbound() {
        const code = document.getElementById('receipt_code').value.trim();
        if (!code) { alert('Vui lòng nhập số phiếu!'); return; }

        const rows = document.querySelectorAll('#main-table tbody tr');
        if (!rows.length) { alert('Vui lòng thêm ít nhất 1 dòng hàng hóa!'); return; }

        const items = [];
        let valid = true;
        rows.forEach(tr => {
            const rowRid = tr.dataset.receiptId;
            if (currentReceiptId && rowRid !== currentReceiptId && rowRid) return;
            if (!currentReceiptId && rowRid) return;

            const ma  = tr.querySelector('.col-ma').value.trim();
            const ten = tr.querySelector('.col-ten').value.trim();
            const slStr = tr.querySelector('.val-qty').value.replace(/\./g, '').replace(',', '.');
            const sl = parseFloat(slStr) || 0;

            if (!ma || !ten) { alert('Vui lòng nhập đầy đủ Mã hàng và Tên hàng!'); valid = false; return; }
            if (sl <= 0) { alert(`Số lượng dòng "${ten}" phải lớn hơn 0!`); valid = false; return; }

            const giaStr = tr.querySelector('.val-price').value.replace(/\./g, '').replace(',', '.');

            items.push({
                product_catalog_id: tr.dataset.productId || null,
                ma_hang:    ma,
                ten_hang:   ten,
                category_id: tr.dataset.categoryId || tr.dataset.initialCategoryId || null,
                don_vi_tinh: tr.querySelector('.col-dvt').value,
                quy_cach:   tr.querySelector('.col-qc').value,
                so_luong:   sl,
                don_gia:    parseFloat(giaStr) || 0,
                so_lo:      tr.querySelector('.col-lo').value,
                ngay_san_xuat: tr.querySelector('.col-nsx').value || null,
                han_su_dung:   tr.querySelector('.col-hsd').value || null,
                kho_nhap:   tr.querySelector('.col-kho').value,
            });
        });
        if (!valid) return;

        // Build FormData (để upload file cùng lúc)
        const fd = new FormData();
        fd.append('_token', CSRF);
        fd.append('receipt_code',  code);
        fd.append('invoice_no',    document.getElementById('invoice_no').value);
        fd.append('receipt_date',  document.getElementById('receipt_date').value);
        fd.append('invoice_date',  document.getElementById('invoice_date').value);
        fd.append('supplier_name', document.getElementById('supplier_name').value);
        fd.append('warehouse',     document.getElementById('warehouse').value);
        fd.append('origin',        document.getElementById('origin').value);
        fd.append('notes',         document.getElementById('notes').value);
        fd.append('department',    document.getElementById('department').value);
        fd.append('items', JSON.stringify(items));
        if (currentReceiptId) {
            fd.append('receipt_id', currentReceiptId);
        }

        pendingFiles.filter(Boolean).forEach(f => fd.append('attachments[]', f));

        const btn = document.querySelector('[onclick="submitInbound()"]');
        btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';

        try {
            const response = await fetch(STORE_URL, {
                method: 'POST',
                body: fd,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });

            const text = await response.text();
            let res;
            try {
                res = JSON.parse(text);
            } catch {
                // Server trả về HTML (lỗi 500, debug page...)
                console.error('Non-JSON response:', text.substring(0, 500));
                alert('Lỗi server (HTTP ' + response.status + '). Xem console để biết chi tiết.');
                return;
            }

            if (res.success) {
                await showToast(res.message);
                setTimeout(() => window.location.reload(), 1200);
            } else {
                alert(res.message || 'Lỗi khi lưu phiếu!');
            }
        } catch(e) {
            alert('Lỗi kết nối: ' + e.message);
        } finally {
            btn.disabled = false; btn.innerHTML = '<i class="fas fa-check-circle"></i> Lưu & Hoàn tất';
        }
    }

    function loadReceiptFromRow(tr) {
        const rid = tr.dataset.receiptId;
        if (!rid) return;

        currentReceiptId = rid;
        document.getElementById('receipt_code').value   = tr.dataset.receiptCode || '';
        document.getElementById('invoice_no').value     = tr.dataset.invoiceNo || '';
        document.getElementById('receipt_date').value   = tr.dataset.receiptDate || '';
        document.getElementById('invoice_date').value   = tr.dataset.invoiceDate || '';
        document.getElementById('supplier_name').value = tr.dataset.supplierName || '';
        document.getElementById('warehouse').value     = tr.dataset.warehouse || 'Kho Nguyên Liệu';
        document.getElementById('origin').value        = tr.dataset.origin || '';
        document.getElementById('notes').value         = tr.dataset.notes || '';

        // Load files
        const tbodyAttach = document.querySelector('#attachment-list tbody');
        tbodyAttach.innerHTML = '';
        const attachData = JSON.parse(tr.dataset.attachments || '[]');
        if (attachData.length) {
            attachData.forEach(att => {
                const trAtt = document.createElement('tr');
                trAtt.innerHTML = `
                    <td style="color:#0070D2;"><i class="fas fa-file"></i> <a href="/storage/${att.file_path}" target="_blank" style="color:#0070D2; text-decoration:none;">${att.original_name || 'Tài liệu'}</a></td>
                    <td width="30"><i class="fas fa-times" style="color:#ef4444; cursor:pointer;" onclick="this.closest('tr').remove()"></i></td>
                `;
                tbodyAttach.appendChild(trAtt);
            });
        } else {
            tbodyAttach.innerHTML = '<tr id="no-attach-row"><td style="color:#94a3b8; text-align:center; padding:10px;">Chưa có file đính kèm</td></tr>';
        }

        document.getElementById('btn-save').innerHTML = '<i class="fas fa-save"></i> Cập nhật phiếu';
    }

    function resetForm() {
        currentReceiptId = null;
        document.getElementById('receipt_code').value = '{{ $nextCode }}';
        document.getElementById('invoice_no').value = '';
        document.getElementById('receipt_date').value = '{{ date('Y-m-d') }}';
        document.getElementById('invoice_date').value = '';
        document.getElementById('supplier_name').value = '';
        document.getElementById('warehouse').value = 'Kho Nguyên Liệu';
        document.getElementById('origin').value = '';
        document.getElementById('notes').value = '';

        document.querySelectorAll('#main-table tbody tr').forEach(row => row.style.background = '');
        document.getElementById('btn-save').innerHTML = '<i class="fas fa-check-circle"></i> Lưu & Hoàn tất';
    }

    // Load ban đầu: không tự động thêm dòng trống nếu đã có data, nhưng nếu trống thì thêm.
    window.onload = () => {
        const tbody = document.querySelector('#main-table tbody');
        if (!tbody.querySelectorAll('tr').length) {
            addRow();
        }
    };

    // Click ra ngoài để đóng popup
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.prod-autocomplete') && !e.target.closest('.col-ma')) {
            document.querySelectorAll('.prod-autocomplete').forEach(dd => dd.style.display = 'none');
        }
    });

    // Tìm kiếm trong bảng hàng hóa
    function filterTable() {
        const q = document.getElementById('table-search').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#main-table tbody tr');
        rows.forEach(tr => {
            const ma = tr.querySelector('.col-ma').value.toLowerCase();
            const ten = tr.querySelector('.col-ten').value.toLowerCase();
            if (ma.includes(q) || ten.includes(q)) {
                tr.style.display = '';
            } else {
                tr.style.display = 'none';
            }
        });
    }
    // Phân trang
    function changePerPage(val) {
        const url = new URL(window.location);
        url.searchParams.set('per_page', val);
        url.searchParams.set('page', 1);
        window.location = url.toString();
    }

    document.getElementById('gotoPage')?.addEventListener('change', function() {
        const url = new URL(window.location);
        url.searchParams.set('page', this.value);
        window.location = url.toString();
    });
</script>
@endpush
