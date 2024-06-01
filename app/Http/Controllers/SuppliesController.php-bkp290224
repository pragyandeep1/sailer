<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreSuppliesRequest;
use App\Http\Requests\UpdateSuppliesRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\EquipmentRelation;
use App\Models\{AssetFiles, MeterReadings, AssetPartSuppliesLog, AssetGeneralInfo, AssetChargeDepartment, AssetAddress, AssetAccounts, Position, Facility, MeterReadUnits, AssetCategory, AssetInventory, AssetWarranty, Business, Country, State, City, Currency, Equipment, FacilityEquipmentRelation, FacilityRelation, Stock, Supplies, User};
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
            'assetFiles',
            'stocks',
            'inventories',
            'assetWarranty',

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
            'categories' => AssetCategory::where('type', 'supply')->where('status', '1')->pluck('name', 'id')->all(),
            'MeterReadUnits' =>  MeterReadUnits::select('id', 'name', 'symbol', 'unit_precision')->get()->toArray(),
            'accounts' =>  AssetAccounts::select('id', 'code', 'description')->get()->toArray(),
            'departments' =>  AssetChargeDepartment::select('id', 'code', 'description')->get()->toArray(),
            'supplies' =>  Supplies::select('id', 'code', 'description', 'name')->get()->toArray(),
            'currencies' => Currency::pluck('name', 'id')->all(),
            'businesses' => Business::pluck('name', 'id')->all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSuppliesRequest $request): RedirectResponse
    {
        $input = $request->all();
        $supply = Supplies::create($input);
        $supplyId = $supply->id;
        $supplyName = $supply->name;

        $create_genInfo = new AssetGeneralInfo;
        $create_genInfo->asset_type  = 'supply';
        $create_genInfo->asset_id    = $supplyId;
        $create_genInfo->accounts_id = $request->account;
        $create_genInfo->charge_department_id = $request->department;
        $create_genInfo->unspc_code = $request->unspc_code;
        $create_genInfo->barcode = $request->barcode;
        $create_genInfo->make = $request->make;
        $create_genInfo->model = $request->model;
        $create_genInfo->last_price = $request->last_price;
        $create_genInfo->total_stock = $request->total_stock;
        $create_genInfo->notes = $request->notes;
        $create_genInfo->save();

        //saving Inventories
        if ($request->inventory_received_to) {
            $supplyinventory = new AssetInventory;
            $supplyinventory->asset_type = 'supply';
            $supplyinventory->asset_id = $supplyId;
            $supplyinventory->purchased_from = $request->inventory_purchased_from;
            $supplyinventory->purchase_currency = $request->inventory_purchase_currency;
            $supplyinventory->date_ordered = $request->inventory_date_ordered;
            $supplyinventory->date_received = $request->inventory_date_received;
            $supplyinventory->parent_id = $request->inventory_received_to;
            $supplyinventory->quantity_received = $request->inventory_quantity_received;
            $supplyinventory->purchase_price_per_unit = $request->inventory_purchase_price_per_unit;
            $supplyinventory->purchase_price_total = $request->inventory_purchase_price_total;
            $supplyinventory->date_of_expiry = $request->inventory_date_of_expiry;
            $supplyinventory->save();
        }
        //saving Warranties
        $supplyWarranty = new AssetWarranty;
        $supplyWarranty->asset_type = 'supply';
        $supplyWarranty->asset_id = $supplyId;
        $supplyWarranty->warranty_type = $request->warranty_type;
        $supplyWarranty->provider = $request->provider;
        $supplyWarranty->warranty_usage_term_type = $request->warranty_usage_term_type;
        $supplyWarranty->expiry_date = $request->expiry_date;
        $supplyWarranty->meter_reading = $request->meter_reading;
        $supplyWarranty->meter_reading_units = $request->meter_read_units;
        $supplyWarranty->certificate_number = $request->certificate_number;
        $supplyWarranty->description = $request->warranty_description;
        $supplyWarranty->save();

        //saving Stocks
        if ($request->stocks_parent_facility) {
            $stock = new Stock;
            $stock->asset_type = 'supply';
            $stock->asset_id = $supplyId;
            $stock->parent_id = $request->stocks_parent_facility;
            $stock->location = json_encode($input['st_loc']);
            $stock->stocks_qty_on_hand = $request->stocks_qty_on_hand;
            $stock->stocks_min_qty = $request->stocks_min_qty;
            $stock->stocks_max_qty = $request->stocks_max_qty;
            $stock->save();
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
                $destinationPath = public_path('Supply/SupplyId_' . $supplyId);

                // Move the file to the destination
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true, true);
                }
                $file->move($destinationPath, $fileNameToStore);

                // Create a Certification record
                $create_doc = new AssetFiles;
                $create_doc->asset_type  = 'supply';
                $create_doc->asset_id    = $supplyId;
                $create_doc->name = $fileNameToStore;
                $create_doc->url = ('public/Supply/SupplyId_' . $supplyId . '/' . $fileNameToStore);

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
        return redirect()->route('supplies.edit', $supplyId)
            ->withSuccess('New supply is added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplies $supplies): View
    {
        $supplies->load([
            'assetAddress',
            'assetGeneralInfo',
            'assetPartSuppliesLog',
            'meterReadings',
            'assetFiles',
            'stocks',
            'inventories',
            'assetWarranty'
        ]);
        $data['countries'] = Country::get(["name", "id"]);
        $data['states'] = State::get(["name", "id"]);
        $data['cities'] = City::get(["name", "id"]);
        return view('supplies.show', $data, [
            'roles' => Role::pluck('name')->all(),
            'positions' => Position::pluck('name', 'id')->all(),
            'users' => User::pluck('name', 'id')->all(),
            'facilities' => Facility::pluck('name', 'id')->all(),
            'equipments' => Equipment::pluck('name', 'id')->all(),
            'categories' => AssetCategory::where('type', 'equipment')->where('status', '1')->pluck('name', 'id')->all(),
            'MeterReadUnits' =>  MeterReadUnits::select('id', 'name', 'symbol', 'unit_precision')->get()->toArray(),
            'accounts' =>  AssetAccounts::select('id', 'code', 'description')->get()->toArray(),
            'departments' =>  AssetChargeDepartment::select('id', 'code', 'description')->get()->toArray(),
            // 'supplies' =>  Supplies::select('id', 'code', 'description', 'name')->get()->toArray(),
            'supplies' => $supplies
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplies $supply): View
    {
        // Eager load related data
        $supply->load([
            'assetAddress',
            'assetGeneralInfo',
            'assetPartSuppliesLog',
            'meterReadings',
            'assetFiles',
            'stocks',
            'inventories',
            'assetWarranty'

        ]);
        $stocks = Stock::where('asset_type', 'supply')->where('asset_id', $supply->id)->get();
        $totalStock = $stocks->sum('stocks_qty_on_hand');

        $data['countries'] = Country::get(["name", "id"]);
        return view('supplies.edit', $data, [
            'roles' => Role::pluck('name')->all(),
            'positions' => Position::pluck('name', 'id')->all(),
            'users' => User::pluck('name', 'id')->all(),
            'facilities' => Facility::pluck('name', 'id')->all(),
            'equipments' => Equipment::pluck('name', 'id')->all(),
            'categories' => AssetCategory::where('type', 'supply')->where('status', '1')->pluck('name', 'id')->all(),
            'MeterReadUnits' =>  MeterReadUnits::select('id', 'name', 'symbol', 'unit_precision')->get()->toArray(),
            'accounts' =>  AssetAccounts::select('id', 'code', 'description')->get()->toArray(),
            'supplies' =>  Supplies::select('id', 'code', 'description', 'name')->get()->toArray(),
            'currencies' => Currency::pluck('name', 'id')->all(),
            'businesses' => Business::pluck('name', 'id')->all(),
            'receivedTo' => Stock::where('asset_type', 'supply')->where('asset_id', $supply->id)->pluck('asset_id', 'id')->all(),
            'departments' =>  AssetChargeDepartment::select('id', 'code', 'description')->get()->toArray(),
            'totalStock' => $totalStock,
            'supply' => $supply
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSuppliesRequest $request, Supplies $supply): RedirectResponse
    {
        $input = $request->all();
        $supply->update($request->all());
        $supplyId = $supply->id;
        $supplyName = $supply->name;

        $update_genInfo = AssetGeneralInfo::where('asset_type', 'supply')->where('asset_id', $supply->id)->first();
        $update_genInfo->accounts_id = $request->account;
        $update_genInfo->charge_department_id = $request->department;
        $update_genInfo->unspc_code = $request->unspc_code;
        $update_genInfo->barcode = $request->barcode;
        $update_genInfo->make = $request->make;
        $update_genInfo->model = $request->model;
        $update_genInfo->last_price = $request->last_price;
        $update_genInfo->total_stock = $request->total_stock;
        $update_genInfo->notes = $request->notes;
        $update_genInfo->save();

        //saving Inventories
        // if (isset($request->inventory_received_to) && !empty($request->inventory_received_to)) {
        //     $assetStock = Stock::where('id', $request->inventory_received_to)->first();
        //     $inventory_quantity_received = $request->inventory_quantity_received;
        //     $assetStock->stocks_qty_on_hand += $inventory_quantity_received; // this code need more customization to show history and logs as per Fixx website
        //     $assetStock->save();
        //     $supplyinventory = new AssetInventory;
        //     $supplyinventory->asset_type = 'supply';
        //     $supplyinventory->asset_id = $supplyId;
        //     $supplyinventory->purchased_from = $request->inventory_purchased_from;
        //     $supplyinventory->purchase_currency = $request->inventory_purchase_currency;
        //     $supplyinventory->date_ordered = $request->inventory_date_ordered;
        //     $supplyinventory->date_received = $request->inventory_date_received;
        //     $supplyinventory->parent_id = $request->inventory_received_to;
        //     $supplyinventory->quantity_received = $request->inventory_quantity_received;
        //     $supplyinventory->purchase_price_per_unit = $request->inventory_purchase_price_per_unit;
        //     $supplyinventory->purchase_price_total = $request->inventory_purchase_price_total;
        //     $supplyinventory->date_of_expiry = $request->inventory_date_of_expiry;
        //     $supplyinventory->save();
        // } else {
        //     $supplyinventory = new AssetInventory;
        //     $supplyinventory->asset_type = 'supply';
        //     $supplyinventory->asset_id = $supplyId;
        //     $supplyinventory->purchased_from = $request->inventory_purchased_from;
        //     $supplyinventory->purchase_currency = $request->inventory_purchase_currency;
        //     $supplyinventory->date_ordered = $request->inventory_date_ordered;
        //     $supplyinventory->date_received = $request->inventory_date_received;
        //     $supplyinventory->parent_id = $request->inventory_received_to;
        //     $supplyinventory->quantity_received = $request->inventory_quantity_received;
        //     $supplyinventory->purchase_price_per_unit = $request->inventory_purchase_price_per_unit;
        //     $supplyinventory->purchase_price_total = $request->inventory_purchase_price_total;
        //     $supplyinventory->date_of_expiry = $request->inventory_date_of_expiry;
        //     $supplyinventory->save();
        // }
        //saving Warranties
        // $supplyWarranty = AssetWarranty::where('asset_type', 'supply')->where('asset_id', $supply->id)->first();
        // $supplyWarranty->warranty_type = $request->warranty_type;
        // $supplyWarranty->provider = $request->provider;
        // $supplyWarranty->warranty_usage_term_type = $request->warranty_usage_term_type;
        // $supplyWarranty->expiry_date = $request->expiry_date;
        // $supplyWarranty->meter_reading = $request->meter_reading;
        // $supplyWarranty->meter_reading_units = $request->meter_read_units;
        // $supplyWarranty->certificate_number = $request->certificate_number;
        // $supplyWarranty->description = $request->warranty_description;
        // $supplyWarranty->save();

        //saving Stocks
        if ($request->stocks_parent_facility) {
            $stock = new Stock;
            $stock->asset_type = 'supply';
            $stock->asset_id = $supplyId;
            $stock->parent_id = $request->stocks_parent_facility;
            $stock->location = json_encode($input['st_loc']);
            $stock->stocks_qty_on_hand = $request->stocks_qty_on_hand;
            $stock->stocks_min_qty = $request->stocks_min_qty;
            $stock->stocks_max_qty = $request->stocks_max_qty;
            $stock->save();
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
                $destinationPath = public_path('Supply/SupplyId_' . $supplyId);

                // Move the file to the destination
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true, true);
                }
                $file->move($destinationPath, $fileNameToStore);

                // Create a Certification record
                $create_doc = new AssetFiles;
                $create_doc->asset_type  = 'supply';
                $create_doc->asset_id    = $supplyId;
                $create_doc->name = $fileNameToStore;
                $create_doc->url = ('public/Supply/SupplyId_' . $supplyId . '/' . $fileNameToStore);

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
        return redirect()->route('supplies.edit', $supplyId)
            ->withSuccess('Supply is updated successfully.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplies $supply): RedirectResponse
    {
        //do not delete items permanently, instead update is_delete column
        $supply->delete();
        return redirect()->route('supplies.index')
            ->withSuccess('Supply is deleted successfully.');
    }
    public function saveStocks(Request $request)
    {
        // Validate the request data
        $request->validate([
            'stocks_parent_facility' => 'required',
        ]);

        // Save stocks
        if ($request->stocks_parent_facility) {
            $stock = new Stock;
            $stock->asset_type = 'supply';
            $stock->asset_id = $request->input('supply_id');
            $stock->parent_id = $request->input('stocks_parent_facility');
            $stock->location = json_encode($request->input('st_loc'));
            $stock->stocks_qty_on_hand = $request->stocks_qty_on_hand;
            $stock->stocks_min_qty = $request->stocks_min_qty;
            $stock->stocks_max_qty = $request->stocks_max_qty;
            $stock->save();

            // Return success response
            return response()->json(['message' => 'Stocks saved successfully']);
        }
        // Return error response if necessary
        return response()->json(['error' => 'Failed to save stocks'], 500);
    }
    public function getSupplyName(Request $request)
    {
        $stockId = $request->stockId;
        $stock = Stock::find($stockId);

        if ($stock) {
            $location = Facility::find($stock->parent_id);
            $supply = Supplies::find($stock->asset_id);

            if ($location && $supply) {
                $locationName = $location->name;
                $supplyName = $supply->name;
                $supplyCode = $supply->code;
                $message = $supplyName . ' (' . $supplyCode . ') at ' . $locationName;

                return response()->json([
                    'supplyName' => $message
                ]);
                // return response()->json([
                //     'supplyName' => $supplyName,
                //     'supplyCode' => $supplyCode,
                //     'locationName' => $locationName
                // ]);
            } else {
                return response()->json(['error' => 'Location or supply not found'], 404);
            }
        } else {
            return response()->json(['error' => 'Stock not found'], 404);
        }
    }
    public function saveInventories(Request $request)
    {
        // Validate the request data
        $request->validate([
            'inventory_received_to' => 'required',
        ]);

        // Save inventory
        if (isset($request->inventory_received_to) && !empty($request->inventory_received_to)) {
            $assetStock = Stock::where('id', $request->inventory_received_to)->first();
            $inventory_quantity_received = $request->inventory_quantity_received;
            $assetStock->stocks_qty_on_hand += $inventory_quantity_received; // this code need more customization to show history and logs as per Fixx website
            $assetStock->save();
            $supplyinventory = new AssetInventory;
            $supplyinventory->asset_type = 'supply';
            $supplyinventory->asset_id = $request->input('supply_id');
            $supplyinventory->purchased_from = $request->inventory_purchased_from;
            $supplyinventory->purchase_currency = $request->inventory_purchase_currency;
            $supplyinventory->date_ordered = $request->inventory_date_ordered;
            $supplyinventory->date_received = $request->inventory_date_received;
            $supplyinventory->parent_id = $request->inventory_received_to;
            $supplyinventory->quantity_received = $request->inventory_quantity_received;
            $supplyinventory->purchase_price_per_unit = $request->inventory_purchase_price_per_unit;
            $supplyinventory->purchase_price_total = $request->inventory_purchase_price_total;
            $supplyinventory->date_of_expiry = $request->inventory_date_of_expiry;
            $supplyinventory->save();

            // Return success response
            return response()->json(['message' => 'Inventory saved successfully']);
        }
        // Return error response if necessary
        return response()->json(['error' => 'Failed to save inventory'], 500);
    }
    public function saveWarranties(Request $request)
    {
        // Save warranty
        $supplyWarranty = new AssetWarranty;
        $supplyWarranty->asset_type = 'supply';
        $supplyWarranty->asset_id = $request->input('supply_id');
        $supplyWarranty->warranty_type = $request->warranty_type;
        $supplyWarranty->provider = $request->provider;
        $supplyWarranty->warranty_usage_term_type = $request->warranty_usage_term_type;
        $supplyWarranty->expiry_date = $request->expiry_date;
        $supplyWarranty->meter_reading = $request->meter_reading;
        $supplyWarranty->meter_reading_units = $request->meter_read_units;
        $supplyWarranty->certificate_number = $request->certificate_number;
        $supplyWarranty->description = $request->warranty_description;
        $supplyWarranty->save();

        // Return success response
        return response()->json(['message' => 'Warranty Certificate saved successfully']);
    }
}
