@extends('layouts.app')
@push('css')
@endpush
@section('content')
    <!-- Begin Page Content -->
    <div class="page-content container">
        <div class="wrapper">
            <div class="row align-items-center">
                <div class="col-md-9">
                    <div class="page-header">
                        <h3>Facilities List</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    @can('create-facility')
                        <a href="{{ route('facilities.create') }}" class="btn btn-primary float-end"><i
                                class="bi bi-plus-circle"></i>
                            Add New
                            Facility</a>
                    @endcan
                </div>
            </div>
            <div class="whitebox">
                <table id="FacilityList" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @push('javascript')
        <script>
            const positions = @json($facilityRelations);

            // Map the sorted positions to organisationData
            const organisationData = positions.map(position => ({
                tt_key: position.id,
                tt_parent: position.parent_id ? position.parent_id : 0,
                name: position.name,
                status: position.status == 1 ?
                    '<input type="button" class="facility_status btn btn-success" value="Active">' :
                    '<input type="button" class="facility_status btn btn-secondary" value="Disabled">',
                action: `<form id="deleteForm${position.id}" action="{{ route('facilities.destroy', '') }}/${position.id}" method="post">
                @csrf
                @method('DELETE')
                <a href="{{ url('facilities') }}/${position.id}/edit" class="link-primary"><i class="fa-regular fa-pen-to-square"></i></a>
                <button type="submit" class="link-danger" onclick="return confirm('Do you want to delete this facility?');"><i class="fa-solid fa-trash-can"></i></button>
            </form>`
            }));
            console.log(organisationData);
            // Initializing TreeTable with the extracted data
            $('#FacilityList').treeTable({
                "data": organisationData,
                "columns": [{
                        "data": "name"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "status"
                    },
                    {
                        "data": "action",
                        "render": function(data, type, row) {
                            return row.action;
                        }
                    }
                ],
                order: [
                    [1, 'asc'],
                ],
                initialState: 'collapsed'
            });
        </script>
    @endpush
@endsection
