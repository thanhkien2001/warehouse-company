<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Xác nhận đơn hàng - {{ $order->cto_code }}</title>
    <style>
        /* PDF specific resets */
        /* In ấn: Đặt lề trực tiếp tại đây để không bị cắt */
        @page {
            size: A4;
            margin: 10mm 15mm 45mm 15mm;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            background: #fff;
            color: #111;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt; /* CHUẨN WORD 8 */
            line-height: 1.3;
        }

        /* Container chuẩn A4 */
        .page {
            width: 100%;
            position: relative;
        }

        /* Header top dùng table */
        .top-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6mm;
        }
        .logo {
            font-size: 8pt;
            font-weight: 700;
            color: #111;
            letter-spacing: -0.2px;
            margin: 0;
            line-height: 1;
        }
        .company-lines {
            font-size: 8pt;
            text-transform: uppercase;
        }
        .topmeta {
            text-align: right;
            font-size: 8pt;
            text-transform: uppercase;
            vertical-align: top;
        }
        .topmeta .code {
            font-size: 10pt;
            margin-bottom:30px;
        }

        /* Title */
        .title {
            text-align: center;
            font-size: 16pt;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin: 3mm 0 5mm 0;
            text-transform: uppercase;
        }

        /* Tables base */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #2a2a2a;
            padding: 3px 6px;
            vertical-align: top;
            word-wrap: break-word;
        }

        /* Top Meta Table */
        .order-meta-table {
            border-collapse: collapse;
        }
        .order-meta-table th {
            font-size: 8pt;
            font-weight: 400; /* KHÔNG IN ĐẬM */
            text-transform: uppercase;
            height: 7mm;
            border: 1px solid #2a2a2a;
        }
        .order-meta-table td {
            font-size: 8pt;
            text-align: center;
            height: 8mm;
            vertical-align: middle;
            border: 1px solid #2a2a2a;
        }

        /* Seller Buyer Split */
        .twocol-table {
            width: 100%;
            margin-top: 4mm;
            border-collapse: collapse;
            border: 1px solid #2a2a2a;
        }
        .twocol-table td {
            padding: 0;
        }
        .box {
            width: 100%;
            border-collapse: collapse;
        }
        .boxhead {
            background: #e3e3e3;
            font-weight: 800;
            text-transform: uppercase;
            text-align: center;
            font-size: 8pt;
            border-bottom: 1px solid #2a2a2a;
            padding: 5px;
        }
        .boxbody {
            padding: 7px 8px;
        }
        .boxbody .company-name {
            font-size: 8pt;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }
        .boxbody .mst-addr {
            font-size: 8pt;
            margin-bottom: 2mm;
        }
        .boxbody .label {
            text-transform: uppercase;
        }
        .boxbody .contact-info {
            font-size: 8pt;
        }
        .boxbody .address-lines {
            padding-left: 32px; /* Thụt lề địa chỉ như mẫu */
            margin-bottom: 2mm;
        }

        /* Items Table - TRẢI RỘNG 100% TRANG */
        .items-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-top: 4mm;
            border: none !important;
        }
        .items-table th {
            font-size: 8pt;
            border-top: 1px solid #000 !important;
            border-bottom: 1px solid #000 !important;
            border-left: none !important;
            border-right: none !important;
            background: #e3e3e3;
            font-weight: 700;
            text-transform: uppercase;
            padding: 7px 2px;
            white-space: nowrap;
            text-align: center; /* CĂN GIỮA TIÊU ĐỀ */
        }
        .items-table td {
            font-size: 8pt;
            border-top: none !important;
            border-bottom: 0.5pt solid #2a2a2a !important;
            border-left: none !important;
            border-right: none !important;
            padding: 6px 6px;
        }
        /* Dòng cuối cùng của bảng có thể kẻ đậm hơn nếu muốn hoặc giữ nguyên */
        .items-table tbody tr:last-child td {
            border-bottom: 1px solid #000 !important;
        }
        .c-center { text-align: center; vertical-align: middle; }
        .c-right { text-align: right; }

        /* Below Items Area */
        .below-table {
            width: 100%;
            margin-top: 2mm;
            border-collapse: collapse;
        }
        .below-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .notes {
            font-size: 8pt;
            margin-top: 8mm;
        }
        .notes-title {
            font-size: 8pt;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }
        .notes ul {
            margin: 0;
            padding-left: 14px;
        }
        .notes li { margin-bottom: 1.2mm; }

        /* Totals */
        .totals-table {
            width: 78mm;
            border-collapse: collapse;
            float: right;
        }
        .totals-table td {
            border: none;
            border-bottom: 1px solid #2a2a2a;
            padding: 1px 0 1px 2mm; /* GIẢM TỐI ĐA PADDING */
            height: 6mm; /* THU HẸP CHIỀU CAO DÒNG */
            text-transform: uppercase;
        }
        .totals-table .label { 
            width: 62%; 
            font-size: 8pt;
            font-weight: 800;
        }
        .totals-table .value { 
            width: 38%; 
            text-align: center; 
            font-size: 8pt;
            font-weight: 800;
        }

        /* Bottom Section */
        .footer-cards {
            position: fixed;
            left: 15mm;
            right: 15mm;
            bottom: 10mm;
            width: calc(100% - 30mm);
            border-collapse: collapse;
        }
        .footer-cards td {
            border: none;
            padding: 0;
            vertical-align: stretch;
        }
        .bcard {
            border: 1px solid #2a2a2a;
            min-height: 38mm;
        }
        .bhead {
            background: #e3e3e3;
            border-bottom: 1px solid #2a2a2a;
            font-weight: 800;
            text-transform: uppercase;
            text-align: center;
            font-size: 8pt;
            height: 7mm;
            padding: 4px;
        }
        .bbody {
            line-height: 1.25;
        }
        .qr-box {
            border: 1px solid #2a2a2a;
            height: 38mm;
            text-align: center;
            vertical-align: middle;
            background: #fff;
        }
        .qr-placeholder {
            width: 32mm;
            height: 32mm;
            border: 1px dashed #777;
            display: inline-block;
            font-size: 7pt;
            text-transform: uppercase;
            color: #444;
            padding-top: 12mm;
        }
        .upper { text-transform: uppercase; }
    </style>
