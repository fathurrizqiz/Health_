import { test, expect } from '@playwright/test';

test('test rencana diklat eksternal', async ({ page }) => {
  // 1. Proses Login
  await page.goto('http://localhost:8000/');
  await page.getByText('Klik di mana saja untuk').click();
  await page.locator('input[name="nrp"]').fill('005100439');
  await page.locator('input[name="password"]').fill('005100439');
  await page.getByRole('button', { name: 'Login' }).click();
  await page.waitForURL('**/dashboard'); 

  // 2. Navigasi Ke Menu Eksternal
  const Rencana = page.getByRole('link', { name: 'Rencana Diklat' });
  await Rencana.waitFor({ state: 'visible' });
  await Rencana.click();
  
  const Eksternal = page.getByRole('link', { name: 'Eksternal' });
  await Eksternal.waitFor({ state: 'visible' });
  await Eksternal.click();

  // 3. Tambah Peserta & Isi Form
  await page.getByRole('button', { name: 'Tambah Peserta' }).nth(1).click();
  await page.getByRole('textbox', { name: 'Ketik NRP/Nama...' }).fill('eva');
  await page.getByText('- EVA EFFENDI').click();

  await page.getByRole('textbox').nth(2).fill('2026-05-19');
  await page.getByRole('textbox').nth(3).fill('2026-05-19');
  await page.getByRole('spinbutton').fill('01');
  await page.getByRole('textbox').nth(4).fill('RSMDH');
  
  // 4. Proses Simpan Aman (Menunggu network tenang agar data benar-benar masuk DB)
  await page.getByRole('button', { name: 'Simpan' }).click();
  await page.waitForLoadState('networkidle'); // Hapus page.goto manual yang merusak request data

  // 5. Proses Persetujuan di Inbox
  // Catatan: Pastikan namanya 'Indbox' atau 'Inbox' di aplikasi kamu
  const menuInbox = page.getByRole('link', { name: /In(d)?box/i }); 
  await menuInbox.waitFor({ state: 'visible' });
  await menuInbox.click();
  
  await page.getByRole('button', { name: 'Setujui' }).first().click();
  await page.waitForLoadState('networkidle');

  // 6. Konfirmasi Kehadiran di Jadwal Diklat
  await page.getByRole('link', { name: 'Jadwal Diklat' }).click();
  await page.getByRole('button', { name: 'eksternal' }).click();

  // PERBAIKAN: Pasang listener dialog SEBELUM mengeklik tombol 'Hadir'
  page.once('dialog', async dialog => {
    console.log(`Dialog message: ${dialog.message()}`);
    await dialog.accept(); // Gunakan .accept() untuk klik "OK/Ya" pada konfirmasi kehadiran
  });

  await page.getByRole('button', { name: 'Hadir' }).click();
});