<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PodcastRequest;
use App\Http\Resources\EpisodeResource;
use App\Http\Resources\PodcastResource;
use App\Repositories\Interfaces\EpisodeRepositoryInterface;
use App\Repositories\Interfaces\PodcastRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Podcasts",
 *     description="API Endpoints for Podcasts"
 * )
 */
class PodcastController extends ApiController
{
    /**
     * @var PodcastRepositoryInterface
     */
    private PodcastRepositoryInterface $podcastRepository;

    /**
     * @var EpisodeRepositoryInterface
     */
    private EpisodeRepositoryInterface $episodeRepository;

    /**
     * PodcastController constructor.
     *
     * @param PodcastRepositoryInterface $podcastRepository
     * @param EpisodeRepositoryInterface $episodeRepository
     */
    public function __construct(
        PodcastRepositoryInterface $podcastRepository,
        EpisodeRepositoryInterface $episodeRepository
    ) {
        $this->podcastRepository = $podcastRepository;
        $this->episodeRepository = $episodeRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/podcasts",
     *     summary="Get all podcasts",
     *     tags={"Podcasts"},
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         description="Filter podcasts by title",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="author",
     *         in="query",
     *         description="Filter podcasts by author",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="featured",
     *         in="query",
     *         description="Filter podcasts by featured status",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Field to sort by",
     *         required=false,
     *         @OA\Schema(type="string", enum={"created_at", "title", "author_name"})
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
     *                 @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Podcast")),
     *                 @OA\Property(property="links", type="object"),
     *                 @OA\Property(property="meta", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['title', 'author', 'featured', 'sort_by', 'sort_direction']);
        $perPage = $request->input('per_page', 15);

        $podcasts = $this->podcastRepository->getFiltered($filters, $perPage);
        
        return $this->successResponse(PodcastResource::collection($podcasts));
    }

    /**
     * @OA\Get(
     *     path="/api/podcasts/featured",
     *     summary="Get featured podcasts",
     *     tags={"Podcasts"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of podcasts to return",
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
     *                 @OA\Items(ref="#/components/schemas/Podcast")
     *             )
     *         )
     *     )
     * )
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 5);
        $podcasts = $this->podcastRepository->getFeatured($limit);
        
        return $this->successResponse(PodcastResource::collection($podcasts));
    }

    /**
     * @OA\Get(
     *     path="/api/podcasts/{slug}",
     *     summary="Get podcast by slug",
     *     tags={"Podcasts"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Podcast slug",
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
     *             @OA\Property(property="data", ref="#/components/schemas/Podcast")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Podcast not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Podcast not found"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $podcast = $this->podcastRepository->findBySlug($slug);
            
            return $this->successResponse(new PodcastResource($podcast));
        } catch (\Exception $e) {
            return $this->notFoundResponse('Podcast not found');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/podcasts/{slug}/episodes",
     *     summary="Get episodes by podcast",
     *     tags={"Podcasts"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Podcast slug",
     *         required=true,
     *         @OA\Schema(type="string")
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
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Podcast not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Podcast not found"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function episodes(string $slug, Request $request): JsonResponse
    {
        try {
            $podcast = $this->podcastRepository->findBySlug($slug);
            $perPage = $request->input('per_page', 15);
            
            $episodes = $this->episodeRepository->getByPodcast($podcast->id, $perPage);
            
            return $this->successResponse(EpisodeResource::collection($episodes));
        } catch (\Exception $e) {
            return $this->notFoundResponse('Podcast not found');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/podcasts",
     *     summary="Create a new podcast",
     *     tags={"Podcasts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PodcastRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Podcast created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Podcast created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Podcast")
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
    public function store(PodcastRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $podcast = $this->podcastRepository->create($validatedData);
        
        // Attach tags if provided
        if (isset($validatedData['tags']) && is_array($validatedData['tags'])) {
            $podcast->tags()->sync($validatedData['tags']);
        }
        
        return $this->successResponse(
            new PodcastResource($podcast),
            'Podcast created successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Put(
     *     path="/api/podcasts/{id}",
     *     summary="Update a podcast",
     *     tags={"Podcasts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Podcast ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PodcastRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Podcast updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Podcast updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Podcast")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Podcast not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Podcast not found"),
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
    public function update(PodcastRequest $request, int $id): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $podcast = $this->podcastRepository->update($id, $validatedData);
            
            // Sync tags if provided
            if (isset($validatedData['tags']) && is_array($validatedData['tags'])) {
                $podcast->tags()->sync($validatedData['tags']);
            }
            
            return $this->successResponse(
                new PodcastResource($podcast),
                'Podcast updated successfully'
            );
        } catch (\Exception $e) {
            return $this->notFoundResponse('Podcast not found');
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/podcasts/{id}",
     *     summary="Delete a podcast",
     *     tags={"Podcasts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Podcast ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Podcast deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Podcast deleted successfully"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Podcast not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Podcast not found"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->podcastRepository->delete($id);
            
            return $this->successResponse(null, 'Podcast deleted successfully');
        } catch (\Exception $e) {
            return $this->notFoundResponse('Podcast not found');
        }
    }
}