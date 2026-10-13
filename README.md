# 🍳 MÁY HÚT MÙI STORE - E-COMMERCE PLATFORM (LARAVEL 12)

<div align="center">

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![MoMo](https://img.shields.io/badge/Payment-MoMo%20Gateway-A50064?style=for-the-badge)
![GHN](https://img.shields.io/badge/Shipping-Giao%20Hàng%20Nhanh-F6821F?style=for-the-badge)
![Tests](https://img.shields.io/badge/Tests-Passed%20(100%25)-22c55e?style=for-the-badge)

**Hệ thống website thương mại điện tử chuyên ngành Thiết Bị Máy Hút Mùi Cao Cấp chuẩn Châu Âu**  
*Tích hợp Thanh toán MoMo · Vận chuyển GHN thời gian thực · Quản lý biến thể đa chiều · Chống Race Condition đặt hàng · Xác thực Email chuẩn Brand*

---

**Tác giả:** [Hoàng Ngọc (Hoangngoc262205)](https://github.com/Hoangngoc262205)  
**Email liên hệ:** [hoangnhungoc2462005@gmail.com](mailto:hoangnhungoc2462005@gmail.com)

</div>

---

## 📌 TỔNG QUAN DỰ ÁN (PROJECT OVERVIEW)

**Máy Hút Mùi Store** là giải pháp thương mại điện tử hoàn chỉnh được thiết kế và xây dựng theo mô hình kiến trúc phần mềm hiện đại với **Laravel 12 (MVC + Service Layer)**. Dự án đáp ứng các tiêu chuẩn khắt khe về hiệu năng, bảo mật và trải nghiệm người dùng cao cấp, phục vụ các thương hiệu thiết bị nhà bếp hàng đầu: **Bosch (Đức), Hafele (Đức), Malloca (Tây Ban Nha), Canzy (Ý), Sunhouse (Việt Nam), Electrolux (Thụy Điển), Kaff (Đức), Teka...**

### 🎯 Điểm Sáng Kỹ Thuật Dành Cho Nhà Tuyển Dụng (Key Highlights)
1. **Kiến trúc phân tầng rõ ràng (Separation of Concerns)**:
   - **Controller mỏng (Thin Controllers)**: Chỉ tiếp nhận Request và trả về Response/View.
   - **Tầng nghiệp vụ (Service Layer)**: Toàn bộ Business Logic phức tạp về Giỏ hàng, Đặt hàng, Vận chuyển GHN, Thanh toán MoMo, Báo cáo Tài chính được đóng gói riêng biệt trong `app/Services/`.
   - **Form Requests**: Xác thực dữ liệu đầu vào chặt chẽ (`AddToCartRequest`, `CheckoutRequest`, `StoreProductRequest`...).
   - **Enums**: Chuẩn hóa kiểu dữ liệu nghiệp vụ (`OrderStatus`, `PaymentStatus`, `PaymentGateway`).
2. **Bảo toàn dữ liệu & Chống Race Condition**:
   - Sử dụng `DB::transaction()` kết hợp `lockForUpdate()` trên `ProductVariant` trong quá trình thanh toán, ngăn ngừa triệt để tình trạng bán vượt tồn kho (Overselling) khi có nhiều khách mua đồng thời.
3. **Quy trình xác thực Email chuẩn Doanh Nghiệp**:
   - Tùy biến `VerifyEmail::toMailUsing()` với mẫu thư sang trọng mang thương hiệu Máy Hút Mùi Store.
   - Giao diện xác thực `/email/verify` chuyên nghiệp: Mã hóa hiển thị email, phím tắt 1-chạm mở Gmail/Outlook, cơ chế đếm ngược 60 giây chống spam.
4. **Quản lý tồn kho & Nhập hàng nhanh (Quick Restock)**:
   - Admin tạo sản phẩm trọn gói (Ảnh + Thông số + Biến thể + Giá + Kho) chỉ trong 1 màn hình.
   - Hệ thống cảnh báo tự động `⛔ Hết hàng (0)` và cơ chế **1-Click Quick Restock** bổ sung kho ngay lập tức.
5. **Logistics & Thanh toán thực tế**:
   - Tích hợp API **Giao Hàng Nhanh (GHN)**: Tính phí ship tự động theo địa giới hành chính và cân nặng kiện hàng, đẩy vận đơn sang hệ thống bưu cục 1-click.
   - Tích hợp cổng thanh toán trực tuyến **Ví MoMo**: Cơ chế tạo mã QR thanh toán, kiểm tra chữ ký số HMAC-SHA256, xử lý Callback & Webhook IPN an toàn.
6. **Kiểm thử tự động (Automated Testing)**:
   - Bộ Feature Tests tự động bao phủ luồng Thêm giỏ hàng, Phát hiện hết kho, Đặt hàng & Trừ kho an toàn.

---

## 🏗️ CẤU TRÚC KIẾN TRÚC THƯ MỤC (ARCHITECTURE)

```
may-hut-mui/
├── app/
│   ├── Enums/                     # Định nghĩa hằng số kiểu Enum (OrderStatus, PaymentStatus...)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/             # Điều phối Quản trị: Dashboard, Sản phẩm, Đơn hàng, Vận chuyển, Tài chính
│   │   │   └── Customer/          # Điều phối Cửa hàng: Trang chủ, Sản phẩm, Giỏ hàng, Checkout, MoMo
│   │   ├── Requests/              # Form Requests tiền xử lý & kiểm tra hợp lệ dữ liệu
│   │   └── Middleware/            # Phân quyền Người dùng, Xác thực Email, Quyền Quản trị viên
│   ├── Models/                    # Eloquent Models & Quan hệ dữ liệu (Product, Variant, Order, Review...)
│   ├── Providers/                 # Cấu hình Service Provider & Tùy biến Mail Xác thực
│   └── Services/                  # ⭐ TẦNG NGHIỆP VỤ CỐT LÕI (Business Logic Layer)
│       ├── CartService.php        # Quản lý giỏ hàng, tính giá & xác thực tồn kho
│       ├── CheckoutService.php    # Giao dịch đặt hàng, khóa kho chống tranh chấp
│       ├── FinanceService.php     # Phân tích tài chính, báo cáo dòng tiền & tỷ lệ COD/MoMo
│       ├── GHNService.php         # Tích hợp API Tỉnh/Huyện/Xã & Tính phí ship GHN
│       ├── GHNOrderService.php    # Quản lý & khởi tạo mã vận đơn GHN
│       └── MomoService.php        # Tích hợp Cổng thanh toán MoMo (HMAC-SHA256)
│
├── database/
│   ├── migrations/                # Cấu trúc CSDL chuẩn hóa (3NF)
│   └── seeders/                   # Seeder dữ liệu mẫu phong phú & 10 sản phẩm bán chạy nhất
│
├── docs/                          # 📚 Bộ tài liệu kiến trúc chuyên sâu
│   ├── ARCHITECTURE.md            # Sơ đồ thiết kế hệ thống & Luồng dữ liệu (Data Flow)
│   ├── SETUP_GUIDE.md             # Hướng dẫn triển khai chi tiết
│   ├── API_INTEGRATION.md         # Tài liệu tích hợp API MoMo Sandbox & GHN
│   └── PROJECT_STATE.md           # Sổ tay nhật ký phát triển
│
├── public/                        # Web root, tài nguyên tĩnh & hình ảnh sản phẩm
├── resources/                     # Giao diện Blade Views, CSS & Scripts
└── tests/                         # Automated Unit & Feature Tests
```

---

## ⚡ HƯỚNG DẪN CÀI ĐẶT & CHẠY LOCAL (GETTING STARTED)

### 1. Yêu cầu hệ thống
- **PHP**: >= 8.2 (Bật các extension: `pdo_mysql`, `curl`, `mbstring`, `openssl`, `fileinfo`)
- **Composer**: >= 2.x
- **Node.js**: >= 18.x & NPM
- **MySQL**: >= 8.0 hoặc MariaDB >= 10.4

### 2. Các bước cài đặt
```bash
# 1. Clone dự án từ GitHub
git clone https://github.com/Hoangngoc262205/may-hut-mui-laravel.git
cd may-hut-mui-laravel

# 2. Cài đặt các thư viện PHP
composer install

# 3. Cài đặt các gói giao diện
npm install

# 4. Sao chép tệp cấu hình môi trường
copy .env.example .env

# 5. Khởi tạo Application Key
php artisan key:generate

# 6. Cấu hình CSDL trong tệp .env, sau đó chạy migration và nạp dữ liệu mẫu:
php artisan migrate --seed

# 7. Tạo liên kết thư mục lưu trữ hình ảnh
php artisan storage:link

# 8. Biên dịch giao diện
npm run build

# 9. Khởi chạy máy chủ phát triển
php artisan serve
```

---

## 🔑 TÀI KHOẢN TRẢI NGHIỆM DEMO (DEMO CREDENTIALS)

Nhà tuyển dụng có thể đăng nhập ngay với các tài khoản được tạo sẵn sau khi seed dữ liệu:

| Phân Quyền | Tài Khoản Đăng Nhập | Mật Khẩu | Quyền Hạn & Tính Năng |
| :--- | :--- | :---: | :--- |
| **Quản trị viên (Admin)** | `admin@example.com` | `123456` | Toàn quyền Quản lý kho, Duyệt đơn, Tạo vận đơn GHN, Nhập kho nhanh, Báo cáo doanh thu |
| **Khách hàng (Customer)** | `test@example.com` | `123456` | Đã xác thực Email: Mua sắm, Đặt hàng COD / MoMo, Xem lịch sử đơn, Đánh giá sản phẩm |
| **Khách hàng mẫu** | `tuananh.nguyen@example.com` | `123456` | Khách hàng thân thiết có lịch sử mua các dòng máy Bosch cao cấp |

- **Trang chủ Khách hàng**: `http://127.0.0.1:8000`
- **Trang Quản trị Admin**: `http://127.0.0.1:8000/admin`

---

## 🧪 CHẠY KIỂM THỬ TỰ ĐỘNG (AUTOMATED TESTING)

Dự án được viết sẵn bộ kiểm thử tích hợp (Feature Tests) kiểm tra các kịch bản thực tế quan trọng:
```bash
php artisan test
```

**Kết quả kiểm thử:**
```
 PASS  Tests\Unit\ExampleTest
 ✓ that true is true

 PASS  Tests\Feature\CartAndCheckoutTest
 ✓ cart service can add item and detect stock                                  0.21s  
 ✓ checkout service creates order and decrements stock                         0.34s  

 PASS  Tests\Feature\ExampleTest
 ✓ the application returns a successful response                               0.04s  

Tests:    4 passed (10 assertions)
Duration: 0.77s
```

---

## 🛡️ BẢO MẬT & TỐI ƯU HÓA (SECURITY & PERFORMANCE)

- **CSRF Protection**: Bảo vệ toàn bộ form gửi dữ liệu.
- **XSS & SQL Injection Defense**: Sử dụng Eloquent ORM Binding và Blade Templating an toàn.
- **Signed Email Verification URLs**: URL kích hoạt tài khoản có chữ ký số mã hóa và thời hạn hết hạn.
- **Database Indexing**: Đánh chỉ mục (Index) trên các cột thường xuyên tìm kiếm & lọc: `slug`, `category_id`, `brand_id`, `sku`, `status`, `created_at`.
- **Eager Loading**: Ngăn ngừa triệt để vấn đề truy vấn N+1 khi hiển thị danh sách sản phẩm và đơn hàng.

---

<div align="center">

Dự án được xây dựng với tâm huyết và cam kết chất lượng mã nguồn cao nhất.  
*Cảm ơn Quý Nhà Tuyển Dụng đã quan tâm và đánh giá dự án!*

</div>
