<?php

namespace App\Console\Commands;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\TranslationKey;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:seed-translations {count=100100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate the database with a large number of translation records for scalability testing.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = (int) $this->argument('count');

        $this->info("Seeding {$count} translation records...");

        Locale::factory()->count(5)->create();
        Tag::factory()->count(5)->create();
        TranslationKey::factory()->count((int)($count / 2))->create();

        $locales = Locale::pluck('id')->toArray();
        $keys = TranslationKey::pluck('id')->toArray();

        $batchSize = 1000;
        $data = [];
        $usedPairs = [];

        for ($i = 0; $i < $count; $i++) {
            $keyId = $keys[array_rand($keys)];
            $localeId = $locales[array_rand($locales)];
            $pairKey = "{$keyId}-{$localeId}";

            if (isset($usedPairs[$pairKey])) {
                continue;
            }

            $usedPairs[$pairKey] = true;

            $data[] = [
                'translation_key_id' => $keyId,
                'locale_id' => $localeId,
                'content' => fake()->sentence(),
                'context' => json_encode([
                    'env' => fake()->randomElement(['web', 'api', 'mobile']),
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($data) >= $batchSize) {
                DB::table('translations')->insertOrIgnore($data);
                $data = [];
            }

        }

        if (!empty($data)) {
            DB::table('translations')->insertOrIgnore($data);
        }

        $this->newLine(2);
        $this->info('Successfully seeded!');
        return Command::SUCCESS;
    }
}
