# 📦 ListoQuote

## 🔍 Proje Amacı

**ListoQuote**, kullanıcıların farklı fiyat listelerini sisteme ekleyerek bu listelerden hızlı ve pratik bir şekilde fiyat teklifleri oluşturmasını sağlayan bir web uygulamasıdır.

Excel üzerinden yürüttüğüm işlemler, GNU/Linux tabanlı sistemler kullandığım için gerçekleştiremediğimden dolayı bu işlevsellik PHP tabanlı bir yapıya aktarılmıştır.

Uygulama aracılığıyla oluşturulan teklifler, aşağıdaki dosya formatlarında dışa aktarılabilir:
- LibreOffice (ODS)
- Microsoft Office (XLSX)
- Taşınabilir Belge Formatı (PDF)

---

## ⚙️ Gereksinimler

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

---

## 🚀 Kurulum

```bash
# Kurulum işlemi GNU/Linux ortamında rootless docker ile test edilmiştir.
# Windows işletim sistemlerinde ek yapılandırmalar gerekebilir.
docker compose up
```

## Lisans
Apache License 2.0