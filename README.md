# 🧠 Second Brain App

Aplikasi **Second Brain** berbasis Laravel untuk membantu mencatat, mengelola, dan mengingat semua tugas (_task_), tindak lanjut (_follow up_), dan aktivitas kerja secara terstruktur.

Dirancang untuk kebutuhan nyata:

- 🏢 **Pekerjaan Kantor**
- 🚀 **Sidejobs / Freelance**
- 📚 **Learning & Research**
- ✅ **Task Harian**

---

## 🚀 Features

### ✅ Task Management

- **Full CRUD**: Membuat, memperbarui, dan menyelesaikan tugas.
- **Priority & Due Date**: Pengaturan skala prioritas dan tenggat waktu.
- **Task Group**: Kategorisasi (Kantor, Sidejobs, Learning, dll).

### 🔁 Follow Up System

- **Sub-tasking**: Menambahkan tindak lanjut spesifik untuk setiap tugas.
- **Reminder**: Pengingat otomatis untuk langkah selanjutnya.
- **Scheduling**: Penjadwalan target selesai (_due_at_).

### 💬 Timeline Activity (Chat Style)

- **Visual History**: Riwayat tindak lanjut ditampilkan dengan gaya percakapan (_chat-style_).
- **Rich Media**: Mendukung deskripsi teks, lampiran file (_attachment_), dan stempel waktu.

### 📎 Attachment Support

- **Multi-upload**: Unggah berbagai file pendukung.
- **Management**: Preview dan download langsung dari aplikasi melalui Laravel Storage.

### 🔔 Reminder System

- **Email Notification**: Notifikasi otomatis via SMTP.
- **Status Tracking**: Pantau status _pending_, _done_, atau _missed_.

### 🧠 Insight System

Analisis otomatis dari linimasa:

- Identifikasi tugas _Overdue_.
- Deteksi progres lambat atau tanpa aktivitas.

### ⚡ Quick Capture & Daily Brief

- **Instant Input**: Tambah tugas cepat dari dashboard.
- **Daily Statistics**: Ringkasan harian untuk memulai hari dengan terorganisir.

### 🔁 Recurring Task

- **Automation**: Mendukung tugas harian, mingguan, hingga bulanan yang dibuat otomatis oleh sistem.

---

## 🏗️ Tech Stack

- **Backend**: Laravel 11+
- **Frontend**: TailAdmin + TailwindCSS
- **Database**: MySQL / MariaDB
- **Scheduler**: Laravel Scheduler
- **Email**: SMTP (Gmail / Custom)
- **Storage**: Laravel Storage (Public Disk)

---

## 📁 Project Structure

```text
app/
├── Models/              # Task, FollowUp, Category, Attachment
├── Services/            # Business Logic (TaskService, InsightService)
├── Console/Commands/    # Scheduler & Automation Commands
├── Http/Controllers/    # Web Controllers
database/
├── migrations/          # Database Schema
└── seeders/             # Initial Data & Categories
resources/views/
├── dashboard/           # Analytics & Quick Capture
├── tasks/               # Management UI
└── followups/           # Timeline UI
```

---

## ⚙️ Installation

### 1. Clone Project

```bash
git clone your-repo-url.git
cd second-brain
```

### 2. Install Dependencies

```bash
composer install
npm install && npm run build
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database & Storage

```bash
# Sesuaikan koneksi database di file .env
php artisan migrate --seed
php artisan storage:link
```

### 5. Run App

```bash
php artisan serve
```

---

## ⏰ Scheduler & Email

Aplikasi memerlukan scheduler aktif untuk fitur pengingat:

```bash
# Jalankan secara lokal untuk testing
php artisan schedule:work
```

**Konfigurasi Email (.env):**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
```

---

## 🎯 Use Case

- **Task**: Follow up Client A.
- **Follow Up**: Kirim penawaran (Upload PDF).
- **Next Step**: Jadwalkan telepon konfirmasi besok pagi.
- **Timeline**: Semua aktivitas terekam kronologis layaknya histori chat.

---

## 🧠 Philosophy

Aplikasi ini dirancang sebagai eksternal memori. Tujuannya adalah mengurangi beban kognitif agar Anda bisa fokus pada eksekusi, sementara sistem menangani detail pengingat dan riwayat progres.

---

## 🔥 Future Improvement

- [ ] AI Parsing (Natural Language Input)
- [ ] WhatsApp Notification
- [ ] Advanced Analytics Dashboard

---

**Developed by Saldi Sucipto**
