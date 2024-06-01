@extends('layouts.app')
@section('content')

<!-- Begin Page Content -->
<div class="page-content container">
    <div class="wrapper">
        <div class="row align-items-center">
            <div class="col-md-9">
                <div class="page-header">
                    <h3>Assets List</h3>
                </div>
            </div>
            <div class="col-md-3">
                <a data-bs-toggle="modal" data-bs-target="#CreateAssetModal" class="btn btn-primary float-end" style="cursor:pointer"><i class="bi bi-plus-circle"></i> Add New
                    Asset</a>
            </div>
        </div>
        <div class="whitebox">
            <table id="ProductsList" class="table table-striped nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th scope="col">Hierarchy</th>
                        <th scope="col">Name</th>
                        <th scope="col">Category</th>
                        <th scope="col">Status</th>
                        {{-- <th scope="col">Category-Location</th> --}}
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($facilities as $facility)
                    <tr>
                        <td>{{ $facility['name'] }}</td>
                        <td>{{ $facility['name'] }}</td>
                        <td>{{ 'Facility' }}</td>
                        <td>{{ $facility['status'] == '1' ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <form action="{{ route('facilities.destroy', $facility['id']) }}" method="post">
                                @csrf
                                @method('DELETE')

                                <a href="{{ route('facilities.show', $facility['id']) }}" class="btn btn-warning btn-sm"><i class="bi bi-eye"></i> </a>

                                @can('edit-facility')
                                <a href="{{ route('facilities.edit', $facility['id']) }}" class="link-primary"><i class="fa-regular fa-pen-to-square"></i></a>
                                @endcan

                                @can('delete-facility')
                                <button type="submit" class="link-danger" onclick="return confirm('Do you want to delete this facility?');"><i class="fa-solid fa-trash-can"></i></button>
                                @endcan
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    {{-- @foreach ($equipments as $equipment)
                            <tr>
                                <td>{{ $equipment->name }}</td>
                    <td>{{ $equipment->name }}</td>
                    <td>{{ 'Equipment' }}</td>
                    <td>{{ $equipment->status == '1' ? 'Active' : 'Inactive' }}</td>
                    {{-- <td>
                                    @if ($equipment->assetAddress && $equipment->assetAddress->parent_id && ($parentFacility = $facilities->where('id', $equipment->assetAddress->parent_id)->first()))
                                        {{ $parentFacility->name }}
                    @endif
                    </td> --}}
                    {{-- <td>
                                    <form action="{{ route('equipments.destroy', $equipment->id) }}" method="post">
                    @csrf
                    @method('DELETE')

                    <a href="{{ route('equipments.show', $equipment->id) }}" class="btn btn-warning btn-sm"><i class="bi bi-eye"></i> </a>

                    @can('edit-equipment')
                    <a href="{{ route('equipments.edit', $equipment->id) }}" class="link-primary"><i class="fa-regular fa-pen-to-square"></i></a>
                    @endcan

                    @can('delete-equipment')
                    <button type="submit" class="link-danger" onclick="return confirm('Do you want to delete this equipment?');"><i class="fa-solid fa-trash-can"></i></button>
                    @endcan
                    </form>
                    </td>
                    </tr>
                    @endforeach --}}
                    {{-- @foreach ($tools as $tool)
                            <tr>
                                <td>{{ $tool->name }}</td>
                    <td>{{ $tool->name }}</td>
                    <td>{{ 'Tool' }}</td>
                    <td>{{ $tool->status == '1' ? 'Active' : 'Inactive' }}</td> --}}
                    {{-- <td>
                                    @if ($tool->assetAddress && $tool->assetAddress->parent_id && ($parentFacility = $facilities->where('id', $tool->assetAddress->parent_id)->first()))
                                        {{ $parentFacility->name }}
                    @endif
                    </td> --}}
                    {{-- <td>
                                    <form action="{{ route('tools.destroy', $tool->id) }}" method="post">
                    @csrf
                    @method('DELETE')

                    <a href="{{ route('tools.show', $tool->id) }}" class="btn btn-warning btn-sm"><i class="bi bi-eye"></i> </a>

                    @can('edit-tools')
                    <a href="{{ route('tools.edit', $tool->id) }}" class="link-primary"><i class="fa-regular fa-pen-to-square"></i></a>
                    @endcan

                    @can('delete-tools')
                    <button type="submit" class="link-danger" onclick="return confirm('Do you want to delete this tool ?');"><i class="fa-solid fa-trash-can"></i></button>
                    @endcan
                    </form>
                    </td>
                    </tr>
                    @endforeach --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="CreateAssetModal" data-bs-keyboard="false" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <h6 class="modal-title">Create New Asset</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i></button>
                <div class="row align-items-center justify-content-center">
                    @canany(['read-facility', 'create-facility', 'edit-facility', 'delete-facility'])
                    <div class="col-md-6">
                        <a href="{{ route('facilities.create') }}" class="border-purple">
                            <div class="d-flex">
                                <span class="me-2"><img src="{{ asset('img/location_icon.png') }}" alt="img" class="img-fluid"></span>
                                <span class="mt-2">Locations or Facilities</span>
                            </div>
                        </a>
                    </div>
                    @endcanany
                    @canany(['read-equipment', 'create-equipment', 'edit-equipment', 'delete-equipment'])
                    <div class="col-md-6">

                        <a href="{{ route('equipments.create') }}" class="border-purple">
                            <div class="d-flex">
                                <span class="me-2"><img src="{{ asset('img/machine_icon.png') }}" alt="img" class="img-fluid"></span>
                                <span class="mt-2">Equipment or Machines</span>
                            </div>
                        </a>
                    </div>
                    @endcanany
                    @canany(['read-tools', 'create-tools', 'edit-tools', 'delete-tools'])
                    <div class="col-md-6">
                        <a href="{{ route('tools.create') }}" class="border-purple">
                            <div class="d-flex">
                                <span class="me-2"><img src="{{ asset('img/tool_icon.png') }}" alt="img" class="img-fluid"></span>
                                <span class="mt-4">Tools</span>
                            </div>
                        </a>
                    </div>
                    @endcanany
                </div>
            </div>
        </div>
    </div>
</div>
@endsection