<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{

    
    public function run()
    {
        \DB::table("members")->truncate();
        $m = Member::factory()
            ->count(30)
            ->create();

    }
}
