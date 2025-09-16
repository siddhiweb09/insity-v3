@extends('frames.frame')

@section('content')
    <style>
        .form-section {
            border-bottom: 1px solid #eee;
        }

        .section-title {
            border-bottom: 2px solid var(--super-light-primary);
        }

        .form-label {
            color: #495057;
        }

        .input-group-text bg-super-light-priamry {
            border: 1px solid #ced4da;
            border-radius: 0.5rem;
        }

        .create-user-card{
            border: 3px solid var(--super-light-primary) !important;
        }

        .required-field::after {
            content: "*";
            color: var(--danger);
            margin-left: 4px;
        }
    </style>
    <div class="content-wrapper">
        <div class="card overflow-hidden p-4 shadow-none">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="font-weight-500 text-primary mb-0">Create User</h3>
            </div>


            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="create-user-card card bg-white rounded-3 shadow overflow-hidden">
                        <!-- Form Header -->
                        <form id="createUser" class="needs-validation" novalidate>
                            <!-- Basic Information Section -->
                            <div class="form-section p-3">
                                <h5 class="section-title text-primary fw-semibold mb-4 pb-2"><i
                                        class="ti ti-id text-primary fs-5 me-2"></i>User Basic
                                    Information</h5>
                                <div class="row m-0">
                                    <div class="col-md-6 mb-3">
                                        <label for="employeeCode" class="form-label fw-medium mb-2 required-field">Employee
                                            Code</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-hash fs-6"></i></span>
                                            <input type="text" class="form-control transition" id="employeeCode"
                                                placeholder="Enter employee code" required>
                                            <div class="invalid-feedback">
                                                Please provide a valid employee code.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="employeeName" class="form-label fw-medium mb-2 required-field">Employee
                                            Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-user fs-6"></i></span>
                                            <input type="text" class="form-control" id="employeeName"
                                                placeholder="Enter full name" required>
                                            <div class="invalid-feedback">
                                                Please provide the employee's name.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row m-0">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label fw-medium mb-2 required-field">Email
                                            Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-mail fs-6"></i></span>
                                            <input type="email" class="form-control transition" id="email"
                                                placeholder="Enter email address" required>
                                            <div class="invalid-feedback">
                                                Please provide a valid email address.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="mobile" class="form-label fw-medium mb-2 required-field">Personal Mobile
                                            No</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-device-mobile fs-6"></i></span>
                                            <input type="tel" class="form-control" id="mobile"
                                                placeholder="Enter mobile number" required>
                                            <div class="invalid-feedback">
                                                Please provide a valid mobile number.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row m-0">
                                    <div class="col-md-6 mb-3">
                                        <label for="dob" class="form-label fw-medium mb-2 required-field">Date Of
                                            Birth</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-calendar fs-6"></i></span>
                                            <input type="date" class="form-control transition" id="dob" required>
                                            <div class="invalid-feedback">
                                                Please select date of birth.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label fw-medium mb-2 required-field">Gender</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-user-question fs-6"></i></span>
                                            <select class="form-select transition" id="gender" required>
                                                <option value="" selected disabled>Select gender</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                            <div class="invalid-feedback">
                                                Please select a gender.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row m-0">
                                    <div class="col-md-6 mb-3">
                                        <label for="pan" class="form-label fw-medium mb-2 required-field">Pan Card
                                            No</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-id fs-6"></i></span>
                                            <input type="text" class="form-control" id="pan" placeholder="Enter PAN number"
                                                required>
                                            <div class="invalid-feedback">
                                                Please provide a valid PAN number.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Employment Details Section -->
                            <div class="form-section p-3">
                                <h5 class="section-title text-primary fw-semibold mb-4 pb-2"><i
                                        class="ti ti-briefcase text-primary fs-5 me-2"></i>User Employment
                                    Details</h5>
                                <div class="row m-0">
                                    <div class="col-md-6 mb-3">
                                        <label for="department"
                                            class="form-label fw-medium mb-2 required-field">Department</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-layers-intersect fs-6"></i></span>
                                            <select class="form-select transition" id="department" required>
                                                <option value="" selected disabled>Select department</option>
                                                @foreach ($department as $dept)
                                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                Please select a department.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="designation"
                                            class="form-label fw-medium mb-2 required-field">Designation</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-briefcase fs-6"></i></span>
                                            <select class="form-select transition" id="designation" required>
                                                <option value="" selected disabled>Select designation</option>
                                            </select>
                                            <div class="invalid-feedback">
                                                Please select a designation.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row m-0">
                                    <div class="col-md-6 mb-3">
                                        <label for="zone" class="form-label fw-medium mb-2">Zone</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-map fs-6"></i></span>
                                            <select class="form-select transition" id="zone">
                                                <option value="" selected>Select zone</option>
                                                @foreach ($zone as $z)
                                                    <option class="{{ $z }}">{{ $z }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="branch" class="form-label fw-medium mb-2">Branch</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-building fs-6"></i></span>
                                            <select class="form-select transition" id="branch">
                                                <option value="" selected>Select branch (optional)</option>
                                                <option value="main">Main Branch</option>
                                                <option value="east">East Branch</option>
                                                <option value="west">West Branch</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row m-0">
                                    <div class="col-md-6 mb-3">
                                        <label for="doj" class="form-label fw-medium mb-2 required-field">Date Of
                                            Join</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-calendar-plus fs-6"></i></span>
                                            <input type="date" class="form-control transition" id="doj" required>
                                            <div class="invalid-feedback">
                                                Please select date of joining.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Official Information Section -->
                            <div class="form-section p-3">
                                <h5 class="section-title text-primary fw-semibold mb-4 pb-2"><i
                                        class="ti ti-certificate text-primary fs-5 me-2"></i>User Official Information
                                </h5>
                                <div class="row m-0">
                                    <div class="col-md-6 mb-3">
                                        <label for="officialEmail" class="form-label fw-medium mb-2">Official Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-mail-star fs-6"></i></span>
                                            <input type="email" class="form-control transition" id="officialEmail"
                                                placeholder="Enter official email">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="officialMobile" class="form-label fw-medium mb-2">Official
                                            Mobile</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-super-light-priamry"><i
                                                    class="ti ti-phone-call fs-6"></i></span>
                                            <input type="tel" class="form-control transition" id="officialMobile"
                                                placeholder="Enter official mobile number">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="form-section p-3 border-none">
                                <div class="d-flex justify-content-end gap-3">
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <i class="ti ti-rotate-clockwise-2 me-2"></i>Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-user-plus me-2"></i>Create User
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('customJs')
    <script>
        $(document).ready(function () {

            // Set CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                }
            });

            (function () {
                'use strict'

                var forms = document.querySelectorAll('.needs-validation')

                Array.prototype.slice.call(forms)
                    .forEach(function (form) {
                        form.addEventListener('submit', function (event) {
                            if (!form.checkValidity()) {
                                event.preventDefault()
                                event.stopPropagation()
                            }

                            form.classList.add('was-validated')
                        }, false)
                    })
            })()

            $('#department').on('change', function () {
                let department = $(this).val();
                if (department) {
                    $.ajax({
                        url: '/get-designations/' + department,
                        type: 'GET',
                        success: function (data) {
                            let options = '<option value="" selected disabled>Select designation</option>';
                            $.each(data, function (key, value) {
                                options += `<option value="${value}">${value}</option>`;
                            });
                            $('#designation').html(options);
                        },
                        error: function () {
                            alert('Unable to fetch designations.');
                        }
                    });
                } else {
                    $('#designation').html('<option value="" selected disabled>Select designation</option>');
                }
            });

            $('#zone').on('change', function () {
                let zone = $(this).val();
                if (zone) {
                    $.ajax({
                        url: '/get-branches/' + zone,
                        type: 'GET',
                        success: function (data) {
                            let options = '<option value="" selected disabled>Select branches</option>';
                            $.each(data, function (key, value) {
                                options += `<option value="${value}">${value}</option>`;
                            });
                            $('#branch').html(options);
                        },
                        error: function () {
                            alert('Unable to fetch branches.');
                        }
                    });
                } else {
                    $('#branch').html('<option value="" selected disabled>Select branches</option>');
                }
            });

            $('#createUser').on('submit', function (e) {
                e.preventDefault();

                let formData = {
                    employeeCode: $('#employeeCode').val(),
                    employeeName: $('#employeeName').val(),
                    email: $('#email').val(),
                    mobile: $('#mobile').val(),
                    dob: $('#dob').val(),
                    gender: $('#gender').val(),
                    pan: $('#pan').val(),
                    department: $('#department').val(),
                    designation: $('#designation').val(),
                    zone: $('#zone').val(),
                    branch: $('#branch').val(),
                    doj: $('#doj').val(),
                    officialEmail: $('#officialEmail').val(),
                    officialMobile: $('#officialMobile').val(),
                };

                $.ajax({
                    url: "{{ route('storeUser') }}",
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            $('#createUser')[0].reset();
                            $('#designation').html('<option value="" selected disabled>Select designation</option>');
                            $('#branch').html('<option value="" selected disabled>Select branch</option>');
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMessages = '';
                            $.each(errors, function (key, value) {
                                errorMessages += value + '\n';
                            });
                            alert(errorMessages);
                        } else {
                            alert('Something went wrong!');
                        }
                    }
                });
            });
        });
    </script>
@endsection