  <div class="offcanvas offcanvas-end" tabindex="-1" id="editOffcanvas" aria-labelledby="editTeamEndLabel">
    <div class="offcanvas-header">
      <h5 id="editTeamEndLabel" class="offcanvas-title">Edit Team</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">X</button>
    </div>
    <div class="offcanvas-body m-0 flex-grow-0">
      <form id="editForm" method="POST">
        <div class="row">
          <div class="col-md-6 col-12">
            <div class="form-group">
              <label class="mb-2" for="editName">Team Name</label>
              <input type="text" id="editName" class="form-control" placeholder="Enter Team Name" name="team_name" readonly>
            </div>
          </div>
          <div class="col-md-6 col-12">
            <div class="form-group">
              <input type="hidden" id="editId" name="team_id">
              <label class="mb-2" for="editLeader">Team Leader</label>
              <select id="editLeader" name="team_leader" class="form-control editLeader js-example-basic-single">
                <option value="">Select Team Leader Name</option>
              </select>
            </div>
          </div>

          <div class="mb-3 col-md-12 hstack gap-6  d-flex justify-content-end">
            <button class="btn btn-primary" type="submit">
              <i class="ti ti-send fs-5"></i>
              Submit
            </button>
            <button class="btn bg-danger-subtle text-danger hstack gap-6" type="reset">
              <i class="ti ti-refresh fs-5"></i>
              Reset
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>