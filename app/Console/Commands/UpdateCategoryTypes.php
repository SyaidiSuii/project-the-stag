<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;

class UpdateCategoryTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:category-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update subcategory types based on their parent categories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Removing "Test Category 2" and reassigning its subcategories...');

        // Get "Test Category 2" (assuming it has ID 1 from our previous inspection)
        $testCategory = Category::where('name', 'Test Category 2')->first();
        
        if (!$testCategory) {
            $this->info('Test Category 2 not found, nothing to do.');
            return;
        }

        $this->info("Found 'Test Category 2' with ID: {$testCategory->id}");
        
        // Get all subcategories of "Test Category 2"
        $subcategories = Category::where('parent_id', $testCategory->id)->get();
        
        if ($subcategories->count() == 0) {
            $this->info('No subcategories found under "Test Category 2". Deleting the category...');
            $testCategory->delete();
            $this->info('Test Category 2 deleted.');
            return;
        }

        // Find the proper parent categories
        $foodParent = Category::whereNull('parent_id')->where('name', 'Food')->first();
        if (!$foodParent) {
            $foodParent = Category::create([
                'name' => 'Food',
                'type' => 'food',
                'parent_id' => null,
                'sort_order' => 1
            ]);
            $this->info("Created Food parent category with ID: {$foodParent->id}");
        }

        // Move all subcategories from "Test Category 2" to the proper "Food" category
        foreach ($subcategories as $subcategory) {
            $this->info("Moving subcategory '{$subcategory->name}' (ID: {$subcategory->id}) from Test Category 2 to Food");
            $subcategory->update(['parent_id' => $foodParent->id, 'type' => 'food']);
        }

        // Now delete "Test Category 2"
        $testCategory->delete();
        
        $this->info("Moved {$subcategories->count()} subcategories to the proper Food category.");
        $this->info('Test Category 2 has been removed.');
        
        // Show the final state
        $this->info('');
        $this->info('Final state:');
        $foodSubcategories = Category::where('parent_id', $foodParent->id)->get();
        $this->info("Subcategories under Food (ID: {$foodParent->id}):");
        foreach ($foodSubcategories as $subcategory) {
            $this->info("  - ID: {$subcategory->id}, Name: {$subcategory->name}, Type: {$subcategory->type}");
        }
        
        $this->info('Done!');
    }
}
