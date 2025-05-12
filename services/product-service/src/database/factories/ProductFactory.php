<?php 

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    // The model that the factory corresponds to
    protected $model = Product::class;

    // Define the model's default state
    public function definition()
    {
        return [
            'id'            => Str::uuid(),
            'sku'           => strtoupper(Str::random(10)),
            'title'         => $this->faker->unique()->words(3, true),
            'slug'          => Str::slug($this->faker->unique()->words(3, true)),
            'price'         => $this->faker->randomFloat(2, 10, 999),
            'description'   => $this->faker->sentence(),
            'status'        => 'draft',
            'image'         => 'products/placeholder.png', // Path for the image
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
    }
}
