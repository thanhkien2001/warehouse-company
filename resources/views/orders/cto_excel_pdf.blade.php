<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>{{ strtoupper($order->cto_code) }}</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'dejavusans', Arial, sans-serif;
    font-size: 9pt;
    color: #000;
}

/* ── HEADER TOP ──────────────────────────── */
.top-wrap {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 4mm;
}
.logo-cell { width: 20%; vertical-align: middle; padding: 0; }
.logo-cell img { height: 25px; width: auto; margin-top: 5px; }
.company-cell { width: 55%; vertical-align: top; padding-left: 4mm; font-size: 8.5pt; line-height: 1.5; text-transform: uppercase; }
.meta-cell { width: 25%; vertical-align: top; font-size: 8.5pt; line-height: 1.5; }

/* ── TITLE ───────────────────────────────── */
.title {
    text-align: center;
    font-size: 18pt;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin: 3mm 0;
}

/* ── INFO BANDS ──────────────────────────── */
table.band {
    width: 100%;
    border-collapse: collapse;
    font-size: 8.5pt;
}
table.band th {
    background: #E7E6E6;
    border: 1px solid #aaa;
    padding: 3px 6px;
    text-align: center;
    text-transform: uppercase;
    font-weight: normal;
}
table.band td {
    border: 1px solid #aaa;
    padding: 3px 6px;
    text-align: center;
}

