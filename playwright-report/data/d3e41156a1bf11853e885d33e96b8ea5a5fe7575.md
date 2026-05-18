# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: Diklat_Karyawan\diklat.spec.ts >> test_Diklat_Karyawan
- Location: tests\Playwright\Diklat_Karyawan\diklat.spec.ts:3:1

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
          - link "Jadwal Diklat 2" [ref=e68] [cursor=pointer]:
            - /url: /JadwalDiklat/Internal
            - img [ref=e70]
            - generic [ref=e73]:
              - generic [ref=e74]: Jadwal Diklat
              - generic [ref=e75]: "2"
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
    - textbox "Cari bagian ..." [ref=e133]
    - generic [ref=e134]:
      - generic [ref=e135]:
        - generic [ref=e136]:
          - generic [ref=e137]:
            - generic [ref=e138]: Total Karyawan
            - generic [ref=e139]: "631"
          - img [ref=e141]
        - generic [ref=e143]:
          - generic [ref=e144]:
            - generic [ref=e145]: Total Jam Target
            - generic [ref=e146]: "135420"
          - img [ref=e148]
        - generic [ref=e150]:
          - generic [ref=e151]:
            - generic [ref=e152]: Terlaksana (2026)
            - generic [ref=e153]: "85"
          - img [ref=e155]
      - generic [ref=e157]:
        - heading "Detail per Bagian (2026)" [level=2] [ref=e159]
        - table [ref=e161]:
          - rowgroup [ref=e162]:
            - row "Nama Bagian Jml. Karyawan Target Jam Aktual Jam Pencapaian (%)" [ref=e163]:
              - columnheader "Nama Bagian" [ref=e164]
              - columnheader "Jml. Karyawan" [ref=e165]
              - columnheader "Target Jam" [ref=e166]
              - columnheader "Aktual Jam" [ref=e167]
              - columnheader "Pencapaian (%)" [ref=e168]
          - rowgroup [ref=e169]:
            - row "KEPERAWATAN 330 77400.0 6 0.01%" [ref=e170]:
              - cell "KEPERAWATAN" [ref=e171]
              - cell "330" [ref=e172]
              - cell "77400.0" [ref=e173]
              - cell "6" [ref=e174]
              - cell "0.01%" [ref=e175]:
                - generic [ref=e178]: 0.01%
            - row "PENUNJANG UMUM 51 7980.0 0 0%" [ref=e179]:
              - cell "PENUNJANG UMUM" [ref=e180]
              - cell "51" [ref=e181]
              - cell "7980.0" [ref=e182]
              - cell "0" [ref=e183]
              - cell "0%" [ref=e184]:
                - generic [ref=e187]: 0%
            - row "PELAYANAN MEDIS 59 13800.0 0 0%" [ref=e188]:
              - cell "PELAYANAN MEDIS" [ref=e189]
              - cell "59" [ref=e190]
              - cell "13800.0" [ref=e191]
              - cell "0" [ref=e192]
              - cell "0%" [ref=e193]:
                - generic [ref=e196]: 0%
            - row "KEUANGAN 32 4920.0 0 0%" [ref=e197]:
              - cell "KEUANGAN" [ref=e198]
              - cell "32" [ref=e199]
              - cell "4920.0" [ref=e200]
              - cell "0" [ref=e201]
              - cell "0%" [ref=e202]:
                - generic [ref=e205]: 0%
            - row "YANMED 1 240.0 0 0%" [ref=e206]:
              - cell "YANMED" [ref=e207]
              - cell "1" [ref=e208]
              - cell "240.0" [ref=e209]
              - cell "0" [ref=e210]
              - cell "0%" [ref=e211]:
                - generic [ref=e214]: 0%
            - row "IT 2 300.0 6 2%" [ref=e215]:
              - cell "IT" [ref=e216]
              - cell "2" [ref=e217]
              - cell "300.0" [ref=e218]
              - cell "6" [ref=e219]
              - cell "2%" [ref=e220]:
                - generic [ref=e224]: 2%
            - row "PENUNJANG MEDIS 90 20310.0 0 0%" [ref=e225]:
              - cell "PENUNJANG MEDIS" [ref=e226]
              - cell "90" [ref=e227]
              - cell "20310.0" [ref=e228]
              - cell "0" [ref=e229]
              - cell "0%" [ref=e230]:
                - generic [ref=e233]: 0%
            - row "SPI 1 150.0 0 0%" [ref=e234]:
              - cell "SPI" [ref=e235]
              - cell "1" [ref=e236]
              - cell "150.0" [ref=e237]
              - cell "0" [ref=e238]
              - cell "0%" [ref=e239]:
                - generic [ref=e242]: 0%
            - row "SEKRETARIS RS 2 300.0 0 0%" [ref=e243]:
              - cell "SEKRETARIS RS" [ref=e244]
              - cell "2" [ref=e245]
              - cell "300.0" [ref=e246]
              - cell "0" [ref=e247]
              - cell "0%" [ref=e248]:
                - generic [ref=e251]: 0%
            - row "MUTU 6 1170.0 0 0%" [ref=e252]:
              - cell "MUTU" [ref=e253]
              - cell "6" [ref=e254]
              - cell "1170.0" [ref=e255]
              - cell "0" [ref=e256]
              - cell "0%" [ref=e257]:
                - generic [ref=e260]: 0%
            - row "MARKETING 34 5190.0 1 0.02%" [ref=e261]:
              - cell "MARKETING" [ref=e262]
              - cell "34" [ref=e263]
              - cell "5190.0" [ref=e264]
              - cell "1" [ref=e265]
              - cell "0.02%" [ref=e266]:
                - generic [ref=e270]: 0.02%
            - row "PJ ADMINISTRASI RUANGAN 2 300.0 0 0%" [ref=e271]:
              - cell "PJ ADMINISTRASI RUANGAN" [ref=e272]
              - cell "2" [ref=e273]
              - cell "300.0" [ref=e274]
              - cell "0" [ref=e275]
              - cell "0%" [ref=e276]:
                - generic [ref=e279]: 0%
            - row "DIREKSI 3 540.0 0 0%" [ref=e280]:
              - cell "DIREKSI" [ref=e281]
              - cell "3" [ref=e282]
              - cell "540.0" [ref=e283]
              - cell "0" [ref=e284]
              - cell "0%" [ref=e285]:
                - generic [ref=e288]: 0%
            - row "JKN 13 2010.0 0 0%" [ref=e289]:
              - cell "JKN" [ref=e290]
              - cell "13" [ref=e291]
              - cell "2010.0" [ref=e292]
              - cell "0" [ref=e293]
              - cell "0%" [ref=e294]:
                - generic [ref=e297]: 0%
            - row "HRD 5 810.0 72 8.89%" [ref=e298]:
              - cell "HRD" [ref=e299]
              - cell "5" [ref=e300]
              - cell "810.0" [ref=e301]
              - cell "72" [ref=e302]
              - cell "8.89%" [ref=e303]:
                - generic [ref=e307]: 8.89%
