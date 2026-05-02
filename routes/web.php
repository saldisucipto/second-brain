<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\MomController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskGroupController;
use App\Http\Controllers\UserManagementController;
use App\Models\FollowUp;
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
        ]);
    })->name('dashboard');

    Route::resource('users', UserManagementController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('task-groups', TaskGroupController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('moms', MomController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    Route::post('moms/{mom}/create-task', [MomController::class, 'createTask'])->name('moms.create-task');

    // Second Brain Route
    Route::redirect('/task', '/tasks');
    Route::resource('tasks', TaskController::class)->only(['index', 'store', 'update']);
    Route::post('tasks/quick-capture', [TaskController::class, 'quickCapture'])->name('tasks.quick-capture');
    Route::post('tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');

    // Task Follow UP
    Route::post('tasks/{task}/follow-up', [FollowUpController::class, 'addFollowUP'])->name('tasks.follow-up.store');
});
