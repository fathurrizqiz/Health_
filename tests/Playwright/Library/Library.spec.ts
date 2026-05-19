import { test, expect } from '@playwright/test';

test('test input library materi folder dan file', async ({ page }) => {
  // 1. Proses Login
  await page.goto('http://localhost:8000/');
  await page.getByText('Klik di mana saja untuk').click();
  
  await page.locator('input[name="nrp"]').fill('005100439');
  await page.locator('input[name="password"]').fill('005100439');
  await page.getByRole('button', { name: 'Login' }).click();
  
  // Menangani jeda setelah login sebelum masuk ke menu utama
  await page.waitForURL('**/dashboard'); 

  // 2. Masuk ke Menu Library Materi
  const menuLibrary = page.getByRole('link', { name: 'Library Materi' });
  await menuLibrary.waitFor({ state: 'visible' });
  await menuLibrary.click();

  // 3. Tambah Materi Pertama (Bentuk Folder)
  await page.getByRole('button', { name: 'Tambah Materi' }).click();
  await page.getByRole('combobox').selectOption('folder');
  
  // Mengisi nama materi folder (menggunakan regex agar pencarian textbox lebih fleksibel)
  await page.getByRole('textbox', { name: /marketing/i }).fill('Diklat Karyawan');
  await page.getByRole('button', { name: 'Simpan Materi' }).click();

  // 4. Masuk ke dalam Folder yang baru dibuat
  const folderBaru = page.getByText('Diklat Karyawan');
  await folderBaru.waitFor({ state: 'visible' });
  await folderBaru.click();

  // 5. Tambah Materi Kedua di dalam Folder (Bentuk File)
  await page.getByRole('button', { name: 'Tambah Materi' }).click();
  await page.getByRole('combobox').selectOption('file');
  await page.getByRole('textbox', { name: /panduan sop/i }).fill('Diklat Kebijakan 2026');
  
  // Proses Upload File (Pastikan file PDF ini ada di root folder project kamu)
  const fileInput = page.locator('input[type="file"]');
  if (await fileInput.count() > 0) {
    await fileInput.setInputFiles('hasil_turnitin_1767684375748_Revisi.pdf');
  } else {
    await page.getByRole('button', { name: 'Choose File' }).setInputFiles('hasil_turnitin_1767684375748_Revisi.pdf');
  }

  // 6. Simpan dan Verifikasi
  await page.getByRole('button', { name: 'Simpan Materi' }).click();
  await page.getByRole('button', { name: 'Verifikasi' }).click();
});