<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsUser;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\ProjectController; // ⬅️ IMPORT CORRECT
use App\Http\Controllers\SeoAnalysisController;
use App\Http\Controllers\WhoisController;
use App\Http\Controllers\Admin\SeoContentController;
use App\Http\Controllers\User\SeoHistoryController;

Route::get('/', function () {
    return view('welcome');
});

/////// Only for user route
Route::middleware(['auth', IsUser::class])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
/////// End Only for user route

/////// Only for admin route
Route::prefix('admin')->middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.index');
    })->name('admin.dashboard');

    Route::get('/admin/logout', [AdminController::class, 'AdminLogout'])->name('admin.logout');
    Route::get('/admin/profile', [AdminController::class, 'AdminProfile'])->name('admin.profile');
    Route::post('/admin/profile/store', [AdminController::class, 'AdminProfileStore'])->name('admin.profile.store');
    Route::post('/admin/change/password', [AdminController::class, 'AdminChangePassword'])->name('admin.change.password');
    Route::post('/admin/password/update', [AdminController::class, 'AdminPasswordUpdate'])->name('admin.password.update');

    Route::controller(PlanController::class)->group(function () {
        Route::get('/all/plans', 'AllPlans')->name('all.plans');
        Route::get('/add/plans', 'AddPlans')->name('add.plans');
        Route::post('/store/plans', 'StorePlans')->name('store.plans');
        Route::get('/edit/plans/{id}', 'EditPlans')->name('edit.plans');
        Route::post('/update/plans/{id}', 'UpdatePlans')->name('update.plans');
        Route::get('/delete/plans/{id}', 'DeletePlans')->name('delete.plans');
    });

    Route::controller(ProjectController::class)->group(function () {
        Route::get('/all/projects', 'AllProjects')->name('all.projects');
        Route::delete('/projects/{id}', 'destroy')->name('projects.destroy');
        Route::get('/add/projects', 'AddProject')->name('add.projects');
        Route::post('/store/projects', 'StoreProject')->name('store.projects');
    });

    // Historique SEO côté admin
    Route::get('/seo/history', [SeoHistoryController::class, 'index'])
        ->name('admin.seo.history.index');
    Route::post('/seo/history/reuse/{generation}', [SeoHistoryController::class, 'reuse'])
        ->name('admin.seo.history.reuse');
});
/////// End Only for admin route

// SEO Analysis run
Route::post('/analysis/run', [SeoAnalysisController::class, 'run'])->name('analysis.run');

