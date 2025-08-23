  <div class="offcanvas offcanvas-end" tabindex="-1" id="editOffcanvas" aria-labelledby="editGroupEndLabel">
    <div class="offcanvas-header">
      <h5 id="editGroupEndLabel" class="offcanvas-title">Edit groups</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">X</button>
    </div>
    <div class="offcanvas-body m-0 flex-grow-0 row">
      <form id="editForm" method="POST">
        <div class="row">
          <div class="col-lg-6 col-12">
            <div class="form-group">
              <label class="mb-2" for="editName">Group Name</label>
              <input type="text" id="editName" class="form-control" placeholder="Enter Group Name" name="group_name"
                readonly>
            </div>
          </div>
          <div class="col-lg-6 col-12">
            <div class="form-group">
              <label class="mb-2" for="editZone">Group Zone</label>
              <input type="hidden" name="id" id="editId" />
              <select id="editZone" name="group_zone" class="form-control editZone">
                <option value="">Select Group Zone</option>
              </select>
            </div>
          </div>
          <div class="col-lg-6 col-12">
            <div class="form-group">
              <label class="mb-2" for="editLeader">Group Leader</label>
              <select id="editLeader" name="group_leader" class="form-control editLeader">
                <option value="">Select Group Leader</option>
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