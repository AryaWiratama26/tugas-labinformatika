# API Tugas Lab v1

Base URL: `http://domain-anda/api/v1`
Auth: header `X-API-KEY: <key>` (atau `Authorization: Bearer <key>`).
Semua response JSON. List dipaginasi Laravel (`data`, `links`, `meta`).

## 1. Dapat Key
- Admin panel: buka **API Keys** → isi nama → **Buat Key** → salin key yang muncul (cuma sekali).
- Atau CLI: `php artisan api:key-generate --name="SIMBA-FT"` → revoke: `php artisan api:key-revoke {id|prefix}` → list: `php artisan api:key-list`.

## 2. Health (tanpa key)
`GET /api/v1/health` → `{ "status": "ok", ... }`

## 3. Endpoints (butuh key)

| Metode | Endpoint | Fungsi |
|---|---|---|
| GET/POST | `/subjects`, `/subjects/{id}` | List/tambah/lihat/ubah/hapus matkul (`?visibility=visible|hidden|all`) |
| PUT/PATCH/DELETE | `/subjects/{id}` | Ubah (termasuk `is_hidden` buat arsip)/hapus matkul |
| GET | `/subjects/{subject}/modules` | Modul per matkul (`?visibility=`) |
| GET/POST | `/modules`, `/modules/{id}` (+PUT/PATCH/DELETE) | CRUD modul (filter `?subject_id=&search=&visibility=`) |
| GET/POST | `/classes`, `/classes/{id}` (+PUT/PATCH/DELETE) | CRUD kelas (filter `?search=&visibility=`) |
| GET | `/submissions?search=&subject_id=&module_id=&lab_class_id=&graded=1&visibility=visible` | Siapa ngumpulin + nilai (`visibility`: `visible` default, `hidden`, `all`) |
| GET | `/submissions/{id}` | Detail + relasi subject/module/labClass |
| PUT/PATCH | `/submissions/{id}` | Update nilai/arsip: `{ "score": 85, "feedback": "...", "is_hidden": false }` |
| POST | `/submissions/bulk` | Bulk: `{ "ids": [1,2], "action": "hide|unhide|delete|grade", "score": 90, "feedback": "..." }` |
| DELETE | `/submissions/{id}` | Hapus (best-effort hapus file Drive) |

`score`: 0–100, boleh desimal. `feedback`: maks 5000 char. Upload file **tidak** lewat API (tetap via form web).

## 4. Contoh curl

```bash
KEY="tl_xxx..."

# List submission + filter
curl -H "X-API-KEY: $KEY" "http://localhost:8000/api/v1/submissions?graded=0&per_page=20"

# Beri nilai
curl -X PUT -H "X-API-KEY: $KEY" -H "Content-Type: application/json" \
  -d '{"score": 90, "feedback": "Bagus, rapikan format laporan."}' \
  http://localhost:8000/api/v1/submissions/1

# Arsipkan yang sudah selesai (toggle)
curl -X PUT -H "X-API-KEY: $KEY" -H "Content-Type: application/json" \
  -d '{"is_hidden": true}' \
  http://localhost:8000/api/v1/submissions/1

# Bulk sembunyikan + bulk nilai
curl -X POST -H "X-API-KEY: $KEY" -H "Content-Type: application/json" \
  -d '{"ids": [1,2,3], "action": "hide"}' \
  http://localhost:8000/api/v1/submissions/bulk

# Tambah matkul
curl -X POST -H "X-API-KEY: $KEY" -H "Content-Type: application/json" \
  -d '{"name": "Pemrograman Web", "code": "IF201"}' \
  http://localhost:8000/api/v1/subjects
```

## 5. Error
- `401` key hilang/salah/dicabut/kedaluwarsa → `{ "message": "..." }`
- `404` data tidak ada, `422` validasi gagal (`{ "message", "errors" }`), `429` kebanyakan request.
