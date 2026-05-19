import { test, expect } from '@playwright/test';
import path from 'path'; // Dibutuhkan untuk membaca file upload dengan aman

test('test input diklat dan persetujuan', async ({ page }) => {
  // 1. Proses Login
  await page.goto('http://localhost:8000/');
  await page.getByText('Klik di mana saja untuk melanjutkan').click({ force: true });
  
  await page.locator('input[name="nrp"]').fill('005100439');
  await page.locator('input[name="password"]').fill('005100439');
  await page.getByRole('button', { name: 'Login' }).click();
  
  // Tunggu sampai benar-benar masuk ke dashboard
  await page.waitForURL('**/dashboard');

  // 2. Navigasi ke Menu Diklat
  // Hapus properti 'description' yang bikin pencarian terlalu kaku
const menuDiklat = page.locator('a[href="/Diklat"]', { hasText: 'Diklat' });
await menuDiklat.waitFor({ state: 'visible' });
await menuDiklat.click();

  // 3. Masuk ke Form Tambah Diklat
  await page.getByRole('button', { name: 'Tambah' }).click();

  // 4. Pengisian Form Diklat
  await page.getByRole('textbox', { name: 'Tanggal Mulai Tanggal Selesai' }).fill('2026-05-20');
  await page.locator('#tanggal').nth(1).fill('2026-05-20');
  
  await page.getByRole('textbox', { name: 'Nama Diklat' }).fill('Diklat Karyawan');
  await page.locator('select[name="diklat"]').selectOption('HLC');
  
  await page.getByRole('combobox', { name: 'Pengajar' }).fill('dr Minar Mitutuluhur');
  await page.getByRole('textbox', { name: 'Penyelenggara' }).fill('RSMDH');
  
  await page.getByRole('spinbutton', { name: 'Jam Diklat' }).fill('1');
  
  await page.locator('#keterangan').nth(1).fill('sangat bagus');
  await page.locator('#keterangan').nth(2).fill('luar biasa');

  // 5. Proses Upload Sertifikat
  // Catatan: Pastikan file 'hasil_turnitin_1767684375748_Revisi.pdf' berada di folder project kamu
  // Jika tombol menggunakan <input type="file">, Playwright bisa langsung mengisinya:
  const lokasiFile = 'C:\\Users\\Fathur\\Downloads\\hasil_turnitin_1767684375748_Revisi.pdf';
  const fileInput = page.locator('input[type="file"]');
  if (await fileInput.count() > 0) {
    await fileInput.setInputFiles('hasil_turnitin_1767684375748_Revisi.pdf');
  } else {
    // Jika input filenya tersembunyi, trigger lewat button-nya
    await page.getByRole('button', { name: 'Upload Sertifikat' }).setInputFiles('hasil_turnitin_1767684375748_Revisi.pdf');
  }

  // 6. Simpan Data
  await page.getByRole('button', { name: 'Simpan Data' }).click();

  // 7. Proses Persetujuan (Approval)
  await page.getByRole('link', { name: 'Persetujuan' }).click();
  await page.getByRole('button', { name: 'SETUJUI' }).first().click();
  await page.getByRole('button', { name: 'Ya' }).click();
});