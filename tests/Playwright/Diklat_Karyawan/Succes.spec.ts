import { test, expect } from '@playwright/test';

test('Test Alur Sukses - Input Diklat sampai Persetujuan Admin', async ({ page }) => {
  // 1. Proses Login
  await page.goto('http://localhost:8000/login');
  await page.getByRole('textbox', { name: 'Enter your Employee ID' }).fill('005100439');
  await page.getByRole('textbox', { name: 'Enter Password' }).fill('005100439');
  await page.getByRole('button', { name: 'Sign in →' }).click();

  // 2. Navigasi ke Halaman Utama Diklat & Klik Tambah
  // FIX: Menggunakan locator link yang fleksibel (menghilangkan exact & description yang bikin timeout)
  await page.locator('a[href="/Diklat"]').click();
  await page.getByRole('button', { name: 'Tambah' }).click();

  // 3. Pengisian Form Diklat (Bersih tanpa page.goto penyeleweng yang bikin form reset)
  await page.getByRole('textbox', { name: 'Tanggal Mulai Tanggal Selesai' }).fill('2026-06-04');
  await page.locator('#tanggal').nth(1).fill('2026-06-04');
  
  await page.getByRole('textbox', { name: 'Nama Diklat' }).fill('Diklat Excelent');
  await page.locator('select[name="diklat"]').selectOption('HLC');
  await page.getByRole('combobox', { name: 'Pengajar' }).fill('Dr Minar mitutuluhur');
  await page.getByRole('textbox', { name: 'Penyelenggara' }).fill('RSMDH');
  
  // Mengisi jam diklat bernilai positif langsung
  await page.getByRole('spinbutton', { name: 'Jam Diklat' }).fill('1');
  
  await page.locator('#keterangan').nth(1).fill('Sangat bagus');
  await page.locator('#keterangan').nth(2).fill('luar biasa');
  
  // Upload file sertifikat
  await page.getByRole('button', { name: 'Upload Sertifikat' }).setInputFiles('hasil_turnitin_1767684375748_Revisi.pdf');

  // 4. Simpan Data & Validasi Toast
  await page.getByRole('button', { name: 'Simpan Data' }).click();

  // EXPECT TOAST: Memastikan toast sukses muncul setelah klik simpan
  // (Silakan ganti teks di bawah ini agar pas dengan pesan sukses aplikasi Anda, misal: "Data berhasil disimpan")
  const toastSukses = page.locator('text=Data Berhasil Disimpan!'); 
  await expect(toastSukses).toBeVisible();

  // 5. Alur Persetujuan (Approval)
  await page.getByRole('link', { name: 'Persetujuan' }).click();
  await page.getByRole('button', { name: 'Diklat Mandiri Pengajuan' }).click();
  await page.getByRole('button', { name: 'SETUJUI' }).click();
  await page.getByRole('button', { name: 'Ya' }).click();

  
});