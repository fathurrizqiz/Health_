# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: DiklatEksternal\Eksternal.spec.ts >> test
- Location: tests\Playwright\DiklatEksternal\Eksternal.spec.ts:3:1

# Error details

```
Test timeout of 30000ms exceeded.
```

```
Error: locator.click: Test timeout of 30000ms exceeded.
Call log:
  - waiting for getByRole('button', { name: 'Hadir' })

```

# Page snapshot

```yaml
- generic [ref=e3]:
  - generic [ref=e6]:
    - list [ref=e8]:
      - listitem [ref=e9]:
        - link "Eichar" [ref=e10] [cursor=pointer]:
          - /url: /dashboard
          - generic [ref=e13]: Eichar
    - generic [ref=e15]:
      - generic [ref=e16]: Platform
      - list [ref=e17]:
        - listitem [ref=e18]:
          - link "Dashboard Diklat" [ref=e19] [cursor=pointer]:
            - /url: /dashboard
            - img [ref=e21]
            - generic [ref=e27]: Dashboard Diklat
        - listitem [ref=e28]:
          - link "Diklat" [ref=e29] [cursor=pointer]:
            - /url: /Diklat
            - img [ref=e31]
            - generic [ref=e35]: Diklat
        - listitem [ref=e36]:
          - link "Diklat Internal" [ref=e37] [cursor=pointer]:
            - /url: /DiklatInternal/user
            - img [ref=e39]
            - generic [ref=e43]: Diklat Internal
        - listitem [ref=e44]:
          - link "Persetujuan 2" [ref=e45] [cursor=pointer]:
            - /url: /Approve/Diklat
            - img [ref=e47]
            - generic [ref=e49]:
              - generic [ref=e50]: Persetujuan
              - generic [ref=e51]: "2"
        - listitem [ref=e52]:
          - link "Library Materi" [ref=e53] [cursor=pointer]:
            - /url: /Materi
            - img [ref=e55]
            - generic [ref=e58]: Library Materi
        - listitem [ref=e59]:
          - link "Rencana Diklat" [ref=e60] [cursor=pointer]:
            - /url: /RencanaDiklat/RPT/PF
            - img [ref=e62]
            - generic [ref=e66]: Rencana Diklat
        - listitem [ref=e67]:
          - link "Jadwal Diklat 5" [ref=e68] [cursor=pointer]:
            - /url: /JadwalDiklat/Internal
            - img [ref=e70]
            - generic [ref=e73]:
              - generic [ref=e74]: Jadwal Diklat
              - generic [ref=e75]: "5"
        - listitem [ref=e76]:
          - link "Evaluasi" [ref=e77] [cursor=pointer]:
            - /url: /Diklat/Evaluasi
            - img [ref=e79]
            - generic [ref=e83]: Evaluasi
        - listitem [ref=e84]:
          - link "Laporan" [ref=e85] [cursor=pointer]:
            - /url: /Laporan/Diklat
            - img [ref=e87]
            - generic [ref=e93]: Laporan
        - listitem [ref=e94]:
          - link "Master Data" [ref=e95] [cursor=pointer]:
            - /url: /MasterData/home
            - img [ref=e97]
            - generic [ref=e102]: Master Data
        - listitem [ref=e103]:
          - link "Whattsapp Settings" [ref=e104] [cursor=pointer]:
            - /url: /Settings
            - img [ref=e106]
            - generic [ref=e110]: Whattsapp Settings
        - listitem [ref=e111]:
          - link "Indbox" [ref=e112] [cursor=pointer]:
            - /url: /HLC/Home/user
            - img [ref=e114]
            - generic [ref=e118]: Indbox
    - generic [ref=e119]:
      - generic [ref=e120]:
        - generic:
          - list
      - list [ref=e121]:
        - listitem [ref=e122]:
          - button "EE EVA EFFENDI" [ref=e123]:
            - generic [ref=e125]: EE
            - generic [ref=e127]: EVA EFFENDI
            - img [ref=e128]
  - main [ref=e131]:
    - generic [ref=e132]:
      - generic [ref=e133]:
        - heading "Jadwal Diklat Saya" [level=1] [ref=e134]
        - paragraph [ref=e135]: Daftar seluruh pelatihan mendatang yang Anda ikuti.
      - generic [ref=e136]:
        - generic [ref=e137]:
          - img [ref=e139]
          - textbox "Cari kegiatan..." [ref=e141]
        - generic [ref=e142]:
          - generic [ref=e143]: "Pilih Template Pesan:"
          - combobox [ref=e144]:
            - option "-- Pilih Template --" [disabled]
            - option "Notifikasi Jadwal" [selected]
            - option "Jadwal-Formal"
          - paragraph [ref=e145]: "*Pilih template terlebih dahulu sebelum klik 'Umumkan WA'"
        - generic [ref=e146]:
          - button "internal" [ref=e147]
          - button "hlc" [ref=e148]
          - button "eksternal" [active] [ref=e149]
      - generic [ref=e150]:
        - button "Tambah Nomor HP" [ref=e151]:
          - img [ref=e152]
          - text: Tambah Nomor HP
        - button "History" [ref=e154]:
          - img [ref=e155]
          - text: History
```

