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

                    <!-- Financial and Tax details -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6">
                            <label for="amount" class="form-label fs-8 fw-semibold text-muted">Gross Amount (₱) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="amount" class="form-control" placeholder="0.00" step="0.01" min="1" required oninput="calculateTaxPreview()">
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="taxType" class="form-label fs-8 fw-semibold text-muted">Tax Classification <span class="text-danger">*</span></label>
                            <select name="tax_type" id="taxType" class="form-select" required onchange="calculateTaxPreview()">
                                <option value="" disabled selected>Select Tax Type</option>
                                <?php foreach ($taxConfigurations as $config): ?>
                                    <option value="<?php echo htmlspecialchars($config['tax_type']); ?>">
                                        <?php echo htmlspecialchars($config['tax_type']) . " (" . number_format($config['tax_percentage']) . "%)"; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Interactive Tax Preview Card -->
                    <div class="mb-4 d-none" id="taxPreviewCard">
                        <span class="fs-8 fw-semibold text-muted text-uppercase d-block mb-1">Financial Calculation Preview</span>
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="row text-center text-sm-start">
                                <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                                    <small class="text-muted d-block">Gross Amount</small>
                                    <span class="fw-semibold text-dark fs-6" id="previewGross">₱0.00</span>
                                </div>
                                <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                                    <small class="text-muted d-block">Tax Deduction Amount</small>
                                    <span class="fw-semibold text-danger fs-6" id="previewTax">₱0.00</span>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <small class="text-muted d-block">Estimated Net Payout</small>
                                    <strong class="text-primary-dark fs-5" id="previewNet">₱0.00</strong>
                                </div>
                            </div>
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
                }
            });
            // Clear input value so selecting the same file again triggers 'change'
            fileInput.value = '';
            syncFileInput();
        }
        renderSelectedFiles();
    }

    function syncFileInput() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        fileInput.files = dataTransfer.files;
    }

    window.removeFile = function(index) {
        selectedFiles.splice(index, 1);
        syncFileInput();
        renderSelectedFiles();
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

async function calculateTaxPreview() {
    const amount = parseFloat(document.getElementById('amount').value);
    const taxType = document.getElementById('taxType').value;
    const previewCard = document.getElementById('taxPreviewCard');

    if (!amount || amount <= 0 || !taxType) {
        previewCard.classList.add('d-none');
        return;
    }

    const payload = new FormData();
    payload.append('amount', amount);
    payload.append('tax_type', taxType);
    payload.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');

    const response = await API.request('<?php echo env('APP_URL'); ?>/api/tax/compute-tax.php', {
        method: 'POST',
        body: payload
    });

    if (response && response.success) {
        document.getElementById('previewGross').innerText = '₱' + amount.toLocaleString('en-PH', { minimumFractionDigits: 2 });
        document.getElementById('previewTax').innerText = '₱' + parseFloat(response.data.tax_amount).toLocaleString('en-PH', { minimumFractionDigits: 2 });
        document.getElementById('previewNet').innerText = '₱' + parseFloat(response.data.net_amount).toLocaleString('en-PH', { minimumFractionDigits: 2 });
        previewCard.classList.remove('d-none');
    } else {
        previewCard.classList.add('d-none');
    }
}

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
