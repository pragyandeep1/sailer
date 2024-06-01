<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreSuppliesRequest;
use App\Http\Requests\UpdateSuppliesRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\{AssetFiles, MeterReadings, AssetPartSuppliesLog, AssetGeneralInfo, AssetChargeDepartment, AssetAddress, AssetAccounts, Position, Facility, MeterReadUnits, AssetCategory, Country, State, City, Supplies, FacilityEquipmentRelation, FacilityRelation, FacilityToolsRelation, User};
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SuppliesController extends Controller
{

    /**
     * Instantiate a new SuppliesController instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:read-supply|create-supply|edit-supply|delete-supply', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-supply', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-supply', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-supply', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $supplies = Supplies::with([
            'assetAddress',
            'assetGeneralInfo',
            'assetPartSuppliesLog',
            'meterReadings',
            'assetFiles'
        ])->latest()->paginate();

        $data['countries'] = Country::get(["name", "id"]);

        return view('supplies.index', [
            'roles' => Role::pluck('name')->all(),
            'positions' => Position::pluck('name', 'id')->all(),
            'categories' => AssetCategory::where('type', 'facility')->where('status', '1')->pluck('name', 'id')->all(),
            'MeterReadUnits' =>  MeterReadUnits::select('id', 'name', 'symbol', 'unit_precision')->get()->toArray(),
            'accounts' =>  AssetAccounts::select('id', 'code', 'description')->get()->toArray(),
            'departments' =>  AssetChargeDepartment::select('id', 'code', 'description')->get()->toArray(),
            // 'supplies' =>  Supplies::select('id', 'code', 'description', 'name')->get()->toArray(),
            'supplies' => $supplies
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $data['countries'] = Country::get(["name", "id"]);
        return view('supplies.create', $data, [
            'roles' => Role::pluck('name')->all(),
            'positions' => Position::pluck('name', 'id')->all(),
            'facilities' => Facility::pluck('name', 'id')->all(),
            'users' => User::pluck('name', 'id')->all(),
            'categories' => AssetCategory::where('type', 'facility')->where('status', '1')->pluck('name', 'id')->all(),
            'MeterReadUnits' =>  MeterReadUnits::select('id', 'name', 'symbol', 'unit_precision')->get()->toArray(),
            'accounts' =>  AssetAccounts::select('id', 'code', 'description')->get()->toArray(),
            'departments' =>  AssetChargeDepartment::select('id', 'code', 'description')->get()->toArray(),
            'supplies' =>  Supplies::select('id', 'code', 'description', 'name')->get()->toArray(),

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSuppliesRequest $request): RedirectResponse
    {
        $input = $request->all();
        $facility = Facility::create($input);
        $facilityId = $facility->id;
        $facilityName = $facility->name;
        // AssetAddress::create($request->all()); saturday work from here......

        $create_location = new AssetAddress;
        $create_location->asset_type  = 'facility';
        $create_location->asset_id    = $facilityId;
        $create_location->has_parent = $request->faci_chkbox;
        $create_location->parent_id = $request->parent_id;
        if ($request->faci_chkbox == 1) {
            if ($request->parent_id != '') {

                //save in relation table
                $facility_relation = new FacilityRelation;
                $facility_relation->parent_id = $request->parent_id;
                $facility_relation->child_id = $facilityId;
                $facility_relation->save();

                $parent_address = AssetAddress::where('asset_type', 'facility')->where('asset_id', $request->parent_id)->latest()->first();
                if ($parent_address) {
                    $create_location->address = $parent_address->address;
                } else {
                    $create_location->address = json_encode($request->add_address);
                }
            } else {
                $create_location->address = json_encode($input['contact']);
            }
        } else {
            $create_location->address = json_encode($input['contact']);
        }
        $create_location->save();




        $create_genInfo = new AssetGeneralInfo;
        $create_genInfo->asset_type  = 'facility';
        $create_genInfo->asset_id    = $facilityId;
        $create_genInfo->accounts_id = $request->account;
        $create_genInfo->barcode = $request->barcode;
        $create_genInfo->charge_department_id = $request->department;
        $create_genInfo->notes = $request->notes;
        $create_genInfo->save();
        if ($request->quantity) {
            $create_supplieslog = new AssetPartSuppliesLog;
            $create_supplieslog->asset_type  = 'facility';
            $create_supplieslog->asset_id    = $facilityId;
            $create_supplieslog->part_supply_id = $request->supplies;
            $create_supplieslog->quantity = $request->quantity;
            $create_supplieslog->submitted_by = auth()->user()->id;
            $create_supplieslog->save();
        }
        if ($request->meter_reading) {
            $create_meterRead = new MeterReadings;
            $create_meterRead->asset_type  = 'facility';
            $create_meterRead->asset_id    = $facilityId;
            $create_meterRead->reading_value = $request->meter_reading;
            $create_meterRead->meter_units_id = $request->meter_read_units;
            $create_meterRead->submitted_by = auth()->user()->id;
            $create_meterRead->save();
        }
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // Validate file
                // $this->validate($request, [
                //     'files.*' => 'mimes:doc,docx,xlsx,xls,ppt,pptx,txt,pdf,jpg,jpeg,png,gif|max:2048',
                // ]);
                $this->validate($request, [
                    'files.*' => 'mimes:doc,docx,xlsx,xls,ppt,pptx,txt,pdf,jpg,jpeg,png,gif,webp|max:2048|not_in:invalid_file',
                ], [
                    'files.*.not_in' => 'Invalid file type. Please upload files with allowed extensions.',
                ]);

                // Generate a unique file name
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $fileNameToStore = Str::slug($fileName) . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // $fileNameToStore = uniqid() . '_' . $file->getClientOriginalName();

                // Define the destination path
                $destinationPath = public_path('Facility/FacilityId_' . $facilityId);

                // Move the file to the destination
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true, true);
                }
                $file->move($destinationPath, $fileNameToStore);

                // Create a Certification record
                $create_doc = new AssetFiles;
                $create_doc->asset_type  = 'facility';
                $create_doc->asset_id    = $facilityId;
                $create_doc->name = $fileNameToStore;
                $create_doc->url = ('public/Facility/FacilityId_' . $facilityId . '/' . $fileNameToStore);

                // Determine file type
                $extension = $file->getClientOriginalExtension();
                if ($extension == 'mp4') {
                    $create_doc->type = "video";
                } elseif (in_array($extension, ['pdf', 'jpeg', 'png', 'gif', 'webp'])) {
                    $create_doc->type = $extension;
                } else {
                    $create_doc->type = "other";
                }

                $create_doc->save();
            }
        }
        return redirect()->route('facilities.edit', $facilityId)
            ->withSuccess('New facility is added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Facility $facility): View
    {
        $facility->load([
            'assetAddress',
            'assetGeneralInfo',
            'assetPartSuppliesLog',
            'meterReadings',
            'assetFiles'
        ]);
        $data['countries'] = Country::get(["name", "id"]);
        $data['states'] = State::get(["name", "id"]);
        $data['cities'] = City::get(["name", "id"]);
        return view('facilities.show', $data, [
            'roles' => Role::pluck('name')->all(),
            'positions' => Position::pluck('name', 'id')->all(),
            'facilities' => Facility::pluck('name', 'id')->all(),
            'categories' => AssetCategory::where('type', 'facility')->where('status', '1')->pluck('name', 'id')->all(),
            'MeterReadUnits' =>  MeterReadUnits::select('id', 'name', 'symbol', 'unit_precision')->get()->toArray(),
            'accounts' =>  AssetAccounts::select('id', 'code', 'description')->get()->toArray(),
            'departments' =>  AssetChargeDepartment::select('id', 'code', 'description')->get()->toArray(),
            'supplies' =>  Supplies::select('id', 'code', 'description', 'name')->get()->toArray(),
            'facility' => $facility
        ]);
        // return view('facilities.show', [
        //     'facility' => $facility
        // ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Facility $facility): View
    {
        // Eager load related data
        $facility->load([
            'assetAddress',
            'assetGeneralInfo',
            'assetPartSuppliesLog',
            'meterReadings',
            'assetFiles'
        ]);
        $data['countries'] = Country::get(["name", "id"]);
        return view('facilities.edit', $data, [
            'roles' => Role::pluck('name')->all(),
            'positions' => Position::pluck('name', 'id')->all(),
            'facilities' => Facility::pluck('name', 'id')->all(),
            'categories' => AssetCategory::where('type', 'facility')->where('status', '1')->pluck('name', 'id')->all(),
            'MeterReadUnits' =>  MeterReadUnits::select('id', 'name', 'symbol', 'unit_precision')->get()->toArray(),
            'accounts' =>  AssetAccounts::select('id', 'code', 'description')->get()->toArray(),
            'departments' =>  AssetChargeDepartment::select('id', 'code', 'description')->get()->toArray(),
            'supplies' =>  Supplies::select('id', 'code', 'description', 'name')->get()->toArray(),
            'facility' => $facility
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFacilityRequest $request, Facility $facility): RedirectResponse
    {
        // echo 'hi dilip= ' . auth()->user()->id;
        // exit();
        // Update facility details
        $facility->update($request->all());
        $facilityId = $facility->id;
        $facilityName = $facility->name;
        // Update facility address
        $facilityAddress = AssetAddress::where('asset_type', 'facility')->where('asset_id', $facility->id)->first();
        if ($facilityAddress) {
            $facilityAddress->has_parent = $request->faci_chkbox;
            $facilityAddress->parent_id = $request->parent_id;
            if ($request->faci_chkbox == 1) {
                if ($request->parent_id != '') {

                    //update in relation table
                    $facility_relation =  FacilityRelation::where('child_id', $facilityId)->first();
                    $facility_relation->parent_id = $request->parent_id;
                    // $facility_relation->child_id = $facilityId;
                    $facility_relation->save();

                    $parentAddress = AssetAddress::where('asset_type', 'facility')->where('asset_id', $request->parent_id)->latest()->first();
                    if ($parentAddress) {
                        $facilityAddress->address = $parentAddress->address;
                    } else {
                        $facilityAddress->address = json_encode($request->add_address);
                    }
                } else {
                    $facilityAddress->address = 'null';
                }
            } else {
                $facilityAddress->address = json_encode($request->contact);
            }
            $facilityAddress->save();
        }

        // Update general information
        $facilityGeneralInfo = AssetGeneralInfo::where('asset_type', 'facility')->where('asset_id', $facility->id)->first();
        if ($facilityGeneralInfo) {
            $facilityGeneralInfo->accounts_id = $request->account;
            $facilityGeneralInfo->barcode = $request->barcode;
            $facilityGeneralInfo->charge_department_id = $request->department;
            $facilityGeneralInfo->notes = $request->notes;
            $facilityGeneralInfo->save();
        }

        // Update part supplies log
        if ($request->supplies || $request->quantity) {
            AssetPartSuppliesLog::create([
                'asset_type' => 'facility',
                'asset_id' => $facility->id,
                'part_supply_id' => $request->supplies,
                'quantity' => $request->quantity,
                'submitted_by' => auth()->user()->id
            ]);
        }

        // Update meter readings if provided
        if ($request->meter_reading) {
            MeterReadings::create([
                'asset_type' => 'facility',
                'asset_id' => $facility->id,
                'reading_value' => $request->meter_reading,
                'meter_units_id' => $request->meter_read_units,
                'submitted_by' => auth()->user()->id
            ]);
        }

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // Validate file
                // $this->validate($request, [
                //     'files.*' => 'mimes:doc,docx,xlsx,xls,ppt,pptx,txt,pdf,jpg,jpeg,png,gif|max:2048',
                // ]);
                $this->validate($request, [
                    'files.*' => 'mimes:doc,docx,xlsx,xls,ppt,pptx,txt,pdf,jpg,jpeg,png,gif,webp|max:2048|not_in:invalid_file',
                ], [
                    'files.*.not_in' => 'Invalid file type. Please upload files with allowed extensions.',
                ]);

                // Generate a unique file name
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $fileNameToStore = Str::slug($fileName) . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // $fileNameToStore = uniqid() . '_' . $file->getClientOriginalName();

                // Define the destination path
                $destinationPath = public_path('Facility/FacilityId_' . $facilityId);

                // Move the file to the destination
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true, true);
                }
                $file->move($destinationPath, $fileNameToStore);

                // Create a Certification record
                $create_doc = new AssetFiles;
                $create_doc->asset_type  = 'facility';
                $create_doc->asset_id    = $facilityId;
                $create_doc->name = $fileNameToStore;
                $create_doc->url = ('public/Facility/FacilityId_' . $facilityId . '/' . $fileNameToStore);

                // Determine file type
                $extension = $file->getClientOriginalExtension();
                if ($extension == 'mp4') {
                    $create_doc->type = "video";
                } elseif (in_array($extension, ['pdf', 'jpeg', 'png', 'gif', 'webp'])) {
                    $create_doc->type = $extension;
                } else {
                    $create_doc->type = "other";
                }

                $create_doc->save();
            }
        }

        return redirect()->back()->withSuccess('Facility is updated successfully.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Facility $facility): RedirectResponse
    {
        $facility->delete();
        return redirect()->route('facilities.index')
            ->withSuccess('Facility is deleted successfully.');
    }
    public function getParentAddress($id)
    {
        // Find the parent facility by its ID
        $parentFacility = Facility::find($id);

        if (!$parentFacility) {
            // If the parent facility does not exist, return an empty response
            return response()->json(['error' => 'Parent facility not found'], 404);
        }

        // Retrieve the latest address associated with the parent facility
        $latestAddress = AssetAddress::where('asset_type', 'facility')
            ->where('asset_id', $id)
            ->latest()
            ->first();

        if ($latestAddress) {
            // Decode the JSON address data
            $addressData = json_decode($latestAddress->address, true);
            // If decoding is successful and 'address' key exists
            if ($addressData && isset($addressData['address'])) {
                // Retrieve the names of the country, state, and city
                $countryName = $addressData['country'] ? Country::find($addressData['country'])->name : null;
                $stateName = $addressData['state'] ? State::find($addressData['state'])->name : null;
                $cityName = $addressData['city'] ? City::find($addressData['city'])->name : null;

                // Return all necessary data including address, country, state, and city names
                return response()->json([
                    'address' => $addressData['address'],
                    'country' => $countryName,
                    'state' => $stateName,
                    'city' => $cityName,
                    'postcode' => $addressData['postcode'] ?? null,
                ]);
            }
        }
        // If no address is found or address data is invalid, return an empty response
        return response()->json(['address' => null]);
    }
}
