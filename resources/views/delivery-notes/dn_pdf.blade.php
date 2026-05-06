<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>{{ $delivery->dn_code }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'dejavusans', Arial, sans-serif;
        font-size: 9pt;
        color: #000;
        line-height: 1.5;
    }
    @page {
        margin: 10mm 10mm 10mm 10mm;
    }

    /* ── HEADER ──────────────────────────────── */
    .header-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 5mm;
    }
    .logo-cell { width: 20%; vertical-align: middle; padding: 0; }
    .logo-cell img { height: 45px; width: auto; }
    
    .company-cell { 
        width: 50%; 
        vertical-align: middle; 
        padding-left: 2mm; 
        font-size: 8pt;
        line-height: 1.5;
        text-transform: uppercase;
    }
    .company-name { font-size: 11pt; font-weight: bold; margin-bottom: 1mm; }
    
    .meta-cell { 
        width: 30%; 
        vertical-align: middle; 
        font-size: 8pt;
        line-height: 1.5;
    }
    .meta-table { width: 100%; border-collapse: collapse; margin-left: auto; margin-top: 2mm; font-size: 7.5pt; }
    .meta-table td { padding: 0.5mm 0; }

    /* ── TITLE ───────────────────────────────── */
    .title-wrap {
        text-align: center;
        margin: 2.5mm 0 4mm 0;
    }
    .title-main { font-size: 16pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
    .title-date { font-size: 8pt; margin-top: 0.5mm;}
    .title-sub { font-size: 8pt; margin-top: 0.5mm; font-weight: bold; text-transform: uppercase; }

    /* ── BOXES ───────────────────────────────── */
    .box-container {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 4mm;
    }
    .box-cell {
        width: 48%;
        vertical-align: top;
    }
    .box-side-label {
        width: 25px;
        background: #E7E6E6;
        border-right: 1px solid #000;
        vertical-align: middle;
        text-align: center;
        font-weight: bold;
        font-size: 7pt;
        padding: 0;
    }
    .box-content {
        padding: 3mm;
        vertical-align: top;
        line-height: 1.6;
        font-size: 8pt;
    }

    /* ── ITEMS TABLE ─────────────────────────── */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0mm;
    }
    .items-table th {
        background: #E7E6E6;
        border: 1px solid #000;
        padding: 0px 2px;
        font-size: 7.5pt;
        text-align: center;
        font-weight: bold;
    }
    .items-table td {
        border: 1px solid #000;
        padding: 6px 4px;
        font-size: 8pt;
        vertical-align: middle;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-bold { font-weight: bold; }

    /* ── FOOTER ──────────────────────────────── */
    .notes-section {
        margin-top: 5mm;
        font-size: 8pt;
        line-height: 1.8;
    }
    .notes-title { font-weight: bold; margin-bottom: 1.5mm; }
    
    .signature-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 12mm;
    }
    .sig-cell {
        width: 33.33%;
        text-align: center;
        vertical-align: top;
    }
    .sig-label { font-size: 8pt; font-weight: normal; margin-bottom: 15mm; height: 15mm; }
    .sig-name { font-size: 8pt; font-weight: normal; margin-top: 20mm; text-transform: uppercase; }
    .sig-sub { font-size: 8pt; font-weight: bold; display: block; margin-bottom: 8mm; }

    .bottom-date {
        text-align: right;
        margin-top: 10mm;
        font-size: 8pt;
        text-transform: uppercase;
        font-weight: normal;
    }
    .footer-company {
        text-align: center;
        margin-top: 10mm;
        font-size: 8pt;
        text-transform: uppercase;
    }
</style>
</head>
<body>

    @php
        $seller = [
            'name'    => mb_strtoupper(\App\Models\SystemSetting::get('ten_cong_ty', 'CÔNG TY TNHH GAMBERTE VIỆT NAM'), 'UTF-8'),
            'mst'     => \App\Models\SystemSetting::get('ma_so_thue', '0317574324'),
            'address' => \App\Models\SystemSetting::get('dia_chi_cong_ty') ?: 'LẦU 4, 55 BIS NGUYỄN VĂN THỦ, PHƯỜNG ĐA KAO, QUẬN 1, TP HCM',
            'tel'     => \App\Models\SystemSetting::get('sdt_cong_ty') ?: '0818 518 519 - 0896 399 225',
            'email'   => \App\Models\SystemSetting::get('email_cong_ty') ?: 'INFINITY.INGRE@GMAIL.COM'
        ];
        $order = $delivery->order;
        $customer = $delivery->customer;
        $delivery_date = \Carbon\Carbon::parse($delivery->delivery_date);
    @endphp

