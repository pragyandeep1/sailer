<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreEquipmentRequest;
use App\Http\Requests\UpdateEquipmentRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\{AssetFiles, MeterReadings, AssetPartSuppliesLog, AssetGeneralInfo, AssetChargeDepartment, AssetAddress, AssetAccounts, Position, Facility, MeterReadUnits, AssetCategory, Country, State, City, Equipment, EquipmentRelation, Supplies, EquipmentToolsRelation, FacilityRelation};
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EquipmentController extends Controller
{

    /**
     * Instantiate a new EquipmentController instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:read-equipment|create-equipment|edit-equipment|delete-equipment', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-equipment', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-equipment', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-equipment', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    // public function index(): View
    // {
    //     $equipments = Equipment::with([
    //         'assetAddress',
    //         'assetGeneralInfo',
    //         'assetPartSuppliesLog',
    //         'meterReadings',
    //         'assetFiles',
    //         'equipmentRelation'
    //     ])->latest()->paginate();

    //     $data['countries'] = Country::get(["name", "id"]);

    //     return view('equipments.index', [
    //         'roles' => Role::pluck('name')->all(),
    //         'positions' => Position::pluck('name', 'id')->all(),
    //         'facilities' => Facility::pluck('name', 'id'),
    //         'allfacilities' => Facility::latest()->paginate(),
    //         'categories' => AssetCategory::where('type', 'equipment')->where('status', '1')->pluck('name', 'id')->all(),
    //         'MeterReadUnits' =>  MeterReadUnits::select('id', 'name', 'symbol', 'unit_precision')->get()->toArray(),
    //         'accounts' =>  AssetAccounts::select('id', 'code', 'description')->get()->toArray(),
    //         'departments' =>  AssetChargeDepartment::select('id', 'code', 'description')->get()->toArray(),
    //         'supplies' =>  Supplies::select('id', 'code', 'description', 'name')->get()->toArray(),
    //         'equipments' => $equipments
    //     ]);
    // }
    public function index(): View
    {
        $equipmentRelations = [];

        $equipments = Equipment::leftjoin('equipment_relation', 'equipments.id', '=', 'equipment_relation.child_id')
            ->select('equipments.*', 'equipment_relation.parent_id', 'equipment_relation.child_id')
            ->get()->toArray();

        /*Foreach loop starts*/
        foreach ($equipments as $equipment) {

            $id = $equipment['id'];

            $uid = !empty($equipment['parent_id']) ? $id : 0;

            // $results = DB::select(

            //     "SELECT * FROM stocks st, supplies s WHERE st.parent_id = $id AND st.asset_id = s.id",

            // )->toArray();

            $results = DB::table('stocks as st')

                ->join('supplies as s', 'st.asset_id', '=', 's.id')

                ->where('st.parent_id', $id)

                ->get()

                ->toArray();



            $results = array_map(function ($result) {

                return (array) $result;

            }, $results);

            // Push results into facilityRelations array

            // $facility->abc = $results;

            $results = array_map(function ($result) use ($uid) {

                $result['keyName'] = $uid;

                return $result;

            }, $results);



            $equipmentRelations[] = $results;

        }

        $singleArrayForCategory = array_reduce($equipmentRelations, 'array_merge', array());



        $supplyrelation = array_merge($equipments, $singleArrayForCategory);
        /*FOR EACH LOOP ENDS*/

        return view('equipments.index', [
            'equipments' => Equipment::all(),
            'equipmentRelations' => $equipments,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $data['countries'] = Country::get(["name", "id"]);
        return view('equipments.create', $data, [
            'roles' => Role::pluck('name')->all(),
            'positions' => Position::pluck('name', 'id')->all(),
            'facilities' => Facility::pluck('name', 'id')->all(),
            'equipments' => Equipment::pluck('name', 'id')->all(),
            'categories' => AssetCategory::where('type', 'equipment')->where('status', '1')->pluck('name', 'id')->all(),
            'MeterReadUnits' =>  MeterReadUnits::select('id', 'name', 'symbol', 'unit_precision')->get()->toArray(),
            'accounts' =>  AssetAccounts::select('id', 'code', 'description')->get()->toArray(),
            'departments' =>  AssetChargeDepartment::select('id', 'code', 'description')->get()->toArray(),
            'supplies' =>  Supplies::select('id', 'code', 'description', 'name')->get()->toArray(),

        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEquipmentRequest $request): RedirectResponse
    {
        $input = $request->all();
        $equipment = Equipment::create($input);
        $equipmentId = $equipment->id;
        $equipmentName = $equipment->name;

        $create_location = new AssetAddress;
        $create_location->asset_type  = 'equipment';
        $create_location->asset_id    = $equipmentId;
        $create_location->has_parent = $request->equip_chkbox;
        $create_location->parent_id = $request->parent_id;
        $create_location->aisle = $request->aisle;
        $create_location->row = $request->row;
        $create_location->bin = $request->bin;
        $create_location->save();

        if ($request->equip_chkbox == 1) {
            if ($request->parent_id != '') {
                //save in relation table
                $equipment_relation = new EquipmentRelation;
                // $equipment_relation->facility_id = $request->parent_id;
                $equipment_relation->equipment_id = $request->parent_id;
                $equipment_relation->equipment_id = $equipmentId;
                $equipment_relation->save();

                $parent_address = AssetAddress::where('asset_type', 'equipment')->where('asset_id', $request->parent_id)->latest()->first();
                    if ($parent_address) {

                        $create_location->address = $parent_address->address;

                    } else {

                        $create_location->address = json_encode($request->add_address);

                    }
            }/*else {

                    $create_location->address = json_encode($input['contact']);
                }*/
        }
        /*else {

            $create_location->address = json_encode($input['contact']);

        }*/
        $create_location->save();
        if ($request->equip_chkbox == 0) {
            if ($request->parent_id != '') {
                //save in relation table
                $equipment_relation = new EquipmentRelation;
                $equipment_relation->parent_id = $request->parent_id;
                $equipment_relation->child_id = $equipmentId;
                $equipment_relation->save();
            }
        }

        $create_genInfo = new AssetGeneralInfo;
        $create_genInfo->asset_type  = 'equipment';
        $create_genInfo->asset_id    = $equipmentId;
        $create_genInfo->accounts_id = $request->account;
        $create_genInfo->barcode = $request->barcode;
        $create_genInfo->charge_department_id = $request->department;
        $create_genInfo->make = $request->make;
        $create_genInfo->model = $request->model;
        $create_genInfo->serial_number = $request->serial_number;
        $create_genInfo->unspc_code = $request->unspc_code;
        $create_genInfo->notes = $request->notes;
        $create_genInfo->save();
        if ($request->supplies || $request->quantity) {
            $create_supplieslog = new AssetPartSuppliesLog;
            $create_supplieslog->asset_type  = 'equipment';
            $create_supplieslog->asset_id    = $equipmentId;
            $create_supplieslog->part_supply_id = $request->supplies;
            $create_supplieslog->quantity = $request->quantity;
            $create_supplieslog->submitted_by = auth()->user()->id;
            $create_supplieslog->save();
        }
        if ($request->meter_reading) {
            $create_meterRead = new MeterReadings;
            $create_meterRead->asset_type  = 'equipment';
            $create_meterRead->asset_id    = $equipmentId;
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
                $destinationPath = public_path('Equipment/EquipmentId_' . $equipmentId);

                // Move the file to the destination
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true, true);
                }
                $file->move($destinationPath, $fileNameToStore);

                // Create a Certification record
                $create_doc = new AssetFiles;
                $create_doc->asset_type  = 'equipment';
                $create_doc->asset_id    = $equipmentId;
                $create_doc->name = $fileNameToStore;
                $create_doc->url = ('public/Equipment/EquipmentId_' . $equipmentId . '/' . $fileNameToStore);

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
        return redirect()->route('equipments.edit', $equipmentId)
            ->withSuccess('New equipment is added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipment $equipment): View
    {
        $equipment->load([
            'assetAddress',
            'assetGeneralInfo',
            'assetPartSuppliesLog',
            'meterReadings',
            'assetFiles',
            'equipmentRelation'
        ]);
        $data['countries'] = Country::get(["name", "id"]);
        $data['states'] = State::get(["name", "id"]);
        $data['cities'] = City::get(["name", "id"]);
        return view('equipments.show', $data, [
            'roles' => Role::pluck('name')->all(),
            'positions' => Position::pluck('name', 'id')->all(),
            'facilities' => Facility::pluck('name', 'id')->all(),
            'equipments' => Equipment::pluck('name', 'id')->all(),
            'categories' => AssetCategory::where('type', 'equipment')->where('status', '1')->pluck('name', 'id')->all(),
            'MeterReadUnits' =>  MeterReadUnits::select('id', 'name', 'symbol', 'unit_precision')->where('status', '1')->get()->toArray(),
            'accounts' =>  AssetAccounts::select('id', 'code', 'description')->get()->toArray(),
            'departments' =>  AssetChargeDepartment::select('id', 'code', 'description')->get()->toArray(),
            'supplies' =>  Supplies::select('id', 'code', 'description', 'name')->get()->toArray(),
            'equipment' => $equipment
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipment $equipment): View
    {
        // Eager load related data
        $equipment->load([
            'assetAddress',
            'assetGeneralInfo',
            'assetPartSuppliesLog',
            'meterReadings',
            'assetFiles',
            'equipmentRelation'
        ]);
        $data['countries'] = Country::get(["name", "id"]);
        return view('equipments.edit', $data, [
            'roles' => Role::pluck('name')->all(),
            'positions' => Position::pluck('name', 'id')->all(),
            'facilities' => Facility::pluck('name', 'id')->all(),
            'equipments' => Equipment::pluck('name', 'id')->all(),
            'categories' => AssetCategory::where('type', 'equipment')->where('status', '1')->pluck('name', 'id')->all(),
            'MeterReadUnits' =>  MeterReadUnits::select('id', 'name', 'symbol', 'unit_precision')->where('status', '1')->get()->toArray(),
            'accounts' =>  AssetAccounts::select('id', 'code', 'description')->get()->toArray(),
            'departments' =>  AssetChargeDepartment::select('id', 'code', 'description')->get()->toArray(),
            'supplies' =>  Supplies::select('id', 'code', 'description', 'name')->get()->toArray(),
            'equiprelation' =>  EquipmentRelation::select('id', 'parent_id', 'child_id')->where('child_id', $equipment->id)->get()->toArray(),
            'equipment' => $equipment
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEquipmentRequest $request, Equipment $equipment): RedirectResponse
    {
        // echo 'hi dilip= ' . auth()->user()->id;
        // exit();
        // Update equipment details
        $input = $request->all();
        $equipment->update($request->all());
        $equipmentId = $equipment->id;
        $equipmentName = $equipment->name;
        $equipmentAddress = AssetAddress::where('asset_type', 'equipment')->where('asset_id', $equipment->id)->first();
        if ($equipmentAddress) {
            $equipmentAddress->has_parent = $request->equip_chkbox;
            $equipmentAddress->parent_id = $request->parent_id;

            /*equip_checkbox check starts here*/
            if ($request->equip_chkbox == 1) {
                if ($request->parent_id != '') {
                    //update in relation table
                    $equipment_relation =  EquipmentRelation::where('child_id', $equipmentId)->first();
                    if ($equipment_relation) {
                        $equipment_relation->parent_id = $request->parent_id;
                        $equipment_relation->child_id = $equipmentId;
                        $equipment_relation->save();
                    }
                    else {
                        $equipment_relation = new EquipmentRelation;
                        $equipment_relation->parent_id = $request->parent_id;
                        $equipment_relation->child_id = $equipmentId;
                        $equipment_relation->save();
                    }

                    $parentAddress = AssetAddress::where('asset_type', 'equipment')->where('asset_id', $request->parent_id)->latest()->first();
                    if ($parentAddress) {
                        $EquipmentAddress->address = $parentAddress->address;
                    }
                    else {
                        $EquipmentAddress->address = json_encode($request->add_address);
                    }
                }
            /*}
            equip_checkbox check ends here*/

            /*$equipmentAddress->aisle = $request->aisle;
            $equipmentAddress->row = $request->row;
            $equipmentAddress->bin = $request->bin;
            $equipmentAddress->save();
        } else {
            $equipmentAddress = new AssetAddress;
            $equipmentAddress->asset_type = 'equipment';
            $equipmentAddress->asset_id = $equipmentId;
            $equipmentAddress->has_parent = $request->equip_chkbox;
            $equipmentAddress->parent_id = $request->parent_id;
            $equipmentAddress->aisle = $request->aisle;
            $equipmentAddress->row = $request->row;
            $equipmentAddress->bin = $request->bin;
            $equipmentAddress->save();
        }
        if ($request->equip_chkbox == 1) {
            if ($request->parent_id != '') {
                //save in relation table
                $equipment_relation = EquipmentRelation::where('equipment_id', $equipmentId)->first();
                if ($equipment_relation) {
                    $equipment_relation->facility_id = $request->parent_id;
                    // $equipment_relation->equipment_id = $equipmentId;
                    $equipment_relation->save();
                }*/
                 else {
                    // $equipment_relation = new EquipmentRelation;
                    $equipment_relation = EquipmentRelation::where('equipment_id', $equipmentId)->first();
                    /*Check equipment relation start*/
                    if ($equipment_relation) {
                        $equipment_relation->parent_id = $request->parent_id;
                        $equipment_relation->child_id = $equipmentId;
                        $equipment_relation->save();
                    }
                    else {
                        $equipment_relation = new FacilityEquiupmentRelation;
                        $equipment_relation->parent_id = $request->parent_id;
                        $equipment_relation->child_id = $equipmentId;
                        $equipment_relation->save();
                    }

                    $equipmentAddress->address = json_encode($request->add_address);

                    /*Check equipment relation end*/
                    
                    
                }
            }

            else {
                if ($request->parent_id) {
                    $equipment_relation =  FacilityEquiupmentRelation::where('child_id', $equipmentId)->first();
                    $equipment_relation->parent_id = $request->parent_id;
                    $equipment_relation->save();
                    $equipmentAddress->address = json_encode($request->contact);
                }
            }
            $equipmentAddress->save();
        }
        else {

            $create_location = new AssetAddress;
            $create_location->asset_type  = 'equipment';
            $create_location->asset_id    = $equipmentId;
            $create_location->has_parent = $request->faci_chkbox;
            $create_location->parent_id = $request->parent_id;

        if ($request->equip_chkbox == 1) {
            if ($request->parent_id != '') {
                //save in relation table
                $equipment_relation = new EquipmentRelation;
                $equipment_relation->parent_id = $request->parent_id;
                $equipment_relation->child_id = $equipmentId;
                $equipment_relation->save();

                $parent_address = AssetAddress::where('asset_type', 'equipment')->where('asset_id', $request->parent_id)->latest()->first();

                if ($parent_address) {
                    $create_location->address = $parent_address->address;
                } else {
                   $create_location->address = json_encode($request->add_address);  
                }
            }
            else {
                    $create_location->address = json_encode($input['contact']);
                }
        }
        else {
                $create_location->address = json_encode($input['contact']);
            }
        }

        // Update general information
        $equipmentGeneralInfo = AssetGeneralInfo::where('asset_type', 'equipment')->where('asset_id', $equipment->id)->first();
        if ($equipmentGeneralInfo) {
            $equipmentGeneralInfo->accounts_id = $request->account;
            $equipmentGeneralInfo->barcode = $request->barcode;
            $equipmentGeneralInfo->charge_department_id = $request->department;
            $equipmentGeneralInfo->make = $request->make;
            $equipmentGeneralInfo->model = $request->model;
            $equipmentGeneralInfo->serial_number = $request->serial_number;
            $equipmentGeneralInfo->unspc_code = $request->unspc_code;
            $equipmentGeneralInfo->notes = $request->notes;
            $equipmentGeneralInfo->save();
        }

        // Update part supplies log
        if ($request->supplies || $request->quantity) {
            AssetPartSuppliesLog::create([
                'asset_type' => 'equipment',
                'asset_id' => $equipment->id,
                'part_supply_id' => $request->supplies,
                'quantity' => $request->quantity,
                'submitted_by' => auth()->user()->id
            ]);
        }

        // Update meter readings if provided
        if ($request->meter_reading) {
            MeterReadings::create([
                'asset_type' => 'equipment',
                'asset_id' => $equipment->id,
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
                $destinationPath = public_path('Equipment/EquipmentId_' . $equipmentId);

                // Move the file to the destination
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true, true);
                }
                $file->move($destinationPath, $fileNameToStore);

                // Create a Certification record
                $create_doc = new AssetFiles;
                $create_doc->asset_type  = 'equipment';
                $create_doc->asset_id    = $equipmentId;
                $create_doc->name = $fileNameToStore;
                $create_doc->url = ('public/Equipment/EquipmentId_' . $equipmentId . '/' . $fileNameToStore);

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

        return redirect()->back()->withSuccess('Equipment is updated successfully.')->withInput(['tab' => 'Galleries']);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipment $equipment): RedirectResponse
    {
        //do not delete items permanently, instead update is_delete column
        $equipment->delete();
        return redirect()->route('equipments.index')
            ->withSuccess('Equipment is deleted successfully.');
    }
    public function getEquipParentAddress($id)
    {
        $parentEquipment = Equipment::find($id);
        // Retrieve the facility ID associated with the equipment
        // $equipmentId = EquipmentRelation::where('equipment_id', $id)->value('facility_id');

        if (!$parentEquipment) {
            // If no facility ID is found, return an empty response
            return response()->json(['error' => 'No parent facility found for the equipment'], 404);
        }

        // Traverse upward until reaching the top-level parent facility
        $topParentEquipmentId = $this->getTopParentFacilityId($equipmentId);

        if (!$topParentEquipmentId) {
            // If no top-level parent facility is found, return an empty response
            return response()->json(['error' => 'No top-level parent facility found'], 404);
        }

        // Retrieve the latest address associated with the top-level parent facility
        $latestAddress = AssetAddress::where('asset_id', $topParentEquipmentId)
            ->where('asset_id', $id)
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
     * @param int $equipmentId
     * @return int|null
     */
    protected function getTopParentFacilityId($equipmentId)
    {
        // Check if the current facility ID is associated with a facility
        $isFacility = Facility::where('id', $equipmentId)->exists();

        if ($isFacility) {
            // If the current ID is associated with a facility, return it
            return $equipmentId;
        }

        // Retrieve the parent equipment's parent facility
        $parentEquipment = EquipmentRelation::where('child_id', $equipmentId)->first();

        if ($parentEquipment) {
            // If the current ID is associated with an equipment, check its parent recursively
            return $this->getTopParentFacilityId($parentEquipment->parent_id);
        }

        // If no parent equipment is found, return null (no top-level parent facility)
        return null;
    }

    // public function getEquipParentAddress($id)
    // {
    //     // Find the parent equipment by its ID
    //     $parentEquipment = Equipment::find($id);

    //     if (!$parentEquipment) {
    //         // If the parent equipment does not exist, return an empty response
    //         return response()->json(['error' => 'Parent equipment not found'], 404);
    //     }

    //     // Initialize an array to store the parent facility IDs
    //     $parentEquipmentIds = [];

    //     // Start with the current equipment ID
    //     $currentEquipmentId = $id;

    //     // Loop through the chain of equipment relationships until we find the top-level parent
    //     do {
    //         // Find the current equipment's parent facility
    //         $facilityEquip = EquipmentRelation::where('equipment_id', $currentEquipmentId)->first();

    //         if ($facilityEquip) {
    //             // Add the facility ID to the list of parent facility IDs
    //             $parentEquipmentIds[] = $facilityEquip->facility_id;

    //             // Update the current equipment ID to its parent equipment ID
    //             $currentEquipmentId = $facilityEquip->parent_id;
    //         } else {
    //             // If there is no parent facility for the current equipment, break out of the loop
    //             break;
    //         }
    //     } while ($facilityEquip);

    //     // Retrieve the latest address associated with the top-level parent facility
    //     $latestAddress = AssetAddress::whereIn('asset_id', $parentEquipmentIds)
    //         ->where('asset_type', 'facility')
    //         ->latest()
    //         ->first();

    //     if ($latestAddress) {
    //         // Decode the JSON address data
    //         $addressData = json_decode($latestAddress->address, true);

    //         if ($addressData && isset($addressData['address'])) {
    //             // Retrieve the names of the country, state, and city
    //             $countryName = $addressData['country'] ? Country::find($addressData['country'])->name : null;
    //             $stateName = $addressData['state'] ? State::find($addressData['state'])->name : null;
    //             $cityName = $addressData['city'] ? City::find($addressData['city'])->name : null;

    //             // Return all necessary data including address, country, state, and city names
    //             return response()->json([
    //                 'address' => $addressData['address'],
    //                 'country' => $countryName,
    //                 'state' => $stateName,
    //                 'city' => $cityName,
    //                 'postcode' => $addressData['postcode'] ?? null,
    //             ]);
    //         }
    //     }

    //     // If no address is found or address data is invalid, return an empty response
    //     return response()->json(['address' => null]);
    // }

    // public function getEquipParentAddress($id)
    // {
    //     // Find the parent equipment by its ID
    //     $parentEquipment = Equipment::find($id);

    //     if (!$parentEquipment) {
    //         // If the parent equipment does not exist, return an empty response
    //         return response()->json(['error' => 'Parent equipment not found'], 404);
    //     }
    //     $facilityequip = EquipmentRelation::where('equipment_id', $id)->first();
    //     $equipmentId = $facilityequip->facility_id;
    //     // Retrieve the latest address associated with the parent facility
    //     $latestAddress = AssetAddress::where('asset_type', 'facility')
    //         ->where('asset_id', $equipmentId)
    //         ->latest()
    //         ->first();

    //     if ($latestAddress) {
    //         // Decode the JSON address data
    //         $addressData = json_decode($latestAddress->address, true);
    //         // If decoding is successful and 'address' key exists
    //         if ($addressData && isset($addressData['address'])) {
    //             // Retrieve the names of the country, state, and city
    //             $countryName = $addressData['country'] ? Country::find($addressData['country'])->name : null;
    //             $stateName = $addressData['state'] ? State::find($addressData['state'])->name : null;
    //             $cityName = $addressData['city'] ? City::find($addressData['city'])->name : null;

    //             // Return all necessary data including address, country, state, and city names
    //             return response()->json([
    //                 'address' => $addressData['address'],
    //                 'country' => $countryName,
    //                 'state' => $stateName,
    //                 'city' => $cityName,
    //                 'postcode' => $addressData['postcode'] ?? null,
    //             ]);
    //         }
    //     }
    //     // If no address is found or address data is invalid, return an empty response
    //     return response()->json(['address' => null]);
    // }
    public function saveequipparts(Request $request)
    {
        // Validate the request data
        $request->validate([
            'supplies_id' => 'required',
            'quantity' => 'nullable|numeric|min:0',
        ]);
        // Extract the request data
        $log_id = $request->input('log_id');
        $supplies_id = $request->input('supplies_id');
        $quantity = $request->input('quantity');
        // Update part supplies log
        $equipmentPartsUpdate = AssetPartSuppliesLog::where('id', $log_id);
        if (empty($supplies_id) || empty($quantity)) {
            return response()->json([
                'error' => 'Validation failed',
                'message' => 'Please fix the following errors',
                'errors' => [
                    'supplies_id' => ['The supplies  field is required.'],
                    'quantity.min' => ['The quantity field must be a positive number.']
                ]
            ], 422);
        }
        // Check if supplies_id and quantity are not empty or null
        $equipmentPartsUpdate->update([
            'part_supply_id' => $supplies_id,
            'quantity' => $quantity,
            'submitted_by' => auth()->user()->id,
        ]);
        // Return success response
        return response()->json(['success' => 'Records saved successfully']);
    }
    public function destroyequipparts($equippartsID)
    {
        $equippartsIDdata = AssetPartSuppliesLog::where('id', $equippartsID)->first();
        $equippartsIDdata->delete();
        return response()->json(['success' => 'Record deleted successfully']);
    }
    public function saveequipmeter(Request $request)
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
        $equipmentMetersUpdate = MeterReadings::where('id', $log_id);
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
        $equipmentMetersUpdate->update([
            'meter_units_id' => $meterunit_id,
            'reading_value' => $meterreading,
            'submitted_by' => auth()->user()->id,
        ]);
        // Return success response
        return response()->json(['success' => 'Records saved successfully']);
    }
    public function destroyequipmeter($equipmeterID)
    {
        $equipmeterIDdata = MeterReadings::where('id', $equipmeterID)->first();
        $equipmeterIDdata->delete();
        return response()->json(['success' => 'Record deleted successfully']);
    }
    public function equipdocs(Request $request)
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
        $equipmentDocsUpdate = AssetFiles::where('af_id', $log_id);
        if (empty($docsName)) {
            return response()->json([
                'error' => 'Validation failed',
                'message' => 'Please fix the following errors',
                'errors' => [
                    'docsName' => ['The name field is required.'],
                ]
            ], 422);
        }
        $equipmentDocsUpdate->update([
            'name' => $docsName,
            'description' => $docsDescription,
            'submitted_by' => auth()->user()->id,
        ]);
        // Return success response
        return response()->json(['success' => 'Records saved successfully']);
    }
    public function destroyequipdocs($equipdocsID)
    {
        $equipdocsIDdata = AssetFiles::where('af_id', $equipdocsID)->first();
        $equipdocsIDdata->delete();
        return response()->json(['success' => 'Record deleted successfully']);
    }
}
