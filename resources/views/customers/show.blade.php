@extends('layouts.app')
@section('title', 'Chi tiết Khách Hàng')
@section('page-title', 'Hồ sơ Khách Hàng')

@section('content')
<a href="{{ route('customers.index') }}" style="display: inline-block; background: #fff; border: 1px solid #cbd5e1; padding: 8px 18px; border-radius: 50px; color: #475569; font-weight: 600; cursor: pointer; margin-bottom: 20px; transition: 0.2s; box-shadow: 0 2px 5px rgba(0,0,0,0.02); text-decoration: none;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
    <i class="fas fa-arrow-left" style="margin-right: 6px;"></i> Trở lại
</a>

<div style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); padding: 25px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 25px;">
    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #cbd5e1;">
        <div style="background: #e0e7ff; color: #10568f; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fas fa-building"></i>
        </div>
        <div>
            <h2 id="ctkh-ten" style="margin: 0; font-size: 20px; color: #000000; font-weight: 800;">{{ $customer->ten_cty }}</h2>
            <div style="color: #000000; font-weight: 700; font-size: 14px; margin-top: 4px;">ID: <span id="ctkh-ma">{{ $customer->ma_kh }}</span></div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 13.5px; color: #000000;">
        <div>
            <p style="margin: 0 0 12px 0;"><span style="font-weight: 600; display: inline-block; width: 120px;">Mã số thuế:</span> <b id="ctkh-mst" style="color: #000000; font-size: 14px;">{{ $customer->ma_so_thue }}</b></p>
            <p style="margin: 0 0 12px 0;"><span style="font-weight: 600; display: inline-block; width: 120px;">Email:</span> <span id="ctkh-email">{{ $customer->email ?: '---' }}</span></p>
            <p style="margin: 0 0 12px 0;"><span style="font-weight: 600; display: inline-block; width: 120px;">Địa chỉ xuất HĐ:</span> <span id="ctkh-diachi">{{ $customer->dia_chi ?: '---' }}</span></p>
        </div>
        <div>
            <p style="margin: 0 0 12px 0;"><span style="font-weight: 600; display: inline-block; width: 130px;">Người liên hệ:</span> <b id="ctkh-nguoilienhe" style="color: #000000;">{{ $customer->nguoi_lien_he ?: '---' }}</b></p>
            <p style="margin: 0 0 12px 0;"><span style="font-weight: 600; display: inline-block; width: 130px;">Số điện thoại:</span> <b id="ctkh-sdt" style="color: #000000; font-size: 14px;">{{ $customer->sdt ?: '---' }}</b></p>
            <p style="margin: 0 0 12px 0;"><span style="font-weight: 600; display: inline-block; width: 130px;">SĐT nhận hàng:</span> <span id="ctkh-sdtnhan">{{ $customer->sdt_nhan ?: '---' }}</span></p>
            <p style="margin: 0 0 12px 0;"><span style="font-weight: 600; display: inline-block; width: 130px;">Địa chỉ giao hàng:</span> <span id="ctkh-diachinhan">{{ $customer->dia_chi_nhan ?: '---' }}</span></p>
        </div>
    </div>
</div>

<div style="margin-top: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 16px; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px; font-family: 'Inter', sans-serif;">
        <h3 style="margin: 0; color: #000000; font-size: 18px; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-history" style="color: #f59e0b;"></i> Lịch sử mua hàng
        </h3>
    </div>

    <div style="overflow-x: auto; background: #fff; font-family: 'Inter', sans-serif; font-size: 13px;">
        <table style="width: 100%; min-width: 800px; border-collapse: collapse;">
            <thead style="background: #f8fafc; color: #000000; font-weight: 700; text-transform: uppercase; font-size: 12px;">
                <tr>
                    <th style="padding: 16px 15px; width: 5%; border-bottom: 2px solid #e2e8f0; text-align: center;">STT</th>
                    <th style="padding: 16px 15px; width: 15%; border-bottom: 2px solid #e2e8f0; text-align: center;">Ngày tạo</th>
                    <th style="padding: 16px 15px; width: 20%; border-bottom: 2px solid #e2e8f0; text-align: center;">Mã Đơn (CTO)</th>
                    <th style="padding: 16px 15px; width: 40%; border-bottom: 2px solid #e2e8f0; text-align: left;">Ghi chú đơn hàng</th>
                    <th style="padding: 16px 15px; width: 20%; border-bottom: 2px solid #e2e8f0; text-align: center;">Trạng thái</th>
                </tr>
            </thead>
            <tbody id="ctkh-ds-donhang">
                @forelse($orders as $key => $order)
                <tr>
                    <td style="padding: 16px 15px; border-bottom: 1px solid #f1f5f9; text-align: center;">{{ $loop->iteration }}</td>
                    <td style="padding: 16px 15px; border-bottom: 1px solid #f1f5f9; text-align: center;">{{ $order->order_date?->format('d/m/Y') }}</td>
                    <td style="padding: 16px 15px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                        <a href="{{ route('orders.show', $order->id) }}" style="color: #10568f; text-decoration: none; font-weight: bold;">{{ $order->cto_code }}</a>
                    </td>
                    <td style="padding: 16px 15px; border-bottom: 1px solid #f1f5f9;">{{ $order->ghi_chu ?: '---' }}</td>
                    <td style="padding: 16px 15px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                        @if($order->status == 'processing')
                            <span style="background: #fffbeb; color: #d97706; padding: 4px 10px; border-radius: 20px; font-weight: 600; font-size: 11px;">Đang xử lý</span>
                        @elseif($order->status == 'completed')
                            <span style="background: #f0fdf4; color: #16a34a; padding: 4px 10px; border-radius: 20px; font-weight: 600; font-size: 11px;">Hoàn thành</span>
                        @elseif($order->status == 'cancelled')
                            <span style="background: #fef2f2; color: #dc2626; padding: 4px 10px; border-radius: 20px; font-weight: 600; font-size: 11px;">Đã hủy</span>
                        @else
                            <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 20px; font-weight: 600; font-size: 11px;">{{ $order->status }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 30px; text-align: center; color: #94a3b8;">Khách hàng này chưa có đơn hàng nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; margin-bottom: 30px; font-family: 'Inter', sans-serif;">
        <div style="color: #64748b; font-size: 13.5px;">Hiển thị {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} mục (Tổng: {{ $orders->total() }})</div>
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="display: flex; gap: 4px;">
                {{ $orders->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
