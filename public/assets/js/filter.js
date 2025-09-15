$(document).ready(function () {
    // Lead Form Customization
    let filterCount = 1;
    // Leads Filter
    $(".filters").on("click", function () {
        localStorage.setItem("tableName", "registered_leads");
        var offcanvasElement = document.getElementById("filtersOffcanvasEnd");
        var bsOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
        bsOffcanvas.show();
        offcanvasElement.addEventListener(
            "shown.bs.offcanvas",
            function () {
                $(".js-example-basic-single").select2({
                    dropdownParent: $("#filtersOffcanvasEnd .offcanvas-body"),
                });
            },
            {
                once: true,
            }
        );
        $("#url").val(window.location.href);
        $("#tableName").val("registered_leads");

        fetch_filter_title("registered_leads", ".filterSet");
    });

    $(document).on("submit", "#action-filters", function (e) {
        e.preventDefault();

        const $form = $(this);
        const url = $form.data("action"); // from data-action attribute
        const data = $form.serialize(); // lead_id + employee_code (and @csrf hidden input if present)

        // Optional: disable button while submitting
        const $btn = $form.find('button[type="submit"]');
        $btn.prop("disabled", true).text("Submitting...");

        $.ajax({
            url: url,
            method: "POST",
            data: data,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (res) {
                if (res.ok) {
                    window.location.reload();
                } else {
                    alert(res.message || "Something went wrong.");
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const json = xhr.responseJSON || {};
                    const errs = json.errors || {};
                    const list = Object.values(errs).flat().join("\n");
                    alert("Validation error:\n" + (list || "Invalid input."));
                } else {
                    alert("Server error. Please try again.");
                }
            },
            complete: function () {
                $btn.prop("disabled", false).text("Submit");
            },
        });
    });

    // Handle filterTitle change
    $(document).on("change", ".filterTitle", function () {
        const $filterSet = $(this).closest(".filterSet");
        const filterTitle = $(this).val();
        const $filterSearch = $filterSet.find(".filterSearch");
        const $filterValueDiv = $filterSet.find(".filterValueDiv");
        const index = $filterSet.index();

        const allowedFilters = [
            "state",
            "city",
            "branch",
            "zone",
            "utm_source",
            "utm_medium",
            "utm_campaign",
            "level_applying_for",
            "course",
            "lead_owner",
            "lead_origin",
            "lead_source",
            "current_url",
            "lead_stage",
            "lead_sub_stage",
            "assign_reassigned_by",
            "mobile_verification_status",
            "email_verification_status",
            "widget_name",
            "application_submitted",
            "lead_status",
            "is_rec_available",
            "aadhar_flag",
            "eligibility_check_submitted",
        ];

        const allowedNumericFilters = [
            "stage_change_count",
            "registration_attempts",
            "notes_count",
            "followup_count",
            "outbound_success",
            "outbound_missed",
            "outbound_total",
            "inbound_success",
            "inbound_missed",
            "inbound_total",
            "reassigned_count",
            "lead_score",
            "email_sent_count",
            "whatsapp_message_count",
            "sms_sent_count",
        ];

        $filterSearch.empty();

        if (allowedFilters.includes(filterTitle)) {
            $filterSearch.append(`
        <option value="">Search</option>
        <option value="=">EQUAL</option>
        <option value="!=">NOT EQUAL</option>
        <option value="LIKE">LIKE</option>
        <option value="NOT LIKE">NOT LIKE</option>
      `);
        } else if (allowedNumericFilters.includes(filterTitle)) {
            $filterSearch.append(`
        <option value="">Search</option>
        <option value="=">EQUAL</option>
        <option value="!=">NOT EQUAL</option>
        <option value="LIKE">LIKE</option>
        <option value="NOT LIKE">NOT LIKE</option>
        <option value="BETWEEN">BETWEEN</option>
        <option value=">">GREATER THAN</option>
        <option value="<">LESS THAN</option>
        <option value=">=">GREATER THAN OR EQUAL TO</option>
        <option value="<=">LESS THAN OR EQUAL TO</option>
      `);
            $filterValueDiv.html(`
        <input name="filterValue${index}[]" type="text" class="form-control" placeholder="Enter Search Value">
      `);
        } else {
            $filterSearch.append(`
        <option value="">Search</option>
        <option value="=">EQUAL</option>
        <option value="!=">NOT EQUAL</option>
        <option value="LIKE">LIKE</option>
        <option value="NOT LIKE">NOT LIKE</option>
      `);
            $filterValueDiv.html(`
        <input name="filterValue${index}[]" type="text" class="form-control" placeholder="Enter Search Value">
      `);
        }
    });

    // Handle filterSearch change
    $(document).on("change", ".filterSearch", function () {
        const $filterSet = $(this).closest(".filterSet");
        const filterIndex = $filterSet.index();
        const filterSearch = $(this).val();
        const filterTitle = $filterSet.find(".filterTitle").val();
        const $filterValueDiv = $filterSet.find(".filterValueDiv");

        const allowedFilters = [
            "state",
            "city",
            "branch",
            "zone",
            "utm_source",
            "utm_medium",
            "utm_campaign",
            "level_applying_for",
            "course",
            "lead_owner",
            "lead_origin",
            "lead_source",
            "current_url",
            "lead_stage",
            "lead_sub_stage",
            "assign_reassigned_by",
            "mobile_verification_status",
            "email_verification_status",
            "widget_name",
            "application_submitted",
            "lead_status",
            "is_rec_available",
            "aadhar_flag",
            "eligibility_check_submitted",
        ];

        const allowedNumericFilters = [
            "stage_change_count",
            "registration_attempts",
            "notes_count",
            "followup_count",
            "outbound_success",
            "outbound_missed",
            "outbound_total",
            "inbound_success",
            "inbound_missed",
            "inbound_total",
            "reassigned_count",
            "lead_score",
            "email_sent_count",
            "whatsapp_message_count",
            "sms_sent_count",
        ];

        if (allowedFilters.includes(filterTitle)) {
            fetch_filter_distinct_value(filterTitle, $filterSet, filterIndex);
        } else if (
            allowedNumericFilters.includes(filterTitle) &&
            filterSearch === "BETWEEN"
        ) {
            $filterValueDiv.html(`
        <input name="filterValue${filterIndex}[]" type="text" class="form-control" hidden>
        <input name="filterValueFirst" type="text" class="form-control mb-3" placeholder="Enter First Value">
        <input name="filterValueSecond" type="text" class="form-control" placeholder="Enter Second Value">
      `);
        } else {
            $filterValueDiv.html(`
        <input name="filterValue${filterIndex}[]" type="text" class="form-control" placeholder="Enter Search Value">
      `);
        }
    });

    // Fetch dropdown values via AJAX
    function fetch_filter_distinct_value(columnName, $filterSet, filterCount) {
        const tableName = localStorage.getItem("tableName");
        $.ajax({
            type: "POST",
            url: "../fetch/distinct-column",
            data: {
                columnName,
                tableName,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",
            success: function (response) {
                const $filterValueDiv = $filterSet.find(".filterValueDiv");
                $filterValueDiv.html(`
          <select name="filterValue${filterCount}[]" class="form-control filterValue js-example-basic-single w-100" multiple></select>
        `);
                const $select = $filterSet.find(".filterValue");
                $select
                    .empty()
                    .append('<option value="">Select Value</option>');

                $.each(response, function (index, value) {
                    $select.append(
                        $("<option>", {
                            value: value,
                            text: value,
                        })
                    );
                });

                $select.select2({
                    dropdownParent: $filterSet,
                    multiple: true,
                    width: "resolve",
                });
            },
            error: function (error) {
                console.error("Error fetching values:", error);
            },
        });
    }

    // Fetch dropdown values via AJAX
    function fetch_filter_title(tableName, $filterSet) {
        console.log("Fetching titles for table:", tableName);
        console.log("Filter Set:", $filterSet);
        $.ajax({
            type: "POST",
            url: "../fetch/distinct-title",
            data: {
                tableName,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",
            success: function (response) {
                const select = $(".filterTitle");
                console.log("Select Element:", select);
                select.empty().append('<option value="">Select Value</option>');
                const skipColumns = [
                    "id",
                    "created_at",
                    "updated_at",
                    "dumped_at",
                ];

                $.each(response, function (index, value) {
                    if (skipColumns.includes(value)) {
                        return; // Skip this iteration
                    }
                    let formattedValue = value.replace(/_/g, " ");
                    formattedValue = formattedValue.replace(
                        /\b\w/g,
                        function (char) {
                            return char.toUpperCase();
                        }
                    );

                    select.append(
                        $("<option>", {
                            value: value,
                            text: formattedValue,
                        })
                    );
                });

                select.select2({
                    dropdownParent: $filterSet,
                    width: "resolve",
                });
            },
            error: function (error) {
                console.error("Error fetching values:", error);
            },
        });

        const leadsColumns = [
            "lead_assignment_date",
            "last_lead_activity_date",
            "last_enquirer_activity_date",
            "recording_date",
        ];

        const defaultsColumns = ["crreated_at"];

        if (tableName === "registered_leads") {
            date_source_change(leadsColumns, $filterSet);
        } else {
            date_source_change(defaultsColumns, $filterSet);
        }
    }

    function date_source_change(columns, $filterSet) {
        $.each(columns, function (index, value) {
            let formattedValue = value.replace(/_/g, " ");
            formattedValue = formattedValue.replace(/\b\w/g, function (char) {
                return char.toUpperCase();
            });

            $(".date_source").append(
                $("<option>", {
                    value: value,
                    text: formattedValue,
                })
            );
        });

        $(".date_source").select2({
            dropdownParent: $filterSet,
            width: "resolve",
        });
    }

    $("#addFilter").click(function () {
        if (filterCount < 20) {
            var filterClone = $("#filterBox .filterSet:first").clone();

            // Update the name attributes and clear values
            filterClone
                .find('select[name^="filterTitle"]')
                .attr("name", "filterTitle" + filterCount)
                .val("")
                .removeClass("select2-hidden-accessible")
                .next(".select2-container")
                .remove();

            filterClone
                .find('select[name^="filterSearch"]')
                .attr("name", "filterSearch" + filterCount)
                .val("")
                .removeClass("select2-hidden-accessible")
                .next(".select2-container")
                .remove();

            filterClone
                .find(
                    'input[name^="filterValue"], input[name^="filterValueFirst"], input[name^="filterValueSecond"]'
                )
                .remove();

            filterClone
                .find(".filterValueDiv")
                .html(
                    `<input name="filterValue${filterCount}[]" type="text" class="form-control" placeholder="Enter Search Value">`
                );

            $("#filterBox").append(filterClone);

            // Re-initialize select2 for the new selects
            filterClone
                .find('select[name="filterTitle' + filterCount + '"]')
                .select2({
                    dropdownParent: $("#filtersOffcanvasEnd .offcanvas-body"),
                });

            filterClone
                .find('select[name="filterSearch' + filterCount + '"]')
                .select2({
                    dropdownParent: $("#filtersOffcanvasEnd .offcanvas-body"),
                });

            filterCount++;
        } else {
            alert("You cannot add more than 20 filters.");
        }
    });
});
