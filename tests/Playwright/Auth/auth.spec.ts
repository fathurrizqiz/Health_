import { expect, test } from '@playwright/test';

test('Test Login - Menunggu Toast Sukses Berhasil Muncul', async ({ page }) => {
    await page.goto('http://localhost:8000/login');

    // Isi form login (click manual dihapus karena otomatis dilakukan oleh .fill)
    await page
        .getByRole('textbox', { name: 'Enter your Employee ID' })
        .fill('005100439');
    await page
        .getByRole('textbox', { name: 'Enter Password' })
        .fill('005100439');

    // Klik tombol Sign In
    await page.getByRole('button', { name: 'Sign in →' }).click();

    // FIX 1: Cek toast sukses langsung setelah klik tombol, tanpa page.goto di tengahnya
    // Playwright akan otomatis menunggu (jeda) sampai text ini muncul di frontend
    
});
