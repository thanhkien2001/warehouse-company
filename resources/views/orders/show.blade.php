@extends('layouts.app')
@section('title', 'Chi tiết Đơn: ' . $order->cto_code)
@section('page-title', 'Chi Tiết Đơn Hàng')
@section('page-subtitle', $order->cto_code . ' — ' . $order->ten_kh)

@push('styles')
<style>
    .order-detail-container {
        padding: 24px;
        background: #f8fafc;
        min-height: calc(100vh - 100px);
    }

    .admin-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
        overflow: hidden;
        position: relative;
    }

    .cto-badge {
        position: absolute;
        top: 0;
        right: 0;
        background: #0070D2 !important;
        color: #fff;
        padding: 6px 20px;
        font-weight: 800;
        font-size: 13px;
        letter-spacing: 1px;
        border-bottom-left-radius: 12px;
        box-shadow: -2px 2px 10px rgba(0,0,0,0.1);
        z-index: 10;
    }

    .info-section {
        background: #f8fafc;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
        border: 1px solid #e2e8f0;
        display: flex;
        gap: 40px;
        flex-wrap: wrap;
    }

    .info-column {
        flex: 1;
        min-width: 300px;
        font-size: 14px;
        color: #0f172a;
        line-height: 2.2;
    }

    .info-column h3 {
        margin: 0 0 10px 0;
        font-size: 14px;
        font-weight: 800;
        text-transform: uppercase;
        border-bottom: 1px dashed #cbd5e1;
        padding-bottom: 5px;
    }

    .info-row-flex {
        display: flex;
    }

    .info-label {
        color: #64748b;
        width: 140px;
        flex-shrink: 0;
    }

    .info-val {
        flex: 1;
        font-weight: 600;
    }

    /* Table Styling to match legacy */
    .legacy-table-wrapper {
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        margin-bottom: 15px;
    }

    .legacy-table {
        width: 100%;
        border-collapse: collapse;
        border: none;
        background: transparent;
        margin: 0;
        font-size: 13.5px;
    }

    .legacy-table thead {
        background: #f8fafc;
        border-bottom: 1px solid #cbd5e1;
    }

    .legacy-table th {
        padding: 12px 8px;
        text-align: center;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        border: none;
        text-transform: uppercase;
    }

    .legacy-table td {
        padding: 8px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .legacy-table input, .legacy-table textarea {
        width: 100%;
        border: 1px solid transparent;
        background: transparent;
        padding: 6px;
        border-radius: 4px;
        outline: none;
        font-family: inherit;
        font-size: 13.5px;
        transition: 0.2s;
    }

    .legacy-table input:focus, .legacy-table textarea:focus {
        border-color: #cbd5e1;
        background: #fff;
    }

    .legacy-table .col-action {
        width: 50px;
        text-align: center;
    }

    .btn-remove-row {
        color: #ef4444;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        opacity: 0.6;
        transition: 0.2s;
    }

    .btn-remove-row:hover {
        opacity: 1;
        transform: scale(1.1);
    }

    .add-row-btn {
        width: 100%;    
        padding: 12px;
        border:none;
        margin-top: 10px;
        border-radius: 8px;
        font-size: 15px;
        background: #0070D2 !important;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .add-row-btn:hover {
        background: #f1f5f9;
        border-color: #3b82f6;
        color: #3b82f6;
    }

    /* Summary Section */
    .summary-container {
        display: flex;
        justify-content: flex-end;
        margin-top: 25px;
    }

    .summary-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        width: 340px;
        font-family: 'Inter', sans-serif;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 14px;
        color: #475569;
    }

    .summary-row b {
        color: #0f172a;
    }

    .vat-box {
        display: inline-flex;
        align-items: center;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 4px 10px;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
    }

    .vat-box input {
        width: 40px;
        border: none;
        background: transparent;
        text-align: center;
        font-weight: 800;
        color: #0070D2;
        font-size: 15px;
        outline: none;
    }

    .total-row {
        border-top: 1px dashed #cbd5e1;
        padding-top: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .total-label {
        color: #ef4444;
        font-weight: 800;
        font-size: 16px;
    }

    .total-val {
        color: #ef4444;
        font-size: 22px;
        font-weight: 900;
    }

    .status-select {
        padding: 8px 15px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        outline: none;
        font-weight: 600;
        color: #0f172a;
        cursor: pointer;
        font-size: 14px;
    }

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
        text-align: left;
    }
    .prod-autocomplete .ac-item {
        padding: 8px 10px;
        cursor: pointer;
        font-size: 12px;
        border-bottom: 1px solid #f1f5f9;
        color: #000 !important;
    }
    .prod-autocomplete .ac-item:hover { background: #eff6ff; }
    .prod-autocomplete .ac-code { font-weight: 700; color: #0070D2; }
</style>
@endpush

@section('content')
<div class="order-detail-container">
    <div class="admin-card">
        <div class="cto-badge">
            <i class="fas fa-tag"></i> <span>{{ $order->cto_code }}</span>
        </div>

        <div style="padding: 24px;">
            <div style="margin-bottom: 25px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 15px; padding-right: 120px;">
                <a href="{{ route('orders.index') }}" class="ui-btn ui-btn-outline" style="padding: 6px 12px; font-size: 13px; margin-bottom: 15px;">
                    <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Quay lại danh sách
                </a>
                <h2 style="font-size: 24px; font-weight: 900; color: #0f172a; margin: 0 0 5px 0;">Chi Tiết Đơn Hàng</h2>
                <p style="margin: 0; color: #64748b; font-size: 14px;">Xem thông tin, cập nhật trạng thái và chỉnh sửa hàng hóa.</p>
            </div>

            <div class="info-section">
                <div class="info-column">
                    <h3 style="margin: 0 0 15px 0; font-size: 15px; font-weight: 800; color: #0f172a; text-transform: uppercase; border-bottom: 1px dashed #cbd5e1; padding-bottom: 8px;">
                        <i class="fas fa-user-tie" style="margin-right: 5px; color: #0070D2;"></i> THÔNG TIN KHÁCH HÀNG
                    </h3>
                    <div class="info-row-flex"><span class="info-label">Khách hàng:</span> <b class="info-val">{{ $order->ten_kh }}</b></div>
                    <div class="info-row-flex"><span class="info-label">Mã khách hàng:</span> <span class="info-val">{{ $order->customer->ma_kh }}</span></div>
                    <div class="info-row-flex"><span class="info-label">Mã số thuế:</span> <span class="info-val">{{ $order->customer->ma_so_thue }}</span></div>
                    <div class="info-row-flex"><span class="info-label">Số điện thoại:</span> <span class="info-val">{{ $order->customer->sdt }}</span></div>
                    <div class="info-row-flex"><span class="info-label">Địa chỉ:</span> <span class="info-val" style="line-height: 1.4; padding-top: 5px;">{{ $order->customer->dia_chi }}</span></div>
                </div>

                <div class="info-column">
                    <h3 style="margin: 0 0 15px 0; font-size: 15px; font-weight: 800; color: #0f172a; text-transform: uppercase; border-bottom: 1px dashed #cbd5e1; padding-bottom: 8px;">
                        <i class="fas fa-file-invoice-dollar" style="margin-right: 5px; color: #0070D2;"></i> THÔNG TIN ĐƠN HÀNG
                    </h3>
                    <div class="info-row-flex"><span class="info-label">Mã đơn hàng:</span> <b class="info-val" style="color: #0070D2;">{{ $order->cto_code }}</b></div>
                    <div class="info-row-flex"><span class="info-label">Người bán:</span> <span class="info-val">{{ $order->nguoi_ban }}</span></div>
                    <div class="info-row-flex"><span class="info-label">SĐT người bán:</span> <span class="info-val">{{ $order->sdt_ban }}</span></div>
                    <div class="info-row-flex"><span class="info-label">Người mua:</span> <span class="info-val">{{ $order->nguoi_mua }}</span></div>
                    <div class="info-row-flex"><span class="info-label">SĐT người mua:</span> <span class="info-val">{{ $order->sdt_mua }}</span></div>
                </div>
            </div>

            <div style="margin-bottom: 25px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 15px;">
                    <h3 style="margin: 0; font-size: 15px; font-weight: 800; color: #0f172a; text-transform: uppercase;">
                        <i class="fas fa-box-open" style="color: #10b981; margin-right: 5px;"></i> CHI TIẾT GIỎ HÀNG
                    </h3>

                    <div style="display: flex; gap: 10px; align-items: center;">
                        <div style="height: 38px; display: flex; align-items: center; gap: 8px; background: #fff; padding: 0 12px; border-radius: 8px; border: 1px solid #cbd5e1; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                            <i class="fas fa-coins" style="color: #3b82f6;"></i>
                            <span style="font-size: 13.5px; font-weight: 700; color: #475569;">Tỷ giá:</span>
                            <input type="text" id="inp-tygia" 
                                value="{{ number_format((float)($order->meta?->ty_gia ?? 25450), 0, ',', '.') }}" 
                                placeholder="Tỷ giá..."
                                oninput="formatExchangeRate(this)"
                                style="width: 85px; border: none; outline: none; text-align: right; color: #0f172a; font-weight: 800; font-size: 14px; background:transparent;">
                            <span style="color: #cbd5e1;">|</span>
                            <input type="text" id="inp-ngay-tygia" 
                                value="{{ $order->meta?->ngay_ty_gia ?? now()->format('d/m/Y') }}" 
                                placeholder="DD/MM/YYYY"
                                style="width: 90px; border: none; outline: none; text-align: center; color: #64748b; font-size: 13px; background:transparent;">
                        </div>

                        <span style="font-size: 14px; font-weight: 600; color: #475569;">Trạng thái:</span>
                        <select id="sel-status" class="status-select" onchange="syncStatusFromSelect()">
                            <option value="Chờ xác nhận">Chờ xác nhận</option>
                            <option value="Đang xử lý">Đang xử lý</option>
                            <option value="Đang vận chuyển">Đang vận chuyển</option>
                            <option value="Hoàn thành">Hoàn thành</option>
                            <option value="Đã hủy">Đã hủy đơn này</option>
                        </select>

                        <div style="display: flex; gap: 5px;">
                            <a href="{{ route('orders.pdf', $order->id) }}" target="_blank"
                               class="ui-btn ui-btn-pdf"
                               style="display:inline-flex; align-items:center; gap:6px; text-decoration:none; padding:8px 16px; border-radius:8px; font-size:14px; font-weight:700; background:#ef4444; color:#fff; box-shadow: 0 2px 8px rgba(239,68,68,0.3); transition:0.2s;"
                               onmouseover="this.style.background='#dc2626'"
                               onmouseout="this.style.background='#ef4444'">
                                <i class="fas fa-file-pdf"></i> Xuất PDF
                            </a>
                        </div>

                        @if(auth()->user()->canDo('donhang','edit') || auth()->user()->isAdmin())
                        <button id="btn-save" class="ui-btn ui-btn-save" onclick="saveOrderDetails()">
                            <i class="fas fa-save" style="margin-right: 5px;"></i> Lưu
                        </button>
                        @endif
                    </div>
                </div>

                <div class="legacy-table-wrapper">
                    <table class="legacy-table">
                        <thead>
                            <tr>
                                <th style="width: 10%;">Mã Hàng</th>
                                <th style="width: 30%;">Mô tả hàng hóa</th>
                                <th style="width: 8%;">Số lượng</th>
                                <th style="width: 6%;">ĐVT</th>
                                <th style="width: 14%;">Đơn giá (VND)</th>
                                <th style="width: 16%;">Thành tiền (VND)</th>
                                <th style="width: 12%;">Công nợ</th>
                                <th style="width: 4%; text-align:center"><i class="fas fa-trash"></i></th>
                            </tr>
                        </thead>
                        <tbody id="items-body">
                            {{-- Rendered by JS --}}
                        </tbody>
                    </table>
                </div>

                @if(auth()->user()->canDo('donhang','edit') || auth()->user()->isAdmin())
                <button class="add-row-btn" onclick="addRow()">
                    <i class="fas fa-plus-circle"></i> Thêm dòng hàng hóa mới
                </button>
                @endif

                <div class="summary-container">
                    <div class="summary-card">
                        <div class="summary-row">
                            <span>Cộng tiền hàng:</span>
                            <b id="sum-subtotal">0</b>
                        </div>

                        <div class="summary-row" style="align-items: center;">
                            <div class="vat-box">
                                <span>Thuế VAT</span>
                                <input type="text" id="vat-input"
                                    value="{{ rtrim(rtrim(number_format($order->meta?->vat_percent ?? 8, 2, '.', ''), '0'), '.') }}"
                                    onchange="calcTotal()" style="text-align:center;">
                                <span>%</span>
                            </div>
                            <b id="sum-vat">0</b>
                        </div>

                        <div class="total-row">
                            <span class="total-label">TỔNG CỘNG:</span>
                            <b id="sum-total" class="total-val">0</b>
                        </div>
                    </div>
                </div>

                <!-- <div style="margin-top: 25px; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
                    <h3 style="margin: 0 0 10px 0; font-size: 14px; font-weight: 800; color: #475569; text-transform: uppercase;">
                        <i class="fas fa-sticky-note" style="margin-right: 5px;"></i> Ghi chú & Thao tác
                    </h3>
                    <div class="form-group">
                        <textarea id="val-ghi-chu" class="form-control" rows="3" placeholder="Nhập ghi chú vận hành..." style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; outline: none; font-family: inherit; font-size: 14px;">{{ $order->ghi_chu }}</textarea>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 15px;">
                        @if(!$order->deliveryNote && $order->trang_thai != 'Đã hủy' && (auth()->user()->canDo('phieugiao','edit') || auth()->user()->isAdmin()))
                        <button class="ui-btn" style="background: #8b5cf6; color: #fff; border-radius: 8px;" onclick="openModalTaoPhieu()">
                            <i class="fas fa-truck-loading"></i> Tạo Phiếu Giao Hàng
                        </button>
                        @endif

                        @if($order->deliveryNote)
                        <a href="{{ route('deliveries.show', $order->deliveryNote->id) }}" class="ui-btn" style="background:#f59e0b; color:#fff; border-radius: 8px; text-decoration: none; padding: 10px 16px; font-size: 14px; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-file-invoice"></i> Xem PGH ({{ $order->deliveryNote->dn_code }})
                        </a>
                        @endif
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</div>

<datalist id="productList">
    @foreach($products as $p)
        <option value="{{ $p->ma_hang }}">{{ $p->ten_hang }}</option>
    @endforeach
</datalist>

{{-- MODAL TẠO PHIẾU GIAO --}}
<div id="modal-taophieu" class="modal-overlay">
    <div class="modal-box" style="border-radius: 16px;">
        <div class="modal-header" style="border-bottom: 2px solid #f1f5f9;">
            <h3 style="font-weight: 800;"><i class="fas fa-truck-loading" style="color:#0070D2"></i> Tạo Phiếu Giao Hàng</h3>
            <button class="modal-close" onclick="closeModal('modal-taophieu')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body" style="padding: 24px;">
            <input type="hidden" id="dn_cto_code" value="{{ $order->cto_code }}">
            <div style="margin-bottom: 15px; display: flex; justify-content: space-between;"><span style="color:#64748b">Đơn Hàng:</span> <b style="color:#0f172a">{{ $order->cto_code }}</b></div>
            <div style="margin-bottom: 20px; display: flex; justify-content: space-between;"><span style="color:#64748b">Khách Hàng:</span> <b style="color:#0f172a; text-align: right; width: 60%">{{ $order->ten_kh }}</b></div>
            <hr style="margin:16px 0;border:none;border-top:1px dashed #cbd5e1">
            <div class="form-group" style="margin-top: 15px;">
                <label class="form-label">Ngày giao <span style="color:red">*</span></label>
                <input type="date" id="dn_date" class="form-control" style="border-radius: 8px;" value="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label class="form-label">Số ngày giới hạn thanh toán</label>
                <input type="number" id="dn_han" class="form-control" style="border-radius: 8px;" placeholder="Để trống nếu không giới hạn">
                <div style="font-size:11.5px;color:#64748b;margin-top:6px; font-style: italic;">VD: Nhập 30 => Hạn thanh toán là {{ date('d/m/Y', strtotime('+30 days')) }}</div>
            </div>
        </div>
        <div class="modal-footer" style="background: #f8fafc; padding: 16px 24px; border-top: 1px solid #e2e8f0;">
            <button class="btn btn-ghost" onclick="closeModal('modal-taophieu')">Hủy</button>
            <button class="btn btn-primary" style="background:#0070D2; border-radius: 8px;" onclick="submitTaoPhieu()"><i class="fas fa-check"></i> Xác Nhận Tạo</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const STOCK = @json($stockMap);
const PRODUCTS = @json($products);
let orderItems = @json($order->items);
let currentStatus = '{{ $order->trang_thai }}';
const canEdit = {{ (auth()->user()->canDo('donhang','edit') || auth()->user()->isAdmin()) ? 'true' : 'false' }};

window.addEventListener('DOMContentLoaded', () => {
    document.getElementById('sel-status').value = currentStatus;
    loadTable();
});

function syncStatusFromSelect() {
    currentStatus = document.getElementById('sel-status').value;
}

// Table Logic
function parseLocaleNumber(str) {
    if (!str) return 0;
    // Replace thousand separator (.) and decimal separator (,) for standard float parsing
    return parseFloat(str.replace(/\./g, '').replace(',', '.')) || 0;
}

function loadTable() {
    const tbody = document.getElementById('items-body');
    tbody.innerHTML = '';
    if(orderItems.length === 0) {
        for(let i=0; i<3; i++) addRow(false);
    } else {
        orderItems.forEach((it, i) => {
            const html = createRowHtml(i+1, it);
            tbody.insertAdjacentHTML('beforeend', html);
        });
    }
    calcTotal();
}

function createRowHtml(stt, data = {}) {
    const ma = data.ma_hang || '';
    const ten = data.ten_hang || '';
    const phu = data.mo_ta_phu || '';
    const sl = data.so_luong || '';
    const dvt = data.don_vi_tinh || '';
    const gia = data.don_gia || '';
    const nợ = data.cong_no || ''; // Placeholder for "Công nợ"

    const warn = (ma && STOCK[ma] !== undefined && STOCK[ma] < Number(sl)) 
                 ? `<div class="warn-stock" style="font-size:10px;color:red;margin-top:2px">! Tồn: ${STOCK[ma]}</div>` : '';
                 
    return `
    <tr class="item-row">
        <td style="position:relative;">
            <input type="text" class="in-ma" value="${ma}" placeholder="Mã..." 
                oninput="searchProduct(this)" onfocus="searchProduct(this)" 
                autocomplete="off" ${!canEdit?'readonly':''}>
            <div class="prod-autocomplete"></div>
            ${warn}
        </td>
        <td>
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <input type="text" class="in-ten" value="${ten}" placeholder="Tên sản phẩm..." style="font-weight: 700; height: 30px;" ${!canEdit?'readonly':''}>
                <input type="text" class="in-phu" value="${phu}" placeholder="Mô tả phụ (quy cách, chất liệu...)" style="font-size: 12px; color: #64748b; height: 26px;" ${!canEdit?'readonly':''}>
            </div>
        </td>
        <td><input type="text" class="in-sl" value="${formatQuantity(sl)}" oninput="calcRow(this)" style="text-align:right" ${!canEdit?'readonly':''}></td>
        <td><input type="text" class="in-dvt" value="${dvt}" style="text-align:center" ${!canEdit?'readonly':''}></td>
        <td><input type="text" class="in-gia" value="${formatMoney(gia)}" oninput="formatAndCalc(this)" onfocus="this.select()" style="text-align:right" ${!canEdit?'readonly':''}></td>
        <td class="calc-tt" style="text-align:right; font-weight:700; color:#FF0000 !important">0,00</td>
        <td><input type="text" class="in-no" value="${nợ}" placeholder="..." style="text-align:center; font-size: 12px;" ${!canEdit?'readonly':''}></td>
        <td class="col-action">
            ${canEdit ? `<button class="btn-remove-row" onclick="removeRow(this)" tabindex="-1"><i class="fas fa-times-circle"></i></button>` : ''}
        </td>
    </tr>`;
}

function addRow(calc = true) {
    if(!canEdit) return;
    const tbody = document.getElementById('items-body');
    const stt = tbody.querySelectorAll('tr').length + 1;
    tbody.insertAdjacentHTML('beforeend', createRowHtml(stt));
    if(calc) calcTotal();
}

function removeRow(btn) {
    btn.closest('tr').remove();
    calcTotal();
}

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
            data-dvt="${p.don_vi_tinh ?? ''}"
            data-gia="${p.gia_ban ?? 0}">
            <span class="ac-code">[${p.ma_hang}]</span> ${p.ten_hang}
        </div>`).join('');
    dd.style.display = 'block';
}

function fillProduct(item) {
    const tr = item.closest('tr');
    tr.querySelector('.in-ma').value    = item.dataset.ma;
    tr.querySelector('.in-ten').value   = item.dataset.ten;
    tr.querySelector('.in-dvt').value   = item.dataset.dvt;
    tr.querySelector('.in-gia').value   = formatMoney(item.dataset.gia || 0);

    // Stock Warning Logic
    const ma = item.dataset.ma;
    let warnBox = tr.querySelector('.warn-stock');
    if(!warnBox) {
        warnBox = document.createElement('div');
        warnBox.className = 'warn-stock';
        warnBox.style.cssText = 'font-size:10px;color:red;margin-top:2px';
        tr.querySelector('.in-ma').parentNode.appendChild(warnBox);
    }
    if(STOCK[ma] !== undefined) {
        warnBox.innerText = `Tồn kho: ${STOCK[ma]}`;
        warnBox.style.color = '#3b82f6';
    }

    calcRow(tr.querySelector('.in-sl'));
    item.closest('.prod-autocomplete').style.display = 'none';
}

// Click ra ngoài để đóng popup
document.addEventListener('click', function(e) {
    if (!e.target.closest('.prod-autocomplete') && !e.target.closest('.in-ma')) {
        document.querySelectorAll('.prod-autocomplete').forEach(dd => dd.style.display = 'none');
    }
});

function formatAndCalc(input) {
    let val = input.value.replace(/[^0-9]/g, '');
    input.value = val ? Number(val).toLocaleString('vi-VN') : '';
    calcRow(input);
}

function formatExchangeRate(input) {
    let val = input.value.replace(/[^0-9]/g, '');
    input.value = val ? Number(val).toLocaleString('vi-VN') : '';
}

function calcRow(el) {
    const tr = el.closest('tr');
    const sl = parseLocaleNumber(tr.querySelector('.in-sl').value);
    const gia = parseLocaleNumber(tr.querySelector('.in-gia').value);
    const tt = sl * gia;
    tr.querySelector('.calc-tt').innerText = formatMoney(tt);
    
    // Check stock warning
    const ma = tr.querySelector('.in-ma').value.trim();
    const warnBox = tr.querySelector('.warn-stock');
    if(warnBox && STOCK[ma] !== undefined) {
        if(sl > STOCK[ma]) {
            warnBox.style.color = '#ef4444';
            warnBox.innerHTML = `! Tồn: ${STOCK[ma]} (Thiếu ${sl - STOCK[ma]})`;
        } else {
            warnBox.style.color = '#3b82f6';
            warnBox.innerHTML = `Tồn: ${STOCK[ma]}`;
        }
    }
    
    calcTotal();
}

function calcTotal() {
    let sub = 0;
    document.querySelectorAll('.item-row').forEach(tr => {
        const sl = parseLocaleNumber(tr.querySelector('.in-sl').value);
        const gia = parseLocaleNumber(tr.querySelector('.in-gia').value);
        const tt = sl * gia;
        tr.querySelector('.calc-tt').innerText = formatMoney(tt);
        sub += tt;
    });
    
    const vatPercent = Number(document.getElementById('vat-input').value) || 0;
    const vatVal = sub * (vatPercent/100);
    const total = sub + vatVal;
    
    document.getElementById('sum-subtotal').innerText = formatMoney(sub);
    document.getElementById('sum-vat').innerText = formatMoney(vatVal);
    document.getElementById('sum-total').innerText = formatMoney(total) + ' đ';
}

async function saveOrderDetails() {
    const rows = document.querySelectorAll('.item-row');
    const items = [];
    rows.forEach(tr => {
        const ma = tr.querySelector('.in-ma').value.trim();
        if(ma) {
            items.push({
                ma_hang: ma,
                ten_hang: tr.querySelector('.in-ten').value.trim(),
                mo_ta_phu: tr.querySelector('.in-phu').value.trim(),
                so_luong: parseLocaleNumber(tr.querySelector('.in-sl').value),
                don_vi_tinh: tr.querySelector('.in-dvt').value.trim(),
                don_gia: parseLocaleNumber(tr.querySelector('.in-gia').value),
                cong_no: tr.querySelector('.in-no').value.trim()
            });
        }
    });
    
    const tr_thai = document.getElementById('sel-status').value;
    const data = {
        items,
        vat_percent: document.getElementById('vat-input').value,
        ty_gia: document.getElementById('inp-tygia').value,
        ngay_ty_gia: document.getElementById('inp-ngay-tygia').value,
        trang_thai: tr_thai,
        ghi_chu: document.getElementById('val-ghi-chu')?.value || ''
    };
    
    const btn = document.getElementById('btn-save');
    const old = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Đang lưu...';
    
    try {
        const res = await apiPost('{{ route("orders.save-items", $order->id) }}', data);
        if(res.success) {
            await showToast('Đã lưu toàn bộ thay đổi thành công!');
            location.reload();
        } else {
            showToast(res.message, 'error');
        }
    } catch(e) { 
        console.error(e);
        showToast('Lỗi máy chủ hoặc dữ liệu không hợp lệ', 'error'); 
    }
    finally { btn.innerHTML = old; }
}

function openModalTaoPhieu() {
    openModal('modal-taophieu');
}

async function submitTaoPhieu() {
    const data = {
        cto_code: document.getElementById('dn_cto_code').value,
        delivery_date: document.getElementById('dn_date').value,
        han_thanh_toan: document.getElementById('dn_han').value
    };
    
    try {
        const res = await apiPost('{{ route("deliveries.store") }}', data);
        if(res.success) {
            await showToast('Tạo phiếu thành công!');
            location.reload();
        } else { showToast(res.message, 'error'); }
    } catch(e) { showToast('Lỗi', 'error'); }
}

// Bắt sự kiện double-click vào table để add dòng cho tiện
document.getElementById('table-container')?.addEventListener('dblclick', function(e) {
    if(e.target === this) addRow();
});
</script>
@endpush

