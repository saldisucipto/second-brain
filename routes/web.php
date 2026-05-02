<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\MomController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskGroupController;
use App\Http\Controllers\UserManagementController;
use App\Models\Asset;
use App\Models\AssetSchedule;
use App\Models\FollowUp;
use App\Models\Mom;
use App\Models\MomItem;
use App\Models\Task;
use App\Models\TaskGroup;
use App\Services\DailyBriefService;
use App\Services\InsightService;

Route::middleware('guest')->group(function () {
    Route::get('/signin', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/signin', [AuthController::class, 'login'])->name('login.store');
    Route::get('/signup', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/signup', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // dashboard pages
    Route::get('/', function () {
        try {
            $taskStats = [
                'total' => Task::count(),
                'todo' => Task::where('status', 'todo')->count(),
                'progress' => Task::where('status', 'progress')->count(),
                'done' => Task::where('status', 'done')->count(),
                'highPriority' => Task::where('priority', 'high')->where('status', '!=', 'done')->count(),
            ];

            $followUpStats = [
                'pending' => FollowUp::where('status', 'pending')->count(),
                'today' => FollowUp::whereDate('reminder_at', today())->where('status', 'pending')->count(),
                'missed' => FollowUp::where('status', 'missed')->count(),
            ];

            $recentTasks = Task::with([
                'group',
                'followUps' => fn($query) => $query->orderBy('created_at', 'asc'),
            ])->withCount('followUps')->latest()->take(6)->get();
            $upcomingFollowUps = FollowUp::with('task')
                ->where('status', 'pending')
                ->orderBy('reminder_at')
                ->take(5)
                ->get();
            $groups = TaskGroup::orderBy('name')->get();
            $brief = app(DailyBriefService::class)->get();

            $insightService = app(InsightService::class);
            $insightsByTask = $recentTasks->mapWithKeys(function ($task) use ($insightService) {
                return [$task->id => $insightService->analyze($task)];
            })->all();

            $attentionTasks = collect($insightsByTask)->filter(function ($insights) {
                return collect($insights)->contains(fn($item) => ($item['type'] ?? 'info') === 'warning');
            })->count();

            $assetStats = [
                'total' => Asset::count(),
                'overdue' => AssetSchedule::where('next_due_at', '<', now())->count(),
            ];

            $momStats = [
                'total' => Mom::where('created_by', auth()->id())->count(),
                'ongoing' => Mom::where('created_by', auth()->id())->where('status', 'ongoing')->count(),
                'pendingItems' => MomItem::whereHas('mom', fn($q) => $q->where('created_by', auth()->id()))
                    ->where('status', 'pending')->count(),
            ];

            $recentMoms = Mom::where('created_by', auth()->id())
                ->withCount(['items', 'items as pending_items_count' => fn($q) => $q->where('status', 'pending')])
                ->latest('meeting_date')
                ->take(5)
                ->get();

            $upcomingMaintenances = AssetSchedule::with('asset')
                ->where('next_due_at', '>=', now())
                ->orderBy('next_due_at')
                ->take(5)
                ->get();
        } catch (\Throwable) {
            $taskStats = [
                'total' => 0,
                'todo' => 0,
                'progress' => 0,
                'done' => 0,
                'highPriority' => 0,
            ];

            $followUpStats = [
                'pending' => 0,
                'today' => 0,
                'missed' => 0,
            ];

            $recentTasks = collect();
            $upcomingFollowUps = collect();
            $groups = collect();
            $brief = [
                'total_tasks_today' => 0,
                'overdue_tasks' => 0,
                'follow_ups_today' => 0,
                'tasks_per_category' => [],
            ];
            $insightsByTask = [];
            $attentionTasks = 0;
            $assetStats = ['total' => 0, 'overdue' => 0];
            $momStats = ['total' => 0, 'ongoing' => 0, 'pendingItems' => 0];
            $recentMoms = collect();
            $upcomingMaintenances = collect();
        }

        return view('pages.dashboard.tasks', [
            'title' => 'My Second Brain',
            'taskStats' => $taskStats,
            'followUpStats' => $followUpStats,
            'recentTasks' => $recentTasks,
            'upcomingFollowUps' => $upcomingFollowUps,
            'groups' => $groups,
            'brief' => $brief,
            'insightsByTask' => $insightsByTask,
            'attentionTasks' => $attentionTasks,
            'assetStats' => $assetStats,
            'momStats' => $momStats,
            'recentMoms' => $recentMoms,
            'upcomingMaintenances' => $upcomingMaintenances,
        ]);
    })->name('dashboard');

    Route::resource('users', UserManagementController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('task-groups', TaskGroupController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('moms', MomController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    Route::post('moms/{mom}/create-task', [MomController::class, 'createTask'])->name('moms.create-task');
    Route::resource('notes', NoteController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('/search', function () {
        $q = trim(request('q', ''));
        if ($q === '') {
            return redirect()->route('dashboard');
        }

        $tasks = Task::where('title', 'like', "%{$q}%")
            ->orWhere('description', 'like', "%{$q}%")
            ->latest()
            ->take(20)
            ->get();

        $moms = Mom::where('created_by', auth()->id())
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%");
            })
            ->latest('meeting_date')
            ->take(20)
            ->get();

        $assets = Asset::where('name', 'like', "%{$q}%")
            ->orWhere('category', 'like', "%{$q}%")
            ->orWhere('note', 'like', "%{$q}%")
            ->latest()
            ->take(20)
            ->get();

        return view('pages.dashboard.search', [
            'title' => "Hasil Pencarian: {$q}",
            'q' => $q,
            'tasks' => $tasks,
            'moms' => $moms,
            'assets' => $assets,
        ]);
    })->name('search');

    // Asset Management
    Route::resource('assets', AssetController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('assets/{asset}/schedule/create', [AssetController::class, 'createSchedule'])->name('assets.schedule.create');
    Route::post('assets/{asset}/schedule', [AssetController::class, 'storeSchedule'])->name('assets.schedule.store');
    Route::get('assets/schedule/{schedule}/edit', [AssetController::class, 'editSchedule'])->name('assets.schedule.edit');
    Route::patch('assets/schedule/{schedule}', [AssetController::class, 'updateSchedule'])->name('assets.schedule.update');
    Route::delete('assets/schedule/{schedule}', [AssetController::class, 'destroySchedule'])->name('assets.schedule.destroy');
    Route::post('assets/schedule/{schedule}/complete', [AssetController::class, 'completeSchedule'])->name('assets.schedule.complete');
    Route::post('assets/{asset}/history', [AssetController::class, 'storeHistory'])->name('assets.history.store');

    // Second Brain Route
    Route::redirect('/task', '/tasks');
    Route::resource('tasks', TaskController::class)->only(['index', 'store', 'update']);
    Route::post('tasks/quick-capture', [TaskController::class, 'quickCapture'])->name('tasks.quick-capture');
    Route::post('tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');

    // Task Follow UP
    Route::post('tasks/{task}/follow-up', [FollowUpController::class, 'addFollowUP'])->name('tasks.follow-up.store');
});
