<div id="modal-thutien" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modal-thutien-title"><i class="fas fa-hand-holding-usd" style="color:var(--success)"></i> Ghi Nhận Thanh Toán</h3>
            <button class="modal-close" onclick="closeModal('modal-thutien')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div id="tt-error" style="display:none;background:#fef2f2;color:#991b1b;padding:12px;border-radius:8px;margin-bottom:16px;font-size:13.5px;border-left:4px solid #ef4444"></div>
            
            <div class="form-group" style="position:relative">
                <label class="form-label">Mã Đơn Hàng (CTO) <span>*</span></label>
                <input type="text" id="tt_cto_code" class="form-control" placeholder="Gõ CTO-..." style="font-weight:700;color:var(--primary)" autocomplete="off">
            </div>

            <div id="tt_info_box" style="display:none;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:16px;margin-bottom:20px">
                <div style="font-size:14.5px;font-weight:700;color:var(--text);margin-bottom:12px" id="lbl_ten_kh">---</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:13.5px">
                    <div style="color:#64748b">Tổng đơn (có VAT): <div style="color:#0f172a;font-weight:700;font-size:15px;margin-top:2px" id="lbl_tong_don">0</div></div>
                    <div style="color:#64748b">Số tiền còn nợ: <div style="color:#ef4444;font-weight:900;font-size:16px;margin-top:2px" id="lbl_con_no">0</div></div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Số tiền khách thanh toán <span>*</span></label>
                <input type="text" id="tt_so_tien" class="form-control" style="font-size:22px;font-weight:900;color:var(--success);padding:14px" placeholder="0" oninput="let v=this.value.replace(/[^0-9]/g,'');this.value=v?Number(v).toLocaleString('vi-VN'):''">
                <div style="display:flex;gap:8px;margin-top:8px">
                    <button class="btn btn-outline btn-sm" style="border-radius:20px" onclick="fillSoTien('all')">Khai hết nợ</button>
                    <button class="btn btn-outline btn-sm" style="border-radius:20px" onclick="fillSoTien('50')">Khai 50%</button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Ghi chú thêm</label>
                <textarea id="tt_ghi_chu" class="form-control" rows="2" placeholder="Nội dung chuyển khoản..."></textarea>
            </div>
            
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="closeModal('modal-thutien')">Hủy bỏ</button>
            <button id="btn-save-tt" class="btn btn-success" onclick="submitThuTien()" disabled><i class="fas fa-check"></i> Lưu Lịch Sử Thanh Toán</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentDebt = 0;

function openModalThuTien(ctoCode = '') {
    document.getElementById('tt_cto_code').value = '';
    document.getElementById('tt_so_tien').value = '';
    document.getElementById('tt_ghi_chu').value = '';
    document.getElementById('tt_info_box').style.display = 'none';
    document.getElementById('btn-save-tt').disabled = true;
    document.getElementById('tt-error').style.display = 'none';
    
    currentDebt = 0;
    
    // Nạp orders cho autocomplete
    @if(isset($orders))
        initAutocomplete(
            document.getElementById('tt_cto_code'),
            @json($orders),
            'cto_code',
            item => item.cto_code + ' - ' + item.ten_kh,
            item => loadDebtInfo(item.cto_code)
        );
    @endif
    
    openModal('modal-thutien');
    
    if (ctoCode) {
        document.getElementById('tt_cto_code').value = ctoCode;
        loadDebtInfo(ctoCode);
    }
}

async function loadDebtInfo(ctoCode) {
    if(!ctoCode) return;
    try {
        const res = await fetch(`/thanh-toan/api/debt/${ctoCode}`, {headers:{'Accept':'application/json'}}).then(r=>r.json());
        if(res.error) {
            document.getElementById('tt-error').innerHTML = res.error;
            document.getElementById('tt-error').style.display = 'block';
            document.getElementById('tt_info_box').style.display = 'none';
            document.getElementById('btn-save-tt').disabled = true;
            return;
        }
        
        document.getElementById('tt-error').style.display = 'none';
        document.getElementById('lbl_ten_kh').innerText = res.ten_kh + ' (' + res.ma_kh + ')';
        document.getElementById('lbl_tong_don').innerText = Number(res.tong_don).toLocaleString('vi-VN');
        document.getElementById('lbl_con_no').innerText = Number(res.con_lai).toLocaleString('vi-VN');
        
        currentDebt = res.con_lai;
        if(res.con_lai <= 0) {
            document.getElementById('tt-error').innerHTML = 'Khách hàng này đã thanh toán xong đơn hàng! Không còn nợ.';
            document.getElementById('tt-error').style.display = 'block';
            document.getElementById('btn-save-tt').disabled = true;
        } else {
            document.getElementById('btn-save-tt').disabled = false;
        }
        document.getElementById('tt_info_box').style.display = 'block';
    } catch(e) {
        document.getElementById('tt-error').innerHTML = 'Không kết nối được server';
        document.getElementById('tt-error').style.display = 'block';
    }
}

function fillSoTien(type) {
    if(currentDebt <= 0) return;
    let amount = type === 'all' ? currentDebt : Math.round(currentDebt / 2);
    document.getElementById('tt_so_tien').value = amount.toLocaleString('vi-VN');
}

async function submitThuTien() {
    const data = {
        cto_code: document.getElementById('tt_cto_code').value,
        so_tien: (document.getElementById('tt_so_tien').value).replace(/\./g,''),
        ghi_chu: document.getElementById('tt_ghi_chu').value
    };
    
    const btn = document.getElementById('btn-save-tt');
    btn.innerHTML = '<i class="fas fa-spinner spin"></i> Đang lưu...'; btn.disabled = true;
    
    try {
        const res = await apiPost('{{ route("payments.store") }}', data);
        if(res.success) {
            closeModal('modal-thutien');
            showToast(res.message);
            setTimeout(()=>location.reload(), 800);
        } else {
            let msg = res.message;
            if(res.errors) msg = Object.values(res.errors).flat().join('<br>');
            document.getElementById('tt-error').innerHTML = msg;
            document.getElementById('tt-error').style.display = 'block';
            btn.innerHTML = '<i class="fas fa-check"></i> Lưu Lịch Sử Thanh Toán'; btn.disabled = false;
        }
    } catch(e) {
        document.getElementById('tt-error').innerHTML = 'Lỗi máy chủ';
        document.getElementById('tt-error').style.display = 'block';
        btn.innerHTML = '<i class="fas fa-check"></i> Lưu Lịch Sử Thanh Toán'; btn.disabled = false;
    }
}
</script>
@endpush
