<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminContentController;
use App\Http\Controllers\Api\AdminAuditLogController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AdminLeadController;
use App\Http\Controllers\Api\AdminMediaController;
use App\Http\Controllers\Api\AdminNavigationController;
use App\Http\Controllers\Api\AdminSettingsController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\PublicContentController;
use App\Http\Controllers\Api\PublicSettingsController;
use App\Http\Controllers\Api\QuoteRequestController;

Route::get('/health', static fn () => response()->json([
    'status' => 'ok',
    'service' => 'northstar-digital-api',
]));

Route::prefix('content')->group(function () {
    Route::get('/settings/seo', [PublicSettingsController::class, 'seo']);
    Route::get('/services', [PublicContentController::class, 'services']);
    Route::get('/services/{slug}', [PublicContentController::class, 'service']);
    Route::get('/service-categories', [PublicContentController::class, 'serviceCategories']);
    Route::get('/packages', [PublicContentController::class, 'packages']);
    Route::get('/portfolio', [PublicContentController::class, 'portfolio']);
    Route::get('/portfolio/{slug}', [PublicContentController::class, 'project']);
    Route::get('/case-studies', [PublicContentController::class, 'caseStudies']);
    Route::get('/case-studies/{slug}', [PublicContentController::class, 'caseStudy']);
    Route::get('/blog-categories', [PublicContentController::class, 'blogCategories']);
    Route::get('/blog', [PublicContentController::class, 'blog']);
    Route::get('/blog/{slug}', [PublicContentController::class, 'post']);
    Route::get('/faqs', [PublicContentController::class, 'faqs']);
    Route::get('/testimonials', [PublicContentController::class, 'testimonials']);
});

Route::post('/leads', [LeadController::class, 'store'])->middleware('throttle:public-leads');
Route::post('/quote-requests', [QuoteRequestController::class, 'store'])->middleware('throttle:public-quotes');

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:admin-login');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:admin-login');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:admin-login');
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/dashboard', AdminDashboardController::class);
    Route::get('/leads', [AdminLeadController::class, 'leads'])->middleware('permission:leads.read');
    Route::get('/lead-staff', [AdminLeadController::class, 'staff'])->middleware('permission:leads.read');
    Route::get('/leads/export', [AdminLeadController::class, 'export'])->middleware('permission:leads.read');
    Route::post('/leads/retention/purge', [AdminLeadController::class, 'purgeArchived'])->middleware('permission:leads.manage');
    Route::get('/leads/{lead}', [AdminLeadController::class, 'lead'])->middleware('permission:leads.read');
    Route::patch('/leads/{lead}', [AdminLeadController::class, 'updateLead'])->middleware('permission:leads.manage');
    Route::post('/leads/{lead}/notes', [AdminLeadController::class, 'addLeadNote'])->middleware('permission:leads.manage');
    Route::post('/leads/{lead}/archive', [AdminLeadController::class, 'archiveLead'])->middleware('permission:leads.manage');
    Route::get('/quote-requests', [AdminLeadController::class, 'quotes'])->middleware('permission:leads.read');
    Route::get('/quote-requests/{quote}', [AdminLeadController::class, 'quote'])->middleware('permission:leads.read');
    Route::patch('/quote-requests/{quote}', [AdminLeadController::class, 'updateQuote'])->middleware('permission:leads.manage');
    Route::post('/quote-requests/{quote}/archive', [AdminLeadController::class, 'archiveQuote'])->middleware('permission:leads.manage');
    Route::get('/settings', [AdminSettingsController::class, 'index'])->middleware('permission:settings.manage');
    Route::get('/settings/{key}', [AdminSettingsController::class, 'show'])->middleware('permission:settings.manage');
    Route::put('/settings/{key}', [AdminSettingsController::class, 'update'])->middleware('permission:settings.manage');
    Route::get('/navigation', [AdminNavigationController::class, 'index'])->middleware('permission:navigation.manage');
    Route::post('/navigation', [AdminNavigationController::class, 'store'])->middleware('permission:navigation.manage');
    Route::put('/navigation/{navigation}', [AdminNavigationController::class, 'update'])->middleware('permission:navigation.manage');
    Route::delete('/navigation/{navigation}', [AdminNavigationController::class, 'destroy'])->middleware('permission:navigation.manage');
    Route::get('/media', [AdminMediaController::class, 'index'])->middleware('permission:media.manage');
    Route::post('/media', [AdminMediaController::class, 'store'])->middleware('permission:media.manage');
    Route::patch('/media/{media}', [AdminMediaController::class, 'update'])->middleware('permission:media.manage');
    Route::delete('/media/{media}', [AdminMediaController::class, 'destroy'])->middleware('permission:media.manage');
    Route::get('/users', [AdminUserController::class, 'index'])->middleware('permission:users.manage');
    Route::get('/roles', [AdminUserController::class, 'roles'])->middleware('permission:users.manage');
    Route::post('/users', [AdminUserController::class, 'store'])->middleware('permission:users.manage');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->middleware('permission:users.manage');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->middleware('permission:users.manage');
    Route::get('/audit-logs', AdminAuditLogController::class)->middleware('permission:audit.read');
    Route::get('/content/{resource}', [AdminContentController::class, 'index']);
    Route::post('/content/{resource}', [AdminContentController::class, 'store']);
    Route::get('/content/{resource}/{content}', [AdminContentController::class, 'show']);
    Route::match(['put', 'patch'], '/content/{resource}/{content}', [AdminContentController::class, 'update']);
    Route::post('/content/{resource}/{content}/publish', [AdminContentController::class, 'publish']);
    Route::post('/content/{resource}/{content}/unpublish', [AdminContentController::class, 'unpublish']);
    Route::delete('/content/{resource}/{content}', [AdminContentController::class, 'destroy']);
});
