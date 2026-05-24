# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: Library\Library.spec.ts >> test input library materi folder dan file
- Location: tests\Playwright\Library\Library.spec.ts:3:1

# Error details

```
Test timeout of 30000ms exceeded.
```

```
Error: locator.fill: Test timeout of 30000ms exceeded.
Call log:
  - waiting for getByRole('textbox', { name: 'Nama Folder' })

```

# Page snapshot

```yaml
- generic [ref=e1]:
  - generic [ref=e4]:
    - generic [ref=e7]:
      - list [ref=e9]:
        - listitem [ref=e10]:
          - link "Eichar" [ref=e11] [cursor=pointer]:
            - /url: /dashboard
            - generic [ref=e14]: Eichar
      - generic [ref=e16]:
        - generic [ref=e17]: Platform
        - list [ref=e18]:
          - listitem [ref=e19]:
            - link "Dashboard Diklat" [ref=e20] [cursor=pointer]:
              - /url: /dashboard
              - img [ref=e22]
              - generic [ref=e28]: Dashboard Diklat
          - listitem [ref=e29]:
            - link "Diklat" [ref=e30] [cursor=pointer]:
              - /url: /Diklat
              - img [ref=e32]
              - generic [ref=e36]: Diklat
          - listitem [ref=e37]:
            - link "Sertifikat Internal" [ref=e38] [cursor=pointer]:
              - /url: /DiklatInternal/user
              - img [ref=e40]
              - generic [ref=e46]: Sertifikat Internal
          - listitem [ref=e47]:
            - link "Persetujuan" [ref=e48] [cursor=pointer]:
              - /url: /Approve/Diklat
              - img [ref=e50]
              - generic [ref=e53]: Persetujuan
          - listitem [ref=e54]:
            - link "Library Materi" [ref=e55] [cursor=pointer]:
              - /url: /Materi
              - img [ref=e57]
              - generic [ref=e60]: Library Materi
          - listitem [ref=e61]:
            - link "Rencana Diklat" [ref=e62] [cursor=pointer]:
              - /url: /RencanaDiklat/RPT/PF
              - img [ref=e64]
              - generic [ref=e68]: Rencana Diklat
          - listitem [ref=e69]:
            - link "Jadwal Diklat 3" [ref=e70] [cursor=pointer]:
              - /url: /JadwalDiklat/Internal
              - img [ref=e72]
              - generic [ref=e75]:
                - generic [ref=e76]: Jadwal Diklat
                - generic [ref=e77]: "3"
          - listitem [ref=e78]:
            - link "Evaluasi" [ref=e79] [cursor=pointer]:
              - /url: /Diklat/Evaluasi
              - img [ref=e81]
              - generic [ref=e85]: Evaluasi
          - listitem [ref=e86]:
            - link "Laporan" [ref=e87] [cursor=pointer]:
              - /url: /Laporan/Diklat
              - img [ref=e89]
              - generic [ref=e95]: Laporan
          - listitem [ref=e96]:
            - link "Master Data" [ref=e97] [cursor=pointer]:
              - /url: /MasterData/home
              - img [ref=e99]
              - generic [ref=e104]: Master Data
          - listitem [ref=e105]:
            - link "Whattsapp Settings" [ref=e106] [cursor=pointer]:
              - /url: /Settings
              - img [ref=e108]
              - generic [ref=e112]: Whattsapp Settings
          - listitem [ref=e113]:
            - link "Indbox 3" [ref=e114] [cursor=pointer]:
              - /url: /HLC/Home/user
              - img [ref=e116]
              - generic [ref=e119]:
                - generic [ref=e120]: Indbox
                - generic [ref=e121]: "3"
      - generic [ref=e122]:
        - generic [ref=e123]:
          - generic:
            - list
        - list [ref=e124]:
          - listitem [ref=e125]:
            - button "EE EVA EFFENDI" [ref=e126]:
              - generic [ref=e128]: EE
              - generic [ref=e130]: EVA EFFENDI
              - img [ref=e131]
    - main [ref=e134]:
      - generic [ref=e136]:
        - generic [ref=e137]:
          - generic [ref=e138]:
            - heading "Perpustakaan Diklat" [level=1] [ref=e139]
            - paragraph [ref=e140]: Kelola dan akses semua materi pelatihan
          - button "Tambah Materi" [active] [ref=e141]:
            - img [ref=e142]
            - text: Tambah Materi
        - link "Home" [ref=e145] [cursor=pointer]:
          - /url: /Materi
        - generic [ref=e146]:
          - generic [ref=e147]:
            - generic [ref=e148] [cursor=pointer]:
              - img [ref=e150]
              - paragraph [ref=e152]: cuci alat
              - generic [ref=e154]: verified
            - button "Hapus" [ref=e156]
          - generic [ref=e157]:
            - generic [ref=e158] [cursor=pointer]:
              - img [ref=e160]
              - paragraph [ref=e162]: Kebakaran
              - generic [ref=e164]: verified
            - button "Hapus" [ref=e166]
          - generic [ref=e167]:
            - generic [ref=e168] [cursor=pointer]:
              - img [ref=e170]
              - paragraph [ref=e172]: Kesehatan
              - generic [ref=e174]: verified
            - button "Hapus" [ref=e176]
          - generic [ref=e177]:
            - generic [ref=e178] [cursor=pointer]:
              - img [ref=e180]
              - paragraph [ref=e182]: Klinis
              - generic [ref=e184]: verified
            - button "Hapus" [ref=e186]
          - generic [ref=e187]:
            - generic [ref=e188] [cursor=pointer]:
              - img [ref=e190]
              - paragraph [ref=e192]: Public Speaking
              - generic [ref=e194]: verified
            - button "Hapus" [ref=e196]
  - generic [ref=e198]:
    - heading "Tambah Materi" [level=2] [ref=e199]
    - generic [ref=e200]:
      - generic [ref=e201]:
        - generic [ref=e202]: Tipe Konten
        - combobox [ref=e203]:
          - option "Pilih tipe" [disabled]
          - option "Folder Baru" [selected]
          - option "File Dokumen"
      - generic [ref=e204]:
        - generic [ref=e205]: Nama Folder
        - textbox "Marketing 2024" [ref=e206]
    - generic [ref=e207]:
      - button "Simpan Materi" [ref=e208]
      - button "Batal" [ref=e209]
```

