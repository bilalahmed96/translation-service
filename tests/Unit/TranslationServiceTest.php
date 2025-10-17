<?php

namespace Tests\Unit;

use App\Models\Locale;
use App\Models\Translation;
use App\Services\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TranslationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TranslationService::class);
        Locale::create(['code' => 'en', 'name' => 'English']);
    }

    public function test_it_creates_a_translation()
    {
        $data = [
            'tkey' => 'home.title',
            'namespace' => 'web',
            'locale' => 'en',
            'content' => 'Welcome Home!',
        ];

        $t = $this->service->create($data);

        $this->assertInstanceOf(Translation::class, $t);
        $this->assertEquals('Welcome Home!', $t->content);
    }
}
