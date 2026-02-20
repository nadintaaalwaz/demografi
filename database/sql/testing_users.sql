-- SQL Commands untuk Testing (Optional)
-- Jalankan di TablePlus atau MySQL Client

-- 1. Buat database (jika belum ada)
CREATE DATABASE IF NOT EXISTS demografi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Gunakan database
USE demografi;

-- 3. Cek tabel users setelah migration
DESCRIBE users;

-- 4. Insert user manual untuk testing (alternative dari seeder)

-- User Kasi
INSERT INTO users (username, password, nama, role, id_dusun, created_at, updated_at) VALUES
('kasi', '$2y$12$LzJM8j3zfFQ.x7.FH8YWZ.XKPxXvZ2Q6wW5VvJ.KN5qQEJ.P0Z0S6', 'Kepala Seksi Pemerintahan', 'kasi', NULL, NOW(), NOW());
-- Password: password123

-- User Kasun (sesuaikan id_dusun dengan tabel wilayah Anda)
INSERT INTO users (username, password, nama, role, id_dusun, created_at, updated_at) VALUES
('kasun1', '$2y$12$LzJM8j3zfFQ.x7.FH8YWZ.XKPxXvZ2Q6wW5VvJ.KN5qQEJ.P0Z0S6', 'Kepala Dusun Mawar', 'kasun', 1, NOW(), NOW()),
('kasun2', '$2y$12$LzJM8j3zfFQ.x7.FH8YWZ.XKPxXvZ2Q6wW5VvJ.KN5qQEJ.P0Z0S6', 'Kepala Dusun Melati', 'kasun', 2, NOW(), NOW()),
('kasun3', '$2y$12$LzJM8j3zfFQ.x7.FH8YWZ.XKPxXvZ2Q6wW5VvJ.KN5qQEJ.P0Z0S6', 'Kepala Dusun Anggrek', 'kasun', 3, NOW(), NOW()),
('kasun4', '$2y$12$LzJM8j3zfFQ.x7.FH8YWZ.XKPxXvZ2Q6wW5VvJ.KN5qQEJ.P0Z0S6', 'Kepala Dusun Kenanga', 'kasun', 4, NOW(), NOW()),
('kasun5', '$2y$12$LzJM8j3zfFQ.x7.FH8YWZ.XKPxXvZ2Q6wW5VvJ.KN5qQEJ.P0Z0S6', 'Kepala Dusun Dahlia', 'kasun', 5, NOW(), NOW());
-- Password semua: password123

-- 5. Query untuk testing

-- Lihat semua user
SELECT id, username, nama, role, id_dusun FROM users;

-- Lihat user kasi
SELECT * FROM users WHERE role = 'kasi';

-- Lihat user kasun
SELECT * FROM users WHERE role = 'kasun';

-- Lihat kasun dengan dusun tertentu
SELECT u.*, w.nama as nama_dusun 
FROM users u 
LEFT JOIN wilayah w ON u.id_dusun = w.id 
WHERE u.role = 'kasun';

-- Update password user (jika diperlukan)
-- UPDATE users SET password = '$2y$12$LzJM8j3zfFQ.x7.FH8YWZ.XKPxXvZ2Q6wW5VvJ.KN5qQEJ.P0Z0S6' WHERE username = 'kasi';

-- Delete user
-- DELETE FROM users WHERE username = 'username_yang_akan_dihapus';

-- Reset auto increment
-- ALTER TABLE users AUTO_INCREMENT = 1;
