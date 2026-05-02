<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetSchedule;
use App\Models\AssetHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    /**
     * Display list of assets
     */
    public function index(Request $request)
    {
        $query = Asset::with(['schedules', 'histories'])
            ->orderBy('category')
            ->orderBy('name');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $assets = $query->paginate(12)->withQueryString();

        // Get categories for filter
        $categories = Asset::distinct('category')->pluck('category');

        return view('pages.dashboard.assets.index', [
            'title'      => 'Asset Management',
            'assets'     => $assets,
            'categories' => $categories,
            'filter'     => $request->only(['category', 'search']),
        ]);
    }

    /**
     * Show create asset form
     */
    public function create()
    {
        return view('pages.dashboard.assets.create', [
            'title' => 'Tambah Asset Baru',
            'categories' => [
                'motor'   => 'Motor',
                'mobil'   => 'Mobil',
                'laptop'  => 'Laptop',
                'printer' => 'Printer',
                'other'   => 'Lainnya',
            ],
        ]);
    }

    /**
     * Store new asset
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'note'     => ['nullable', 'string'],
        ]);

        $asset = Asset::create($validated);

        return redirect()->route('assets.show', $asset)->with('success', "Asset '{$asset->name}' berhasil dibuat.");
    }

    /**
     * Show asset detail with schedules and history
     */
    public function show(Asset $asset)
    {
        $asset->load(['schedules' => fn($q) => $q->orderBy('next_due_at'), 'histories' => fn($q) => $q->latest('done_at')]);

        return view('pages.dashboard.assets.show', [
            'title' => $asset->name,
            'asset' => $asset,
        ]);
    }

    /**
     * Show edit form
     */
    public function edit(Asset $asset)
    {
        return view('pages.dashboard.assets.edit', [
            'title'      => "Edit {$asset->name}",
            'asset'      => $asset,
            'categories' => [
                'motor'   => 'Motor',
                'mobil'   => 'Mobil',
                'laptop'  => 'Laptop',
                'printer' => 'Printer',
                'other'   => 'Lainnya',
            ],
        ]);
    }

    /**
     * Update asset
     */
    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'note'     => ['nullable', 'string'],
        ]);

        $asset->update($validated);

        return redirect()->route('assets.show', $asset)->with('success', "Asset berhasil diupdate.");
    }

    /**
     * Delete asset
     */
    public function destroy(Asset $asset)
    {
        $name = $asset->name;
        $asset->delete();

        return redirect()->route('assets.index')->with('success', "Asset '{$name}' berhasil dihapus.");
    }

    /**
     * Create schedule form
     */
    public function createSchedule(Asset $asset)
    {
        return view('pages.dashboard.assets.schedule-create', [
            'title' => "Buat Jadwal untuk {$asset->name}",
            'asset' => $asset,
        ]);
    }

    /**
     * Store maintenance schedule
     */
    public function storeSchedule(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'title'                => ['required', 'string', 'max:255'],
            'repeat_type'          => ['required', 'in:daily,weekly,monthly,yearly'],
            'repeat_interval'      => ['required', 'integer', 'min:1'],
            'reminder_before_days' => ['nullable', 'integer', 'min:0'],
            'first_due_at'         => ['required', 'date'],
        ]);

        $schedule = AssetSchedule::create([
            'asset_id'             => $asset->id,
            'title'                => $validated['title'],
            'repeat_type'          => $validated['repeat_type'],
            'repeat_interval'      => $validated['repeat_interval'],
            'reminder_before_days' => $validated['reminder_before_days'] ?? 1,
            'next_due_at'          => $validated['first_due_at'],
        ]);

        return redirect()->route('assets.show', $asset)->with('success', "Jadwal '{$schedule->title}' berhasil dibuat.");
    }

    /**
     * Complete maintenance
     */
    public function completeSchedule(Request $request, AssetSchedule $schedule)
    {
        $validated = $request->validate([
            'description' => ['nullable', 'string'],
            'cost'        => ['nullable', 'numeric', 'min:0'],
            'done_at'     => ['required', 'date'],
        ]);

        DB::transaction(function () use ($schedule, $validated) {
            // Create history record
            AssetHistory::create([
                'asset_id'    => $schedule->asset_id,
                'schedule_id' => $schedule->id,
                'title'       => $schedule->title,
                'description' => $validated['description'],
                'cost'        => $validated['cost'],
                'done_at'     => $validated['done_at'],
            ]);

            // Update schedule
            $schedule->markComplete($validated['done_at']);
        });

        return redirect()->route('assets.show', $schedule->asset)->with('success', "Maintenance '{$schedule->title}' berhasil dicatat.");
    }

    /**
     * Create ad-hoc maintenance history (not related to schedule)
     */
    public function storeHistory(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cost'        => ['nullable', 'numeric', 'min:0'],
            'done_at'     => ['required', 'date'],
        ]);

        AssetHistory::create([
            'asset_id'    => $asset->id,
            'schedule_id' => null,
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'cost'        => $validated['cost'],
            'done_at'     => $validated['done_at'],
        ]);

        return redirect()->route('assets.show', $asset)->with('success', "Maintenance history berhasil dicatat.");
    }

    /**
     * Edit schedule
     */
    public function editSchedule(AssetSchedule $schedule)
    {
        return view('pages.dashboard.assets.schedule-edit', [
            'title'    => "Edit Jadwal: {$schedule->title}",
            'schedule' => $schedule,
        ]);
    }

    /**
     * Update schedule
     */
    public function updateSchedule(Request $request, AssetSchedule $schedule)
    {
        $validated = $request->validate([
            'title'                => ['required', 'string', 'max:255'],
            'repeat_type'          => ['required', 'in:daily,weekly,monthly,yearly'],
            'repeat_interval'      => ['required', 'integer', 'min:1'],
            'reminder_before_days' => ['nullable', 'integer', 'min:0'],
        ]);

        $schedule->update($validated);

        return redirect()->route('assets.show', $schedule->asset)->with('success', "Jadwal berhasil diupdate.");
    }

    /**
     * Delete schedule
     */
    public function destroySchedule(AssetSchedule $schedule)
    {
        $asset = $schedule->asset;
        $title = $schedule->title;
        $schedule->delete();

        return redirect()->route('assets.show', $asset)->with('success', "Jadwal '$title' berhasil dihapus.");
    }
}
