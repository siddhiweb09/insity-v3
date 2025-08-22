// Assuming jQuery is already loaded
$(document).ready(function () {
  var rawDataForm = $("#raw-data-form");
  var sourceDataForm = $("#source-data-box");
  var filterSearchBox = $("#filterSearch-box");
  var option1 = ["Select User", "All", "Zone", "Branch", "Counselor"];
  var option2 = ["Select User", "All", "Branch", "Counselor"];
  var option3 = ["Select User", "All", "Counselor"];
  // var option4 = ["Select Source", "Shine", "Naukari","NMAT","CMAT","12th Pass","University"];

  // var option4 = [];

  // function fetchOption(type, callback) {
  //   $.ajax({
  //     url: "dbFiles/get_dropdown_options.php",
  //     method: "POST",
  //     data: { type: type },
  //     dataType: "json",
  //     success: function (response) {
  //       callback(response); // Pass the data to the callback
  //     },
  //     error: function (err) {
  //       console.error(`Error fetching ${type}:`, err);
  //     },
  //   });
  // }

  // fetchOption("option4", function (data) {
  //   option4 = data;
  //   console.log("Option4:", option4);
  // });

  var option4 = [];

  function fetchOption(type, callback) {
    $.ajax({
      url: "dbFiles/get_dropdown_options.php",
      method: "POST",
      data: { type: type },
      dataType: "json",
      success: function (response) {
        callback(response); // Pass the data to the callback
      },
      error: function (err) {
        console.error(`Error fetching ${type}:`, err);
      },
    });
  }

  fetchOption("option4", function (data) {
    option4 = data;

    // Create and append label + select
    $("#source-box").append(
      $("<label>", {
        text: "Select Source",
      }),
      $("<select>", {
        class:
          "form-control lead_source form-control-sm js-example-basic-single w-100",
        id: "source",
        name: "source",
      })
    );

    // Append options to the select
    option4.forEach(function (option) {
      $("#source").append(
        $("<option>", {
          value: option,
          text: option,
        })
      );
    });

    // ✅ Initialize Select2 AFTER the select is in the DOM
    $("#source").select2();
  });

  fetch_requests();

  sourceDataForm.append(
    $("<div>", {
      class: "col-lg-12 col-md-12",
      id: "request-display-box",
    }),
    $("<div>", {
      class: "form-group col-lg-6 col-md-12",
      id: "source-box",
    }),
    $("<div>", {
      class: "form-group col-lg-6 col-md-12",
      id: "request-box",
    })
  );

  // $("#source-box").append(
  //   $("<label>", {
  //     text: "Select Source",
  //   }),
  //   $("<select>", {
  //     class:
  //       "form-control lead_source form-control-sm js-example-basic-single w-100",
  //     id: "source",
  //     name: "source",
  //   })
  // );
  // option4.forEach(function (option) {
  //   $("#source").append(
  //     $("<option>", {
  //       value: option,
  //       text: option,
  //     })
  //   );
  // });

  $("#request-box").append(
    $("<label>", {
      text: "Select Request",
    }),
    $("<select>", {
      class:
        "form-control lead_source form-control-sm js-example-basic-single w-100",
      id: "request",
      name: "request",
    })
  );

  if (jobTitleDesignation === "Managing Director") {
    filterSearchBox.append(
      $("<select>", {
        class: "form-control js-example-basic-single w-100",
        id: "filterSearch",
      })
    );
    option1.forEach(function (option) {
      $("#filterSearch").append(
        $("<option>", {
          value: option,
          text: option,
        })
      );
    });
  } else if (jobTitleDesignation === "Zonal Head") {
    filterSearchBox.append(
      $("<select>", {
        class: "form-control js-example-basic-single w-100",
        id: "filterSearch",
      })
    );
    option2.forEach(function (option) {
      $("#filterSearch").append(
        $("<option>", {
          value: option,
          text: option,
        })
      );
    });
  } else if (jobTitleDesignation === "Branch Manager") {
    filterSearchBox.append(
      $("<select>", {
        class: "form-control js-example-basic-single w-100",
        id: "filterSearch",
      })
    );
    option3.forEach(function (option) {
      $("#filterSearch").append(
        $("<option>", {
          value: option,
          text: option,
        })
      );
    });
  } else {
    rawDataForm.append(
      $("<div>", {
        class: "form-group col-lg-6 col-md-12",
        id: "file-box",
      }),
      $("<div>", {
        class: "form-group col-12",
        id: "form-button",
      })
    );
    $("#file-box").append(
      $("<input>", {
        class: "file-upload-default",
        name: "csvFile",
        type: "file",
      }),
      $("<div>", {
        class: "input-group col-xs-12",
        id: "input-group-box",
      })
    );
    $("#input-group-box").append(
      $("<input>", {
        class: "form-control file-upload-info",
        name: "csvFile",
        type: "file",
      })
    );

    $("#form-button").append(
      $("<button>", {
        class: "btn btn-primary mr-2",
        text: "Submit",
        type: "submit",
      }),
      $("<a>", {
        class: "btn btn-light",
        text: "Cancel",
        href: "#",
      })
    );
  }
  $(".js-example-basic-single").select2();

  $("#filterSearch").on("change", function () {
    var filterValue = $(this).val();
    rawDataForm.empty();

    if (filterValue === "All") {
      rawDataForm.append(
        $("<div>", {
          class: "form-group col-lg-6 col-md-12",
          id: "file-box",
        })
      );
      $("#file-box").append(
        $("<input>", {
          class: "file-upload-default",
          name: "csvFile",
          type: "file",
        }),
        $("<div>", {
          class: "input-group col-xs-12",
          id: "input-group-box",
        })
      );
      $("#input-group-box").append(
        $("<input>", {
          class: "form-control file-upload-info",
          name: "csvFile",
          type: "file",
        })
      );
    } else if (filterValue === "Zone") {
      rawDataForm.append(
        $("<div>", {
          class: "form-group col-lg-6 col-md-12",
          id: "zone-box",
        })
      );
      $("#zone-box").append(
        $("<select>", {
          class: "form-control js-example-basic-single w-100",
          id: "zone",
          name: "zone",
        })
      );
      fetch_zones();
      rawDataForm.append(
        $("<div>", {
          class: "form-group col-lg-6 col-md-12",
          id: "file-box",
        })
      );
      $("#file-box").append(
        $("<input>", {
          class: "file-upload-default",
          name: "csvFile",
          type: "file",
        }),
        $("<div>", {
          class: "input-group col-xs-12",
          id: "input-group-box",
        })
      );
      $("#input-group-box").append(
        $("<input>", {
          class: "form-control file-upload-info",
          name: "csvFile",
          type: "file",
        })
      );
    } else if (filterValue === "Branch") {
      rawDataForm.append(
        $("<div>", {
          class: "form-group col-lg-6 col-md-12",
          id: "zone-box",
        }),
        $("<div>", {
          class: "form-group col-lg-6 col-md-12",
          id: "branch-box",
        }),
        $("<div>", {
          class: "form-group col-lg-6 col-md-12",
          id: "file-box",
        })
      );
      $("#zone-box").append(
        $("<select>", {
          class: "form-control js-example-basic-single w-100",
          id: "zone",
          name: "zone",
        })
      );
      $("#branch-box").append(
        $("<select>", {
          class: "form-control js-example-basic-single w-100",
          id: "branch",
          name: "branch",
        })
      );
      $("#file-box").append(
        $("<input>", {
          class: "file-upload-default",
          name: "csvFile",
          type: "file",
        }),
        $("<div>", {
          class: "input-group col-xs-12",
          id: "input-group-box",
        })
      );
      $("#input-group-box").append(
        $("<input>", {
          class: "form-control file-upload-info",
          name: "csvFile",
          type: "file",
        })
      );
      $(".js-example-basic-single").select2();
      fetch_zones();
      $("#zone").on("change", function () {
        var zone = $(this).val();
        fetch_branches(zone);
      });
      var branchesSelect = $("#branch");
      branchesSelect.empty();

      branchesSelect.append(
        $("<option>", {
          value: "",
          text: "Select a Branch",
          disabled: true,
          selected: true,
        })
      );
    } else if (filterValue === "Counselor") {
      rawDataForm.append(
        $("<div>", {
          class: "form-group col-lg-6 col-md-12",
          id: "zone-box",
        }),
        $("<div>", {
          class: "form-group col-lg-6 col-md-12",
          id: "branch-box",
        }),
        $("<div>", {
          class: "form-group col-lg-6 col-md-12",
          id: "counselor-box",
        }),
        $("<div>", {
          class: "form-group col-lg-6 col-md-12",
          id: "file-box",
        })
      );
      $("#zone-box").append(
        $("<select>", {
          class: "form-control js-example-basic-single w-100",
          id: "zone",
          name: "zone",
        })
      );
      $("#branch-box").append(
        $("<select>", {
          class: "form-control js-example-basic-single w-100",
          id: "branch",
          name: "branch",
        })
      );
      $("#counselor-box").append(
        $("<select>", {
          class: "form-control js-example-basic-single w-100",
          id: "counselor",
          name: "counselor[]",
          multiple: "multiple",
        })
      );
      $("#file-box").append(
        $("<input>", {
          class: "file-upload-default",
          name: "csvFile",
          type: "file",
        }),
        $("<div>", {
          class: "input-group col-xs-12",
          id: "input-group-box",
        })
      );
      $("#input-group-box").append(
        $("<input>", {
          class: "form-control file-upload-info",
          name: "csvFile",
          type: "file",
        })
      );
      $(".js-example-basic-single").select2();
      fetch_zones();
      $("#zone").on("change", function () {
        var zone = $(this).val();
        fetch_branches(zone);
      });
      var branchesSelect = $("#branch");
      branchesSelect.empty();

      branchesSelect.append(
        $("<option>", {
          value: "",
          text: "Select a Branch",
          disabled: true,
          selected: true,
        })
      );
      $("#branch").on("change", function () {
        var branch = $(this).val();
        fetch_counselors(branch);
      });
      var counselorsSelect = $("#counselor");
      counselorsSelect.empty();

      // counselorsSelect.append(
      //   $("<option>", {
      //     value: "",
      //     text: "Select a Counselor",
      //     disabled: true,
      //     selected: false,
      //   })
      // );
    } else {
    }
    rawDataForm.append(
      $("<div>", {
        class: "form-group col-12",
        id: "form-button",
      })
    );
    $("#form-button").append(
      $("<button>", {
        class: "btn btn-primary mr-2",
        text: "Submit",
        type: "submit",
      }),
      $("<a>", {
        class: "btn btn-light",
        text: "Cancel",
        href: "#",
      })
    );
  });

  // Fetch zones
  function fetch_zones() {
    $.ajax({
      type: "POST",
      url: "dbFiles/fetch_zones.php",
      dataType: "json",
      success: function (response) {
        var zones = response.zones;
        var zonesSelect = $("#zone");
        zonesSelect.empty();

        zonesSelect.append(
          $("<option>", {
            value: "",
            text: "Select a zone",
            disabled: true,
            selected: true,
          })
        );

        $.each(zones, function (index, zone) {
          zonesSelect.append(
            $("<option>", {
              value: zone,
              text: zone,
            })
          );
        });
      },
      error: function (error) {
        console.error("Error fetching zones:", error);
      },
    });
  }

  // Fetch branches
  function fetch_branches(zone) {
    $.ajax({
      type: "POST",
      url: "dbFiles/fetch_branches.php",
      dataType: "json",
      data: { zone: zone },
      success: function (response) {
        var branches = response.branchs;
        var branchesSelect = $("#branch");
        branchesSelect.empty();

        branchesSelect.append(
          $("<option>", {
            value: "",
            text: "Select a Branch",
            disabled: true,
            selected: true,
          })
        );

        $.each(branches, function (index, branch) {
          branchesSelect.append(
            $("<option>", {
              value: branch,
              text: branch,
            })
          );
        });
      },
      error: function (error) {
        console.error("Error fetching branches:", error);
      },
    });
  }

  // Fetch counselors
  function fetch_counselors(branch) {
    $.ajax({
      type: "POST",
      url: "dbFiles/fetch_counselors.php",
      dataType: "json",
      data: { branch: branch },
      success: function (response) {
        var counselors = response.counselors;
        var counselorsSelect = $("#counselor");
        counselorsSelect.empty();

        // counselorsSelect.append(
        //   $("<option>", {
        //     value: "",
        //     text: "Select a Counselor",
        //     disabled: true,
        //     selected: true,
        //   })
        // );

        $.each(counselors, function (index, counselor) {
          counselorsSelect.append(
            $("<option>", {
              value: counselor,
              text: counselor,
            })
          );
        });
      },
      error: function (error) {
        console.error("Error fetching counselors:", error);
      },
    });
  }

  // Fetch counselors
  function fetch_requests() {
    $.ajax({
      type: "POST",
      url: "dbFiles/fetch_marketing_data_requests.php",
      data: { dataId: "all" },
      dataType: "json",
      success: function (response) {
        if (Array.isArray(response) && response.length > 0) {
          $("#request").append(
            $("<option>", {
              value: "",
              text: "Select a Request",
              disabled: true,
              selected: true,
            })
          );
          response.forEach((item) => {
            if (item.requested_for) {
              var requestValue =
                item.requested_by +
                ", " +
                item.requested_branch +
                ", " +
                item.requested_zone;

              $("#request").append(
                $("<option>", {
                  value: item.id,
                  text: requestValue,
                })
              );
            }
          });
        } else {
          console.error("No data found in response");
        }
      },
      error: function (error) {
        console.error("Error fetching counselors:", error);
      },
    });
  }

  // Fetch counselors
  function fetch_requests_details(requestId) {
    $.ajax({
      type: "POST",
      url: "dbFiles/fetch_requests_details.php",
      dataType: "json",
      data: { requestId: requestId },
      success: function (response) {
        $("#request-display-box").empty();
        $("#request-display-box").append(
          `<div class="d-flex mb-3">
                                            <div>
                                                <p class="text-info mb-1">` +
            response.requested_branch +
            ", " +
            response.requested_zone +
            `</p>
                                                <p class="mb-0"><b>Requested by: </b>` +
            response.requested_by +
            `</p>
                                                <p class="mb-0"><b>Requested Count: </b>` +
            response.requested_count +
            `</p>
                                                <p class="mb-0"><b>Requested for: </b>` +
            response.requested_for +
            `</p>
                                                <p class="mb-0"><b>Locations: </b>` +
            response.locations +
            `</p>
                                                <p class="mb-0"><b>Criteria: </b>` +
            response.criteria +
            `</p>
                                                <small>` +
            response.requested_on +
            `</small>
                                            </div>
                                        </div>`
        );
      },
      error: function (error) {
        console.error("Error fetching counselors:", error);
      },
    });
  }

  $("#request").on("change", function () {
    var requestId = $(this).val();
    $("#requestId").val(requestId);
    fetch_requests_details(requestId);
  });
});
