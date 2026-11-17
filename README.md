# Projek Media Streamer (CodeIgniter 4)

Aplikasi **media streaming & video wall** berbasis **CodeIgniter 4** yang mendukung:

* Playlist video
* Urutan play berdasarkan `daftar_play.position`
* Tampilan grid otomatis (ganjil → video utama lebih besar)
* Autoplay video (muted) + aktivasi audio aman browser
* Cocok untuk **TV Wall / Digital Signage / Streaming Lokal**

---

## ✨ Fitur Utama

* 📂 Manajemen Playlist
* 🎞️ Video streaming (MP4 / HLS)
* 🔁 Konversi otomatis MP4 → HLS (m3u8 + segmen) via ffmpeg, dengan fallback MP4
* 🔢 Urutan video berdasarkan database
* 🖥️ Auto layout grid responsif
* 🔊 Kontrol audio berbasis interaksi user (browser-safe)
* 🧱 Siap dikembangkan ke multi-screen

---

## 🛠️ Teknologi

* PHP 8.x
* CodeIgniter 4
* MySQL / MariaDB
* HTML5 Video
* JavaScript (Vanilla)

---

## 📦 Instalasi

### 1️⃣ Clone repository

```bash
git clone https://github.com/username/projek-media-streamer.git
cd projek-media-streamer
```

### 2️⃣ Install dependency

```bash
composer install
```

### 3️⃣ Konfigurasi environment

Salin file contoh:

```bash
cp env.example .env
```

Atur database di `.env`:

```env
app.baseURL = http://localhost:8080

database.default.hostname = localhost
database.default.database = db_stream
database.default.username = root
database.default.password =
```

---

### 4️⃣ Migration database

```bash
php spark migrate
```
```bash
php spark migrate:refresh
```

---

### 5️⃣ Permission folder (WAJIB)

Pastikan folder berikut **writeable**:

```
writable/cache
writable/logs
writable/session
writable/uploads
writable/hls
```

```bash
mkdir -p writable/{cache,logs,session,uploads,hls}
chmod -R 775 writable
```

---

### 6️⃣ Jalankan server

```bash
php spark serve
```

Buka:

```
http://localhost:8080
```

---

## 📺 Struktur Playlist

* Playlist disimpan di tabel `playlists`
* Relasi video menggunakan tabel `daftar_play`
* Urutan play ditentukan oleh kolom `position`

```sql
ORDER BY daftar_play.position ASC
```

---

## ⚠️ Catatan Penting

* Folder **video & upload TIDAK di-push ke GitHub**
* Konversi HLS membutuhkan **ffmpeg** (`ffmpeg -version`). Jika tidak tersedia, video tetap diputar sebagai MP4.
* Audio **tidak bisa autoplay tanpa interaksi user** (aturan browser)
* Gunakan klik / sentuhan pertama untuk aktivasi audio

---

## 🚀 Roadmap (Next Feature)

* 🔁 Rotasi video utama otomatis
* 🕒 Jadwal playlist
* 📡 Sinkron multi layar
* 📊 Monitoring status player

---

## 📄 Lisensi

MIT License

---

## 🤝 Kontribusi

Pull Request & Issue sangat diterima 🙌
Silakan fork repository ini dan buat perubahan yang Anda inginkan.
## 🐳 Menjalankan dengan Docker

Docker image sudah menyertakan **PHP 8.2 + Apache**, ekstensi yang dibutuhkan, dan **ffmpeg** untuk konversi HLS. Database **tidak** dijalankan di container (memakai database eksternal).

### 1. Siapkan environment

```bash
cp .env.docker.example .env.docker
```

Sesuaikan minimal:

```env
APP_PORT=8080
APP_BASE_URL=http://localhost:8080/
DB_HOST=host.docker.internal
DB_DATABASE=streaming_db
DB_USERNAME=streamer
DB_PASSWORD=ubah_saya
```

`host.docker.internal` mengarah ke host mesin Docker (disediakan melalui `extra_hosts: host-gateway`). Jika database ada di server lain, ganti `DB_HOST` dengan IP/hostname server tersebut.

### 2. Jalankan (production-like)

```bash
docker compose --env-file .env.docker up -d --build
```

Buka `http://localhost:8080`.

### 3. Jalankan mode development (bind-mount + auto migrate)

```bash
docker compose --env-file .env.docker \
  -f docker-compose.yml -f docker-compose.dev.yml up -d --build
```

Perubahan pada `app/`, `public/`, `tests/`, dan `writable/` langsung terlihat tanpa rebuild.

### 4. Perintah berguna

```bash
docker compose exec app php spark migrate
docker compose exec app php spark routes
docker compose exec app ffmpeg -version
docker compose logs -f app
docker compose down
```

Migrasi otomatis saat container start:

```env
RUN_MIGRATIONS=true
```

### Catatan Docker

- Upload video dan hasil HLS disimpan di volume `media-streamer-writable` (`writable/uploads`, `writable/hls`).
- Batas upload di container: **2 GB** (`upload_max_filesize` / `post_max_size`).
- Database eksternal harus mengizinkan koneksi dari container (mis. user `'streamer'@'%'`) dan `bind-address` tidak hanya `127.0.0.1`.
- Jika ffmpeg tidak tersedia, upload tetap berhasil dan video diputar sebagai MP4 (fallback).

---

## Troubleshooting


### Masalah Umum dan Solusi

#### 1. Database Connection Error
**Penyebab:**
- Database `streaming_db_live` belum dibuat
- Username/password database tidak sesuai
- MySQL server tidak berjalan

**Solusi:**
```bash
# Pastikan MySQL berjalan
sudo service mysql start

# Buat database
mysql -u root -p -e "CREATE DATABASE streaming_db_live;"
```

#### 2. Encryption Key Not Set
**Penyebab:**
- `encryption.key` tidak dikonfigurasi di `.env`

**Solusi:**
Uncomment dan generate encryption key:
```bash
php spark key:generate
```

#### 3. Permission Denied on Writable Directory
**Penyebab:**
- Folder `writable/` tidak memiliki izin tulis

**Solusi:**
```bash
chmod -R 755 writable/
```

#### 4. Port 8081 Already in Use
**Solusi:**
```bash
php spark serve --port 8000
```

jika gagal spark dan menghasilkan output "", lakukan langkah berikut:
mkdir -p /home/cx/Media-Streamer/writable/{cache,logs,session,uploads} && chmod -R 777 /home/cx/Media-Streamer/writable