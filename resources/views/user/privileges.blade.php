@extends('frames.frame')

@section('content')
    <style>

    </style>
    <div class="content-wrapper">
        <div class="card overflow-hidden p-4 shadow-none">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="font-weight-500 text-primary mb-0">User Privileges</h3>
                <div class="btn-group" role="group" aria-label="Basic example">
                    <a href="{{ route('user.create_user_privileges') }}" class="btn-primary btn" tabindex="0" title="Grant Privileges">
                        <i class="mdi mdi-plus"></i>
                    </a>
                    <a href="{{ route('user.add_sidebar_menus') }}" class="btn-primary btn" tabindex="0"
                        title="Create Sidebar Menus">
                        <i class="mdi mdi-menu"></i>
                    </a>
                    <a href="{{ route('user.add_action_buttons') }}" class="btn-primary btn" tabindex="0"
                        title="Add Buttons">
                        <i class="mdi mdi-arrow-top-right"></i>
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <div id="privilegeDataContainer"></div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                }
            });

            displayPrivilegeData();

        });

        function displayPrivilegeData() {
            $.ajax({
                url: "{{ route('fetchPrivilegesData') }}",
                type: "GET",
                dataType: "json",
                success: function (response) {
                    if (response.success && response.data.length > 0) {
                        const container = $("#privilegeDataContainer");
                        container.empty();

                        // Create tabs
                        let tabsHtml = '<ul class="nav nav-tabs" id="privilegeTabs" role="tablist">';
                        let contentHtml = '<div class="tab-content p-0" id="privilegeTabContent">';

                        response.data.forEach((privilege, index) => {
                            const activeClass = index === 0 ? 'active' : '';
                            const showClass = index === 0 ? 'show active' : '';
                            const tabId = `privilege-${privilege.id}`;

                            // Tab headers
                            tabsHtml += `
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link ${activeClass}" id="${tabId}-tab" data-bs-toggle="tab" 
                                            data-bs-target="#${tabId}" type="button" role="tab" 
                                            aria-controls="${tabId}" aria-selected="${index === 0}">
                                            ${privilege.pri_group_name}
                                        </button>
                                    </li>
                                `;
                            // Tab content
                            contentHtml += `
                                    <div class="tab-pane p-0 fade ${showClass}" id="${tabId}" role="tabpanel" 
                                        aria-labelledby="${tabId}-tab">
                                        <div class="card mt-3">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-1"></div>
                                                    <!-- Sidebar Menus -->
                                                    <div class="col-md-5">
                                                        <h6>Sidebar Menus</h6>
                                                        <ul class="list-group">
                                                            ${privilege.menubar_items.map(item => `
                                                                <li class="list-group-item">${item.name}</li>
                                                            `).join('')}
                                                        </ul>
                                                    </div>

                                                    <!-- Action Buttons -->
                                                    <div class="col-md-5">
                                                        <h6>Action Buttons</h6>
                                                        <ul class="list-group">
                                                            ${privilege.action_buttons.map(button => `
                                                                <li class="list-group-item">${button.name}</li>
                                                            `).join('')}
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-1"></div>
                                                </div>
                                                <div class="mt-3 text-muted">
                                                    <small>Created on: ${privilege.created_at}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                        });

                        tabsHtml += '</ul>';
                        contentHtml += '</div>';

                        container.append(tabsHtml);
                        container.append(contentHtml);
                    } else {
                        $("#privilegeDataContainer").html('<div class="alert alert-info">No privilege groups found</div>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching privilege data:", error);
                    $("#privilegeDataContainer").html('<div class="alert alert-danger">Error loading privilege data</div>');
                }
            });
        }

    </script>
@endsection