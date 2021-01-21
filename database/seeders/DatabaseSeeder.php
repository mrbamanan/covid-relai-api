<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Post;
use Illuminate\Database\Seeder;
use phpseclib\Crypt\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

         \App\Models\User::create([
             'name'=>'Bamanan Test',
             'email'=>'bamanan@email.com',
             'password'=>bcrypt('BamananTest')
         ]);

        Author::factory(10)
            ->has(Post::factory(1), 'posts')
            ->create();
    }
}