{{-- ── HEADER ── --}}
<table class="header-table">
    <tr>
        <td class="logo-cell">
            @php $logoPath = base_path('logo.png'); @endphp
            @if(file_exists($logoPath))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" height="45" style="max-height:45px;">
            @endif
        </td>
        <td class="company-cell">
            <div class="company-name">{{ $seller['name'] }}</div>
            MST: 0 3 1 7 5 7 4 3 2 4<br>
            ADD: {{ $seller['address'] }}<br>
            TEL: {{ $seller['tel'] }}<br>
            EMAIL: {{ $seller['email'] }}
        </td>
        <td class="meta-cell">
            <div style="width: 100%; margin-bottom: 2mm;">
                <table style="margin-left: auto; border-collapse: collapse;">
                    <tr>
                        <td style="text-align: center; padding: 0;">
                            <barcode code="{{ $delivery->dn_code }}" type="C128B" height="0.9" text="0" size="0.8" />
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; padding: 0; font-size: 7.5pt; letter-spacing: 2px; font-weight: normal;">
                            {{ strtoupper(implode(' ', str_split($delivery->dn_code))) }}
                        </td>
                    </tr>
                </table>
            </div>
            <table class="meta-table" style="width: 190px; margin-left: auto;">
                <tr>
                    <td style="width: 45%; text-align: left;">ID CUSTOMER:</td>
                    <td style="width: 55%; text-align: left; font-weight: bold;">{{ $customer?->ma_kh }}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">P.O NUMBER:</td>
                    <td style="text-align: left;font-weight: bold;">{{ $delivery->cto_code }}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">SALES PERSON:</td>
                    <td style="text-align: left;font-weight: bold;">{{ mb_strtoupper($order->nguoi_ban ?? '', 'UTF-8') }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- ── TITLE ── --}}
<div class="title-wrap">
    <div class="title-main">DELIVERY NOTE</div>
    <div class="title-date">NGÀY {{ $delivery_date->day }} THÁNG {{ $delivery_date->month }} NĂM {{ $delivery_date->year }}</div>
    <div class="title-sub">SỐ PHIẾU (DELIVERY NO.): {{ $delivery->dn_code }}</div>
</div>

