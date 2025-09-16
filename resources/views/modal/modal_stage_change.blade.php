<div class="modal fade" id="leadStageChange" tabindex="-1" aria-labelledby="leadStageChangeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leadStageChangeLabel">Change Lead Stage</h5>
            </div>
            <form id="submit-lead-stage" method="POST" data-action="{{ route('leads.submitLeadStage') }}">
                <div class="modal-body row">

                    <input name="lead_id" type="text" class="form-control" id="leadId" hidden>

                    <div class="form-group col-lg-6 col-md-12">
                        <label>Lead Stage</label>
                        <select class="form-control form-control-sm js-example-basic-single w-100" id="lead_stage" name="lead_stage" required></select>
                    </div>
                    <div class="form-group col-lg-6 col-md-12">
                        <label>Lead Sub Stage</label>
                        <select class="form-control form-control-sm js-example-basic-single w-100" id="lead_sub_stage" name="lead_sub_stage" required></select>
                    </div>
                    <div class="form-group col-lg-6 col-md-12" id="follow"></div>
                    <div class="form-group col-lg-6 col-md-12" id="noteContainer">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-inverse-danger btn-fw" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = document.getElementById('leadStageChange');
        myModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            $("#leadId").val(id);

            $(".js-example-basic-single").select2();

            var lead_sub_stagesSelect = $("#lead_sub_stage");
            lead_sub_stagesSelect.append(
                $("<option>", {
                    value: "",
                    text: "Select Lead Sub-Stage",
                })
            );

            $.ajax({
                type: "POST",
                url: "/fetch-lead-stages",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function(response) {

                    var lead_stagesSelect = $("#lead_stage");
                    lead_stagesSelect.empty();
                    lead_stagesSelect.append(
                        $("<option>", {
                            value: "",
                            text: "Select Lead Stage",
                        })
                    );

                    var lead_stages = response.lead_stages;
                    $.each(lead_stages, function(index, lead_stage) {
                        lead_stagesSelect.append(
                            $("<option>", {
                                value: lead_stage,
                                text: lead_stage,
                            })
                        );
                    });
                },
                error: function(error) {
                    console.error("Error fetching lead_details:", error);
                },
            });

            $("#lead_stage").on("change", function() {
                var lead_stage = $(this).val();
                $("#follow").empty();

                $.ajax({
                    type: "POST",
                    url: "/fetch-lead-substages",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: {
                        lead_stage: lead_stage
                    },
                    success: function(response) {
                        var lead_sub_stages = response.lead_sub_stages;
                        var lead_sub_stagesSelect = $("#lead_sub_stage");
                        lead_sub_stagesSelect.empty();

                        $.each(lead_sub_stages, function(index, lead_sub_stage) {
                            lead_sub_stagesSelect.append(
                                $("<option>", {
                                    value: lead_sub_stage,
                                    text: lead_sub_stage,
                                })
                            );
                        });
                    },
                    error: function(error) {
                        console.error("Error fetching lead_details:", error);
                    },
                });

                if (lead_stage !== "Admission Done" && lead_stage !== "Scrap" && lead_stage !== "Admission In Process") {
                    $("#follow").append(
                        $("<label>", {
                            text: "Follow up Date",
                        }),
                        $("<div>", {
                            class: "row",
                            id: "row-box",
                        })
                    );
                    $("#row-box").append(
                        $("<div>", {
                            class: "col",
                            id: "datepicker-box",
                        }),
                        $("<div>", {
                            class: "col",
                            id: "timepicker-box",
                        })
                    );
                    $("#datepicker-box").append(
                        $("<input>", {
                            class: "form-control",
                            id: "datepicker",
                            type: "date",
                            name: "followup_date"
                        })
                    );
                    $("#timepicker-box").append(
                        $("<input>", {
                            class: "form-control",
                            id: "timepicker",
                            type: "time",
                            name: "followup_time"
                        })
                    );
                }

                if (lead_stage === "Admission Done") {
                    $("#noteContainer").empty();
                    $("#noteContainer").append(`<label>Application ID</label>
                        <input class="form-control form-control-sm" name="applicatin_id" required />`);
                } else {
                    $("#noteContainer").empty();
                    $("#noteContainer").append(`<label>Note</label>
                        <textarea class="form-control form-control-sm" name="note"></textarea>`);
                }
            });
        });
    });
</script>