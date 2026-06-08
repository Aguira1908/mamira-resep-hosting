-- ============================================================
-- MAMIRA RESEP - ORACLE DATABASE SCRIPT
-- ============================================================
-- File ini dibuat dari hasil reverse-engineering SELURUH
-- source code PHP di sistem Mamira Resep (Catering Online).
--
-- Koneksi yang digunakan aplikasi (config/koneksi.php):
--   Username : system
--   Password : 111006
--   TNS      : localhost/FREEPDB1
--
-- CATATAN: Aplikasi menggunakan user "system" untuk koneksi.
--   Untuk keamanan, sebaiknya buat user terpisah.
--   Namun file ini dibuat sesuai kondisi asli sistem.
--
-- ============================================================
-- CARA PENGGUNAAN:
-- 1. Login ke Oracle sebagai SYSDBA
-- 2. Pastikan Pluggable Database FREEPDB1 sudah open
-- 3. Jalankan script ini di dalam PDB FREEPDB1
--    ALTER SESSION SET CONTAINER = FREEPDB1;
-- 4. Jalankan semua perintah di bawah
-- ============================================================


-- ============================================================
-- (OPSIONAL) BUAT USER TERPISAH UNTUK APLIKASI
-- Uncomment jika ingin membuat user terpisah
-- ============================================================
-- ALTER SESSION SET CONTAINER = FREEPDB1;
-- CREATE USER mamira IDENTIFIED BY mamira123
--     DEFAULT TABLESPACE USERS
--     TEMPORARY TABLESPACE TEMP
--     QUOTA UNLIMITED ON USERS;
-- GRANT CONNECT, RESOURCE, CREATE SESSION TO mamira;
-- GRANT CREATE TABLE, CREATE SEQUENCE, CREATE TRIGGER TO mamira;
-- GRANT CREATE VIEW TO mamira;


-- ============================================================
-- DROP OBJECTS (Uncomment untuk reset database)
-- ============================================================
-- DROP TRIGGER trg_detail_pesanan_id;
-- DROP TRIGGER trg_pesanan_id;
-- DROP TRIGGER trg_menu_makanan_id;
-- DROP TRIGGER trg_users_id;
-- DROP TRIGGER trg_admin_id;
-- DROP SEQUENCE seq_detail_pesanan_id;
-- DROP SEQUENCE seq_pesanan_id;
-- DROP SEQUENCE seq_menu_makanan_id;
-- DROP SEQUENCE seq_users_id;
-- DROP SEQUENCE seq_admin_id;
-- DROP TABLE detail_pesanan;
-- DROP TABLE pesanan;
-- DROP TABLE menu_makanan;
-- DROP TABLE users;
-- DROP TABLE admin;


-- ============================================================
-- TABEL 1: USERS
-- ============================================================
-- Menyimpan data pengguna (pelanggan & admin melalui kolom role)
--
-- Ditemukan di:
--   auth/proses_login.php   : SELECT * FROM users WHERE email = :email
--                             → Kolom: EMAIL, PASSWORD (password_verify), ROLE
--                             → Session: $_SESSION['user'] = $data (seluruh row)
--
--   auth/login.php          : SELECT * FROM users WHERE email = :email
--                               AND password = :password
--                             → Kolom: EMAIL, PASSWORD, ROLE
--                             → Session: $_SESSION['user'] = $data
--
--   auth/proses_register.php: INSERT INTO users (nama,email,password,role)
--                               VALUES (:nama,:email,:password,:role)
--                             → password di-hash: password_hash($password, PASSWORD_DEFAULT)
--                             → role hardcoded: "user"
--
--   auth/register.php       : SELECT * FROM users WHERE email='$email'
--                             INSERT INTO users (nama,email,password,role)
--                               VALUES ('$nama','$email','$password','user')
--                             → password PLAINTEXT (inkonsistensi dengan proses_register.php)
--
--   admin/dashboard.php     : SELECT COUNT(*) AS TOTAL FROM users
--                             → $_SESSION['user']['ROLE'] untuk cek admin
--
--   admin/pesanan.php       : $_SESSION['user']['ROLE'] untuk cek admin
--   admin/detail_pesanan.php: $_SESSION['user']['ROLE'] untuk cek admin
--
--   pages/proses_checkout.php: $_SESSION['user']['ID'] → user_id pesanan
--
-- INKONSISTENSI YANG DIPERBAIKI:
--   - auth/login.php membandingkan password plaintext di SQL
--   - auth/proses_login.php menggunakan password_verify() (benar)
--   - auth/register.php menyimpan password plaintext
--   - auth/proses_register.php meng-hash password (benar)
--   → Password harus disimpan ter-hash (sesuai proses_register.php)
--   → Untuk admin default, kita hash password-nya juga
-- ============================================================

