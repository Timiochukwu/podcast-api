<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Podcast Platform API",
 *     version="1.0.0",
 *     description="API documentation for the Podcast Platform",
 *     @OA\Contact(
 *         email="admin@example.com",
 *         name="API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="/api/v1",
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="API Token"
 * )
 * 
 * @OA\Schema(
 *     schema="Category",
 *     required={"id", "name", "slug", "image_url"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="News & Storytelling"),
 *     @OA\Property(property="slug", type="string", example="news-storytelling"),
 *     @OA\Property(property="description", type="string", example="Podcasts about current events and storytelling"),
 *     @OA\Property(property="image_url", type="string", example="https://example.com/images/news.jpg"),
 *     @OA\Property(property="is_featured", type="boolean", example=true),
 *     @OA\Property(property="sort_order", type="integer", example=1),
 *     @OA\Property(property="podcasts_count", type="integer", example=15),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="Podcast",
 *     required={"id", "title", "slug", "image_url", "author_name", "category_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="The Podcast Experience"),
 *     @OA\Property(property="slug", type="string", example="the-podcast-experience"),
 *     @OA\Property(property="description", type="string", example="A weekly discussion about current events"),
 *     @OA\Property(property="image_url", type="string", example="https://example.com/images/podcast1.jpg"),
 *     @OA\Property(property="author_name", type="string", example="John Doe"),
 *     @OA\Property(property="is_featured", type="boolean", example=true),
 *     @OA\Property(property="category", ref="#/components/schemas/Category"),
 *     @OA\Property(property="tags", type="array", @OA\Items(ref="#/components/schemas/Tag")),
 *     @OA\Property(property="episodes_count", type="integer", example=25),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="Episode",
 *     required={"id", "title", "slug", "audio_url", "duration_in_seconds", "published_at", "podcast_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="The Good, the Bad, and the Ugly"),
 *     @OA\Property(property="slug", type="string", example="the-good-the-bad-and-the-ugly"),
 *     @OA\Property(property="description", type="string", example="In this episode, we discuss..."),
 *     @OA\Property(property="audio_url", type="string", example="https://example.com/audio/episode1.mp3"),
 *     @OA\Property(property="duration_in_seconds", type="integer", example=1800),
 *     @OA\Property(property="formatted_duration", type="string", example="30:00"),
 *     @OA\Property(property="transcript", type="string", example="Full transcript of the episode..."),
 *     @OA\Property(property="is_featured", type="boolean", example=true),
 *     @OA\Property(property="published_at", type="string", format="date-time"),
 *     @OA\Property(property="podcast", ref="#/components/schemas/Podcast"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="Tag",
 *     required={"id", "name", "slug"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Politics"),
 *     @OA\Property(property="slug", type="string", example="politics"),
 *     @OA\Property(property="podcasts_count", type="integer", example=10),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="CategoryRequest",
 *     required={"name", "slug", "image_url"},
 *     @OA\Property(property="name", type="string", example="News & Storytelling"),
 *     @OA\Property(property="slug", type="string", example="news-storytelling"),
 *     @OA\Property(property="description", type="string", example="Podcasts about current events and storytelling"),
 *     @OA\Property(property="image_url", type="string", example="https://example.com/images/news.jpg"),
 *     @OA\Property(property="is_featured", type="boolean", example=true),
 *     @OA\Property(property="sort_order", type="integer", example=1)
 * )
 * 
 * @OA\Schema(
 *     schema="PodcastRequest",
 *     required={"title", "slug", "image_url", "author_name", "category_id"},
 *     @OA\Property(property="title", type="string", example="The Podcast Experience"),
 *     @OA\Property(property="slug", type="string", example="the-podcast-experience"),
 *     @OA\Property(property="description", type="string", example="A weekly discussion about current events"),
 *     @OA\Property(property="image_url", type="string", example="https://example.com/images/podcast1.jpg"),
 *     @OA\Property(property="author_name", type="string", example="John Doe"),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="is_featured", type="boolean", example=true),
 *     @OA\Property(property="tags", type="array", @OA\Items(type="integer"), example={1, 2, 3})
 * )
 * 
 * @OA\Schema(
 *     schema="EpisodeRequest",
 *     required={"title", "slug", "audio_url", "duration_in_seconds", "published_at", "podcast_id"},
 *     @OA\Property(property="podcast_id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="The Good, the Bad, and the Ugly"),
 *     @OA\Property(property="slug", type="string", example="the-good-the-bad-and-the-ugly"),
 *     @OA\Property(property="description", type="string", example="In this episode, we discuss..."),
 *     @OA\Property(property="audio_url", type="string", example="https://example.com/audio/episode1.mp3"),
 *     @OA\Property(property="duration_in_seconds", type="integer", example=1800),
 *     @OA\Property(property="transcript", type="string", example="Full transcript of the episode..."),
 *     @OA\Property(property="is_featured", type="boolean", example=true),
 *     @OA\Property(property="published_at", type="string", format="date-time")
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}