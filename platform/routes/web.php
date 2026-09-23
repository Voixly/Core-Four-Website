<?php

use App\Http\Controllers\Admin\AdsController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JobOpsController;
use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Account\InviteController as AccountInviteController;
use App\Http\Controllers\Account\JobController as AccountJobController;
use App\Http\Controllers\Account\LoginController as AccountLoginController;
use App\Http\Controllers\Admin\GuideController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Public\ChatWidgetController;
use App\Http\Controllers\Public\LeadFormController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [PageController::class, 'sitemap']);
Route::get('/robots.txt', [PageController::class, 'robots']);
Route::get('/llms.txt', [PageController::class, 'llms']);

Route::permanentRedirect('/home/', '/');
Route::permanentRedirect('/home', '/');
Route::permanentRedirect('/about-us/', '/about-core-four-roofing/');
Route::permanentRedirect('/about-us', '/about-core-four-roofing/');
Route::permanentRedirect('/about/', '/about-core-four-roofing/');
Route::permanentRedirect('/about', '/about-core-four-roofing/');
Route::permanentRedirect('/areas-we-serve/', '/service-areas/');
Route::permanentRedirect('/areas-we-serve', '/service-areas/');
Route::permanentRedirect('/emergency-roof-repair/', '/storm-emergency/');
Route::permanentRedirect('/emergency-roof-repair', '/storm-emergency/');
Route::permanentRedirect('/emergency-roofing/', '/storm-emergency/');
Route::permanentRedirect('/emergency-roofing', '/storm-emergency/');
// Short material URLs the old build used, folded back onto the live structure.
Route::permanentRedirect('/asphalt-shingles/', '/residential-roofing/asphalt-shingles/');
Route::permanentRedirect('/metal-roofing/', '/residential-roofing/metal-roofs/');
Route::permanentRedirect('/synthetic-roofing/', '/residential-roofing/synthetic-roofs/');
Route::permanentRedirect('/stone-coated-steel/', '/residential-roofing/stone-coated-steel/');
Route::permanentRedirect('/tpo-roofing/', '/commercial-roofing/roof-replacement-installation/');
Route::permanentRedirect('/epdm-roofing/', '/commercial-roofing/roof-replacement-installation/');
Route::permanentRedirect('/modified-bitumen/', '/commercial-roofing/roof-replacement-installation/');
Route::permanentRedirect('/built-up-roofing/', '/commercial-roofing/roof-replacement-installation/');
Route::permanentRedirect('/residential-roofing-city-landing-page-template/', '/service-areas/');
Route::permanentRedirect('/commercial-roofing-city-landing-page-template/', '/service-areas/');
Route::permanentRedirect('/sub-page-template/', '/');

Route::get('/', [PageController::class, 'home'])->name('home');

// A layout file in resources/data/pages is a public page and a sitemap URL.
foreach (\App\Support\PageLayout::publishedPaths() as $path) {
    $slug = str_replace('/', '--', trim($path, '/'));
    Route::get($path, [PageController::class, 'page'])
        ->defaults('slug', $slug)
        ->name('page.'.$slug);
}
Route::get('/reviews/', [ReviewController::class, 'show'])->name('reviews.landing');
Route::post('/reviews/', [ReviewController::class, 'store'])->middleware('throttle:8,1')->name('reviews.store');
Route::get('/reviews/{token}/', [ReviewController::class, 'show'])->name('reviews.show');
Route::post('/reviews/{token}/', [ReviewController::class, 'store'])->middleware('throttle:8,1')->name('reviews.store.token');
Route::get('/reviews/{token}/google/', [ReviewController::class, 'google'])->name('reviews.google');
Route::get('/reviews/{token}/yelp/', [ReviewController::class, 'yelp'])->name('reviews.yelp');
Route::permanentRedirect('/leave-a-review/', '/reviews/');
Route::permanentRedirect('/leave-a-review', '/reviews/');

Route::get('/careers/', [PageController::class, 'careers'])->name('careers');
Route::post('/careers/', [LeadFormController::class, 'hiring'])->middleware('throttle:8,1')->name('careers.store');
Route::get('/careers/thank-you/', [PageController::class, 'careersThanks'])->name('careers.thanks');
Route::permanentRedirect('/hiring/', '/careers/');
Route::permanentRedirect('/hiring', '/careers/');