# Test source

```ts
  1  | import { test, expect } from '@playwright/test';
  2  | 
  3  | test('test', async ({ page }) => {
  4  |   await page.goto('http://localhost:8000/');
  5  |   await page.getByText('Klik di mana saja untuk').click();
  6  |   await page.locator('input[name="nrp"]').click();
  7  |   await page.locator('input[name="nrp"]').fill('005100439');
  8  |   await page.locator('input[name="password"]').click();
  9  |   await page.locator('input[name="password"]').fill('005100439');
  10 |   await page.getByRole('button', { name: 'Login' }).click();
  11 |   await page.waitForURL('**/dashboard'); 
  12 |   const Rencana = page.getByRole('link', { name: 'Rencana Diklat' });
  13 |   await Rencana.waitFor({ state: 'visible' });
  14 |   await Rencana.click();
  15 |   const Eksternal = page.getByRole('link', { name: 'Eksternal' });
  16 |   await Eksternal.waitFor({ state: 'visible' });
  17 |   await Eksternal.click();
  18 |   await page.getByRole('button', { name: 'Tambah Peserta' }).nth(1).click();
  19 |   await page.getByRole('textbox', { name: 'Ketik NRP/Nama...' }).click();
  20 |   await page.getByRole('textbox', { name: 'Ketik NRP/Nama...' }).fill('eva');
  21 |   await page.getByText('- EVA EFFENDI').click();
  22 | //   perlu di ubah jika ingin di test
  23 |   await page.getByRole('textbox').nth(2).fill('2025-05-19');
  24 |   await page.getByRole('textbox').nth(3).fill('2026-05-19');
  25 |   await page.getByRole('textbox').nth(2).fill('2026-05-19');
  26 | //   
  27 |   await page.getByRole('spinbutton').click();
  28 |   await page.getByRole('spinbutton').fill('01');
  29 |   await page.getByRole('textbox').nth(4).click();
  30 |   await page.getByRole('textbox').nth(4).fill('RSMDH');
  31 |   await page.getByRole('button', { name: 'Simpan' }).click();
  32 |   await page.goto('http://localhost:8000/RencanaDiklat/RPT/PN');
  33 |   await page.getByRole('link', { name: 'Indbox' }).click();
  34 |   await page.getByRole('button', { name: 'Setujui' }).first().click();
  35 |   await page.getByRole('link', { name: 'Jadwal Diklat' }).click();
  36 |   await page.getByRole('button', { name: 'eksternal' }).click();
  37 |   page.once('dialog', dialog => {
  38 |     console.log(`Dialog message: ${dialog.message()}`);
  39 |     dialog.dismiss().catch(() => {});
  40 |   });
> 41 |   await page.getByRole('button', { name: 'Hadir' }).click();
     |                                                     ^ Error: locator.click: Test timeout of 30000ms exceeded.
  42 | });
```