</head>

<body>
    <div class="page">
        <!-- TOP HEADER -->
        <table class="top-table">
            <tr>
                <td style="border:none; padding:0;">
                    <div class="logo">
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(base_path('logo.png'))) }}" style="height: 50px; width: auto;">
                    </div>
                    <div class="company-lines">
                        <div><b>CÔNG TY TNHH GAMBERTE VIỆT NAM</b></div>
                        <div>MST: 0317574324</div>
                        <div>ADD: LẦU 4, 55 BIS NGUYỄN VĂN THỦ, PHƯỜNG TÂN ĐỊNH, TP HCM</div>
                        <div>TEL: 0818 158 519 - 0896 399 225</div>
                        <div style="text-transform: none; opacity: 0.8;">EMAIL: INFO@GAMBERTE.COM</div>
                    </div>
                </td>
                <td class="topmeta" style="border:none; padding-top:70px;">
                    <div class="code">{{ strtoupper($order->cto_code) }}</div>
                    <div style="margin-top:2mm; font-size:7pt;"> NGÀY HIỆU LỰC: <b>{{ $order->order_date?->addDays(30)->format('d/m/Y') ?? now()->addDays(30)->format('d/m/Y') }}</b></div>
                    <div style="font-size:7pt;">PAGE NO: 1 OF 1</div>
                </td>
            </tr>
        </table>

        <!-- TITLE -->
        <div class="title">XÁC NHẬN ĐƠN HÀNG</div>

        <!-- ORDER META TABLE -->
        <table class="order-meta-table">
            <colgroup>
                <col style="width: 16%">
                <col style="width: 16%">
                <col style="width: 20%">
                <col style="width: 12%">
                <col style="width: 18%">
                <col style="width: 18%">
            </colgroup>
            <thead>
                <tr>
                    <th>MÃ KHÁCH HÀNG</th>
                    <th>PO NUMBER</th>
                    <th>NGƯỜI BÁN HÀNG</th>
                    <th>DIST.CH.</th>
                    <th>TÌNH TRẠNG</th>
                    <th>NGÀY IN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $customer->ma_kh }}</td>
                    <td style="font-weight:bold;">{{ strtoupper($order->cto_code) }}</td>
                    <td>{{ strtoupper($order->nguoi_ban ?: '') }}</td>
                    <td>DIRECT</td>
                    <td>{{ strtoupper($order->trang_thai) }}</td>
                    <td>{{ now()->format('d/m/Y') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- SELLER / BUYER -->
        <table class="twocol-table" style="margin-top: 4mm; border: 1px solid #2a2a2a; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; border-right: 1px solid #2a2a2a; border-bottom: 1px solid #2a2a2a; background: #e3e3e3; font-weight: 800; text-transform: uppercase; text-align: center; font-size: 8pt; padding: 5px;">TÊN ĐƠN VỊ BÁN HÀNG</td>
                <td style="width: 50%; border-bottom: 1px solid #2a2a2a; background: #e3e3e3; font-weight: 800; text-transform: uppercase; text-align: center; font-size: 8pt; padding: 5px;">TÊN ĐƠN VỊ MUA HÀNG</td>
            </tr>
            <tr>
                <td style="width: 50%; border-right: 1px solid #2a2a2a; vertical-align: top; padding: 7px 8px;">
                    <div class="company-name" style="font-size: 8pt; font-weight: 800; text-transform: uppercase; margin-bottom: 2mm;">{{ $seller_info['ten'] }}</div>
                    <div class="mst-addr" style="font-size: 8pt; margin-bottom: 2mm;">
                        <div><span class="label">MST:</span> {{ $seller_info['mst'] }}</div>
                        <div style="margin-top:1mm;"><span class="label">ĐỊA CHỈ:</span></div>
                        <div class="address-lines" style="padding-left: 32px; margin-bottom: 2mm;">
                            @php
                                $sellerAddrParts = explode(',', $seller_info['dia_chi']);
                            @endphp
                            @foreach($sellerAddrParts as $sp)
                                <div>{{ strtoupper(trim($sp)) }}</div>
                            @endforeach
                        </div>
                    </div>
                    <div class="contact-info" style="font-size: 8pt;">
                        <div><span class="label">NGƯỜI LIÊN HỆ:</span> {{ strtoupper($order->nguoi_ban ?: 'MR. PHƯỚC') }}</div>
                        <div><span class="label">SỐ ĐIỆN THOẠI:</span> {{ $order->sdt_ban ?: $seller_info['sdt'] }}</div>
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top; padding: 7px 8px;">
                    <div class="company-name" style="font-size: 8pt; font-weight: 800; text-transform: uppercase; margin-bottom: 2mm;">{{ $order->ten_kh }}</div>
                    <div class="mst-addr" style="font-size: 8pt; margin-bottom: 2mm;">
                        <div><span class="label">MST:</span> {{ $customer->ma_so_thue ?: '---' }}</div>
                        <div style="margin-top:1mm;"><span class="label">ĐỊA CHỈ:</span></div>
                        <div class="address-lines" style="padding-left: 32px; margin-bottom: 2mm;">
                            @php
                                $addr = $customer->dia_chi;
                                $addrParts = explode(',', $addr);
                            @endphp
                            @foreach($addrParts as $p)
                                <div>{{ strtoupper(trim($p)) }}</div>
                            @endforeach
                        </div>
                    </div>
                    <div class="contact-info" style="font-size: 8pt;">
                        <div><span class="label">NGƯỜI LIÊN HỆ:</span> {{ strtoupper($order->nguoi_mua ?: '...') }}</div>
                        <div><span class="label">SỐ ĐIỆN THOẠI:</span> {{ $order->sdt_mua ?: '...' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- ITEMS TABLE -->
        <table class="items-table" style="margin-top: 4mm;">
            <colgroup>
                <col style="width: 8%">  <!-- Mã hàng -->
                <col style="width: 42%"> <!-- Mô tả hàng hóa -->
                <col style="width: 10%"> <!-- Số lượng -->
                <col style="width: 10%"> <!-- ĐVT -->
                <col style="width: 15%"> <!-- Đơn giá -->
                <col style="width: 15%"> <!-- Thành tiền -->
            </colgroup>
            <thead>
                <tr>
                    <th style="width: 8%;">MÃ HÀNG</th>
                    <th style="width: 42%;">MÔ TẢ HÀNG HÓA</th>
                    <th style="width: 10%;">SỐ LƯỢNG</th>
                    <th style="width: 10%;">ĐVT</th>
                    <th style="width: 15%;">ĐƠN GIÁ (VND)</th>
                    <th style="width: 15%;">THÀNH TIỀN (VND)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td class="c-center">{{ $item->ma_hang }}</td>
                    <td>
                        <div style="font-weight:bold; text-transform:uppercase;">{{ $item->ten_hang }}</div>
                        @if($item->mo_ta_phu)
                        <div style="font-size:7.5pt; color:#444;">{{ $item->mo_ta_phu }}</div>
                        @endif
                    </td>
                    <td class="c-center">{{ number_format($item->so_luong, 0, ',', '.') }}</td>
                    <td class="c-center">{{ strtoupper($item->don_vi_tinh) }}</td>
                    <td class="c-center">{{ number_format($item->don_gia, 0, ',', '.') }}</td>
                    <td class="c-center">{{ number_format($item->so_luong * $item->don_gia, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- NOTES + TOTALS -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 2mm; border: none !important;">
            <tr>
                <td style="width: 70%; vertical-align: top; padding-top: 40px; border: none !important;">
                    <div class="notes">
                        <div class="notes-title">*** GHI CHÚ:</div>
                        <ul style="list-style-type: none; padding-left: 50px;">
                            <li style="margin-bottom: 1mm;">- TỶ GIÁ: <b>{{ number_format((float)$ty_gia, 0, ',', '.') }} VNĐ </b> – NGÂN HÀNG VIETCOMBANK NGÀY <b>{{ $ngay_ty_gia ?: now()->format('d/m/Y') }} </b></li>
                            <li style="margin-bottom: 1mm;">- THANH TOÁN 100% GIÁ TRỊ ĐƠN HÀNG TRƯỚC KHI GIAO HÀNG</li>
                            <li style="margin-bottom: 1mm;">- ĐỊA CHỈ GIAO HÀNG: THEO YÊU CẦU</li>
                        </ul>
                    </div>
                </td>
                <td style="width: 40%; vertical-align: top; border: none !important;">
                    <table class="totals-table">
                        <tr>
                            <td class="label">TỔNG CỘNG (VND)</td>
                            <td class="value">{{ number_format($subtotal, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="label">THUẾ VAT ({{ $vat_pct }}%)</td>
                            <td class="value">{{ number_format($vat_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="label" style="font-size: 11px; border-bottom: 1px solid #2a2a2a;">TỔNG THANH TOÁN (VND)</td>
                            <td class="value" style="font-size: 11px; border-bottom: 1px solid #2a2a2a;">{{ number_format($total, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- BOTTOM INFO BLOCK -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 10mm; border: 1px solid #2a2a2a; page-break-inside: avoid;">
            <thead>
                <tr>
                    <th style="width: 35%; background: #e3e3e3; border: none !important; border-bottom: 1px solid #2a2a2a !important; padding: 5px; font-size: 8pt; text-align: center; color: #000;">THÔNG TIN THANH TOÁN</th>
                    <th style="width: 15%; background: #e3e3e3; border: none !important; border-bottom: 1px solid #2a2a2a !important;"></th>
                    <th style="width: 50%; background: #e3e3e3; border: none !important; border-bottom: 1px solid #2a2a2a !important; padding: 5px; font-size: 8pt; text-align: center; color: #000;">THÔNG TIN GIAO HÀNG</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 8px; vertical-align: top; border: none !important; font-size: 8pt; line-height: 1.4;">
                        <div style="margin-bottom: 2mm;">{{ strtoupper($seller_info['ten']) }}</div>
                        <div>STK: {{ $seller_info['stk'] }}</div>
                        <div style="margin-top: 2mm;">{{ strtoupper($seller_info['bank']) }}</div>
                        <div>{{ strtoupper($seller_info['branch']) }}</div>
                    </td>
                    <td style="padding: 5px; text-align: center; vertical-align: middle; border: none !important;">
                        <div class="qr-box-img" style="width: 80px; height: 80px; margin: 0 auto;">
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(base_path('qr.png'))) }}" style="width: 100%; height: 100%;">
                        </div>
                    </td>
                    <td style="padding: 8px; vertical-align: top; border: none !important; font-size: 8pt; line-height: 1.4;">
                        <div style="margin-bottom: 1mm;">{{ strtoupper($customer->ten_cty) }}</div>
                        <div style="margin-top: 1mm;">ĐỊA CHỈ: {{ strtoupper($customer->dia_chi_nhan ?: $customer->dia_chi) }}</div>
                        <div style="margin-top: 2mm;">
                            @if($customer->nguoi_lien_he || $customer->sdt_nhan || $customer->sdt)
                                SĐT: {{ strtoupper($customer->nguoi_lien_he) }} - {{ $customer->sdt_nhan ?: $customer->sdt }}
                            @endif
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
</body>
</html>
