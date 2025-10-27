<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VoucherTemplate;
use App\Models\CustomerVoucher;
use App\Models\User;
use Carbon\Carbon;

class VoucherTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Voucher Templates
        $templates = [
            [
                'name' => 'RM10 OFF',
                'title' => 'RM10 Discount Voucher',
                'description' => 'Get RM10 off your order',
                'discount_type' => 'fixed',
                'discount_value' => 10.00,
                'minimum_spend' => 50.00,
                'max_discount' => null,
                'terms_conditions' => 'Minimum spend RM50 required',
                'expiry_days' => 30
            ],
            [
                'name' => 'RM20 OFF',
                'title' => 'RM20 Discount Voucher',
                'description' => 'Get RM20 off your order',
                'discount_type' => 'fixed',
                'discount_value' => 20.00,
                'minimum_spend' => 100.00,
                'max_discount' => null,
                'terms_conditions' => 'Minimum spend RM100 required',
                'expiry_days' => 30
            ],
            [
                'name' => '15% OFF',
                'title' => '15% Discount Voucher',
                'description' => 'Get 15% off your order (max RM30)',
                'discount_type' => 'percentage',
                'discount_value' => 15.00,
                'minimum_spend' => 30.00,
                'max_discount' => 30.00,
                'terms_conditions' => 'Minimum spend RM30. Maximum discount RM30',
                'expiry_days' => 60
            ],
            [
                'name' => 'FREE DELIVERY',
                'title' => 'Free Delivery Voucher',
                'description' => 'Free delivery on your next order',
                'discount_type' => 'fixed',
                'discount_value' => 5.00,
                'minimum_spend' => 20.00,
                'max_discount' => null,
                'terms_conditions' => 'Free delivery (RM5 value)',
                'expiry_days' => 14
            ],
        ];

        foreach ($templates as $templateData) {
            VoucherTemplate::updateOrCreate(
                ['name' => $templateData['name']],
                $templateData
            );
        }

        $this->command->info('✅ Voucher templates created!');

        // Get the first customer user (or any logged-in user)
        $customer = User::whereHas('customerProfile')->first();

        if ($customer && $customer->customerProfile) {
            $this->command->info('Creating test vouchers for user: ' . $customer->email);

            // Create customer vouchers for all templates
            $allTemplates = VoucherTemplate::all();

            foreach ($allTemplates as $template) {
                // Check if voucher already exists
                $exists = CustomerVoucher::where('customer_profile_id', $customer->customerProfile->id)
                    ->where('voucher_template_id', $template->id)
                    ->exists();

                if (!$exists) {
                    CustomerVoucher::create([
                        'customer_profile_id' => $customer->customerProfile->id,
                        'voucher_template_id' => $template->id,
                        'voucher_code' => 'TEST-' . strtoupper(substr(md5(rand()), 0, 8)),
                        'status' => 'active',
                        'expiry_date' => Carbon::now()->addDays($template->expiry_days),
                        'redeemed_at' => null
                    ]);

                    $this->command->info('  ✓ Created: ' . $template->name);
                }
            }

            $this->command->info('✅ Test vouchers created for customer!');
        } else {
            $this->command->warn('⚠️  No customer user found. Please create a customer account first.');
        }

        $this->command->info('');
        $this->command->info('Summary:');
        $this->command->info('  Voucher Templates: ' . VoucherTemplate::count());
        $this->command->info('  Customer Vouchers: ' . CustomerVoucher::count());
    }
}