Route::get('/thank-you/', [PageController::class, 'thanks'])->name('thanks');
Route::get('/privacy-policy/', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms/', [PageController::class, 'terms'])->name('terms');
Route::get('/guides/', [PageController::class, 'guides'])->name('guides');
Route::get('/guides/{slug}/', [PageController::class, 'guide'])->name('guides.show');

Route::get('/residential-roofing-in-tx/', [PageController::class, 'city'])->defaults('slug', 'tx');
Route::get('/commercial-roofing-in-tx/', [PageController::class, 'city'])->defaults('slug', 'tx');
Route::get('/residential-roofing-in-{slug}-tx/', [PageController::class, 'city'])->where('slug', '[a-z0-9-]+')->name('city.residential');
Route::get('/commercial-roofing-in-{slug}-tx/', [PageController::class, 'city'])->where('slug', '[a-z0-9-]+')->name('city.commercial');

Route::post('/leads', [LeadFormController::class, 'store'])->middleware('throttle:8,1')->name('leads.store');
Route::post('/guides/{slug}/download', [LeadFormController::class, 'download'])->middleware('throttle:6,1')->name('guides.download');

Route::prefix('chat')->middleware('throttle:40,1')->group(function () {
    Route::post('/start', [ChatWidgetController::class, 'start'])->name('chat.start');
    Route::get('/poll', [ChatWidgetController::class, 'poll'])->name('chat.poll');
    Route::post('/send', [ChatWidgetController::class, 'send'])->name('chat.send');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:8,1');
    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])->middleware('throttle:6,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->middleware('throttle:6,1')->name('password.store');

    Route::get('/account/login/', [AccountLoginController::class, 'show'])->name('account.login');
    Route::post('/account/login/', [AccountLoginController::class, 'store'])->middleware('throttle:8,1');
    Route::get('/account/invite/{token}/', [AccountInviteController::class, 'show'])->name('account.invite');
    Route::post('/account/invite/{token}/', [AccountInviteController::class, 'store'])->middleware('throttle:8,1')->name('account.invite.accept');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');
Route::post('/account/logout/', [AccountLoginController::class, 'destroy'])->middleware('auth')->name('account.logout');

Route::prefix('account')->name('account.')->middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/', [AccountJobController::class, 'index'])->name('home');
    Route::get('/jobs/{job}/', [AccountJobController::class, 'show'])->name('jobs.show');
    Route::post('/jobs/{job}/documents/', [AccountJobController::class, 'upload'])->name('jobs.documents');
    Route::get('/jobs/{job}/documents/{document}/', [AccountJobController::class, 'download'])->name('jobs.download');
    Route::post('/jobs/{job}/quotes/{quote}/accept/', [AccountJobController::class, 'acceptQuote'])->name('jobs.quotes.accept');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,agency,owner,staff'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::patch('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::post('/leads/{lead}/notes', [LeadController::class, 'note'])->name('leads.note');
    Route::post('/leads/{lead}/jobs', [AdminJobController::class, 'store'])->name('leads.jobs.store');

    Route::get('/schedule', CalendarController::class)->name('schedule');

    Route::get('/jobs', [AdminJobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/create', [AdminJobController::class, 'create'])->name('jobs.create');
    Route::post('/jobs', [AdminJobController::class, 'storeStandalone'])->name('jobs.store');
    Route::get('/jobs/{job}', [AdminJobController::class, 'show'])->name('jobs.show');
    Route::patch('/jobs/{job}', [AdminJobController::class, 'update'])->name('jobs.update');
    Route::post('/jobs/{job}/move', [AdminJobController::class, 'move'])->name('jobs.move');
    Route::post('/jobs/{job}/invite', [AdminJobController::class, 'invite'])->name('jobs.invite');
    Route::post('/jobs/{job}/requests', [AdminJobController::class, 'requestDocument'])->name('jobs.requests');
    Route::post('/jobs/{job}/documents', [AdminJobController::class, 'upload'])->name('jobs.documents');
    Route::get('/jobs/{job}/documents/{document}', [AdminJobController::class, 'download'])->name('jobs.download');
    Route::post('/jobs/{job}/notes', [JobOpsController::class, 'note'])->name('jobs.notes');
    Route::post('/jobs/{job}/appointments', [JobOpsController::class, 'storeAppointment'])->name('jobs.appointments.store');
    Route::patch('/jobs/{job}/appointments/{appointment}', [JobOpsController::class, 'updateAppointment'])->name('jobs.appointments.update');
    Route::post('/jobs/{job}/quotes', [JobOpsController::class, 'storeQuote'])->name('jobs.quotes.store');
    Route::post('/jobs/{job}/quotes/{quote}/items', [JobOpsController::class, 'addQuoteItem'])->name('jobs.quotes.items');
    Route::post('/jobs/{job}/quotes/{quote}/status', [JobOpsController::class, 'quoteStatus'])->name('jobs.quotes.status');
    Route::post('/jobs/{job}/change-orders', [JobOpsController::class, 'storeChangeOrder'])->name('jobs.changes.store');
    Route::post('/jobs/{job}/change-orders/{order}/status', [JobOpsController::class, 'changeOrderStatus'])->name('jobs.changes.status');
    Route::post('/jobs/{job}/invoices', [JobOpsController::class, 'storeInvoice'])->name('jobs.invoices.store');
    Route::post('/jobs/{job}/invoices/{invoice}/status', [JobOpsController::class, 'invoiceStatus'])->name('jobs.invoices.status');
    Route::post('/jobs/{job}/materials', [JobOpsController::class, 'storeMaterial'])->name('jobs.materials.store');
    Route::post('/jobs/{job}/materials/{material}/status', [JobOpsController::class, 'materialStatus'])->name('jobs.materials.status');
    Route::post('/jobs/{job}/tasks', [JobOpsController::class, 'storeTask'])->name('jobs.tasks.store');
    Route::post('/jobs/{job}/tasks/{task}/toggle', [JobOpsController::class, 'toggleTask'])->name('jobs.tasks.toggle');
    Route::post('/jobs/{job}/warranties', [JobOpsController::class, 'storeWarranty'])->name('jobs.warranties.store');
    Route::post('/jobs/{job}/costs', [JobOpsController::class, 'storeCost'])->name('jobs.costs.store');

    Route::get('/password', [UserController::class, 'editOwn'])->name('password.edit');
    Route::put('/password', [UserController::class, 'updateOwn'])->name('password.update');

    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews', [AdminReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}', [AdminReviewController::class, 'show'])->name('reviews.show');
    Route::patch('/reviews/{review}', [AdminReviewController::class, 'update'])->name('reviews.update');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::get('/chat/{conversation}/poll', [ChatController::class, 'poll'])->name('chat.poll');
    Route::post('/chat/{conversation}/reply', [ChatController::class, 'reply'])->name('chat.reply');
    Route::post('/chat/{conversation}/close', [ChatController::class, 'close'])->name('chat.close');
    Route::post('/chat/{conversation}/convert', [ChatController::class, 'convert'])->name('chat.convert');

    Route::middleware('role:admin,agency,owner')->group(function () {
        Route::get('/ads', [AdsController::class, 'index'])->name('ads');
        Route::get('/ads/pdf', [AdsController::class, 'pdf'])->name('ads.pdf');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{slug}/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
        Route::get('/reports/{slug}', [ReportController::class, 'show'])->name('reports.show');

        Route::get('/email', [EmailController::class, 'index'])->name('email.index');
        Route::get('/email/steps/{step}', [EmailController::class, 'edit'])->name('email.edit');
        Route::patch('/email/steps/{step}', [EmailController::class, 'update'])->name('email.update');
        Route::get('/email/steps/{step}/preview', [EmailController::class, 'preview'])->name('email.preview');
        Route::post('/email/steps/{step}/test', [EmailController::class, 'test'])->name('email.test');

        Route::get('/guides', [GuideController::class, 'index'])->name('guides.index');
        Route::post('/guides', [GuideController::class, 'store'])->name('guides.store');
        Route::post('/guides/{guide}/toggle', [GuideController::class, 'toggle'])->name('guides.toggle');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/password', [UserController::class, 'password'])->name('users.password');
        Route::post('/users/{user}/invite', [UserController::class, 'invite'])->name('users.invite');
        Route::post('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::middleware('role:admin,agency')->group(function () {
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});

Route::get('/blog/{slug}/', function (string $slug) {
    return redirect('/'.$slug.'/', 301);
})->where('slug', '[a-z0-9-]+');

Route::get('/{slug}/', [PageController::class, 'post'])
    ->where('slug', '[a-z0-9-]+')
    ->name('blog.show');
