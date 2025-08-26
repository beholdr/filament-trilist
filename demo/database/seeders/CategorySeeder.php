<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class CategorySeeder extends Seeder
{
    protected $data = [
        [
            'name' => 'Category 1',
            'children' => [
                ['name' => 'Subcategory 1-1'],
                ['name' => 'Subcategory 1-2'],
            ],
        ],
        [
            'name' => 'Category 2',
            'children' => [
                [
                    'name' => 'Subcategory 2-3',
                    'children' => [
                        ['name' => 'Sub-subcategory 2-3-1'],
                        ['name' => 'Sub-subcategory 2-3-2'],
                    ],
                ],
                ['name' => 'Subcategory 2-4'],
            ],
        ],
        [
            'name' => 'Category 3',
            'children' => [
                ['name' => 'Subcategory 3-5'],
                ['name' => 'Subcategory 3-6'],
            ]
        ]
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Arr::map($this->data, fn ($item) => $this->createCategory($item));
    }

    protected function createCategory($item, $parent = null)
    {
        $category = Category::create([
            'parent_id' => $parent,
            'name' => $item['name'],
        ]);

        if (!empty($item['children'])) {
            foreach ($item['children'] as $child) {
                $this->createCategory($child, $category->id);
            }
        }
    }
}
