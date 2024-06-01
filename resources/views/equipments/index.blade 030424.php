@extends('layouts.app')
@section('content')
    <!-- Begin Page Content -->
    <div class="page-content container">
        <div class="wrapper">
            <div class="row align-items-center">
                <div class="col-md-9">
                    <div class="page-header">
                        <h3>Equipment List</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    @can('create-equipment')
                        <a href="{{ route('equipments.create') }}" class="btn btn-primary float-end"><i
                                class="bi bi-plus-circle"></i>
                            Add New
                            Equipment</a>
                    @endcan
                </div>
            </div>
            <div class="whitebox">
                <table id="EquipmentsList" class="table table-striped nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">S#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Hierarchy</th>
                            <th scope="col">Code</th>
                            <th scope="col">Status</th>
                            {{-- <th scope="col">Description</th> --}}
                            <th scope="col">Asset Location</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($equipments as $equipment)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $equipment->name }}</td>
                                <td></td>
                                <td>{{ $equipment->code }}</td>
                                <td>{{ $equipment->status == 1 ? 'Active' : 'Inactive' }}</td>
                                <td>
                                    @if (
                                        $equipment->assetAddress &&
                                            $equipment->assetAddress->parent_id &&
                                            ($parentFacility = $allfacilities->where('id', $equipment->assetAddress->parent_id)->first()))
                                        {{ $parentFacility->name }}
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('equipments.destroy', $equipment->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')

                                        {{-- <a href="{{ route('equipments.show', $equipment->id) }}"
                                            class="btn btn-warning btn-sm"><i class="bi bi-eye"></i> </a> --}}

                                        @can('edit-equipment')
                                            <a href="{{ route('equipments.edit', $equipment->id) }}" class="link-primary"><i
                                                    class="fa-regular fa-pen-to-square"></i></a>
                                        @endcan

                                        @can('delete-equipment')
                                            <button type="submit" class="link-danger"
                                                onclick="return confirm('Do you want to delete this asset?');"><i
                                                    class="fa-solid fa-trash-can"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <span class="text-danger">
                                    <strong>No Asset Found!</strong>
                                </span>
                            </td>
                            <td></td>
                            <td></td>
                        @endforelse
                    </tbody>
                </table>
                {{-- {{ $products->links() }} --}}
            </div>
        </div>
    </div>
@endsection
