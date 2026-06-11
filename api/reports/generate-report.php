<?php
/**
 * Reports Generator API for SDO FAST.
 * Compiles stats and exports to JSON (Screen), CSV, and PDF (FPDF).
 */

// Load session & dependencies
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php'; // Enforces auth
require_once __DIR__ . '/../../vendor/autoload.php';

if ($fastPDO === null) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'];

// 1. Report Access Controls
if ($userRole === 'Requestor') {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: Requestors do not have reports access.']);
    exit;
}

$reportType = trim($_GET['report_type'] ?? '');
$format = trim($_GET['format'] ?? 'screen'); // screen, csv, pdf

// Verify role authorization per report type
if ($userRole === 'Budget Officer') {
    // Budget officers can only view Transaction Summary and Financial Summary
    if (!in_array($reportType, ['transaction_summary', 'financial_summary'])) {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden: Budget Officers can only access Transaction and Financial Summaries.']);
        exit;
    }
} elseif ($userRole === 'Approver') {
    // Approvers can only view Pending Approvals
    if ($reportType !== 'pending_approvals') {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden: Approvers are restricted to Pending Approvals report.']);
        exit;
    }
}

// 2. Fetch parameters and prepare query filters
$dateStart = trim($_GET['date_start'] ?? '');
$dateEnd = trim($_GET['date_end'] ?? '');
$transactionType = trim($_GET['transaction_type'] ?? '');
$status = trim($_GET['status'] ?? '');
$requestorId = (int)($_GET['requestor_id'] ?? 0);

// Default date range is current month if empty
if (empty($dateStart)) {
    $dateStart = date('Y-m-01');
}
if (empty($dateEnd)) {
    $dateEnd = date('Y-m-t');
}

$whereClauses = [];
$params = [];

// Apply Date Range
$whereClauses[] = "t.created_at BETWEEN :date_start AND :date_end";
$params['date_start'] = $dateStart . ' 00:00:00';
$params['date_end'] = $dateEnd . ' 23:59:59';

// Apply optional filters
if (!empty($transactionType)) {
    $whereClauses[] = "t.transaction_type = :type";
    $params['type'] = $transactionType;
}
if (!empty($status)) {
    $whereClauses[] = "t.current_status = :status";
    $params['status'] = $status;
}
if ($requestorId > 0) {
    $whereClauses[] = "t.requestor_id = :requestor_id";
    $params['requestor_id'] = $requestorId;
}

// Apply role-based data visibility scope filter
$whereClauses[] = get_data_scope_filter($userRole, $userId, 't');

$whereSql = implode(' AND ', $whereClauses);

// 3. Execute queries based on Report Type
$data = [];
$reportTitle = '';

