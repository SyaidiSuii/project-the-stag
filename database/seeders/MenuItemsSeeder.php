<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;
use App\Models\Category;

class MenuItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $foodCategory = Category::where('type', 'food')->first();
        $drinkCategory = Category::where('type', 'drink')->first();
        $setMealCategory = Category::where('type', 'set-meal')->first();

        if (!$foodCategory || !$drinkCategory || !$setMealCategory) {
            $this->command->error('Categories not found! Please run CategorySeeder first.');
            return;
        }

        // ====== FOOD ITEMS ======
        $foodItems = [
            [
                'name' => 'Grilled Ribeye Steak',
                'description' => 'Premium 300g ribeye steak grilled to perfection, served with mashed potatoes, seasonal vegetables and black pepper sauce',
                'price' => 68.00,
                'category_id' => $foodCategory->id,
                'allergens' => ['dairy', 'soy'],
                'preparation_time' => 25,
                'availability' => true,
                'is_featured' => true,
                'rating_average' => 4.8,
                'rating_count' => 156,
            ],
            [
                'name' => 'Aglio Olio Pasta',
                'description' => 'Classic Italian pasta with garlic, olive oil, chili flakes, parsley and parmesan cheese',
                'price' => 24.00,
                'category_id' => $foodCategory->id,
                'allergens' => ['gluten', 'dairy'],
                'preparation_time' => 15,
                'availability' => true,
                'is_featured' => false,
                'rating_average' => 4.5,
                'rating_count' => 89,
            ],
            [
                'name' => 'Carbonara Pasta',
                'description' => 'Creamy pasta with bacon, egg yolk, parmesan cheese and black pepper',
                'price' => 26.00,
                'category_id' => $foodCategory->id,
                'allergens' => ['gluten', 'dairy', 'eggs'],
                'preparation_time' => 18,
                'availability' => true,
                'is_featured' => true,
                'rating_average' => 4.7,
                'rating_count' => 134,
            ],
            [
                'name' => 'Classic Beef Burger',
                'description' => 'Juicy beef patty with cheese, lettuce, tomato, onions and special sauce in a toasted bun. Served with crispy fries',
                'price' => 22.00,
                'category_id' => $foodCategory->id,
                'allergens' => ['gluten', 'dairy', 'eggs'],
                'preparation_time' => 20,
                'availability' => true,
                'is_featured' => false,
                'rating_average' => 4.6,
                'rating_count' => 201,
            ],
            [
                'name' => 'Nasi Goreng Kampung',
                'description' => 'Traditional Malaysian fried rice with anchovies, egg, vegetables and sambal belacan',
                'price' => 16.00,
                'category_id' => $foodCategory->id,
                'allergens' => ['eggs', 'shellfish'],
                'preparation_time' => 15,
                'availability' => true,
                'is_featured' => false,
                'rating_average' => 4.4,
                'rating_count' => 178,
            ],
            [
                'name' => 'Mee Goreng Mamak',
                'description' => 'Spicy fried yellow noodles with vegetables, egg, tofu and special mamak gravy',
                'price' => 14.00,
                'category_id' => $foodCategory->id,
                'allergens' => ['gluten', 'eggs', 'soy'],
                'preparation_time' => 12,
                'availability' => true,
                'is_featured' => false,
                'rating_average' => 4.3,
                'rating_count' => 92,
            ],
            [
                'name' => 'Grilled Chicken Chop',
                'description' => 'Tender grilled chicken breast with mushroom sauce, served with french fries and coleslaw',
                'price' => 28.00,
                'category_id' => $foodCategory->id,
                'allergens' => ['dairy'],
                'preparation_time' => 22,
                'availability' => true,
                'is_featured' => false,
                'rating_average' => 4.5,
                'rating_count' => 167,
            ],
            [
                'name' => 'Fish & Chips',
                'description' => 'Crispy battered fish fillet served with thick-cut chips, tartar sauce and lemon wedge',
                'price' => 26.00,
                'category_id' => $foodCategory->id,
                'allergens' => ['gluten', 'fish', 'eggs'],
                'preparation_time' => 18,
                'availability' => true,
                'is_featured' => true,
                'rating_average' => 4.6,
                'rating_count' => 143,
            ],
            [
                'name' => 'Chicken Wings (6pcs)',
                'description' => 'Crispy fried chicken wings tossed in your choice of BBQ, honey mustard or buffalo sauce',
                'price' => 18.00,
                'category_id' => $foodCategory->id,
                'allergens' => ['gluten', 'soy'],
                'preparation_time' => 15,
                'availability' => true,
                'is_featured' => false,
                'rating_average' => 4.4,
                'rating_count' => 234,
            ],
            [
                'name' => 'Caesar Salad',
                'description' => 'Fresh romaine lettuce with caesar dressing, croutons, parmesan cheese and grilled chicken',
                'price' => 20.00,
                'category_id' => $foodCategory->id,
                'allergens' => ['gluten', 'dairy', 'eggs'],
                'preparation_time' => 10,
                'availability' => true,
                'is_featured' => false,
                'rating_average' => 4.2,
                'rating_count' => 76,
            ],
            [
                'name' => 'Mushroom Soup',
                'description' => 'Creamy mushroom soup served with garlic bread',
                'price' => 12.00,
                'category_id' => $foodCategory->id,
                'allergens' => ['dairy', 'gluten'],
                'preparation_time' => 8,
                'availability' => true,
                'is_featured' => false,
                'rating_average' => 4.3,
                'rating_count' => 98,
            ],
            [
                'name' => 'French Fries (Large)',
                'description' => 'Crispy golden french fries served with ketchup and mayo',
                'price' => 10.00,
                'category_id' => $foodCategory->id,
                'allergens' => [],
                'preparation_time' => 8,
                'availability' => true,
                'is_featured' => false,
                'rating_average' => 4.1,
                'rating_count' => 312,
            ],
            [
                'name' => 'Tom Yum Fried Rice',
                'description' => 'Thai-style spicy and sour fried rice with seafood, mushrooms and aromatic herbs',
                'price' => 18.00,
                'category_id' => $foodCategory->id,
                'allergens' => ['shellfish', 'fish'],
                'preparation_time' => 15,
                'availability' => true,
                'is_featured' => false,
                'rating_average' => 4.5,
                'rating_count' => 121,
            ],
            [
                'name' => 'Lamb Chop',
                'description' => 'Grilled lamb chops with rosemary, served with roasted vegetables and mint sauce',
                'price' => 58.00,
                'category_id' => $foodCategory->id,
                'allergens' => ['dairy'],
                'preparation_time' => 28,
                'availability' => true,
                'is_featured' => true,
                'rating_average' => 4.7,
                'rating_count' => 87,
            ],
            [
                'name' => 'Margarita Pizza',
                'description' => 'Classic Italian pizza with tomato sauce, mozzarella cheese and fresh basil',
                'price' => 32.00,
                'category_id' => $foodCategory->id,
                'allergens' => ['gluten', 'dairy'],
                'preparation_time' => 20,
                'availability' => true,
                'is_featured' => false,
                'rating_average' => 4.4,
                'rating_count' => 156,
            ],
        ];

        // ====== DRINK ITEMS ======
        $drinkItems = [
            [
                'name' => 'Cappuccino',
                'description' => 'Rich espresso with steamed milk and velvety foam',
                'price' => 12.00,
                'category_id' => $drinkCategory->id,
                'allergens' => ['dairy'],
                'preparation_time' => 5,
                'availability' => true,
                'is_featured' => false,
                'rating_average' => 4.5,
                'rating_count' => 267,
            ],
            [
                'name' => 'Iced Latte',
                'description' => 'Smooth espresso with cold milk over ice',
                'price' => 13.00,
                'category_id' => $drinkCategory->id,
                'allergens' => ['dairy'],
                'preparation_time' => 5,
                'availability' => true,
                'is_featured' => true,
                'rating_average' => 4.6,
                'rating_count' => 198,
            ],
            [
                'name' => 'Fresh Orange Juice',
                'description' => 'Freshly squeezed orange juice, no added sugar',
                'price' => 10.00,
                'category_id' => $drinkCategory->id,
                'allergens' => [],
                'preparation_time' => 5,
                'availability' => true,
                'is_featured' => false,
                'rating_average' => 4.3,
                'rating_count' => 143,
            ],
            [
                'name' => 'Mango Smoothie',
                'description' => 'Tropical mango blended with yogurt and ice',
                'price' => 14.00,
                'category_id' => $drinkCategory->id,
                'allergens' => ['dairy'],
                'preparation_time' => 5,
                'availability' => true,
                'is_featured' => true,
                'rating_average' => 4.7,
                'rating_count' => 223,
            ],
            [
                'name' => 'Green Tea Latte',
                'description' => 'Premium matcha green tea with steamed milk',
                'price' => 13.00,
                'category_id' => $drinkCategory->id,
                'allergens' => ['dairy'],
                'preparation_time' => 5,
                'availability' => true,
                'is_featured' => false,
                'rating_average' => 4.4,
                'rating_count' => 112,
            ],
            [
                'name' => 'Coca Cola',
                'description' => 'Classic Coca Cola soft drink (330ml)',
                'price' => 6.00,
                'category_id' => $drinkCategory->id,
                'allergens' => [],
                'preparation_time' => 2,
                'availability' => true,
                'is_featured' => false,
                'rating_average' => 4.0,
                'rating_count' => 421,
            ],
            [
                'name' => 'Iced Lemon Tea',
                'description' => 'Refreshing black tea with fresh lemon and ice',
                'price' => 8.00,
                'category_id' => $drinkCategory->id,
                'allergens' => [],
                'preparation_time' => 5,
                'availability' => true,
                'is_featured' => false,
                'rating_average' => 4.2,
                'rating_count' => 187,
            ],
            [
                'name' => 'Carlsberg Beer',
                'description' => 'Premium Danish lager beer (330ml)',
                'price' => 15.00,
                'category_id' => $drinkCategory->id,
                'allergens' => ['gluten'],
                'preparation_time' => 2,
                'availability' => true,
                'is_featured' => false,
                'rating_average' => 4.3,
                'rating_count' => 234,
            ],
            [
                'name' => 'House Red Wine',
                'description' => 'Full-bodied red wine (glass)',
                'price' => 25.00,
                'category_id' => $drinkCategory->id,
                'allergens' => ['sulfites'],
                'preparation_time' => 2,
                'availability' => true,
                'is_featured' => false,
                'rating_average' => 4.4,
                'rating_count' => 89,
            ],
            [
                'name' => 'Strawberry Milkshake',
                'description' => 'Creamy milkshake with fresh strawberries and vanilla ice cream',
                'price' => 14.00,
                'category_id' => $drinkCategory->id,
                'allergens' => ['dairy'],
                'preparation_time' => 5,
                'availability' => true,
                'is_featured' => true,
                'rating_average' => 4.6,
                'rating_count' => 176,
            ],
            [
                'name' => 'Teh Tarik',
                'description' => 'Traditional Malaysian pulled tea with condensed milk',
                'price' => 5.00,
                'category_id' => $drinkCategory->id,
                'allergens' => ['dairy'],
                'preparation_time' => 5,
                'availability' => true,
                'is_featured' => false,
                'rating_average' => 4.5,
                'rating_count' => 312,
            ],
        ];

        // Insert food and drink items
        $insertedFoodItems = [];
        foreach ($foodItems as $item) {
            $insertedFoodItems[] = MenuItem::create($item);
        }

        $insertedDrinkItems = [];
        foreach ($drinkItems as $item) {
            $insertedDrinkItems[] = MenuItem::create($item);
        }

        $this->command->info('Created ' . count($insertedFoodItems) . ' food items');
        $this->command->info('Created ' . count($insertedDrinkItems) . ' drink items');

        // ====== SET MEAL ITEMS ======
        // Now we can create set meals that include the items we just created

        // Set Meal 1: Steak Combo
        $steakCombo = MenuItem::create([
            'name' => 'Steak Lover Combo',
            'description' => 'Perfect combo for meat lovers! Includes our signature Grilled Ribeye Steak and a refreshing drink of your choice',
            'price' => 78.00,
            'category_id' => $setMealCategory->id,
            'allergens' => ['dairy', 'soy'],
            'preparation_time' => 30,
            'availability' => true,
            'is_featured' => true,
            'is_set_meal' => true,
            'rating_average' => 4.8,
            'rating_count' => 98,
        ]);

        // Attach components (Steak + Iced Latte)
        $steakCombo->components()->attach([
            $insertedFoodItems[0]->id => ['quantity' => 1], // Grilled Ribeye Steak
            $insertedDrinkItems[1]->id => ['quantity' => 1], // Iced Latte
        ]);

        // Set Meal 2: Burger Combo
        $burgerCombo = MenuItem::create([
            'name' => 'Classic Burger Meal',
            'description' => 'Our Classic Beef Burger served with crispy fries and a cold drink',
            'price' => 28.00,
            'category_id' => $setMealCategory->id,
            'allergens' => ['gluten', 'dairy', 'eggs'],
            'preparation_time' => 25,
            'availability' => true,
            'is_featured' => true,
            'is_set_meal' => true,
            'rating_average' => 4.7,
            'rating_count' => 187,
        ]);

        $burgerCombo->components()->attach([
            $insertedFoodItems[3]->id => ['quantity' => 1], // Classic Beef Burger
            $insertedDrinkItems[5]->id => ['quantity' => 1], // Coca Cola
        ]);

        // Set Meal 3: Pasta Combo
        $pastaCombo = MenuItem::create([
            'name' => 'Italian Pasta Set',
            'description' => 'Delicious Carbonara Pasta with creamy Cappuccino to complete your Italian experience',
            'price' => 35.00,
            'category_id' => $setMealCategory->id,
            'allergens' => ['gluten', 'dairy', 'eggs'],
            'preparation_time' => 20,
            'availability' => true,
            'is_featured' => false,
            'is_set_meal' => true,
            'rating_average' => 4.6,
            'rating_count' => 134,
        ]);

        $pastaCombo->components()->attach([
            $insertedFoodItems[2]->id => ['quantity' => 1], // Carbonara Pasta
            $insertedDrinkItems[0]->id => ['quantity' => 1], // Cappuccino
        ]);

        // Set Meal 4: Local Delight
        $localSet = MenuItem::create([
            'name' => 'Malaysian Local Set',
            'description' => 'Experience authentic Malaysian flavors with Nasi Goreng Kampung and Teh Tarik',
            'price' => 20.00,
            'category_id' => $setMealCategory->id,
            'allergens' => ['eggs', 'shellfish', 'dairy'],
            'preparation_time' => 18,
            'availability' => true,
            'is_featured' => false,
            'is_set_meal' => true,
            'rating_average' => 4.5,
            'rating_count' => 156,
        ]);

        $localSet->components()->attach([
            $insertedFoodItems[4]->id => ['quantity' => 1], // Nasi Goreng Kampung
            $insertedDrinkItems[10]->id => ['quantity' => 1], // Teh Tarik
        ]);

        // Set Meal 5: Family Feast
        $familyFeast = MenuItem::create([
            'name' => 'Family Feast Set',
            'description' => 'Perfect for sharing! Includes Fish & Chips, Chicken Wings, Fries, and 2 drinks',
            'price' => 75.00,
            'category_id' => $setMealCategory->id,
            'allergens' => ['gluten', 'fish', 'eggs', 'soy', 'dairy'],
            'preparation_time' => 35,
            'availability' => true,
            'is_featured' => true,
            'is_set_meal' => true,
            'rating_average' => 4.8,
            'rating_count' => 89,
        ]);

        $familyFeast->components()->attach([
            $insertedFoodItems[7]->id => ['quantity' => 1], // Fish & Chips
            $insertedFoodItems[8]->id => ['quantity' => 1], // Chicken Wings
            $insertedFoodItems[11]->id => ['quantity' => 1], // French Fries
            $insertedDrinkItems[5]->id => ['quantity' => 2], // Coca Cola x2
            $insertedDrinkItems[6]->id => ['quantity' => 1], // Iced Lemon Tea
        ]);

        // Set Meal 6: Breakfast Special
        $breakfastSet = MenuItem::create([
            'name' => 'Breakfast Special',
            'description' => 'Start your day right with Mushroom Soup, Caesar Salad, and Fresh Orange Juice',
            'price' => 38.00,
            'category_id' => $setMealCategory->id,
            'allergens' => ['dairy', 'gluten', 'eggs'],
            'preparation_time' => 15,
            'availability' => true,
            'is_featured' => false,
            'is_set_meal' => true,
            'rating_average' => 4.4,
            'rating_count' => 67,
        ]);

        $breakfastSet->components()->attach([
            $insertedFoodItems[10]->id => ['quantity' => 1], // Mushroom Soup
            $insertedFoodItems[9]->id => ['quantity' => 1], // Caesar Salad
            $insertedDrinkItems[2]->id => ['quantity' => 1], // Fresh Orange Juice
        ]);

        $this->command->info('Created 6 set meal items with components');
        $this->command->info('✅ Menu items seeder completed successfully!');
        $this->command->info('Total items created: ' . (count($insertedFoodItems) + count($insertedDrinkItems) + 6));
    }
}
