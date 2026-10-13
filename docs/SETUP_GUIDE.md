# 🚀 HƯỚNG DẪN CÀI ĐẶT & TRIỂN KHAI HỆ THỐNG (SETUP GUIDE)

> **Môi trường yêu cầu**:
> - Windows 10/11 hoặc Linux
> - XAMPP (Apache + MySQL 8.x)
> - PHP 8.2 trở lên (bật extension: `curl`, `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`)
> - Composer 2.x
> - Node.js 18+ & npm

---

## 1. CÁC BƯỚC KHỞI TẠO MÔI TRƯỜNG TỪ ĐẦU

### Bước 1: Chuẩn bị CSDL MySQL
1. Khởi động **Apache** và **MySQL** trong bảng điều khiển **XAMPP Control Panel**.
2. Truy cập `http://localhost/phpmyadmin` hoặc mở MySQL CLI và tạo database:
   ```sql
   CREATE DATABASE may_hut_mui CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

### Bước 2: Cấu hình tệp môi trường `.env`
1. Tạo tệp `.env` từ `.env.example`:
   ```powershell
   copy .env.example .env
   ```
2. Cập nhật thông số kết nối CSDL trong `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=may_hut_mui
   DB_USERNAME=root
   DB_PASSWORD=YOUR_PASSWORD
   ```
3. Sinh App Key:
   ```powershell
   php artisan key:generate
   ```

### Bước 3: Chạy Migrations và Dữ liệu mẫu (Seeders)
```powershell
# Chạy toàn bộ bảng dữ liệu
php artisan migrate

# Nạp dữ liệu mẫu ban đầu (Người dùng demo, danh mục, thương hiệu, sản phẩm và biến thể)
php artisan db:seed
```

### Bước 4: Tạo liên kết thư mục Storage (Bắt buộc)
```powershell
php artisan storage:link
```
> **Lưu ý**: Lệnh này giúp ảnh sản phẩm được tải lên từ Admin hiển thị chính xác ra ngoài trang web.

### Bước 5: Cài đặt thư viện Front-end & Biên dịch Assets
```powershell
npm.cmd install
npm.cmd run build
```

---

## 2. TÀI KHOẢN TRẢI NGHIỆM HỆ THỐNG

Sau khi chạy `php artisan db:seed`, bạn có thể đăng nhập ngay với:

| Loại tài khoản | Email đăng nhập | Mật khẩu | Quyền hạn |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@example.com` | `123456` | Toàn quyền quản trị Dashboard, Sản phẩm, Đơn hàng, Vận đơn GHN, Báo cáo Tài chính |
| **Khách hàng Demo** | `test@example.com` | `123456` | Mua sắm, giỏ hàng, checkout, đặt đơn COD/MoMo, theo dõi đơn hàng |

---

## 3. KHỞI CHẠY HỆ THỐNG

Mở terminal tại thư mục dự án `c:\xampp3\htdocs\may-hut-mui`:

```powershell
php artisan serve
```

Truy cập website tại:
- **Trang chủ khách hàng**: `http://127.0.0.1:8000`
- **Trang quản trị Admin**: `http://127.0.0.1:8000/admin`
- **Đăng nhập**: `http://127.0.0.1:8000/login`

---

## 4. XỬ LÝ SỰ CỐ THƯỜNG GẶP (TROUBLESHOOTING)

1. **Lỗi ảnh sản phẩm không hiển thị (Icon hỏng hoặc ảnh mặc định)**:
   - Chạy lại lệnh: `php artisan storage:link`.
2. **Lỗi Vite hoặc mất giao diện CSS**:
   - Chạy lệnh: `npm.cmd run build` rồi `php artisan optimize:clear`.
3. **Lỗi Gửi Email xác thực tài khoản**:
   - Nếu đang chạy test nội bộ không cần gửi mail thật, đổi trong `.env`:
     ```env
     MAIL_MAILER=log
     ```
     Link kích hoạt email sẽ được ghi thẳng vào `storage/logs/laravel.log`.