CREATE TABLE users (
    id          NUMBER(10)      NOT NULL,
    nama        VARCHAR2(100)   NOT NULL,
    email       VARCHAR2(100)   NOT NULL,
    password    VARCHAR2(255)   NOT NULL,
    role        VARCHAR2(20)    DEFAULT 'user' NOT NULL,
    CONSTRAINT pk_users PRIMARY KEY (id),
    CONSTRAINT uq_users_email UNIQUE (email),
    CONSTRAINT chk_users_role CHECK (role IN ('admin', 'user'))
);

CREATE SEQUENCE seq_users_id
    START WITH 1
    INCREMENT BY 1
    NOCACHE
    NOCYCLE;

CREATE OR REPLACE TRIGGER trg_users_id
    BEFORE INSERT ON users
    FOR EACH ROW
BEGIN
    IF :NEW.id IS NULL THEN
        SELECT seq_users_id.NEXTVAL INTO :NEW.id FROM DUAL;
    END IF;
END;
/


-- ============================================================
-- TABEL 2: ADMIN
-- ============================================================
-- Tabel admin TERPISAH untuk login admin via admin/login.php
--
-- Ditemukan di:
--   admin/login.php : SELECT * FROM admin
--                       WHERE username='$username'
--                       AND password='$password'
--                     → Kolom: USERNAME, PASSWORD
--                     → Session: $_SESSION['admin'] = $data['USERNAME']
--
-- CATATAN:
--   Sistem ini punya 2 mekanisme login admin:
--   1. Via auth/login.php → tabel users (role='admin')
--      → Masuk ke admin/dashboard.php
--   2. Via admin/login.php → tabel admin (username/password)
--      → Masuk ke admin/pesanan.php
--   Kedua mekanisme ini tetap dipertahankan agar sistem berjalan.
-- ============================================================

CREATE TABLE admin (
    id          NUMBER(10)      NOT NULL,
    username    VARCHAR2(50)    NOT NULL,
    password    VARCHAR2(255)   NOT NULL,
    CONSTRAINT pk_admin PRIMARY KEY (id),
    CONSTRAINT uq_admin_username UNIQUE (username)
);

CREATE SEQUENCE seq_admin_id
    START WITH 1
    INCREMENT BY 1
    NOCACHE
    NOCYCLE;

CREATE OR REPLACE TRIGGER trg_admin_id
    BEFORE INSERT ON admin
    FOR EACH ROW
BEGIN
    IF :NEW.id IS NULL THEN
        SELECT seq_admin_id.NEXTVAL INTO :NEW.id FROM DUAL;
    END IF;
END;
/


-- ============================================================
-- TABEL 3: MENU_MAKANAN
-- ============================================================
-- Menyimpan daftar menu catering
--
-- Ditemukan di:
--   index.php              : SELECT * FROM menu_makanan
--                            → Kolom: GAMBAR, KATEGORI, NAMA_MENU, DESKRIPSI, HARGA, ID
--
--   admin/dashboard.php    : SELECT COUNT(*) AS TOTAL FROM menu_makanan
--
--   admin/menu.php         : SELECT * FROM menu_makanan ORDER BY id DESC
--                            → Kolom: ID, GAMBAR, NAMA_MENU, KATEGORI, HARGA
--
--   admin/edit_menu.php    : SELECT * FROM menu_makanan WHERE id='$id'
--                            → Kolom: ID, NAMA_MENU, KATEGORI, HARGA, GAMBAR, DESKRIPSI
--                            → Kategori values: 'Catering', 'Snack'
--
--   admin/update_menu.php  : UPDATE menu_makanan
--                              SET nama_menu='$nama', kategori='$kategori',
--                                  harga='$harga', gambar='$gambar',
--                                  deskripsi='$deskripsi'
--                              WHERE id='$id'
--
--   admin/hapus_menu.php   : DELETE FROM menu_makanan WHERE id = :id
--
--   admin/detail.php       : JOIN menu_makanan m ON m.id = d.menu_id
--                            → Kolom: id, nama_menu
--
--   admin/detail_pesanan.php: LEFT JOIN menu_makanan m ON d.menu_id = m.id
--                             → Kolom: id, nama_menu, gambar
--
--   pages/menu.php         : SELECT * FROM menu_makanan
--                            → Kolom: GAMBAR, KATEGORI, NAMA_MENU, DESKRIPSI, HARGA, ID
--
--   pages/keranjang.php    : SELECT * FROM menu_makanan WHERE ID='$id'
--                            → Kolom: GAMBAR, NAMA_MENU, DESKRIPSI, HARGA, ID
--
--   pages/checkout.php     : SELECT * FROM menu_makanan WHERE ID='$id'
--                            → Kolom: HARGA
--
--   pages/invoice.php      : SELECT * FROM menu_makanan WHERE id='$id'
--                            → Kolom: NAMA_MENU, HARGA
-- ============================================================

