<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FetchValuesController;
use App\Http\Controllers\LeadController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\TemplateController;

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
    Route::get('/leads/{category}', [LeadController::class, 'index'])
        ->where('category', 'untouched|hot|warm|cold|inquiry|admission-in-process|admission-done|scrap|non-qualified|non-contactable|follow-up')
        ->name('leads.index.category');

    Route::post('/leads/reassign', [LeadController::class, 'reassign'])->name('leads.reassign');
    Route::post('/leads/recommendation', [LeadController::class, 'recommendation'])->name('leads.recommendation');
    Route::post('/leads/add-applicationId', [LeadController::class, 'addApplicationId'])->name('leads.addApplicationId');

    // Filter API
    Route::post('/fetch/distinct-column', [FetchValuesController::class, 'distinctColumnValues'])->name('distinctColumnValues');
    Route::get('/fetch-users', [FetchValuesController::class, 'fetchAllUsers'])->name('fetchAllUsers');
    Route::post('/fetch/distinct-title', [FetchValuesController::class, 'distinctTitleValues'])->name('distinctTitleValues');
    Route::post('/fetch/filtered-values', [FetchValuesController::class, 'filteredValues'])->name('filteredValues');
    Route::post('/clear-filter', [FetchValuesController::class, 'clearFilter'])->name('clearFilter');
    Route::get('/get-designations/{department}', [FetchValuesController::class, 'getDesignations'])->name('getDesignations');
    Route::get('/get-branches/{zone}', [FetchValuesController::class, 'getBranches'])->name('getBranches');

    // fetch API
    Route::get('/fetch-privileges-data', [FetchValuesController::class, 'fetchPrivilegesData'])->name('fetchPrivilegesData');
    Route::get('/fetch-sidebar-and-buttons', [FetchValuesController::class, 'fetchSidebarMenusActionButtons'])->name('fetchSidebarMenusActionButtons');
     Route::match(['get', 'post'],'/fetch-active-lead-sources', [FetchValuesController::class, 'fetchActiveLeadSources'])->name('fetchActiveLeadSources');

    // User-groups
    Route::get('/profile', [DashboardController::class, 'leadDashboard'])->name('profile');

    Route::match(['get', 'post'], '/user-groups', [UserController::class, 'userGroups'])->name('user.groups');
    Route::match(['get', 'post'], '/fetch-zones', [UserController::class, 'fetchZones'])->name('getZones');
    Route::match(['get', 'post'], '/fetch-counselors', [UserController::class, 'fetchCounselors'])->name('getCounselors');
    Route::match(['get', 'post'], '/store-groups', [UserController::class, 'storeGroups'])->name('storeGroups');
    Route::post('/fetch-group-data', [UserController::class, 'fetchGroupData'])->name('fetchGroupData');
    Route::post('/update-group', [UserController::class, 'updateGroup'])->name('updateGroup');
    Route::get('/view-connected-teams/{encoded}', [UserController::class, 'viewConnectedTeams'])->name('user.view_teams');
    Route::get('/teams-mapping/{encoded}', [UserController::class, 'teamMapping'])->name('user.team_mapping');
    Route::post('/teams-mapping-updation', [UserController::class, 'teamMappingUpdation'])->name('teamMappingUpdation');

    //user-teams
    Route::match(['get', 'post'], '/user-teams', [UserController::class, 'userTeams'])->name('user.teams');
    Route::match(['get', 'post'], '/fetch-all-counselors', [UserController::class, 'fetchAllCounselors'])->name('fetchAllCounselors');
    Route::match(['get', 'post'], '/store-teams', [UserController::class, 'storeTeams'])->name('storeTeams');
    // Route::post('/fetch-team-data', [UserController::class, 'fetchTeamData'])->name('fetchTeamData');
    // Route::post('/update-team', [UserController::class, 'updateTeam'])->name('updateTeam');
    Route::get('/view-connected-members/{encoded}', [UserController::class, 'viewConnectedUsers'])->name('user.view_members');
    Route::get('/users-mapping/{encoded}', [UserController::class, 'UsersMapping'])->name('user.user_mapping');
    Route::get('/users/search', [UserController::class, 'searchUsers'])->name('users.search');
    Route::post('/users/add-to-team', [UserController::class, 'addUserToTeam'])->name('users.addToTeam');
    Route::post('/users/remove-from-team', [UserController::class, 'removeUserFromTeam'])->name('users.removeFromTeam');

    Route::match(['get', 'post'], '/users', [UserController::class, 'users'])->name('user.users');
    Route::match(['get', 'post'], '/create-user', [UserController::class, 'createUser'])->name('user.createUser');
    Route::post('/store-user', [UserController::class, 'storeUser'])->name('storeUser');
    Route::match(['get', 'post'], '/user-privileges', [UserController::class, 'userPrivileges'])->name('user.privileges');
    Route::match(['get', 'post'], '/add-sidebar-menus', [UserController::class, 'addSidebarMenus'])->name('user.add_sidebar_menus');
    Route::post('/store-sidebar-menu', [UserController::class, 'storeSidebarMenus'])->name('storeSidebarMenus');
    Route::match(['get', 'post'], '/add-action-buttons', [UserController::class, 'addActionButtons'])->name('user.add_action_buttons');
    Route::post('/store-action-button', [UserController::class, 'storeActionButton'])->name('storeActionButton');
    Route::match(['get', 'post'], '/create-user-privileges', [UserController::class, 'createUserPrivileges'])->name('user.create_user_privileges');
    Route::post('/store-user-privilege', [UserController::class, 'storeUserPrivilege'])->name('storeUserPrivilege');
    Route::match(['get', 'post'], '/active-lead-sources', [UserController::class, 'activeLeadSources'])->name('user.active_lead_sources');
    Route::match(['get', 'post'], '/manage-lead-sources', [UserController::class, 'manageLeadSources'])->name('user.manage_lead_sources');
    Route::post('/update-lead-sources', [UserController::class, 'updateLeadSources'])->name('updateLeadSources');
    Route::match(['get', 'post'], '/manage-team-memebers/{encoded}', [UserController::class, 'manageTeamMembers'])->name('user.manage_team_members');
    Route::match(['get', 'post'],'/fetch-team-info', [UserController::class, 'fetchteamInfo'])->name('fetchteamInfo');
    Route::post('/update-team-info', [UserController::class, 'updateTeamInfo'])->name('updateTeamInfo');


    Route::match(['get', 'post'], '/creative-templates', [TemplateController::class, 'CreativeTemplateList'])->name('templates.list_creativeTemplate');
    Route::match(['get', 'post'], '/load-creative-templates', [TemplateController::class, 'loadCreativeTemplates'])->name('loadCreativeTemplates');
    Route::match(['get', 'post'], '/build-creative-templates', [TemplateController::class, 'buildCreativeTemplate'])->name('templates.build_creativeTemplate');
    Route::post('/store-creative-template', [TemplateController::class, 'storeCreativeTemplate'])->name('store.creativeTemplate');
    Route::match(['get', 'post'], '/create-creative-image/{id}', [TemplateController::class, 'createCreativeImage'])->name('templates.create_creativeImage');
    Route::delete('/delete-creative-template/{id}', [TemplateController::class, 'deleteCreativeTemplate'])->name('deleteCreativeTemplate');
    Route::post('/update-creative-background', [TemplateController::class, 'updateCreativeBackground'])->name('updateCreativeBackground');
});
