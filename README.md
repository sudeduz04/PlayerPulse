# PlayerPulse

Futbol takımı performans ve gelişim takip sistemi. Antrenör, yönetici ve futbolcu için ayrı paneller; manuel veya AI destekli ilk 11 önerisi; oyuncu analizi; lig ve fikstür yönetimi.

## Tech Stack
- Laravel 12 (PHP 8.2+) + Sanctum
- MySQL / SQLite
- Tailwind CSS 4 + Vite
- Queue (database driver) + arka plan job'lar
- League CommonMark (markdown render)
- phpoffice/phpspreadsheet (CSV / XLSX import)

## Kurulum
```bash
git clone <repo>
cd PlayerPulse
composer setup           # install + .env + key:generate + migrate + npm install + npm build
php artisan db:seed      # demo kullanıcılar + 2025-2026 Süper Lig fikstürü
composer run-script dev  # serve + queue:listen + pail + vite (paralel)
```

Demo kullanıcılar:
| E-posta | Şifre | Rol |
|---|---|---|
| admin@test.com | password | super_admin |
| coach@test.com | password | coach |
| manager@test.com | password | manager |

## Roller
- **super_admin** — Lig, takım ve kullanıcı yönetimi. Fikstür yükleme.
- **manager** — Atanmış takımın oyuncularını ve raporlarını görür, AI analizine erişir.
- **coach** — Antrenman/maç verisi girer, manuel veya AI ilk 11 önerisi oluşturur, AI analiz isteği başlatır.
- **player** — Kendi performansını, sağlığını ve maç/antrenman geçmişini görür.

## AI
`.env` içine en az bir sağlayıcı ekle:
```env
OPENAI_API_KEY=sk-...
# veya
GEMINI_API_KEY=...
```
Hiçbiri tanımlı değilse uygulama AI bölümlerinde "AI sağlayıcısı yapılandırılmamış" uyarısı gösterir; diğer modüller etkilenmez.

## Queue Worker
AI analiz, akıllı kadro, fikstür dosya yükleme ve büyük bulk işlemler kuyruğa alınır.
- Dev: `composer run-script dev` otomatik `queue:listen` başlatır.
- Production: `php artisan queue:work --tries=3 --timeout=300` (supervisord önerilir).

## Fikstür Import
- Süper-admin panelinden `Dosyadan Yükle` (CSV/XLSX) veya `Manuel Satır` formuyla yükleme yapılır.
- Dosya yükleme **asenkron**'dur — durum panelinden polling ile takip edilir.
- Örnek dosya: `storage/fixtures/sample-fixtures-2025-2026.csv`
- Sütunlar: `week,date,home_team,away_team,location,status` (status opsiyonel; varsayılan `scheduled`).

## API
Detaylı endpoint referansı: [docs/api.md](docs/api.md)
- Base URL: `/api`
- Auth: Sanctum bearer token
- Her endpoint için web arayüzdeki karşılığı dokümanda belirtilmiştir.

## Test
```bash
composer test
```
SQLite in-memory ile çalışır; mevcut suite 52+ feature/unit testten oluşur.

## Güvenlik
- Rol bazlı route middleware (`role:super_admin,coach,...`)
- Form request sınıfları ile validation
- API CORS: `config/cors.php`
- Bağımlılık denetimi: `composer audit`

## Dökümantasyon
- `docs/api.md` — API referansı
- `docs/architecture.md` — mimari özet
- `docs/database.md` — veri modeli
- `docs/modules.md` — modül özetleri
- `docs/roles-and-permissions.md` — yetki matrisi
- `docs/testing.md` — test stratejisi