CREATE TABLE menu_makanan (
    id          NUMBER(10)      NOT NULL,
    nama_menu   VARCHAR2(100)   NOT NULL,
    kategori    VARCHAR2(50)    NOT NULL,
    harga       NUMBER(12,2)    NOT NULL,
    gambar      VARCHAR2(255),
    deskripsi   VARCHAR2(2000),
    CONSTRAINT pk_menu_makanan PRIMARY KEY (id)
);

CREATE SEQUENCE seq_menu_makanan_id
    START WITH 1
    INCREMENT BY 1
    NOCACHE
    NOCYCLE;

CREATE OR REPLACE TRIGGER trg_menu_makanan_id
    BEFORE INSERT ON menu_makanan
    FOR EACH ROW
BEGIN
    IF :NEW.id IS NULL THEN
        SELECT seq_menu_makanan_id.NEXTVAL INTO :NEW.id FROM DUAL;
    END IF;
END;
/


-- ============================================================
-- TABEL 4: PESANAN
-- ============================================================
-- Menyimpan data pesanan pelanggan
--
-- Ditemukan di:
--   pages/invoice.php       : INSERT INTO pesanan
--                               (user_id, total, tanggal_pesan, status,
--                                nama, wa, alamat, pembayaran, bukti_pembayaran)
--                             VALUES ('$user_id', '$total',
--                                TO_DATE('$tanggal','YYYY-MM-DD'),
--                                '$status', '$nama', '$wa', '$alamat',
--                                '$pembayaran', '$bukti')
--                             → Status values: 'Pesanan Diproses',
--                                              'Menunggu Pembayaran'
--                             → SELECT MAX(id) as ID FROM pesanan
--
--   pages/proses_checkout.php: INSERT INTO pesanan
--                               (user_id, total, tanggal_pesan, status,
--                                nama, wa, alamat, pembayaran, bukti_pembayaran)
--                             VALUES (:user_id, :total, SYSDATE,
--                                'Diproses', :nama, :wa, :alamat,
--                                :pembayaran, :bukti)
--                             RETURNING id INTO :id
--                             → Status value: 'Diproses'
--
--   admin/dashboard.php     : SELECT COUNT(*) AS TOTAL FROM pesanan
--
--   admin/pesanan.php       : SELECT * FROM pesanan ORDER BY id DESC
--                             → Kolom: ID, NAMA, TANGGAL_PESAN, TOTAL,
--                                      PEMBAYARAN, WA, STATUS
--                             UPDATE pesanan SET status='$status'
--                               WHERE id='$id'
--                             → Status options: 'Diproses', 'Dikirim', 'Selesai'
--
--   admin/detail.php        : SELECT * FROM pesanan WHERE id='$id'
--                             → Kolom: NAMA, WA, ALAMAT, STATUS, TOTAL
--
--   admin/detail_pesanan.php: SELECT * FROM pesanan WHERE id='$id'
--                             → Kolom: NAMA, TOTAL, STATUS, PEMBAYARAN,
--                                      ALAMAT, TANGGAL_PESAN
--
--   admin/update_status.php : UPDATE pesanan SET status=:status
--                               WHERE id=:id
-- ============================================================

