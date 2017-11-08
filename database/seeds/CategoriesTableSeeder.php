<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            ['subject'  =>  '语文', 'parent_id'   =>  0, 'created_at' =>  \Carbon\Carbon::now()->addHours(8)],
            ['subject'  =>  '数学', 'parent_id'   =>  0, 'created_at' =>  \Carbon\Carbon::now()->addHours(8)],
            ['subject'  =>  '英语', 'parent_id'   =>  0, 'created_at' =>  \Carbon\Carbon::now()->addHours(8)],
        ]);
    }
}
