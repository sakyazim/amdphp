# FAZ 7: EDİTÖRE NOT

**Durum**: ⚪ Bekliyor
**Tahmini Süre**: 4 saat
**Öncelik**: 🟢 Düşük
**Bağımlılık**: Faz 1 tamamlanmalı

---

## 🎯 AMAÇ

Editöre not/mesaj gönderme sistemi oluşturmak:
- Basit text veya rich text editor
- Karakter sayacı
- Opsiyonel alan
- Maksimum karakter limiti

---

## ✅ GÖREVLER

### 7.1 - Gereksinimler Belirle

**Süre**: 15 dakika

**Lütfen aşağıdaki soruları cevaplayın:**

#### Sorular:

1. **Editor türü:**
   - [ ] Basit textarea (sadece düz metin)
   - [ ] Rich text editor (bold, italic, liste, vb.)
   - [ ] Markdown editor

   **Öneri**: Basit textarea (kolay implementasyon)

2. **Karakter limiti:**
   - [ ] 500 karakter
   - [ ] 1000 karakter
   - [ ] 2000 karakter
   - [ ] Limit yok

   **Öneri**: 1000 karakter

3. **Zorunlu mu?**
   - [ ] Evet (makale göndermek için gerekli)
   - [ ] Hayır (opsiyonel)

   **Öneri**: Hayır (opsiyonel)

**Kararlar:**

```
[Buraya kararlarınızı yazın]

Örnek:
- Editor: Basit textarea
- Limit: 1000 karakter
- Zorunlu: Hayır (opsiyonel)
```

---

### 7.2 - Veritabanı Alanı Ekle

**Süre**: 5 dakika

`makaleler` tablosuna `editore_notu` alanı ekleyelim:

```sql
ALTER TABLE `makaleler`
ADD COLUMN `editore_notu` TEXT AFTER `anahtar_kelimeler_en`;
```

**Görevler:**

- [ ] SQL'i çalıştır
- [ ] Tabloyu phpMyAdmin'de kontrol et

**Test SQL:**

```sql
UPDATE makaleler SET editore_notu = 'Bu bir test notudur.' WHERE id = 1;
SELECT id, baslik, editore_notu FROM makaleler WHERE id = 1;
```

---

### 7.3 - Basit Text Editor veya Rich Text Editor?

**Süre**: 2 saat

#### Seçenek 1: Basit Textarea (Önerilen)

**Avantajlar:**
- Kolay implementasyon
- Hızlı çalışır
- Gereksiz complexity yok

**HTML:**

```html
<div class="form-group">
    <label for="editore-notu">Editöre Not (Opsiyonel)</label>
    <textarea id="editore-notu" name="editore_notu" class="form-control" rows="5" maxlength="1000"
        placeholder="Makaleniz ile ilgili editöre iletmek istediğiniz özel notlar veya açıklamalar..."></textarea>
    <small class="form-text text-muted">
        <span id="char-count">0</span> / 1000 karakter
    </small>
</div>
```

**JavaScript:**

```javascript
// Karakter sayacı
const textarea = document.getElementById('editore-notu');
const charCount = document.getElementById('char-count');

textarea.addEventListener('input', () => {
    charCount.textContent = textarea.value.length;

    if (textarea.value.length >= 1000) {
        charCount.classList.add('text-danger');
    } else {
        charCount.classList.remove('text-danger');
    }
});
```

---

#### Seçenek 2: Rich Text Editor (İleri Seviye)

**Eğer formatting gerekiyorsa:**

**Kütüphane:** [TinyMCE](https://www.tiny.cloud/) veya [Quill](https://quilljs.com/)

**HTML:**

```html
<div class="form-group">
    <label for="editore-notu">Editöre Not (Opsiyonel)</label>
    <div id="editore-notu-editor"></div>
    <input type="hidden" id="editore-notu" name="editore_notu">
    <small class="form-text text-muted">
        <span id="char-count">0</span> / 1000 karakter
    </small>
</div>

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
var quill = new Quill('#editore-notu-editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }]
        ]
    }
});

// Karakter sayacı
quill.on('text-change', function() {
    const text = quill.getText();
    document.getElementById('char-count').textContent = text.length;

    // Hidden input'a kaydet
    document.getElementById('editore-notu').value = quill.root.innerHTML;
});
</script>
```

---

**Görevler:**

- [ ] Seçeneği belirle (Basit textarea veya Rich text)
- [ ] HTML'i ekle
- [ ] JavaScript'i ekle
- [ ] Test et

---

### 7.4 - Karakter Sayacı Ekle

**Süre**: 30 dakika

Zaten 7.3'te ekledik. Test edelim:

**Test senaryoları:**

- [ ] Textarea'ya yazıldıkça sayaç artıyor
- [ ] 1000 karaktere ulaşınca kırmızıya dönüyor
- [ ] Limit aşılamıyor (maxlength attributü)

---

### 7.5 - Test Et

**Süre**: 30 dakika

**Test senaryoları:**

**Basit textarea:**
- [ ] Yazı yazılabiliyor
- [ ] Karakter sayacı çalışıyor
- [ ] 1000 karakter limiti çalışıyor
- [ ] Form submit'te veritabanına kaydediliyor
- [ ] Boş bırakılabilir (opsiyonel)

**Rich text editor (eğer kullanıldıysa):**
- [ ] Bold, italic çalışıyor
- [ ] Liste oluşturabiliyor
- [ ] HTML içeriği doğru kaydediliyor
- [ ] Karakter sayacı HTML taglarını saymamalı

**Backend:**
- [ ] Not kaydediliyor
- [ ] Not gösteriliyor (yazar panelinde)
- [ ] Not editör panelinde görünüyor

---

## 🎉 FAZ 7 TAMAMLANDI MI?

Tüm checkboxlar işaretlendiyse:

- [ ] **Faz 7 tamamlandı!**
- [ ] [CHECKLIST-MASTER.md](CHECKLIST-MASTER.md) dosyasında Faz 7'yi tamamlandı olarak işaretle
- [ ] Faz 8'e geç: [FAZ-8-KONTROL-LISTESI.md](FAZ-8-KONTROL-LISTESI.md)

---

## 📝 NOTLAR

**Karşılaşılan Sorunlar:**

```
[Buraya notlarınızı yazın]
```

**Öğrenilen Dersler:**

```
[Buraya notlarınızı yazın]
```

---

**Son Güncelleme**: 2024-12-03
**Durum**: ⚪ Bekliyor
