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
        height: calc(100vh - 100px);
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
        grid-template-rows: repeat(5, auto);
        gap: 8px 20px;
    }

    .form-group {
        display: flex;
        align-items: center;
        gap: 10px;
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
        color: #1e293b;
        outline: none;
        background: #fff;
        flex: 1;
    }

    .ib-input:focus { border-color: var(--primary); }
    .ib-input:read-only { background: #f8fafc; color: #64748b; }

    /* DROPZONE DESIGN */
    .dropzone-container {
        grid-column: 3;
        grid-row: 1 / span 3;
        border: 1.5px dashed #cbd5e1;
        border-radius: 8px;
        background: #fcfcfc;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 15px;
        text-align: center;
    }
    
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
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        overflow-y: auto;
        max-height: 100px;
    }

    .attachment-table {
        width: 100%;
        font-size: 11px;
        border-collapse: collapse;
    }

    .attachment-table td { padding: 6px; border-bottom: 1px solid #f1f5f9; }

    /* TABLE ITEM STYLE (NO BORDER INPUTS) */
    .item-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed; /* Cố định để kiểm soát chiều rộng */
    }

    .item-table th {
        background: #f8fafc;
        padding: 10px 5px;
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        border: 1px solid #e2e8f0;
        text-align: center;
    }

    .item-table td {
        padding: 0; 
        border: 1px solid #e2e8f0;
    }

    /* Input trong bảng không viền */
    .item-table .ib-input {
        border: 1px solid transparent;
        border-radius: 0;
        padding: 8px 6px;
        width: 100%;
        height: 100%;
        background: transparent;
    }

    .item-table tr:hover { background: #fcfcfc; }
    
    .item-table .ib-input:focus {
        border-color: var(--primary);
        background: #fff;
        z-index: 10;
        box-shadow: inset 0 0 0 1px var(--primary);
    }

    .item-table select.ib-input {
        padding-right: 2px;
    }

    .btn-add { background: #0070D2; color: #fff; padding: 6px 12px; border-radius: 4px; font-weight: 600; border: none; cursor: pointer; font-size: 12px; }
    .btn-outline-custom { background: #fff; color: #64748b; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 12px; }

    /* Custom flex for table block */
    .table-block {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }

    .table-responsive {
        flex: 1;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
    }

    .item-table th {
        position: sticky;
        top: 0;
        z-index: 20;
    }

</style>
@endpush

@section('content')
<div class="inbound-container">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
        <div>
            <h2 style="font-weight: 800; font-size: 18px;">NHẬP KHO HÀNG HÓA</h2>
            <p style="font-size: 11px; color: #64748b;">Trang chủ > Quản lý tồn kho > Nhập kho</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <button class="btn-outline-custom"><i class="fas fa-arrow-left"></i> Quay lại</button>
            <button class="ui-btn ui-btn-primary" style="height: 32px;"><i class="fas fa-check-circle"></i> Lưu & Hoàn tất</button>
        </div>
    </div>

    <!-- 1/ THÔNG TIN PHIẾU NHẬP -->
    <div class="ib-block">
        <div class="ib-block-title"><i class="fas fa-file-alt"></i> Thông tin phiếu nhập</div>
        
        <div class="info-grid">
            <div class="form-group">
                <label>Số phiếu<span>*</span></label>
                <input type="text" class="ib-input" placeholder="NK-2025-00001">
            </div>
            <div class="form-group">
                <label>Số hóa đơn</label>
                <input type="text" class="ib-input" placeholder="...">
            </div>
            
            <div class="dropzone-container">
                <div class="dropzone-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                <div class="dropzone-text">Kéo thả file vào đây hoặc</div>
                <label class="btn-choose-file">
                    <i class="far fa-file-alt"></i> Chọn file
                    <input type="file" hidden multiple>
                </label>
                <div class="format-info">PDF, JPG, PNG, Excel (Max 10MB)</div>
            </div>

            <div class="form-group">
                <label>Ngày nhập<span>*</span></label>
                <input type="date" class="ib-input" value="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label>Ngày HĐ</label>
                <input type="date" class="ib-input">
            </div>

            <div class="form-group">
                <label>Nhà cung cấp<span>*</span></label>
                <input type="text" class="ib-input" placeholder="Nhập tên nhà cung cấp...">
            </div>
            <div class="form-group">
                <label>Kho nhập<span>*</span></label>
                <select class="ib-input">
                    <option>Kho Nguyên Liệu</option>
                    <option>Kho Lab</option>
                </select>
            </div>

            <div class="form-group">
                <label>Địa chỉ</label>
                <input type="text" class="ib-input" placeholder="Địa chỉ..." readonly>
            </div>
            <div class="form-group">
                <label>Người nhập<span>*</span></label>
                <input type="text" class="ib-input" value="{{ auth()->user()->display_name }}" readonly>
            </div>

            <div class="attachment-col">
                <table class="attachment-table">
                    <tbody>
                        <tr>
                            <td style="color: #0070D2;"><i class="far fa-file-pdf"></i> INV-DSMN-25.pdf</td>
                            <td width="30"><i class="fas fa-trash-alt" style="color: #ef4444; cursor: pointer;"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="form-group">
                <label>Ghi chú</label>
                <textarea class="ib-input" style="height: 32px; min-height: 32px; resize: vertical;" placeholder="..."></textarea>
            </div>
            <div class="form-group">
                <label>Bộ phận</label>
                <input type="text" class="ib-input" value="Kho" readonly>
            </div>
        </div>
    </div>

    <!-- 2/ DANH SÁCH HÀNG HÓA -->
    <div class="ib-block table-block">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <div class="ib-block-title" style="margin-bottom: 0;"><i class="fas fa-table"></i> Danh sách hàng hóa</div>
            <div style="display: flex; gap: 8px;">
                <button class="btn-add" onclick="addRow()"><i class="fas fa-plus"></i> Thêm dòng</button>
                <button class="btn-outline-custom" style="color: #ef4444;">Xóa dòng</button>
                <button class="btn-outline-custom" style="color: #10b981;"><i class="fas fa-file-import"></i> Nhập nhiều dòng</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="item-table" id="main-table">
                <thead>
                    <tr>
                        <th width="35">STT</th>
                        <th width="90">Mã hàng</th>
                        <th width="150">Tên hàng</th>
                        <th width="90">Nhóm</th>
                        <th width="50">ĐVT</th>
                        <th width="150">Quy cách</th>
                        <th width="70">SL</th>
                        <th width="90">Đơn giá</th>
                        <th width="100">Thành tiền</th>
                        <th width="90">Lot</th>
                        <th width="100">NSX</th>
                        <th width="100">Hạn dùng</th>
                        <th width="110">Kho</th>
                        <th width="100">Ghi chú</th>
                        <th width="35"></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>

    <div style="display: flex; justify-content: flex-end; gap: 10px;">
        <button class="btn-outline-custom" style="padding: 6px 20px;">Hủy</button>
        <button class="ui-btn ui-btn-primary" style="padding: 6px 30px;">Lưu & Hoàn tất</button>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let counter = 0;
    function addRow() {
        counter++;
        const tbody = document.querySelector('#main-table tbody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td style="text-align: center; color: #64748b; font-size: 11px;">${counter}</td>
            <td><input type="text" class="ib-input"></td>
            <td><input type="text" class="ib-input"></td>
            <td><input type="text" class="ib-input"></td>
            <td><input type="text" class="ib-input"></td>
            <td><input type="text" class="ib-input"></td>
            <td><input type="text"  style="text-align: right;"class="ib-input val-qty" step="0.01" value="0.00" oninput="calc(this)"></td>
            <td><input type="text"  style="text-align: right;"class="ib-input val-price" value="0" oninput="calc(this)"></td>
            <td><input type="text"  style="text-align: right;"class="ib-input val-total" value="0" readonly></td>
            <td><input type="text" class="ib-input"></td>
            <td><input type="date" class="ib-input"></td>
            <td><input type="date" class="ib-input"></td>
            <td>
                <select class="ib-input">
                    <option>Kho Nguyên Liệu</option>
                    <option>Kho Lab</option>
                </select>
            </td>
            <td><input type="text" class="ib-input"></td>
            <td style="text-align: center;">
                <i class="fas fa-eye" style="color: #0070D2; cursor: pointer;"></i>
            </td>
        `;
        tbody.appendChild(row);
    }

    function calc(input) {
        const tr = input.closest('tr');
        const qty = parseFloat(tr.querySelector('.val-qty').value) || 0;
        const price = parseFloat(tr.querySelector('.val-price').value) || 0;
        const total = qty * price;
        tr.querySelector('.val-total').value = total.toLocaleString('vi-VN');
        updateSummary();
    }

    function updateSummary() {
        // Summary elements removed as per user request
    }

    function reOrder() {
        document.querySelectorAll('#main-table tbody tr').forEach((tr, i) => { tr.cells[0].innerText = i + 1; });
        counter = document.querySelectorAll('#main-table tbody tr').length;
    }

    window.onload = () => { addRow(); };
</script>
@endpush
