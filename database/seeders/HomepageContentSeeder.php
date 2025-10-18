<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HomepageContent;

class HomepageContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing content
        HomepageContent::truncate();

        // Hero Section - exactly matching admin form fields
        HomepageContent::create([
            'section_type' => 'hero',
            'title' => 'Welcome to The Stag',
            'subtitle' => 'Experience premium dining with our signature steaks, authentic Malaysian flavors, and exceptional service that creates unforgettable culinary memories',
            'highlighted_text' => 'The Stag',
            'primary_button_text' => 'Explore Menu',
            'secondary_button_text' => 'Learn More',
            'background_color_1' => '#6366f1',
            'background_color_2' => '#5856eb',
            'background_color_3' => '#ff6b35',
            'is_active' => true,
            'sort_order' => 1
        ]);

        // About Section - exactly matching admin form fields
        HomepageContent::create([
            'section_type' => 'about',
            'title' => 'About The Stag',
            'subtitle' => 'Discover the perfect blend of Western cuisine and Malaysian favorites in the heart of the city.',
            'description' => 'At The Stag, we pride ourselves on delivering exceptional dining experiences through carefully crafted dishes, premium ingredients, and warm hospitality. From our signature steaks to authentic local delicacies, every meal tells a story.',
            'feature_1' => 'Premium beef steaks aged to perfection',
            'feature_2' => 'Authentic Malaysian dishes with a modern twist',
            'feature_3' => 'Fresh pasta made daily with imported Italian ingredients',
            'feature_4' => 'Award-winning culinary team with years of expertise',
            'about_primary_button_text' => 'View Full Menu',
            'about_secondary_button_text' => 'Contact Us',
            'is_active' => true,
            'sort_order' => 2
        ]);

        // Promotions Section Header - exactly matching admin form fields
        HomepageContent::create([
            'section_type' => 'featured_menu',
            'title' => 'Featured Promotions',
            'subtitle' => "Don't miss out on our limited-time offers and special deals!",
            'is_active' => true,
            'sort_order' => 3
        ]);

        // Statistics Section - exactly matching admin form fields
        HomepageContent::create([
            'section_type' => 'statistics',
            'stat1_icon' => '🍽️',
            'stat1_value' => '100+',
            'stat1_label' => 'Menu Items',
            'stat2_icon' => '⭐',
            'stat2_value' => '4.8',
            'stat2_label' => 'Customer Rating',
            'stat3_icon' => '👨‍🍳',
            'stat3_value' => '15+',
            'stat3_label' => 'Years Experience',
            'stat4_icon' => '🏆',
            'stat4_value' => '25k+',
            'stat4_label' => 'Happy Customers',
            'is_active' => true,
            'sort_order' => 4
        ]);

        // Contact Section - exactly matching admin form fields
        HomepageContent::create([
            'section_type' => 'contact',
            'title' => 'Visit Us Today',
            'subtitle' => "We're located in the heart of the city, ready to serve you exceptional dining experiences.",
            'address' => '123 Food Street, City Center',
            'phone' => '+60 12-345-6789',
            'email' => 'hello@thestag.com',
            'hours' => 'Daily 11AM - 11PM',
            'feedback_form_title' => 'Share Your Feedback',
            'feedback_form_subtitle' => 'Help us improve by sharing your dining experience with us.',
            'is_active' => true,
            'sort_order' => 5
        ]);

    }
}
