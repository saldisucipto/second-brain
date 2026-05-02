# Second Brain App

Second Brain adalah aplikasi produktivitas berbasis Laravel untuk mengelola pekerjaan harian, tindak lanjut, dokumentasi rapat, pengelolaan aset, dan catatan pribadi dalam satu dashboard.

Target penggunaan:

- Pekerjaan kantor
- Proyek sampingan / freelance
- Knowledge base pribadi
- Tracking aktivitas harian

## Fitur Utama

### 1. Task Management

- CRUD task (buat, ubah, tandai selesai)
- Prioritas: low, medium, high
- Status: todo, progress, done, cancel
- Due date dan task grouping
- Quick Capture langsung dari dashboard

### 2. Follow Up System

- Follow up per task
- Reminder berdasarkan waktu
- Status follow up: pending, done, missed
- Timeline aktivitas dengan attachment

### 3. MOM (Minutes of Meeting)

- CRUD MOM
- Action items di dalam MOM
- Konversi action item menjadi Task
- Filter dan monitoring progres action items

### 4. Asset Management

- CRUD asset
- Maintenance schedule per asset
- Riwayat maintenance (history + biaya)
- Penandaan overdue dan upcoming maintenance

### 5. Notes (Knowledge Cards)

- Notes berbasis card UI
- Create note via tombol action + modal
- Pin/unpin note
- Search notes (title/content)
- Filter notes berdasarkan type
- Type: general, idea, meeting, technical
- Warna card: yellow, blue, green, gray

### 6. Smart Dashboard

- Statistik Task & Follow Up
- Daily brief
- Insight otomatis untuk task bermasalah
- Ringkasan MOM dan Asset di dashboard utama

### 7. Global Search

- Pencarian lintas modul:
    - Task
    - MOM
    - Asset

### 8. Automation & Action Engine

- Scheduler untuk reminder dan recurring task
- Action Engine berkala untuk trigger otomatis dari kondisi data

## Stack Teknologi

- Backend: Laravel 11+
- Frontend: Blade + Tailwind CSS + TailAdmin components
- Database: MySQL / MariaDB (SQLite juga bisa, dengan ekstensi PHP sqlite)
- Scheduler: Laravel Scheduler
- Mail: SMTP
- File storage: Laravel public disk

## Struktur Modul (Ringkas)

```text
app/
	Http/Controllers/
		TaskController.php
		FollowUpController.php
		MomController.php
		AssetController.php
		NoteController.php
	Models/
		Task.php
		FollowUp.php
		Mom.php
		MomItem.php
		Asset.php
		AssetSchedule.php
		AssetHistory.php
		Note.php
	Services/
		TaskService.php
		InsightService.php
		DailyBriefService.php
		ActionEngineService.php

resources/views/pages/dashboard/
	tasks.blade.php
	moms/
	assets/
	notes/
	search.blade.php
```

## Instalasi

### 1. Clone repository

```bash
git clone <repo-url>
cd second-brain
```

### 2. Install dependency

```bash
composer install
npm install
npm run build
```

### 3. Setup environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi database

- Set koneksi database di file `.env`
- Jalankan migrasi:

```bash
php artisan migrate
php artisan storage:link
```

### 5. Jalankan aplikasi

```bash
php artisan serve
```

## Scheduler (Wajib untuk Automation)

Untuk local development:

```bash
php artisan schedule:work
```

Untuk production, jalankan scheduler via cron agar trigger reminder dan action engine berjalan otomatis.

## Konfigurasi Email (Opsional tapi direkomendasikan)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="Second Brain"
```

## Catatan SQLite

Jika memakai SQLite, pastikan ekstensi PHP sqlite aktif.

Contoh pengecekan:

```bash
php -m | grep -i sqlite
```

Jika belum aktif, install ekstensi sqlite sesuai versi PHP yang digunakan.

## Roadmap

- Markdown renderer untuk notes
- Auto-link note ke entitas (task/mom/asset) berdasarkan pattern
- Notifikasi multi-channel
- Analytics dashboard lanjutan

---

Developed by Saldi Sucipto
