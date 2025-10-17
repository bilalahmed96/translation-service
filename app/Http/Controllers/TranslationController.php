<?php

namespace App\Http\Controllers;

use App\Services\TranslationService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Info(
 *      title="Translation Management API",
 *      version="1.0.0",
 *      description="Scalable API for managing translations and exporting locales"
 * ),
 * @OA\Server(
 *      url="http://localhost:8000/api/v1",
 *      description="Local Development Server"
 * ),
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT"
 * )
 */

class TranslationController extends Controller
{
    public function __construct(private TranslationService $service) {}

    /**
     * @OA\Get(
     *     path="/translations",
     *     summary="List all translations",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of translations"
     *     )
     * )
     */
    public function index()
    {
        return response()->json(
            $this->service->getAll(),
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *     path="/translations",
     *     summary="Create or update a translation",
     *     tags={"Translations"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tkey","locale","content"},
     *             @OA\Property(property="tkey", type="string", example="home.title"),
     *             @OA\Property(property="locale", type="string", example="en"),
     *             @OA\Property(property="content", type="string", example="Welcome!"),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string", example="web"))
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tkey'      => 'required|string|max:191',
            'namespace' => 'nullable|string|max:100',
            'locale'    => 'required|string|max:10',
            'content'   => 'required|string',
            'tags'      => 'array',
            'context'   => 'nullable|array',
        ]);

        $translation = $this->service->create($data);

        return response()->json($translation, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/translations/search",
     *     summary="Search translations by tag, locale, key, namespace or content",
     *     description="Fetch filtered translations based on provided search parameters such as tag, locale, tkey, namespace, or query text.",
     *     operationId="searchTranslations",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="tag",
     *         in="query",
     *         description="Filter by tag (e.g. web, api, mobile)",
     *         required=false,
     *         @OA\Schema(type="string", example="web")
     *     ),
     *     @OA\Parameter(
     *         name="locale",
     *         in="query",
     *         description="Filter by locale code (e.g. en, fr, es)",
     *         required=false,
     *         @OA\Schema(type="string", example="en")
     *     ),
     *     @OA\Parameter(
     *         name="tkey",
     *         in="query",
     *         description="Filter by translation key",
     *         required=false,
     *         @OA\Schema(type="string", example="home.title")
     *     ),
     *     @OA\Parameter(
     *         name="namespace",
     *         in="query",
     *         description="Filter by namespace",
     *         required=false,
     *         @OA\Schema(type="string", example="frontend")
     *     ),
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         description="Search within translation content",
     *         required=false,
     *         @OA\Schema(type="string", example="Welcome")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of translations matching the filters",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="Welcome to the site"),
     *                 @OA\Property(property="locale", type="object",
     *                     @OA\Property(property="code", type="string", example="en")
     *                 ),
     *                 @OA\Property(property="translation_key", type="object",
     *                     @OA\Property(property="tkey", type="string", example="home.title"),
     *                     @OA\Property(property="namespace", type="string", example="frontend")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized (invalid or missing token)"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function search(Request $request)
    {
        $filters = $request->only(['tag', 'locale', 'query', 'tkey', 'namespace']);
        $results = $this->service->search($filters);
    }
}
