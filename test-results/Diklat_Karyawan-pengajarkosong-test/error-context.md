# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: Diklat_Karyawan\pengajarkosong.spec.ts >> test
- Location: tests\Playwright\Diklat_Karyawan\pengajarkosong.spec.ts:3:1

# Error details

```
Test timeout of 30000ms exceeded.
```

```
Error: locator.click: Test timeout of 30000ms exceeded.
Call log:
  - waiting for getByRole('link', { name: 'Diklat', description: 'Diklat', exact: true })

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
          - link "Jadwal Diklat 1" [ref=e70] [cursor=pointer]:
            - /url: /JadwalDiklat/Internal
            - img [ref=e72]
            - generic [ref=e75]:
              - generic [ref=e76]: Jadwal Diklat
              - generic [ref=e77]: "1"
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
          - link "Indbox 2" [ref=e114] [cursor=pointer]:
            - /url: /HLC/Home/user
            - img [ref=e116]
            - generic [ref=e119]:
              - generic [ref=e120]: Indbox
              - generic [ref=e121]: "2"
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
    - textbox "Cari bagian ..." [ref=e136]
    - generic [ref=e137]:
      - generic [ref=e138]:
        - generic [ref=e139]:
          - generic [ref=e140]:
            - generic [ref=e141]: Total Karyawan
            - generic [ref=e142]: "631"
          - img [ref=e144]
        - generic [ref=e146]:
          - generic [ref=e147]:
            - generic [ref=e148]: Total Jam Target
            - generic [ref=e149]: "135420"
          - img [ref=e151]
        - generic [ref=e153]:
          - generic [ref=e154]:
            - generic [ref=e155]: Terlaksana (2026)
            - generic [ref=e156]: "87"
          - img [ref=e158]
      - generic [ref=e160]:
        - heading "Detail per Bagian (2026)" [level=2] [ref=e162]
        - table [ref=e164]:
          - rowgroup [ref=e165]:
            - row "Nama Bagian Jml. Karyawan Target Jam Aktual Jam Pencapaian (%)" [ref=e166]:
              - columnheader "Nama Bagian" [ref=e167]
              - columnheader "Jml. Karyawan" [ref=e168]
              - columnheader "Target Jam" [ref=e169]
              - columnheader "Aktual Jam" [ref=e170]
              - columnheader "Pencapaian (%)" [ref=e171]
          - rowgroup [ref=e172]:
            - row "KEPERAWATAN 330 77400.0 6 0.01%" [ref=e173]:
              - cell "KEPERAWATAN" [ref=e174]
              - cell "330" [ref=e175]
              - cell "77400.0" [ref=e176]
              - cell "6" [ref=e177]
              - cell "0.01%" [ref=e178]:
                - generic [ref=e181]: 0.01%
            - row "PENUNJANG UMUM 51 7980.0 0 0%" [ref=e182]:
              - cell "PENUNJANG UMUM" [ref=e183]
              - cell "51" [ref=e184]
              - cell "7980.0" [ref=e185]
              - cell "0" [ref=e186]
              - cell "0%" [ref=e187]:
                - generic [ref=e190]: 0%
            - row "PELAYANAN MEDIS 59 13800.0 0 0%" [ref=e191]:
              - cell "PELAYANAN MEDIS" [ref=e192]
              - cell "59" [ref=e193]
              - cell "13800.0" [ref=e194]
              - cell "0" [ref=e195]
              - cell "0%" [ref=e196]:
                - generic [ref=e199]: 0%
            - row "KEUANGAN 32 4920.0 0 0%" [ref=e200]:
              - cell "KEUANGAN" [ref=e201]
              - cell "32" [ref=e202]
              - cell "4920.0" [ref=e203]
              - cell "0" [ref=e204]
              - cell "0%" [ref=e205]:
                - generic [ref=e208]: 0%
            - row "YANMED 1 240.0 0 0%" [ref=e209]:
              - cell "YANMED" [ref=e210]
              - cell "1" [ref=e211]
              - cell "240.0" [ref=e212]
              - cell "0" [ref=e213]
              - cell "0%" [ref=e214]:
                - generic [ref=e217]: 0%
            - row "IT 2 300.0 7 2.33%" [ref=e218]:
              - cell "IT" [ref=e219]
              - cell "2" [ref=e220]
              - cell "300.0" [ref=e221]
              - cell "7" [ref=e222]
              - cell "2.33%" [ref=e223]:
                - generic [ref=e227]: 2.33%
            - row "PENUNJANG MEDIS 90 20310.0 0 0%" [ref=e228]:
              - cell "PENUNJANG MEDIS" [ref=e229]
              - cell "90" [ref=e230]
              - cell "20310.0" [ref=e231]
              - cell "0" [ref=e232]
              - cell "0%" [ref=e233]:
                - generic [ref=e236]: 0%
            - row "SPI 1 150.0 0 0%" [ref=e237]:
              - cell "SPI" [ref=e238]
              - cell "1" [ref=e239]
              - cell "150.0" [ref=e240]
              - cell "0" [ref=e241]
              - cell "0%" [ref=e242]:
                - generic [ref=e245]: 0%
            - row "SEKRETARIS RS 2 300.0 0 0%" [ref=e246]:
              - cell "SEKRETARIS RS" [ref=e247]
              - cell "2" [ref=e248]
              - cell "300.0" [ref=e249]
              - cell "0" [ref=e250]
              - cell "0%" [ref=e251]:
                - generic [ref=e254]: 0%
            - row "MUTU 6 1170.0 0 0%" [ref=e255]:
              - cell "MUTU" [ref=e256]
              - cell "6" [ref=e257]
              - cell "1170.0" [ref=e258]
              - cell "0" [ref=e259]
              - cell "0%" [ref=e260]:
                - generic [ref=e263]: 0%
            - row "MARKETING 34 5190.0 1 0.02%" [ref=e264]:
              - cell "MARKETING" [ref=e265]
              - cell "34" [ref=e266]
              - cell "5190.0" [ref=e267]
              - cell "1" [ref=e268]
              - cell "0.02%" [ref=e269]:
                - generic [ref=e273]: 0.02%
            - row "PJ ADMINISTRASI RUANGAN 2 300.0 0 0%" [ref=e274]:
              - cell "PJ ADMINISTRASI RUANGAN" [ref=e275]
              - cell "2" [ref=e276]
              - cell "300.0" [ref=e277]
              - cell "0" [ref=e278]
              - cell "0%" [ref=e279]:
                - generic [ref=e282]: 0%
            - row "DIREKSI 3 540.0 0 0%" [ref=e283]:
              - cell "DIREKSI" [ref=e284]
              - cell "3" [ref=e285]
              - cell "540.0" [ref=e286]
              - cell "0" [ref=e287]
              - cell "0%" [ref=e288]:
                - generic [ref=e291]: 0%
            - row "JKN 13 2010.0 0 0%" [ref=e292]:
              - cell "JKN" [ref=e293]
              - cell "13" [ref=e294]
              - cell "2010.0" [ref=e295]
              - cell "0" [ref=e296]
              - cell "0%" [ref=e297]:
                - generic [ref=e300]: 0%
            - row "HRD 5 810.0 73 9.01%" [ref=e301]:
              - cell "HRD" [ref=e302]
              - cell "5" [ref=e303]
              - cell "810.0" [ref=e304]
              - cell "73" [ref=e305]
              - cell "9.01%" [ref=e306]:
                - generic [ref=e310]: 9.01%
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
> 11 |   await page.getByRole('link', { name: 'Diklat', description: 'Diklat', exact: true }).click();
     |                                                                                        ^ Error: locator.click: Test timeout of 30000ms exceeded.
  12 |   await page.getByRole('button', { name: 'Tambah' }).click();
  13 |   await page.getByRole('textbox', { name: 'Tanggal Mulai Tanggal Selesai' }).fill('2025-05-22');
  14 |   await page.locator('#tanggal').nth(1).fill('2026-05-22');
  15 |   await page.getByRole('textbox', { name: 'Tanggal Mulai Tanggal Selesai' }).fill('2026-05-22');
  16 |   await page.getByRole('textbox', { name: 'Nama Diklat' }).click();
  17 |   await page.getByRole('textbox', { name: 'Nama Diklat' }).fill('Diklat Karyawan');
  18 |   await page.locator('select[name="diklat"]').selectOption('HLC');
  19 |   await page.getByRole('combobox', { name: 'Pengajar' }).click();
  20 |   await page.getByRole('textbox', { name: 'Penyelenggara' }).click();
  21 |   await page.getByRole('textbox', { name: 'Penyelenggara' }).fill('RSMDH');
  22 |   await page.getByRole('spinbutton', { name: 'Jam Diklat' }).click();
  23 |   await page.getByRole('spinbutton', { name: 'Jam Diklat' }).fill('1');
  24 |   await page.locator('#keterangan').nth(1).click();
  25 |   await page.locator('#keterangan').nth(1).fill('bagus');
  26 |   await page.locator('#keterangan').nth(2).click();
  27 |   await page.locator('#keterangan').nth(2).fill('bagus');
  28 |   await page.getByRole('button', { name: 'Upload Sertifikat' }).click();
  29 |   await page.getByRole('button', { name: 'Upload Sertifikat' }).setInputFiles('hasil_turnitin_1767684375748_Revisi.pdf');
  30 |   await page.goto('http://localhost:8000/Diklat/create');
  31 |   await page.getByRole('button', { name: 'Simpan Data' }).click();
  32 | });
```