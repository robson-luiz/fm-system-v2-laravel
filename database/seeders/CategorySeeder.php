<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Alimentação',
                'slug' => 'alimentacao',
                'icon' => '🍽️',
                'color' => '#F59E0B', // Amber
                'is_active' => true,
            ],
            [
                'name' => 'Transporte',
                'slug' => 'transporte',
                'icon' => '🚗',
                'color' => '#3B82F6', // Blue
                'is_active' => true,
            ],
            [
                'name' => 'Lazer',
                'slug' => 'lazer',
                'icon' => '🎮',
                'color' => '#8B5CF6', // Purple
                'is_active' => true,
            ],
            [
                'name' => 'Saúde',
                'slug' => 'saude',
                'icon' => '💊',
                'color' => '#10B981', // Green
                'is_active' => true,
            ],
            [
                'name' => 'Educação',
                'slug' => 'educacao',
                'icon' => '📚',
                'color' => '#06B6D4', // Cyan
                'is_active' => true,
            ],
            [
                'name' => 'Moradia',
                'slug' => 'moradia',
                'icon' => '🏠',
                'color' => '#14B8A6', // Teal
                'is_active' => true,
            ],
            [
                'name' => 'Serviços',
                'slug' => 'servicos',
                'icon' => '🔧',
                'color' => '#EF4444', // Red
                'is_active' => true,
            ],
            [
                'name' => 'Outros',
                'slug' => 'outros',
                'icon' => '📌',
                'color' => '#6B7280', // Gray
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('✅ 8 categorias padrão criadas com sucesso!');
    }
}
