@extends('frames.frame')

@section('content')
    <style>
        .create-user-card {
            border: 3px solid var(--super-light-primary) !important;
        }

        .form-label {
            color: #495057;
        }

        .form-check-input {
            margin-top: 0.15em !important;
        }

        .card {
            position: unset !important;
        }
    </style>
    <div class="content-wrapper">
        <div class="card p-4 shadow-none">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="font-weight-500 text-primary mb-0">Add Sidebar Menus</h3>
            </div>

            <div class="row justify-content-center">
                <div class="col-12">

                </div>
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

            // Select All functionality for each tab
            $(document).on("change", "#selectAllSidebar", function () {
                const isChecked = $(this).prop("checked");
                $(".sidebar-category").prop("checked", isChecked);
                $(".sidebar-item-checkbox").prop("checked", isChecked);
                ensureLogoutSelected();
            });

            $(document).on("change", "#selectAllActions", function () {
                const isChecked = $(this).prop("checked");
                $(".action-category").prop("checked", isChecked);
                $(".action-item-checkbox").prop("checked", isChecked);
            });

            // Category selection handlers
            $(document).on("change", ".sidebar-category", function () {
                const categorySlug = $(this).data("category");
                const isChecked = $(this).prop("checked");
                $(`.sidebar-category-${categorySlug}`).prop("checked", isChecked);
                ensureLogoutSelected();
                updateSelectAllState("#selectAllSidebar", ".sidebar-item-checkbox");
            });

            $(document).on("change", ".action-category", function () {
                const categorySlug = $(this).data("category");
                const isChecked = $(this).prop("checked");
                $(`.action-category-${categorySlug}`).prop("checked", isChecked);
                updateSelectAllState("#selectAllActions", ".action-item-checkbox");
            });

            // Individual item selection handlers
            $(document).on("change", ".sidebar-item-checkbox", function () {
                const checkbox = $(this);
                const classes = checkbox.attr("class").split(/\s+/);
                let categorySlug = null;

                classes.forEach(function (cls) {
                    if (cls.startsWith("sidebar-category-")) {
                        categorySlug = cls.replace("sidebar-category-", "");
                    }
                });

                if (categorySlug) {
                    const allItems = $(`.sidebar-category-${categorySlug}`);
                    const checkedItems = allItems.filter(":checked");
                    const categoryCheckbox = $(`#sidebar_category_${categorySlug}`);
                    categoryCheckbox.prop("checked", allItems.length === checkedItems.length);
                }

                ensureLogoutSelected();
                updateSelectAllState("#selectAllSidebar", ".sidebar-item-checkbox");
            });

            $(document).on("change", ".action-item-checkbox", function () {
                const checkbox = $(this);
                const classes = checkbox.attr("class").split(/\s+/);
                let categorySlug = null;

                classes.forEach(function (cls) {
                    if (cls.startsWith("action-category-")) {
                        categorySlug = cls.replace("action-category-", "");
                    }
                });

                if (categorySlug) {
                    const allItems = $(`.action-category-${categorySlug}`);
                    const checkedItems = allItems.filter(":checked");
                    const categoryCheckbox = $(`#action_category_${categorySlug}`);
                    categoryCheckbox.prop("checked", allItems.length === checkedItems.length);
                }

                updateSelectAllState("#selectAllActions", ".action-item-checkbox");
            });

            fetchSidebarMenusActionButtons();

            // Form submission
            $('#storePrivilegesForm').on('submit', function (e) {

                e.preventDefault();
                console.log('hii');

                // Validate at least one menu item is selected
                if ($('input[name="menubar_items[]"]:checked').length === 0) {
                    alert("Please select at least one menu item.");
                    return false;
                }

                // Collect all form data
                let formData = $(this).serialize();

                // Show loading state
                $('button[type="submit"]').prop('disabled', true).html(
                    '<i class="ti ti-loader fs-5"></i> Processing...');

                $.ajax({
                    url: "{{ route('storeUserPrivilege') }}",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            alert(response.message);
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        } else {
                            alert("Error: " + response.message);
                            $('button[type="submit"]').prop('disabled', false).html(
                                '<i class="ti ti-send fs-5"></i> Submit');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                        alert("Error submitting privileges. Please try again.");
                        $('button[type="submit"]').prop('disabled', false).html(
                            '<i class="ti ti-send fs-5"></i> Submit');
                    }
                });
            });

        });

        function fetchSidebarMenusActionButtons() {
            $.ajax({
                type: "GET",
                url: "{{ route('fetchSidebarMenusActionButtons') }}",
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        // --- Sidebar Menus ---
                        if (response.sidebar_menus && response.sidebar_menus.length > 0) {
                            renderMenuItems(response.sidebar_menus, "menubar_items_container", "sidebar");
                        } else {
                            $("#menubar_items_container").html(
                                '<div class="col-12 text-center py-3">No sidebar menus found</div>'
                            );
                        }

                        // --- Action Buttons ---
                        if (response.action_buttons && response.action_buttons.length > 0) {
                            renderMenuItems(response.action_buttons, "action_buttons_container", "action");
                        } else {
                            $("#action_buttons_container").html(
                                '<div class="col-12 text-center py-3">No action buttons found</div>'
                            );
                        }
                    } else {
                        console.error("Error:", response.error);
                        $("#menubar_items_container, #action_buttons_container").html(
                            '<div class="col-12 text-center py-3 text-danger">Error loading data</div>'
                        );
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", error, xhr.responseText);
                    $("#menubar_items_container, #action_buttons_container").html(
                        '<div class="col-12 text-center py-3 text-danger">Error loading data</div>'
                    );
                },
            });
        }

        function renderMenuItems(items, containerId, type) {
            const groupedItems = {};
            const container = $("#" + containerId);
            container.empty();

            // Group items by category
            items.forEach((item) => {
                const category = item.categories || 'Uncategorized';
                if (!groupedItems[category]) {
                    groupedItems[category] = [];
                }
                groupedItems[category].push(item);
            });

            // Build HTML for each category
            Object.keys(groupedItems).forEach((category) => {
                const slug = category.toLowerCase().replace(/\s+/g, "-");
                const cat = category.replace(/\s+/g, "-");

                let html = `
                                                                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                                                                    <div class="ms-md-3 border p-3 mb-3 rounded bg-light">
                                                                                                        <div class="form-check mb-2">
                                                                                                            <input type="checkbox" class="form-check-input ${type}-category" 
                                                                                                                name="${type}_categories[]" value="${cat}" 
                                                                                                                data-category="${slug}" id="${type}_category_${slug}">
                                                                                                            <label class="form-check-label fw-medium" for="${type}_category_${slug}">${category}</label>
                                                                                                        </div>
                                                                                            `;

                groupedItems[category].forEach((item, index) => {
                    const itemId = item.id || index;
                    const isLogout = (type === "sidebar" && item.name.toLowerCase() === "logout");

                    // input name differs
                    const inputName = (type === "sidebar")
                        ? "menubar_items[]"
                        : "action_items[]";

                    html += `
                                                                                                    <div class="form-check ms-3 mb-1" style="padding-left:inherit;">
                                                                                                        <input class="form-check-input ${type}-item-checkbox ${type}-category-${slug}"
                                                                                                            type="checkbox" name="${inputName}" value="${itemId}"
                                                                                                            id="${type}_item_${slug}_${index}" ${isLogout ? 'checked disabled' : ''}>
                                                                                                        <label class="form-check-label" for="${type}_item_${slug}_${index}">
                                                                                                            ${item.name}
                                                                                                        </label>
                                                                                                    </div>
                                                                                                `;
                });

                html += `</div></div>`;
                container.append(html);
            });

            // Special: ensure Logout is always selected
            if (type === "sidebar") {
                ensureLogoutSelected();
            }
        }


        function ensureLogoutSelected() {
            // Always keep the Logout item selected and disabled
            $(".sidebar-category").each(function () {
                const label = $(this).next("label").text().toLowerCase();
                if (label === "logout") {
                    $(this).prop("checked", true).prop("disabled", true);
                    // Also ensure its category is checked
                    const classes = $(this).attr("class").split(/\s+/);
                    classes.forEach(function (cls) {
                        if (cls.startsWith("sidebar-category-")) {
                            const categorySlug = cls.replace("sidebar-category-", "");
                            $(`#sidebar_category_${categorySlug}`).prop("checked", true);
                        }
                    });
                }
            });
        }

        function updateSelectAllState(selectAllId, itemSelector) {
            const totalItems = $(itemSelector).length;
            const checkedItems = $(`${itemSelector}:checked`).length;
            $(selectAllId).prop("checked", totalItems > 0 && totalItems === checkedItems);
        }

    </script>
@endsection