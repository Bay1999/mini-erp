<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['name' => 'Kopi Susu Gula Aren', 'price' => 18000],
            ['name' => 'Roti Bakar Cokelat Keju', 'price' => 22000],
            ['name' => 'Nasi Goreng Spesial', 'price' => 25000],
            ['name' => 'Ayam Bakar Taliwang', 'price' => 32000],
            ['name' => 'Es Teh Manis Jumbo', 'price' => 6000],
            ['name' => 'Kentang Goreng Keju', 'price' => 15000],
            ['name' => 'Mie Goreng Tek-Tek', 'price' => 18000],
            ['name' => 'Sate Ayam Madura (10 tusuk)', 'price' => 28000],
            ['name' => 'Soto Ayam Lamongan', 'price' => 20000],
            ['name' => 'Bakso Sapi Urat', 'price' => 22000],
            ['name' => 'Siomay Bandung Khas', 'price' => 15000],
            ['name' => 'Batagor Renyah Mang Asep', 'price' => 15000],
            ['name' => 'Pisang Goreng Pasir', 'price' => 12000],
            ['name' => 'Matcha Latte Dingin', 'price' => 24000],
            ['name' => 'Red Velvet Frappe', 'price' => 26000],
            ['name' => 'Spaghetti Bolognese', 'price' => 28000],
            ['name' => 'Club Sandwich Lezat', 'price' => 30000],
            ['name' => 'Jus Alpukat Kerok', 'price' => 18000],
            ['name' => 'Jus Mangga Manis', 'price' => 16000],
            ['name' => 'Tahu Crispy Pedas', 'price' => 10000],
            ['name' => 'Cireng Rujak Maknyus', 'price' => 12000],
            ['name' => 'Martabak Manis Cokelat', 'price' => 45000],
            ['name' => 'Martabak Telur Spesial', 'price' => 50000],
            ['name' => 'Dimsum Ayam (5 pcs)', 'price' => 18000],
            ['name' => 'Pempek Palembang Asli', 'price' => 25000],
            ['name' => 'Bubur Ayam Kuning', 'price' => 14000],
            ['name' => 'Nasi Kuning Komplit', 'price' => 16000],
        ];

        foreach ($items as $index => $itemData) {
            $code = 'ITM-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            Item::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $itemData['name'],
                    'price' => $itemData['price'],
                    'image' => '', // Default empty image
                ]
            );
        }
    }
}
