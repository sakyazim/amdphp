# 🌍 YENİ DİL EKLEME REHBERİ

**Tarih**: 2024-12-03
**Sistem**: AMDS Çoklu Dil Desteği

---

## 🎯 GENEL BAKIŞ

AMDS sistemi **genişletilebilir çoklu dil desteği** ile tasarlanmıştır. Yeni bir dil eklemek için **KOD DEĞİŞİKLİĞİ GEREKMEZ**!

**Sadece 3 adım:**
1. Klasör oluştur
2. JSON dosyalarını çevir
3. Config'de aktif et

---

## 📋 HAZIR DİLLER

Sistemde şu diller **tanımlı** (aktif edilmeyi bekliyor):

| Dil | Kod | Native | Direction | Durum |
|-----|-----|--------|-----------|-------|
| Türkçe | tr | Türkçe | ltr | ✅ Aktif |
| English | en | English | ltr | ✅ Aktif |
| العربية | ar | العربية | **rtl** | ⏸️ Hazır |
| 日本語 | ja | 日本語 | ltr | ⏸️ Hazır |
| Русский | ru | Русский | ltr | ⏸️ Hazır |
| 中文 | zh | 中文 | ltr | ⏸️ Hazır |
| Deutsch | de | Deutsch | ltr | ⏸️ Hazır |
| Français | fr | Français | ltr | ⏸️ Hazır |
| 한국어 | ko | 한국어 | ltr | ⏸️ Hazır |

---

## 🚀 ÖRNEK: JAPONCA EKLEME

### Adım 1: Klasör Oluştur

```bash
mkdir config/languages/ja
```

### Adım 2: JSON Dosyalarını Oluştur

**config/languages/ja/common.json:**

```json
{
  "buttons": {
    "save": "保存",
    "cancel": "キャンセル",
    "submit": "送信",
    "delete": "削除",
    "edit": "編集",
    "add": "追加",
    "remove": "削除",
    "search": "検索",
    "filter": "フィルター",
    "clear": "クリア",
    "close": "閉じる",
    "back": "戻る",
    "next": "次へ",
    "previous": "前へ"
  },
  "messages": {
    "success": "操作が成功しました",
    "error": "エラーが発生しました",
    "warning": "警告",
    "info": "情報",
    "confirm_delete": "削除してもよろしいですか？",
    "no_data": "データが見つかりません",
    "loading": "読み込み中...",
    "saving": "保存中...",
    "please_wait": "お待ちください..."
  },
  "validation": {
    "required": "この項目は必須です",
    "email": "有効なメールアドレスを入力してください",
    "min_length": "最低{min}文字必要です",
    "max_length": "最大{max}文字まで入力できます"
  }
}
```

**config/languages/ja/create_article.json:**

```json
{
  "page_title": "新しい記事の提出",
  "step_labels": {
    "1": "記事情報",
    "2": "著者情報",
    "3": "参考文献",
    "4": "推奨査読者",
    "5": "ファイルアップロード",
    "6": "編集者への注記",
    "7": "チェックリスト",
    "8": "プレビューと送信"
  },
  "form": {
    "article_type": "記事の種類",
    "article_title": "記事のタイトル",
    "abstract": "要約",
    "keywords": "キーワード",
    "author": {
      "title": "著者情報",
      "add": "著者を追加",
      "name": "名前",
      "surname": "姓",
      "email": "メール",
      "institution": "所属機関"
    }
  },
  "buttons": {
    "save_draft": "下書きを保存",
    "submit": "記事を送信"
  }
}
```

### Adım 3: Config'de Aktif Et

**config/languages/config.json:**

```json
{
  "available_languages": [
    {
      "code": "ja",
      "name": "Japanese",
      "native_name": "日本語",
      "direction": "ltr",
      "enabled": true,  ← false'tan true'ya değiştir
      "default": false,
      "flag": "🇯🇵"
    }
  ]
}
```

### Adım 4: Veritabanına İçe Aktar (Opsiyonel)

**API ile:**

```bash
curl -X POST http://yoursite.com/api/languages/import \
  -H "Content-Type: application/json" \
  -d '{"language": "ja", "page": "common"}'

curl -X POST http://yoursite.com/api/languages/import \
  -H "Content-Type: application/json" \
  -d '{"language": "ja", "page": "create_article"}'
```

**Veya PHP ile:**

```php
$lang->importFromJson('ja', 'common');
$lang->importFromJson('ja', 'create_article');
```

### Adım 5: Test Et

```
http://yoursite.com/?lang=ja
```

veya dil seçiciden 🇯🇵 **日本語**'yi seç.

---

## 🔤 RTL DİL EKLEME (Örnek: Arapça)

### Özellik: Right-to-Left (RTL)

Arapça, İbranice, Farsça gibi diller için otomatik RTL desteği.

### Adım 1: Klasör Oluştur

```bash
mkdir config/languages/ar
```

### Adım 2: JSON Dosyalarını Oluştur

**config/languages/ar/common.json:**

```json
{
  "buttons": {
    "save": "حفظ",
    "cancel": "إلغاء",
    "submit": "إرسال",
    "delete": "حذف",
    "edit": "تعديل",
    "add": "إضافة",
    "search": "بحث",
    "close": "إغلاق"
  },
  "messages": {
    "success": "تمت العملية بنجاح",
    "error": "حدث خطأ",
    "loading": "جار التحميل...",
    "please_wait": "الرجاء الانتظار..."
  }
}
```

### Adım 3: Config'de Aktif Et

```json
{
  "code": "ar",
  "direction": "rtl",  ← RTL önemli!
  "enabled": true
}
```

### Adım 4: Test Et

