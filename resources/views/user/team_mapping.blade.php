@extends('frames.frame')

@section('content')
<div class="content-wrapper">
    <div class="card overflow-hidden p-4 shadow-none">
        <div class="row justify-content-between mb-4 mx-0">
            <h3 class="font-weight-500 mb-xl-4 text-primary">Add Teams</h3>
            <div class="btn-group" role="group" aria-label="Basic example">
                <button class="btn btn-primary addCheckedTeams d-flex align-items-center rounded-4" type="button"
                    data-bs-toggle="tooltip" data-bs-placement="top" title="Add Teams">
                    <i class="mdi mdi-plus fs-5 me-2"></i> Add Teams
                </button>
            </div>
        </div>

        <div class="row m-0">
            <div class="col-12 col-md-4 col-lg-4">
                <p class="card-text fw-bold">Group Name:</p>
                <h4 class="card-title">{{ $name }}</h4>
                <input type="hidden" id="groupNameId" name="group_name_id" value="{{ $id }}*{{ $name }}" />
            </div>
            <div class="col-12 col-md-4 col-lg-4">
                <p class="card-text fw-bold">Group Leader:</p>
                <h4 class="card-title">{{ $leaderCode }}*{{ $leaderName }}</h4>
            </div>
            <div class="col-12 col-md-4 col-lg-4">
                <p class="card-text fw-bold">Zone:</p>
                <h4 class="card-title">{{ $zone }}</h4>
            </div>
        </div>

        <div class="card-body" id="TeamsMappings_table" style="height:80vh; overflow-y:auto;">
            <div class="table-responsive" style="min-height: 400px;">
                <table class="table">
                    <thead class="bg-primary text-white" style="position: sticky; top: 0; z-index: 1;">
                        <tr>
                            <th>#</th>
                            <th id="col-th-1"><input type="checkbox" id="checkAll"> </th>
                            <th class="w-25">Team Name</th>
                            <th class="w-25">Team Leader</th>
                            <th class="w-25">Group Name</th>
                        </tr>
                    </thead>
                    <tbody class="teamsTableBody">
                        @forelse($teams as $index => $team)
                        @php
                        $isSameGroup = strcasecmp(trim($team->group_name), trim($name)) === 0;
                        @endphp
                        <tr class="{{ $isSameGroup ? 'selectedRows' : '' }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <input type="checkbox" value="{{ $team->id }}" name="check" {{ $isSameGroup ? 'checked disabled' : ($team->group_name ? 'disabled' : '') }}>
                            </td>
                            <td>{{ $team->team_name ?? '' }}</td>
                            <td>{{ $team->team_leader ?? '' }}</td>
                            <td>{{ $team->group_name ?? 'NA' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customJs')
<script>
    $(document).ready(function() {
        var scrollbar1 = document.getElementById("ViewTeams_table");
        if (scrollbar1) {
            new PerfectScrollbar(scrollbar1, {
                wheelPropagation: false
            });
        }
    });

    $(document).ready(function() {
        // Set CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            }
        });

        $(".addCheckedTeams").on("click", function() {
            getCheckedValues();
            let checkedValues = [];

            $("input[name='check']:checked").each(function() {
                checkedValues.push($(this).val());
            });
            console.log("Selected Team Values:", checkedValues);

            if (checkedValues.length === 0) {
                alert("Please select at least one checkbox.");
                return; // Stop execution
            }

            var selectedGroup = $("#groupNameId").val();
            console.log("Selected Group:", selectedGroup);

            $.ajax({
                type: "POST",
                url: "/teams-mapping-updation",
                dataType: "json",
                data: {
                    selected_group: selectedGroup,
                    selected_teams: checkedValues,
                },
                success: function(response) {
                    if (response.status === "success") {
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    } else {
                        alert(response.message || "Something went wrong!");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", xhr.responseText);
                    alert("Server error: " + xhr.status + " - " + error);
                }
            });
        });

    });
</script>
@endsection