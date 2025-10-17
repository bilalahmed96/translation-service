<?php

namespace Tests\Feature;

use App\Models\Locale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup default locale
        Locale::create(['code' => 'en', 'name' => 'English']);
    }

    /** @test */
    public function it_creates_a_translation_via_api()
    {
        $response = $this->postJson('/api/v1/translations', [
            'tkey' => 'welcome.message',
            'locale' => 'en',
            'content' => 'Hello from API',
            'tags' => ['mobile'],
        ], [
            'Authorization' => 'Bearer ' . config('app.api_token'),
        ]);

        $response->assertCreated()
                 ->assertJsonStructure([
                     'id',
                     'translation_key_id',
                     'locale_id',
                     'content',
                 ]);
    }

    /** @test */
    public function it_searches_translations_by_locale()
    {
        $response = $this->getJson('/api/v1/translations/search?locale=en', [
            'Authorization' => 'Bearer ' . config('app.api_token'),
        ]);

        $response->assertOk();
    }

    /** @test */
    public function it_returns_unauthorized_without_token()
    {
        $response = $this->postJson('/api/v1/translations', [
            'tkey' => 'unauth.test',
            'locale' => 'en',
            'content' => 'Unauthorized test',
        ]);

        $response->assertStatus(401);
    }
}
