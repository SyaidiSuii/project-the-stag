<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\VoucherCollection;
use App\Models\VoucherTemplate;

/**
 * PHASE 2.1: Migrate VoucherCollection Data to Unified VoucherTemplate
 *
 * This seeder migrates existing voucher_collections records to voucher_templates
 * with source_type='collection' for unified voucher management.
 *
 * Run this ONCE after deploying the unified voucher structure migration.
 */
class MigrateVoucherCollectionDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting VoucherCollection to VoucherTemplate migration...');

        // Check if there are any voucher_collections to migrate
        $collectionsCount = VoucherCollection::count();

        if ($collectionsCount === 0) {
            $this->command->info('✓ No voucher_collections found. Migration skipped.');
            return;
        }

        $this->command->info("Found {$collectionsCount} voucher collections to migrate.");

        DB::beginTransaction();

        try {
            $migratedCount = 0;
            $skippedCount = 0;

            VoucherCollection::chunk(100, function ($collections) use (&$migratedCount, &$skippedCount) {
                foreach ($collections as $collection) {
                    // Check if already migrated (by name match)
                    $existingTemplate = VoucherTemplate::where('name', $collection->name)
                        ->where('source_type', 'collection')
                        ->first();

                    if ($existingTemplate) {
                        $this->command->warn("⊘ Skipped: '{$collection->name}' already exists in voucher_templates");
                        $skippedCount++;
                        continue;
                    }

                    // Map VoucherCollection fields to VoucherTemplate unified structure
                    VoucherTemplate::create([
                        // Original fields
                        'name' => $collection->name,
                        'title' => $collection->name, // Use name as title if not present
                        'description' => $collection->description,

                        // Unified structure
                        'source_type' => 'collection',

                        // Map voucher_type and voucher_value to discount_type and discount_value
                        'discount_type' => $this->mapDiscountType($collection->voucher_type),
                        'discount_value' => $collection->voucher_value,

                        // Collection-specific fields
                        'spending_requirement' => $collection->spending_requirement,
                        'valid_until' => $collection->valid_until,

                        // Map status to is_active
                        'is_active' => $collection->status === 'active',

                        // Timestamps
                        'created_at' => $collection->created_at,
                        'updated_at' => $collection->updated_at,
                        'deleted_at' => $collection->deleted_at,
                    ]);

                    $migratedCount++;
                }
            });

            DB::commit();

            $this->command->info("✓ Successfully migrated {$migratedCount} voucher collections");
            if ($skippedCount > 0) {
                $this->command->info("⊘ Skipped {$skippedCount} duplicates");
            }

            $this->command->info('✓ VoucherCollection migration completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('✗ Migration failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Map old voucher_type to new discount_type enum
     */
    private function mapDiscountType(?string $voucherType): string
    {
        return match ($voucherType) {
            'percentage', 'percent' => 'percentage',
            'fixed', 'amount' => 'fixed',
            default => 'percentage', // Default fallback
        };
    }
}
