<?php

use App\Http\Controllers\AboutPagesController;
use App\Http\Controllers\Admin\AnnouncementsController as AdminAnnouncementsController;
use App\Http\Controllers\Admin\TaxaController as AdminTaxaController;
use App\Http\Controllers\Admin\TaxaImportController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\AnnouncementsController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Contributor\DashboardController;
use App\Http\Controllers\ExportDownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Preferences\AccountPreferencesController;
use App\Http\Controllers\Preferences\GeneralPreferencesController;
use App\Http\Controllers\Preferences\LicensePreferencesController;
use App\Http\Controllers\Preferences\NotificationsPreferencesController;
use App\Http\Controllers\TaxaController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');

Route::get('exports/{export}/download', ExportDownloadController::class)
    ->middleware(['auth', 'verified'])
    ->name('export-download');

Route::prefix(LaravelLocalization::setLocale())->middleware([
    'localeCookieRedirect', 'localizationRedirect', 'localeViewPath', 'localizationPreferenceUpdate',
])->group(function () {
    Route::auth(['verify' => false, 'confirm' => false]);
    Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('taxa/{taxon}', [TaxaController::class, 'show'])->name('taxa.show');
    //Route::get('groups', [GroupsController::class, 'index'])->name('groups.index');
    //Route::get('groups/{group}/species/{species}', [GroupSpeciesController::class, 'show'])->name('groups.species.show');
    //Route::get('groups/{group}/species', [GroupSpeciesController::class, 'index'])->name('groups.species.index');

    // About pages
    Route::view('pages/about/about-project', 'pages.about.about-project')->name('pages.about.about-project');
    Route::view('pages/about/project-team', 'pages.about.project-team')->name('pages.about.project-team');
    Route::view('pages/about/organisations', 'pages.about.organisations')->name('pages.about.organisations');
    Route::get('pages/about/local-community', [AboutPagesController::class, 'localCommunity'])->name('pages.about.local-community');
    Route::view('pages/about/biodiversity-data', 'pages.about.biodiversity-data')->name('pages.about.biodiversity-data');
    Route::view('pages/about/development-supporters', 'pages.about.development-supporters')->name('pages.about.development-supporters');
    Route::get('pages/about/stats', [AboutPagesController::class, 'stats'])->name('pages.about.stats');

    // Legal
    Route::view('pages/privacy-policy', 'pages.privacy-policy')->name('pages.privacy-policy');

    // Licenses
    Route::view('licenses', 'licenses.index')->name('licenses.index');
    Route::view('licenses/partially-open-data-license', 'licenses.partially-open-data-license')->name('licenses.partially-open-data-license');
    Route::view('licenses/temporarily-closed-data-license', 'licenses.temporarily-closed-data-license')->name('licenses.temporarily-closed-data-license');
    Route::view('licenses/closed-data-license', 'licenses.closed-data-license')->name('licenses.closed-data-license');
    Route::view('licenses/partially-open-image-license', 'licenses.partially-open-image-license')->name('licenses.partially-open-image-license');
    Route::view('licenses/closed-image-license', 'licenses.closed-image-license')->name('licenses.closed-image-license');

    Route::get('announcements', [AnnouncementsController::class, 'index'])->name('announcements.index');
    Route::get('announcements/{announcement}', [AnnouncementsController::class, 'show'])->name('announcements.show');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::redirect('/preferences', '/preferences/general')->name('preferences.index');

        Route::prefix('preferences')->name('preferences.')->group(function () {
            Route::get('general', [GeneralPreferencesController::class, 'index'])->name('general');
            Route::patch('general', [GeneralPreferencesController::class, 'update']);

            Route::get('account', [AccountPreferencesController::class, 'index'])->name('account');
            Route::patch('account/password', [AccountPreferencesController::class, 'changePassword'])->name('account.password');
            Route::delete('account', [AccountPreferencesController::class, 'destroy'])->name('account.delete');

            Route::get('license', [LicensePreferencesController::class, 'index'])->name('license');
            Route::patch('license', [LicensePreferencesController::class, 'update']);

            Route::get('notifications', [NotificationsPreferencesController::class, 'index'])->name('notifications');
            Route::patch('notifications', [NotificationsPreferencesController::class, 'update']);
        });

        Route::prefix('contributor')->name('contributor.')->group(function () {
            Route::get('/', [DashboardController::class, 'index'])
                ->name('index');
        });

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('taxa', [AdminTaxaController::class, 'index'])
                ->middleware('role:admin,expert')
                ->name('taxa.index');

            Route::get('taxa/{taxon}/edit', [AdminTaxaController::class, 'edit'])
                ->middleware('can:update,taxon')
                ->name('taxa.edit');

            Route::get('taxa/new', [AdminTaxaController::class, 'create'])
                ->middleware('role:admin,expert')
                ->name('taxa.create');

            Route::get('taxa/import', [TaxaImportController::class, 'index'])
                ->name('taxa-import.index');

            Route::view('taxa/import/guide', 'admin.taxon-import.guide')
                ->name('taxa-import.guide');

            Route::get('users', [UsersController::class, 'index'])
                ->middleware('can:list,App\User')
                ->name('users.index');

            Route::get('users/{user}/edit', [UsersController::class, 'edit'])
                ->middleware('can:update,user')
                ->name('users.edit');

            Route::put('users/{user}', [UsersController::class, 'update'])
                ->middleware('can:update,user')
                ->name('users.update');

            Route::get('announcements', [AdminAnnouncementsController::class, 'index'])
                ->middleware('can:list,App\Announcement')
                ->name('announcements.index');

            Route::get('announcements/new', [AdminAnnouncementsController::class, 'create'])
                ->middleware('can:create,App\Announcement')
                ->name('announcements.create');

            Route::get('announcements/{announcement}/edit', [AdminAnnouncementsController::class, 'edit'])
                ->middleware('can:update,announcement')
                ->name('announcements.edit');
        });
    });
});
