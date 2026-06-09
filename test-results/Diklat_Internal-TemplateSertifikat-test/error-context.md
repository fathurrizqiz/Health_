# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: Diklat_Internal\TemplateSertifikat.spec.ts >> test
- Location: tests\Playwright\Diklat_Internal\TemplateSertifikat.spec.ts:3:1

# Error details

```
Test timeout of 30000ms exceeded.
```

```
Error: locator.click: Test timeout of 30000ms exceeded.
Call log:
  - waiting for getByRole('button', { name: 'Simpan' })

```

# Page snapshot

```yaml
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
          - link "Dashboard Diklat (admin)" [ref=e20] [cursor=pointer]:
            - /url: /dashboard
            - img [ref=e22]
            - generic [ref=e28]: Dashboard Diklat (admin)
        - listitem [ref=e29]:
          - link "Dashboard Diklat (user)" [ref=e30] [cursor=pointer]:
            - /url: http://localhost:8000/dashboard/user
            - img [ref=e32]
            - generic [ref=e38]: Dashboard Diklat (user)
        - listitem [ref=e39]:
          - link "Diklat" [ref=e40] [cursor=pointer]:
            - /url: /Diklat
            - img [ref=e42]
            - generic [ref=e46]: Diklat
        - listitem [ref=e47]:
          - link "Sertifikat Internal" [ref=e48] [cursor=pointer]:
            - /url: /DiklatInternal/user
            - img [ref=e50]
            - generic [ref=e56]: Sertifikat Internal
        - listitem [ref=e57]:
          - link "Persetujuan" [ref=e58] [cursor=pointer]:
            - /url: /Persetujuan
            - img [ref=e60]
            - generic [ref=e63]: Persetujuan
        - listitem [ref=e64]:
          - link "Library Materi" [ref=e65] [cursor=pointer]:
            - /url: /Materi
            - img [ref=e67]
            - generic [ref=e70]: Library Materi
        - listitem [ref=e71]:
          - link "Rencana Diklat" [ref=e72] [cursor=pointer]:
            - /url: /RencanaDiklat/RPT/PF
            - img [ref=e74]
            - generic [ref=e78]: Rencana Diklat
        - listitem [ref=e79]:
          - link "Jadwal Diklat 1" [ref=e80] [cursor=pointer]:
            - /url: /JadwalDiklat/Internal
            - img [ref=e82]
            - generic [ref=e85]:
              - generic [ref=e86]: Jadwal Diklat
              - generic [ref=e87]: "1"
        - listitem [ref=e88]:
          - link "Evaluasi" [ref=e89] [cursor=pointer]:
            - /url: /Diklat/Evaluasi
            - img [ref=e91]
            - generic [ref=e95]: Evaluasi
        - listitem [ref=e96]:
          - link "Laporan" [ref=e97] [cursor=pointer]:
            - /url: /Laporan/Diklat
            - img [ref=e99]
            - generic [ref=e105]: Laporan
        - listitem [ref=e106]:
          - link "Master Data" [ref=e107] [cursor=pointer]:
            - /url: /MasterData/home
            - img [ref=e109]
            - generic [ref=e114]: Master Data
        - listitem [ref=e115]:
          - link "Whattsapp Settings" [ref=e116] [cursor=pointer]:
            - /url: /Settings
            - img [ref=e118]
            - generic [ref=e122]: Whattsapp Settings
        - listitem [ref=e123]:
          - link "Inbox" [ref=e124] [cursor=pointer]:
            - /url: /HLC/Home/user
            - img [ref=e126]
            - generic [ref=e130]: Inbox
    - generic [ref=e131]:
      - generic [ref=e132]:
        - generic:
          - list
      - list [ref=e133]:
        - listitem [ref=e134]:
          - button "EE EVA EFFENDI" [ref=e135]:
            - generic [ref=e137]: EE
            - generic [ref=e139]: EVA EFFENDI
            - img [ref=e140]
  - main [ref=e143]:
    - generic [ref=e144]:
      - heading "Aksi Diklat" [level=1] [ref=e145]
      - generic [ref=e146]:
        - heading "Pilih Periode" [level=2] [ref=e148]
        - combobox [ref=e149]:
          - option "-- Pilih periode pelaksanaan --" [selected]
          - option "2026-06-09 - MINAR NAPITUPULU, DR. (RSMDH)"
        - generic [ref=e150]:
          - generic [ref=e151]: "Status Periode:"
          - generic [ref=e152]: Belum dimulai
      - generic [ref=e153]:
        - heading "Durasi Diklat (Jam)" [level=2] [ref=e154]
        - textbox "0" [ref=e155]
      - button "Pilih periode dulu" [disabled] [ref=e157] [cursor=pointer]
      - generic [ref=e158]:
        - heading "Link Pree-Test :" [level=3] [ref=e160]
        - heading "Link Post-Test :" [level=3] [ref=e162]
        - heading "Link Evaluasi :" [level=3] [ref=e164]
      - generic [ref=e165]:
        - heading "Manajemen Test" [level=2] [ref=e166]
        - generic [ref=e167]:
          - button "Pre-test" [ref=e168]
          - button "Post-test" [ref=e169]
      - generic [ref=e170]:
        - heading "Template Sertifikat" [level=2] [ref=e171]
        - button "Buat Template" [active] [ref=e172]
      - generic [ref=e173]:
        - heading "Navigasi Cepat" [level=2] [ref=e174]
        - generic [ref=e175]:
          - button "Detail Periode" [ref=e176]
          - button "Data Presensi" [ref=e177]
          - button "Sertifikat" [ref=e178]
          - button "Dokumentasi" [ref=e179]
```

# Test source

```ts
  1  | import { test, expect } from '@playwright/test';
  2  | 
  3  | test('test', async ({ page }) => {
  4  |   await page.goto('http://localhost:8000/login');
  5  |   await page.getByRole('textbox', { name: 'Enter your Employee ID' }).click();
  6  |   await page.getByRole('textbox', { name: 'Enter your Employee ID' }).fill('005100439');
  7  |   await page.getByRole('textbox', { name: 'Enter Password' }).click();
  8  |   await page.getByRole('textbox', { name: 'Enter Password' }).fill('005100439');
  9  |   await page.getByRole('button', { name: 'Sign in →' }).click();
  10 |   await page.getByRole('link', { name: 'Rencana Diklat' }).click();
  11 |   await page.goto('http://localhost:8000/RencanaDiklat/RPT/PF');
  12 |   await page.getByRole('heading', { name: 'Non-Klinis' }).click();
  13 |   await page.locator('div:nth-child(4) > .overflow-x-auto > .w-full > .divide-y > .cursor-pointer > .flex.gap-3 > .flex.w-20.justify-center.gap-2.rounded-xl.bg-gradient-to-r.from-blue-600').click();
  14 |   await page.getByRole('combobox').selectOption('47');
  15 |   await page.goto('http://localhost:8000/RencanaDiklat/Internal/detail/aksi/44');
  16 |   await page.getByRole('button', { name: 'Buat Template' }).click();
> 17 |   await page.getByRole('button', { name: 'Simpan' }).click();
     |                                                      ^ Error: locator.click: Test timeout of 30000ms exceeded.
  18 |   await expect(page.locator('text=isi materi Diklat Minimal 1! ')).toBeVisible();
  19 | });
```