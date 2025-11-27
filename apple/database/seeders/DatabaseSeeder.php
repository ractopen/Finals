<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        \App\Models\User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@apple.com',
            'password' => '$2y$12$YObOZEZJyzFlzN2JI9FVCuvMwkh1i4xXpAtXD4fO/9ZqYo5DkZgne', // 12345678
            'is_admin' => true,
        ]);

        // Users
        $users = [
            ['Royette', 'royette@gmail.com', '12345678'],
            ['Marian', 'marian@gmail.com', '12345678'],
            ['Jay-r', 'jay-r@gmail.com', '12345678'],
            ['Daniel', 'daniel@gmail.com', '12345678'],
            ['Sean', 'sean@gmail.com', '12345678'],
            ['Maria', 'maria@gmail.com', '12345678'],
            ['Ranela', 'ranela@gmail.com', '12345678'],
            ['John', 'john@gmail.com', '12345678'],
            ['Charlie', 'charlie@gmail.com', '12345678'],
            ['Raphael', 'raphael@gmail.com', '12345678'],
            ['Clarence', 'clarence@gmail.com', '12345678'],
            ['Jahlia', 'jahlia@gmail.com', '12345678'],
        ];

        foreach ($users as $userData) {
            if (\App\Models\User::where('email', $userData[1])->exists()) continue;

            \App\Models\User::create([
                'name' => $userData[0],
                'username' => strtolower($userData[0]),
                'email' => $userData[1],
                'password' => '$2y$12$YObOZEZJyzFlzN2JI9FVCuvMwkh1i4xXpAtXD4fO/9ZqYo5DkZgne', // 12345678
            ]);
        }

        // iPhone Lineup
        $iphones = [
            [
                'iPhone 15', 
                'The iPhone 15 features a durable color-infused glass and aluminum design. It features the Dynamic Island, which bubbles up alerts and Live Activities. The 48MP Main camera allows you to take super-high-resolution photos. And the A16 Bionic chip powers all kinds of advanced features.', 
                799.00, 
                '/images/products/iphone-15.png', 
                100
            ],
            [
                'iPhone 15 Plus', 
                'iPhone 15 Plus features a huge 6.7-inch Super Retina XDR display. It has the same powerful A16 Bionic chip as the iPhone 15 Pro. The all-day battery life lets you keep doing what you love. And with the 48MP Main camera, you can capture stunning photos with ease.', 
                899.00, 
                '/images/products/iphone-15-plus.png', 
                100
            ],
            [
                'iPhone 14', 
                'iPhone 14 comes with the A15 Bionic chip for lightning-fast performance. It features a dual-camera system for impressive photos in low light and bright light. Crash Detection calls for help when you can’t. And the Super Retina XDR display is beautiful to look at.', 
                699.00, 
                '/images/products/iphone-14.png', 
                75
            ],
            [
                'iPhone 13', 
                'iPhone 13 features a cinema-standard wide color gamut. The A15 Bionic chip delivers fast performance and great battery life. The dual-camera system captures beautiful photos and videos. And the durable design is water and dust resistant.', 
                599.00, 
                '/images/products/iphone-13.png', 
                50
            ],
            [
                'iPhone SE', 
                'iPhone SE puts the powerful A15 Bionic chip in the most popular size. It features a 4.7-inch Retina HD display. The advanced single-camera system takes beautiful photos. And the Home button with Touch ID gives you a fast, simple, and secure way to unlock your phone.', 
                429.00, 
                '/images/products/iphone-se.png', 
                200
            ],
        ];

        foreach ($iphones as $phone) {
            \App\Models\Item::create([
                'name' => $phone[0],
                'description' => $phone[1],
                'price' => $phone[2],
                'image_path' => $phone[3],
                'quantity' => $phone[4],
            ]);
        }
    }
}
