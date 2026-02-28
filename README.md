# Akıllı Analiz Destekli Futbolcu Performans ve Gelişim Takip Sistemi

## Proje Tanımı

Akıllı Analiz Destekli Futbolcu Performans ve Gelişim Takip Sistemi; takım yöneticisi, antrenör ve futbolcu panelleri üzerinden oyuncuların antrenman ve maç verilerini kaydetmeyi, gelişimlerini grafiklerle takip etmeyi ve istatistiklere dayalı otomatik analiz ve öneriler üretmeyi amaçlayan web ve mobil tabanlı bir platformdur.

Bu sistem sayesinde:

- Oyuncu performans verileri kayıt altına alınır.
- Gelişim süreci grafiklerle izlenir.
- Performans değişimleri analiz edilir.
- Antrenörler için veri destekli karar mekanizması oluşturulur.

---

# Projenin Amacı

- Futbolcuların bireysel performans gelişimini ölçmek
- Antrenörlere veri destekli analiz sunmak
- Takım içi performans karşılaştırması yapmak
- Manuel değerlendirme yerine sistematik analiz sağlamak

---

# Kullanılan Teknolojiler

## Web (Yönetim Paneli)

- Laravel (Backend Framework)
- MySQL (Veritabanı)
- HTML5
- CSS3
- JavaScript

## Mobil Uygulama

- React Native

---

# Kullanıcı Rolleri

## Yönetici

- Takım oluşturma
- Oyuncu ekleme / silme
- Antrenör atama
- Genel istatistikleri görüntüleme

## Antrenör

- Antrenman verisi girme
- Maç performans verisi girme
- Oyuncu gelişim grafiği görüntüleme
- Sistem analizlerini inceleme

## Futbolcu

- Kendi performansını görüntüleme
- Gelişim grafiğini takip etme
- Kişisel analiz sonuçlarını görme

---

# Sistem Özellikleri

## 1. Performans Veri Girişi

- Maç istatistikleri (gol, asist, şut, pas yüzdesi vb.)
- Antrenman verileri (dayanıklılık, hız, kondisyon vb.)
- Haftalık performans kayıtları

## 2. Grafiksel Gelişim Takibi

- Oyuncu bazlı performans grafikleri
- Tarihe göre gelişim analizi
- Kategori bazlı istatistik grafikleri

## 3. Akıllı Analiz Sistemi

- Önceki verilere göre performans karşılaştırma
- Artış / düşüş tespiti
- Otomatik yorum üretimi

### Örnek Analiz Çıktıları

- "Şut isabet oranında %12 düşüş var."
- "Dayanıklılık seviyesi son 3 haftada artış göstermiştir."
- "Pas başarısı stabil seyrediyor."

## 4. İlk 11 Öneri Sistemi (Opsiyonel Gelişmiş Özellik)

- Pozisyon bazlı en yüksek performanslı oyuncuları analiz etme
- Form durumuna göre otomatik ilk 11 önerisi
- Ortalama performans puanı hesaplama

---

# Veritabanı Yapısı (Özet)

- users
- teams
- players
- matches
- trainings
- performance_stats
- analysis_results

---

# Güvenlik Özellikleri

- Rol bazlı yetkilendirme
- Laravel authentication sistemi
- API güvenliği
- Veri doğrulama (validation)

---

# Yapılacaklar (To-Do)

- [ ] Veritabanı tasarımının tamamlanması
- [ ] Laravel backend kurulumu
- [ ] API endpointlerinin oluşturulması
- [ ] React Native mobil arayüz tasarımı
- [ ] Grafik sistemi entegrasyonu
- [ ] Analiz algoritmasının geliştirilmesi
- [ ] Test süreci
- [ ] Deploy işlemleri

---

# Sonuç

Bu proje, futbol takımları için veri odaklı karar verme mekanizması sunarak oyuncu gelişimini sistematik ve ölçülebilir hale getirmeyi amaçlamaktadır.