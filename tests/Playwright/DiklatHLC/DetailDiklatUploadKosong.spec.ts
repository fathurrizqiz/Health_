import { expect, test } from '@playwright/test';

test('test', async ({ page }) => {
    await page.goto('http://localhost:8000/login');
    await page.getByRole('textbox', { name: 'Enter your Employee ID' }).click();
    await page
        .getByRole('textbox', { name: 'Enter your Employee ID' })
        .fill('005100439');
    await page.getByRole('textbox', { name: 'Enter Password' }).click();
    await page
        .getByRole('textbox', { name: 'Enter Password' })
        .fill('005100439');
    await page.getByRole('button', { name: 'Sign in →' }).click();
    await page.getByRole('link', { name: 'Rencana Diklat' }).click();

    // Tunggu navbar HLC muncul
  const hlcLink = page.locator('a[href="/HLC/Home/manajemen"]');

  await expect(hlcLink).toBeVisible({
    timeout: 10000,
  });

    // Masuk halaman HLC
    await hlcLink.click();

    await expect(page).toHaveURL(/HLC\/Home\/manajemen/);

    // Pilih program
    await page
        .getByRole('heading', {
            name: 'Diklat Skin Care',
        })
        .click();

    // Pastikan detail program terbuka
    await expect(
        page.getByRole('heading', {
            name: 'Diklat Skin Care',
        }),
    ).toBeVisible();

    // Tambah diklat/detail
    await page.getByRole('heading', {
  name: 'Diklat Skin Care',
}).click();

await expect(
  page.getByRole('button', { name: /tambah/i })
).toBeVisible();

    // Isi form
    await page.getByRole('textbox').nth(1).fill('1');
    await page.getByRole('textbox').nth(2).fill('2026-06-04');
    await page.getByRole('textbox').nth(3).fill('2026-06-04');

    await page
        .getByRole('textbox', {
            name: 'Ketik nama atau NRP karyawan',
        })
        .fill('eva');

    await page
        .getByText('EVA EFFENDI', {
            exact: true,
        })
        .click();

    await page
        .getByRole('button', {
            name: 'Simpan Diklat',
        })
        .click();
    await expect(
    page.getByText('Gagal menambahkan detail: The dokumen field is required.')
  ).toBeVisible();
});
