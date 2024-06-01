<?php

namespace App\Http\Controllers;

use App\Models\{Equipment, Facility, Asset, Tool};
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AssetController extends Controller
{
    /**
     * Instantiate a new AssetController instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:read-asset|create-asset|edit-asset|delete-asset', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-asset', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-asset', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-asset', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // $equipments = Facility::with([
        //     'assetAddress',
        //     'assetGeneralInfo',
        //     'assetPartSuppliesLog',
        //     'meterReadings',
        //     'assetFiles'
        // ])->latest()->paginate();
        // $facilities = Facility::latest()->paginate();
        // $equipments = Equipment::latest()->paginate();
        // $tools = Tool::latest()->paginate();
        // $assets = Asset::latest()->paginate();
        // Retrieve data by joining related models and filtering by status
        $facilities = Facility::with('facilityEquipmentRelations', 'facilityRelations', 'facilityToolsRelations')
            ->get();

        // Organize the data into a hierarchical structure
        $organisedData = [];
        foreach ($facilities as $facility) {
            $organisedData[] = [
                'id' => $facility->id,
                'parent_id' => $facility->parent_id,
                'name' => $facility->name,
                'status' => $facility->status,
                // Add other relevant fields from related models
                // Example: 'equipment' => $facility->facilityEquipmentRelations,
                // Example: 'relations' => $facility->facilityRelations,
                // Example: 'tools' => $facility->facilityToolsRelations,
                'children' => [] // Placeholder for children
            ];
        }

        // Pass the hierarchical data to the view
        return view('assets.index', [
            'facilities' => $organisedData
        ]);

        // return view('assets.index', compact('facilities', 'equipments', 'tools'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('assets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssetRequest $request): RedirectResponse
    {
        Asset::create($request->all());
        return redirect()->route('assets.index')
            ->withSuccess('New asset is added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset): View
    {
        return view('assets.show', [
            'asset' => $asset
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset): View
    {
        return view('assets.edit', [
            'asset' => $asset
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssetRequest $request, Asset $asset): RedirectResponse
    {
        $asset->update($request->all());
        return redirect()->back()
            ->withSuccess('Asset is updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset): RedirectResponse
    {
        $asset->delete();
        return redirect()->route('assets.index')
            ->withSuccess('Asset is deleted successfully.');
    }
}