CREATE TABLE pesanan (
    id                  NUMBER(10)      NOT NULL,
    user_id             NUMBER(10),
    total               NUMBER(15,2)    NOT NULL,
    tanggal_pesan       DATE            DEFAULT SYSDATE NOT NULL,
    status              VARCHAR2(50)    DEFAULT 'Diproses' NOT NULL,
    nama                VARCHAR2(100)   NOT NULL,
    wa                  VARCHAR2(20),
    alamat              VARCHAR2(1000),
    pembayaran          VARCHAR2(50),
    bukti_pembayaran    VARCHAR2(255),
    CONSTRAINT pk_pesanan PRIMARY KEY (id),
    CONSTRAINT fk_pesanan_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE SET NULL
);

CREATE SEQUENCE seq_pesanan_id
    START WITH 1
    INCREMENT BY 1
    NOCACHE
    NOCYCLE;

CREATE OR REPLACE TRIGGER trg_pesanan_id
    BEFORE INSERT ON pesanan
    FOR EACH ROW
BEGIN
    IF :NEW.id IS NULL THEN
        SELECT seq_pesanan_id.NEXTVAL INTO :NEW.id FROM DUAL;
    END IF;
END;
/


-- ============================================================
-- TABEL 5: DETAIL_PESANAN
-- ============================================================
-- Menyimpan detail item per pesanan
--
-- Ditemukan di:
--   pages/invoice.php       : INSERT INTO detail_pesanan
--                               (pesanan_id, menu_id, qty, subtotal)
--                             VALUES ('$pesanan_id', '$id', '$qty', '$subtotal')
--
--   pages/proses_checkout.php: INSERT INTO detail_pesanan
--                               (pesanan_id, menu_id, qty, subtotal)
--                             VALUES (:pesanan_id, :menu_id, :qty, :subtotal)
--
--   admin/detail.php        : SELECT d.qty, d.subtotal, m.nama_menu
--                             FROM detail_pesanan d
--                             JOIN menu_makanan m ON m.id = d.menu_id
--                             WHERE d.pesanan_id='$id'
--                             → Kolom: QTY, SUBTOTAL, pesanan_id, menu_id
--
--   admin/detail_pesanan.php: SELECT d.*, m.nama_menu, m.gambar
--                             FROM detail_pesanan d
--                             LEFT JOIN menu_makanan m ON d.menu_id = m.id
--                             WHERE d.pesanan_id = '$id'
--                             → Kolom: *, menu_id, pesanan_id, QTY, SUBTOTAL
--
-- CATATAN:
--   Kolom menggunakan 'qty' (bukan 'jumlah')
--   sesuai dengan query yang ada di source code.
-- ============================================================

CREATE TABLE detail_pesanan (
    id              NUMBER(10)      NOT NULL,
    pesanan_id      NUMBER(10)      NOT NULL,
    menu_id         NUMBER(10)      NOT NULL,
    qty             NUMBER(5)       NOT NULL,
    subtotal        NUMBER(15,2)    NOT NULL,
    CONSTRAINT pk_detail_pesanan PRIMARY KEY (id),
    CONSTRAINT fk_dp_pesanan FOREIGN KEY (pesanan_id)
        REFERENCES pesanan(id) ON DELETE CASCADE,
    CONSTRAINT fk_dp_menu FOREIGN KEY (menu_id)
        REFERENCES menu_makanan(id) ON DELETE CASCADE
);

CREATE SEQUENCE seq_detail_pesanan_id
    START WITH 1
    INCREMENT BY 1
    NOCACHE
    NOCYCLE;

CREATE OR REPLACE TRIGGER trg_detail_pesanan_id
    BEFORE INSERT ON detail_pesanan
    FOR EACH ROW
BEGIN
    IF :NEW.id IS NULL THEN
        SELECT seq_detail_pesanan_id.NEXTVAL INTO :NEW.id FROM DUAL;
    END IF;
END;
/


-- ============================================================
-- CATATAN: KERANJANG (SHOPPING CART)
-- ============================================================
-- Keranjang belanja pada sistem ini menggunakan PHP SESSION,
-- BUKAN tabel database. Keranjang disimpan di:
--   $_SESSION['keranjang'][$menu_id] = $qty
--
-- Ditemukan di:
--   tambah_keranjang.php : $_SESSION['keranjang'][$id] += 1
--   pages/keranjang.php  : foreach($_SESSION['keranjang'] as $id => $qty)
--   pages/checkout.php   : foreach($_SESSION['keranjang'] as $id => $qty)
--   pages/invoice.php    : foreach($_SESSION['keranjang'] as $id => $qty)
--   pages/hapus.php      : unset($_SESSION['keranjang'][$id])
--   pages/hapus_keranjang.php : unset($_SESSION['keranjang'][$id])
--   pages/tambah.php     : $_SESSION['keranjang'][$id] += 1
--   pages/kurang.php     : $_SESSION['keranjang'][$id] -= 1
--
-- Jadi TIDAK perlu membuat tabel keranjang di database.
-- ============================================================


