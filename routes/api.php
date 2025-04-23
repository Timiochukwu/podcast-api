<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PodcastController;
use App\Http\Controllers\Api\EpisodeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API version prefix
Route::prefix('v1')->group(function () {
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        
        // Protected auth routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/user', [AuthController::class, 'user']);
        });
    });
    
    // Categories routes
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/featured', [CategoryController::class, 'featured']);
        Route::get('/{slug}', [CategoryController::class, 'show']);
        Route::get('/{slug}/podcasts', [CategoryController::class, 'podcasts']);
        
        // Protected routes (require auth)
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/', [CategoryController::class, 'store']);
            Route::put('/{id}', [CategoryController::class, 'update']);
            Route::delete('/{id}', [CategoryController::class, 'destroy']);
        });
    });
    
    // Podcasts routes
    Route::prefix('podcasts')->group(function () {
        Route::get('/', [PodcastController::class, 'index']);
        Route::get('/featured', [PodcastController::class, 'featured']);
        Route::get('/{slug}', [PodcastController::class, 'show']);
        Route::get('/{slug}/episodes', [PodcastController::class, 'episodes']);
        
        // Protected routes (require auth)
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/', [PodcastController::class, 'store']);
            Route::put('/{id}', [PodcastController::class, 'update']);
            Route::delete('/{id}', [PodcastController::class, 'destroy']);
        });
    });
    
    // Episodes routes
    Route::prefix('episodes')->group(function () {
        Route::get('/', [EpisodeController::class, 'index']);
        Route::get('/featured', [EpisodeController::class, 'featured']);
        Route::get('/recent', [EpisodeController::class, 'recent']);
        Route::get('/{slug}', [EpisodeController::class, 'show']);
        
        // Protected routes (require auth)
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/', [EpisodeController::class, 'store']);
            Route::put('/{id}', [EpisodeController::class, 'update']);
            Route::delete('/{id}', [EpisodeController::class, 'destroy']);
        });
    });
    
    // API documentation route (auto-generated with Swagger)
    Route::get('/documentation', function () {
        return response()->json([
            'message' => 'API documentation is available via the Postman collection in the /docs directory of the project repository.',
            'instructions' => 'Import the collection JSON file into Postman to explore and test the API endpoints.'
        ]);
    });;
});

// Public routes
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});

// Catch-all route for undefined API routes
Route::fallback(function () {
    return response()->json([
        'success' => false, 
        'message' => 'API endpoint not found',
        'data' => null
    ], 404);
});