{{-- ── DELIVERY / INVOICE BOXES ── --}}
<table class="box-container" style="table-layout: fixed;">
    <tr>
        <td class="box-cell" style="width: 48%;">
            <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; height: 32mm;">
                <tr>
                    <td class="box-side-label" style="text-rotate: 90; padding: 0; width: 25px; text-align: center; vertical-align: middle;">
                        DELIVERY TO
                    </td>
                    <td class="box-content" style="height: 32mm;">
                        <div class="text-bold">{{ mb_strtoupper($customer?->ten_cty ?? '', 'UTF-8') }}</div>
                        <br>
                        ĐỊA CHỈ: {{ mb_strtoupper($customer?->dia_chi ?? '', 'UTF-8') }}<br>
                        <br>
                        SĐT: {{ $customer?->sdt }}
                    </td>
                </tr>
            </table>
        </td>
        <td style="width: 2%;"></td>
        <td class="box-cell" style="width: 50%;">
            <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; height: 32mm;">
                <tr>
                    <td class="box-side-label" style="text-rotate: 90; padding: 0; width: 25px; text-align: center; vertical-align: middle;">
                        INVOICE TO
                    </td>
                    <td class="box-content" style="height: 32mm;">
                        <div class="text-bold">{{ mb_strtoupper($customer?->ten_cty ?? '', 'UTF-8') }}</div>
                        MST: {{ $customer?->ma_so_thue }}<br>
                        ĐỊA CHỈ: {{ mb_strtoupper($customer?->dia_chi ?? '', 'UTF-8') }}<br>
                        EMAIL: -
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- ── ITEMS TABLE ── --}}
<table class="items-table">
    <thead>
        <tr>
            <th style="width: 4%;">STT<br>NO.</th>
            <th style="width: 12%;">MÃ HÀNG<br>ITEM CODE</th>
            <th style="width: 24%;">MÔ TẢ HÀNG HÓA<br>ITEM DESCRIPTIONS</th>
            <th style="width: 10%;">SỐ LÔ<br>BATCH NO.</th>
            <th style="width: 10%;">HẠN SỬ DỤNG<br>(EXP DATE)</th>
            <th style="width: 10%;">QUY CÁCH<br>SPECIFICATION</th>
            <th style="width: 8%;">SL GIAO<br>QTY</th>
            <th style="width: 6%;">ĐVT<br>UNIT</th>
            <th style="width: 10%;">SỐ QUI ĐỔI<br>ALT. QTY</th>
            <th style="width: 8%;">GHI CHÚ<br>NOTE</th>
        </tr>
    </thead>
    <tbody>
        @php $total_qty = 0; $total_alt = 0; @endphp
        @foreach($delivery->order->items as $index => $item)
            @php 
                $total_qty += $item->so_luong;
                $alt_val = $item->quy_doi > 0 ? ($item->so_luong / $item->quy_doi) : 0;
                $total_alt += $alt_val;
                $alt_unit = $item->quy_cach ? (explode('/', $item->quy_cach)[1] ?? 'PCS') : 'PCS';
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $item->ma_hang }}</td>
                <td>
                    <div class="text-bold">{{ $item->ten_hang }}</div>
                    @if($item->mo_ta_phu)<div style="font-size: 7.5pt; color: #444;">{{ $item->mo_ta_phu }}</div>@endif
                </td>
                <td class="text-center">{{ $item->ma_lot }}</td>
                <td class="text-center">{{ $item->han_su_dung ? \Carbon\Carbon::parse($item->han_su_dung)->format('d/m/Y') : '' }}</td>
                <td class="text-center">{{ $item->quy_cach }}</td>
                <td class="text-center">{{ number_format($item->so_luong, 2) }}</td>
                <td class="text-center">{{ strtoupper($item->don_vi_tinh) }}</td>
                <td class="text-center">{{ number_format($alt_val, 0) }} {{ strtoupper($alt_unit) }}</td>
                <td class="text-center">{{ $item->ghi_chu }}</td>
            </tr>
        @endforeach
        {{-- Row Tổng cộng --}}
        <tr>
            <td colspan="8" class="text-right text-bold" style="text-transform: uppercase; padding-right: 2mm;">TỔNG CỘNG</td>
            <td class="text-center text-bold">{{ number_format($total_alt, 0) }} {{ strtoupper($alt_unit ?? 'PCS') }}</td>
            <td class="text-center text-bold">{{ number_format($total_qty, 0) }} {{ strtoupper($item->don_vi_tinh ?? 'KG') }}</td>
        </tr>
    </tbody>
</table>

{{-- ── FOOTER ── --}}
<div class="notes-section">
    <div class="notes-title">** NOTES:</div>
    <div>- INFINITY SẼ KHÔNG CHẤP NHẬN VIỆC ĐỔI/ TRẢ HÀNG SAU 07 NGÀY KỂ TỪ NGÀY GIAO HÀNG</div>
    <div>- HÀNG ĐỔI/ TRẢ TRONG THỜI GIAN QUY ĐỊNH PHẢI CÒN NGUYÊN ĐAI, NGUYÊN KIỆN./.</div>
</div>

<table class="signature-table">
    <tr>
        <td class="sig-cell">
            <div class="sig-label">PREPARED BY (NGƯỜI LẬP PHIẾU)</div>
            @if($delivery->nguoi_tao)
                <br><br><br>
                <div class="sig-name">{{ $delivery->nguoi_tao }}</div>
            @else
                <br><br><br><br>
            @endif
        </td>
        <td class="sig-cell">
            <div class="sig-label">DELIVER BY (NGƯỜI GIAO HÀNG)</div>
            <br><br><br><br>
        </td>
        <td class="sig-cell">
            <div class="sig-label">RECEIVED BY (NGƯỜI NHẬN HÀNG)</div>
            <span class="sig-sub">ĐÃ NHẬN ĐỦ SỐ LƯỢNG NHƯ TRÊN</span>
            <br><br><br>
            <div class="bottom-date">NGÀY {{ $delivery_date->day }} THÁNG {{ $delivery_date->month }} NĂM {{ $delivery_date->year }}</div>
        </td>
    </tr>
</table>

<div class="footer-company">({{ $seller['name'] }})</div>

</body>
</html>
