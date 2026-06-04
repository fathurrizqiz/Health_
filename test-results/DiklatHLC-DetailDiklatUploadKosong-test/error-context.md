# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: DiklatHLC\DetailDiklatUploadKosong.spec.ts >> test
- Location: tests\Playwright\DiklatHLC\DetailDiklatUploadKosong.spec.ts:3:1

# Error details

```
Error: expect(locator).toBeVisible() failed

Locator: getByRole('textbox', { name: /ketik nama atau nrp karyawan/i })
Expected: visible
Timeout: 5000ms
Error: element(s) not found

Call log:
  - Expect "toBeVisible" with timeout 5000ms
  - waiting for getByRole('textbox', { name: /ketik nama atau nrp karyawan/i })

```

```yaml
- list:
  - listitem:
    - link "Eichar":
      - /url: /dashboard
- text: Platform
- list:
  - listitem:
    - link "Dashboard Diklat (admin)":
      - /url: /dashboard
      - img
      - text: Dashboard Diklat (admin)
  - listitem:
    - link "Dashboard Diklat (user)":
      - /url: http://localhost:8000/dashboard/user
      - img
      - text: Dashboard Diklat (user)
  - listitem:
    - link "Diklat":
      - /url: /Diklat
      - img
      - text: Diklat
  - listitem:
    - link "Sertifikat Internal":
      - /url: /DiklatInternal/user
      - img
      - text: Sertifikat Internal
  - listitem:
    - link "Persetujuan":
      - /url: /Persetujuan
      - img
      - text: Persetujuan
  - listitem:
    - link "Library Materi":
      - /url: /Materi
      - img
      - text: Library Materi
  - listitem:
    - link "Rencana Diklat":
      - /url: /RencanaDiklat/RPT/PF
      - img
      - text: Rencana Diklat
  - listitem:
    - link "Jadwal Diklat":
      - /url: /JadwalDiklat/Internal
      - img
      - text: Jadwal Diklat
  - listitem:
    - link "Evaluasi":
      - /url: /Diklat/Evaluasi
      - img
      - text: Evaluasi
  - listitem:
    - link "Laporan":
      - /url: /Laporan/Diklat
      - img
      - text: Laporan
  - listitem:
    - link "Master Data":
      - /url: /MasterData/home
      - img
      - text: Master Data
  - listitem:
    - link "Whattsapp Settings":
      - /url: /Settings
      - img
      - text: Whattsapp Settings
  - listitem:
    - link "Inbox":
      - /url: /HLC/Home/user
      - img
      - text: Inbox
- list
- list:
  - listitem:
    - button "EE EVA EFFENDI":
      - text: EE EVA EFFENDI
      - img
- main:
  - navigation:
    - link "Internal":
      - /url: /RencanaDiklat/RPT/PF
    - link "Eksternal":
      - /url: /RencanaDiklat/RPT/PN
    - link "HLC":
      - /url: /HLC/Home/manajemen
  - img
  - heading "Human Learning Center (HLC)" [level=1]
  - paragraph: Kelola data program pelatihan dan detail diklat peserta.
  - button "Tambah Program":
    - img
    - text: Tambah Program
  - text: Cari Program
  - textbox "Ketik nama program..."
  - img
  - text: Tahun
  - combobox:
    - option "Semua Tahun" [selected]
    - option "2026"
  - text: Tampilan
  - combobox:
    - option "5 baris" [selected]
    - option "10 baris"
    - option "20 baris"
  - text: Menampilkan 5 dari 5 program
  - heading "Internal" [level=3]
  - text: "Tahun: 2026"
  - button:
    - img
  - button:
    - img
  - button:
    - img
  - table:
    - rowgroup:
      - row "Nama Diklat Tanggal Mulai Tanggal Selesai Jam Diklat Status Alasan":
        - columnheader "Nama Diklat"
        - columnheader "Tanggal Mulai"
        - columnheader "Tanggal Selesai"
        - columnheader "Jam Diklat"
        - columnheader "Status"
        - columnheader "Alasan"
    - rowgroup:
      - row "Kebijakan 2026 2026-02-19 2026-02-19 2 jam approved E EVA EFFENDI":
        - cell "Kebijakan 2026"
        - cell "2026-02-19"
        - cell "2026-02-19"
        - cell "2 jam"
        - cell "approved"
        - cell
        - cell "E EVA EFFENDI"
        - cell:
          - button:
            - img
          - button:
            - img
      - row "Lihat undangan 2026-05-30 2026-05-30 3 jam Tolak Lihat Alasan E EVA EFFENDI":
        - cell "Lihat undangan":
          - button "Lihat undangan"
        - cell "2026-05-30"
        - cell "2026-05-30"
        - cell "3 jam"
        - cell "Tolak"
        - cell "Lihat Alasan":
          - button "Lihat Alasan"
        - cell "E EVA EFFENDI"
        - cell:
          - button:
            - img
          - button:
            - img
      - row "Lihat undangan 2026-05-25 2026-05-25 1 jam pending E EVA EFFENDI":
        - cell "Lihat undangan":
          - button "Lihat undangan"
        - cell "2026-05-25"
        - cell "2026-05-25"
        - cell "1 jam"
        - cell "pending"
        - cell
        - cell "E EVA EFFENDI"
        - cell:
          - button:
            - img
          - button:
            - img
      - row "Lihat undangan 2026-05-26 2026-05-28 3 jam approved E EVA EFFENDI":
        - cell "Lihat undangan":
          - button "Lihat undangan"
        - cell "2026-05-26"
        - cell "2026-05-28"
        - cell "3 jam"
        - cell "approved"
        - cell
        - cell "E EVA EFFENDI"
        - cell:
          - button:
            - img
          - button:
            - img
      - row "Lihat undangan 2026-06-02 2026-06-03 18 jam approved E EVA EFFENDI":
        - cell "Lihat undangan":
          - button "Lihat undangan"
        - cell "2026-06-02"
        - cell "2026-06-03"
        - cell "18 jam"
        - cell "approved"
        - cell
        - cell "E EVA EFFENDI"
        - cell:
          - button:
            - img
          - button:
            - img
  - heading "Formal Program" [level=3]
  - text: "Tahun: 2026"
  - button:
    - img
  - button:
    - img
  - button:
    - img
  - table:
    - rowgroup:
      - row "Nama Diklat Tanggal Mulai Tanggal Selesai Jam Diklat Status Alasan":
        - columnheader "Nama Diklat"
        - columnheader "Tanggal Mulai"
        - columnheader "Tanggal Selesai"
        - columnheader "Jam Diklat"
        - columnheader "Status"
        - columnheader "Alasan"
    - rowgroup:
      - row "Pelatihan Public Speaking 2026-01-21 2026-01-21 2 jam approved E EVA EFFENDI":
        - cell "Pelatihan Public Speaking"
        - cell "2026-01-21"
        - cell "2026-01-21"
        - cell "2 jam"
        - cell "approved"
        - cell
        - cell "E EVA EFFENDI"
        - cell:
          - button:
            - img
          - button:
            - img
      - row "pelatihan manajerial klinis 2026-01-21 2026-01-21 2 jam approved A ARIF JUNIANTO":
        - cell "pelatihan manajerial klinis"
        - cell "2026-01-21"
        - cell "2026-01-21"
        - cell "2 jam"
        - cell "approved"
        - cell
        - cell "A ARIF JUNIANTO"
        - cell:
          - button:
            - img
          - button:
            - img
      - row "Diklat Karyawan 2026-05-19 2026-05-19 1 jam approved E EVA EFFENDI":
        - cell "Diklat Karyawan"
        - cell "2026-05-19"
        - cell "2026-05-19"
        - cell "1 jam"
        - cell "approved"
        - cell
        - cell "E EVA EFFENDI"
        - cell:
          - button:
            - img
          - button:
            - img
      - row "Diklat Karyawan 2026-05-24 2026-05-24 1 jam rejected E EVA EFFENDI":
        - cell "Diklat Karyawan"
        - cell "2026-05-24"
        - cell "2026-05-24"
        - cell "1 jam"
        - cell "rejected"
        - cell
        - cell "E EVA EFFENDI"
        - cell:
          - button:
            - img
          - button:
            - img
      - row "Diklat Karyawan 2026-05-19 2026-05-19 1 jam rejected E EVA EFFENDI":
        - cell "Diklat Karyawan"
        - cell "2026-05-19"
        - cell "2026-05-19"
        - cell "1 jam"
        - cell "rejected"
        - cell
        - cell "E EVA EFFENDI"
        - cell:
          - button:
            - img
          - button:
            - img
      - row "Lihat undangan 2026-06-02 2026-06-02 1 jam approved E EVA EFFENDI":
        - cell "Lihat undangan":
          - button "Lihat undangan"
        - cell "2026-06-02"
        - cell "2026-06-02"
        - cell "1 jam"
        - cell "approved"
        - cell
        - cell "E EVA EFFENDI"
        - cell:
          - button:
            - img
          - button:
            - img
  - heading "Public Speaking" [level=3]
  - text: "Tahun: 2026"
  - button:
    - img
  - button:
    - img
  - button:
    - img
  - table:
    - rowgroup:
      - row "Nama Diklat Tanggal Mulai Tanggal Selesai Jam Diklat Status Alasan":
        - columnheader "Nama Diklat"
        - columnheader "Tanggal Mulai"
        - columnheader "Tanggal Selesai"
        - columnheader "Jam Diklat"
        - columnheader "Status"
        - columnheader "Alasan"
    - rowgroup:
      - row "Diklat Public Speaking RSMDH 2026-05-06 2026-05-06 2 jam approved E EVA EFFENDI":
        - cell "Diklat Public Speaking RSMDH"
        - cell "2026-05-06"
        - cell "2026-05-06"
        - cell "2 jam"
        - cell "approved"
        - cell
        - cell "E EVA EFFENDI"
        - cell:
          - button:
            - img
          - button:
            - img
      - row "Teknik Observatif 2026-05-10 2026-05-11 4 jam approved E EVA EFFENDI":
        - cell "Teknik Observatif"
        - cell "2026-05-10"
        - cell "2026-05-11"
        - cell "4 jam"
        - cell "approved"
        - cell
        - cell "E EVA EFFENDI"
        - cell:
          - button:
            - img
          - button:
            - img
      - row "Lihat undangan 2026-05-27 2026-05-28 4 jam approved M MUHAMMAD NUR SALAM":
        - cell "Lihat undangan":
          - button "Lihat undangan"
        - cell "2026-05-27"
        - cell "2026-05-28"
        - cell "4 jam"
        - cell "approved"
        - cell
        - cell "M MUHAMMAD NUR SALAM"
        - cell:
          - button:
            - img
          - button:
            - img
      - row "Lihat undangan 2026-05-28 2026-05-29 2 jam menunggu_persetujuan E EVA EFFENDI":
        - cell "Lihat undangan":
          - button "Lihat undangan"
        - cell "2026-05-28"
        - cell "2026-05-29"
        - cell "2 jam"
        - cell "menunggu_persetujuan"
        - cell
        - cell "E EVA EFFENDI"
        - cell:
          - button:
            - img
          - button:
            - img
      - row "Diklat Presenter 2026-05-17 2026-05-17 1 jam approved E EVA EFFENDI":
        - cell "Diklat Presenter"
        - cell "2026-05-17"
        - cell "2026-05-17"
        - cell "1 jam"
        - cell "approved"
        - cell
        - cell "E EVA EFFENDI"
        - cell:
          - button:
            - img
          - button:
            - img
      - row "Public Learning 2026-05-11 2026-05-11 1 jam approved E EVA EFFENDI":
        - cell "Public Learning"
        - cell "2026-05-11"
        - cell "2026-05-11"
        - cell "1 jam"
        - cell "approved"
        - cell
        - cell "E EVA EFFENDI"
        - cell:
          - button:
            - img
          - button:
            - img
  - heading "Diklat Kebersihan" [level=3]
  - text: "Tahun: 2026"
  - button:
    - img
  - button:
    - img
  - button:
    - img
  - table:
    - rowgroup:
      - row "Nama Diklat Tanggal Mulai Tanggal Selesai Jam Diklat Status Alasan":
        - columnheader "Nama Diklat"
        - columnheader "Tanggal Mulai"
        - columnheader "Tanggal Selesai"
        - columnheader "Jam Diklat"
        - columnheader "Status"
        - columnheader "Alasan"
    - rowgroup:
      - row "Pelatihan Kebersihan 2026-05-10 2026-05-10 1 jam approved E EVA EFFENDI":
        - cell "Pelatihan Kebersihan"
        - cell "2026-05-10"
        - cell "2026-05-10"
        - cell "1 jam"
        - cell "approved"
        - cell
        - cell "E EVA EFFENDI"
        - cell:
          - button:
            - img
          - button:
            - img
      - row "Diklat Klinis 2026-05-15 2026-05-15 2 jam approved E EVA EFFENDI":
        - cell "Diklat Klinis"
        - cell "2026-05-15"
        - cell "2026-05-15"
        - cell "2 jam"
        - cell "approved"
        - cell
        - cell "E EVA EFFENDI"
        - cell:
          - button:
            - img
          - button:
            - img
      - row "Klinis part2 2026-05-15 2026-05-15 2 jam approved E EVA EFFENDI":
        - cell "Klinis part2"
        - cell "2026-05-15"
        - cell "2026-05-15"
        - cell "2 jam"
        - cell "approved"
        - cell
        - cell "E EVA EFFENDI"
        - cell:
          - button:
            - img
          - button:
            - img
      - row "Lihat undangan 2026-05-28 2026-05-29 4 jam approved S SURYAN LESTIANTO":
        - cell "Lihat undangan":
          - button "Lihat undangan"
        - cell "2026-05-28"
        - cell "2026-05-29"
        - cell "4 jam"
        - cell "approved"
        - cell
        - cell "S SURYAN LESTIANTO"
        - cell:
          - button:
            - img
          - button:
            - img
      - row "Diklat Cleaning 2026-05-19 2026-05-19 1 jam approved M MUHAMMAD NUR SALAM":
        - cell "Diklat Cleaning"
        - cell "2026-05-19"
        - cell "2026-05-19"
        - cell "1 jam"
        - cell "approved"
        - cell
        - cell "M MUHAMMAD NUR SALAM"
        - cell:
          - button:
            - img
          - button:
            - img
  - heading "Diklat Skin Care" [level=3]
  - text: "Tahun: 2026"
  - button:
    - img
  - button:
    - img
  - button:
    - img
  - img
  - text: Belum ada data diklat untuk program ini.
  - heading "Tambah Program" [level=3]
  - text: Nama Program
  - textbox
  - text: Tahun
  - spinbutton: "2026"
  - button "Batal"
  - button "Simpan"
```