/* ── SELLER / BUYER ──────────────────────── */
table.two-col {
    width: 100%;
    border-collapse: collapse;
    margin-top: 3mm;
    font-size: 8.5pt;
}
table.two-col .header-cell {
    background: #E7E6E6;
    border: 1px solid #aaa;
    padding: 4px 6px;
    font-weight: bold;
    text-transform: uppercase;
    text-align: center;
}
table.two-col .data-cell {
    border: 1px solid #aaa;
    padding: 5px 8px;
    vertical-align: top;
    line-height: 1.6;
    width: 50%;
}
table.two-col .label { color: #333; font-weight: normal; }

/* ── ITEMS TABLE ─────────────────────────── */
table.items {
    width: 100%;
    border-collapse: collapse;
    margin-top: 3mm;
    font-size: 8.5pt;
}
table.items th {
    background: #E7E6E6;
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    border-left: none;
    border-right: none;
    padding: 6px 5px;
    text-align: center;
    font-weight: normal;
    text-transform: uppercase;
}
table.items td {
    border-bottom: 1px solid #000;
    border-left: none;
    border-right: none;
    border-top: none;
    padding: 6px 5px;
    vertical-align: top;
}
table.items .c { text-align: center; }
table.items .r { text-align: right; }
table.items tbody tr:nth-child(even) { background: transparent; }

/* ── GHI CHÚ + TỔNG ─────────────────────── */
table.bottom {
    width: 100%;
    border-collapse: collapse;
    margin-top: 3mm;
    font-size: 8.5pt;
}
.notes {padding-right: 4mm; line-height: 1.7; }
table.totals { border-collapse: collapse; width: 100%; border: none; }
table.totals td {
    border-bottom: 1px solid #000;
    border-left: none;
    border-right: none;
    border-top: none;
    padding: 6px 8px;
}
table.totals tr:first-child td {
    border-top: 1px solid #000;
}
table.totals .lbl { text-align: left; font-weight: normal; width: 60%; }
table.totals .val { text-align: right; font-weight: normal; width: 40%; }
table.totals tr.total-row td { font-weight: bold; background: #E7E6E6; }

/* ── PAYMENT + DELIVERY ──────────────────── */
table.footer-info {
    width: 100%;
    border-collapse: collapse;
    margin-top: 5mm;
    font-size: 8.5pt;
}
table.footer-info th {
    background: #E7E6E6;
    border-top: 1px solid #aaa;
    border-bottom: 1px solid #aaa;
    border-left: none;
    border-right: none;
    padding: 4px 8px;
    text-align: center;
    font-weight: bold;
    text-transform: uppercase;
}
table.footer-info td {
    border-top: 1px solid #aaa;
    border-bottom: 1px solid #aaa;
    border-left: none;
    border-right: none;
    padding: 5px 8px;
    vertical-align: top;
    line-height: 1.7;
}
</style>
</head>
<body>

{{-- ── HEADER ── --}}
<table class="top-wrap">
  <tr>
    <td class="logo-cell">
      @php $logoPath = base_path('logo.png'); @endphp
      @if(file_exists($logoPath))
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" height="45" style="max-height:45px;">
      @endif
    </td>
    <td class="company-cell">
      <div style="font-size: 11pt; font-weight: bold; margin-bottom: 3px;">CÔNG TY TNHH GAMBERTE VIỆT NAM</div>
      <b>MST: 0 3 1 7 5 7 4 3 2 4</b><br>
      TEL: (+84) 818 518 519 – (+84) 896 399 225<br>
      ĐỊA CHỈ: LẦU 4, 55 BIS NGUYỄN VĂN THỦ, PHƯỜNG ĐA KAO<br>
      QUẬN 1, TP. HỒ CHÍ MINH
    </td>
    <td class="meta-cell">
      <div style="text-align: right; font-size: 11pt; margin-bottom: 12px;">({{ strtoupper($order->cto_code) }})</div>
      <table style="width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-left: auto;">
        <tr>
          <td style="text-align: left; width: 55%; padding: 0;">SERI NO.:</td>
          <td style="text-align: left; width: 45%; padding: 0;">: {{ $order->meta?->seri_no ?? date('Ymd') }}</td>
        </tr>
        <tr>
          <td style="text-align: left; padding: 0;">DOCUMENT DATE</td>
          <td style="text-align: left; padding: 0;">: {{ now()->format('d/m/Y') }}</td>
        </tr>
        <tr>
          <td style="text-align: left; padding: 0;">PAGE NO.</td>
          <td style="text-align: left; padding: 0;">: 1 OF 1</td>
        </tr>
      </table>
    </td>
  </tr>
</table>

{{-- ── TITLE ── --}}
<div class="title">XÁC NHẬN ĐƠN HÀNG</div>

{{-- ── INFO BAND ── --}}
<table class="band">
  <thead>
    <tr>
      <th style="width:20%">MÃ KHÁCH HÀNG</th>
      <th style="width:16%">P.O NUMBER</th>
      <th style="width:21%">NGƯỜI BÁN HÀNG</th>
      <th style="width:12%">DIST.CH.</th>
      <th style="width:17%">TÌNH TRẠNG</th>
      <th style="width:14%">NGÀY IN</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>{{ $customer?->ma_kh ?? '' }}</td>
      <td><b>{{ strtoupper($order->cto_code) }}</b></td>
      <td>{{ strtoupper($order->nguoi_ban ?? auth()->user()->display_name ?? '') }}</td>
      <td>VC</td>
      <td>{{ strtoupper($order->trang_thai ?? 'CHỜ XÁC NHẬN') }}</td>
      <td>{{ now()->format('d/m/Y') }}</td>
    </tr>
  </tbody>
</table>

{{-- ── SELLER / BUYER ── --}}
<table class="two-col">
  <tr>
    <td class="header-cell">TÊN ĐƠN VỊ BÁN HÀNG</td>
    <td class="header-cell">TÊN ĐƠN VỊ MUA HÀNG</td>
  </tr>
  <tr>
    <td class="data-cell">
      <b>CÔNG TY TNHH GAMBERTE VIỆT NAM</b><br>
      MST: 0317574324<br>
      ĐỊA CHỈ:<br>
      &nbsp;&nbsp;LẦU 4, 55 BIS NGUYỄN VĂN THỦ<br>
      &nbsp;&nbsp;PHƯỜNG TÂN ĐỊNH<br>
      &nbsp;&nbsp;TP HỒ CHÍ MINH<br>
      <br>
      CONTACT PERSON: {{ strtoupper($order->nguoi_ban ?? 'MR. PHƯỚC') }}<br>
      TELEPHONE: 0818 518 519
    </td>
    <td class="data-cell">
      <b>{{ strtoupper($customer?->ten_cty ?? '') }}</b><br>
      MST: {{ $customer?->ma_so_thue ?? '' }}<br>
      ĐỊA CHỈ:<br>
      @php
        $addrParts = explode(',', $customer?->dia_chi ?? '');
      @endphp
      @foreach($addrParts as $p)
        &nbsp;&nbsp;{{ strtoupper(trim($p)) }}<br>
      @endforeach
      <br>
      CONTACT PERSON: {{ strtoupper($customer?->nguoi_lien_he ?? '') }}<br>
      TELEPHONE: {{ $customer?->sdt ?? '' }}
    </td>
  </tr>
</table>

{{-- ── BẢNG HÀNG HÓA ── --}}
<table class="items">
  <thead>
    <tr>
      <th style="width:10%">MÃ HÀNG</th>
      <th style="width:36%">MÔ TẢ HÀNG HOÁ</th>
      <th style="width:12%">SỐ LƯỢNG</th>
      <th style="width:8%">ĐVT</th>
      <th style="width:16%">ĐƠN GIÁ (VNĐ)</th>
      <th style="width:18%">THÀNH TIỀN (VNĐ)</th>
    </tr>
  </thead>
  <tbody>
    @foreach($order->items as $item)
    <tr>
      <td class="c">{{ $item->ma_hang }}</td>
      <td>
        <b>{{ $item->ten_hang }}</b>
        @if($item->ghi_chu)<br><span style="font-size:8pt;color:#444;">{{ $item->ghi_chu }}</span>@endif
      </td>
      <td class="c">{{ number_format($item->so_luong, 2) }}</td>
      <td class="c">{{ strtoupper($item->don_vi_tinh ?? 'KG') }}</td>
      <td class="r">{{ number_format($item->don_gia, 0, ',', '.') }}</td>
      <td class="r">{{ number_format($item->so_luong * $item->don_gia, 0, ',', '.') }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

{{-- ── GHI CHÚ + TỔNG ── --}}
<table style="width: 100%; border-collapse: collapse; margin-top: 3mm; font-size: 8.5pt;">
  <tr>
    <td style="width: 60%; vertical-align: top; padding-right: 4mm; padding-top: 65px; line-height: 1.7;">
      *** GHI CHÚ:<br>
      @if($ty_gia)
      &nbsp;- TỶ GIÁ: {{ number_format($ty_gia, 0, ',', '.') }} VND – NGÂN HÀNG VIETCOMBANK NGÀY {{ $ngay_ty_gia ?? now()->format('d/m/Y') }}<br>
      @endif
      &nbsp;- THANH TOÁN 100% GIÁ TRỊ ĐƠN HÀNG TRƯỚC KHI GIAO HÀNG<br>
      &nbsp;- ĐỊA CHỈ GIAO HÀNG: THEO YÊU CẦU
    </td>
    <td style="width: 40%; vertical-align: top;">
      <table class="totals">
        <tr>
          <td class="lbl">TỔNG CỘNG (VNĐ)</td>
          <td class="val">{{ number_format($subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
          <td class="lbl">THUẾ VAT ({{ $vat_pct }}%)</td>
          <td class="val">{{ number_format($vat_amount, 0, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
          <td class="lbl">TỔNG THANH TOÁN (VNĐ)</td>
          <td class="val">{{ number_format($total, 0, ',', '.') }}</td>
        </tr>
      </table>
    </td>
  </tr>
</table>

{{-- ── FOOTER: THANH TOÁN + GIAO HÀNG ── --}}
<table class="footer-info">
  <tr>
    <th style="width:40%">THÔNG TIN THANH TOÁN</th>
    <th style="width:20%;"></th>
    <th style="width:40%">THÔNG TIN GIAO HÀNG</th>
  </tr>
  <tr>
    <td>
      CÔNG TY TNHH GAMBERTE VIỆT NAM<br>
      STK: {{ $seller_info['stk'] }}<br>
      {{ strtoupper($seller_info['bank']) }} – {{ strtoupper($seller_info['branch']) }}
    </td>
    <td style="text-align: center; vertical-align: middle; padding: 5px;">
      @php $qrPath = base_path('qr.png'); @endphp
      @if(file_exists($qrPath))
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($qrPath)) }}" style="width: 80px; height: 80px;">
      @endif
    </td>
    <td>
      {{ strtoupper($customer?->ten_cty ?? '') }}<br>
      {{ strtoupper($customer?->dia_chi ?? '') }}<br>
      {{ $customer?->nguoi_lien_he ?? '' }} – {{ $customer?->sdt ?? '' }}
    </td>
  </tr>
</table>

</body>
</html>
