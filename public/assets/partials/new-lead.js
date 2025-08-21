$(".newlead").click(function () {
  var offcanvasElement = document.getElementById("newleadOffcanvasEnd");
  var bsOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
  bsOffcanvas.show();
  // Clear and set default options for select elements

  var levelSelect = $(".level");
  var courseSelect = $(".course");
  var citySelect = $(".city");
  levelSelect.empty().append(
    $("<option>", {
      value: "",
      text: "Select Level",
    })
  );
  courseSelect.empty().append(
    $("<option>", {
      value: "",
      text: "Select Course",
    })
  );
  citySelect.empty().append(
    $("<option>", {
      value: "",
      text: "Select City",
    })
  );
  // Initialize select2 for level and course selects

  levelSelect.select2({
    dropdownParent: $("#newleadOffcanvasEnd .offcanvas-body"),
  });
  courseSelect.select2({
    dropdownParent: $("#newleadOffcanvasEnd .offcanvas-body"),
  });
  citySelect.select2({
    dropdownParent: $("#newleadOffcanvasEnd .offcanvas-body"),
  });
  // Handle the shown.bs.offcanvas event

  offcanvasElement.addEventListener(
    "shown.bs.offcanvas",
    function () {
      $(".js-example-basic-single").select2({
        dropdownParent: $("#newleadOffcanvasEnd .offcanvas-body"),
      });
      // Fetch states and update select2

      $.ajax({
        type: "POST",
        url: "dbFiles/fetch_states.php",
        dataType: "json",
        success: function (response) {
          var states = response.states;
          var statesSelect = $(".state");
          statesSelect.empty();
          $.each(states, function (index, state) {
            statesSelect.append(
              $("<option>", {
                value: state,
                text: state,
              })
            );
          });
          // Initialize select2 for the states select

          statesSelect.select2({
            dropdownParent: $("#newleadOffcanvasEnd .offcanvas-body"),
          });
        },
        error: function (error) {
          console.error("Error fetching states:", error);
        },
      });
    },
    {
      once: true,
    }
  );
});

$(".state").on("change", function () {
  var state = $(this).val();
  $.ajax({
    type: "POST",
    url: "dbFiles/fetch_cities.php",
    dataType: "json",
    data: {
      state: state,
    },
    success: function (response) {
      var cities = response.cities;
      var citiesSelect = $(".city");
      citiesSelect.empty();
      $.each(cities, function (index, city) {
        citiesSelect.append(
          $("<option>", {
            value: city,
            text: city,
          })
        );
      });
    },
    error: function (error) {
      console.error("Error fetching cities:", error);
    },
  });
});

$(".widget_name").on("change", function () {
  var entity = $(this).val();
  $.ajax({
    type: "POST",
    url: "dbFiles/fetch_levels.php",
    dataType: "json",
    data: {
      entity: entity,
    },
    success: function (response) {
      var levels = response.levels;
      var levelsSelect = $(".level");
      levelsSelect.empty();
      $.each(levels, function (index, level) {
        levelsSelect.append(
          $("<option>", {
            value: level,
            text: level,
          })
        );
      });
    },
    error: function (error) {
      console.error("Error fetching levels:", error);
    },
  });
});

$(".level").on("change", function () {
  var level = $(this).val();
  var entity = $(".widget_name").val();
  $.ajax({
    type: "POST",
    url: "dbFiles/fetch_courses.php",
    dataType: "json",
    data: {
      level: level,
      entity: entity,
    },
    success: function (response) {
      var courses = response.courses;
      var coursesSelect = $(".course");
      coursesSelect.empty();
      $.each(courses, function (index, course) {
        coursesSelect.append(
          $("<option>", {
            value: course,
            text: course,
          })
        );
      });
    },
    error: function (error) {
      console.error("Error fetching courses:", error);
    },
  });
});

$(".lead_source").on("change", function () {
  var lead_source = $(this).val();

  if (lead_source === "Reference") {
    $("#ref_box").append(`
      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Reference from</label>
        <div class="col-sm-4">
          <div class="form-check">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" name="reference_category" id="reference_category1" value="enquiry" checked="">
              Enquiry
              <i class="input-helper"></i>
            </label>
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-check">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" name="reference_category" id="reference_category2" value="student">
              Student
              <i class="input-helper"></i>
            </label>
          </div>
        </div>
      </div>
      <div class="form-group row">
        <label class="col-sm-2 col-form-label py-0">Name of Referrer</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="referrer" id="referrer">
        </div>
      </div>
    `);

    // <select name="referrer" class="form-control js-example-basic-single w-100 referrer">
    //   <option value="">Select Referrer Name</option>
    // </select>

    $(".referrer").select2({
      dropdownParent: $("#newleadOffcanvasEnd .offcanvas-body"),
    });

    $(document).on("click", "input[name='reference_category']", function () {
      const selectedValue = $(this).val();
      if (selectedValue === "student") {
        fetch_referrer("dbFiles/fetch_students.php");
      } else {
        fetch_referrer("dbFiles/fetch_enquiry.php");
      }
    });

    fetch_referrer("dbFiles/fetch_enquiry.php");
  } else {
    $("#ref_box").empty();
  }
});

function fetch_referrer(url) {
  $.ajax({
    type: "POST",
    url: url,
    dataType: "json",
    success: function (response) {
      var referrerSelect = $(".referrer");
      referrerSelect.empty();
      referrerSelect.append(
        $("<option>", {
          value: "",
          text: "Select Referrer Name",
        })
      );
      $.each(response, function (index, referrer) {
        referrerSelect.append(
          $("<option>", {
            value: referrer,
            text: referrer,
          })
        );
      });
    },
    error: function (error) {
      console.error("Error fetching referrers:", error);
    },
  });
}
