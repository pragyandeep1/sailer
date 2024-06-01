<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreToolRequest;
use App\Http\Requests\UpdateToolRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\{AssetFiles, MeterReadings, AssetPartSuppliesLog, AssetGeneralInfo, AssetChargeDepartment, AssetAddress, AssetAccounts, Position, Facility, MeterReadUnits, AssetCategory, Country, State, City, Equipment, EquipmentRelation, EquipmentToolsRelation, FacilityEquipmentRelation, FacilityRelation, FacilityToolsRelation, Supplies, Tool};
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ToolController extends Controller
{

    /**
     * Instantiate a new ToolController instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:read-tools|create-tools|edit-tools|delete-tools', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-tools', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-tools', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-tools', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    /*public function index(): View
    {
        $tools = Tool::leftjoin('tool_relation', 'tools.id', '=', 'tool_relation.child_id')
            ->select('tools.*', 'tool_relation.parent_id', 'tool_relation.child_id')
            ->get()->toArray();

        return view('tools.index', [
            'toolRelations' => $tools,
        ]);
    }*/

    /*public function index(): View
    {
        // $toolRelations = [];
        $tools = Tool::all();

        foreach ($tools as $tool) {
            $id = $tool['id'];
            // $uid = !empty($tool['child_id']) ? $id : 0;
            $result1 = DB::table('facility_tools_relation as ft')
                ->join('facilities as f', 'ft.facility_id', '=', 'f.id')
                ->where('ft.facility_id', $id)
                ->get()
                ->toArray();

            $result2 = DB::table('equipment_tools_relation as et')
                ->join('equipments as e', 'et.equipment_id', '=', 'e.id')
                ->where('et.equipment_id', $id)
                ->get()
                ->toArray();

            $results = array_merge($result1, $result2);

            $results = array_map(function ($result) {
                return (array) $result;
            }, $results);

            $toolRelations[] = $results;
        }

        $singleArrayForCategory = array_reduce($toolRelations, 'array_merge', array());
        $facilityToolsRelation = array_merge($tools, $singleArrayForCategory);
        $equipmentToolsRelation = array_merge($tools, $singleArrayForCategory);
        $toolsRelation = array_merge($facilityToolsRelation, $equipmentToolsRelation);
        return view('tools.index', [
            'toolRelations' => $facilityToolsRelation, $equipmentToolsRelation
        ]);
    }*/

    public function index(): View
    {
        $tools = Tool::all()->toArray();

        foreach ($tools as $tool) {
            $id = $tool['id'];
            $facilityRelations = DB::table('facility_tools_relation as ft')
            ->join('facilities as f', 'ft.facility_id', '=', 'f.id')
            ->where('ft.tool_id', $id)
            ->get()
            ->toArray();

            $equipmentRelations = DB::table('equipment_tools_relation as et')
            ->join('equipments as e', 'et.equipment_id', '=', 'e.id')
            ->where('et.tool_id', $id)
            ->get()
            ->toArray();

            $tool['facility_relations'] = $facilityRelations;
            $tool['equipment_relations'] = $equipmentRelations;
        }
        return view('tools.index', [
            'tools' => $tools,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $data['countries'] = Country::get(["name", "id"]);
        return view('tools.create', $data, [
            'roles' => Role::pluck('name')->all(),
            'positions' => Position::pluck('name', 'id')->all(),
            'facilities' => Facility::pluck('name', 'id')->all(),
            'tools' => Tool::pluck('name', 'id')->all(),
            'tools' => Equipment::pluck('name', 'id')->all(),
            'categories' => AssetCategory::where('type', 'tools')->where('status', '1')->pluck('name', 'id')->all(),
            'MeterReadUnits' =>  MeterReadUnits::select('id', 'name', 'symbol', 'unit_precision')->get()->toArray(),
            'accounts' =>  AssetAccounts::select('id', 'code', 'description')->get()->toArray(),
            'departments' =>  AssetChargeDepartment::select('id', 'code', 'description')->get()->toArray(),
            'supplies' =>  Supplies::select('id', 'code', 'description', 'name')->get()->toArray(),

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreToolRequest $request): RedirectResponse
    {
        $input = $request->all();
        $tool = Tool::create($input);
        $toolId = $tool->id;
        $toolName = $tool->name;

        $create_location = new AssetAddress;
        $create_location->asset_type  = 'tools';
        $create_location->asset_id    = $toolId;
        $create_location->has_parent = $request->faci_chkbox;
        $create_location->parent_id = $request->parent_id;
        $create_location->aisle = $request->aisle;
        $create_location->row = $request->row;
        $create_location->bin = $request->bin;
        $create_location->save();

        if ($request->faci_chkbox == 1) {
            if ($request->parent_id != '') {
                //save in relation table
                $facitool_relation = new FacilityToolsRelation;
                $facitool_relation->facility_id = $request->parent_id;
                $facitool_relation->tool_id = $toolId;
                $facitool_relation->save();
            }
        }
        if ($request->faci_chkbox == 0) {
            if ($request->parent_tool != '') {
                //save in relation table
                $tool_relation = new EquipmentToolsRelation;
                $tool_relation->tool_id = $request->parent_tool;
                $tool_relation->tool_id = $toolId;
                $tool_relation->save();
            }
        }

        $create_genInfo = new AssetGeneralInfo;
        $create_genInfo->asset_type  = 'tools';
        $create_genInfo->asset_id    = $toolId;
        $create_genInfo->accounts_id = $request->account;
        $create_genInfo->barcode = $request->barcode;
        $create_genInfo->charge_department_id = $request->department;
        $create_genInfo->make = $request->make;
        $create_genInfo->model = $request->model;
        $create_genInfo->serial_number = $request->serial_number;
        $create_genInfo->unspc_code = $request->unspc_code;
        $create_genInfo->notes = $request->notes;
        $create_genInfo->save();
        if ($request->quantity) {
            $create_supplieslog = new AssetPartSuppliesLog;
            $create_supplieslog->asset_type  = 'tools';
            $create_supplieslog->asset_id    = $toolId;
            $create_supplieslog->part_supply_id = $request->supplies;
            $create_supplieslog->quantity = $request->quantity;
            $create_supplieslog->submitted_by = auth()->user()->id;
            $create_supplieslog->save();
        }
        if ($request->meter_reading) {
            $create_meterRead = new MeterReadings;
            $create_meterRead->asset_type  = 'tools';
            $create_meterRead->asset_id    = $toolId;
            $create_meterRead->reading_value = $request->meter_reading;
            $create_meterRead->meter_units_id = $request->meter_read_units;
            $create_meterRead->submitted_by = auth()->user()->id;
            $create_meterRead->save();
        }
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {

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
                $destinationPath = public_path('Tool/ToolId_' . $toolId);

                // Move the file to the destination
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true, true);
                }
                $file->move($destinationPath, $fileNameToStore);

                // Create a Certification record
                $create_doc = new AssetFiles;
                $create_doc->asset_type  = 'tools';
                $create_doc->asset_id    = $toolId;
                $create_doc->name = $fileNameToStore;
                $create_doc->url = ('public/Tool/ToolId_' . $toolId . '/' . $fileNameToStore);

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
        return redirect()->route('tools.edit', $toolId)
            ->withSuccess('New tool is added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tool $tool): View
    {
        $tool->load([
            'assetAddress',
            'assetGeneralInfo',
            'assetPartSuppliesLog',
            'meterReadings',
            'assetFiles',

        ]);
        $data['countries'] = Country::get(["name", "id"]);
        $data['states'] = State::get(["name", "id"]);
        $data['cities'] = City::get(["name", "id"]);
        return view('tools.show', $data, [
            'roles' => Role::pluck('name')->all(),
            'positions' => Position::pluck('name', 'id')->all(),
            'facilities' => Facility::pluck('name', 'id')->all(),
            'tools' => Equipment::pluck('name', 'id')->all(),
            'categories' => AssetCategory::where('type', 'tools')->where('status', '1')->pluck('name', 'id')->all(),
            'MeterReadUnits' =>  MeterReadUnits::select('id', 'name', 'symbol', 'unit_precision')->get()->toArray(),
            'accounts' =>  AssetAccounts::select('id', 'code', 'description')->get()->toArray(),
            'departments' =>  AssetChargeDepartment::select('id', 'code', 'description')->get()->toArray(),
            'supplies' =>  Supplies::select('id', 'code', 'description', 'name')->get()->toArray(),
            'tool' => $tool
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tool $tool): View
    {
        // Eager load related data
        $tool->load([
            'assetAddress',
            'assetGeneralInfo',
            'assetPartSuppliesLog',
            'meterReadings',
            'assetFiles',

        ]);
        $data['countries'] = Country::get(["name", "id"]);
        return view('tools.edit', $data, [
            'roles' => Role::pluck('name')->all(),
            'positions' => Position::pluck('name', 'id')->all(),
            'facilities' => Facility::pluck('name', 'id')->all(),
            'tools' => Equipment::pluck('name', 'id')->all(),
            'categories' => AssetCategory::where('type', 'tools')->where('status', '1')->pluck('name', 'id')->all(),
            'MeterReadUnits' =>  MeterReadUnits::select('id', 'name', 'symbol', 'unit_precision')->get()->toArray(),
            'accounts' =>  AssetAccounts::select('id', 'code', 'description')->get()->toArray(),
            'departments' =>  AssetChargeDepartment::select('id', 'code', 'description')->get()->toArray(),
            'supplies' =>  Supplies::select('id', 'code', 'description', 'name')->get()->toArray(),
            'facirelation' =>  FacilityToolsRelation::select('id', 'facility_id', 'tool_id')->where('tool_id', $tool->id)->get()->toArray(),
            'equiprelation' =>  EquipmentToolsRelation::select('id', 'tool_id', 'tool_id')->where('tool_id', $tool->id)->get()->toArray(),
            'tool' => $tool
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateToolRequest $request, Tool $tool): RedirectResponse
    {
        // echo 'hi dilip= ' . auth()->user()->id;
        // exit();
        // Update tool details
        $tool->update($request->all());
        $toolId = $tool->id;
        $toolName = $tool->name;
        $toolAddress = AssetAddress::where('asset_type', 'tools')->where('asset_id', $tool->id)->first();
        if ($toolAddress) {
            $toolAddress->has_parent = $request->faci_chkbox;
            $toolAddress->parent_id = $request->parent_id;
            $toolAddress->aisle = $request->aisle;
            $toolAddress->row = $request->row;
            $toolAddress->bin = $request->bin;
            $toolAddress->save();
        }
        if ($request->faci_chkbox == 1) {
            if ($request->parent_id != '') {
                //save in relation table
                $facitool_relation = FacilityToolsRelation::where('tool_id', $toolId)->first();
                if ($facitool_relation) {
                    $facitool_relation->facility_id = $request->parent_id;
                    // $facitool_relation->tool_id = $toolId;
                    $facitool_relation->save();
                } else {
                    $facitool_relation = new FacilityToolsRelation;
                    $facitool_relation->facility_id = $request->parent_id;
                    $facitool_relation->tool_id = $toolId;
                    $facitool_relation->save();
                }
            }
        }
        if ($request->faci_chkbox == 0) {
            if ($request->parent_tool != '') {
                //save in relation table
                $tool_relation = EquipmentToolsRelation::where('tool_id', $toolId)->first();
                if ($tool_relation) {
                    $tool_relation->tool_id = $request->parent_tool;
                    // $tool_relation->tool_id = $toolId;
                    $tool_relation->save();
                } else {
                    $tool_relation = new EquipmentToolsRelation;
                    $tool_relation->tool_id = $request->parent_tool;
                    $tool_relation->tool_id = $toolId;
                    $tool_relation->save();
                }
            }
        }

        // Update general information
        $toolGeneralInfo = AssetGeneralInfo::where('asset_type', 'tools')->where('asset_id', $tool->id)->first();
        if ($toolGeneralInfo) {
            $toolGeneralInfo->accounts_id = $request->account;
            $toolGeneralInfo->barcode = $request->barcode;
            $toolGeneralInfo->charge_department_id = $request->department;
            $toolGeneralInfo->make = $request->make;
            $toolGeneralInfo->model = $request->model;
            $toolGeneralInfo->serial_number = $request->serial_number;
            $toolGeneralInfo->unspc_code = $request->unspc_code;
            $toolGeneralInfo->notes = $request->notes;
            $toolGeneralInfo->save();
        }

        // Update part supplies log
        if ($request->supplies || $request->quantity) {
            AssetPartSuppliesLog::create([
                'asset_type' => 'tools',
                'asset_id' => $tool->id,
                'part_supply_id' => $request->supplies,
                'quantity' => $request->quantity,
                'submitted_by' => auth()->user()->id
            ]);
        }

        // Update meter readings if provided
        if ($request->meter_reading) {
            MeterReadings::create([
                'asset_type' => 'tools',
                'asset_id' => $tool->id,
                'reading_value' => $request->meter_reading,
                'meter_units_id' => $request->meter_read_units,
                'submitted_by' => auth()->user()->id
            ]);
        }

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $this->validate($request, [
                    'files.*' => 'mimes:doc,docx,xlsx,xls,ppt,pptx,txt,pdf,jpg,jpeg,png,gif,webp|max:2048|not_in:invalid_file',
                ], [
                    'files.*.not_in' => 'Invalid file type. Please upload files with allowed extensions.',
                ]);

                // Generate a unique file name
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $fileNameToStore = Str::slug($fileName) . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Define the destination path
                $destinationPath = public_path('Tool/ToolId_' . $toolId);

                // Move the file to the destination
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true, true);
                }
                $file->move($destinationPath, $fileNameToStore);

                // Create a Certification record
                $create_doc = new AssetFiles;
                $create_doc->asset_type  = 'tools';
                $create_doc->asset_id    = $toolId;
                $create_doc->name = $fileNameToStore;
                $create_doc->url = ('public/Tool/ToolId_' . $toolId . '/' . $fileNameToStore);

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

        return redirect()->back()->withSuccess('Tool is updated successfully.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tool $tool): RedirectResponse
    {
        //do not delete items permanently, instead update is_delete column
        $tool->delete();
        return redirect()->route('tools.index')
            ->withSuccess('Tool is deleted successfully.');
    }
    public function getEquipParentAddress($id)
    {
        // Retrieve the facility ID associated with the tool
        $facilityId = FacilityEquipmentRelation::where('tool_id', $id)->value('facility_id');

        if (!$facilityId) {
            // If no facility ID is found, return an empty response
            return response()->json(['error' => 'No parent facility found for the tool'], 404);
        }

        // Traverse upward until reaching the top-level parent facility
        $topParentFacilityId = $this->getTopParentFacilityId($facilityId);

        if (!$topParentFacilityId) {
            // If no top-level parent facility is found, return an empty response
            return response()->json(['error' => 'No top-level parent facility found'], 404);
        }

        // Retrieve the latest address associated with the top-level parent facility
        $latestAddress = AssetAddress::where('asset_id', $topParentFacilityId)
            ->where('asset_type', 'facility')
            ->latest()
            ->first();

        if ($latestAddress) {
            // Decode the JSON address data
            $addressData = json_decode($latestAddress->address, true);

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

    /**
     * Get the top-level parent facility ID recursively.
     *
     * @param int $facilityId
     * @return int|null
     */
    protected function getTopParentFacilityId($facilityId)
    {
        // Check if the current facility ID is associated with a facility
        $isFacility = Facility::where('id', $facilityId)->exists();

        if ($isFacility) {
            // If the current ID is associated with a facility, return it
            return $facilityId;
        }

        // Retrieve the parent tool's parent facility
        $parentEquipment = EquipmentRelation::where('child_id', $facilityId)->first();

        if ($parentEquipment) {
            // If the current ID is associated with an tool, check its parent recursively
            return $this->getTopParentFacilityId($parentEquipment->parent_id);
        }

        // If no parent tool is found, return null (no top-level parent facility)
        return null;
    }
    public function savetoolsmeter(Request $request)
    {

        // print_r($request->input());
        // exit();
        // Validate the request data
        $request->validate([
            'meterunit_id' => 'required',
            'meterreading' => 'nullable|numeric|min:0',
        ]);
        // Extract the request data
        $log_id = $request->input('log_id');
        $meterunit_id = $request->input('meterunit_id');
        $meterreading = $request->input('meterreading');
        // Update part supplies log
        $toolsMetersUpdate = MeterReadings::where('id', $log_id);
        if (empty($meterunit_id) || empty($meterreading)) {
            return response()->json([
                'error' => 'Validation failed',
                'message' => 'Please fix the following errors',
                'errors' => [
                    'meterunit_id' => ['The Unit field is required.'],
                    'meterreading.min' => ['The reading field must be a positive number.']
                ]
            ], 422);
        }
        // Check if supplies_id and quantity are not empty or null
        $toolsMetersUpdate->update([
            'meter_units_id' => $meterunit_id,
            'reading_value' => $meterreading,
            'submitted_by' => auth()->user()->id,
        ]);
        // Return success response
        return response()->json(['success' => 'Records saved successfully']);
    }
    public function destroytoolsmeter($toolsmeterID)
    {
        $toolsmeterIDdata = MeterReadings::where('id', $toolsmeterID)->first();
        $toolsmeterIDdata->delete();
        return response()->json(['success' => 'Record deleted successfully']);
    }
    public function toolsdocs(Request $request)
    {

        // print_r($request->input());
        // exit();
        // Validate the request data
        $request->validate([
            'docsName' => 'required',
        ]);
        // Extract the request data
        $log_id = $request->input('log_id');
        $docsName = $request->input('docsName');
        $docsDescription = $request->input('docsDescription');
        $toolsDocsUpdate = AssetFiles::where('af_id', $log_id);
        if (empty($docsName)) {
            return response()->json([
                'error' => 'Validation failed',
                'message' => 'Please fix the following errors',
                'errors' => [
                    'docsName' => ['The name field is required.'],
                ]
            ], 422);
        }
        $toolsDocsUpdate->update([
            'name' => $docsName,
            'description' => $docsDescription,
            'submitted_by' => auth()->user()->id,
        ]);
        // Return success response
        return response()->json(['success' => 'Records saved successfully']);
    }
    public function destroytoolsdocs($toolsdocsID)
    {
        $toolsdocsIDdata = AssetFiles::where('af_id', $toolsdocsID)->first();
        $toolsdocsIDdata->delete();
        return response()->json(['success' => 'Record deleted successfully']);
    }
}
