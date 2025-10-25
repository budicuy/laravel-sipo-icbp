# Instructions for Fixing Harga Obat Format and Calculations

## Overview
This guide will help you fix the harga_obat format to use decimal(20, 3) and verify that the calculations in the laporan are correct.

## Step 1: Run the Migration

First, run the migration to update the harga_obat format to decimal(20, 3):

```bash
php artisan migrate
```

This will update the `harga_per_satuan` and `harga_per_kemasan` columns in the `harga_obat_per_bulan` table from decimal(15, 2) to decimal(20, 3).

## Step 2: Test the Calculations

There are two ways to test the calculations:

### Option 1: Using the Test Script

Run the test script to verify the calculations:

```bash
php test_harga_obat.php
```

This script will:
1. Run the migration automatically
2. Test the harga obat calculations for August 2025
3. Show the detailed calculations
4. Verify that the manual calculations match the code calculations

### Option 2: Using Tinker

If you prefer to use Tinker, follow these steps:

1. Start Tinker:
   ```bash
   php artisan tinker
   ```

2. Copy and paste the content of `database/seeders/test_harga_obat_calculation.php` into the Tinker session.

3. The script will run and show you the calculation details.

## Step 3: Verify the Results

After running the test, check the following:

1. **Database Format**: The harga_obat columns should now be in decimal(20, 3) format, allowing for more precise pricing (e.g., 1.234,567.890).

2. **Calculation Accuracy**: The total biaya calculated by the code should match the manual calculation.

3. **Expected Total**: For August 2025, the expected total biaya should be Rp6.866.200 (as mentioned in your feedback).

## Step 4: Check the Laporan

After verifying the calculations, check the laporan transaksi page:

1. Go to the laporan transaksi page in your application.
2. Select August 2025 as the month and year.
3. Verify that the total biaya shown matches the expected amount (Rp6.866.200).

## Troubleshooting

If the total biaya still doesn't match the expected amount:

1. Check if there are any missing harga_obat records for the period.
2. Verify that all keluhan records have the correct obat information.
3. Make sure the migration was applied successfully to the database.

## Additional Notes

- The decimal(20, 3) format allows for more precise pricing, especially for expensive medications.
- The test script includes both regular and emergency records in the calculation.
- The calculations use the getBulkHargaObatWithFallback method to ensure that even if a price is not available for a specific month, it will fall back to the most recent price.