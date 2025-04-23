<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\EpisodeRequest;
use App\Http\Resources\EpisodeResource;
use App\Repositories\Interfaces\EpisodeRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Episodes",
 *     description="API Endpoints for Episodes"
 * )
 */
class EpisodeController extends ApiController
{
    /**
     * @var EpisodeRepositoryInterface
     */
    private EpisodeRepositoryInterface $episodeRepository;

    /**
     * EpisodeController constructor.
     *
     * @param EpisodeRepositoryInterface $episodeRepository
     */
    public function __construct(EpisodeRepositoryInterface $episodeRepository)
    {
        $this->episodeRepository = $episodeRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/episodes",
     *     summary="Get all episodes",
     *     tags={"Episodes"},
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         description="Filter episodes by title",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="podcast_id",
     *         in="query",
     *         description="Filter episodes by podcast ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="featured",
     *         in="query",
     *         description="Filter episodes by featured status",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="from_date",
     *         in="query",
     *         description="Filter episodes published after this date",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="to_date",
     *         in="query",
     *         description="Filter episodes published before this date",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Field to sort by",
     *         required=false,
     *         @OA\Schema(type="string", enum={"published_at", "title", "duration_in_seconds"})
     *     ),
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         description="Sort direction",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Episode")),
     *                 @OA\Property(property="links", type="object"),
     *                 @OA\Property(property="meta", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'title', 
            'podcast_id', 
            'featured',
            'from_date',
            'to_date',
            'sort_by', 
            'sort_direction'
        ]);
        $perPage = $request->input('per_page', 15);

        $episodes = $this->episodeRepository->getFiltered($filters, $perPage);
        
        return $this->successResponse(EpisodeResource::collection($episodes));
    }

    /**
     * @OA\Get(
     *     path="/api/episodes/featured",
     *     summary="Get featured episodes",
     *     tags={"Episodes"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of episodes to return",
     *         required=false,
     *         @OA\Schema(type="integer", default=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Episode")
     *             )
     *         )
     *     )
     * )
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 5);
        $episodes = $this->episodeRepository->getFeatured($limit);
        
        return $this->successResponse(EpisodeResource::collection($episodes));
    }

    /**
     * @OA\Get(
     *     path="/api/episodes/recent",
     *     summary="Get recent episodes",
     *     tags={"Episodes"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of episodes to return",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Episode")
     *             )
     *         )
     *     )
     * )
     */
    public function recent(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        $episodes = $this->episodeRepository->getRecent($limit);
        
        return $this->successResponse(EpisodeResource::collection($episodes));
    }

    /**
     * @OA\Get(
     *     path="/api/episodes/{slug}",
     *     summary="Get episode by slug",
     *     tags={"Episodes"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Episode slug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(property="data", ref="#/components/schemas/Episode")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Episode not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Episode not found"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $episode = $this->episodeRepository->findBySlug($slug);
            
            return $this->successResponse(new EpisodeResource($episode));
        } catch (\Exception $e) {
            return $this->notFoundResponse('Episode not found');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/episodes",
     *     summary="Create a new episode",
     *     tags={"Episodes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EpisodeRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Episode created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Episode created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Episode")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation errors"),
     *             @OA\Property(property="errors", type="object"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function store(EpisodeRequest $request): JsonResponse
    {
        $episode = $this->episodeRepository->create($request->validated());
        
        return $this->successResponse(
            new EpisodeResource($episode),
            'Episode created successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Put(
     *     path="/api/episodes/{id}",
     *     summary="Update an episode",
     *     tags={"Episodes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Episode ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EpisodeRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Episode updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Episode updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Episode")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Episode not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Episode not found"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation errors"),
     *             @OA\Property(property="errors", type="object"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function update(EpisodeRequest $request, int $id): JsonResponse
    {
        try {
            $episode = $this->episodeRepository->update($id, $request->validated());
            
            return $this->successResponse(
                new EpisodeResource($episode),
                'Episode updated successfully'
            );
        } catch (\Exception $e) {
            return $this->notFoundResponse('Episode not found');
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/episodes/{id}",
     *     summary="Delete an episode",
     *     tags={"Episodes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Episode ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Episode deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Episode deleted successfully"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Episode not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Episode not found"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->episodeRepository->delete($id);
            
            return $this->successResponse(null, 'Episode deleted successfully');
        } catch (\Exception $e) {
            return $this->notFoundResponse('Episode not found');
        }
    }
}