/////// Authenticated routes (user + admin)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/analysis/new', [SeoAnalysisController::class, 'create'])->name('analysis.create');
    Route::get('/projects/{id}', [SeoAnalysisController::class, 'show'])->name('project.show');
    Route::get('/admin/projects/json', [ProjectController::class, 'getProjectsJson'])->name('projects.json');
    Route::get('/test-whois', [WhoisController::class, 'testWhois']);

    // ✅ SEO Generator routes
    Route::get('/user/projects/seo', [SeoContentController::class, 'page'])
        ->name('user.projects.seo');
    Route::post('/user/projects/seo/generate', [SeoContentController::class, 'generate'])
        ->name('user.projects.seo.generate');

    // ✅ SEO History routes (user)
    Route::get('/seo/history', [SeoHistoryController::class, 'index'])
        ->name('seo.history.index');
    Route::post('/seo/history/reuse/{generation}', [SeoHistoryController::class, 'reuse'])
        ->name('seo.history.reuse');

    // ✅ PageSpeed status route
    Route::get('/seo-analysis/{analysis}/status', function (\App\Models\SeoAnalysis $analysis) {
    
        // 🔥 CACHE : 10 secondes pour éviter les appels trop fréquents
        $cacheKey = "pagespeed_status_{$analysis->id}";
        $cachedResponse = Cache::remember($cacheKey, 10, function () use ($analysis) {
            
            \Log::info('🔍 Statut PageSpeed demandé', [
                'analysis_id' => $analysis->id,
                'desktop_score' => $analysis->pagespeed_desktop_score,
                'mobile_score' => $analysis->pagespeed_mobile_score,
                'page_rank' => $analysis->page_rank,
                'page_rank_global' => $analysis->page_rank_global
            ]);
    
            return [
                'desktop_ready' => !empty($analysis->pagespeed_desktop_score),
                'mobile_ready' => !empty($analysis->pagespeed_mobile_score),
                'desktop_score' => $analysis->pagespeed_desktop_score,
                'mobile_score' => $analysis->pagespeed_mobile_score,
                'desktop_updated' => $analysis->updated_at->toDateTimeString(),
                'page_rank' => $analysis->page_rank,
                'page_rank_global' => $analysis->page_rank_global,
                'cached_at' => now()->toDateTimeString() // Pour debug
            ];
        });
    
        return response()->json($cachedResponse);
    });

    // ✅ PageSpeed data route
    Route::get('/seo-analysis/{analysis}/pagespeed', function (\App\Models\SeoAnalysis $analysis) {
        $strategy = request('strategy', 'desktop');

        $data = [
            'score' => $analysis->{"pagespeed_{$strategy}_score"},
            'metrics' => $analysis->{"pagespeed_{$strategy}_metrics"} ?? [],
            'allScores' => $analysis->{"pagespeed_{$strategy}_scores"} ?? [],
            'audits' => $analysis->{"pagespeed_{$strategy}_audits"} ?? [],
            'formFactor' => $analysis->{"pagespeed_{$strategy}_formFactor"},
        ];

        \Log::info("📡 API PageSpeed - Stratégie: $strategy", [
            'analysis_id' => $analysis->id,
            'score_present' => !is_null($data['score']),
            'metrics_count' => count($data['metrics']),
            'audits_count' => count($data['audits']),
            'scores_count' => count($data['allScores'])
        ]);

        return response()->json($data);
    });

    Route::get('/fix-keywords-now', function() {
        $projects = \App\Models\Project::whereIn('id', [146, 158, 159])->get();
        
        foreach ($projects as $project) {
            $latestAnalysis = $project->seoAnalyses()
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($latestAnalysis && !empty($latestAnalysis->keywords)) {
                $keywordsArray = json_decode($latestAnalysis->keywords, true);
                
                if (is_array($keywordsArray)) {
                    $topKeywords = array_slice(array_keys($keywordsArray), 0, 8);
                    $keywordsString = implode(', ', $topKeywords);
                    
                    $project->target_keywords = $keywordsString;
                    $project->save();
                    
                    echo "✅ <strong>{$project->name}</strong> mis à jour : {$keywordsString}<br>";
                } else {
                    echo "❌ JSON invalide pour {$project->name}<br>";
                }
            } else {
                echo "❌ Aucune analyse trouvée pour {$project->name}<br>";
            }
        }
        
        return "<br><a href='/admin/projects'>Voir la page des projets</a>";
    });

    Route::get('/check-scraper-signature', function() {
        $scraper = new ReflectionClass(App\Services\ScraperService::class);
        $method = $scraper->getMethod('analyze');
        $params = $method->getParameters();
        
        $paramNames = [];
        foreach ($params as $param) {
            $paramNames[] = $param->getName() . ($param->isDefaultValueAvailable() ? ' (optional)' : ' (required)');
        }
        
        return [
            'method_signature' => 'analyze(' . implode(', ', $paramNames) . ')',
            'has_projectId' => count($params) > 1 ? '✅ OUI' : '❌ NON',
            'projectId_position' => count($params) > 1 ? $params[1]->getName() : 'NOT FOUND'
        ];
    });

    Route::delete('/seo/history/{generation}', [SeoHistoryController::class, 'destroy'])
        ->name('seo.history.destroy');

    // 🔥 ROUTES CACHE CORRIGÉES - DÉPLACÉES ICI
    Route::post('/projects/{id}/clear-cache', [SeoAnalysisController::class, 'clearProjectCache'])
        ->name('projects.clear-cache');
        
    // ✅ ROUTE CACHE CORRIGÉE - ProjectController sans "Admin\"
    Route::post('/projects/refresh-cache', [ProjectController::class, 'refreshProjectsCache'])
        ->name('projects.refresh-cache');
        
    Route::get('/projects/{id}/refresh', [SeoAnalysisController::class, 'show'])
        ->name('projects.show.refresh')
        ->defaults('refresh', 'true');
});

// Route de test du cache
Route::get('/test-cache', function() {
    // Test écriture
    Cache::put('test_key', 'Hello Cache!', 60);
    
    // Test lecture
    $value = Cache::get('test_key');
    
    // Vérifier dans la base
    $cacheEntry = DB::table('cache')->where('key', 'test_key')->first();
    
    return [
        'from_cache' => $value,
        'in_database' => $cacheEntry ? '✅ Present' : '❌ Missing',
        'driver' => config('cache.default')
    ];
});

require __DIR__.'/auth.php';