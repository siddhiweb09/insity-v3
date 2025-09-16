// This script handles the visibility of columns in the leads table
(function () {
    const table = document.getElementById("leadsTable");
    if (!table) return;

    const headerRow = table.tHead
        ? table.tHead.rows[0]
        : table.querySelector("thead tr");
    const columnsList = document.getElementById("columnsList");

    // Defaults: only these columns start visible
    const defaultVisible = [
        "Column 1",
        "Registered Name",
        "Registered Email",
        "Registered Mobile",
        "Lead Owner",
        "Lead Source",
        "Lead Stage",
        "Registration Attempt Date",
        "Branch",
        "Zone",
    ];
    const LOCK_FIRST = true;

    function normalize(txt) {
        return String(txt || "")
            .replace(/\s+/g, " ")
            .trim();
    }

    function isHidden(th) {
        return (
            th.hidden ||
            th.classList.contains("d-none") ||
            (th.getAttribute("style") || "")
                .toLowerCase()
                .includes("display:none") ||
            getComputedStyle(th).display === "none" ||
            getComputedStyle(th).visibility === "hidden"
        );
    }

    // ✅ Robust: toggles header + each row's TD by absolute index (no nth-child)
    function setColVisible(absIndex, visible) {
        const hide = !visible;

        // Header cell
        const th =
            headerRow && headerRow.cells ? headerRow.cells[absIndex] : null;
        if (th) th.classList.toggle("d-none", hide);

        // Body cells
        for (const tb of table.tBodies) {
            for (const tr of tb.rows) {
                const td = tr.cells[absIndex]; // same absolute index
                if (td) td.classList.toggle("d-none", hide);
            }
        }
    }

    // Build meta for THs
    const thMeta = Array.from(headerRow.cells).map((th, i) => {
        const label =
            normalize(th.dataset.label ?? th.textContent) || `Column ${i + 1}`;
        return {
            label,
            id: th.id || null,
            absIndex: i,
            hidden: isHidden(th),
        };
    });

    // Build switch list UI
    columnsList.innerHTML = "";
    thMeta.forEach((h) => {
        if (h.hidden) return; // skip hidden headers entirely

        const id = "col-toggle-" + h.absIndex;
        const row = document.createElement("label");
        row.className =
            "list-group-item d-flex justify-content-between align-items-center m-0";

        const isDefault = defaultVisible.includes(h.label);

        row.innerHTML = `
                <span class="me-3">${h.label}</span>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" id="${id}" data-col-index="${
            h.absIndex
        }" ${isDefault ? "checked" : ""}>
                </div>
                `;

        const input = row.querySelector("input");

        // Wire toggle -> show/hide column
        input.addEventListener("change", (e) => {
            const idx = parseInt(e.currentTarget.dataset.colIndex, 10);
            setColVisible(idx, e.currentTarget.checked);
        });

        columnsList.appendChild(row);
    });

    // Initial apply: hide columns that are NOT in defaults (but only for listed ones)
    thMeta.forEach((h) => {
        if (h.hidden) return; // already hidden / skipped
        const shouldShow = defaultVisible.includes(h.label);
        setColVisible(h.absIndex, shouldShow);
    });

    // --- Select All / Clear All buttons ---
    const btnAll = document.getElementById("columnsSelectAll");
    const btnClear = document.getElementById("columnsClearAll");

    function listInputs() {
        return Array.from(
            columnsList.querySelectorAll("input.form-check-input")
        );
    }

    // Check all switches and show all listed columns
    btnAll?.addEventListener("click", () => {
        listInputs().forEach((input) => {
            if (input.disabled) return; // keep locked columns untouched
            input.checked = true;
            const idx = Number(input.dataset.colIndex);
            setColVisible(idx, true);
        });
        // ensure locked first stays on (in case it wasn't listed/was disabled)
        if (LOCK_FIRST) {
            const first = columnsList.querySelector(
                'input[data-col-index="0"]'
            );
            if (first) {
                first.checked = true;
                setColVisible(0, true);
            }
        }
    });

    // Uncheck all switches and hide all listed columns
    btnClear?.addEventListener("click", () => {
        listInputs().forEach((input) => {
            if (input.disabled) return; // keep locked columns untouched
            input.checked = false;
            const idx = Number(input.dataset.colIndex);
            setColVisible(idx, false);
        });
        // keep first column visible to avoid a blank table (optional)
        if (LOCK_FIRST) {
            const first = columnsList.querySelector(
                'input[data-col-index="0"]'
            );
            if (first) {
                first.checked = true;
                setColVisible(0, true);
            }
        }
    });
})();

