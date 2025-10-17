<?php

namespace App\Services;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\TranslationKey;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class TranslationService
{
    /**
     * Return paginated translations with relations.
     */
    public function getAll(int $perPage = 50)
    {
        return Translation::query()
            ->with(['translationKey', 'locale'])
            ->select('id', 'translation_key_id', 'locale_id', 'content', 'created_at')
            ->paginate($perPage);
    }

    /**
     * Create a new translation, auto-create key/locale/tag if missing.
     */
    public function create(array $data): Translation
    {
        return DB::transaction(function () use ($data) {
            $key = TranslationKey::firstOrCreate([
                'tkey'      => $data['tkey'],
                'namespace' => $data['namespace'] ?? null,
            ]);

            $locale = Locale::firstOrCreate(
                ['code' => $data['locale']],
                ['name' => strtoupper($data['locale'])] // e.g. "EN"
            );

            $translation = Translation::updateOrCreate(
                [
                    'translation_key_id' => $key->id,
                    'locale_id'          => $locale->id,
                ],
                [
                    'content' => $data['content'],
                    'context' => $data['context'] ?? [],
                ]
            );

            if (!empty($data['tags'])) {
                $tagIds = collect($data['tags'])->map(function ($tagName) {
                    return Tag::firstOrCreate(['name' => $tagName])->id;
                });
                $key->tags()->syncWithoutDetaching($tagIds);
            }

            Cache::forget("translations_{$locale->code}");

            return $translation->load(['translationKey', 'locale']);
        });
    }


    /**
     * Search by tag, locale, key, namespace or free text.
     */
    public function search(array $filters): Collection
    {
        $query = Translation::query()->with(['translationKey', 'locale']);

        if (!empty($filters['query'])) {
            $query->where('content', 'like', '%' . $filters['query'] . '%');
        }

        if (!empty($filters['tkey'])) {
            $query->whereHas(
                'translationKey',
                fn($q) =>
                $q->where('tkey', 'like', '%' . $filters['tkey'] . '%')
            );
        }

        if (!empty($filters['namespace'])) {
            $query->whereHas(
                'translationKey',
                fn($q) =>
                $q->where('namespace', $filters['namespace'])
            );
        }

        if (!empty($filters['tag'])) {
            $query->where('context->env', $filters['tag']);
        }

        if (!empty($filters['locale'])) {
            $query->whereHas(
                'locale',
                fn($q) =>
                $q->where('code', $filters['locale'])
            );
        }

        return $query->limit(200)->get();
    }

    /**
     * Return optimized dataset for frontend export.
     */
    public function buildExportArray(string $localeCode, array $filters = []): array
    {
        $localeId = Locale::where('code', $localeCode)->value('id');
        $query = DB::table('translations as t')
            ->join('translation_keys as k', 't.translation_key_id', '=', 'k.id')
            ->select('k.tkey', 't.content')
            ->where('t.locale_id', $localeId);

        if (!empty($filters['namespace'])) {
            $query->where('k.namespace', $filters['namespace']);
        }

        if (!empty($filters['tags'])) {
            $query->join('translation_key_tag as kt', 'kt.translation_key_id', '=', 'k.id')
                ->join('tags as tg', 'tg.id', '=', 'kt.tag_id')
                ->whereIn('tg.name', (array) $filters['tags']);
        }

        return $query->pluck('t.content', 'k.tkey')->toArray();
    }
}
