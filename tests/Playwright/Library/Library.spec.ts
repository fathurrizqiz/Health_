import { test } from '@playwright/test';

test('test input library materi folder dan file', async ({ page }) => {
    // 1. Proses Login
    await page.goto('http://localhost:8000/');
    await page.getByText('Klik di mana saja untuk').click();

    await page.locator('input[name="nrp"]').fill('005100439');
    await page.locator('input[name="password"]').fill('005100439');
    await page.getByRole('button', { name: /login/i }).click();

    // Menangani jeda setelah login sebelum masuk ke menu utama
    await page.waitForURL('**/dashboard');

    // 2. Masuk ke Menu Library Materi
    const menuLibrary = page.getByRole('link', { name: /library materi/i });
    await menuLibrary.waitFor({ state: 'visible' });
    await menuLibrary.click();

    // 3. Tambah Materi Pertama (Bentuk Folder)
    // 3. Tambah Materi Pertama (Bentuk Folder)
    await page.getByRole('button', { name: /tambah materi/i }).click();
    
    // Sesuaikan dengan label opsi yang ada di snapshot ("Folder Baru")
    await page.getByRole('combobox').selectOption({ label: 'Folder Baru' });

    // FIX: Ubah 'Nama Materi' menjadi 'Nama Folder' sesuai snapshot halaman
    await page.getByRole('textbox', { name: 'Nama Folder' }).fill('Diklat Karyawan');

    // Klik tombol simpan materi folder
    await page.getByRole('button', { name: /simpan materi/i }).click();

    // 4. Masuk ke dalam Folder yang baru dibuat
    const folderBaru = page.getByText('Diklat Karyawan');
    await folderBaru.waitFor({ state: 'visible' });
    await folderBaru.click();

    // 5. Tambah Materi Kedua di dalam Folder (Bentuk File)
    await page.getByRole('button', { name: /tambah materi/i }).click();
    await page.getByRole('combobox').selectOption('file');
    
    // REVISI: Mengganti '/Materi/store' dengan locator name yang benar
    await page.getByRole('textbox', { name: 'Nama Materi' }).fill('Diklat Kebijakan 2026');

    // Proses Upload File (Pastikan file PDF ini ada di root folder project Anda)
    const fileInput = page.locator('input[type="file"]');
    if ((await fileInput.count()) > 0) {
        await fileInput.setInputFiles('hasil_turnitin_1767684375748_Revisi.pdf');
    } else {
        // Jika input file tersembunyi dan menggunakan library eksternal, tembak tombol pemicunya
        await page.getByRole('button', { name: /choose file|pilih file/i }).setInputFiles('hasil_turnitin_1767684375748_Revisi.pdf');
    }

    // 6. Simpan dan Verifikasi
    await page.getByRole('button', { name: /simpan materi/i }).click();
    
    // Berikan sedikit jeda/nunggu tombol verifikasi muncul setelah proses upload & simpan selesai
    const tombolVerifikasi = page.getByRole('button', { name: /verifikasi/i });
    await tombolVerifikasi.waitFor({ state: 'visible' });
    await tombolVerifikasi.click();
});