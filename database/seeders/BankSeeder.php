<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $banks = [
            [
                'name' => "BRI"
            ],
            [
                'name' => "BNI"
            ],
            [
                'name' => "Mandiri"
            ],
            [
                'name' => "BSI"
            ],
        ];

        Bank::insert($banks);
    }
}
