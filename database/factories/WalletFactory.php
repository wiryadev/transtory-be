<?php
namespace Database\Factories;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $banksId=$this->faker->numberBetween(1,4);
        $datetime = "Y-m-d H:i:s";
        $Wallets=[
            'users_id'=>User::pluck('id')->random(),
            'banks_id'=>$banksId,
            'created_at' => date($datetime, time()),
            'updated_at' => date($datetime, time()),
        ];
        if($banksId==1){
            $Wallets['account_no']=$this->faker->numberBetween (100001,100100);
        }
        if($banksId==2){
            $Wallets['account_no']=$this->faker->numberBetween (200001,200100);
        }
        if($banksId==3){
            $Wallets['account_no']=$this->faker->numberBetween (300001,300100);
        }
        if($banksId==4){
            $Wallets['account_no']=$this->faker->numberBetween (400001,400100);
        }
        return $Wallets;
    }
}
