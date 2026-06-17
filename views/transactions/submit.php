<?php
/**
 * Transaction Submission Form View for SDO FAST.
 */

$currentPage = 'submit_transaction';
$pageTitle = 'Submit Transaction';
$pageHeader = 'Submit Transaction';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/document_checklist.php';

$userRole = $_SESSION['user_role'] ?? '';

// Fetch tax configuration keys for dropdown select
$taxConfigurations = [];
if ($fastPDO !== null) {
    try {
        $taxConfigurations = $fastPDO->query("SELECT * FROM tax_configurations WHERE is_active = 1")->fetchAll();
    } catch (PDOException $e) {
        error_log("Failed to fetch tax configs: " . $e->getMessage());
    }
}
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold text-primary-dark">New Transaction Submission Form</h5>
            </div>
            <div class="card-body">
                <form id="submitTransactionForm" onsubmit="handleFormSubmit(event)" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <!-- Basic details -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="transactionType" class="form-label fs-8 fw-semibold text-muted">Transaction Type <span class="text-danger">*</span></label>
                            <select name="transaction_type" id="transactionType" class="form-select" required>
                                <option value="" disabled selected>Select Type</option>
                                <option value="Cash Advance">Cash Advance</option>
                                <option value="Reimbursement">Reimbursement</option>
                                <option value="Payroll">Payroll</option>
                            </select>
                        </div>
                        
                        <div class="col-12 col-sm-6">
                            <label for="targetDate" class="form-label fs-8 fw-semibold text-muted">Target Completion Date</label>
                            <input type="date" name="target_date" id="targetDate" class="form-control" min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="col-12">
                            <label for="eventName" class="form-label fs-8 fw-semibold text-muted">Particulars / Event Name <span class="text-danger">*</span></label>
                            <input type="text" name="event_name" id="eventName" class="form-control" placeholder="e.g. SDO Seminar Reimbursement for Math Teachers" required>
                        </div>
                    </div>
                    
                    <!-- ====================================================================
                         CASH ADVANCE COVERAGE SECTION
                         ==================================================================== -->
                    <div id="cashAdvanceCategorySection" class="mb-4 d-none p-3 rounded-3 border bg-light">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="cashAdvanceCategory" class="form-label fs-8 fw-semibold text-muted">Cash Advance Coverage Type <span class="text-danger">*</span></label>
                                <select name="cash_advance_category" id="cashAdvanceCategory" class="form-select">
                                    <option value="" disabled selected>Select Coverage Type</option>
                                    <option value="Travel">Travel (land transpo excluded)</option>
                                    <option value="School MOOE">School MOOE</option>
                                    <option value="SBFP">SBFP (School Based Feeding Program)</option>
                                    <option value="Training">Training</option>
                                    <option value="Meals">Meals</option>
                                    <option value="Accommodation">Accommodation</option>
                                    <option value="Meals and Accommodation">Meals and Accommodation</option>
                                    <option value="Honorarium">Honorarium</option>
                                    <option value="Supplies and Materials">Supplies and Materials</option>
                                    <option value="Communication Expenses">Communication Expenses</option>
                                    <option value="SLAC / Moving-Up / Graduation / GAWAD">SLAC / Moving-Up / Graduation / GAWAD and similar events</option>
                                </select>
                            </div>
                        </div>

                        <!-- CA: Date & Venue Fields (Travel, Training, Meals, Accommodation, M&A, SLAC/GAWAD) -->
                        <div id="caDateVenueContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-primary-dark mb-3 fs-7"><i class="bi bi-calendar-event me-1"></i>Schedule & Venue Details</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-sm-6">
                                        <label for="caStartDate" class="form-label fs-8 fw-semibold text-muted">Inclusive Start Date <span class="text-danger">*</span></label>
                                        <input type="date" name="mooe_start_date" id="caStartDate" class="form-control">
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="caEndDate" class="form-label fs-8 fw-semibold text-muted">Inclusive End Date <span class="text-danger">*</span></label>
                                        <input type="date" name="mooe_end_date" id="caEndDate" class="form-control">
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label for="caVenue" class="form-label fs-8 fw-semibold text-muted">Venue <span class="text-danger">*</span></label>
                                        <input type="text" name="venue" id="caVenue" class="form-control" placeholder="e.g. Regional Office, Hotel Venue Name">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CA: Fund Source Field (Travel, School MOOE, SBFP) -->
                        <div id="caFundSourceContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-primary-dark mb-3 fs-7"><i class="bi bi-wallet2 me-1"></i>Fund Source</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label for="caFundSource" class="form-label fs-8 fw-semibold text-muted">Fund Source <span class="text-danger">*</span></label>
                                        <input type="text" name="fund_source" id="caFundSource" class="form-control" placeholder="e.g. School MOOE, Division MOOE, SEF">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CA: TA + Itinerary Uploads (Travel only) -->
                        <div id="caTaItineraryContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-primary-dark mb-3 fs-7"><i class="bi bi-airplane-engines me-1"></i>Travel Documents</h6>
                                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-2 mb-3 py-2 px-3" style="font-size: 0.8rem;">
                                    <i class="bi bi-info-circle-fill fs-6 text-primary"></i>
                                    <div><strong>Note:</strong> For Travel Cash Advance, the attached documents you must upload below are your <strong>Approved TA (Travel Authority)</strong> and <strong>Travel Itinerary</strong>.</div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-sm-6">
                                        <label for="approvedTa" class="form-label fs-8 fw-semibold text-muted">Upload Approved TA (Travel Authority) <span class="text-danger">*</span></label>
                                        <input type="file" name="approved_ta" id="approvedTa" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" style="padding-top: 10px;">
                                        <small class="text-muted fs-9">PDF, JPG, PNG, DOCX up to 10MB.</small>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="travelItinerary" class="form-label fs-8 fw-semibold text-muted">Upload Travel Itinerary <span class="text-danger">*</span></label>
                                        <input type="file" name="travel_itinerary" id="travelItinerary" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" style="padding-top: 10px;">
                                        <small class="text-muted fs-9">PDF, JPG, PNG, DOCX up to 10MB.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CA: Activity Proposal Upload (Training, SLAC/GAWAD) -->
                        <div id="caActivityProposalContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-primary-dark mb-3 fs-7"><i class="bi bi-journal-check me-1"></i>Activity Proposal</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label for="caActivityProposal" class="form-label fs-8 fw-semibold text-muted">Upload Activity Proposal <span class="text-danger">*</span></label>
                                        <input type="file" name="activity_proposal" id="caActivityProposal" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" style="padding-top: 10px;">
                                        <small class="text-muted fs-9">PDF, JPG, PNG, DOCX up to 10MB.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CA: Month Selector (Communication Expenses) -->
                        <div id="caMonthContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-primary-dark mb-3 fs-7"><i class="bi bi-telephone-inbound me-1"></i>Communication Period</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label for="caMonth" class="form-label fs-8 fw-semibold text-muted">Select Month <span class="text-danger">*</span></label>
                                        <select name="ca_month" id="caMonth" class="form-select">
                                            <option value="" disabled selected>Select Month</option>
                                            <?php
                                            $currentYear = (int)date('Y');
                                            $prevYear = $currentYear - 1;
                                            $monthsList = [
                                                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 
                                                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 
                                                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                                            ];
                                            for ($y = $currentYear; $y >= $prevYear; $y--) {
                                                for ($m = 12; $m >= 1; $m--) {
                                                    $mLabel = $monthsList[$m] . ' ' . $y;
                                                    echo '<option value="' . htmlspecialchars($mLabel) . '">' . htmlspecialchars($mLabel) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ====================================================================
                         REIMBURSEMENT COVERAGE SECTION
                         ==================================================================== -->
                    <div id="reimbursementCategorySection" class="mb-4 d-none p-3 rounded-3 border bg-light">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="reimbursementCategory" class="form-label fs-8 fw-semibold text-muted">Reimbursement Coverage Type <span class="text-danger">*</span></label>
                                <select name="reimbursement_category" id="reimbursementCategory" class="form-select">
                                    <option value="" disabled selected>Select Coverage Type</option>
                                    <option value="Travel">Travel</option>
                                    <option value="Supplies and Materials">Supplies and Materials</option>
                                    <option value="Meals">Meals</option>
                                    <option value="Accommodation">Accommodation</option>
                                    <option value="Meals and Accommodation">Meals and Accommodation</option>
                                    <option value="Honorarium">Honorarium</option>
                                    <option value="Communication Load">Communication Load</option>
                                    <option value="Utility Bills">Utility Bills (Electricity, Water, Telephone, Internet)</option>
                                    <option value="Repair, Repaint, Improvement">Repair, Repaint, Improvement</option>
                                    <option value="Installation of Electricity and Water">Installation of Electricity and Water</option>
                                    <option value="Installation of Internet / Telephone">Installation of Internet / Telephone</option>
                                    <option value="Seminars / Trainings">Seminars / Trainings (from Enclosure 12)</option>
                                    <option value="GAD Documents / SLAC Session">GAD Documents / SLAC Session</option>
                                    <option value="Job Order">Job Order</option>
                                    <option value="Fidelity Bond">Fidelity Bond</option>
                                    <option value="Immersion and Insurance for SHS">Immersion and Insurance for SHS</option>
                                </select>
                            </div>
                        </div>

                        <!-- Reimb: Date & Venue Fields (Travel, Meals, Accommodation, M&A, Seminars/Trainings, GAD/SLAC) -->
                        <div id="reimbDateVenueContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-primary-dark mb-3 fs-7"><i class="bi bi-calendar-event me-1"></i>Schedule & Venue Details</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-sm-6">
                                        <label for="reimbStartDate" class="form-label fs-8 fw-semibold text-muted">Inclusive Start Date <span class="text-danger">*</span></label>
                                        <input type="date" name="reimb_start_date" id="reimbStartDate" class="form-control">
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="reimbEndDate" class="form-label fs-8 fw-semibold text-muted">Inclusive End Date <span class="text-danger">*</span></label>
                                        <input type="date" name="reimb_end_date" id="reimbEndDate" class="form-control">
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label for="reimbVenue" class="form-label fs-8 fw-semibold text-muted">Venue <span class="text-danger">*</span></label>
                                        <input type="text" name="reimb_venue" id="reimbVenue" class="form-control" placeholder="e.g. SDO Conference Hall, School Gym">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reimb: TA + Itinerary Uploads (Travel only) -->
                        <div id="reimbTaItineraryContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-primary-dark mb-3 fs-7"><i class="bi bi-airplane-engines me-1"></i>Travel Documents</h6>
                                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-2 mb-3 py-2 px-3" style="font-size: 0.8rem;">
                                    <i class="bi bi-info-circle-fill fs-6 text-primary"></i>
                                    <div><strong>Note:</strong> For Travel Reimbursement, please upload your <strong>Approved TA (Travel Authority)</strong> and <strong>Travel Itinerary</strong>.</div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-sm-6">
                                        <label for="reimbApprovedTa" class="form-label fs-8 fw-semibold text-muted">Upload Approved TA (Travel Authority) <span class="text-danger">*</span></label>
                                        <input type="file" name="reimb_approved_ta" id="reimbApprovedTa" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" style="padding-top: 10px;">
                                        <small class="text-muted fs-9">PDF, JPG, PNG, DOCX up to 10MB.</small>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="reimbTravelItinerary" class="form-label fs-8 fw-semibold text-muted">Upload Travel Itinerary <span class="text-danger">*</span></label>
                                        <input type="file" name="reimb_travel_itinerary" id="reimbTravelItinerary" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" style="padding-top: 10px;">
                                        <small class="text-muted fs-9">PDF, JPG, PNG, DOCX up to 10MB.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reimb: Activity Proposal Upload (Seminars/Trainings) -->
                        <div id="reimbActivityProposalContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-primary-dark mb-3 fs-7"><i class="bi bi-journal-check me-1"></i>Activity Proposal</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label for="reimbActivityProposal" class="form-label fs-8 fw-semibold text-muted">Upload Activity Proposal <span class="text-danger">*</span></label>
                                        <input type="file" name="reimb_activity_proposal" id="reimbActivityProposal" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" style="padding-top: 10px;">
                                        <small class="text-muted fs-9">PDF, JPG, PNG, DOCX up to 10MB.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reimb: Communications Load Fields (Month, DTR, Certificate, Bill/Proof) -->
                        <div id="reimbCommunicationsContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-primary-dark mb-3 fs-7"><i class="bi bi-telephone-inbound me-1"></i>Communications Load Details</h6>
                                
                                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-2 mb-3 py-2 px-3" style="font-size: 0.8rem;">
                                    <i class="bi bi-info-circle-fill fs-6 text-primary"></i>
                                    <div><strong>Note:</strong> All three documents (DTR, Certificate, and Bill / Proof of Payment) are strictly required.</div>
                                </div>
                                
                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label for="reimbursementMonth" class="form-label fs-8 fw-semibold text-muted">Select Month <span class="text-danger">*</span></label>
                                        <select name="reimbursement_month" id="reimbursementMonth" class="form-select">
                                            <option value="" disabled selected>Select Month</option>
                                            <?php
                                            for ($y = $currentYear; $y >= $prevYear; $y--) {
                                                for ($m = 12; $m >= 1; $m--) {
                                                    $mLabel = $monthsList[$m] . ' ' . $y;
                                                    echo '<option value="' . htmlspecialchars($mLabel) . '">' . htmlspecialchars($mLabel) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-sm-4">
                                        <label for="reimbDtr" class="form-label fs-8 fw-semibold text-muted">Upload DTR <span class="text-danger">*</span></label>
                                        <input type="file" name="reimb_dtr" id="reimbDtr" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" style="padding-top: 10px;">
                                        <small class="text-muted fs-9">PDF, JPG, PNG, DOCX up to 10MB.</small>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <label for="reimbCertificate" class="form-label fs-8 fw-semibold text-muted">Upload Certificate <span class="text-danger">*</span></label>
                                        <input type="file" name="reimb_certificate" id="reimbCertificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" style="padding-top: 10px;">
                                        <small class="text-muted fs-9">PDF, JPG, PNG, DOCX up to 10MB.</small>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <label for="reimbBillProof" class="form-label fs-8 fw-semibold text-muted">Upload Bill / Proof of Payment <span class="text-danger">*</span></label>
                                        <input type="file" name="reimb_bill_proof" id="reimbBillProof" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" style="padding-top: 10px;">
                                        <small class="text-muted fs-9">PDF, JPG, PNG, DOCX up to 10MB.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reimb: Utility Bills Fields (Month + Bill/Proof only) -->
                        <div id="reimbUtilityBillsContainer" class="d-none mt-3">
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-primary-dark mb-3 fs-7"><i class="bi bi-lightning-charge me-1"></i>Utility Bill Details</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-sm-6">
                                        <label for="utilityMonth" class="form-label fs-8 fw-semibold text-muted">Select Month <span class="text-danger">*</span></label>
                                        <select name="utility_month" id="utilityMonth" class="form-select">
                                            <option value="" disabled selected>Select Month</option>
                                            <?php
                                            for ($y = $currentYear; $y >= $prevYear; $y--) {
                                                for ($m = 12; $m >= 1; $m--) {
                                                    $mLabel = $monthsList[$m] . ' ' . $y;
                                                    echo '<option value="' . htmlspecialchars($mLabel) . '">' . htmlspecialchars($mLabel) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="utilityBillProof" class="form-label fs-8 fw-semibold text-muted">Upload Bill / Proof of Payment <span class="text-danger">*</span></label>
                                        <input type="file" name="utility_bill_proof" id="utilityBillProof" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" style="padding-top: 10px;">
                                        <small class="text-muted fs-9">PDF, JPG, PNG, DOCX up to 10MB.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DM 214 Documents Checklist (Cash Advance / Reimbursement) -->
                    <div id="documentChecklistSection" class="mb-4 d-none">
                        <div class="card border border-primary-subtle bg-white shadow-sm">
                            <div class="card-header bg-primary-subtle py-2 px-3 border-bottom border-primary-subtle">
                                <h6 class="mb-0 fw-bold text-primary-dark d-flex align-items-center gap-2 fs-7">
                                    <i class="bi bi-clipboard2-check"></i>
                                    <span>Documents Checklist</span>
                                    <span class="badge bg-primary fs-9" id="documentChecklistCategoryLabel"></span>
                                </h6>
                                <small class="text-muted fs-9 d-block mt-1">Per DM No. 214, S. 2026 — prepare and attach the documents below.</small>
                            </div>
                            <div class="card-body p-3">
                                <div id="documentChecklistNote" class="d-none alert alert-info border-0 py-2 px-3 mb-3 fs-9"></div>
                                <div id="documentChecklistSource" class="d-none text-muted fs-9 mb-2"></div>
                                <div id="documentChecklistContent"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial and Tax details -->
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label for="amount" class="form-label fs-8 fw-semibold text-muted">Gross Amount (₱) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="amount" class="form-control" placeholder="0.00" step="0.01" min="1" required>
                        </div>
                    </div>

                    <!-- Upload attachment -->
                    <div class="mb-4">
                        <label class="form-label fs-8 fw-semibold text-muted">Supporting Attachment Document(s) <span class="text-muted fw-normal">(Optional)</span></label>
                        
                        <!-- Drag and Drop Area -->
                        <div id="dropzone" class="border border-2 border-dashed rounded-3 p-4 text-center bg-light position-relative" style="cursor: pointer; transition: background-color 0.2s, border-color 0.2s;">
                            <input type="file" name="attachment[]" id="attachment" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" accept=".pdf,.jpg,.jpeg,.png,.docx" multiple style="cursor: pointer; z-index: 10;">
                            <div class="dz-message">
                                <i class="bi bi-cloud-arrow-up-fill fs-2 text-primary mb-2 d-block"></i>
                                <span class="fw-bold text-dark d-block">Drag & Drop files here or click to upload</span>
                                <span class="text-muted fs-9">Accepts PDF, JPG, PNG, DOCX (Max 10MB per file)</span>
                            </div>
                        </div>

                        <!-- Selected Files List -->
                        <div id="fileListContainer" class="mt-3 d-none">
                            <span class="fs-9 fw-semibold text-muted text-uppercase d-block mb-2">Selected Attachment(s):</span>
                            <div class="list-group list-group-flush border rounded-3 overflow-hidden bg-white shadow-sm" id="selectedFilesList">
                                <!-- Dynamically loaded files -->
                            </div>
                        </div>
                        
                        <small class="text-muted fs-9 d-block mt-1">Upload any general supporting document(s) if applicable.</small>
                    </div>

                    <!-- Remarks -->
                    <div class="mb-4">
                        <label for="remarks" class="form-label fs-8 fw-semibold text-muted">Submission Remarks / Notes (Optional)</label>
                        <textarea name="remarks" id="remarks" class="form-control" rows="4" placeholder="Enter supporting statements, supplier specifics, or DV details..."></textarea>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="<?php echo env('APP_URL'); ?>/views/dashboard/index.php" class="btn btn-light border px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 justify-content-center">Submit Transaction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     JAVASCRIPT LOGIC
     ========================================================================= -->
<script>
const DOCUMENT_CHECKLIST_DATA = <?php echo getDocumentChecklistsForJs(); ?>;

document.addEventListener('DOMContentLoaded', function() {
    const txTypeSelect = document.getElementById('transactionType');
    const caCategorySelect = document.getElementById('cashAdvanceCategory');
    const caSection = document.getElementById('cashAdvanceCategorySection');
    const reimbCategorySelect = document.getElementById('reimbursementCategory');
    const reimbSection = document.getElementById('reimbursementCategorySection');

    // CA sub-field containers
    const caDateVenue = document.getElementById('caDateVenueContainer');
    const caFundSource = document.getElementById('caFundSourceContainer');
    const caTaItinerary = document.getElementById('caTaItineraryContainer');
    const caActivityProposal = document.getElementById('caActivityProposalContainer');
    const caMonth = document.getElementById('caMonthContainer');

    // Reimb sub-field containers
    const reimbDateVenue = document.getElementById('reimbDateVenueContainer');
    const reimbTaItinerary = document.getElementById('reimbTaItineraryContainer');
    const reimbActivityProposal = document.getElementById('reimbActivityProposalContainer');
    const reimbCommunications = document.getElementById('reimbCommunicationsContainer');
    const reimbUtilityBills = document.getElementById('reimbUtilityBillsContainer');
    const documentChecklistSection = document.getElementById('documentChecklistSection');
    const documentChecklistContent = document.getElementById('documentChecklistContent');
    const documentChecklistNote = document.getElementById('documentChecklistNote');
    const documentChecklistSource = document.getElementById('documentChecklistSource');
    const documentChecklistCategoryLabel = document.getElementById('documentChecklistCategoryLabel');

    // Map of document key -> array of attached file names (frontend only, for UX grouping)
    let docFileMap = {};
    let currentAttachDocKey = null;
    let currentChecklistDocs = [];

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function resolveChecklistClient(txType, category) {
        if (!txType || !category) return null;

        const alias = DOCUMENT_CHECKLIST_DATA.aliases?.[txType]?.[category];
        let lookupType = txType;
        let lookupCategory = category;
        let note = null;
        let sourceLabel = null;

        if (alias) {
            if (alias.ref) {
                lookupType = alias.ref;
                lookupCategory = alias.category;
            }
            note = alias.note || null;
            sourceLabel = alias.source_label || null;
        }

        const entry = DOCUMENT_CHECKLIST_DATA.checklists?.[lookupType]?.[lookupCategory];
        if (!entry) return null;

        return { entry, note, sourceLabel, category };
    }

    function buildDocKey(doc) {
        const section = doc.sectionTitle || '';
        return `${doc.title}__${section}`;
    }

    function normalizeText(text) {
        return (text || '')
            .toLowerCase()
            .replace(/[^a-z0-9\s]/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function tokenize(text) {
        return normalizeText(text)
            .split(' ')
            .filter(token => token.length > 2);
    }

    function renderDocumentRows(documents) {
        if (!documents || !documents.length) return '';

        return documents.map(doc => {
            const required = !!doc.required;
            const badgeClass = required ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary';
            const badgeText = required ? 'Required' : 'Optional';
            const condition = doc.condition ? ` <small class="text-muted">(${escapeHtml(doc.condition)})</small>` : '';
            const docKey = buildDocKey(doc);
            return `
                <li class="list-group-item d-flex flex-column gap-1 py-2 px-3 fs-8" data-doc-key="${escapeHtml(docKey)}">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <span class="text-dark">
                            ${escapeHtml(doc.title)}${condition}
                            ${doc.sectionTitle ? `<br><small class="text-muted">Section: ${escapeHtml(doc.sectionTitle)}</small>` : ''}
                        </span>
                        <span class="badge ${badgeClass} fs-9 flex-shrink-0">${badgeText}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center gap-2 mt-1">
                        <small class="text-muted fs-9" data-doc-files-summary data-doc-key="${escapeHtml(docKey)}">No files attached yet.</small>
                        <button type="button" class="btn btn-outline-primary btn-xs py-0 px-2 fs-9" data-doc-attach-btn data-doc-key="${escapeHtml(docKey)}">
                            <i class="bi bi-paperclip me-1"></i>Attach
                        </button>
                    </div>
                    <div class="mt-1" data-doc-files-list data-doc-key="${escapeHtml(docKey)}"></div>
                </li>
            `;
        }).join('');
    }

    function updateDocumentChecklist() {
        const txType = txTypeSelect.value;
        let category = '';
        if (txType === 'Cash Advance') {
            category = caCategorySelect.value;
        } else if (txType === 'Reimbursement') {
            category = reimbCategorySelect.value;
        }

        if (!category || (txType !== 'Cash Advance' && txType !== 'Reimbursement')) {
            documentChecklistSection.classList.add('d-none');
            documentChecklistContent.innerHTML = '';
            return;
        }

        const resolved = resolveChecklistClient(txType, category);
        if (!resolved) {
            documentChecklistSection.classList.add('d-none');
            documentChecklistContent.innerHTML = '';
            return;
        }

        documentChecklistSection.classList.remove('d-none');
        documentChecklistCategoryLabel.textContent = category;

        const baseDocs = (resolved.entry.documents || []).map(d => ({ ...d, sectionTitle: null }));
        const sectionDocs = (resolved.entry.sections || []).flatMap(section =>
            (section.documents || []).map(d => ({
                ...d,
                sectionTitle: section.title || 'Additional Documents'
            }))
        );

        const allDocs = baseDocs.concat(sectionDocs);
        currentChecklistDocs = allDocs.map(doc => ({
            key: buildDocKey(doc),
            title: doc.title
        }));
        const requiredDocs = allDocs.filter(d => d.required);
        const optionalDocs = allDocs.filter(d => !d.required);
        const reqCount = allDocs.filter(d => d.required).length;
        const optCount = allDocs.length - reqCount;

        if (resolved.note) {
            documentChecklistNote.classList.remove('d-none');
            documentChecklistNote.innerHTML = '<i class="bi bi-info-circle me-1"></i>' + escapeHtml(resolved.note);
        } else {
            documentChecklistNote.classList.add('d-none');
            documentChecklistNote.innerHTML = '';
        }

        if (resolved.sourceLabel) {
            documentChecklistSource.classList.remove('d-none');
            documentChecklistSource.innerHTML = '<i class="bi bi-arrow-return-right me-1"></i>' + escapeHtml(resolved.sourceLabel);
        } else {
            documentChecklistSource.classList.add('d-none');
            documentChecklistSource.innerHTML = '';
        }

        let html = `<div class="d-flex gap-2 mb-3 flex-wrap">
            <span class="badge bg-danger-subtle text-danger fs-9">${reqCount} Required</span>
            <span class="badge bg-secondary-subtle text-secondary fs-9">${optCount} Optional</span>
        </div>`;
        if (requiredDocs.length > 0) {
            html += '<h6 class="fw-semibold text-secondary fs-8 mb-2">Required Documents</h6>';
            html += '<ul class="list-group list-group-flush border rounded-3 overflow-hidden mb-0">';
            html += renderDocumentRows(requiredDocs);
            html += '</ul>';
        }

        if (optionalDocs.length > 0) {
            html += '<h6 class="fw-semibold text-secondary fs-8 mt-3 mb-2">Optional / Conditional Documents</h6>';
            html += '<ul class="list-group list-group-flush border rounded-3 overflow-hidden mb-0">';
            html += renderDocumentRows(optionalDocs);
            html += '</ul>';
        }

        documentChecklistContent.innerHTML = html;

        wireChecklistAttachButtons();
        autoAssignUnmappedFiles();
        refreshDocFileSummaries();
    }

    function autoAssignUnmappedFiles() {
        if (!currentChecklistDocs.length || !selectedFiles.length) return;

        const assignedSet = new Set(Object.values(docFileMap).flat());
        const unassigned = selectedFiles.filter(file => !assignedSet.has(file.name));

        unassigned.forEach(file => {
            const fileTokens = tokenize(file.name);
            if (!fileTokens.length) return;

            let bestKey = null;
            let bestScore = 0;

            currentChecklistDocs.forEach(doc => {
                const titleTokens = tokenize(doc.title);
                if (!titleTokens.length) return;

                let score = 0;
                titleTokens.forEach(token => {
                    if (fileTokens.some(ft => ft.includes(token) || token.includes(ft))) {
                        score += 1;
                    }
                });

                if (score > bestScore) {
                    bestScore = score;
                    bestKey = doc.key;
                }
            });

            if (bestKey && bestScore > 0) {
                const existing = docFileMap[bestKey] || [];
                if (!existing.includes(file.name)) {
                    existing.push(file.name);
                    docFileMap[bestKey] = existing;
                }
            }
        });
    }

    function wireChecklistAttachButtons() {
        const buttons = document.querySelectorAll('[data-doc-attach-btn]');
        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                const key = btn.getAttribute('data-doc-key');
                currentAttachDocKey = key;
                if (fileInput) {
                    fileInput.click();
                }
            });
        });
    }

    window.removeChecklistFile = function(docKey, fileName) {
        // Remove file association from the checklist row
        docFileMap[docKey] = (docFileMap[docKey] || []).filter(name => name !== fileName);

        // Remove from selected files pool (first exact name match)
        const fileIdx = selectedFiles.findIndex(f => f.name === fileName);
        if (fileIdx !== -1) {
            selectedFiles.splice(fileIdx, 1);
        }

        syncFileInput();
        renderSelectedFiles();
        refreshDocFileSummaries();
    };

    function refreshDocFileSummaries() {
        const summaryEls = document.querySelectorAll('[data-doc-files-summary]');
        summaryEls.forEach(el => {
            const key = el.getAttribute('data-doc-key');
            const list = docFileMap[key] || [];
            const attachBtn = document.querySelector(`[data-doc-attach-btn][data-doc-key="${CSS.escape(key)}"]`);
            if (!list.length) {
                el.textContent = 'No files attached yet.';
                if (attachBtn) {
                    attachBtn.className = 'btn btn-outline-primary btn-xs py-0 px-2 fs-9';
                    attachBtn.innerHTML = '<i class="bi bi-paperclip me-1"></i>Attach';
                }
            } else {
                el.textContent = `${list.length} file(s) attached.`;
                if (attachBtn) {
                    attachBtn.className = 'btn btn-success btn-xs py-0 px-2 fs-9';
                    attachBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Attached';
                }
            }
        });

        const listEls = document.querySelectorAll('[data-doc-files-list]');
        listEls.forEach(el => {
            const key = el.getAttribute('data-doc-key');
            const list = docFileMap[key] || [];
            if (!list.length) {
                el.innerHTML = '';
                return;
            }

            const chips = list.map(name => {
                const jsKey = JSON.stringify(key);
                const jsName = JSON.stringify(name);
                return `
                <span class="badge bg-light text-dark border me-1 mb-1 d-inline-flex align-items-center gap-1">
                    <span class="text-truncate" style="max-width: 240px;" title="${escapeHtml(name)}">${escapeHtml(name)}</span>
                    <button type="button" class="btn btn-link p-0 text-danger text-decoration-none fw-bold" onclick='removeChecklistFile(${jsKey}, ${jsName})' title="Remove file">×</button>
                </span>
            `;
            }).join('');
            el.innerHTML = chips;
        });
    }

    // Coverage type → sub-field mapping
    const caFieldMap = {
        'Travel':           { dateVenue: true, fundSource: true, taItinerary: true, activityProposal: false, month: false },
        'School MOOE':      { dateVenue: false, fundSource: true, taItinerary: false, activityProposal: false, month: false },
        'SBFP':             { dateVenue: false, fundSource: true, taItinerary: false, activityProposal: false, month: false },
        'Training':         { dateVenue: true, fundSource: false, taItinerary: false, activityProposal: true, month: false },
        'Meals':            { dateVenue: true, fundSource: false, taItinerary: false, activityProposal: false, month: false },
        'Accommodation':    { dateVenue: true, fundSource: false, taItinerary: false, activityProposal: false, month: false },
        'Meals and Accommodation': { dateVenue: true, fundSource: false, taItinerary: false, activityProposal: false, month: false },
        'Honorarium':       { dateVenue: false, fundSource: false, taItinerary: false, activityProposal: false, month: false },
        'Supplies and Materials': { dateVenue: false, fundSource: false, taItinerary: false, activityProposal: false, month: false },
        'Communication Expenses': { dateVenue: false, fundSource: false, taItinerary: false, activityProposal: false, month: true },
        'SLAC / Moving-Up / Graduation / GAWAD': { dateVenue: true, fundSource: false, taItinerary: false, activityProposal: true, month: false }
    };

    const reimbFieldMap = {
        'Travel':           { dateVenue: true, taItinerary: true, activityProposal: false, communications: false, utilityBills: false },
        'Supplies and Materials': { dateVenue: false, taItinerary: false, activityProposal: false, communications: false, utilityBills: false },
        'Meals':            { dateVenue: true, taItinerary: false, activityProposal: false, communications: false, utilityBills: false },
        'Accommodation':    { dateVenue: true, taItinerary: false, activityProposal: false, communications: false, utilityBills: false },
        'Meals and Accommodation': { dateVenue: true, taItinerary: false, activityProposal: false, communications: false, utilityBills: false },
        'Honorarium':       { dateVenue: false, taItinerary: false, activityProposal: false, communications: false, utilityBills: false },
        'Communication Load': { dateVenue: false, taItinerary: false, activityProposal: false, communications: true, utilityBills: false },
        'Utility Bills':    { dateVenue: false, taItinerary: false, activityProposal: false, communications: false, utilityBills: true },
        'Repair, Repaint, Improvement': { dateVenue: false, taItinerary: false, activityProposal: false, communications: false, utilityBills: false },
        'Installation of Electricity and Water': { dateVenue: false, taItinerary: false, activityProposal: false, communications: false, utilityBills: false },
        'Installation of Internet / Telephone': { dateVenue: false, taItinerary: false, activityProposal: false, communications: false, utilityBills: false },
        'Seminars / Trainings': { dateVenue: true, taItinerary: false, activityProposal: true, communications: false, utilityBills: false },
        'GAD Documents / SLAC Session': { dateVenue: true, taItinerary: false, activityProposal: false, communications: false, utilityBills: false },
        'Job Order':        { dateVenue: false, taItinerary: false, activityProposal: false, communications: false, utilityBills: false },
        'Fidelity Bond':    { dateVenue: false, taItinerary: false, activityProposal: false, communications: false, utilityBills: false },
        'Immersion and Insurance for SHS': { dateVenue: false, taItinerary: false, activityProposal: false, communications: false, utilityBills: false }
    };

    function setFieldsState(container, enabled, required) {
        if (!container) return;
        const inputs = container.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.disabled = !enabled;
            // Only set required on non-file inputs or file inputs when explicitly needed
            if (input.type !== 'file') {
                input.required = required;
            } else {
                input.required = required;
            }
        });
    }

    function hideAndDisable(container) {
        if (!container) return;
        container.classList.add('d-none');
        setFieldsState(container, false, false);
    }

    function showAndEnable(container) {
        if (!container) return;
        container.classList.remove('d-none');
        setFieldsState(container, true, true);
    }

    function toggleFormFields() {
        const txType = txTypeSelect.value;

        // ── CASH ADVANCE ──
        if (txType === 'Cash Advance') {
            caSection.classList.remove('d-none');
            caCategorySelect.disabled = false;
            caCategorySelect.required = true;

            const caCat = caCategorySelect.value;
            const cfg = caFieldMap[caCat] || {};

            // Toggle each sub-field group
            cfg.dateVenue ? showAndEnable(caDateVenue) : hideAndDisable(caDateVenue);
            cfg.fundSource ? showAndEnable(caFundSource) : hideAndDisable(caFundSource);
            cfg.taItinerary ? showAndEnable(caTaItinerary) : hideAndDisable(caTaItinerary);
            cfg.activityProposal ? showAndEnable(caActivityProposal) : hideAndDisable(caActivityProposal);
            cfg.month ? showAndEnable(caMonth) : hideAndDisable(caMonth);
        } else {
            caSection.classList.add('d-none');
            caCategorySelect.disabled = true;
            caCategorySelect.required = false;
            hideAndDisable(caDateVenue);
            hideAndDisable(caFundSource);
            hideAndDisable(caTaItinerary);
            hideAndDisable(caActivityProposal);
            hideAndDisable(caMonth);
        }

        // ── REIMBURSEMENT ──
        if (txType === 'Reimbursement') {
            reimbSection.classList.remove('d-none');
            reimbCategorySelect.disabled = false;
            reimbCategorySelect.required = true;

            const reimbCat = reimbCategorySelect.value;
            const cfg = reimbFieldMap[reimbCat] || {};

            cfg.dateVenue ? showAndEnable(reimbDateVenue) : hideAndDisable(reimbDateVenue);
            cfg.taItinerary ? showAndEnable(reimbTaItinerary) : hideAndDisable(reimbTaItinerary);
            cfg.activityProposal ? showAndEnable(reimbActivityProposal) : hideAndDisable(reimbActivityProposal);
            cfg.communications ? showAndEnable(reimbCommunications) : hideAndDisable(reimbCommunications);
            cfg.utilityBills ? showAndEnable(reimbUtilityBills) : hideAndDisable(reimbUtilityBills);
        } else {
            reimbSection.classList.add('d-none');
            reimbCategorySelect.disabled = true;
            reimbCategorySelect.required = false;
            hideAndDisable(reimbDateVenue);
            hideAndDisable(reimbTaItinerary);
            hideAndDisable(reimbActivityProposal);
            hideAndDisable(reimbCommunications);
            hideAndDisable(reimbUtilityBills);
        }

        updateDocumentChecklist();
    }

    txTypeSelect.addEventListener('change', toggleFormFields);
    caCategorySelect.addEventListener('change', toggleFormFields);
    reimbCategorySelect.addEventListener('change', toggleFormFields);
    
    // Initial call
    toggleFormFields();

    // Drag and drop dropzone handlers
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('attachment');
    const fileListContainer = document.getElementById('fileListContainer');
    const selectedFilesList = document.getElementById('selectedFilesList');
    let selectedFiles = [];

    if (dropzone && fileInput) {
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('bg-primary-subtle', 'border-primary');
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                dropzone.classList.remove('bg-primary-subtle', 'border-primary');
            });
        });

        fileInput.addEventListener('change', updateFileList);
    }

    function updateFileList() {
        const files = fileInput.files;
        if (files.length > 0) {
            Array.from(files).forEach(file => {
                // Prevent duplicate files in the list
                const isDuplicate = selectedFiles.some(f => f.name === file.name && f.size === file.size);
                if (!isDuplicate) {
                    selectedFiles.push(file);
                    if (currentAttachDocKey) {
                        const existing = docFileMap[currentAttachDocKey] || [];
                        if (!existing.includes(file.name)) {
                            existing.push(file.name);
                        }
                        docFileMap[currentAttachDocKey] = existing;
                    }
                }
            });
            // Clear input value so selecting the same file again triggers 'change'
            fileInput.value = '';
            syncFileInput();
        }
        if (!currentAttachDocKey) {
            autoAssignUnmappedFiles();
        }
        currentAttachDocKey = null;
        renderSelectedFiles();
        refreshDocFileSummaries();
    }

    function syncFileInput() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        fileInput.files = dataTransfer.files;
    }

    window.removeFile = function(index) {
        const removed = selectedFiles.splice(index, 1)[0];

        if (removed) {
            Object.keys(docFileMap).forEach(key => {
                docFileMap[key] = (docFileMap[key] || []).filter(name => name !== removed.name);
            });
        }

        syncFileInput();
        renderSelectedFiles();
        refreshDocFileSummaries();
    };

    function renderSelectedFiles() {
        selectedFilesList.innerHTML = '';
        
        if (selectedFiles.length > 0) {
            fileListContainer.classList.remove('d-none');
            
            selectedFiles.forEach((file, index) => {
                const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                let iconClass = 'bi-file-earmark-code';
                if (file.type.includes('image')) iconClass = 'bi-file-earmark-image text-success';
                else if (file.type.includes('pdf')) iconClass = 'bi-file-earmark-pdf text-danger';
                else if (file.name.endsWith('.docx')) iconClass = 'bi-file-earmark-word text-primary';
                
                const fileItem = `
                    <div class="list-group-item d-flex align-items-center justify-content-between p-2 fs-8 border-light">
                        <div class="d-flex align-items-center gap-2 text-truncate" style="max-width: 70%;">
                            <i class="bi ${iconClass} fs-5"></i>
                            <span class="text-dark fw-medium text-truncate" title="${file.name}">${file.name}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-muted border py-1 px-2">${sizeInMB} MB</span>
                            <button type="button" class="btn btn-sm btn-link text-danger p-0 border-0" onclick="removeFile(${index})" style="line-height: 1;">
                                <i class="bi bi-x-circle-fill fs-6"></i>
                            </button>
                        </div>
                    </div>
                `;
                selectedFilesList.insertAdjacentHTML('beforeend', fileItem);
            });
        } else {
            fileListContainer.classList.add('d-none');
        }
    }
});



async function handleFormSubmit(e) {
    e.preventDefault();
    
    const form = document.getElementById('submitTransactionForm');
    const formData = new FormData(form);

    API.showSpinner();

    // Call Submit Endpoint
    const response = await fetch('<?php echo env('APP_URL'); ?>/api/transactions/submit-transaction.php', {
        method: 'POST',
        headers: {
            'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>'
        },
        body: formData
    });

    const data = await response.json().catch(() => ({ success: false, message: 'Response parsing failure.' }));
    
    API.hideSpinner();

    if (data.success) {
        API.showToast(data.message, 'success');
        
        // Redirect directly to Tracker timeline page on success!
        setTimeout(() => {
            window.location.href = '<?php echo env('APP_URL'); ?>/views/tracker/index.php?tracking=' + encodeURIComponent(data.data.tracking_number);
        }, 1500);
    } else {
        API.showToast(data.message || 'Submission failed.', 'danger');
    }
}
</script>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
?>
