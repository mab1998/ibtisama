<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Department;
use App\Models\Faq;
use App\Models\KnowledgeBase;
use App\Models\Organization;
use App\Models\Priority;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call([
            RoleSeeder::class,
            CountrySeeder::class,
            EmailTemplateSeeder::class,
            FrontPageSeeder::class,
            LanguageSeeder::class,
            SettingSeeder::class,
            CategorySeeder::class,
            PrioritySeeder::class,
            StatusSeeder::class,
        ]);

        DB::table('departments')->truncate();
        Department::factory()->createMany([
            ['name' => 'Technical'], ['name' => 'Management'], ['name' => 'Hardware'], ['name' => 'Software'], ['name' => 'Development'], ['name' => 'Admin']
        ]);

        DB::table('types')->truncate();
        Type::factory()->createMany([
            ['name' => 'Service'], ['name' => 'Hardware'], ['name' => 'Software'], ['name' => 'Event'], ['name' => 'New type']
        ]);
    }
}
