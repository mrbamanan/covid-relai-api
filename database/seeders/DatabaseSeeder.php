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
             'name'=>'Admin',
             'email'=>'admin@email.com',
             'password'=>bcrypt('admin')
         ]);

        Author::factory(3)
            ->has(Post::factory(1), 'posts')
            ->create();
    }
}
