  <div class="offcanvas offcanvas-end" tabindex="-1" id="addTeam" aria-labelledby="addTeamEndLabel">
    <div class="offcanvas-header">
      <h5 id="addTeamEndLabel" class="offcanvas-title">Add Teams</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">X</button>
    </div>
    <div class="offcanvas-body">
      <form id="storeTeams" method="POST">
        <div class="row">
          <div class="col-md-6 mb-3 col-12">
            <div class="form-group">
              <label class="mb-2" for="team_name">Team Name</label>
              <input type="text" id="team_name" class="form-control" placeholder="Enter Team Name" name="team_name">
            </div>
          </div>
          <div class="col-md-6 mb-3 col-12">
            <div class="form-group">
              <label class="mb-2" for="team_leader">Team Leader</label>
              <select id="team_leader" name="team_leader" class="form-control leader js-example-basic-single">
                <option value="">Select Team Leader Name</option>
              </select>
            </div>
          </div>
          <div class="mb-3 col-md-12 hstack gap-6  d-flex justify-content-end">
            <button class="btn btn-primary hstack gap-6" type="submit">
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