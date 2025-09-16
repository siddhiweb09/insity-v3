@extends('frames.frame')

@section('content')
<style>
  /* Chatbox container */
  .whatsapp_chatbox {
    width: auto;
    height: 300px;
    border: 1px solid #ddd;
    overflow-y: auto;
    padding: 10px;
    background-color: #f5f5f5;
    font-family: Arial, sans-serif;
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
  }
  .whatsapp_message { display: flex; flex-direction: column; margin: 10px 0; }
  .whatsapp_message.sent { align-items: flex-end; }
  .whatsapp_message.received { align-items: flex-start; }
  .whatsapp_message-bubble {
    background-color: #ffffff; padding: 12px; border-radius: 15px; border: 1px solid #ddd;
    max-width: 70%; font-size: 14px; line-height: 1.5;
  }
  .whatsapp_message.sent .whatsapp_message-bubble { background-color: #4747a1; color: #fff; border-bottom-right-radius: 0; }
  .whatsapp_message.received .whatsapp_message-bubble { background-color: #f1f1f1; color: #000; border-bottom-left-radius: 0; }
  .whatsapp_message .reply-box { margin-top: 5px; padding: 8px; background-color: #f0f0f0; border-left: 4px solid #4747a1; font-size: 13px; color: #555; }
  .whatsapp_message .reply-box small.text-muted { display: block; font-size: 12px; margin-bottom: 4px; }
  .whatsapp_message .reply-box span { display: block; margin-top: 4px; }
  .whatsapp_message .message-time { font-size: 12px; }

  /* newcss */
  .link-container { background-color: #f1f6ff; border-radius: 10px; padding: 20px; margin-top: 25px; display: none; border-left: 4px solid var(--primary); }
  .generated-link { color: var(--primary); font-weight: 500; word-break: break-all; padding: 12px; background: white; border-radius: 8px; border: 1px dashed #ccc; }
  .action-buttons .btn { margin-left: 10px; border-radius: 6px; }
  .institution-badge { position: absolute; top: 25px; right: 25px; background: var(--secondary); color: white; padding: 5px 15px; border-radius: 20px; font-size: 14px; font-weight: 500; }
  .modal-content { border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
  .form-label { font-weight: 500; color: #3c3c3c; }
  .toast { position: fixed; bottom: 20px; right: 20px; z-index: 1000; border-radius: 8px; }
  .institution-selector { background-color: white; border-radius: 10px; padding: 15px; margin-bottom: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
  .instructions { background-color: #e9f7ff; border-radius: 10px; padding: 15px; margin-top: 20px; font-size: 14px; }
  .feature-icon { width: 50px; height: 50px; background: rgba(67,97,238,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; color: var(--primary); font-size: 20px; }
  .student-info { background-color: #f8f9fa; border-radius: 10px; padding: 15px; margin-bottom: 20px; }
  .info-item { display: flex; margin-bottom: 8px; }
  .info-label { font-weight: 600; min-width: 150px; color: #495057; }
  .info-value { color: #212529; }
  .modalclosebtn { background: none; border: none; font-size: 20px; line-height: 1; cursor: pointer; padding: 10px !important; }
</style>

<div class="content-wrapper">
  {{-- Top Nav Buttons --}}
  <div class="navigation-buttons justify-content-between mx-0 mb-4 row">
    @isset($prevLeadId)
      <a href="{{ route('leads.details', ['info' => base64_encode($prevLeadId)]) }}">
        <button type="button" class="btn btn-inverse-primary btn-rounded btn-icon">
          <i class="mdi mdi-arrow-left-bold" style="font-size:1.5rem;"></i>
        </button>
      </a>
    @endisset

    @isset($nextLeadId)
      <a href="{{ route('leads.details', ['info' => base64_encode($nextLeadId)]) }}">
        <button type="button" class="btn btn-inverse-primary btn-rounded btn-icon">
          <i class="mdi mdi-arrow-right-bold" style="font-size:1.5rem;"></i>
        </button>
      </a>
    @endisset
  </div>

  <div class="row">
    {{-- Left card --}}
    <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
      <div class="card card-light-blue">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div class="pr-2 col-7 pl-0">
              <div class="row m-0">
                <div class="col-lg-8 p-0">
                  <h3 class="font-weight-500 text-white">
                    {{ $lead->registered_name ?? 'N/A' }}
                    <span class="badge badge-light ml-2">{{ $lead->registration_attempts ?? 0 }}</span>
                  </h3>
                </div>
                <div class="col-lg-4 p-0">
                  @if(!empty($isExistingStudent))
                    <button type="button" class="text flash1 m-0 btn btn-link text-white"
                      data-bs-toggle="modal" data-bs-target="#existingStudentModal" data-id="{{ $lead->id }}">
                      <div class="element">
                        <p class="text flash1 m-0">This is Our Existing Student</p>
                      </div>
                    </button>
                  @endif
                </div>
              </div>

              <p class="card-title mb-xl-4 text-white text-decoration-underline">
                {{ $lead->lead_status ?? 'N/A' }}
                <button type="button" class="btn text-white btn-sm btn-transparent"
                  data-bs-toggle="modal" data-bs-target="#leadStageChange" data-id="{{ $lead->id }}">
                  <i class="ml-2 mdi mdi-open-in-new"></i>
                </button>
              </p>

              <p class="fs-25 mb-2"><b>Email: </b>{{ $lead->registered_email ?? 'N/A' }}</p>
              <p class="fs-25 mb-2"><b>Mobile: </b>{{ $lead->registered_mobile ?? 'N/A' }}</p>
              <p class="fs-25 mb-2"><b>Added On: </b>{{ $lead->lead_assignment_date ?? 'N/A' }}</p>
              <p class="fs-25 mb-2"><b>Last Active: </b>{{ $lead->last_lead_activity_date ?? 'N/A' }}</p>
            </div>

            <div class="action-icons-box col-5 align-items-end">
              @php $w = $lead->widget_name ?? ''; @endphp
              @if($w === 'ISBMU')
                <div>
                  <span>{{ $w }} - </span>
                  <img src="{{ asset('assets/images/logo/isbm-university-favicon.png') }}" class="action-icons bg-white">
                </div>
              @elseif($w === 'ISTM')
                <div>
                  <span>{{ $w }} - </span>
                  <img src="{{ asset('assets/images/logo/istm-favicon.png') }}" class="action-icons bg-white">
                </div>
              @elseif(in_array($w, ['ISBM','ISBMA']))
                <div>
                  <span>{{ $w }} - </span>
                  <img src="{{ asset('assets/images/logo/isbm-favicon.jpg') }}" class="action-icons">
                </div>
              @elseif(in_array($w, ['Acadment','ACADMENT']))
                <div>
                  <span>{{ $w }} - </span>
                  <img src="{{ asset('assets/images/logo/acadment-favicon.png') }}" class="action-icons bg-white">
                </div>
              @endif

              @if(!empty($lead->registered_mobile))
                <a href="tel:{{ $lead->registered_mobile }}">
                  <img src="{{ asset('assets/images/file-icons/call.png') }}" class="action-icons mt-3">
                </a>
              @endif

              <button type="button" class="btn text-white btn-sm btn-transparent p-0"
                data-bs-toggle="modal" data-bs-target="#leadStageChange" data-id="{{ $lead->id }}">
                <img src="{{ asset('assets/images/file-icons/edit.png') }}" class="action-icons">
              </button>

              <button type="button" class="btn text-white btn-sm btn-transparent p-0"
                data-bs-toggle="modal" data-bs-target="#aadharVerificationModal" data-id="{{ $lead->id }}">
                <img src="{{ asset('assets/images/file-icons/verified.png') }}" class="action-icons">
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Right card --}}
    <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
      <div class="card card-dark-blue">
        <div class="card-body">
          <div class="row mx-0 row-cols-2">
            <div class="col mb-3">
              <p class="card-title text-white">Communication Status</p>
              <p>Email Sent - <b>{{ $lead->email_sent_count ?? 0 }}</b></p>
              <p>SMS Sent - <b>{{ $lead->sms_sent_count ?? 0 }}</b></p>
              <p>Whatsapp Message Sent - <b>{{ $lead->whatsapp_message_count ?? 0 }}</b></p>
            </div>
            <div class="col mb-3">
              <p class="card-title text-white">Assigned Counsellor</p>
              <p>{{ $lead->lead_owner ?? 'N/A' }}</p>
            </div>
            <div class="col mb-3">
              <p class="card-title text-white">Upcoming Followup</p>
              <p>{{ $lead->lead_followup_date ?? 'N/A' }}</p>
              <p>Followup Counts: {{ $lead->followup_count ?? 0 }}</p>
            </div>
            <div class="col mb-3">
              <p class="card-title text-white">Lead Source</p>
              <p>{{ $lead->lead_source ?? 'N/A' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Main tabs --}}
  <div class="row m-0 mt-5">
    <div class="col-md-9 py-0 ps-0 tab-content" id="v-pills-tabContent">

      {{-- Lead Details --}}
      <div class="tab-pane card fade" id="v-pills-lead" role="tabpanel" aria-labelledby="v-pills-lead-tab" tabindex="0">
        <div class="card-body">
          <div class="row mx-0 justify-content-between mb-4">
            <h3 class="text-decoration-underline">Lead Details</h3>
            <button type="button" class="btn btn-primary editLead btn-rounded btn-icon"
              data-bs-toggle="offcanvas" data-bs-target="#editLead" aria-controls="offcanvasEnd"
              data-id="{{ base64_encode($lead->log_id ?? '') }}">
              <i class="ti-pencil"></i>
            </button>
          </div>

          <div class="row mx-0 mb-2"><h5 class="col-lg-5"><b>ID: </b></h5><h5 class="col-lg-6">{{ $lead->id ?? 'N/A' }}</h5></div>
          <div class="row mx-0 mb-2"><h5 class="col-lg-5"><b>Alternate Mobile Number: </b></h5><h5 class="col-lg-6">{{ $lead->alternate_mobile ?? 'N/A' }}</h5></div>
          <div class="row mx-0 mb-2"><h5 class="col-lg-5"><b>Registered State: </b></h5><h5 class="col-lg-6">{{ $lead->state ?? 'N/A' }}</h5></div>
          <div class="row mx-0 mb-2"><h5 class="col-lg-5"><b>Registered City: </b></h5><h5 class="col-lg-6">{{ $lead->city ?? 'N/A' }}</h5></div>
          <div class="row mx-0 mb-2"><h5 class="col-lg-5"><b>Level Applying: </b></h5><h5 class="col-lg-6">{{ $lead->level_applying_for ?? 'N/A' }}</h5></div>
          <div class="row mx-0 mb-2"><h5 class="col-lg-5"><b>Course: </b></h5><h5 class="col-lg-6">{{ $lead->course ?? 'N/A' }}</h5></div>

          <div class="row mx-0 justify-content-between mb-4">
            <h3 class="text-decoration-underline">Last Interactions</h3>
          </div>
          <div class="row mx-0 mb-2"><h5 class="col-lg-5"><b>Lead Stage: </b></h5><h5 class="col-lg-6">{{ $lead->lead_stage ?? 'N/A' }}</h5></div>
          <div class="row mx-0 mb-2"><h5 class="col-lg-5"><b>Lead Sub Stage: </b></h5><h5 class="col-lg-6">{{ $lead->lead_sub_stage ?? 'N/A' }}</h5></div>
          <div class="row mx-0 mb-2"><h5 class="col-lg-5"><b>Note: </b></h5><h5 class="col-lg-6">{{ $lead->lead_remark ?? 'N/A' }}</h5></div>
        </div>
      </div>

      {{-- Timeline --}}
      <div class="tab-pane card fade show active" id="v-pills-timeline" role="tabpanel" aria-labelledby="v-pills-timeline-tab" tabindex="0">
        <div class="card-body">
          <div class="row justify-content-between mx-0">
            <h6 class="card-title">Timeline</h6>
            <p class="text-secondary">Stage Change Count: {{ $lead->stage_change_count ?? 0 }}</p>
          </div>
          <div id="content">
            <ul class="timeline">
              @forelse(($logs ?? []) as $log)
                <li class="event" data-date="{{ $log->created_at }}">
                  <h3>{{ $log->task }}</h3>
                  @if(!empty($log->followup_date))
                    <p>Follow Up On: <b>{{ $log->followup_date }}</b></p>
                  @endif
                  @if(!empty($log->recorded_file))
                    <a href="{{ asset('AppAPI/recordings/'.$log->recorded_file) }}" target="_blank"><i class="mdi mdi-play-circle"></i> Play Recording</a>
                  @endif
                </li>
              @empty
                <li class="event" data-date="-"><h3>No timeline entries</h3></li>
              @endforelse
            </ul>
          </div>
        </div>
      </div>

      {{-- Recommendation --}}
      <div class="tab-pane card fade show" id="v-pills-recommendation" role="tabpanel" aria-labelledby="v-pills-recommendation-tab" tabindex="0">
        <div class="card-body">
          <div class="row mx-0 justify-content-between mb-4">
            <h6 class="card-title">Recommendation</h6>
            <button type="button" class="btn btn-primary btn-rounded btn-icon" id="recommendationButton"
              data-bs-toggle="offcanvas" data-bs-target="#recommendationOffcanvasEnd" aria-controls="recommendationOffcanvasEnd">
              <i class="mdi mdi-plus"></i>
            </button>
          </div>
          <div id="content">
            <ul class="timeline">
              @forelse(($recommendations ?? []) as $rec)
                <li class="event" data-date="{{ $rec->added_on }}">
                  <div class="col mx-0 mb-3 justify-content-between">
                    <h3>{{ $rec->recommendation }}</h3>
                    <p>Recommended by: {{ $rec->added_by }}</p>
                  </div>
                </li>
              @empty
                <li class="event" data-date="-"><h3>No recommendations</h3></li>
              @endforelse
            </ul>
          </div>
        </div>
      </div>

      {{-- Communication --}}
      <div class="tab-pane card fade" id="v-pills-communication" role="tabpanel" aria-labelledby="v-pills-communication-tab" tabindex="0">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item" role="presentation"><button class="nav-link active" id="call-tab" data-bs-toggle="tab" data-bs-target="#call-tab-pane" type="button" role="tab" aria-controls="call-tab-pane" aria-selected="true">Calls</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email-tab-pane" type="button" role="tab" aria-controls="email-tab-pane" aria-selected="false">Email</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" id="whatsapp-tab" data-bs-toggle="tab" data-bs-target="#whatsapp-tab-pane" type="button" role="tab" aria-controls="whatsapp-tab-pane" aria-selected="false">Whatsapp</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" id="sms-tab" data-bs-toggle="tab" data-bs-target="#sms-tab-pane" type="button" role="tab" aria-controls="sms-tab-pane" aria-selected="false">SMS</button></li>
        </ul>

        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active" id="call-tab-pane" role="tabpanel" aria-labelledby="call-tab" tabindex="0">
            <div class="row row-cols-1 row-cols-md-3 g-4">
              <div class="col"><div class="shadow-lg p-3 mb-5 bg-body rounded"><h4><b>{{ $lead->outbound_success ?? 0 }}</b></h4><h6>Outbound Success</h6></div></div>
              <div class="col"><div class="shadow-lg p-3 mb-5 bg-body rounded"><h4><b>{{ $lead->outbound_missed ?? 0 }}</b></h4><h6>Outbound Missed</h6></div></div>
              <div class="col"><div class="shadow-lg p-3 mb-5 bg-body rounded"><h4><b>{{ $lead->outbound_total ?? 0 }}</b></h4><h6>Total Outbound Calls</h6></div></div>
              <div class="col"><div class="shadow-lg p-3 mb-5 bg-body rounded"><h4><b>{{ $lead->inbound_success ?? 0 }}</b></h4><h6>Inbound Success</h6></div></div>
              <div class="col"><div class="shadow-lg p-3 mb-5 bg-body rounded"><h4><b>{{ $lead->inbound_missed ?? 0 }}</b></h4><h6>Inbound Success</h6></div></div>
              <div class="col"><div class="shadow-lg p-3 mb-5 bg-body rounded"><h4><b>{{ $lead->inbound_total ?? 0 }}</b></h4><h6>Total Inbound Calls</h6></div></div>
            </div>
          </div>

          <div class="tab-pane fade" id="email-tab-pane" role="tabpanel" aria-labelledby="email-tab" tabindex="0">Will Launch this Feature Soon...</div>

          <div class="tab-pane fade" id="whatsapp-tab-pane" role="tabpanel" aria-labelledby="whatsapp-tab" tabindex="0">
            <div id="whatsapp_chatbox" class="whatsapp_chatbox">
              <div class="whatsapp_message">
                <div class="whatsapp_message-bubble"><span>This is the original message</span></div>
                <div class="reply-box">
                  <small class="text-muted">Reply to: This is the original message</small>
                  <span>hie</span>
                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="sms-tab-pane" role="tabpanel" aria-labelledby="sms-tab" tabindex="0">Will Launch this Feature Soon...</div>
        </div>
      </div>

      {{-- Generate Link --}}
      <div class="tab-pane card fade" id="v-pills-generate-link" role="tabpanel" aria-labelledby="v-pills-generate-link-tab" tabindex="0">
        <div class="header text-center mb-0 pt-5">
          <h2><i class="fas fa-link me-2"></i>Student Link Generator</h2>
          <p class="mb-0">Generate application and payment links for students</p>
        </div>

        <div class="card mb-4 position-relative">
          <div class="institution-badge" id="institutionBadge">ISBMU</div>
          <div class="card-body">
            <div class="student-info">
              <h5 class="mb-3"><i class="fas fa-user-graduate me-2"></i>Student Information</h5>
              <div class="info-item"><span class="info-label">Name:</span><span class="info-value" id="infoName">-</span></div>
              <div class="info-item"><span class="info-label">Mobile:</span><span class="info-value" id="infoMobile">-</span></div>
              <div class="info-item"><span class="info-label">Email:</span><span class="info-value" id="infoEmail">-</span></div>
              <div class="info-item"><span class="info-label">Branch:</span><span class="info-value" id="infoBranch">-</span></div>
              <div class="info-item"><span class="info-label">Zone:</span><span class="info-value" id="infoZone">-</span></div>
              <div class="info-item d-none"><span class="info-label">Lead Id:</span><span class="info-value" id="infoLeadId">-</span></div>
              <div class="info-item"><span class="info-label">Counsellor:</span><span class="info-value" id="infoCounsellor">-</span></div>
            </div>

            <div class="institution-selector">
              <label class="form-label"><i class="fas fa-university me-2"></i>Select Institution</label>
              <select class="form-select" id="institutionSelect">
                <option value="ISBM">ISBM</option>
                <option value="ISBMU" selected>ISBM University</option>
                <option value="ISTM">ISTM</option>
              </select>
            </div>

            <div class="d-grid gap-2 mb-4">
              <button class="btn btn-primary me-md-3" id="generateApplicationLink">
                <i class="fas fa-file-alt me-2"></i>Generate Application Form Link
              </button>
              <button class="btn btn-success" id="generatePaymentLink">
                <i class="fas fa-credit-card me-2"></i>Generate Payment Form Link
              </button>
            </div>

            <div class="link-container" id="linkResult">
              <h6 class="mb-3"><i class="fas fa-link me-2"></i>Generated Link:</h6>
              <p class="generated-link" id="generatedLink"></p>
              <div class="action-buttons mt-3 d-flex justify-content-end">
                <button class="btn btn-sm btn-outline-secondary" id="copyLink">
                  <i class="fas fa-copy me-1"></i>Copy Link
                </button>
                <button class="btn btn-sm btn-outline-primary" id="shareLink">
                  <i class="fas fa-share-alt me-1"></i>Share
                </button>
              </div>
            </div>

            <div class="instructions">
              <h6><i class="fas fa-info-circle me-2"></i>How to use:</h6>
              <ul class="mb-0">
                <li>Select the institution from the dropdown</li>
                <li>Click on either button to generate the appropriate link</li>
                <li>A form will open with pre-filled data that you can modify</li>
                <li>Submit the form to generate your customized link</li>
                <li>Copy or share the generated link with students</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

    </div>

    {{-- Right side tab nav --}}
    <div class="col-md-3 lead-tabs card nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
      <button class="nav-link" id="v-pills-lead-tab" data-bs-toggle="pill" data-bs-target="#v-pills-lead" type="button" role="tab" aria-controls="v-pills-lead" aria-selected="false">Lead Details</button>
      <button class="nav-link active" id="v-pills-timeline-tab" data-bs-toggle="pill" data-bs-target="#v-pills-timeline" type="button" role="tab" aria-controls="v-pills-timeline" aria-selected="true">Timeline</button>
      <button class="nav-link" id="v-pills-recommendation-tab" data-bs-toggle="pill" data-bs-target="#v-pills-recommendation" type="button" role="tab" aria-controls="v-pills-recommendation" aria-selected="true">Recommendation</button>
      <button class="nav-link" id="v-pills-communication-tab" data-bs-toggle="pill" data-bs-target="#v-pills-communication" type="button" role="tab" aria-controls="v-pills-communication" aria-selected="false">Communication</button>
      <button class="nav-link" id="v-pills-generate-link-tab" data-bs-toggle="pill" data-bs-target="#v-pills-generate-link" type="button" role="tab" aria-controls="v-pills-generate-link" aria-selected="false">Generate-Link</button>
    </div>
  </div>
</div>

{{-- Modals --}}
@includeIf('modal.modal_stage_change')
@includeIf('leads.partials.modal_existing_student')
@includeIf('leads.partials.offcanvas_edit_lead_details')
@includeIf('leads.partials.offcanvas_recommendation_leads')
@includeIf('leads.partials.modal_aadhar_verification')

{{-- Application Form Modal --}}
<div class="modal fade" id="applicationFormModal" tabindex="-1" aria-labelledby="applicationFormModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="applicationFormModalLabel">Generate Application Link</h5></div>
    <div class="modal-body">
      <div class="form-header">
        <h6><i class="fas fa-info-circle me-2"></i>Edit the student details as needed</h6>
        <p class="mb-0">The link will be generated based on these values</p>
      </div>
      <form id="applicationForm">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="appName" class="form-label">Name</label>
            <input type="text" class="form-control" id="appName" required>
          </div>
          <div class="col-md-6 mb-3">
            <label for="appMobile" class="form-label">Mobile Number</label>
            <input type="text" class="form-control" id="appMobile" required>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="appEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="appEmail" required>
          </div>
          <div class="col-md-3 mb-3">
            <label for="appBranch" class="form-label">Branch</label>
            <input type="text" class="form-control" id="appBranch" readonly>
          </div>
          <div class="col-md-3 mb-3">
            <label for="appZone" class="form-label">Zone</label>
            <input type="text" class="form-control" id="appZone" readonly>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="appCounsellor" class="form-label">Counsellor Name</label>
            <input type="text" class="form-control" id="appCounsellor" readonly>
          </div>
          <div class="col-md-6 mb-3">
            <label for="appWidget" class="form-label">Widget Name</label>
            <select class="form-select" id="appWidget" required>
              <option value="ISBM">ISBM</option>
              <option value="ISBMU" selected>ISBM University</option>
              <option value="ISTM">ISTM</option>
            </select>
          </div>
        </div>
        <input type="text" class="form-control" id="appLeadId" hidden>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">close</button>
      <button type="button" class="btn btn-primary" id="submitApplicationForm">Generate Application Link</button>
    </div>
  </div></div>
</div>

{{-- Payment Form Modal --}}
<div class="modal fade" id="paymentFormModal" tabindex="-1" aria-labelledby="paymentFormModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="paymentFormModalLabel">Generate Payment Link</h5></div>
    <div class="modal-body">
      <div class="form-header">
        <h6><i class="fas fa-info-circle me-2"></i>Edit the student details as needed</h6>
        <p class="mb-0">The payment link will be generated based on these values</p>
      </div>
      <form id="paymentForm">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="payName" class="form-label">Name</label>
            <input type="text" class="form-control" id="payName" required>
          </div>
          <div class="col-md-6 mb-3">
            <label for="payMobile" class="form-label">Mobile Number</label>
            <input type="text" class="form-control" id="payMobile" required>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="payEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="payEmail" required>
          </div>
          <div class="col-md-6 mb-3">
            <label for="payBranch" class="form-label">Branch</label>
            <input type="text" class="form-control" id="payBranch" readonly>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="payCounsellor" class="form-label">Counsellor Name</label>
            <input type="text" class="form-control" id="payCounsellor" readonly>
          </div>
          <div class="col-md-6 mb-3">
            <label for="payWidget" class="form-label">Widget Name</label>
            <select class="form-select" id="payWidget" required>
              <option value="ISBM">ISBM</option>
              <option value="ISBMU" selected>ISBM University</option>
              <option value="ISTM">ISTM</option>
            </select>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-6 mb-3">
            <label for="paymentAmount" class="form-label">Payment Amount (₹)</label>
            <input type="number" class="form-control" id="paymentAmount" required>
          </div>
          <div class="col-md-6 mb-3">
            <label for="enrollmentNumber" class="form-label">Enrollment Number</label>
            <input type="text" class="form-control" id="enrollmentNumber" required>
          </div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">close</button>
      <button type="button" class="btn btn-primary" id="submitPaymentForm">Generate Payment Link</button>
    </div>
  </div></div>
</div>

{{-- Success Toast --}}
<div class="toast align-items-center text-white bg-success" role="alert" aria-live="assertive" aria-atomic="true" id="successToast">
  <div class="d-flex">
    <div class="toast-body"><i class="fas fa-check-circle me-2"></i>Link copied to clipboard!</div>
    <button type="button" class="modalclosebtn text-white ms-2 me-2" data-bs-dismiss="toast" aria-label="Close">×</button>
  </div>
</div>
@endsection

@section('customJs')
{{-- If you have a plugins partial, include it here --}}
{{-- @includeIf('assets.partials.plugins_js') --}}

<script>
  // Provide lead data from controller
  const lead = @json($lead ?? (object)[]);
  const timelineLogs = @json($logs ?? []);
  const institutionSelect = document.getElementById('institutionSelect');
  const institutionBadge = document.getElementById('institutionBadge');

  document.addEventListener('DOMContentLoaded', function () {
    // Fill right card stats and student info for link generator
    const infoMap = {
      infoName: lead.registered_name || '-',
      infoMobile: lead.registered_mobile || '-',
      infoEmail: lead.registered_email || '-',
      infoBranch: lead.branch || '-',
      infoZone: lead.zone || '-',
      infoLeadId: lead.id || '-',
      infoCounsellor: lead.lead_owner || '-',
    };
    Object.entries(infoMap).forEach(([id, val]) => {
      const el = document.getElementById(id);
      if (el) el.textContent = val;
    });

    // Default widget
    const widgetName = (lead.widget_name || 'ISBMU').toUpperCase();
    if (institutionSelect) institutionSelect.value = widgetName;
    if (institutionBadge) institutionBadge.textContent = widgetName;

    if (institutionSelect) {
      institutionSelect.addEventListener('change', function () {
        if (institutionBadge) institutionBadge.textContent = this.value;
      });
    }

    // Modals
    const applicationModal = new bootstrap.Modal(document.getElementById('applicationFormModal'));
    const paymentModal = new bootstrap.Modal(document.getElementById('paymentFormModal'));
    const toast = new bootstrap.Toast(document.getElementById('successToast'));

    // Open Application Form Modal
    document.getElementById('generateApplicationLink')?.addEventListener('click', function () {
      document.getElementById('appName').value = lead.registered_name || '';
      document.getElementById('appMobile').value = lead.registered_mobile || '';
      document.getElementById('appEmail').value = lead.registered_email || '';
      document.getElementById('appBranch').value = lead.branch || '';
      document.getElementById('appZone').value = lead.zone || '';
      document.getElementById('appCounsellor').value = lead.lead_owner || '';
      document.getElementById('appLeadId').value = lead.id || '';
      document.getElementById('appWidget').value = (lead.widget_name || 'ISBMU').toUpperCase();
      applicationModal.show();
    });

    // Submit Application Form
    document.getElementById('submitApplicationForm')?.addEventListener('click', function () {
      const form = document.getElementById('applicationForm');
      if (!form.checkValidity()) { form.reportValidity(); return; }

      const name = document.getElementById('appName').value;
      const mobile = document.getElementById('appMobile').value;
      const email = document.getElementById('appEmail').value;
      const branch = document.getElementById('appBranch').value;
      const zone = document.getElementById('appZone').value;
      const counsellor = document.getElementById('appCounsellor').value;
      const leadId = document.getElementById('appLeadId').value;
      const widget = document.getElementById('appWidget').value;

      let baseUrl;
      switch (widget) {
        case 'ISBMU': baseUrl = 'isbmuniversity.edu.in/apply-online'; break;
        case 'ISTM': baseUrl = 'istm.org.in/apply-online'; break;
        default: baseUrl = 'isbm.org.in/online-application';
      }

      const queryString = `name=${encodeURIComponent(name)}&mobile=${mobile}&email=${encodeURIComponent(email)}&branch=${encodeURIComponent(branch)}&zone=${encodeURIComponent(zone)}&counsellor=${encodeURIComponent(counsellor)}&leadId=${encodeURIComponent(leadId)}`;
      const encoded = btoa(unescape(encodeURIComponent(queryString)));
      const link = `https://${baseUrl}?data=${encoded}`;

      document.getElementById('generatedLink').textContent = link;
      document.getElementById('linkResult').style.display = 'block';
      applicationModal.hide();
    });

    // Open Payment Form Modal
    document.getElementById('generatePaymentLink')?.addEventListener('click', function () {
      document.getElementById('payName').value = lead.registered_name || '';
      document.getElementById('payMobile').value = lead.registered_mobile || '';
      document.getElementById('payEmail').value = lead.registered_email || '';
      document.getElementById('payBranch').value = lead.branch || '';
      document.getElementById('payCounsellor').value = lead.lead_owner || '';
      document.getElementById('payWidget').value = (lead.widget_name || 'ISBMU').toUpperCase();
      paymentModal.show();
    });

    // Submit Payment Form
    document.getElementById('submitPaymentForm')?.addEventListener('click', function () {
      const form = document.getElementById('paymentForm');
      if (!form.checkValidity()) { form.reportValidity(); return; }

      const name = document.getElementById('payName').value;
      const mobile = document.getElementById('payMobile').value;
      const email = document.getElementById('payEmail').value;
      const branch = document.getElementById('payBranch').value;
      const counsellor = document.getElementById('payCounsellor').value;
      const widget = document.getElementById('payWidget').value;
      const amount = document.getElementById('paymentAmount').value;
      const enrollmentNumber = document.getElementById('enrollmentNumber').value;

      let baseUrl;
      switch (widget) {
        case 'ISBMU': baseUrl = 'isbmuniversity.edu.in/pay-fees-online'; break;
        case 'ISTM': baseUrl = 'istm.org.in/pay-fees'; break;
        default: baseUrl = 'isbm.org.in/pay-fees';
      }

      const queryString = `name=${encodeURIComponent(name)}&mobile=${mobile}&email=${encodeURIComponent(email)}&branch=${encodeURIComponent(branch)}&counsellor=${encodeURIComponent(counsellor)}&amount=${amount}&enrollmentnumber=${encodeURIComponent(enrollmentNumber)}`;
      const encoded = btoa(unescape(encodeURIComponent(queryString)));
      const link = `https://${baseUrl}?data=${encoded}`;

      document.getElementById('generatedLink').textContent = link;
      document.getElementById('linkResult').style.display = 'block';
      paymentModal.hide();
    });

    // Copy / Share
    document.getElementById('copyLink')?.addEventListener('click', function () {
      const link = document.getElementById('generatedLink').textContent;
      navigator.clipboard.writeText(link).then(() => { toast.show(); });
    });

    document.getElementById('shareLink')?.addEventListener('click', function () {
      const link = document.getElementById('generatedLink').textContent;
      if (navigator.share) {
        navigator.share({ title: 'Student Link', text: 'Here is your student link:', url: link }).catch(console.log);
      } else {
        alert('Web Share API not supported in your browser. The link is: ' + link);
      }
    });

    // Hidden leadId field used elsewhere
    const leadIdField = document.getElementById('leadId');
    if (leadIdField && lead?.id) {
      leadIdField.value = `${lead.id}*${lead.registered_name || ''}`;
    }
  });
</script>
@endsection