// Date Range Picker and URL Update
var dateRange = "";
var currentdateRange = "";
var page = window.location.href;
var pageSegments = page.split("/");
var startDate = moment().subtract(2, "days").startOf("day");
var endDate = moment().endOf("day");
var date_source = $(".date_source").val();
$("#category").val(pageSegments[4]);

dateRange = startDate.format("YYYY-MM-DD") + "*" + endDate.format("YYYY-MM-DD");
$("#dateRange").val(dateRange);

$("#date-range").daterangepicker(
    {
        opens: "left",
        locale: {
            format: "YYYY-MM-DD",
        },
        startDate: startDate,
        endDate: endDate,
    },
    function (start, end) {
        dateRange = start.format("YYYY-MM-DD") + "*" + end.format("YYYY-MM-DD");
        updateURLWithDateRange(dateRange); // Update the URL with selected date range
    }
);

$("#date-range-filter").daterangepicker(
    {
        opens: "left",
        locale: {
            format: "YYYY-MM-DD",
        },
        startDate: startDate,
        endDate: endDate,
    },
    function (start, end) {
        dateRange = start.format("YYYY-MM-DD") + "*" + end.format("YYYY-MM-DD");
        $("#dateRange").val(dateRange);
    }
);

$(".date_source").on("change", function () {
    var date_source = $(this).val();
    console.log("date_source inner:" + date_source);
});

var today = new Date();
var formatDate = function (date) {
    var year = date.getFullYear();
    var month = ("0" + (date.getMonth() + 1)).slice(-2);
    var day = ("0" + date.getDate()).slice(-2);
    return year + "-" + month + "-" + day;
};

var daterangeSegments = pageSegments[3].split("?");
if (!daterangeSegments[1] || daterangeSegments[1].length === 0) {
    page = pageSegments[3];
    currentdateRange =
        formatDate(startDate.toDate()) + "*" + formatDate(endDate.toDate());
} else {
    page = daterangeSegments[0];
    currentdateRange = daterangeSegments[1];
}

function updateURLWithDateRange(dateRange) {
    var date_source = $(".date_source").val();
    var currentURL = window.location.href.split("?")[0];
    var param =
        "date_source=" +
        encodeURIComponent(date_source) +
        "&date_range=" +
        encodeURIComponent(dateRange);
    console.log("param: " + param);
    var newURL = currentURL + "?" + param;
    history.pushState(null, "", newURL);
    window.location.reload();
}

// Get checked values
var checkedValues = [];

function getCheckedValues() {
    checkedValues = [];
    $("input[name='check']").prop("checked", $(this).prop("checked"));

    $("input[name='check']:checked").each(function () {
        checkedValues.push($(this).val());
    });
    // console.log("checkedValues:" + checkedValues);
}

$("#checkAll").click(function () {
    $("input[name='check']").prop("checked", $(this).prop("checked"));
});

// Check/uncheck the "checkAll" checkbox based on the state of individual checkboxes
$("input[name='check']").click(function () {
    if (
        $("input[name='check']:checked").length ===
        $("input[name='check']").length
    ) {
        $("#checkAll").prop("checked", true);
    } else {
        $("#checkAll").prop("checked", false);
    }
});

