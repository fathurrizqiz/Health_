import { test, expect } from '@playwright/test';

test('test', async ({ page }) => {
  await page.goto('http://localhost:8000/');
  await page.getByText('Klik di mana saja untuk').click();
  await page.locator('input[name="nrp"]').click();
  await page.locator('input[name="nrp"]').fill('005100439');
  await page.locator('input[name="password"]').click();
  await page.locator('input[name="password"]').fill('12345678');
  await page.getByRole('button', { name: 'Login' }).click();
});