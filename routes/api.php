<?php

use App\Http\Controllers\Api\AnnouncementsController;
use App\Http\Controllers\Api\Autocomplete\PublicationsController as AutocompletePublicationsController;
use App\Http\Controllers\Api\Autocomplete\UsersController as AutocompleteUsersController;
use App\Http\Controllers\Api\CancelledImportsController;
use App\Http\Controllers\Api\ExportsController;
use App\Http\Controllers\Api\GroupTaxaController;
use App\Http\Controllers\Api\My\ProfileController;
use App\Http\Controllers\Api\My\ReadNotificationsBatchController;
use App\Http\Controllers\Api\My\UnreadNotificationsController;
use App\Http\Controllers\Api\ReadAnnouncementsController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\SynonymsController;
use App\Http\Controllers\Api\TaxaController;
use App\Http\Controllers\Api\TaxonExportsController;
use App\Http\Controllers\Api\TaxonImportsController;
use App\Http\Controllers\Api\TaxonomyController;
use App\Http\Controllers\Api\TaxonPublicPhotosController;
use App\Http\Controllers\Api\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [RegisterController::class, 'store']);

Route::get('groups/{group}/taxa', [GroupTaxaController::class, 'index'])
    ->name('api.groups.taxa.index');

Route::get('taxa/{taxon}/public-photos', [TaxonPublicPhotosController::class, 'index'])
    ->name('api.taxa.public-photos.index');

Route::post('taxonomy/check', [TaxonomyController::class, 'check'])
    ->name('api.taxonomy.check');

Route::post('taxonomy/connect', [TaxonomyController::class, 'connect'])
    ->name('api.taxonomy.connect');

Route::post('taxonomy/disconnect', [TaxonomyController::class, 'disconnect'])
    ->name('api.taxonomy.disconnect');

Route::post('taxonomy/search', [TaxonomyController::class, 'search'])
    ->name('api.taxonomy.search');

Route::middleware(['auth:api', 'verified'])->group(function () {
    // Taxa
    Route::get('taxa', [TaxaController::class, 'index'])
        ->withoutMiddleware('verified')
        ->name('api.taxa.index');

    Route::post('taxa', [TaxaController::class, 'store'])
        ->name('api.taxa.store');

    Route::get('taxa/{taxon}', [TaxaController::class, 'show'])
        ->withoutMiddleware('verified')
        ->name('api.taxa.show');

    Route::put('taxa/{taxon}', [TaxaController::class, 'update'])
        ->middleware('can:update,taxon')
        ->name('api.taxa.update');

    Route::delete('taxa/{taxon}', [TaxaController::class, 'destroy'])
        ->middleware('can:delete,taxon')
        ->name('api.taxa.destroy');

    Route::post('cancelled-imports', [CancelledImportsController::class, 'store'])
        ->name('api.cancelled-imports.store');

    // Users
    Route::get('users', [UsersController::class, 'index'])
        ->middleware('can:list,App\User')
        ->name('api.users.index');

    Route::get('users/{user}', [UsersController::class, 'show'])
        ->middleware('can:view,user')
        ->name('api.users.show');

    Route::put('users/{user}', [UsersController::class, 'update'])
        ->middleware('can:update,user')
        ->name('api.users.update');

    Route::delete('users/{user}', [UsersController::class, 'destroy'])
        ->middleware('can:delete,user')
        ->name('api.users.destroy');

    // Taxa export
    Route::post('taxon-exports', [TaxonExportsController::class, 'store'])
        ->name('api.taxon-exports.store');

    Route::get('exports/{export}', [ExportsController::class, 'show'])
        ->name('api.exports.show');

    // Taxa imports
    Route::post('taxon-imports', [TaxonImportsController::class, 'store'])
        ->name('api.taxon-imports.store');

    Route::get('taxon-imports/{import}', [TaxonImportsController::class, 'show'])
        ->name('api.taxon-imports.show');

    Route::get('taxon-imports/{import}/errors', [TaxonImportsController::class, 'errors'])
        ->name('api.taxon-imports.errors');

    // Announcements
    Route::get('announcements', [AnnouncementsController::class, 'index'])
        ->withoutMiddleware('verified')
        ->name('api.announcements.index');

    Route::get('announcements/{announcement}', [AnnouncementsController::class, 'show'])
        ->withoutMiddleware('verified')
        ->name('api.announcements.show');

    Route::post('announcements', [AnnouncementsController::class, 'store'])
        ->middleware('can:create,App\Announcement')
        ->name('api.announcements.store');

    Route::put('announcements/{announcement}', [AnnouncementsController::class, 'update'])
        ->middleware('can:update,announcement')
        ->name('api.announcements.update');

    Route::delete('announcements/{announcement}', [AnnouncementsController::class, 'destroy'])
        ->middleware('can:delete,announcement')
        ->name('api.announcements.destroy');

    Route::post('read-announcements', [ReadAnnouncementsController::class, 'store'])
        ->withoutMiddleware('verified')
        ->name('api.read-announcements.store');

    // Synonyms
    Route::post('synonyms', [SynonymsController::class, 'store'])
        ->name('api.synonyms.create');

    Route::put('synonyms/{synonym}', [SynonymsController::class, 'update'])
        ->name('api.synonyms.update');

    Route::delete('synonyms/{synonym}', [SynonymsController::class, 'destroy'])
        ->name('api.synonyms.destroy');

    // My
    Route::prefix('my')->group(function () {
        Route::get('profile', [ProfileController::class, 'show'])
            ->withoutMiddleware('verified')
            ->name('api.my.profile.show');

        Route::post('read-notifications/batch', [ReadNotificationsBatchController::class, 'store'])
            ->withoutMiddleware('verified')
            ->name('api.my.read-notifications-batch.store');

        Route::get('unread-notifications', [UnreadNotificationsController::class, 'index'])
            ->withoutMiddleware('verified')
            ->name('api.my.unread-notifications.index');
    });

    Route::prefix('curator')->group(function () {
    });

    Route::prefix('autocomplete')->group(function () {
        Route::get('users', [AutocompleteUsersController::class, 'index'])
            ->middleware('role:admin,curator')
            ->name('api.autocomplete.users.index');

        Route::get('publications', [AutocompletePublicationsController::class, 'index'])
            ->middleware('role:admin,curator')
            ->name('api.autocomplete.publications.index');
    });
});