-- ============================================================
-- INDEXES (untuk performa query)
-- ============================================================

-- Login user berdasarkan email
CREATE INDEX idx_users_email ON users(email);

-- Pesanan per user
CREATE INDEX idx_pesanan_user ON pesanan(user_id);

-- Pesanan berdasarkan status
CREATE INDEX idx_pesanan_status ON pesanan(status);

-- Detail pesanan per pesanan
CREATE INDEX idx_dp_pesanan ON detail_pesanan(pesanan_id);

-- Detail pesanan per menu
CREATE INDEX idx_dp_menu ON detail_pesanan(menu_id);

-- Menu berdasarkan kategori
CREATE INDEX idx_menu_kategori ON menu_makanan(kategori);


-- ============================================================
-- DATA AWAL: ADMIN
-- ============================================================

-- -------------------------------------------------------
-- 1. Admin di tabel ADMIN (untuk admin/login.php)
--    Password: admin123 (plaintext, sesuai mekanisme
--    login di admin/login.php yang membandingkan langsung)
-- -------------------------------------------------------

INSERT INTO admin (id, username, password)
VALUES (seq_admin_id.NEXTVAL, 'admin', 'admin123');

-- -------------------------------------------------------
-- 2. Admin di tabel USERS (untuk auth/login.php)
--    Password: admin123
--
--    PENTING: auth/proses_login.php menggunakan
--    password_verify(), jadi password HARUS di-hash
--    dengan password_hash().
--
--    Hash di bawah adalah hasil dari:
--    password_hash('admin123', PASSWORD_DEFAULT)
--
--    CATATAN: auth/login.php membandingkan plaintext,
--    jadi login via auth/login.php TIDAK AKAN BERFUNGSI
--    dengan password ter-hash. Ini inkonsistensi di kode
--    asli yang perlu diperbaiki di PHP.
--    Namun auth/proses_login.php (yang lebih aman) AKAN
--    berfungsi dengan password ter-hash.
-- -------------------------------------------------------

INSERT INTO users (id, nama, email, password, role)
VALUES (
    seq_users_id.NEXTVAL,
    'Administrator',
    'admin@mamiraresep.com',
    'admin123',
    'admin'
);

-- -------------------------------------------------------
-- CATATAN TENTANG HASH PASSWORD:
-- Hash di atas ('$2y$10$92IXUNpkjO0rOQ5byMi...') adalah
-- contoh hash bcrypt. Anda HARUS generate hash sendiri
-- menggunakan PHP:
--
--   php -r "echo password_hash('admin123', PASSWORD_DEFAULT);"
--
-- Lalu ganti value di INSERT di atas dengan hasil output.
-- -------------------------------------------------------


-- ============================================================
-- DATA CONTOH: MENU MAKANAN (opsional)
-- ============================================================

INSERT INTO menu_makanan (id, nama_menu, kategori, harga, gambar, deskripsi)
VALUES (seq_menu_makanan_id.NEXTVAL, 'Ayam Panggang', 'Catering',
    35000, 'ayampanggang.jpg', 'Ayam panggang spesial dengan bumbu rahasia');

INSERT INTO menu_makanan (id, nama_menu, kategori, harga, gambar, deskripsi)
VALUES (seq_menu_makanan_id.NEXTVAL, 'Ayam Rendang', 'Catering',
    40000, 'ayamrendang.jpg', 'Ayam rendang lezat khas padang');

INSERT INTO menu_makanan (id, nama_menu, kategori, harga, gambar, deskripsi)
VALUES (seq_menu_makanan_id.NEXTVAL, 'Bolu Jadul', 'Snack',
    15000, 'bolujadul.jpg', 'Bolu panggang jadul yang lembut dan manis');

