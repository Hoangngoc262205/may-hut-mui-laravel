# 🔌 TÀI LIỆU TÍCH HỢP CỔNG THANH TOÁN MOMO & VẬN CHUYỂN GHN

---

## 1. CỔNG THANH TOÁN MOMO (MOMO PAYMENT GATEWAY)

### 1.1. Cấu hình biến môi trường (`.env`)
```env
MOMO_ENDPOINT=https://test-payment.momo.vn/v2/gateway/api/create
MOMO_PARTNER_CODE=MOMOBKUN20180529
MOMO_ACCESS_KEY=klm05TvNBzhg7h7j
MOMO_SECRET_KEY=at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa
MOMO_VERIFY_SSL=false
MOMO_REDIRECT_URL=http://127.0.0.1:8000/payment/momo/callback
MOMO_IPN_URL=http://127.0.0.1:8000/payment/momo/ipn
```

### 1.2. Luồng hoạt động (Payment Workflow)
1. **Khách hàng Checkout**: Chọn hình thức `Ví MoMo`, bấm đặt hàng.
2. **Khởi tạo giao dịch**: `CheckoutService` tạo đơn hàng ở trạng thái `pending` và tạo `PaymentTransaction`.
3. **Redirect MoMo Gateway**: Hệ thống sinh chữ ký SHA256 HMAC bằng `secret_key` và chuyển hướng khách hàng sang cổng thanh toán quét mã QR của MoMo Sandbox.
4. **Xử lý phản hồi (Callback & IPN)**:
   - Khi thanh toán thành công, MoMo gửi webhook ngầm tới `payment/momo/ipn` và chuyển hướng khách về `payment/momo/callback`.
   - `MomoController` xác minh chữ ký `hash_hmac`, chuyển đơn hàng sang trạng thái `paid` và tự động kích hoạt tạo vận đơn GHN.

---

## 2. DỊCH VỤ VẬN CHUYỂN GIAO HÀNG NHANH (GHN API)

### 2.1. Cấu hình biến môi trường (`.env`)
```env
GHN_BASE_URL=https://dev-online-gateway.ghn.vn/shiip/public-api
GHN_TOKEN=ffb8aebd-ad87-11f1-a973-aee5264794df
GHN_SHOP_ID=219641
GHN_VERIFY_SSL=false

# Địa chỉ kho gửi hàng mặc định
GHN_FROM_DISTRICT_ID=1450
GHN_FROM_WARD_CODE=DIEN_MA_PHUONG_XA_KHO_GHN
GHN_FROM_NAME="Kho Máy Hút Mùi Store"
GHN_FROM_PHONE=0900123456
GHN_FROM_ADDRESS="245 Định Công"
GHN_FROM_WARD_NAME="Phường Định Công"
GHN_FROM_DISTRICT_NAME="Quận Hoàng Mai"
GHN_FROM_PROVINCE_NAME="Thành phố Hà Nội"

# Thông số kiện hàng máy hút mùi
GHN_PACKAGE_HEIGHT=20
GHN_PACKAGE_LENGTH=60
GHN_PACKAGE_WIDTH=45
GHN_PACKAGE_WEIGHT=5000
```

### 2.2. Các chức năng tích hợp
1. **Địa chỉ hành chính động**:
   - `GET /locations/provinces`: Tải danh sách 63 tỉnh/thành từ GHN API.
   - `GET /locations/districts/{provinceId}`: Tải quận/huyện tương ứng.
   - `GET /locations/wards/{districtId}`: Tải phường/xã tương ứng.
2. **Tính cước phí thời gian thực**:
   - `POST /locations/calculate-fee`: Tự động tính phí vận chuyển theo khối lượng máy hút mùi (5kg) tới tận địa chỉ khách hàng.
3. **Xuất vận đơn trực tiếp từ Admin**:
   - Quản trị viên duyệt đơn hàng có thể bấm nút **Tạo vận đơn GHN**, hệ thống sẽ gửi dữ liệu sang máy chủ GHN, nhận mã vận đơn (Tracking Code) và tự động cập nhật trạng thái giao dịch.
