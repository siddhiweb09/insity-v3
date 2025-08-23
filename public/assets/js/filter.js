$(document).ready(function () {
    let filterCount = 1;

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
                    `<input name="filterValue${filterCount}" type="text" class="form-control" placeholder="Enter Search Value">`
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

    // Handle filterTitle change
    $(document).on("change", ".filterTitle", function () {
        const $filterSet = $(this).closest(".filterSet");
        const filterTitle = $(this).val();
        const $filterSearch = $filterSet.find(".filterSearch");
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
        <input name="filterValue" type="text" class="form-control" placeholder="Enter Search Value">
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
        <input name="filterValue" type="text" class="form-control" placeholder="Enter Search Value">
      `);
        }
    });

    // Handle filterSearch change
    $(document).on("change", ".filterSearch", function () {
        const $filterSet = $(this).closest(".filterSet");
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
            fetch_filter_distinct_value(filterTitle, $filterSet);
        } else if (
            allowedNumericFilters.includes(filterTitle) &&
            filterSearch === "BETWEEN"
        ) {
            $filterValueDiv.html(`
        <input name="filterValue" type="text" class="form-control" hidden>
        <input name="filterValueFirst" type="text" class="form-control mb-3" placeholder="Enter First Value">
        <input name="filterValueSecond" type="text" class="form-control" placeholder="Enter Second Value">
      `);
        } else {
            $filterValueDiv.html(`
        <input name="filterValue" type="text" class="form-control" placeholder="Enter Search Value">
      `);
        }
    });

    // Fetch dropdown values via AJAX
    function fetch_filter_distinct_value(columnName, $filterSet) {
        $.ajax({
            type: "POST",
            url: "../fetch/distinct-column",
            data: {
                columnName,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",
            success: function (response) {
                const $filterValueDiv = $filterSet.find(".filterValueDiv");
                $filterValueDiv.html(`
          <select name="filterValue" class="form-control filterValue js-example-basic-single w-100" multiple></select>
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
});
