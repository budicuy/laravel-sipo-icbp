# AI Response Formatting - SIPO ICBP

## 📋 Deskripsi
Sistem formatting otomatis untuk respons AI agar lebih mudah dibaca dengan mendukung markdown-like formatting, bullet points, numbered lists, dan text styling.

## ✨ Fitur Formatting

### 1. **Headings**
- `# Heading 1` → **Heading 1** (besar, purple-900)
- `## Heading 2` → **Heading 2** (sedang, purple-800)
- `### Heading 3` → **Heading 3** (kecil, purple-700)

### 2. **Text Styling**
- `**bold text**` → **bold text** (tebal, warna ungu gelap)
- `*italic text*` → *italic text* (miring)

### 3. **Bullet Points**
Input:
```
- Item pertama
- Item kedua
- Item ketiga
```

Output: Bullet list dengan proper spacing dan indentation

### 4. **Numbered Lists**
Input:
```
1. Langkah pertama
2. Langkah kedua
3. Langkah ketiga
```

Output: Numbered list dengan proper formatting

### 5. **Paragraphs**
- Double line breaks (`\n\n`) → Paragraf baru dengan spacing
- Single line breaks (`\n`) → Line break dalam paragraf
- Auto paragraph wrapping untuk text yang bukan list

### 6. **Security**
- HTML escaping untuk mencegah XSS attacks
- Safe rendering of user input
- Sanitized text processing

## 🎨 Styling Details

### CSS Classes Applied:
```css
.prose h1 {
    font-size: 1.5rem;
    color: #4c1d95;  /* Purple-900 */
    margin-top: 1rem;
    margin-bottom: 0.75rem;
    font-weight: 700;
}

.prose h2 {
    font-size: 1.25rem;
    color: #5b21b6;  /* Purple-800 */
    margin-top: 0.875rem;
    margin-bottom: 0.5rem;
    font-weight: 700;
}

.prose h3 {
    font-size: 1.125rem;
    color: #6d28d9;  /* Purple-700 */
    margin-top: 0.75rem;
    margin-bottom: 0.5rem;
    font-weight: 700;
}

.prose {
    color: #1f2937;
}

.prose p {
    margin-bottom: 0.5rem;
    line-height: 1.6;
}

.prose strong {
    color: #4c1d95;  /* Purple-900 */
    font-weight: 700;
}

.prose em {
    color: #5b21b6;  /* Purple-800 */
}

.prose ul, .prose ol {
    margin: 0.75rem 0;
    padding-left: 1.5rem;
}

.prose li {
    margin: 0.25rem 0;
    line-height: 1.5;
}
```

## 🔧 Implementation

### JavaScript Function:
```javascript
function formatAIResponse(text) {
    // 1. Escape HTML to prevent XSS
    text = text.replace(/</g, '&lt;').replace(/>/g, '&gt;');
    
    // 2. Convert **bold** to <strong>
    text = text.replace(/\*\*([^*]+)\*\*/g, '<strong class="font-bold">$1</strong>');
    
    // 3. Convert *italic* to <em>
    text = text.replace(/\*([^*]+)\*/g, '<em class="italic">$1</em>');
    
    // 4. Convert bullet points
    text = text.replace(/^[\-\*•]\s+(.+)$/gm, '<li class="ml-4">$1</li>');
    text = text.replace(/(<li.*?<\/li>\n?)+/g, '<ul class="list-disc list-inside space-y-1 my-2">$&</ul>');
    
    // 5. Convert numbered lists
    text = text.replace(/^\d+\.\s+(.+)$/gm, '<li class="ml-4">$1</li>');
    
    // 6. Convert paragraphs
    text = text.split(/\n\n+/).map(para => {
        para = para.trim();
        if (para && !para.startsWith('<ul') && !para.startsWith('<ol')) {
            return '<p class="mb-2">' + para.replace(/\n/g, '<br>') + '</p>';
        }
        return para;
    }).join('');
    
    return text;
}
```

## 📊 Format Examples

### Example 1: Mixed Content
**AI Input:**
```
Halo! Terima kasih atas pertanyaannya.

**SIPO ICBP** adalah sistem informasi pelayanan kesehatan yang memiliki fitur:

- Rekam medis digital
- Manajemen obat
- Laporan kesehatan

Untuk login:
1. Buka halaman login
2. Masukkan username
3. Masukkan password
4. Klik tombol login

*Mudah bukan?*
```

**Formatted Output:**
- Bold pada "SIPO ICBP"
- Proper paragraph spacing
- Bullet list dengan disc markers
- Numbered list dengan decimal markers
- Italic pada "Mudah bukan?"

### Example 2: Simple Response
**AI Input:**
```
SIPO ICBP adalah **Sistem Informasi Pelayanan Kesehatan** untuk ICBP Group.
```

**Formatted Output:**
- Text biasa dengan "Sistem Informasi Pelayanan Kesehatan" dalam bold

### Example 3: Multi-paragraph
**AI Input:**
```
Terima kasih atas pertanyaan Anda.

SIPO memiliki beberapa fitur unggulan yang dapat membantu Anda.

Silakan hubungi kami jika ada pertanyaan lain.
```

**Formatted Output:**
- Three separate paragraphs with proper spacing

## 🎯 Benefits

1. **Readability**: Respons lebih mudah dibaca dengan formatting yang jelas
2. **Structure**: List dan paragraf terstruktur dengan baik
3. **Emphasis**: Poin penting dapat di-highlight dengan bold
4. **Professional**: Tampilan lebih profesional dan organized
5. **Consistency**: Semua respons AI memiliki format yang konsisten

## 🔒 Security Features

1. **HTML Escaping**: Mencegah XSS injection
2. **Safe Rendering**: Hanya render tag HTML yang diizinkan
3. **Input Sanitization**: User input di-escape sebelum ditampilkan
4. **Controlled Formatting**: Hanya support markdown-like syntax yang aman

## 📝 Usage Guidelines

### For AI Prompts:
Anda dapat memberikan instruksi kepada AI untuk menggunakan formatting:

```
System Prompt:
"Jawab dengan format yang terstruktur. Gunakan:
- **bold** untuk emphasis
- Bullet points untuk list
- Numbered list untuk langkah-langkah
- Paragraf terpisah untuk topik berbeda"
```

### For Developers:
```javascript
// Automatically applied to all bot messages
if (sender === 'bot') {
    const formattedText = formatAIResponse(text);
    // Render with formatted text
}
```

## 🚀 Future Enhancements

- [ ] Support untuk code blocks dengan syntax highlighting
- [ ] Support untuk tables
- [ ] Support untuk blockquotes
- [ ] Support untuk links (dengan safety checks)
- [ ] Support untuk headings (h1, h2, h3)
- [ ] Support untuk inline code with backticks
- [ ] Emoji rendering
- [ ] Image embedding (dengan content policy)

## 📅 Version History

- **v1.0** (7 November 2025)
  - Initial release
  - Bold, italic, lists, paragraphs support
  - XSS protection
  - CSS styling

---

**Dibuat oleh**: SIPO ICBP Development Team  
**Tanggal**: 7 November 2025  
**Status**: ✅ Active
