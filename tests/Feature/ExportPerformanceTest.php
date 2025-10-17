<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Translation;
use App\Models\TranslationKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed 2,000 translations for performance test
        $locale = Locale::firstOrCreate(['code' => 'en'], ['name' => 'English']);

        for ($i = 0; $i < 2000; $i++) {
            $key = TranslationKey::create([
                'tkey' => "test.key.{$i}",
                'namespace' => 'web',
            ]);

            Translation::create([
                'translation_key_id' => $key->id,
                'locale_id' => $locale->id,
                'content' => "Value {$i}",
            ]);
        }
    }

    /** @test */
    public function it_exports_translations_in_under_500ms()
    {
        $start = microtime(true);

        $response = $this->getJson('/api/v1/export/en', [
            'Authorization' => 'Bearer ' . config('app.api_token'),
        ]);

        $response->assertOk()
                 ->assertHeader('Content-Type', 'application/json');

        $elapsed = (microtime(true) - $start) * 1000;
        echo "\nExport response time: {$elapsed} ms\n";

        $this->assertLessThanOrEqual(500, $elapsed, 'Export took longer than 500ms.');
    }
}
