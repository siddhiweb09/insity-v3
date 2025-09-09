<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-center align-items-center flex-column">
                    <div class="user-avatar position-relative">
                        U
                        <span class="user-status status-active"></span>
                    </div>
                    <h4 class="user-name"><span id="modalEmployeeCode"></span> | <span id="modalUserName"></span></h4>
                    <div>
                        <span class="calling-badge">
                            <i class="ti ti-phone-calling me-1"></i> Calling Enabled
                        </span>
                        <span class="last-login-badge ms-2">
                            <i class="ti ti-clock me-1"></i> Last Login: <span id="modalLastLogin"></span>
                        </span>
                    </div>
                </div>

                <ul class="nav nav-tabs" id="userDetailsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact"
                            type="button" role="tab">
                            <i class="ti ti-user me-1"></i> Personal Information
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="job-tab" data-bs-toggle="tab" data-bs-target="#job" type="button"
                            role="tab">
                            <i class="ti ti-briefcase me-1"></i> Professional Information
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system"
                            type="button" role="tab">
                            <i class="ti ti-device-laptop me-1"></i> System Info
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="leadSources-tab" data-bs-toggle="tab" data-bs-target="#LeadSources"
                            type="button" role="tab">
                            <i class="ti ti-building-broadcast-tower me-1"></i> Lead Sources
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="telegram-tab" data-bs-toggle="tab" data-bs-target="#telegram"
                            type="button" role="tab">
                            <i class="ti ti-brand-telegram me-1"></i> Telegram
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="userDetailsTabsContent">
                    <!-- Contact Tab -->
                    <div class="tab-pane fade show active" id="contact" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-card d-flex align-items-start">
                                    <div class="icon-container">
                                        <i class="ti ti-mail fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="detail-label">Email Address</div>
                                        <div class="detail-value" id="modalPersonalEmail"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-card d-flex align-items-start">
                                    <div class="icon-container">
                                        <i class="ti ti-device-mobile fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="detail-label">Personal Mobile No</div>
                                        <div class="detail-value" id="modalPersonalMobile"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-card d-flex align-items-start">
                                    <div class="icon-container">
                                        <i class="ti ti-cake fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="detail-label">Date Of Birth</div>
                                        <div class="detail-value" id="modalDOB"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-card d-flex align-items-start">
                                    <div class="icon-container">
                                        <i class="ti ti-user-question fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="detail-label">Gender</div>
                                        <div class="detail-value" id="modalGender"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-card d-flex align-items-start">
                                    <div class="icon-container">
                                        <i class="ti ti-id fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="detail-label">Pan Card No</div>
                                        <div class="detail-value" ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job Details Tab -->
                    <div class="tab-pane fade" id="job" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-card d-flex align-items-start">
                                    <div class="icon-container">
                                        <i class="ti ti-id"></i>
                                    </div>
                                    <div>
                                        <div class="detail-label">Designation</div>
                                        <div class="detail-value">Senior Developer</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-card d-flex align-items-start">
                                    <div class="icon-container">
                                        <i class="ti ti-building"></i>
                                    </div>
                                    <div>
                                        <div class="detail-label">Branch</div>
                                        <div class="detail-value">New York Office</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-card d-flex align-items-start">
                                    <div class="icon-container">
                                        <i class="ti ti-map"></i>
                                    </div>
                                    <div>
                                        <div class="detail-label">Zone</div>
                                        <div class="detail-value">North East Region</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Info Tab -->
                    <div class="tab-pane fade" id="system" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-card d-flex align-items-start">
                                    <div class="icon-container">
                                        <i class="ti ti-key"></i>
                                    </div>
                                    <div>
                                        <div class="detail-label">Email Password</div>
                                        <div class="detail-value">••••••••</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-card d-flex align-items-start">
                                    <div class="icon-container">
                                        <i class="ti ti-file-text"></i>
                                    </div>
                                    <div>
                                        <div class="detail-label">Script</div>
                                        <div class="detail-value">Standard Sales Script v2.1</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-card d-flex align-items-start">
                                    <div class="icon-container">
                                        <i class="ti ti-clock"></i>
                                    </div>
                                    <div>
                                        <div class="detail-label">Last Login</div>
                                        <div class="detail-value">Today at 09:42 AM</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Telegram Tab -->
                    <div class="tab-pane fade" id="telegram" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-card d-flex align-items-start">
                                    <div class="icon-container">
                                        <i class="ti ti-key"></i>
                                    </div>
                                    <div>
                                        <div class="detail-label">Telegram Token</div>
                                        <div class="detail-value text-truncate">
                                            123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-card d-flex align-items-start">
                                    <div class="icon-container">
                                        <i class="ti ti-message-circle"></i>
                                    </div>
                                    <div>
                                        <div class="detail-label">Telegram Chat ID</div>
                                        <div class="detail-value">-1001234567890</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-card d-flex align-items-start">
                                    <div class="icon-container">
                                        <i class="ti ti-brand-telegram"></i>
                                    </div>
                                    <div>
                                        <div class="detail-label">Telegram Channel</div>
                                        <div class="detail-value">Company Announcements</div>
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