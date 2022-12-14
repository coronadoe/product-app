<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    protected $data = [
        'Ethiopia', 
        'Meat', 
        'Beef', 
        'Chili pepper',
        'China', 
        'Fish', 
        'Tofu', 
        'Sichuan pepper',
        'Peru', 
        'Potato', 
        'Yellow Chili pepper',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->categories()->each(function($title, $index) {
            Category::firstOrCreate(['name' => $title]);
        });
    }

    protected function categories(): Collection
    {
        return collect($this->data);
    }
}