# Test source

```ts
  1  | import { test } from '@playwright/test';
  2  | 
  3  | test('test input library materi folder dan file', async ({ page }) => {
  4  |     // 1. Proses Login
  5  |     await page.goto('http://localhost:8000/');
  6  |     await page.getByText('Klik di mana saja untuk').click();
  7  | 
  8  |     await page.locator('input[name="nrp"]').fill('005100439');
  9  |     await page.locator('input[name="password"]').fill('005100439');
  10 |     await page.getByRole('button', { name: /login/i }).click();
  11 | 
  12 |     // Menangani jeda setelah login sebelum masuk ke menu utama
  13 |     await page.waitForURL('**/dashboard');
  14 | 
  15 |     // 2. Masuk ke Menu Library Materi
  16 |     const menuLibrary = page.getByRole('link', { name: /library materi/i });
  17 |     await menuLibrary.waitFor({ state: 'visible' });
  18 |     await menuLibrary.click();
  19 | 
  20 |     // 3. Tambah Materi Pertama (Bentuk Folder)
  21 |     // 3. Tambah Materi Pertama (Bentuk Folder)
  22 |     await page.getByRole('button', { name: /tambah materi/i }).click();
  23 |     
  24 |     // Sesuaikan dengan label opsi yang ada di snapshot ("Folder Baru")
  25 |     await page.getByRole('combobox').selectOption({ label: 'Folder Baru' });
  26 | 
  27 |     // FIX: Ubah 'Nama Materi' menjadi 'Nama Folder' sesuai snapshot halaman
> 28 |     await page.getByRole('textbox', { name: 'Nama Folder' }).fill('Diklat Karyawan');
     |                                                              ^ Error: locator.fill: Test timeout of 30000ms exceeded.
  29 | 
  30 |     // Klik tombol simpan materi folder
  31 |     await page.getByRole('button', { name: /simpan materi/i }).click();
  32 | 
  33 |     // 4. Masuk ke dalam Folder yang baru dibuat
  34 |     const folderBaru = page.getByText('Diklat Karyawan');
  35 |     await folderBaru.waitFor({ state: 'visible' });
  36 |     await folderBaru.click();
  37 | 
  38 |     // 5. Tambah Materi Kedua di dalam Folder (Bentuk File)
  39 |     await page.getByRole('button', { name: /tambah materi/i }).click();
  40 |     await page.getByRole('combobox').selectOption('file');
  41 |     
  42 |     // REVISI: Mengganti '/Materi/store' dengan locator name yang benar
  43 |     await page.getByRole('textbox', { name: 'Nama Materi' }).fill('Diklat Kebijakan 2026');
  44 | 
  45 |     // Proses Upload File (Pastikan file PDF ini ada di root folder project Anda)
  46 |     const fileInput = page.locator('input[type="file"]');
  47 |     if ((await fileInput.count()) > 0) {
  48 |         await fileInput.setInputFiles('hasil_turnitin_1767684375748_Revisi.pdf');
  49 |     } else {
  50 |         // Jika input file tersembunyi dan menggunakan library eksternal, tembak tombol pemicunya
  51 |         await page.getByRole('button', { name: /choose file|pilih file/i }).setInputFiles('hasil_turnitin_1767684375748_Revisi.pdf');
  52 |     }
  53 | 
  54 |     // 6. Simpan dan Verifikasi
  55 |     await page.getByRole('button', { name: /simpan materi/i }).click();
  56 |     
  57 |     // Berikan sedikit jeda/nunggu tombol verifikasi muncul setelah proses upload & simpan selesai
  58 |     const tombolVerifikasi = page.getByRole('button', { name: /verifikasi/i });
  59 |     await tombolVerifikasi.waitFor({ state: 'visible' });
  60 |     await tombolVerifikasi.click();
  61 | });
```