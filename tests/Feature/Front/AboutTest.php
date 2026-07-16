<?php

namespace Tests\Feature\Front;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_can_be_rendered(): void
    {
        $response = $this->get(route('front.about'));

        $response->assertOk();
    }

    public function test_about_page_shows_history_mission_benefits_and_photos(): void
    {
        $response = $this->get(route('front.about'));

        $response->assertOk();
        $response->assertSee('Історія');
        $response->assertSee('Місія');
        $response->assertSee('Швидка доставка');
        $response->assertSee('Фотографії');
    }
}
