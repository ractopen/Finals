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
            'email' => 'admin@example.com',
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
            ['Royette', 'royette@gmail.com', '12345678'], // Duplicate email in list, handled by firstOrCreate or just unique check? User list has duplicate Royette. I'll skip duplicate.
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

        // Items
        $items = [
            ['Nathan Abrenica', 'edit description in admin panel', 160.00, 115],
            ['Marian Nicol Arpon', 'edit description in admin panel', 190.00, 188],
            ['Jay-r Baldevieso', 'edit description in admin panel', 150.00, 25],
            ['Daniel Bulso', 'edit description in admin panel', 130.00, 73],
            ['Sean Cole Calixton', 'edit description in admin panel', 170.00, 0],
            ['Maria Cleare Declanan', 'edit description in admin panel', 180.00, 142],
            ['Ranela Esgana', 'edit description in admin panel', 130.00, 91],
            ['John Roben Manayon', 'edit description in admin panel', 160.00, 167],
            ['Charlie Mangyaw', 'edit description in admin panel', 140.00, 48],
            ['Raphael Simone Oddy Pescadero', 'edit description in admin panel', 270.00, 102],
            ['Clarence Dave Piñas', 'edit description in admin panel', 180.00, 59],
            ['Jehlia Seco', 'edit description in admin panel', 130.00, 179],
            ['Royette Andrei Telar', 'edit description in admin panel', 170.00, 0],
        ];

        foreach ($items as $itemData) {
            \App\Models\Item::create([
                'name' => $itemData[0],
                'description' => $itemData[1],
                'price' => $itemData[2],
                'quantity' => $itemData[3],
            ]);
        }
    }
}
