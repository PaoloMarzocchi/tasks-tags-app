<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagWorkSeeder extends Seeder
{
    public function run(): void
    {
        $name = 'Work';
        $slug = Str::slug($name);

        Tag::updateOrCreate([
            'slug' => $slug,
        ], [
            'name' => $name,
            'color' => null,
            'parent_id' => null,
        ]);
    }
}
