# 📝 SỔ TAY TRẠNG THÁI HỆ THỐNG (PROJECT STATE & SESSIONS MEMORY)

> **Mục đích**: Tệp tin này lưu trữ toàn bộ trạng thái hiện tại của hệ thống website Máy Hút Mùi Store, các phân hệ đã hoàn thành, cấu trúc mới và nhật ký cập nhật qua các phiên làm việc.

---

## 1. THÔNG TIN HỆ THỐNG
- **Dự án**: Website Thương Mại Điện Tử Máy Hút Mùi Store
- **Vị trí**: `c:\xampp3\htdocs\may-hut-mui`
- **Phiên bản cấu trúc**: 2.0 Clean Service-Action Architecture
- **Môi trường**: Windows 11 | XAMPP | PHP 8.2 | MySQL 8.x | Vite 7 & TailwindCSS 4
- **Cơ sở dữ liệu**: `may_hut_mui`
- **Thời điểm nâng cấp lớn**: 2026-10-10

---

## 2. NHẬT KÝ NÂNG CẤP & CẢI TỔ (CHANGELOG)

### [2026-10-10] Tái cấu trúc toàn diện & Dọn dẹp thư mục lồng nhau
1. **Dọn dẹp rác & Tối ưu dung lượng**:
   - Đã xóa triệt để thư mục lồng nhau lỗi thời `may-hut-mui\may-hut-mui` (giải phóng ~500MB ổ cứng).
   - Xóa bỏ dòng `may-hut-mui/` khỏi `.gitignore`.
   - Kết nối thành công liên kết symlink `public/storage` với `storage/app/public` qua `php artisan storage:link`.
2. **Nâng cấp tầng Kiến trúc Clean Service-Action Layer**:
   - Tạo mới các Enums chuẩn hóa: `App\Enums\OrderStatus`, `App\Enums\PaymentStatus`, `App\Enums\PaymentGateway`.
   - Xây dựng tầng Business Services độc lập:
     - `App\Services\CartService`: Đóng gói toàn bộ logic giỏ hàng, kiểm tra tồn kho variant, tính tổng tiền.
     - `App\Services\CheckoutService`: Đóng gói quy trình đặt hàng an toàn trong Database Transaction, khóa tồn kho chống Race Condition, tự động tính cước GHN.
     - `App\Services\FinanceService`: Rút gọn `Admin\FinanceController` từ 540 dòng spaghetti xuống còn ~110 dòng, tối ưu hóa toàn bộ truy vấn tài chính.
   - Xây dựng tầng Form Requests: `App\Http\Requests\AddToCartRequest`, `App\Http\Requests\CheckoutRequest`.
3. **Sửa lỗi hiển thị hình ảnh & Cấu hình môi trường**:
   - Khắc phục lỗi hiển thị ảnh sản phẩm trong `Product::imagePath()`: Giờ đây hỗ trợ hoàn hảo cả ảnh tĩnh trong `public/images/` lẫn ảnh tải lên động qua Admin trong `storage/products/`.
   - Chuẩn hóa các biến `MOMO_*` trong `.env` và `.env.example`, loại bỏ khoảng trắng gây lỗi parse.
4. **Chuẩn hóa bộ Tài liệu kỹ thuật**:
   - `docs/ARCHITECTURE.md`: Tài liệu kiến trúc phân tầng kèm sơ đồ luồng Mermaid.
   - `docs/SETUP_GUIDE.md`: Cẩm nang cài đặt từ 0 trên Windows & XAMPP.
   - `docs/API_INTEGRATION.md`: Hướng dẫn tích hợp MoMo và GHN.
   - `README.md`: Trang chủ tài liệu đẹp mắt, chuyên nghiệp.

---

## 3. DANH SÁCH TÀI KHOẢN MẪU SẴN SÀNG SỬ DỤNG

| Tài khoản | Email | Mật khẩu | Quyền hạn |
| :--- | :--- | :--- | :--- |
| **Admin Quản trị** | `admin@example.com` | `123456` | Toàn quyền kiểm soát hệ thống |
| **Khách hàng Demo** | `test@example.com` | `123456` | Trải nghiệm mua sắm, giỏ hàng, đặt đơn |
