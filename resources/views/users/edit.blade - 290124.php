@extends('layouts.app')

@section('content')
    <?php //print_r($UserInfo);
    $userInfoArray = json_decode($user->userInfo->contact_details, true);
    $CareerInfoArray = json_decode($user->CareerHistory, true);
    //print_r($userInfoArray);
    $current_date_time = \Carbon\Carbon::now();
    ?>
    <!-- Begin Page Content -->
    <div class="page-content container">
        <div class="wrapper">
            <div class="row align-items-center mb-3">
                <div class="col-md-9">
                    <div class="page-header">
                        <h3> Edit User @if (!empty($user->emp_id))
                                <span class="text-info"> <a href="{{ route('users.show', $user->id) }}"
                                        class="btn btn-warning btn-sm">{{ $user->emp_id }}</a><span>
                            @endif
                        </h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('users.index') }}" class="btn btn-primary float-end">
                        <i class="fa-solid fa-list me-1"></i>All Users
                    </a>
                </div>
            </div>
            <form action="{{ route('users.update', $user->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-2">
                    <ul class="nav nav-tabs" id="myTabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" aria-current="page" href="#userBasic">Basic
                                Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#userContact">Contact Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#userCareer">Career</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#userFiles">Files</a>
                        </li>
                    </ul>
                    <div class="col-md-6 col-lg-8">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="userBasic">
                                <div class="page-header">
                                    <h3>Basic Information</h3>
                                </div>
                                <div class="item_name">
                                    <div class="whitebox mb-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="name"
                                                    class="col-form-label text-md-end text-start">Name</label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror" id="name"
                                                    name="name" value="{{ $user->name }}">
                                                @if ($errors->has('name'))
                                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <label for="email" class="col-form-label text-md-end text-start">Email
                                                    Address</label>
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror" id="email"
                                                    name="email" value="{{ $user->email }}">
                                                @if ($errors->has('email'))
                                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="password"
                                                    class="col-form-label text-md-end text-start">Password</label>
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="password" name="password">
                                                @if ($errors->has('password'))
                                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <label for="password_confirmation"
                                                    class="col-form-label text-md-end text-start">Confirm
                                                    Password</label>
                                                <input type="password" class="form-control" id="password_confirmation"
                                                    name="password_confirmation">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="userContact">
                                <div class="page-header">
                                    <h3>Contact Details</h3>
                                </div>
                                <div class="item_name">
                                    <div class="whitebox mb-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="primary_ph"
                                                    class="col-form-label text-md-end text-start">Primary
                                                    Number</label>
                                                <input type="text"
                                                    class="form-control @error('primary_ph') is-invalid @enderror"
                                                    id="primary_ph" name="contact[primary_ph]"
                                                    value="{{ $userInfoArray['primary_ph'] }}">
                                                @if ($errors->has('primary_ph'))
                                                    <span class="text-danger">{{ $errors->first('primary_ph') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <label for="secondary_ph"
                                                    class="col-form-label text-md-end text-start">Secondary
                                                    number</label>
                                                <input type="text"
                                                    class="form-control @error('secondary_ph') is-invalid @enderror"
                                                    id="secondary_ph" name="contact[secondary_ph]"
                                                    value="{{ $userInfoArray['secondary_ph'] }}">
                                                @if ($errors->has('secondary_ph'))
                                                    <span class="text-danger">{{ $errors->first('secondary_ph') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="address"
                                                    class="col-form-label text-md-end text-start">Address</label>
                                                <input type="text"
                                                    class="form-control @error('address') is-invalid @enderror"
                                                    id="address" name="contact[address]"
                                                    value="{{ $userInfoArray['address'] }}">
                                                @if ($errors->has('address'))
                                                    <span class="text-danger">{{ $errors->first('address') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <label for="address2"
                                                    class="col-form-label text-md-end text-start">Apartment or
                                                    unit number (optional)</label>
                                                <input type="text"
                                                    class="form-control @error('address2') is-invalid @enderror"
                                                    id="address2" name="contact[address2]"
                                                    value="{{ $userInfoArray['address2'] }}">
                                                @if ($errors->has('address2'))
                                                    <span class="text-danger">{{ $errors->first('address2') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="country"
                                                    class="col-form-label text-md-end text-start">Country</label>
                                                <input type="hidden" id="FetchcountryId"
                                                    value="{{ $userInfoArray['country'] ?? '' }}">
                                                <select id="country-dd" name="contact[country]"
                                                    class="form-control single-select @error('country') is-invalid @enderror">
                                                    <option value="">--Select Country--</option>
                                                    @foreach ($countries as $data)
                                                        <option value="{{ $data->id }}"
                                                            {{ $userInfoArray['country'] == $data->id ? 'selected' : '' }}>
                                                            {{ $data->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('country'))
                                                    <span class="text-danger">{{ $errors->first('country') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <label for="state" class="col-form-label text-md-end text-start">State
                                                    or
                                                    province</label>
                                                <input type="hidden" id="FetchstateId"
                                                    value="{{ $userInfoArray['state'] ?? '' }}">
                                                <select id="state-dd" name="contact[state]"
                                                    class="form-control single-select @error('state') is-invalid @enderror">
                                                    @if (isset($userInfoArray['state']))
                                                        {{-- <option value="">HI state</option> --}}
                                                    @endif
                                                </select>
                                                @if ($errors->has('state'))
                                                    <span class="text-danger">{{ $errors->first('state') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="city"
                                                    class="col-form-label text-md-end text-start">City</label>
                                                <input type="hidden" id="FetchcityId" name="contact[city]"
                                                    value="{{ $userInfoArray['city'] ?? '' }}">
                                                <select id="city-dd" name="contact[city]"
                                                    class="form-control single-select @error('city') is-invalid @enderror">
                                                    @if (isset($userInfoArray['city']))
                                                        {{-- <option>HI city</option> --}}
                                                    @endif
                                                </select>
                                                @if ($errors->has('city'))
                                                    <span class="text-danger">{{ $errors->first('city') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <label for="postcode" class="col-form-label text-md-end text-start">Zip or
                                                    postal
                                                    code</label>
                                                <input type="text"
                                                    class="form-control @error('postcode') is-invalid @enderror"
                                                    id="postcode" name="contact[postcode]"
                                                    value="{{ $userInfoArray['postcode'] }}">
                                                @if ($errors->has('postcode'))
                                                    <span class="text-danger">{{ $errors->first('postcode') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="userCareer">
                                <div class="page-header">
                                    <h3>Career development</h3>
                                </div>
                                <?php //print_r($user->CareerHistory);
                                ?>
                                <div class="item_name">
                                    <div class="whitebox mb-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="positions" class="col-form-label text-md-end text-start">Job
                                                    Title</label>
                                                <input type="hidden" value="<?php echo $user->userInfo->job_title ? $user->userInfo->job_title : ''; ?>" name="old_positions">
                                                <select class="form-control @error('positions') is-invalid @enderror"
                                                    aria-label="Positions" id="positions" name="positions">
                                                    <option value="">Select</option>
                                                    @forelse ($positions as $id => $position)
                                                        {{-- @if ($position != 'Super Admin') --}}
                                                        <option value="{{ $id }}" {{-- {{ in_array($id, old('positions') ?? []) ? 'selected' : '' }}> --}}
                                                            {{ $user->userInfo->job_title == $id ? 'selected' : '' }}>
                                                            {{ $position }}
                                                        </option>
                                                        {{-- @endif --}}
                                                    @empty
                                                    @endforelse
                                                </select>
                                                @if ($errors->has('positions'))
                                                    <span class="text-danger">{{ $errors->first('positions') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="page-header mt-4">
                                                <h3>Career history</h3>
                                                <hr>
                                            </div>
                                            {{-- @forelse ($CareerInfoArray as  $position) --}}
                                            {{-- @if ($position != 'Super Admin') --}}
                                            {{-- @php print_r($CareerInfoArray) ; @endphp --}}
                                            @if (!empty($CareerInfoArray))
                                                @foreach ($CareerInfoArray as $index => $careerInfo)
                                                    @if ($careerInfo['prev_position'] != 0)
                                                        @php
                                                            $userPromoted = \App\Models\User::find($user->id);
                                                            $userPromoter = \App\Models\User::find($careerInfo['modified_by']);
                                                            // if ($careerInfo['prev_position'] != 0) {
                                                            $prevPosition = \App\Models\Position::find($careerInfo['prev_position']);
                                                            // $prevPositionName = $prevPosition->name;
                                                            // } else {
                                                            // $prevPositionName = 'created with'
                                                            // }
                                                            $currPosition = \App\Models\Position::find($careerInfo['curr_position']);
                                                            $actionType = $careerInfo['prev_position'] > $careerInfo['curr_position'] ? 'demoted' : 'promoted';
                                                        @endphp
                                                        <span>{{ $index }}. User {{ $userPromoted->name }} is
                                                            {{ $actionType }}
                                                            from {{ $prevPosition->name }} to {{ $currPosition->name }} on
                                                            {{ \Carbon\Carbon::parse($careerInfo['modified_on'])->format('jS F, Y h:i A') }}
                                                            by <a
                                                                href="{{ route('users.show', $careerInfo['modified_by']) }}"
                                                                target="_blank">{{ $userPromoter->name }}</a></span>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span>No career history found.</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="userFiles">
                                <div class="page-header">
                                    <h3>Certifications</h3>
                                </div>
                                <div class="item_name">
                                    <div class="whitebox mb-4">
                                        <div class="row">
                                            <div class="col-md-6 offset-md-4">
                                                {{-- <label for="files" class="col-form-label text-md-end text-start">Upload
                                            Documents</label> --}}
                                                <input type="file"
                                                    class="form-control @error('files') is-invalid @enderror"
                                                    id="files" name="files[]" multiple>
                                                @if ($errors->has('files'))
                                                    <span class="text-danger">{{ $errors->first('files') }}</span>
                                                @endif
                                            </div>
                                            {{-- <div class="col-md-6">
                                                <a class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#UploadFileModal">Upload Files</a>
                                            </div> --}}
                                            <?php //print_r($user->certifications)
                                            ?>
                                            @if ($user->certifications->isNotEmpty())
                                                <table id="UsersFilesList" class="display">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">S#</th>
                                                            <th scope="col">Name</th>
                                                            <th scope="col">Description</th>
                                                            <th scope="col">Type</th>
                                                            <th scope="col">Valid from</th>
                                                            <th scope="col">Valid to</th>
                                                            <th scope="col">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($user->certifications as $certificate)
                                                            <tr>
                                                                <th scope="row">{{ $loop->iteration }}</th>
                                                                <td>{{ $certificate->name }}</td>
                                                                <td>{{ $certificate->description }}</td>
                                                                <td>{{ $certificate->type }}</td>
                                                                <td>{{ $certificate->valid_from }}</td>
                                                                <td>{{ $certificate->valid_to }}</td>
                                                                <td><a href="{{ asset($certificate->url) }}"
                                                                        class="link-success me-2" target="_blank"
                                                                        title="View"><i
                                                                            class="fa-regular fa-eye"></i></a>
                                                                    <a data-bs-toggle="modal"
                                                                        data-bs-target="#UploadFileModal{{ $certificate->cf_id }}"
                                                                        class="link-primary"><i
                                                                            class="fa-regular fa-pen-to-square"></i></a>
                                                                    <button type="submit" class="link-danger"
                                                                        onclick="return confirm('Do you want to delete this document?');"><i
                                                                            class="fa-solid fa-trash-can"></i></button>
                                                                </td>
                                                            </tr>
                                                            {{-- view file modal --}}
                                                            <div class="modal fade"
                                                                id="UploadFileModal{{ $certificate->cf_id }}">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <!-- Modal Header -->
                                                                        <div class="modal-header">
                                                                            <h4 class="modal-title">{{ $user->name }}
                                                                                Documents</h4>
                                                                            <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"></button>
                                                                        </div>
                                                                        <!-- Modal body -->
                                                                        <div class="modal-body">
                                                                            <div class="whitebox mb-4">
                                                                                <form
                                                                                    id="certUpdateForm{{ $certificate->cf_id }}">
                                                                                    <div class="row">
                                                                                        <div class=""
                                                                                            style="position:relative;cursor:pointer;">
                                                                                            <label
                                                                                                for="name{{ $certificate->cf_id }}"
                                                                                                class="col-form-label text-md-end text-start">Name</label>
                                                                                            <input type="file"
                                                                                                style="
                                                                                                cursor: pointer;
                                                                                                width: 423px;
                                                                                                position: relative;
                                                                                                z-index: 4;
                                                                                                opacity:0;                                                                                        
                                                                                                margin-left: -40px;
                                                                                                padding: 0px;
                                                                                                "
                                                                                                onchange="getElementById('name{{ $certificate->cf_id }}').value=getFileName(this.value);">
                                                                                            <input type="text"
                                                                                                class="form-control"
                                                                                                id="name{{ $certificate->cf_id }}"
                                                                                                name="name"
                                                                                                value=" {{ $certificate->name }}"
                                                                                                readonly="true"
                                                                                                style="
                                                                                                cursor: pointer;
                                                                                                width: 260px;
                                                                                                position: absolute;
                                                                                                top: 5px;
                                                                                                left: 10px;
                                                                                                z-index: 2;
                                                                                            ">
                                                                                            <img src="{{ asset('public/img/upload-button.png') }}"
                                                                                                style="cursor: pointer;
                                                                                                position: absolute;
                                                                                                top: 5px;
                                                                                                right: 12px;
                                                                                                z-index: 1;
                                                                                                height: 43px;">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-md-12">
                                                                                            <label
                                                                                                for="description{{ $certificate->cf_id }}"
                                                                                                class="col-form-label text-md-end text-start">Description
                                                                                            </label>
                                                                                            <textarea class="form-control" id="description{{ $certificate->cf_id }}" name="description" rows="2">{{ $certificate->description }}</textarea>
                                                                                        </div>
                                                                                    </div>
                                                                                    {{-- <div class="row">
                                                                                        <div class="col-md-6">
                                                                                            <label
                                                                                                for="valid_from{{ $certificate->cf_id }}"
                                                                                                class="col-form-label text-md-end text-start">Valid
                                                                                                from</label>
                                                                                            <input type="date"
                                                                                                class="form-control"
                                                                                                id="valid_from{{ $certificate->cf_id }}"
                                                                                                name="valid_from"
                                                                                                value=" {{ $certificate->valid_from }}">
                                                                                        </div>
                                                                                        <div class="col-md-6">
                                                                                            <label
                                                                                                for="valid_to{{ $certificate->cf_id }}"
                                                                                                class="col-form-label text-md-end text-start">Valid
                                                                                                To
                                                                                            </label>
                                                                                            <input type="date"
                                                                                                class="form-control"
                                                                                                id="valid_to{{ $certificate->cf_id }}"
                                                                                                name="valid_to"
                                                                                                value="{{ $certificate->valid_to }}">
                                                                                        </div>
                                                                                    </div> --}}

                                                                                    <button
                                                                                        id="save{{ $certificate->cf_id }}"
                                                                                        class="btn btn-primary mt-3 float-end"
                                                                                        onclick="saveCertificate({{ $certificate->cf_id }})">
                                                                                        Save
                                                                                    </button>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            {{-- view file modal --}}
                                                        @empty
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td>
                                                                <span class="text-danger">
                                                                    <strong>No Documents Found!</strong>
                                                                </span>
                                                            </td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="additem">
                            <h3>Publish</h3>
                            <div class="additem_body">
                                <div class="mb-3"><i class="fa-solid fa-calendar-days me-2"></i>
                                    <?php
                                    echo $current_date_time->format('jS F, Y h:i A');
                                    ?>
                                </div>
                                <div class="mb-3">
                                    <h3>Status</h3>
                                    <select class="form-select" aria-label="Default select example" name="status">
                                        {{-- <option>Status</option> --}}
                                        <option value="1" {{ $user->userInfo->status == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ $user->userInfo->status == 0 ? 'selected' : '' }}>
                                            Disable
                                        </option>
                                    </select>
                                </div>
                                <input type="submit" class="btn btn-primary" value="Publish">
                            </div>
                        </div>
                        <div class="additem">
                            <h3>Roles</h3>
                            <div class="additem_body">
                                @if (in_array('Super Admin', $userRoles))
                                    <span class="badge bg-primary">Super Admin</span>
                                @else
                                    <select class="form-select multiple-select @error('roles') is-invalid @enderror"
                                        multiple aria-label="Roles" id="roles" name="roles[]">

                                        @forelse ($roles as $role)
                                            @if ($role != 'Super Admin')
                                                <option value="{{ $role }}"
                                                    {{ in_array($role, $userRoles ?? []) ? 'selected' : '' }}>
                                                    {{ $role }}
                                                </option>
                                                {{-- @else
                                        @if (Auth::user()->hasRole('Super Admin'))
                                            <option value="{{ $role }}"
                                                {{ in_array($role, $userRoles ?? []) ? 'selected' : '' }}>
                                                {{ $role }}
                                            </option>
                                        @endif --}}
                                            @endif

                                        @empty
                                        @endforelse
                                    </select>
                                    @if ($errors->has('roles'))
                                        <span class="text-danger">{{ $errors->first('roles') }}</span>
                                    @endif
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('javascript')
        <script>
            jQuery(document).ready(function() {
                $('#myTabs a').click(function(e) {
                    e.preventDefault()
                    $(this).tab('show')
                })
            });
        </script>
        <script>
            var FetchcountryId = $('#FetchcountryId').val();
            var FetchstateId = $('#FetchstateId').val();
            var FetchcityId = $('#FetchcityId').val();
            $(document).ready(function() {
                $('#country-dd').on('change', function() {
                    var idCountry = this.value;
                    $("#state-dd").html('');
                    $.ajax({
                        url: "{{ url('api/fetch-states') }}",
                        type: "POST",
                        data: {
                            country_id: idCountry,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(result) {
                            $('#state-dd').html('<option value="">--Select State--</option>');
                            $.each(result.states, function(key, value) {
                                $("#state-dd").append('<option value="' + value.id + '" ' +
                                    (value.id == FetchstateId ? 'selected' : '') +
                                    '>' + value.name + '</option>');
                            });
                            $('#city-dd').html('<option value="">--Select City--</option>');
                        }
                    });
                });
                $('#state-dd').on('change', function() {
                    var idState = this.value;
                    $("#city-dd").html('');
                    $.ajax({
                        url: "{{ url('api/fetch-cities') }}",
                        type: "POST",
                        data: {
                            state_id: idState,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(res) {
                            $('#city-dd').html('<option value="">--Select City--</option>');
                            $.each(res.cities, function(key, value) {
                                $("#city-dd").append('<option value="' + value.id + '" ' +
                                    (value.id == FetchcityId ? 'selected' : '') +
                                    '>' + value.name + '</option>');
                            });
                        }
                    });
                });
            });
        </script>
        <script>
            function fetchLocationName(id, type) {
                $.ajax({
                    url: "{{ url('get-location-name') }}/" + id + "/" + type,
                    type: "GET",
                    dataType: 'json',
                    success: function(result) {
                        $(`#${type}-dd`).text(result.name);
                        $(`#${type}-dd`).append(`<option value="${id}">${result.name}</option>`);
                    },
                    error: function(error) {
                        console.error('Error fetching location name:', error);
                    }
                });
            }

            $(document).ready(function() {
                // var FetchcountryId = $('#FetchcountryId').val();
                var FetchstateId = $('#FetchstateId').val();
                var FetchcityId = $('#FetchcityId').val();

                // fetchLocationName(FetchcountryId, 'country');
                fetchLocationName(FetchstateId, 'state');
                fetchLocationName(FetchcityId, 'city');
            });
        </script>



        {{-- <script>
            function saveCertificate(certificateId) {
                var formId = 'certUpdateForm' + certificateId;

                $.ajax({
                    type: 'POST',
                    url: '/certification/update/' + certificateId,
                    data: $('#' + formId).serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Close the modal or provide feedback
                            $('#UploadFileModal' + certificateId).modal('hide');
                            // You can also update the content dynamically if needed
                            // For example, you can update the row in the table without refreshing the page
                        } else {
                            // Handle the error case
                        }
                    },
                    error: function() {
                        // Handle the error case
                    }
                });
            }
        </script> --}}
    @endpush
@endsection