Sayfa otomatik olarak:
- `<html dir="rtl">` olur
- `<body class="rtl">` sınıfı eklenir
- Tüm CSS RTL'e uygun şekilde uygulanır

**CSS zaten hazır!** (`language-switcher.css` içinde)

---

## 🛠️ KOMUT SATIRINDAN DİL EKLEME

### Hızlı Script (Bash)

**add-language.sh:**

```bash
#!/bin/bash

LANG_CODE=$1
LANG_NAME=$2
LANG_NATIVE=$3
LANG_DIR=${4:-ltr}

# Klasör oluştur
mkdir -p "config/languages/$LANG_CODE"

# Template kopyala
cp "config/languages/tr/common.json" "config/languages/$LANG_CODE/common.json"
cp "config/languages/tr/create_article.json" "config/languages/$LANG_CODE/create_article.json"

echo "✅ Klasörler ve dosyalar oluşturuldu: $LANG_CODE"
echo "📝 Şimdi JSON dosyalarını çevirin:"
echo "   - config/languages/$LANG_CODE/common.json"
echo "   - config/languages/$LANG_CODE/create_article.json"
echo ""
echo "🔧 config/languages/config.json dosyasında '$LANG_CODE' dilini aktif edin"
```

**Kullanım:**

```bash
./add-language.sh ja Japanese 日本語 ltr
./add-language.sh ar Arabic العربية rtl
```

---

## 📐 ÇEVİRİ ŞABLONU

### common.json Minimum İçerik

```json
{
  "buttons": {
    "save": "...",
    "cancel": "...",
    "submit": "...",
    "delete": "...",
    "edit": "...",
    "add": "..."
  },
  "messages": {
    "success": "...",
    "error": "...",
    "loading": "...",
    "please_wait": "..."
  },
  "validation": {
    "required": "...",
    "email": "...",
    "min_length": "...",
    "max_length": "..."
  }
}
```

### create_article.json Minimum İçerik

```json
{
  "page_title": "...",
  "form": {
    "article_type": "...",
    "article_title": "...",
    "abstract": "...",
    "keywords": "...",
    "author": {
      "title": "...",
      "add": "...",
      "name": "...",
      "email": "..."
    }
  },
  "buttons": {
    "save_draft": "...",
    "submit": "..."
  }
}
```

---

## 🧪 TEST KONTROL LİSTESİ

Yeni dil ekledikten sonra:

- [ ] Dil seçicide görünüyor mu?
- [ ] Bayrak (flag) doğru mu?
- [ ] Native name doğru görünüyor mu?
- [ ] Sayfalar çevrilmiş mi?
- [ ] Butonlar çevrilmiş mi?
- [ ] Mesajlar çevrilmiş mi?
- [ ] RTL dil ise `dir="rtl"` uygulanmış mı?
- [ ] Özel karakterler (日本語, العربية, vb.) düzgün görünüyor mu?
- [ ] Cookie/Session'da dil saklanıyor mu?
- [ ] Sayfa yenilendiğinde dil korunuyor mu?

---

## 🔍 SORUN GİDERME

### Dil seçicide görünmüyor

**Çözüm:**
```json
// config/languages/config.json
{
  "code": "ja",
  "enabled": true  ← false olabilir mi?
}
```

### Çeviriler görünmüyor

**Çözüm 1:** JSON dosyaları doğru yerde mi?
```
config/languages/ja/common.json
config/languages/ja/create_article.json
```

**Çözüm 2:** JSON formatı geçerli mi?
```bash
# JSON doğrulama
cat config/languages/ja/common.json | python -m json.tool
```

**Çözüm 3:** Veritabanına import edildi mi?
```php
$lang->importFromJson('ja', 'common');
```

### Özel karakterler bozuk görünüyor

**Çözüm:** UTF-8 encoding kontrolü

**Veritabanı:**
```sql
SHOW CREATE TABLE dil_degiskenleri;
-- Charset: utf8mb4, Collation: utf8mb4_unicode_ci olmalı
```

**PHP:**
```php
header('Content-Type: text/html; charset=UTF-8');
```

**HTML:**
```html
<meta charset="UTF-8">
```

### RTL düzgün çalışmıyor

**Çözüm:** CSS kontrolü
```html
<!-- language-switcher.css yüklü mü? -->
<link rel="stylesheet" href="/assets/css/language-switcher.css">
```

---

## 📚 KAYNAKLAR

- [Mimari Dokümantasyon](DIL-SISTEMI-MIMARI.md)
- [LanguageService.php](app/Services/LanguageService.php)
- [language-helper.js](public/assets/js/language-helper.js)
- [Config Dosyası](config/languages/config.json)

---

## 💡 İPUÇLARI

1. **Google Translate kullanmayın!**
   - Profesyonel çevirmen kullanın
   - Özellikle teknik terimler için

2. **Kültürel uygunluk**
   - "Submit" → Türkçe'de "Gönder" (not "Teslim Et")
   - Tarih formatları (DD/MM/YYYY vs MM/DD/YYYY)

3. **Tutarlılık**
   - Aynı terimi her yerde aynı şekilde çevirin
   - "Author" → "Yazar" (not bazen "Yazar" bazen "Müellif")

4. **Context**
   - "Save" → Dosya için "Kaydet", para için "Biriktir"
   - JSON'da context notu ekleyin

5. **Plural/Gender**
   - Bazı diller (Rusça, Arapça) farklı plural formlar kullanır
   - Parametreli çeviriler kullanın: `"{count} items"` → `"{count} öğe"`

---

**Kolay gelsin! 🚀**

Sorularınız için: [GitHub Issues](https://github.com/yourproject/issues)

**Son Güncelleme**: 2024-12-03