// Reassign Lead Owner
$(".reassign").on("click", function () {
    var offcanvasElement = document.getElementById("reassignleadsOffcanvasEnd");
    var bsOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
    bsOffcanvas.show();
    offcanvasElement.addEventListener(
        "shown.bs.offcanvas",
        function () {
            $(".js-example-basic-single").select2({
                dropdownParent: $("#reassignleadsOffcanvasEnd .offcanvas-body"), // Ensure the dropdown is appended to the correct off-canvas element
            });
        },
        {
            once: true,
        }
    );
    getCheckedValues();
    var lead_id = $("#lead_id");
    lead_id.val(checkedValues);

    var employee_code = $("#employee_code");
    employee_code.empty();

    const employees = Array.isArray(window.__TEAM_MEMBERS__)
        ? window.__TEAM_MEMBERS__
        : [];
    console.log("Employees:", employees);
    console.log("Employees:", employees);

    employees.forEach(function (emp) {
        employee_code.append(
            $("<option>", {
                value: emp.employee_code + "*" + emp.employee_name,
                text: emp.employee_code + "*" + emp.employee_name,
            })
        );
    });
});

$(document).on("submit", "#reassign", function (e) {
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
                Swal.fire({
                    title: "Congratulations!",
                    text: `Reassigned ${res.updated_count} lead(s) to ${res.new_owner}`,
                    icon: "success",
                    showCancelButton: true,
                    confirmButtonColor: "#4b49ac",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Done",
                }).then((result) => {
                    $('[data-bs-dismiss="offcanvas"]').trigger("click");
                    window.location.reload();
                });
            } else {
                alert(res.message || "Something went wrong.");
            }
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                // validation errors
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

// Recommendation Leads
$(".recommendation").on("click", function () {
    var offcanvasElement2 = document.getElementById(
        "recommendationOffcanvasEnd"
    );
    var bsOffcanvas2 = new bootstrap.Offcanvas(offcanvasElement2);
    bsOffcanvas2.show();
    offcanvasElement2.addEventListener(
        "shown.bs.offcanvas",
        function () {
            $(".js-example-basic-single").select2({
                dropdownParent: $(
                    "#recommendationOffcanvasEnd .offcanvas-body"
                ), // Ensure the dropdown is appended to the correct off-canvas element
            });
        },
        {
            once: true,
        }
    );
    getCheckedValues();
    $("#leadId").val(checkedValues);
});

$(document).on("submit", "#recommendation", function (e) {
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
                Swal.fire({
                    title: "Congratulations!",
                    text: `Receommended ${res.updated_count} lead(s) to ${res.new_owner}`,
                    icon: "success",
                    showCancelButton: true,
                    confirmButtonColor: "#4b49ac",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Done!",
                }).then((result) => {
                    window.location.reload();
                    $('[data-bs-dismiss="offcanvas"]').trigger("click");
                });
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

// Clear Filter
$(".clear-filter").on("click", function () {
    Swal.fire({
        title: "Are You Sure???",
        text: "You want to clear applied filter??",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#4b49ac",
        cancelButtonColor: "#d33",
        cancelButtonText: "No",
        confirmButtonText: "Yes",
    }).then((result) => {
        $.ajax({
            url: "/clear-filter",
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (res) {
                if (res.ok) {
                    Swal.fire({
                        title: "Great!",
                        text: res.message,
                        icon: "success",
                        showCancelButton: true,
                        confirmButtonColor: "#4b49ac",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Done!",
                    }).then((result) => {
                        window.location.reload();
                    });
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
        });
    });
});

// Add Application Id
$(".add-app-id").on("click", function () {
    getCheckedValues();
    if (checkedValues.length !== 1) {
        alert("Please select exactly one item to proceed.");
        return;
    } else {
        var offcanvasElement = document.getElementById("addAppIdOffcanvasEnd");
        var bsOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
        bsOffcanvas.show();
        offcanvasElement.addEventListener(
            "shown.bs.offcanvas",
            function () {
                $(".js-example-basic-single").select2({
                    dropdownParent: $("#addAppIdOffcanvasEnd .offcanvas-body"), // Ensure the dropdown is appended to the correct off-canvas element
                });
            },
            {
                once: true,
            }
        );
        $("#regLeadId").val(checkedValues);
    }
});

$(document).on("submit", "#add-application-id", function (e) {
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
                Swal.fire({
                    title: "Congratulations!",
                    text: `Application ID added successfully`,
                    icon: "success",
                    showCancelButton: true,
                    confirmButtonColor: "#4b49ac",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Done!",
                }).then((result) => {
                    window.location.reload();
                    $('[data-bs-dismiss="offcanvas"]').trigger("click");
                });
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
