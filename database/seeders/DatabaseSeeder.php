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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin Amikom',
            'email' => 'admin@amikom.ac.id',
            'password'=> bcrypt('password'),
            'role'=>'admin',
        ]);

        $category =\App\Models\Category::create([
            'name'=> 'Seminar IT',
            'slug'=> 'seminar-it',

        ]);

        $category2 = \App\Models\Category::firstOrCreate([
            'name'=>'Entertainment',
            'slug'=>'entertainment'

        ]);

        \App\Models\Event::create([
            'category_id'=> $category2->id,
            'title'=> 'Jazz Night 2025',
            'description'=>'Nikmati malam yang indah dengan alunan musik jazz yang merdu.',
            'date'=> '2026-05-10 19:00:00',
            'location' => 'Amikom Baru',
            'price'=> 50000,
            'stock'=> 100,
            'poster_path' => 'posters/event-1.png',
        ]);

        \App\Models\Event::create([
            'category_id'=>$category->id,
            'title' =>'Hackathon - Unleash Your Inner Developer',
            'description'=>'Jelajahi tren terkini dalam kecerdasan buatan dan teknologi masa depan bersama para ahli di bidangnya.',
            'date'=> '2026-05-05 10:00:00',
            'location'=>'Inkubator Amikom',
            'price'=>50000,
            'stock'=>100,
            'poster_path'=>'posters/event-2.png'
        ]);

        \App\Models\Event::create([ 
            'category_id'  => $category->id, 
            'title'=>'AI & FUTURE TECH SUMMIT 2026' , 
            'description'=>'Jelajahi  tren  terkini  dalam  kecerdasan  buatan  dan teknologi masa depan bersama para ahli di bidangnya.', 
            'date'=>'2026-05-01 13:00:00', 
            'location'=>'Cinema Unit 6' , 
            'price'  =>  50000 , 
            'stock'  =>  100 , 
            'poster_path'  =>  'posters/event-3.png' , 
            ]); 
    
    }
}
