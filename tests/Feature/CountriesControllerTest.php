<?php

namespace Tests\Feature;

use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountriesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_looks_for_a_country(): void
    {
        Country::create([
            'code' => 'IQ',
            'name' => 'Iraq',
        ]);

        $this->getJson('api/countries?q=ir')
            ->assertExactJson([
                'data' => [
                    [
                        'id' => 2,
                        'code' => 'IQ',
                        'name' => 'Iraq',
                    ],
                ],
            ]);
    }
}
