<?php

namespace App\Http\Controllers;

use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ExportController extends Controller
{
    public function __construct(private TranslationService $service) {}
    /**
     * @OA\Get(
     *     path="/export/{locale}",
     *     summary="Export all translations for a locale",
     *     tags={"Export"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="path",
     *         required=true,
     *         description="Locale code (e.g. en, fr, de)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="JSON export of translations"),
     *     security={{"bearerAuth":{}}}
     * )
     */

    public function export(string $locale, Request $request)
    {
        $filters = $request->only(['namespace', 'tags']);
        $cacheKey = "export_json_{$locale}_" . md5(json_encode($filters));

        if ($json = Cache::get($cacheKey)) {
            return response($json, 200, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'public, max-age=300',
            ]);
        }

        $data = $this->service->buildExportArray($locale, $filters);
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);

        Cache::put($cacheKey, $json, now()->addHours(12));

        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Cache-Control' => 'public, max-age=300',
        ]);
    }
}
