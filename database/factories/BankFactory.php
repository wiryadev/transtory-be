<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $counter=0;
        $datetime = "Y-m-d H:i:s";
        $nameBank = ['BRI', 'BNI', 'BSI', 'Mandiri'];
        $banks = [
            'name' => $nameBank[$counter++],
            'created_at' => date($datetime, time()),
            'updated_at' => date($datetime, time()),
        ];
        return $banks;
    }
}