# Test source

```ts
  1  | import { expect, test } from '@playwright/test';
  2  | 
  3  | test('test', async ({ page }) => {
  4  |     await page.goto('http://localhost:8000/login');
  5  |     await page.getByRole('textbox', { name: 'Enter your Employee ID' }).click();
  6  |     await page
  7  |         .getByRole('textbox', { name: 'Enter your Employee ID' })
  8  |         .fill('005100439');
  9  |     await page.getByRole('textbox', { name: 'Enter Password' }).click();
  10 |     await page
  11 |         .getByRole('textbox', { name: 'Enter Password' })
  12 |         .fill('005100439');
  13 |     await page.getByRole('button', { name: 'Sign in →' }).click();
  14 |     await page.getByRole('link', { name: 'Rencana Diklat' }).click();
  15 | 
  16 |     // Tunggu navbar HLC muncul
  17 |   const hlcLink = page.locator('a[href="/HLC/Home/manajemen"]');
  18 | 
  19 |   await expect(hlcLink).toBeVisible({
  20 |     timeout: 10000,
  21 |   });
  22 | 
  23 |     // Masuk halaman HLC
  24 |     await hlcLink.click();
  25 | 
  26 |     await expect(page).toHaveURL(/HLC\/Home\/manajemen/);
  27 | 
  28 |     await page.getByRole('heading', { name: 'Diklat Skin Care' }).click();
  29 | 
  30 | const tambahButton = page.getByRole('button', {
  31 |   name: /tambah/i,
  32 | });
  33 | 
  34 | await expect(tambahButton).toBeVisible();
  35 | await tambahButton.click();
  36 | 
  37 | // tunggu modal/form muncul
  38 | await expect(
  39 |   page.getByRole('textbox', {
  40 |     name: /ketik nama atau nrp karyawan/i,
  41 |   })
> 42 | ).toBeVisible();
     |   ^ Error: expect(locator).toBeVisible() failed
  43 | 
  44 |     // Isi form
  45 |     await page.getByRole('textbox').nth(1).fill('1');
  46 |     await page.getByRole('textbox').nth(2).fill('2026-06-04');
  47 |     await page.getByRole('textbox').nth(3).fill('2026-06-04');
  48 | 
  49 |     await page
  50 |         .getByRole('textbox', {
  51 |             name: 'Ketik nama atau NRP karyawan',
  52 |         })
  53 |         .fill('eva');
  54 | 
  55 |     await page
  56 |         .getByText('EVA EFFENDI', {
  57 |             exact: true,
  58 |         })
  59 |         .click();
  60 | 
  61 |     await page
  62 |         .getByRole('button', {
  63 |             name: 'Simpan Diklat',
  64 |         })
  65 |         .click();
  66 |     await expect(
  67 |     page.getByText('Gagal menambahkan detail: The dokumen field is required.')
  68 |   ).toBeVisible();
  69 | });
  70 | 
```