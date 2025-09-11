<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden rounded-4">
            <div class="modal-header text-white border-none p-4">
                <h5 class="modal-title fw-semibold fs-4">User Details( <span class="modalEmployeeCode"></span> )</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-center align-items-center flex-column">
                    <div class="modal-user-avatar-wrapper position-relative mx-auto">
                        <!-- Profile Picture -->
                        <img id="modalProfilePicture" class="modal-user-avatar img-fluid rounded-circle d-none"
                            alt="User Profile"
                            style="width:100px; height:100px; object-fit:cover; border:4px solid #fff;">

                        <!-- Fallback Avatar -->
                        <div id="modalUserAvatar"
                            class="modal-user-avatar bg-accent rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold"
                            style="font-size:2.5rem; border:4px solid #fff;">
                            
                        </div>

                        <!-- Status -->
                        <span class="user-status position-absolute rounded-circle bg-success"
                            style="width:22px; height:22px; bottom:5px; right:5px; border:3px solid #fff;"></span>
                    </div>
                    <h4 class="fw-bold fs-3 mt-3 text-primary"><span class="modalEmployeeCode"></span> | <span
                            id="modalUserName"></span>
                    </h4>
                    <div class="info-highlight rounded-2 bg-accent d-flex justify-content-center gap-2 mb-3 p-3">
                        <span
                            class="bg-super-light-primary text-primary px-3 py-1 rounded-pill fs-7 fw-medium d-flex align-items-center">
                            <i class="ti ti-crown me-1 fs-5"></i><span id="modalUserGroupName"></span>
                        </span> <span class="fs-4 fw-medium">|</span>
                        <span
                            class="bg-super-light-primary text-primary px-3 py-1 rounded-pill fs-7 fw-medium d-flex align-items-center">
                            <i class="ti ti-award me-1 fs-5"></i><span id="modalUserTeamName"></span>
                        </span>
                    </div>
                    <div class="d-flex justify-content-center gap-2">
                        <span id="callingBadge"
                            class="calling-badge text-success px-3 py-1 rounded-pill fs-7 fw-medium d-flex align-items-center">
                            <i class="ti ti-phone-calling me-1"></i> Calling Enabled
                        </span>
                        <span
                            class="last-login-badge text-info px-3 py-1 rounded-pill fs-7 fw-medium d-flex align-items-center">
                            <i class="ti ti-clock me-1"></i> Last Login: <span id="modalLastLogin"></span>
                        </span>
                    </div>
                </div>

                <ul class="nav nav-tabs" id="userDetailsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link border-none fw-medium py-2 px-3 text-secondary transition active"
                            id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">
                            <i class="ti ti-user me-1"></i> Personal Information
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link border-none fw-medium py-2 px-3 text-secondary transition" id="job-tab"
                            data-bs-toggle="tab" data-bs-target="#job" type="button" role="tab">
                            <i class="ti ti-briefcase me-1"></i> Professional Information
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link border-none fw-medium py-2 px-3 text-secondary transition"
                            id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button" role="tab">
                            <i class="ti ti-device-laptop me-1"></i> System Info
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link border-none fw-medium py-2 px-3 text-secondary transition"
                            id="leadSources-tab" data-bs-toggle="tab" data-bs-target="#LeadSources" type="button"
                            role="tab">
                            <i class="ti ti-building-broadcast-tower me-1"></i> Lead Sources
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link border-none fw-medium py-2 px-3 text-secondary transition"
                            id="telegram-tab" data-bs-toggle="tab" data-bs-target="#telegram" type="button" role="tab">
                            <i class="ti ti-brand-telegram me-1"></i> Telegram
                        </button>
                    </li>
                </ul>

                <div class="tab-content py-2 px-1" id="userDetailsTabsContent">
                    <!-- Contact Tab -->
                    <div class="tab-pane fade show active" id="contact" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-mail fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Email Address</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalPersonalEmail"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-device-mobile fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Personal Mobile No</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalPersonalMobile"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-cake fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Date Of Birth</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalDOB"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-user-question fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Gender</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalGender"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-id fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Pan Card No</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1">
                                            <span id="modalPanCard"></span>
                                            <button type="button" id="togglePanCard"
                                                class="btn btn-sm btn-outline-secondary ms-2">
                                                Show
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job Details Tab -->
                    <div class="tab-pane fade" id="job" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-layers-intersect fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Department</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalDepartment"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-briefcase fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Designation</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalDesignation"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-map fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Zone</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalZone"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-building fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Branch</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalBranch"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-calendar fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Date Of Join</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalDOJ"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 group-leader-col">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-crown fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Group leader</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalUserGroupLeader"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 team-leader-col">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-award fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Team Leader</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalUserTeamLeader"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-mail-star fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Official Email</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalofficialEmail"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-phone fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Official Mobile</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalOfficialMobile"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Info Tab -->
                    <div class="tab-pane fade" id="system" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-toggle-left fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Enabled For Leads</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalWorkingStatus"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-world-off fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Enabled For International Leads</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalIntFlag"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-calendar-off fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Inactive Start Date(Leaves)</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalInactiveStartDate"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-calendar-event fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Inactive End Date(Leaves)</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalInactiveEndDate"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-phone fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Enabled For Calling Overlay(App)</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalEnableCallingOverlay"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-device-mobile fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Enabled For App Access</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalAppAccess"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-bell fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Notification Token</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalFirebaseToken"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-file-text fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Script</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalScript"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="LeadSources" role="tabpanel">
                        <div class="row"></div>
                    </div>

                    <!-- Telegram Tab -->
                    <div class="tab-pane fade" id="telegram" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-user-code fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Telegram User Name</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalTelegramUserName"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-key fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Telegram Token</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1 text-truncate"
                                            id="modalTelegramToken"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-message-circle fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Telegram Chat ID</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalTelegramChatId"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="rounded-3 p-4 mb-3 border-start border-4 border-primary d-flex align-items-start shadow">
                                    <div class="bg-accent text-primary d-flex align-items-center rounded-2 me-3"
                                        style="width: 40px !important; height: 40px !important;">
                                        <i class="ti ti-brand-telegram fs-4"></i>
                                    </div>
                                    <div>
                                        <div
                                            class="detail-card bg-accent transition text-secondary fw-medium fs-7 mb-1">
                                            Telegram Channel</div>
                                        <div class="text-dark fw-medium fs-6 d-block text-break overflow-auto pe-1"
                                            id="modalTelegramChannel"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>