@extends('layouts.app')

@section('content')
    @php
        $current_date_time = \Carbon\Carbon::now();
    @endphp
    <!-- Begin Page Content -->
    <div class="page-content container">
        <div class="wrapper">
            <div class="row align-items-center mb-3">
                <div class="col-md-9">
                    <div class="page-header">
                        <h3> Add New Facility</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('facilities.index') }}" class="btn btn-primary float-end">
                        <i class="fa-solid fa-list me-1"></i>All Facilities
                    </a>
                </div>
            </div>
            <form action="{{ route('facilities.store') }}" method="post">
                @csrf
                <div class="row g-2">
                    <ul class="nav nav-tabs" id="myTabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" aria-current="page" href="#userBasic">Basic</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#userGeneral">General</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#userContact">Parts/BOM</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#userCareer">Metering/Events</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#userFiles">Files</a>
                        </li>
                    </ul>
                    <div class="col-md-6 col-lg-8">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="userBasic">
                                <div class="page-header">
                                    <h3>Basic Details</h3>
                                </div>
                                <div class="whitebox mb-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="facility_name"
                                                class="col-form-label text-md-end text-start">Facility name</label>
                                            <input type="text"
                                                class="form-control @error('facility_name') is-invalid @enderror"
                                                id="facility_name" name="facility_name" value="{{ old('facility_name') }}">
                                            @if ($errors->has('facility_name'))
                                                <span class="text-danger">{{ $errors->first('facility_name') }}</span>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email" class="col-form-label text-md-end text-start">Code</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="email" name="email" value="{{ old('email') }}">
                                            @if ($errors->has('email'))
                                                <span class="text-danger">{{ $errors->first('email') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="password" class="col-form-label text-md-end text-start">Category
                                            </label>
                                            <input type="text"
                                                class="form-control @error('password') is-invalid @enderror" id="password"
                                                name="password">
                                            @if ($errors->has('password'))
                                                <span class="text-danger">{{ $errors->first('password') }}</span>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <label for="facility_notes"
                                                class="col-form-label text-md-end text-start">Facility Notes</label>
                                            <textarea class="form-control @error('facility_notes') is-invalid @enderror" name="facility_notes" id="facility_notes"
                                                cols="48" rows="6"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="userGeneral">
                                <div class="item_name">
                                    <div class="page-header">
                                        <h3>Location</h3>
                                    </div>
                                    <div class="whitebox mb-4">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input type="radio" class="@error('old_faci_chkbox') is-invalid @enderror"
                                                    id="old_faci_chkbox" name="faci_chkbox" value="">
                                                <label for="old_faci_chkbox"
                                                    class="col-form-label text-md-end text-start">This
                                                    facility is a part of:</label>
                                                @if ($errors->has('faci_chkbox'))
                                                    <span class="text-danger">{{ $errors->first('faci_chkbox') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-md-8">
                                                <input type="radio" class="@error('new_faci_chkbox') is-invalid @enderror"
                                                    id="new_faci_chkbox" name="faci_chkbox">
                                                <label for="new_faci_chkbox"
                                                    class="col-form-label text-md-end text-start">This
                                                    facility is not part of another location, and is located at:</label>
                                                @if ($errors->has('faci_chkbox'))
                                                    <span class="text-danger">{{ $errors->first('faci_chkbox') }}</span>
                                                @endif
                                            </div>
                                            <div class="item_name old_faci_addr">
                                                <div class="col-md-6">
                                                    <label for="positions"
                                                        class="col-form-label text-md-end text-start">Facilities</label>
                                                    <select class="form-control @error('positions') is-invalid @enderror"
                                                        aria-label="Positions" id="positions" name="positions">
                                                        <option value="">--Select--</option>
                                                        @forelse ($positions as $id => $position)
                                                            <option value="{{ $id }}"
                                                                {{ old('positions') == $id ? 'selected' : '' }}>
                                                                {{ $position }}
                                                            </option>
                                                        @empty
                                                        @endforelse
                                                    </select>
                                                    @if ($errors->has('positions'))
                                                        <span class="text-danger">{{ $errors->first('positions') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="item_name faci_new_addr">
                                                <div class="whitebox mb-4">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label for="address"
                                                                class="col-form-label text-md-end text-start">Address</label>
                                                            <textarea class="form-control @error('contact.address') is-invalid @enderror" name="contact[address]" id="address"
                                                                cols="48" rows="6">{{ old('contact.address') }}</textarea>
                                                            @if ($errors->has('contact.address'))
                                                                <span
                                                                    class="text-danger">{{ $errors->first('contact.address') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="country-dd"
                                                                class="col-form-label text-md-end text-start">Country</label>
                                                            <select id="country-dd" name="contact[country]"
                                                                value="{{ old('country') }}"
                                                                class="form-control single-select @error('contact.country') is-invalid @enderror">
                                                                <option value="">--Select Country--</option>
                                                                @foreach ($countries as $data)
                                                                    <option value="{{ $data->id }}">
                                                                        {{ $data->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @if ($errors->has('contact.country'))
                                                                <span
                                                                    class="text-danger">{{ $errors->first('contact.country') }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="state-dd"
                                                                class="col-form-label text-md-end text-start">State
                                                                or
                                                                province</label>
                                                            <select id="state-dd" name="contact[state]"
                                                                value="{{ old('state') }}"
                                                                class="form-control single-select @error('contact.state') is-invalid @enderror">
                                                            </select>
                                                            @if ($errors->has('contact.state'))
                                                                <span
                                                                    class="text-danger">{{ $errors->first('contact.state') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="city-dd"
                                                                class="col-form-label text-md-end text-start">City</label>
                                                            <select id="city-dd" name="contact[city]"
                                                                value="{{ old('city') }}"
                                                                class="form-control single-select @error('contact.city') is-invalid @enderror">
                                                            </select>
                                                            @if ($errors->has('contact.city'))
                                                                <span
                                                                    class="text-danger">{{ $errors->first('contact.city') }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="postcode"
                                                                class="col-form-label text-md-end text-start">Zip or
                                                                postal
                                                                code</label>
                                                            <input type="text"
                                                                class="form-control @error('contact.postcode') is-invalid @enderror"
                                                                id="postcode" name="contact[postcode]"
                                                                value="{{ old('postcode') }}">
                                                            @if ($errors->has('contact.postcode'))
                                                                <span
                                                                    class="text-danger">{{ $errors->first('contact.postcode') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="page-header">
                                        <h3>General Information</h3>
                                    </div>
                                    <div class="whitebox mb-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="account"
                                                    class="col-form-label text-md-end text-start">Account</label>
                                                <input type="text"
                                                    class="form-control @error('account') is-invalid @enderror"
                                                    id="account" name="account" value="{{ old('account') }}">
                                                @if ($errors->has('account'))
                                                    <span class="text-danger">{{ $errors->first('account') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <label for="email"
                                                    class="col-form-label text-md-end text-start">Barcode</label>
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    id="email" name="email" value="{{ old('email') }}">
                                                @if ($errors->has('email'))
                                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="password" class="col-form-label text-md-end text-start">Charge
                                                    Department
                                                </label>
                                                <input type="text"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="password" name="password">
                                                @if ($errors->has('password'))
                                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <label for="notes"
                                                    class="col-form-label text-md-end text-start">Notes</label>
                                                <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" id="notes" cols="48"
                                                    rows="6"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="userContact">
                                <div class="page-header">
                                    <h3>Asset Parts Supplies</h3>
                                </div>
                                <div class="item_name">
                                    <div class="whitebox mb-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="positions"
                                                    class="col-form-label text-md-end text-start">Part/Supply
                                                </label>
                                                <select class="form-control @error('positions') is-invalid @enderror"
                                                    aria-label="Positions" id="positions" name="positions">
                                                    <option value="">Select</option>
                                                    @forelse ($positions as $id => $position)
                                                        <option value="{{ $id }}"
                                                            {{ old('positions') == $id ? 'selected' : '' }}>
                                                            {{ $position }}
                                                        </option>
                                                    @empty
                                                    @endforelse
                                                </select>
                                                @if ($errors->has('positions'))
                                                    <span class="text-danger">{{ $errors->first('positions') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <label for="effective_date"
                                                    class="col-form-label text-md-end text-start">Qty</label>
                                                <input type="text" id="effective_date" name="effective_date"
                                                    value="" class="form-control">
                                                @if ($errors->has('effective_date'))
                                                    <span
                                                        class="text-danger">{{ $errors->first('effective_date') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="userCareer">
                                <div class="page-header">
                                    <h3>Meter Reading</h3>
                                </div>
                                <div class="item_name">
                                    <div class="whitebox mb-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="effective_date"
                                                    class="col-form-label text-md-end text-start">Meter Reading
                                                </label>
                                                <input type="text" id="effective_date" name="effective_date"
                                                    value="" class="form-control">
                                                @if ($errors->has('effective_date'))
                                                    <span
                                                        class="text-danger">{{ $errors->first('effective_date') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <label for="positions" class="col-form-label text-md-end text-start">Meter
                                                    Reading Units
                                                </label>
                                                <select class="form-control @error('positions') is-invalid @enderror"
                                                    aria-label="Positions" id="positions" name="positions">
                                                    <option value="">Select</option>
                                                    @forelse ($positions as $id => $position)
                                                        <option value="{{ $id }}"
                                                            {{ old('positions') == $id ? 'selected' : '' }}>
                                                            {{ $position }}
                                                        </option>
                                                    @empty
                                                    @endforelse
                                                </select>
                                                @if ($errors->has('positions'))
                                                    <span class="text-danger">{{ $errors->first('positions') }}</span>
                                                @endif
                                            </div>
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
                                            <div class="col-md-6">
                                                <input type="file"
                                                    class="form-control @error('files') is-invalid @enderror"
                                                    id="files" name="files[]" multiple>
                                                @if ($errors->has('files'))
                                                    <span class="text-danger">{{ $errors->first('files') }}</span>
                                                @endif
                                                <span class="text-muted">*Supported file type: doc, docx,
                                                    xlsx, xls, ppt, pptx, txt, pdf, jpg, jpeg, png, webp, gif</span>
                                            </div>
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
                                    {{ $current_date_time->format('jS F, Y h:i A') }}
                                </div>
                                <div class="mb-3">
                                    <h3>Status</h3>
                                    <select class="form-select" aria-label="Default select example" name="status">
                                        {{-- <option>Status</option> --}}
                                        <option value="1">Active</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                                <input type="submit" class="btn btn-primary" value="Publish">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @push('javascript')
        <script>
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
                                $("#state-dd").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
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
                                $("#city-dd").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                        }
                    });
                });
            });
        </script>
        <script>
            function toggleDropdown() {
                var oldFaciAddr = document.getElementById('old_faci_addr');
                var newFaciAddr = document.getElementById('faci_new_addr');
                var oldFaciCheckbox = document.getElementById('old_faci_chkbox');
                if (oldFaciCheckbox.checked) {
                    oldFaciAddr.style.display = 'block';
                    newFaciAddr.style.display = 'none';
                }
            }

            function toggleNewAddr() {
                var oldFaciAddr = document.getElementById('old_faci_addr');
                var newFaciAddr = document.getElementById('faci_new_addr');
                var newFaciCheckbox = document.getElementById('new_faci_chkbox');
                if (newFaciCheckbox.checked) {
                    newFaciAddr.style.display = 'block';
                    oldFaciAddr.style.display = 'none';
                }
            }
        </script>
    @endpush
@endsection
