import { test, expect } from '@playwright/test';

test('User dapat login dengan NRP valid', async ({ page }) => {
  await page.goto('http://localhost:8000/');
  await page.getByText('Klik di mana saja untuk melanjutkan').click({ force: true });
  await page.locator('input[name="nrp"]').click();
  await page.locator('input[name="nrp"]').fill('005100439');
  await page.locator('input[name="password"]').click();
  await page.locator('input[name="password"]').fill('005100439');
  await page.getByRole('button', { name: 'Login' }).click();
  await page.getByTestId('toast-content').click();
});