try {
    if ($reportType === 'transaction_summary') {
        $reportTitle = 'Transaction Summary Report';
        $sql = "
            SELECT t.transaction_type, t.current_status, 
                   COUNT(*) as count, 
                   SUM(t.amount) as total_amount, 
                   SUM(t.tax_amount) as total_tax, 
                   SUM(t.net_amount) as total_net
            FROM transactions t
            WHERE {$whereSql}
            GROUP BY t.transaction_type, t.current_status
            ORDER BY t.transaction_type ASC, t.current_status ASC
        ";
        $stmt = $fastPDO->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        
    } elseif ($reportType === 'financial_summary') {
        $reportTitle = 'Financial Summary Report';
        // Group by month
        $sql = "
            SELECT YEAR(t.created_at) as year_num, MONTH(t.created_at) as month_num, 
                   SUM(t.amount) as total_amount, 
                   SUM(t.tax_amount) as total_tax, 
                   SUM(t.net_amount) as total_net,
                   COUNT(*) as transaction_count
            FROM transactions t
            WHERE {$whereSql}
            GROUP BY YEAR(t.created_at), MONTH(t.created_at)
            ORDER BY year_num DESC, month_num DESC
        ";
        $stmt = $fastPDO->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        
    } elseif ($reportType === 'pending_approvals') {
        $reportTitle = 'Pending Approvals aging Report';
        // Filter transactions currently in a pending state with DATEDIFF
        $pendingWhereSql = str_replace('t.current_status = :status', '1=1', $whereSql); // override status filter
        unset($params['status']);
        
        $sql = "
            SELECT t.*, u.full_name as requestor_name, 
                   DATEDIFF(NOW(), t.created_at) as aging_days
            FROM transactions t
            LEFT JOIN users u ON t.requestor_id = u.id
            WHERE t.current_status IN ('Pending Accountant 1', 'Pending Support', 'Pending Budget Check', 'Pending Accountant 2', 'Pending Final Approval')
              AND {$pendingWhereSql}
            ORDER BY aging_days DESC
        ";
        $stmt = $fastPDO->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        
    } elseif ($reportType === 'audit_trail') {
        $reportTitle = 'Audit Trail Report';
        // Audit log query filtered by user/dates
        $auditWhere = "a.created_at BETWEEN :date_start AND :date_end";
        $auditParams = [
            'date_start' => $dateStart . ' 00:00:00',
            'date_end' => $dateEnd . ' 23:59:59'
        ];
        if ($requestorId > 0) {
            $auditWhere .= " AND a.user_id = :user_id";
            $auditParams['user_id'] = $requestorId;
        }

        $sql = "
            SELECT a.*, u.full_name, u.email
            FROM activity_logs a
            LEFT JOIN users u ON a.user_id = u.id
            WHERE {$auditWhere}
            ORDER BY a.created_at DESC
        ";
        $stmt = $fastPDO->prepare($sql);
        $stmt->execute($auditParams);
        $data = $stmt->fetchAll();
    } else {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid report type specified.']);
        exit;
    }

    // 4. Output Render Formats
    if ($format === 'screen') {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'report_title' => $reportTitle,
            'date_range' => "{$dateStart} to {$dateEnd}",
            'data' => $data
        ]);
        exit;
        
    } elseif ($format === 'csv') {
        // Generate CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="report_' . $reportType . '_' . date('Ymd_His') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        
        // Write Header metadata
        fputcsv($output, [$reportTitle]);
        fputcsv($output, ["Date Range: {$dateStart} to {$dateEnd}"]);
        fputcsv($output, ["Generated At: " . date('Y-m-d H:i:s')]);
        fputcsv($output, []); // empty spacer

        if ($reportType === 'transaction_summary') {
            fputcsv($output, ['Transaction Type', 'Workflow Status', 'Transaction Count', 'Gross Amount', 'Tax Amount', 'Net Amount']);
            $totCount = 0; $totGross = 0; $totTax = 0; $totNet = 0;
            foreach ($data as $row) {
                fputcsv($output, [
                    $row['transaction_type'],
                    $row['current_status'],
                    $row['count'],
                    $row['total_amount'],
                    $row['total_tax'],
                    $row['total_net']
                ]);
                $totCount += $row['count'];
                $totGross += $row['total_amount'];
                $totTax += $row['total_tax'];
                $totNet += $row['total_net'];
            }
            fputcsv($output, []);
            fputcsv($output, ['GRAND TOTALS', '', $totCount, $totGross, $totTax, $totNet]);
            
        } elseif ($reportType === 'financial_summary') {
            fputcsv($output, ['Year', 'Month', 'Transaction Count', 'Gross Amount', 'Tax Amount', 'Net Amount']);
            $totCount = 0; $totGross = 0; $totTax = 0; $totNet = 0;
            foreach ($data as $row) {
                $monthName = date('F', mktime(0, 0, 0, $row['month_num'], 10));
                fputcsv($output, [
                    $row['year_num'],
                    $monthName,
                    $row['transaction_count'],
                    $row['total_amount'],
                    $row['total_tax'],
                    $row['total_net']
                ]);
                $totCount += $row['transaction_count'];
                $totGross += $row['total_amount'];
                $totTax += $row['total_tax'];
                $totNet += $row['total_net'];
            }
            fputcsv($output, []);
            fputcsv($output, ['GRAND TOTALS', '', $totCount, $totGross, $totTax, $totNet]);
            
        } elseif ($reportType === 'pending_approvals') {
            fputcsv($output, ['Tracking Number', 'Event/Particulars', 'Type', 'Requestor', 'Gross Amount', 'Net Amount', 'Status', 'Aging (Days)', 'Submitted Date']);
            $totGross = 0; $totNet = 0;
            foreach ($data as $row) {
                fputcsv($output, [
                    $row['tracking_number'],
                    $row['event_name'],
                    $row['transaction_type'],
                    $row['requestor_name'],
                    $row['amount'],
                    $row['net_amount'],
                    $row['current_status'],
                    $row['aging_days'],
                    $row['created_at']
                ]);
                $totGross += $row['amount'];
                $totNet += $row['net_amount'];
            }
            fputcsv($output, []);
            fputcsv($output, ['GRAND TOTALS', '', '', '', $totGross, $totNet, '', '', '']);
            
        } elseif ($reportType === 'audit_trail') {
            fputcsv($output, ['Timestamp', 'Activity Log', 'User', 'Email', 'IP Address', 'Old Value', 'New Value']);
            foreach ($data as $row) {
                fputcsv($output, [
                    $row['created_at'],
                    $row['activity'],
                    $row['full_name'] ?: 'System Event',
                    $row['email'] ?: 'N/A',
                    $row['ip_address'],
                    $row['old_value'],
                    $row['new_value']
                ]);
            }
        }
        fclose($output);
        exit;
        
    } elseif ($format === 'pdf') {
        // Generate PDF using FPDF
        class FAST_PDF extends FPDF {
            public $reportTitle;
            public $dateRange;
            
            function Header() {
                // Apply Template Branding Theme variables in RGB format
                // Primary Dark: #0a2f4a -> RGB (10, 47, 74)
                // Accent: #d4af37 -> RGB (212, 175, 55)
                
                $this->SetFont('Arial', 'B', 15);
                $this->SetTextColor(10, 47, 74); // Primary Dark
                $this->Cell(0, 8, 'SDO FAST - Financial Accounting Services & Transactions', 0, 1, 'L');
                
                $this->SetFont('Arial', 'B', 11);
                $this->SetTextColor(212, 175, 55); // Accent
                $this->Cell(0, 6, strtoupper($this->reportTitle), 0, 1, 'L');
                
                $this->SetFont('Arial', 'I', 8);
                $this->SetTextColor(100, 116, 139); // Text Muted
                $this->Cell(0, 4, 'Date Range Filter: ' . $this->dateRange, 0, 1, 'L');
                $this->Cell(0, 4, 'Generated At: ' . date('Y-m-d H:i:s'), 0, 1, 'L');
                $this->Ln(8);
            }
            
            function Footer() {
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                $this->SetTextColor(148, 163, 184);
                $this->Cell(0, 10, 'Page ' . $this->PageNo() . ' of {nb} | SDO FAST Confidential Accounting Reports', 0, 0, 'C');
            }
        }

        $pdf = new FAST_PDF('P', 'mm', 'A4');
        $pdf->reportTitle = $reportTitle;
        $pdf->dateRange = "{$dateStart} to {$dateEnd}";
        $pdf->AliasNbPages();
        $pdf->AddPage();
        
        // Define Template Primary Colors
        // Primary: #0f4c75 -> RGB (15, 76, 117)
        // Primary Light: #1b6ca8 -> RGB (27, 108, 168)
        
        if ($reportType === 'transaction_summary') {
            // Write column headers
            $pdf->SetFillColor(15, 76, 117); // Primary Theme Color
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 8);
            
            $pdf->Cell(45, 8, 'Transaction Type', 1, 0, 'L', true);
            $pdf->Cell(35, 8, 'Workflow Status', 1, 0, 'L', true);
            $pdf->Cell(20, 8, 'Count', 1, 0, 'C', true);
            $pdf->Cell(30, 8, 'Gross Amount', 1, 0, 'R', true);
            $pdf->Cell(30, 8, 'Tax Amount', 1, 0, 'R', true);
            $pdf->Cell(30, 8, 'Net Amount', 1, 1, 'R', true);
            
            $pdf->SetTextColor(30, 41, 59); // text main
            $pdf->SetFont('Arial', '', 8);
            
            $totCount = 0; $totGross = 0; $totTax = 0; $totNet = 0;
            $fill = false;
            foreach ($data as $row) {
                // Zebra coloring
                $pdf->SetFillColor($fill ? 248 : 255, $fill ? 250 : 255, $fill ? 252 : 255);
                
                $pdf->Cell(45, 8, $row['transaction_type'], 1, 0, 'L', true);
                $pdf->Cell(35, 8, $row['current_status'], 1, 0, 'L', true);
                $pdf->Cell(20, 8, $row['count'], 1, 0, 'C', true);
                $pdf->Cell(30, 8, 'Php ' . number_format($row['total_amount'], 2), 1, 0, 'R', true);
                $pdf->Cell(30, 8, 'Php ' . number_format($row['total_tax'], 2), 1, 0, 'R', true);
                $pdf->Cell(30, 8, 'Php ' . number_format($row['total_net'], 2), 1, 1, 'R', true);
                
                $totCount += $row['count'];
                $totGross += $row['total_amount'];
                $totTax += $row['total_tax'];
                $totNet += $row['total_net'];
                $fill = !$fill;
            }
            
            // Render Totals
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(241, 245, 249);
            $pdf->Cell(80, 8, 'GRAND TOTALS', 1, 0, 'L', true);
            $pdf->Cell(20, 8, $totCount, 1, 0, 'C', true);
            $pdf->Cell(30, 8, 'Php ' . number_format($totGross, 2), 1, 0, 'R', true);
            $pdf->Cell(30, 8, 'Php ' . number_format($totTax, 2), 1, 0, 'R', true);
            $pdf->Cell(30, 8, 'Php ' . number_format($totNet, 2), 1, 1, 'R', true);

        } elseif ($reportType === 'financial_summary') {
            $pdf->SetFillColor(15, 76, 117);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 8);
            
            $pdf->Cell(25, 8, 'Year', 1, 0, 'C', true);
            $pdf->Cell(30, 8, 'Month', 1, 0, 'C', true);
            $pdf->Cell(25, 8, 'Transactions', 1, 0, 'C', true);
            $pdf->Cell(35, 8, 'Gross Amount', 1, 0, 'R', true);
            $pdf->Cell(35, 8, 'Tax Amount', 1, 0, 'R', true);
            $pdf->Cell(40, 8, 'Net Amount', 1, 1, 'R', true);
            
            $pdf->SetTextColor(30, 41, 59);
            $pdf->SetFont('Arial', '', 8);
            
            $totCount = 0; $totGross = 0; $totTax = 0; $totNet = 0;
            $fill = false;
            foreach ($data as $row) {
                $pdf->SetFillColor($fill ? 248 : 255, $fill ? 250 : 255, $fill ? 252 : 255);
                $monthName = date('F', mktime(0, 0, 0, $row['month_num'], 10));
                
                $pdf->Cell(25, 8, $row['year_num'], 1, 0, 'C', true);
                $pdf->Cell(30, 8, $monthName, 1, 0, 'C', true);
                $pdf->Cell(25, 8, $row['transaction_count'], 1, 0, 'C', true);
                $pdf->Cell(35, 8, 'Php ' . number_format($row['total_amount'], 2), 1, 0, 'R', true);
                $pdf->Cell(35, 8, 'Php ' . number_format($row['total_tax'], 2), 1, 0, 'R', true);
                $pdf->Cell(40, 8, 'Php ' . number_format($row['total_net'], 2), 1, 1, 'R', true);
                
                $totCount += $row['transaction_count'];
                $totGross += $row['total_amount'];
                $totTax += $row['total_tax'];
                $totNet += $row['total_net'];
                $fill = !$fill;
            }
            
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(241, 245, 249);
            $pdf->Cell(55, 8, 'GRAND TOTALS', 1, 0, 'L', true);
            $pdf->Cell(25, 8, $totCount, 1, 0, 'C', true);
            $pdf->Cell(35, 8, 'Php ' . number_format($totGross, 2), 1, 0, 'R', true);
            $pdf->Cell(35, 8, 'Php ' . number_format($totTax, 2), 1, 0, 'R', true);
            $pdf->Cell(40, 8, 'Php ' . number_format($totNet, 2), 1, 1, 'R', true);
            
        } elseif ($reportType === 'pending_approvals') {
            $pdf->SetFillColor(15, 76, 117);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 8);
            
            $pdf->Cell(30, 8, 'Tracking No.', 1, 0, 'L', true);
            $pdf->Cell(45, 8, 'Particulars/Event', 1, 0, 'L', true);
            $pdf->Cell(25, 8, 'Type', 1, 0, 'L', true);
            $pdf->Cell(30, 8, 'Requestor', 1, 0, 'L', true);
            $pdf->Cell(25, 8, 'Net Amount', 1, 0, 'R', true);
            $pdf->Cell(20, 8, 'Status', 1, 0, 'C', true);
            $pdf->Cell(15, 8, 'Aging(d)', 1, 1, 'C', true);
            
            $pdf->SetTextColor(30, 41, 59);
            $pdf->SetFont('Arial', '', 7);
            
            $totNet = 0;
            $fill = false;
            foreach ($data as $row) {
                $pdf->SetFillColor($fill ? 248 : 255, $fill ? 250 : 255, $fill ? 252 : 255);
                
                // Truncate long strings for PDF cell limits
                $evt = strlen($row['event_name']) > 28 ? substr($row['event_name'], 0, 26) . '..' : $row['event_name'];
                $req = strlen($row['requestor_name']) > 16 ? substr($row['requestor_name'], 0, 14) . '..' : $row['requestor_name'];

                $pdf->Cell(30, 8, $row['tracking_number'], 1, 0, 'L', true);
                $pdf->Cell(45, 8, $evt, 1, 0, 'L', true);
                $pdf->Cell(25, 8, $row['transaction_type'], 1, 0, 'L', true);
                $pdf->Cell(30, 8, $req, 1, 0, 'L', true);
                $pdf->Cell(25, 8, 'Php ' . number_format($row['net_amount'], 2), 1, 0, 'R', true);
                $pdf->Cell(20, 8, $row['current_status'], 1, 0, 'C', true);
                $pdf->Cell(15, 8, $row['aging_days'], 1, 1, 'C', true);
                
                $totNet += $row['net_amount'];
                $fill = !$fill;
            }
            
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(241, 245, 249);
            $pdf->Cell(130, 8, 'TOTAL ESTIMATED OUTSTANDING PAYOUT', 1, 0, 'L', true);
            $pdf->Cell(25, 8, 'Php ' . number_format($totNet, 2), 1, 0, 'R', true);
            $pdf->Cell(35, 8, '', 1, 1, 'C', true); // spacers
            
        } elseif ($reportType === 'audit_trail') {
            // Audit logs can be long, so adjust table column scales
            $pdf->SetFillColor(15, 76, 117);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 8);
            
            $pdf->Cell(32, 8, 'Timestamp', 1, 0, 'C', true);
            $pdf->Cell(70, 8, 'Activity Log Description', 1, 0, 'L', true);
            $pdf->Cell(45, 8, 'Actioner Account', 1, 0, 'L', true);
            $pdf->Cell(43, 8, 'IP Address', 1, 1, 'C', true);
            
            $pdf->SetTextColor(30, 41, 59);
            $pdf->SetFont('Arial', '', 7);
            
            $fill = false;
            foreach ($data as $row) {
                $pdf->SetFillColor($fill ? 248 : 255, $fill ? 250 : 255, $fill ? 252 : 255);
                
                $actText = strlen($row['activity']) > 48 ? substr($row['activity'], 0, 46) . '..' : $row['activity'];
                $userStr = $row['full_name'] ? $row['full_name'] : 'System Event';

                $pdf->Cell(32, 8, $row['created_at'], 1, 0, 'C', true);
                $pdf->Cell(70, 8, $actText, 1, 0, 'L', true);
                $pdf->Cell(45, 8, $userStr, 1, 0, 'L', true);
                $pdf->Cell(43, 8, $row['ip_address'], 1, 1, 'C', true);
                
                $fill = !$fill;
            }
        }
        
        $pdf->Output('I', 'report_' . $reportType . '_' . date('Ymd_His') . '.pdf');
        exit;
    }

} catch (PDOException $e) {
    error_log("Report compiler SQL error: " . $e->getMessage());
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database query execution failure.']);
}
