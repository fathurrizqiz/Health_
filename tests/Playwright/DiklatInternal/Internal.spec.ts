import { expect, test } from '@playwright/test';

test('test alur pembuatan rencana diklat internal sampai mulai periode', async ({
    page,
}) => {
    // 1. Proses Login
    await page.goto('http://localhost:8000/');
    await page.getByText('Klik di mana saja untuk').click();

    await page.locator('input[name="nrp"]').fill('005100439');
    await page.locator('input[name="password"]').fill('005100439');
    await page.getByRole('button', { name: 'Login' }).click();
    await page.waitForURL('**/dashboard');

    // Deklarasikan variabel navigasi di awal ONCE (Satu kali saja)
    const menuRencanaDiklat = page.getByRole('link', {
        name: 'Rencana Diklat',
    });

    // 2. Tambah Program Baru
    await menuRencanaDiklat.waitFor({ state: 'visible' });
    await menuRencanaDiklat.click();
    await page.getByRole('button', { name: 'Tambah Program' }).click();
    await page
        .getByRole('textbox', { name: 'contoh: Diklat Kepemimpinan' })
        .fill('Peraturan');
    await page.getByRole('button', { name: 'Simpan' }).click();

    // Tunggu data masuk database sebelum lanjut navigasi
    await page.waitForLoadState('networkidle');

    // 3. Tambah Detail Program
    await page.goto('http://localhost:8000/RencanaDiklat/RPT/PF');
    await page.getByRole('button', { name: 'Tambah Detail' }).last().click(); // Menggunakan .last() agar memilih program teranyar yang baru dibuat
    await page
        .getByRole('textbox', { name: 'Nama Diklat' })
        .fill('Diklat Kebijakan 2027');
    await page
        .getByRole('textbox', { name: 'Keterangan' })
        .fill('Semua Karyawan');
    await page.getByRole('textbox', { name: 'Pengajar' }).fill('Direktur');
    await page.getByRole('button', { name: 'Simpan Detail' }).click();
    await page.waitForLoadState('networkidle');

    // 4. Tambah Periode Pelaksanaan
    await page.goto('http://localhost:8000/RencanaDiklat/RPT/PF');
    // PERBAIKAN 1: Pastikan tabel selesai memuat data baru
    await page.waitForLoadState('networkidle');

    // PERBAIKAN 2: Gunakan regex /semua karyawan/i agar anti-gagal akibat kapitalisasi huruf
    const cellKaryawan = page.getByRole('cell', { name: /semua karyawan/i });
    await cellKaryawan.waitFor({ state: 'visible' });
    await cellKaryawan.click();
    await page.locator('input[type="date"]').fill('2026-05-21');
    await page
        .getByRole('textbox', { name: 'Cari nama karyawan...' })
        .fill('min');
    await page.getByText('Periode PelaksanaanDETAIL ID').click();
    await page.getByRole('textbox').nth(2).fill('RSMDH');
    await page.getByRole('button', { name: 'Tambah Periode' }).click();
    await page.waitForLoadState('networkidle');

    // 5. Setup Bagian / Peserta Diklat
    await page.goto('http://localhost:8000/RencanaDiklat/RPT/PF');
    await page.waitForLoadState('networkidle');

    const barisProgram = page
        .locator('tr')
        .filter({ hasText: 'Diklat Kebijakan 2027' });
    await barisProgram.waitFor({ state: 'visible' });
    await barisProgram.locator('a, button, .cursor-pointer').first().click();

    // 1. Pilih opsi berdasarkan indeks ke-1 pada dropdown Periode
    const dropdownPeriode = page.getByRole('combobox');
    await dropdownPeriode.waitFor({ state: 'visible' });
    await dropdownPeriode.selectOption({ index: 1 });

    // 2. Klik tombol "Detail Periode" (Jika tombol ini ada di UI)
    const tombolDetail = page.getByRole('button', { name: 'Detail Periode' });
    if (await tombolDetail.isVisible()) {
        await tombolDetail.click();
    }

    // 3. JEDA KRITIKAL: Tunggu sistem selesai memuat data periode ke dalam memori
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000); // Beri jeda 1 detik agar state 'disabled' pada tombol hilang

    // 4. Pastikan tombol "Semua Bagian" muncul, lalu klik secara paksa
    const tombolSemuaBagian = page.getByRole('button', {
        name: 'Semua Bagian',
    });
    await tombolSemuaBagian.waitFor({ state: 'visible' });
    await tombolSemuaBagian.click({ force: true });

    // ====================================================================
    // PERBAIKAN UTAMA: Tunggu hingga baris data karyawan pertama muncul
    // di dalam tabel. Ini indikator mutlak bahwa render data selesai.
    // ====================================================================
    await page.locator('table tbody tr, .w-full tbody tr').first().waitFor({
        state: 'visible',
        timeout: 10000,
    });

    // 5. Pastikan tombol Simpan sudah berubah aktif (enabled), lalu klik
    const tombolSimpan = page.getByRole('button', { name: 'Simpan' });
    await expect(tombolSimpan).toBeEnabled({ timeout: 5000 });
    await tombolSimpan.click();

    // Tunggu data terkirim ke server
    await page.waitForLoadState('networkidle');

    // 6. Buat Template Materi
    await page.goto('http://localhost:8000/RencanaDiklat/RPT/PF');
    await page
        .locator('tr')
        .filter({ hasText: 'Diklat Kebijakan 2027' })
        .locator('a, button, .cursor-pointer')
        .first()
        .click();
    await page.getByRole('combobox').selectOption({ index: 1 });
    await page.getByRole('button', { name: 'Buat Template' }).click();

    await page
        .getByRole('textbox', { name: 'Nama materi / pembahasan' })
        .first()
        .fill('Kebijakan baru');
    await page.getByRole('button', { name: '+ Tambah Materi' }).click();
    await page
        .getByRole('textbox', { name: 'Nama materi / pembahasan' })
        .nth(1)
        .fill('Tindakan Baru');
    await page.getByRole('button', { name: '+ Tambah Materi' }).click();
    await page
        .getByRole('textbox', { name: 'Nama materi / pembahasan' })
        .nth(2)
        .fill('Peraturan Baru');
    await page.getByRole('button', { name: 'Simpan' }).click();
    await page.waitForLoadState('networkidle');

    // 7. Input Soal Pre-test
    await page.goto('http://localhost:8000/RencanaDiklat/RPT/PF');
    await page
        .locator('tr')
        .filter({ hasText: 'Diklat Kebijakan 2027' })
        .locator('a, button, .cursor-pointer')
        .first()
        .click();
    await page.getByRole('combobox').selectOption({ index: 1 });
    await page.getByRole('button', { name: 'Pre-test' }).click();

    await page.getByRole('button', { name: '+ Tambah Soal' }).click();
    await page.getByRole('button', { name: '+ Tambah Soal' }).click();

    await page
        .getByRole('textbox', { name: 'Tulis soal...' })
        .first()
        .fill('Apa yang disampaikan pada peraturan di tahun yang akan datang');
    await page
        .getByRole('textbox', { name: 'Teks jawaban...' })
        .first()
        .fill('kebijakan');
    await page
        .getByRole('button', { name: '+ Tambah Pilihan' })
        .first()
        .click();
    await page
        .getByRole('textbox', { name: 'Teks jawaban...' })
        .nth(1)
        .fill('formalitas');

    await page
        .getByRole('textbox', { name: 'Tulis soal...' })
        .nth(1)
        .fill('Peraturan baru akan di terapkan di tahun');
    await page
        .getByRole('textbox', { name: 'Teks jawaban...' })
        .nth(2)
        .fill('2027');
    await page.getByRole('button', { name: '+ Tambah Pilihan' }).nth(1).click();
    await page
        .getByRole('textbox', { name: 'Teks jawaban...' })
        .nth(3)
        .fill('2026');

    await page.getByRole('button', { name: 'Simpan Pre-Test' }).click();
    await page.waitForLoadState('networkidle');

    // 8. Input Soal Post-test
    await page.goto('http://localhost:8000/RencanaDiklat/RPT/PF');
    await page
        .locator('tr')
        .filter({ hasText: 'Diklat Kebijakan 2027' })
        .locator('a, button, .cursor-pointer')
        .first()
        .click();
    await page.getByRole('combobox').selectOption({ index: 1 });
    await page.getByRole('button', { name: 'Post-test' }).click();

    await page.getByRole('button', { name: '+ Tambah Soal' }).click();
    await page
        .getByRole('textbox', { name: 'Tulis soal...' })
        .fill('Peraturan untuk tahun berapa berlaku');
    await page
        .getByRole('textbox', { name: 'Teks jawaban...' })
        .first()
        .fill('2027');
    await page.getByRole('button', { name: '+ Tambah Pilihan' }).click();
    await page
        .getByRole('textbox', { name: 'Teks jawaban...' })
        .nth(1)
        .fill('2026');

    await page.getByRole('button', { name: 'Simpan Post-Test' }).click();
    await page.waitForLoadState('networkidle');

    // 9. Mulai Periode Diklat
    await page.goto('http://localhost:8000/RencanaDiklat/RPT/PF');
    await page
        .locator('tr')
        .filter({ hasText: 'Diklat Kebijakan 2027' })
        .locator('a, button, .cursor-pointer')
        .first()
        .click();
    await page.getByRole('combobox').selectOption({ index: 1 });

    await page.getByPlaceholder('0').fill('1');
    await page.getByRole('button', { name: 'Mulai Periode' }).click();
    await page.waitForLoadState('networkidle');
});
