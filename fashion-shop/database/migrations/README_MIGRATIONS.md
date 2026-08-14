Hướng dẫn ngắn về migrations (tổng hợp)

- Mục tiêu: dùng một migration `2026_05_16_000000_initial_schema.php` cho schema ban đầu, đồng thời lưu backup các migration cũ trong `database/migrations/archived/`.

- Trường hợp triển khai môi trường mới (recommended):
    1. Cài đặt môi trường và biến môi trường `.env`.
    2. Chạy:

        php artisan migrate:fresh --seed --force

    Lệnh này xóa tất cả bảng hiện có và chạy migration mới + seed. Dùng cho môi trường staging/CI hoặc khi chấp nhận reset dữ liệu.

- Trường hợp update production có dữ liệu quan trọng:
    - KHÔNG chạy `migrate:fresh` trực tiếp nếu muốn giữ dữ liệu.
    - Cách an toàn hơn: tạo migration sửa đổi (alter) nhỏ cho các thay đổi cần thiết hoặc dùng `php artisan schema:dump` để quản lý schema snapshot.
    - Bạn có thể giữ `database/migrations/archived/` làm backup nếu cần quay lại.

- Lưu ý quan trọng:
    - Nếu database production đã chạy các migration cũ, xóa các file cũ sẽ khiến lịch sử migration thay đổi. Trước khi xóa file thật, đảm bảo team đồng ý chiến lược migrate (reset DB hoặc tạo migration chuyển đổi).
    - Với việc đã archive file, bạn có thể xóa bản gốc nếu chắc chắn.
