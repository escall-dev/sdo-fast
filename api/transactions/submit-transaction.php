<?php
/**
 * Transaction Submission Processor API for SDO FAST.
 * Conducts tax computations, secure file uploads, and tracking code generation.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php'; // Enforces auth & CSRF
require_once __DIR__ . '/../../services/TrackingNumberService.php';
require_once __DIR__ . '/../../services/AuditLogService.php';

if ($fastPDO === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'];
$userPosition = $_SESSION['user_position'] ?? '';

// Only allow Requestors, Personnel, and Super Admins to submit transactions
if (!in_array($userRole, ['Super Admin', 'Requestor']) && $userPosition !== 'Personnel') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized action.']);
    exit;
}

// 1. Parse and sanitize POST parameters
$type = trim($_POST['transaction_type'] ?? '');
$eventName = trim($_POST['event_name'] ?? '');
$amount = (float)($_POST['amount'] ?? 0.00);
$taxType = trim($_POST['tax_type'] ?? '');
$targetDate = trim($_POST['target_date'] ?? '');
$remarks = trim($_POST['remarks'] ?? '');

// Cash Advance specific fields
$cashAdvanceCategory = trim($_POST['cash_advance_category'] ?? '');
$mooeStartDate = trim($_POST['mooe_start_date'] ?? '');
$mooeEndDate = trim($_POST['mooe_end_date'] ?? '');
$fundSource = trim($_POST['fund_source'] ?? '');
$venue = trim($_POST['venue'] ?? '');
$caMonthValue = trim($_POST['ca_month'] ?? '');

// Reimbursement specific fields
$reimbursementCategory = trim($_POST['reimbursement_category'] ?? '');
$reimbursementMonth = trim($_POST['reimbursement_month'] ?? '');
$reimbStartDate = trim($_POST['reimb_start_date'] ?? '');
$reimbEndDate = trim($_POST['reimb_end_date'] ?? '');
$reimbVenue = trim($_POST['reimb_venue'] ?? '');
$utilityMonth = trim($_POST['utility_month'] ?? '');

// 2. Validate Inputs
if (empty($type) || empty($eventName) || $amount <= 0 || empty($taxType)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Transaction type, event name, tax type, and amount are required.']);
    exit;
}

$allowedTypes = ['Cash Advance', 'Reimbursement', 'Payroll'];
if (!in_array($type, $allowedTypes)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid transaction type selected.']);
    exit;
}

// Allowed coverage types
$allowedCaCategories = [
    'Travel', 'School MOOE', 'SBFP', 'Training', 'Meals', 'Accommodation',
    'Meals and Accommodation', 'Honorarium', 'Supplies and Materials',
    'Communication Expenses', 'SLAC / Moving-Up / Graduation / GAWAD'
];
$allowedReimbCategories = [
    'Travel', 'Supplies and Materials', 'Meals', 'Accommodation',
    'Meals and Accommodation', 'Honorarium', 'Communication Load',
    'Utility Bills', 'Repair, Repaint, Improvement',
    'Installation of Electricity and Water', 'Installation of Internet / Telephone',
    'Seminars / Trainings', 'GAD Documents / SLAC Session',
    'Job Order', 'Fidelity Bond', 'Immersion and Insurance for SHS'
];

// Coverage types that require date/venue
$caDateVenueTypes = ['Travel', 'Training', 'Meals', 'Accommodation', 'Meals and Accommodation', 'SLAC / Moving-Up / Graduation / GAWAD'];
$caTaItineraryTypes = ['Travel'];
$caFundSourceTypes = ['Travel', 'School MOOE', 'SBFP'];
$caActivityProposalTypes = ['Training', 'SLAC / Moving-Up / Graduation / GAWAD'];
$caMonthTypes = ['Communication Expenses'];

$reimbDateVenueTypes = ['Travel', 'Meals', 'Accommodation', 'Meals and Accommodation', 'Seminars / Trainings', 'GAD Documents / SLAC Session'];
$reimbTaItineraryTypes = ['Travel'];
$reimbActivityProposalTypes = ['Seminars / Trainings'];
$reimbCommunicationsTypes = ['Communication Load'];
$reimbUtilityBillsTypes = ['Utility Bills'];

// Cash Advance validations
$inclusiveDates = null;
if ($type === 'Cash Advance') {
    if (empty($cashAdvanceCategory) || !in_array($cashAdvanceCategory, $allowedCaCategories)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'A valid Cash Advance Coverage Type is required.']);
        exit;
    }

    // Date/Venue required?
    if (in_array($cashAdvanceCategory, $caDateVenueTypes)) {
        if (empty($mooeStartDate) || empty($mooeEndDate) || empty($venue)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Inclusive dates and venue are required for ' . $cashAdvanceCategory . '.']);
            exit;
        }
        $inclusiveDates = $mooeStartDate . ' to ' . $mooeEndDate;
    }

    // Fund Source required?
    if (in_array($cashAdvanceCategory, $caFundSourceTypes)) {
        if (empty($fundSource)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Fund source is required for ' . $cashAdvanceCategory . '.']);
            exit;
        }
    }

    // Month required for Communication Expenses?
    if (in_array($cashAdvanceCategory, $caMonthTypes)) {
        if (empty($caMonthValue)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Month is required for Communication Expenses.']);
            exit;
        }
    }
}

// Reimbursement validations
$reimbInclusiveDates = null;
if ($type === 'Reimbursement') {
    if (empty($reimbursementCategory) || !in_array($reimbursementCategory, $allowedReimbCategories)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'A valid Reimbursement Coverage Type is required.']);
        exit;
    }

    // Date/Venue required?
    if (in_array($reimbursementCategory, $reimbDateVenueTypes)) {
        if (empty($reimbStartDate) || empty($reimbEndDate) || empty($reimbVenue)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Inclusive dates and venue are required for ' . $reimbursementCategory . '.']);
            exit;
        }
        $reimbInclusiveDates = $reimbStartDate . ' to ' . $reimbEndDate;
    }

    // Communications Load month required?
    if (in_array($reimbursementCategory, $reimbCommunicationsTypes)) {
        if (empty($reimbursementMonth)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Month is required for Communication Load.']);
            exit;
        }
    }

    // Utility Bills month required?
    if (in_array($reimbursementCategory, $reimbUtilityBillsTypes)) {
        if (empty($utilityMonth)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Month is required for Utility Bills.']);
            exit;
        }
        // Store utility month in the reimbursement_month field
        $reimbursementMonth = $utilityMonth;
    }
}

// Fetch active tax configurations to validate and calculate tax
try {
    $taxStmt = $fastPDO->prepare("SELECT tax_percentage FROM tax_configurations WHERE tax_type = :tax_type AND is_active = 1 LIMIT 1");
    $taxStmt->execute(['tax_type' => $taxType]);
    $taxPercentage = $taxStmt->fetchColumn();

    if ($taxPercentage === false) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Invalid or inactive tax type selected.']);
        exit;
    }
} catch (PDOException $e) {
    error_log("Tax retrieval failure: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'System error during tax validation.']);
    exit;
}

// 3. Compute Tax and Net Amount
$taxAmount = $amount * ($taxPercentage / 100);
$netAmount = $amount - $taxAmount;

// Helper function for secure file uploads
function handleSecureUpload($fileKey, $uploadDir) {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES[$fileKey];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'File upload error code on ' . $fileKey . ': ' . $file['error']]);
        exit;
    }

    $maxSize = 10 * 1024 * 1024; // 10MB
    if ($file['size'] > $maxSize) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Attached file for ' . $fileKey . ' exceeds the maximum limit of 10MB.']);
        exit;
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'docx'];
    if (!in_array($extension, $allowedExtensions)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Allowed file formats for ' . $fileKey . ': PDF, JPG, PNG, DOCX.']);
        exit;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMimeTypes = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    if (!in_array($mimeType, $allowedMimeTypes)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Security check: Invalid file content type detected for ' . $fileKey . '.']);
        exit;
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $extension;
    $targetPath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file: ' . $fileKey]);
        exit;
    }
    
    return 'uploads/transactions/' . $filename;
}

// 4. Secure File Upload Handler
$uploadDir = dirname(dirname(__DIR__)) . '/uploads/transactions/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$attachmentPath = null;
$attachmentPaths = [];
$approvedTaPath = null;
$travelItineraryPath = null;
$activityProposalPath = null;
$dtrPath = null;
$certificatePath = null;
$billProofPath = null;

// Reimbursement-specific file paths
$reimbApprovedTaPath = null;
$reimbTravelItineraryPath = null;
$reimbActivityProposalPath = null;
$utilityBillProofPath = null;

// Enforce required uploads for Cash Advance coverage types
if ($type === 'Cash Advance') {
    if (in_array($cashAdvanceCategory, $caTaItineraryTypes)) {
        // Travel: TA + Itinerary required
        if (!isset($_FILES['approved_ta']) || $_FILES['approved_ta']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Approved TA (Travel Authority) is required for Travel cash advances.']);
            exit;
        }
        if (!isset($_FILES['travel_itinerary']) || $_FILES['travel_itinerary']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Travel Itinerary is required for Travel cash advances.']);
            exit;
        }
        $approvedTaPath = handleSecureUpload('approved_ta', $uploadDir);
        $travelItineraryPath = handleSecureUpload('travel_itinerary', $uploadDir);
    }
    if (in_array($cashAdvanceCategory, $caActivityProposalTypes)) {
        // Training, SLAC/GAWAD: Activity Proposal required
        if (!isset($_FILES['activity_proposal']) || $_FILES['activity_proposal']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Activity Proposal is required for ' . $cashAdvanceCategory . ' cash advances.']);
            exit;
        }
        $activityProposalPath = handleSecureUpload('activity_proposal', $uploadDir);
    }
} elseif ($type === 'Reimbursement') {
    if (in_array($reimbursementCategory, $reimbTaItineraryTypes)) {
        // Travel: TA + Itinerary required
        if (!isset($_FILES['reimb_approved_ta']) || $_FILES['reimb_approved_ta']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Approved TA (Travel Authority) is required for Travel reimbursement.']);
            exit;
        }
        if (!isset($_FILES['reimb_travel_itinerary']) || $_FILES['reimb_travel_itinerary']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Travel Itinerary is required for Travel reimbursement.']);
            exit;
        }
        $reimbApprovedTaPath = handleSecureUpload('reimb_approved_ta', $uploadDir);
        $reimbTravelItineraryPath = handleSecureUpload('reimb_travel_itinerary', $uploadDir);
    }
    if (in_array($reimbursementCategory, $reimbActivityProposalTypes)) {
        // Seminars/Trainings: Activity Proposal required
        if (!isset($_FILES['reimb_activity_proposal']) || $_FILES['reimb_activity_proposal']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Activity Proposal is required for ' . $reimbursementCategory . ' reimbursement.']);
            exit;
        }
        $reimbActivityProposalPath = handleSecureUpload('reimb_activity_proposal', $uploadDir);
    }
    if (in_array($reimbursementCategory, $reimbCommunicationsTypes)) {
        // Communication Load: DTR + Certificate + Bill/Proof
        if (!isset($_FILES['reimb_dtr']) || $_FILES['reimb_dtr']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'DTR document is required for Communication Load.']);
            exit;
        }
        if (!isset($_FILES['reimb_certificate']) || $_FILES['reimb_certificate']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Certificate document is required for Communication Load.']);
            exit;
        }
        if (!isset($_FILES['reimb_bill_proof']) || $_FILES['reimb_bill_proof']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Bill / proof of payment document is required for Communication Load.']);
            exit;
        }
        $dtrPath = handleSecureUpload('reimb_dtr', $uploadDir);
        $certificatePath = handleSecureUpload('reimb_certificate', $uploadDir);
        $billProofPath = handleSecureUpload('reimb_bill_proof', $uploadDir);
    }
    if (in_array($reimbursementCategory, $reimbUtilityBillsTypes)) {
        // Utility Bills: Bill/Proof upload required
        if (!isset($_FILES['utility_bill_proof']) || $_FILES['utility_bill_proof']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Bill / proof of payment document is required for Utility Bills.']);
            exit;
        }
        $utilityBillProofPath = handleSecureUpload('utility_bill_proof', $uploadDir);
    }
}

// Process supporting attachments if provided
if (isset($_FILES['attachment']) && is_array($_FILES['attachment']['name']) && $_FILES['attachment']['error'][0] !== UPLOAD_ERR_NO_FILE) {
    $fileCount = count($_FILES['attachment']['name']);
    for ($i = 0; $i < $fileCount; $i++) {
        if ($_FILES['attachment']['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        
        $fileError = $_FILES['attachment']['error'][$i];
        if ($fileError !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'File upload error code on attachment ' . ($i + 1) . ': ' . $fileError]);
            exit;
        }

        $fileSize = $_FILES['attachment']['size'][$i];
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($fileSize > $maxSize) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Attachment file ' . ($i + 1) . ' exceeds the maximum limit of 10MB.']);
            exit;
        }

        $fileName = $_FILES['attachment']['name'][$i];
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'docx'];
        if (!in_array($extension, $allowedExtensions)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Allowed file formats for attachment ' . ($i + 1) . ': PDF, JPG, PNG, DOCX.']);
            exit;
        }

        $tmpName = $_FILES['attachment']['tmp_name'][$i];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpName);
        finfo_close($finfo);

        $allowedMimeTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        if (!in_array($mimeType, $allowedMimeTypes)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Security check: Invalid file content type detected for attachment ' . ($i + 1) . '.']);
            exit;
        }

        $newFilename = bin2hex(random_bytes(16)) . '.' . $extension;
        $targetPath = $uploadDir . $newFilename;

        if (!move_uploaded_file($tmpName, $targetPath)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to save uploaded attachment: ' . ($i + 1)]);
            exit;
        }

        $attachmentPaths[] = 'uploads/transactions/' . $newFilename;
    }
}

$attachmentPath = !empty($attachmentPaths) ? json_encode($attachmentPaths) : null;

// 5. Database Insertion Workflow
try {
    $fastPDO->beginTransaction();

    // Generate tracking code sequentially with a concurrency lock
    $trackingNumber = TrackingNumberService::generate($fastPDO);
    $uuid = bin2hex(random_bytes(16)); // simple UUID format
    $uuid = substr($uuid, 0, 8) . '-' . substr($uuid, 8, 4) . '-' . substr($uuid, 12, 4) . '-' . substr($uuid, 16, 4) . '-' . substr($uuid, 20, 12);
    
    $status = 'Pending Accountant 1';

    // Insert Transaction
    $insertTxSql = "
        INSERT INTO transactions (uuid, tracking_number, requestor_id, created_by, division_id, transaction_type, event_name, amount, tax_amount, net_amount, target_date, current_status, remarks) 
        VALUES (:uuid, :tracking_number, :requestor_id, :created_by, :division_id, :transaction_type, :event_name, :amount, :tax_amount, :net_amount, :target_date, :current_status, :remarks)
    ";
    
    $txStmt = $fastPDO->prepare($insertTxSql);
    $txStmt->execute([
        'uuid' => $uuid,
        'tracking_number' => $trackingNumber,
        'requestor_id' => $userId,
        'created_by' => $userId,
        'division_id' => null, // Nullable, default references user's office/division
        'transaction_type' => $type,
        'event_name' => $eventName,
        'amount' => $amount,
        'tax_amount' => $taxAmount,
        'net_amount' => $netAmount,
        'target_date' => empty($targetDate) ? null : $targetDate,
        'current_status' => $status,
        'remarks' => $remarks
    ]);

    $transactionDbId = $fastPDO->lastInsertId();

    // Insert Document details
    $insertDocSql = "
        INSERT INTO document_details (transaction_id, tax_type, attachment_path) 
        VALUES (:transaction_id, :tax_type, :attachment_path)
    ";
    $docStmt = $fastPDO->prepare($insertDocSql);
    $docStmt->execute([
        'transaction_id' => $transactionDbId,
        'tax_type' => $taxType,
        'attachment_path' => $attachmentPath
    ]);

    // Insert Cash Advance Details
    if ($type === 'Cash Advance') {
        // For Communication Expenses, store month in inclusive_dates field as a workaround
        $caInclusiveDates = $inclusiveDates;
        if ($cashAdvanceCategory === 'Communication Expenses' && !empty($caMonthValue)) {
            $caInclusiveDates = $caMonthValue; // Store month reference
        }

        $insertCaSql = "
            INSERT INTO cash_advance_details (transaction_id, category, inclusive_dates, fund_source, venue, approved_ta_path, travel_itinerary_path, activity_proposal_path) 
            VALUES (:transaction_id, :category, :inclusive_dates, :fund_source, :venue, :approved_ta_path, :travel_itinerary_path, :activity_proposal_path)
        ";
        $caStmt = $fastPDO->prepare($insertCaSql);
        $caStmt->execute([
            'transaction_id' => $transactionDbId,
            'category' => $cashAdvanceCategory,
            'inclusive_dates' => $caInclusiveDates,
            'fund_source' => in_array($cashAdvanceCategory, $caFundSourceTypes) ? $fundSource : null,
            'venue' => in_array($cashAdvanceCategory, $caDateVenueTypes) ? $venue : null,
            'approved_ta_path' => $approvedTaPath,
            'travel_itinerary_path' => $travelItineraryPath,
            'activity_proposal_path' => $activityProposalPath
        ]);
    }

    // Insert Reimbursement Details
    if ($type === 'Reimbursement') {
        $insertReimbSql = "
            INSERT INTO reimbursement_details (transaction_id, category, reimbursement_month, inclusive_dates, venue, approved_ta_path, travel_itinerary_path, activity_proposal_path, dtr_path, certificate_path, bill_proof_path) 
            VALUES (:transaction_id, :category, :reimbursement_month, :inclusive_dates, :venue, :approved_ta_path, :travel_itinerary_path, :activity_proposal_path, :dtr_path, :certificate_path, :bill_proof_path)
        ";
        $reimbStmt = $fastPDO->prepare($insertReimbSql);
        $reimbStmt->execute([
            'transaction_id' => $transactionDbId,
            'category' => $reimbursementCategory,
            'reimbursement_month' => $reimbursementMonth ?: null,
            'inclusive_dates' => $reimbInclusiveDates,
            'venue' => in_array($reimbursementCategory, $reimbDateVenueTypes) ? $reimbVenue : null,
            'approved_ta_path' => $reimbApprovedTaPath,
            'travel_itinerary_path' => $reimbTravelItineraryPath,
            'activity_proposal_path' => $reimbActivityProposalPath,
            'dtr_path' => $dtrPath,
            'certificate_path' => $certificatePath,
            'bill_proof_path' => $utilityBillProofPath ?: $billProofPath
        ]);
    }

    // Insert Workflow Log
    $logSql = "
        INSERT INTO transaction_status_logs (transaction_id, previous_status, new_status, changed_by, remarks) 
        VALUES (:transaction_id, NULL, :new_status, :changed_by, :remarks)
    ";
    $logStmt = $fastPDO->prepare($logSql);
    $logStmt->execute([
        'transaction_id' => $transactionDbId,
        'new_status' => $status,
        'changed_by' => $userId,
        'remarks' => 'Initial submission by requestor.'
    ]);

    // Audit Log entry
    AuditLogService::log(
        $fastPDO, 
        $userId, 
        "Submitted new transaction: {$trackingNumber}", 
        null, 
        [
            'tracking_number' => $trackingNumber,
            'amount' => $amount,
            'tax_amount' => $taxAmount,
            'net_amount' => $netAmount,
            'tax_type' => $taxType
        ]
    );

    $fastPDO->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Transaction submitted successfully.',
        'data' => [
            'tracking_number' => $trackingNumber
        ]
    ]);

} catch (Exception $e) {
    if ($fastPDO->inTransaction()) {
        $fastPDO->rollBack();
    }
    // Clean up uploaded files if database transaction fails
    $allUploadedPaths = array_merge(
        $attachmentPaths,
        array_filter([$approvedTaPath, $travelItineraryPath, $activityProposalPath, $dtrPath, $certificatePath, $billProofPath, $reimbApprovedTaPath, $reimbTravelItineraryPath, $reimbActivityProposalPath, $utilityBillProofPath])
    );
    foreach ($allUploadedPaths as $path) {
        $fullPath = dirname(dirname(__DIR__)) . '/' . $path;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
    
    error_log("Transaction submission database failure: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred during transaction submission.']);
}
?>
