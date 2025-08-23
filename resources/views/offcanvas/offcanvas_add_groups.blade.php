  <div class="offcanvas offcanvas-end" tabindex="-1" id="addGroup" aria-labelledby="addGroupEndLabel">
    <div class="offcanvas-header">
      <h5 id="addGroupEndLabel" class="offcanvas-title">Add groups</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">X</button>
    </div>
    <div class="offcanvas-body">
      <form id="storeGroups" method="POST">
        @csrf
        <div class="row">
          <div class="col-lg-6 col-12">
            <div class="form-group">
              <label class="mb-2" for="group_name">Group Name</label>
              <input type="text" id="group_name" class="form-control" placeholder="Enter Group Name" name="group_name">
            </div>
          </div>
          <div class="col-lg-6 col-12">
            <div class="form-group">
              <label class="mb-2" for="group_zone">Group Zone</label>
              <select id="group_zone" name="group_zone" class="form-control zone js-example-basic-single">
                <option value="">Select Group Zone</option>
              </select>
            </div>
          </div>
          <div class="col-lg-6 col-12">
            <div class="form-group">
              <label class="mb-2" for="group_leader">Group Leader</label>
              <select id="group_leader" name="group_leader" class="form-control counselor js-example-basic-single">
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