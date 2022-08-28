<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Member::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $this->faker->addProvider(new \Faker\Provider\id_ID\Person($this->faker));
        $this->faker->addProvider(new \Faker\Provider\id_ID\Address($this->faker));
        $this->faker->addProvider(new \Faker\Provider\id_ID\PhoneNumber($this->faker));
        $gender = $this->faker->randomElement(['male', 'female']);
        $postcode = $this->faker->numberBetween(20111,20512);
        return [
            'fullname' => $this->faker->name($gender),
            "nik" => $this->faker->nik($gender),
            "address" => "Jl. " . $this->faker->streetName() ." No. ". $this->faker->buildingNumber() . " Medan " . $postcode ,
            "phone_number" => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            "status" => 1,
        ];
    }
}
