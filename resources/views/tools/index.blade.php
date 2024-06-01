@extends('layouts.app')
@section('content')
    <!-- Begin Page Content -->
    <div class="page-content container">
        <div class="wrapper">
            <div class="row align-items-center">
                <div class="col-md-9">
                    <div class="page-header">
                        <h3>Tools List</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    @can('create-tools')
                        <a href="{{ route('tools.create') }}" class="btn btn-primary float-end"><i class="bi bi-plus-circle"></i>
                            Add New
                            Tool</a>
                    @endcan
                </div>
            </div>
            <div class="whitebox">
                <table id="ToolList" class="table table-striped nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col">S#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Code</th>
                            <th scope="col">Status</th>
                            <th scope="col">Asset Location</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    
                </table>
            </div>
        </div>
    </div>
    @push('javascript')
        <script>
            const positions = @json($tools);

            // Map the sorted positions to organisationData
            const organisationData = positions.map(position => ({
                tt_key: position.id,
                tt_parent: position.parent_id ? position.parent_id : 0,
                name: position.name,
                status: position.status == 1 ?
                    '<input type="button" class="tool_status btn btn-success" value="Active">' :
                    '<input type="button" class="tool_status btn btn-secondary" value="Disabled">',
                action: `<form id="deleteForm${position.id}" action="{{ route('tools.destroy', '') }}/${position.id}" method="post">
                @csrf
                @method('DELETE')
                <a href="{{ url('tools') }}/${position.id}/edit" class="link-primary"><i class="fa-regular fa-pen-to-square"></i></a>
                <button type="submit" class="link-danger" onclick="return confirm('Do you want to delete this tool?');"><i class="fa-solid fa-trash-can"></i></button>
            </form>`
            }));
            console.log(organisationData);
            // Initializing TreeTable with the extracted data
            $('#ToolList').treeTable({
                data: organisationData,
                columns: [
                    {data: "name"},
                    {data: "name"},
                    {data: "status"},
                    {
                        data: "action",
                        render: function(data, type, row) {
                            return row.action;
                        }
                    }
                ],
                order: [[1, 'asc']],
            });
        </script>
    @endpush
@endsection