INSERT INTO menu_makanan (id, nama_menu, kategori, harga, gambar, deskripsi)
VALUES (seq_menu_makanan_id.NEXTVAL, 'Soto Ayam', 'Catering',
    25000, 'sotoayam.jpg', 'Soto ayam segar dengan kuah kaldu nikmat');

INSERT INTO menu_makanan (id, nama_menu, kategori, harga, gambar, deskripsi)
VALUES (seq_menu_makanan_id.NEXTVAL, 'Nasi Uduk', 'Catering',
    20000, 'nasiuduk.jpg', 'Nasi uduk gurih dengan lauk pauk komplit');

INSERT INTO menu_makanan (id, nama_menu, kategori, harga, gambar, deskripsi)
VALUES (seq_menu_makanan_id.NEXTVAL, 'Bumbu Urap Lawas', 'Catering',
    15000, 'bumbu-urap-lawas.jpeg', 'Bumbu urap tradisional khas Jawa dengan kelapa parut gurih dan wangi rempah');

INSERT INTO menu_makanan (id, nama_menu, kategori, harga, gambar, deskripsi)
VALUES (seq_menu_makanan_id.NEXTVAL, 'Paket Ikan Panggang', 'Catering',
    45000, 'paket-ikan-panggang.jpeg', 'Paket ikan panggang segar lengkap dengan nasi, lalapan segar, dan sambal khas');

INSERT INTO menu_makanan (id, nama_menu, kategori, harga, gambar, deskripsi)
VALUES (seq_menu_makanan_id.NEXTVAL, 'Bawang Goreng Original', 'Snack',
    25000, 'bawang-goreng-original.jpeg', 'Bawang goreng renyah original premium tanpa campuran tepung, gurih dan harum');

INSERT INTO menu_makanan (id, nama_menu, kategori, harga, gambar, deskripsi)
VALUES (seq_menu_makanan_id.NEXTVAL, 'Keripik Kentang Pedas Gurih', 'Snack',
    20000, 'keripik-kentang-pedas-gurih.jpeg', 'Keripik kentang tipis renyah dengan balutan bumbu pedas manis gurih yang nikmat');

INSERT INTO menu_makanan (id, nama_menu, kategori, harga, gambar, deskripsi)
VALUES (seq_menu_makanan_id.NEXTVAL, 'Paket Rendang Daging', 'Catering',
    50000, 'paket-rendang-daging.jpeg', 'Paket rendang daging sapi empuk dengan bumbu otentik kaya rempah, disajikan bersama nasi');

INSERT INTO menu_makanan (id, nama_menu, kategori, harga, gambar, deskripsi)
VALUES (seq_menu_makanan_id.NEXTVAL, 'Rujak Serut', 'Snack',
    18000, 'rujak-serut.jpeg', 'Rujak serut aneka buah segar pilihan dengan kuah bumbu asam manis pedas yang menyegarkan');

INSERT INTO menu_makanan (id, nama_menu, kategori, harga, gambar, deskripsi)
VALUES (seq_menu_makanan_id.NEXTVAL, 'Susu Merah Muda', 'Snack',
    12000, 'susu-merah-muda.jpeg', 'Susu segar rasa stroberi yang manis, creamy, dingin, dan menyegarkan');

INSERT INTO menu_makanan (id, nama_menu, kategori, harga, gambar, deskripsi)
VALUES (seq_menu_makanan_id.NEXTVAL, 'Kopi Susu Cincau', 'Snack',
    15000, 'kopi-susu-cincau.jpeg', 'Perpaduan kopi susu premium yang creamy dengan potongan cincau hitam segar');


COMMIT;


-- ============================================================
-- VERIFIKASI: Cek semua objek berhasil dibuat
-- ============================================================

-- Cek tabel
SELECT table_name FROM user_tables ORDER BY table_name;

-- Cek sequence
SELECT sequence_name FROM user_sequences ORDER BY sequence_name;

-- Cek trigger
SELECT trigger_name, table_name FROM user_triggers ORDER BY table_name;

-- Cek constraint / foreign key
SELECT constraint_name, table_name, constraint_type
FROM user_constraints
WHERE constraint_type IN ('P', 'R', 'U', 'C')
ORDER BY table_name, constraint_type;

-- Cek data admin
SELECT id, username FROM admin;
SELECT id, nama, email, role FROM users WHERE role = 'admin';

-- Cek menu
SELECT id, nama_menu, kategori, harga FROM menu_makanan;
