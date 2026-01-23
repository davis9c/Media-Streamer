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
* 🎞️ Video streaming (MP4 / HLS-ready)
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
cp env .env
```
a
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
writable/
public/uploads/
public/videos/
public/hls/
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