```

# Test source

```ts
  1  | import { test, expect } from '@playwright/test';
  2  | 
  3  | test('test_Diklat_Karyawan', async ({ page }) => {
  4  |   await page.goto('http://localhost:8000/');
  5  |   await page.getByText('Klik di mana saja untuk melanjutkan').click({ force: true });
  6  |   await page.locator('input[name="nrp"]').click();
  7  |   await page.locator('input[name="nrp"]').fill('005100439');
  8  |   await page.locator('input[name="password"]').click();
  9  |   await page.locator('input[name="password"]').fill('005100439');
  10 |   await page.getByRole('button', { name: 'Login' }).click();
  11 |   await page.getByTestId('toast-content').click();
> 12 |   await page.getByRole('link', { name: 'Diklat', description: 'Diklat', exact: true }).click();
     |                                                                                        ^ Error: locator.click: Test timeout of 30000ms exceeded.
  13 |   await page.getByRole('button', { name: 'Tambah' }).click();
  14 |   await page.getByRole('textbox', { name: 'Tanggal Mulai Tanggal Selesai' }).fill('2026-05-13');
  15 |   await page.getByRole('textbox', { name: 'Nama Diklat' }).fill('Diklat Kebakaran');
  16 |   await page.locator('select[name="diklat"]').selectOption('HLC');
  17 |   await page.getByRole('combobox', { name: 'Pengajar' }).click();
  18 |   await page.getByRole('combobox', { name: 'Pengajar' }).fill('dr Minar Mitutuluhhur');
  19 |   await page.goto('http://localhost:8000/Diklat/create');
  20 |   await page.getByRole('combobox', { name: 'Pengajar' }).press('ArrowLeft');
  21 |   await page.getByRole('combobox', { name: 'Pengajar' }).press('ArrowLeft');
  22 |   await page.getByRole('combobox', { name: 'Pengajar' }).fill('dr Minar Mitutuluhur');
  23 |   await page.getByRole('textbox', { name: 'Penyelenggara' }).click();
  24 |   await page.getByRole('textbox', { name: 'Penyelenggara' }).fill('RSMDH');
  25 |   await page.getByRole('spinbutton', { name: 'Jam Diklat' }).click();
  26 |   await page.getByRole('spinbutton', { name: 'Jam Diklat' }).fill('2');
  27 |   await page.locator('#keterangan').nth(1).click();
  28 |   await page.locator('#keterangan').nth(1).fill('Sangat Bagus');
  29 |   await page.locator('#keterangan').nth(2).click();
  30 |   await page.locator('#keterangan').nth(2).fill('Sangat Bagus');
  31 |   await page.getByRole('button', { name: 'Upload Sertifikat' }).click();
  32 |   await page.getByRole('button', { name: 'Upload Sertifikat' }).setInputFiles('hasil_turnitin_1767684375748_Revisi.pdf');
  33 |   await page.getByRole('button', { name: 'Simpan Data' }).click();
  34 |   await page.getByRole('link', { name: 'Persetujuan' }).click();
  35 |   await page.getByRole('button', { name: 'SETUJUI' }).first().click();
  36 |   await page.getByRole('button', { name: 'Ya' }).click();
  37 |   await page.getByRole('link', { name: 'Diklat', description: 'Diklat', exact: true }).click();
  38 | });
```