use App\Http\Controllers\Api\PostController;

Route::get('/posts', [PostController::class, 'index']);   // GET all
Route::post('/posts', [PostController::class, 'store']);  // POST new
Route::get('/posts/{id}', [PostController::class, 'show']); // GET single
