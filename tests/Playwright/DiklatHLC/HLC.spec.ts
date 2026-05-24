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
  const Rencana = page.getByRole('link', { name: 'Rencana Diklat' });
  await Rencana.waitFor({ state: 'visible' });
  await Rencana.click();
  const HLC = page.getByRole('link', { name: 'HLC' });
  await HLC.waitFor({ state: 'visible' });
  await HLC.click();
  await page.goto('http://localhost:8000/HLC/Home/manajemen');
  await page.getByRole('button').filter({ hasText: /^$/ }).nth(5).click();
  await page.getByRole('textbox', { name: 'Contoh: Teknik Presentasi' }).click();
  await page.getByRole('textbox', { name: 'Contoh: Teknik Presentasi' }).fill('Diklat Karyawan');
  await page.getByRole('textbox').nth(2).click();
  await page.getByRole('textbox').nth(2).fill('dr Minar');
  await page.getByRole('spinbutton').click();
  await page.getByRole('spinbutton').fill('1');
  await page.getByRole('textbox').nth(3).click();
  await page.getByRole('textbox').nth(3).fill('RSMDH');
//   sesuaikan jika ingin di test
  await page.getByRole('textbox').nth(4).fill('2026-05-24');
  await page.getByRole('textbox').nth(5).fill('2026-05-24');
//   
  await page.getByRole('textbox', { name: 'Ketik nama atau NRP karyawan' }).click();
  await page.getByRole('textbox', { name: 'Ketik nama atau NRP karyawan' }).fill('eva');
  await page.getByText('(005100439)').click();
  await page.getByRole('button', { name: 'Simpan Diklat' }).click();
  await page.getByRole('link', { name: 'Indbox' }).click();
  await page.getByRole('button', { name: 'Setujui' }).first().click();
  await page.getByRole('link', { name: 'Jadwal Diklat' }).click();
  await page.getByRole('button', { name: 'hlc' }).click();
  page.once('dialog', dialog => {
    console.log(`Dialog message: ${dialog.message()}`);
    dialog.dismiss().catch(() => {});
  });
  await page.getByRole('button', { name: 'Hadir' }).click();
});