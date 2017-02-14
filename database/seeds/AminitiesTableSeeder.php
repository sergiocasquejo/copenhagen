<?php

use Illuminate\Database\Seeder;

class AminitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $aminities = [
                ['name' => '26” lcd tv with cable'],
                ['name' => 'aircondition'],
                ['name' => 'balcony'],
                ['name' => 'closet'],
                ['name' => 'electric kettle'],
                ['name' => 'free wifi access'],
                ['name' => 'hot & cold shower'],
                ['name' => 'microwave oven'],
                ['name' => 'personal refrigerator 1.8 cu.ft.'],
                ['name' => 'telephone'],
                ['name' => 'working desk & chair'],
                ['name' => 'kitchen']
            ];
        foreach ($aminities as $a) {    
            DB::table('aminities')->insert($a);
        }
    }
}
