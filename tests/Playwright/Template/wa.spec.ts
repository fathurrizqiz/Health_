import { test, expect } from '@playwright/test';

test('test', async ({ page }) => {
  await page.goto('http://localhost:8000/');
  await page.getByText('Klik di mana saja untuk').click();
  await page.locator('input[name="nrp"]').click();
  await page.locator('input[name="nrp"]').fill('005100439');
  await page.locator('input[name="password"]').click();
  await page.locator('input[name="password"]').fill('005100439');
  await page.getByRole('button', { name: 'Login' }).click();
  await page.waitForURL('**/dashboard');
  const menuWhattsapp = page.getByRole('link', {
        name: 'Whattsapp Settings',
    });
    // 2. Tambah Program Baru
    await menuWhattsapp.waitFor({ state: 'visible' });
    await menuWhattsapp.click();
  await page.getByRole('link', { name: 'Template Pesan Atur kata-kata' }).click();
  await page.getByRole('textbox', { name: 'Misal: Notifikasi Jadwal Baru' }).click();
  await page.getByRole('textbox', { name: 'Misal: Notifikasi Jadwal Baru' }).fill('keperawatan-template');
  await page.getByRole('textbox', { name: 'Halo {nama}, jadwal diklat {' }).click();
  await page.getByRole('textbox', { name: 'Halo {nama}, jadwal diklat {' }).fill('HAI {nama} ada diklat dengan judul {judul} pada tanggal {tanggal} di {lokasi}');
  await page.getByRole('button', { name: 'Simpan Template' }).click();
});