<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FetchValuesController;
use App\Http\Controllers\LeadController;

use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protect multiple routes
Route::group(['middleware' => 'auth'], function () {
    // Dashboards
    Route::get('/dashboard', [DashboardController::class, 'leadDashboard'])->name('dashboard');
    Route::get('/lead-analytics', [DashboardController::class, 'leadStats'])->name('leadStats');
    Route::get('/admin-dashboard', [DashboardController::class, 'adminDashboard'])->name('adminDashboard');
    Route::get('/admin-analytics', [DashboardController::class, 'adminStats'])->name('adminStats');
    Route::get('/collection-dashboard', [DashboardController::class, 'collectionDashboard'])->name('collectionDashboard');
    Route::get('/collection-analytics', [DashboardController::class, 'collectionStats'])->name('collectionStats');
    Route::get('/counsellor-dashboard', [DashboardController::class, 'counsellorDashboard'])->name('counsellorDashboard');
    Route::get('/counsellor-analytics', [DashboardController::class, 'counsellorStats'])->name('counsellorStats');
    Route::get('/communication-dashboard', [DashboardController::class, 'communicationDashboard'])->name('communicationDashboard');
    Route::get('/communication-analytics', [DashboardController::class, 'communicationStats'])->name('communicationStats');
    Route::get('/marketing-dashboard', [DashboardController::class, 'marketingDashboard'])->name('marketingDashboard');
    Route::get('/marketing-analytics', [DashboardController::class, 'marketingStats'])->name('marketingStats');

    // Lead Manager
    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');              // all
    Route::get('/leads/{category}', [LeadController::class, 'index']);
    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/{category}',       [LeadController::class, 'index'])
        ->where('category', 'untouched|hot|warm|cold|inquiry|admission-in-process|admission-done|scrap|non-qualified|non-contactable|follow-up')
        ->name('leads.index.category');

    Route::post('/leads/reassign', [LeadController::class, 'reassign'])->name('leads.reassign');
    Route::post('/leads/recommendation', [LeadController::class, 'recommendation'])->name('leads.recommendation');

    // fetch API
    Route::post('/fetch/distinct-column', [FetchValuesController::class, 'distinctColumnValues'])->name('distinctColumnValues');
    
    Route::get('/profile', [DashboardController::class, 'leadDashboard'])->name('profile');

    // User
    Route::match(['get', 'post'], '/user-groups', [UserController::class, 'userGroups'])->name('user.groups');
    Route::match(['get', 'post'], '/fetch-zones', [UserController::class, 'fetchZones'])->name('getZones');
    Route::match(['get', 'post'], '/fetch-counselors', [UserController::class, 'fetchCounselors'])->name('getCounselors');
    Route::match(['get', 'post'], '/store-groups', [UserController::class, 'storeGroups'])->name('storeGroups');
    Route::post('/fetch-group-data', [UserController::class, 'fetchGroupData'])->name('fetchGroupData');
    Route::post('/update-group', [UserController::class, 'updateGroup'])->name('updateGroup');
    Route::get('/view-connected-teams/{encoded}', [UserController::class, 'viewConnectedTeams'])->name('user.view_teams');
    Route::get('/teams-mapping/{encoded}', [UserController::class, 'teamMapping'])->name('user.team_mapping');

});
