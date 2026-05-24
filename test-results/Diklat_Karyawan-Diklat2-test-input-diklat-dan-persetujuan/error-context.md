# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: Diklat_Karyawan\Diklat2.spec.ts >> test input diklat dan persetujuan
- Location: tests\Playwright\Diklat_Karyawan\Diklat2.spec.ts:4:1

# Error details

```
Test timeout of 30000ms exceeded.
```

```
Error: locator.click: Test timeout of 30000ms exceeded.
Call log:
  - waiting for getByRole('link', { name: 'Persetujuan' })

```

# Page snapshot

```yaml
- generic [ref=e3]:
  - heading "Tambah Data Diklat Baru" [level=1] [ref=e4]
  - generic [ref=e5]:
    - generic [ref=e6]:
      - generic [ref=e7]: Tanggal Mulai
      - textbox "Tanggal Mulai Tanggal Selesai" [ref=e8]: 2026-05-20
    - generic [ref=e9]:
      - generic [ref=e10]: Tanggal Selesai
      - textbox [ref=e11]: 2026-05-20
    - generic [ref=e12]:
      - generic [ref=e13]: Nama Diklat
      - textbox "Nama Diklat" [ref=e14]:
        - /placeholder: "Contoh: Pelatihan Keselamatan"
        - text: Diklat Karyawan
    - generic [ref=e15]:
      - generic [ref=e16]: Diklat
      - combobox [ref=e17]:
        - option "HLC" [selected]
        - option "Eksternal"
    - generic [ref=e18]:
      - generic [ref=e19]: Pengajar
      - combobox "Pengajar" [ref=e20]: dr Minar Mitutuluhur
    - generic [ref=e21]:
      - generic [ref=e22]: Penyelenggara
      - textbox "Penyelenggara" [ref=e23]:
        - /placeholder: "Contoh: PT. Safety Indonesia"
        - text: RSMDH
    - generic [ref=e24]:
      - generic [ref=e25]: Jam Diklat
      - spinbutton "Jam Diklat" [ref=e26]: "1"
    - generic [ref=e27]:
      - generic [ref=e28]: Evaluasi Materi
      - textbox [ref=e29]: sangat bagus
    - generic [ref=e30]:
      - generic [ref=e31]: Evaluasi Pengajar
      - textbox [ref=e32]: luar biasa
    - generic [ref=e33]:
      - generic [ref=e34]: Upload Sertifikat
      - button "Upload Sertifikat" [ref=e35]
      - paragraph [ref=e36]: File harus berformat PDF. Maksimal ukuran 2MB.
      - paragraph [ref=e37]: "File dipilih: hasil_turnitin_1767684375748_Revisi.pdf"
    - button "Simpan Data" [active] [ref=e39]
```

# Test source

```ts
  1  | import { test, expect } from '@playwright/test';
  2  | import path from 'path'; // Dibutuhkan untuk membaca file upload dengan aman
  3  | 
  4  | test('test input diklat dan persetujuan', async ({ page }) => {
  5  |   // 1. Proses Login
  6  |   await page.goto('http://localhost:8000/');
  7  |   await page.getByText('Klik di mana saja untuk melanjutkan').click({ force: true });
  8  |   
  9  |   await page.locator('input[name="nrp"]').fill('005100439');
  10 |   await page.locator('input[name="password"]').fill('005100439');
  11 |   await page.getByRole('button', { name: 'Login' }).click();
  12 |   
  13 |   // Tunggu sampai benar-benar masuk ke dashboard
  14 |   await page.waitForURL('**/dashboard');
  15 | 
  16 |   // 2. Navigasi ke Menu Diklat
  17 |   // Hapus properti 'description' yang bikin pencarian terlalu kaku
  18 | const menuDiklat = page.locator('a[href="/Diklat"]', { hasText: 'Diklat' });
  19 | await menuDiklat.waitFor({ state: 'visible' });
  20 | await menuDiklat.click();
  21 | 
  22 |   // 3. Masuk ke Form Tambah Diklat
  23 |   await page.getByRole('button', { name: 'Tambah' }).click();
  24 | 
  25 |   // 4. Pengisian Form Diklat
  26 |   await page.getByRole('textbox', { name: 'Tanggal Mulai Tanggal Selesai' }).fill('2026-05-20');
  27 |   await page.locator('#tanggal').nth(1).fill('2026-05-20');
  28 |   
  29 |   await page.getByRole('textbox', { name: 'Nama Diklat' }).fill('Diklat Karyawan');
  30 |   await page.locator('select[name="diklat"]').selectOption('HLC');
  31 |   
  32 |   await page.getByRole('combobox', { name: 'Pengajar' }).fill('dr Minar Mitutuluhur');
  33 |   await page.getByRole('textbox', { name: 'Penyelenggara' }).fill('RSMDH');
  34 |   
  35 |   await page.getByRole('spinbutton', { name: 'Jam Diklat' }).fill('1');
  36 |   
  37 |   await page.locator('#keterangan').nth(1).fill('sangat bagus');
  38 |   await page.locator('#keterangan').nth(2).fill('luar biasa');
  39 | 
  40 |   // 5. Proses Upload Sertifikat
  41 |   // Catatan: Pastikan file 'hasil_turnitin_1767684375748_Revisi.pdf' berada di folder project kamu
  42 |   // Jika tombol menggunakan <input type="file">, Playwright bisa langsung mengisinya:
  43 |   const lokasiFile = 'C:\\Users\\Fathur\\Downloads\\hasil_turnitin_1767684375748_Revisi.pdf';
  44 |   const fileInput = page.locator('input[type="file"]');
  45 |   if (await fileInput.count() > 0) {
  46 |     await fileInput.setInputFiles('hasil_turnitin_1767684375748_Revisi.pdf');
  47 |   } else {
  48 |     // Jika input filenya tersembunyi, trigger lewat button-nya
  49 |     await page.getByRole('button', { name: 'Upload Sertifikat' }).setInputFiles('hasil_turnitin_1767684375748_Revisi.pdf');
  50 |   }
  51 | 
  52 |   // 6. Simpan Data
  53 |   await page.getByRole('button', { name: 'Simpan Data' }).click();
  54 | 
  55 |   // 7. Proses Persetujuan (Approval)
> 56 |   await page.getByRole('link', { name: 'Persetujuan' }).click();
     |                                                         ^ Error: locator.click: Test timeout of 30000ms exceeded.
  57 |   await page.getByRole('button', { name: 'SETUJUI' }).first().click();
  58 |   await page.getByRole('button', { name: 'Ya' }).click();
  59 | });
```