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

        $category3 = \App\Models\Category::firstOrCreate([
            'name'=>'Workshop',
            'slug'=>'workshop'

        ]);

        $category4 = \App\Models\Category::firstOrCreate([
            'name'=>'Competition',
            'slug'=>'competition'

        ]);

        $kategori5 = \App\Models\Category::firstOrCreate([
            'name'=>'Exhibition',
            'slug'=>'exhibition'

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
    

         \App\Models\Event::create([ 
            'category_id'  => $category3->id, 
            'title'=>'Workshop - Mastering Web Development' , 
            'description'=>'Tingkatkan keterampilan pengembangan web Anda dengan workshop intensif yang dipandu oleh para ahli industri.', 
            'date'=>'2026-05-15 09:00:00', 
            'location'=>'Inkubator Amikom' , 
            'price'  =>  75000 , 
            'stock'  =>  50 , 
            'poster_path'  =>  'posters/event-4.png' , 
        ]);
        
        \App\Models\Event::create([ 
            'category_id'  => $category4->id, 
            'title'=>'Coding Competition - CodeMaster Challenge' , 
            'description'=>'Uji kemampuan coding Anda dalam kompetisi yang menantang dan menangkan hadiah menarik.', 
            'date'=>'2026-05-20 14:00:00', 
            'location'=>'Amikom Baru' , 
            'price'  =>  30000 , 
            'stock'  =>  200 , 
            'poster_path'  =>  'posters/event-5.png' , 
        ]);

            \App\Models\Event::create([ 
                'category_id'  => $kategori5->id, 
                'title'=>'Exhibition - Tech Innovations Expo 2026' , 
                'description'=>'Temukan inovasi teknologi terbaru dan solusi canggih dalam pameran yang menampilkan berbagai perusahaan teknologi terkemuka.', 
                'date'=>'2026-05-25 10:00:00', 
                'location'=>'Amikom Baru' , 
                'price'  =>  10000 , 
                'stock'  =>  500 , 
                'poster_path'  =>  'posters/event-6.png' , 
            ]);

            

    }
}
