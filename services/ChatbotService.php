<?php
/**
 * AI Chatbot Support Service with API Fallback Chain for SDO FAST.
 */

require_once __DIR__ . '/../config/env.php';

class ChatbotService
{

    // Define system prompt rules and restrictions
    private static function getSystemPrompt()
    {
        return "You are the SDO FAST Financial Accounting Virtual Assistant. Your name is FAST AI.\n"
             . "You have complete knowledge of the SDO FAST (Financial Accounting Services and Transactions) system.\n\n"
             . "CRITICAL INSTRUCTIONS:\n"
             . "- Respond in a friendly but professional tone. Use short sentences.\n"
             . "- Give the most important information first. Number steps when giving instructions.\n"
             . "- Never use technical jargon unless the user asks.\n"
             . "- Always confirm understanding at the end of a response by asking if the user needs more help.\n"
             . "- Always respond in plain HTML. Use ordered lists (<ol><li>...</li></ol>) for steps. Use bold (<strong>) for key terms. Keep each step to one sentence.\n"
             . "- NEVER use markdown symbols like double asterisks (**), hashtags (#, ##, ###), or markdown bullet points (-). Only use HTML.\n"
             . "- If you do not know the answer or it is outside the FAST system scope, say: That is outside my knowledge of the FAST system. Please contact your SDO Admin for assistance. Follow this with a closing question.\n"
             . "- DIRECT ANSWERS FROM DATA: If the user is asking a direct question about transaction details (e.g. who submitted, how much, gross amount, status, tax rate) and that data is available in the 'LIVE SYSTEM DATABASE CONTEXT' below, you MUST answer the question directly with the actual value (e.g. 'The gross amount of transaction FAST-2026-000001 is PHP 50,000.00' or 'The latest cash advance transaction FAST-2026-000005 was submitted by Personnel'). Do NOT provide step-by-step instructions on how to find/navigate to it in the system unless they explicitly asked 'how' to find or do it.\n"
             . "- TRACKING NUMBER REFERENCE: When discussing any specific transaction, always mention its tracking number (e.g., <strong>FAST-2026-000001</strong>) so the user knows exactly which transaction you are referring to.\n\n"
             . "RESPONSE STRUCTURE:\n"
             . "Structure every response exactly like this:\n"
             . "1. One or two sentences that directly answer the question (with live data values if asked).\n"
             . "2. Step-by-step instructions in plain HTML using an ordered list (<ol><li>) ONLY if the user asked 'how to' perform an action, 'how to' find something, or if the requested data is not present in the live context.\n"
             . "3. One closing question like: Is there anything else you need help with?\n\n"
             . "EXAMPLE (Direct query about data):\n"
             . "User: who submits the latest cash advance transaction?\n"
             . "FAST AI: The latest cash advance transaction (<strong>FAST-2026-000005</strong>) was submitted by <strong>Personnel</strong>.\n"
             . "Is there anything else you need help with?\n\n"
             . "EXAMPLE (Query about how to do something):\n"
             . "User: How do I submit a transaction?\n"
             . "FAST AI: You can submit a transaction through the transactions module.\n"
             . "<ol>\n"
             . "  <li>Go to the <strong>Transactions</strong> menu.</li>\n"
             . "  <li>Click <strong>New Transaction</strong>.</li>\n"
             . "  <li>Fill in the <strong>Event Name</strong>, <strong>Transaction Type</strong>, and <strong>Amount</strong>.</li>\n"
             . "  <li>Attach your documents (<strong>PDF</strong>, <strong>JPG</strong>, <strong>PNG</strong>, or <strong>DOCX</strong>, max <strong>10MB</strong>).</li>\n"
             . "  <li>Click <strong>Submit</strong>.</li>\n"
             . "</ol>\n"
             . "Do you need help with a specific transaction type?\n\n"
             . "SYSTEM SUMMARY & CONTEXT:\n"
             . "- SDO FAST is an integrated financial management portal for School Division Offices. It tracks transaction claims, automates tax computations, generates financial reports, integrates bidirectionally with SDO-BAC, and maintains robust security logging.\n\n"
             . "USER ROLES & POSITIONS:\n"
             . "- Super Admin: Full system access including user management, configuration, and logs monitoring.\n"
             . "- Admin: Includes roles like Accountant, Accounting Support, and Budget Officer who verify transactions, assign DV numbers, configure tax rates, and manage settings.\n"
             . "- User: Includes requestors (Personnel) and approvers (ASDS, SDS, Financial Approvers).\n\n"
             . "TRANSACTION TYPES & CREATION:\n"
             . "- Claims can be submitted as Cash Advance, Reimbursement, or Payroll.\n"
             . "- Creation requirements: Requestor enters Event Name, Transaction Type, Amount, and attaches required documents. Max attachment size is 10MB (PDF, JPG, PNG, DOCX).\n\n"
             . "TRANSACTION WORKFLOW STAGES:\n"
             . "1. Pending Accountant 1: Initial check by the Accountant after being submitted by Personnel.\n"
             . "2. Pending Support: Verified and assigned appropriate document details by Accounting Support.\n"
             . "3. Pending Budget Check: Reviewed and budget checked by the Budget Officer.\n"
             . "4. Pending Accountant 2: Sent back to Accountant for final checks and validation.\n"
             . "5. Pending Final Approval: Passed to the final approver (ASDS or SDS) for sign-off.\n"
             . "6. Final Statuses: Approved (funds released), Rejected (denied with remarks), or Returned (sent back to requestor for corrections).\n"
             . "- Tracking: Every transaction gets a unique code in the format: FAST-YYYY-000001 (e.g. FAST-2026-000045).\n\n"
             . "TAX COMPUTATION ENGINE:\n"
             . "- Active SDO Tax configurations are: Goods (5%), Foods (2%), and Services (10%).\n"
             . "- Formula: Tax Amount = Gross Amount * Tax Rate. Net Amount = Gross Amount - Tax Amount.\n"
             . "- Admins can configure these tax categories and percentages under settings.\n\n"
             . "SDO-BAC BIDIRECTIONAL INTEGRATION:\n"
             . "- Connects the FAST transaction pipeline with the Bids and Awards Committee (SDO-BAC) system.\n"
             . "- Pulls project references, syncing project numbers, procurement types, and statuses.\n"
             . "- Authorized endpoints use token-based validation using system tokens (BAC_SYSTEM_TOKEN and FAST_SYSTEM_TOKEN).\n\n"
             . "SECURITY & AUDITING:\n"
             . "- Logs all login activity (IP address, browser agent) in login_logs.\n"
             . "- Tracks every data change (insert/update/delete) in activity_logs for audit trails.\n"
             . "- Employs password reset tokens that expire after 1 hour.\n\n"
             . "ADDITIONAL RESTRICTIONS:\n"
             . "- Do NOT expose actual database schemas, security tokens, or environment keys.\n"
             . "- Do NOT fabricate transaction approval statuses. Instruct users to refer to the 'Progress Tracker' tab for real-time tracking.";
    }

    /**
     * Executes chatbot message routing through the fallback sequence.
     * 
     * @param string $userMessage The query from the user.
     * @param int|null $userId User ID submitting the query.
     * @param PDO $pdo FAST database connection.
     * @return array Result containing status and reply message.
     */
    public static function processQuery($userMessage, ?int $userId, PDO $pdo, $activeTracking = null)
    {
        $sanitizedMessage = htmlspecialchars(trim($userMessage));
        if (empty($sanitizedMessage)) {
            return ['success' => false, 'message' => 'Empty message query.'];
        }

        // --- DYNAMIC DATABASE RETRIEVAL ---
        $userName = 'Unknown';
        $userRole = 'User';
        if ($userId !== null) {
            try {
                $stmt = $pdo->prepare("
                    SELECT u.full_name, r.role_name 
                    FROM users u
                    LEFT JOIN user_roles ur ON u.id = ur.user_id 
                    LEFT JOIN roles r ON ur.role_id = r.id 
                    WHERE u.id = :user_id
                ");
                $stmt->execute(['user_id' => $userId]);
                $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($userRow) {
                    $userName = $userRow['full_name'];
                    $userRole = $userRow['role_name'] ?? 'User';
                }
            } catch (PDOException $e) {
                error_log("Failed to fetch user info: " . $e->getMessage());
            }
        }

        // 1. Detect if a specific transaction tracking code is mentioned
        $matchedTransaction = null;
        $trackingNumber = null;
        if (preg_match('/FAST-\d{4}-\d+/i', $sanitizedMessage, $matches)) {
            $trackingNumber = strtoupper($matches[0]);
        }

        // Check if the user is explicitly mentioning a transaction type in this message
        $msgLower = strtolower($sanitizedMessage);
        $mentionsNewType = (
            strpos($msgLower, 'cash advance') !== false || 
            strpos($msgLower, 'cash-advance') !== false ||
            strpos($msgLower, 'reimbursement') !== false || 
            strpos($msgLower, 'payroll') !== false
        );

        // If no tracking number is in the message and they are NOT switching types, try to inherit tracking number
        if ($trackingNumber === null && !$mentionsNewType) {
            // A. Try active tracking code from the client page context
            if (!empty($activeTracking) && preg_match('/FAST-\d{4}-\d+/i', $activeTracking)) {
                $trackingNumber = strtoupper($activeTracking);
            }

            // B. Try tracking code stored in the current PHP session
            if ($trackingNumber === null && isset($_SESSION['chatbot_last_tracking_number'])) {
                $trackingNumber = $_SESSION['chatbot_last_tracking_number'];
            }

            // C. Try looking back at recent chatbot logs
            if ($trackingNumber === null && $userId !== null) {
                try {
                    $stmt = $pdo->prepare("
                        SELECT user_message, bot_response 
                        FROM chatbot_logs 
                        WHERE user_id = :user_id 
                        ORDER BY created_at DESC, id DESC 
                        LIMIT 6
                    ");
                    $stmt->execute(['user_id' => $userId]);
                    $recentLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($recentLogs as $log) {
                        $combinedText = $log['user_message'] . ' ' . $log['bot_response'];
                        if (preg_match('/FAST-\d{4}-\d+/i', $combinedText, $historyMatches)) {
                            $trackingNumber = strtoupper($historyMatches[0]);
                            break;
                        }
                    }
                } catch (PDOException $e) {
                    error_log("Failed to fetch historical tracking number: " . $e->getMessage());
                }
            }
        }

        // Query the matched transaction details (with document details and requestor name)
        if ($trackingNumber !== null) {
            try {
                // Anyone logged in can fetch tracker details for a specific code
                $stmt = $pdo->prepare("
                    SELECT t.*, u.full_name AS requestor_name, d.attachment_path, d.dv_number, d.bir_2307_number, d.tax_type
                    FROM transactions t
                    LEFT JOIN users u ON t.requestor_id = u.id
                    LEFT JOIN document_details d ON t.id = d.transaction_id
                    WHERE t.tracking_number = :tracking
                ");
                $stmt->execute(['tracking' => $trackingNumber]);
                $matchedTransaction = $stmt->fetch(PDO::FETCH_ASSOC);

                // Save it to session for subsequent follow-up questions
                if ($matchedTransaction) {
                    $_SESSION['chatbot_last_tracking_number'] = $matchedTransaction['tracking_number'];
                }
            } catch (PDOException $e) {
                error_log("Failed to fetch matched transaction details: " . $e->getMessage());
            }
        }

        // 2. Fetch recent transactions based on user visibility rules (with attachment details)
        $recentTransactions = [];
        if ($userId !== null) {
            try {
                if (!in_array($userRole, ['Requestor', 'User'])) {
                    $stmt = $pdo->prepare("
                        SELECT t.tracking_number, t.transaction_type, t.event_name, t.amount, t.tax_amount, t.net_amount, t.current_status, t.remarks, t.created_at, u.full_name AS requestor_name, d.attachment_path
                        FROM transactions t
                        LEFT JOIN users u ON t.requestor_id = u.id
                        LEFT JOIN document_details d ON t.id = d.transaction_id
                        ORDER BY t.created_at DESC, t.id DESC
                        LIMIT 15
                    ");
                    $stmt->execute();
                } else {
                    $stmt = $pdo->prepare("
                        SELECT t.tracking_number, t.transaction_type, t.event_name, t.amount, t.tax_amount, t.net_amount, t.current_status, t.remarks, t.created_at, u.full_name AS requestor_name, d.attachment_path
                        FROM transactions t
                        LEFT JOIN users u ON t.requestor_id = u.id
                        LEFT JOIN document_details d ON t.id = d.transaction_id
                        WHERE t.requestor_id = :user_id
                        ORDER BY t.created_at DESC, t.id DESC
                        LIMIT 15
                    ");
                    $stmt->execute(['user_id' => $userId]);
                }
                $recentTransactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Failed to fetch recent transactions: " . $e->getMessage());
            }
        }

        // 3. Fetch current active tax rates config
        $taxConfigs = [];
        try {
            $stmt = $pdo->query("SELECT tax_type, tax_percentage FROM tax_configurations WHERE is_active = 1");
            $taxConfigs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to fetch tax configs: " . $e->getMessage());
        }

        // --- HEURISTIC CONTEXT LINKING ---
        // If still null, try to infer the transaction from the query topic (e.g. "cash advance", "reimbursement", "payroll") and recent transactions list
        if ($matchedTransaction === null && !empty($recentTransactions)) {
            $msgLower = strtolower($sanitizedMessage);
            
            // Check for explicit type matches
            $inferredType = null;
            if (strpos($msgLower, 'cash advance') !== false || strpos($msgLower, 'cash-advance') !== false) {
                $inferredType = 'Cash Advance';
            } elseif (strpos($msgLower, 'reimbursement') !== false) {
                $inferredType = 'Reimbursement';
            } elseif (strpos($msgLower, 'payroll') !== false) {
                $inferredType = 'Payroll';
            }

            if ($inferredType !== null) {
                foreach ($recentTransactions as $rt) {
                    if (strcasecmp($rt['transaction_type'], $inferredType) === 0) {
                        try {
                            $stmt = $pdo->prepare("
                                SELECT t.*, u.full_name AS requestor_name, d.attachment_path, d.dv_number, d.bir_2307_number, d.tax_type
                                FROM transactions t
                                LEFT JOIN users u ON t.requestor_id = u.id
                                LEFT JOIN document_details d ON t.id = d.transaction_id
                                WHERE t.tracking_number = :tracking
                            ");
                            $stmt->execute(['tracking' => $rt['tracking_number']]);
                            $matchedTransaction = $stmt->fetch(PDO::FETCH_ASSOC);
                            if ($matchedTransaction) {
                                // Save it to session for subsequent follow-up questions
                                $_SESSION['chatbot_last_tracking_number'] = $matchedTransaction['tracking_number'];
                                break;
                            }
                        } catch (PDOException $e) {
                            error_log("Failed to fetch inferred transaction: " . $e->getMessage());
                        }
                    }
                }
            }
            
            // If still null and there is exactly one transaction in recent transactions, assume that is the one they are referring to
            if ($matchedTransaction === null && count($recentTransactions) === 1) {
                try {
                    $stmt = $pdo->prepare("
                        SELECT t.*, u.full_name AS requestor_name, d.attachment_path, d.dv_number, d.bir_2307_number, d.tax_type
                        FROM transactions t
                        LEFT JOIN users u ON t.requestor_id = u.id
                        LEFT JOIN document_details d ON t.id = d.transaction_id
                        WHERE t.tracking_number = :tracking
                    ");
                    $stmt->execute(['tracking' => $recentTransactions[0]['tracking_number']]);
                    $matchedTransaction = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($matchedTransaction) {
                        $_SESSION['chatbot_last_tracking_number'] = $matchedTransaction['tracking_number'];
                    }
                } catch (PDOException $e) {
                    error_log("Failed to fetch single transaction: " . $e->getMessage());
                }
            }
        }

        // 4. Construct live data context block
        $realtimeContext = "\n\n=== LIVE SYSTEM DATABASE CONTEXT ===\n"
                         . "Logged-in User Name: {$userName} (Role: {$userRole}, ID: {$userId})\n\n";

        if ($matchedTransaction) {
            $realtimeContext .= "SPECIFIC TRANSACTION RETRIEVED (User asked about or is referring to {$matchedTransaction['tracking_number']}):\n"
                              . "- Tracking Number: {$matchedTransaction['tracking_number']}\n"
                              . "- Event Name / Purpose: {$matchedTransaction['event_name']}\n"
                              . "- Claim Type / Category: {$matchedTransaction['transaction_type']}\n"
                              . "- Gross Amount: PHP " . number_format($matchedTransaction['amount'], 2) . "\n"
                              . "- Tax Amount: PHP " . number_format($matchedTransaction['tax_amount'], 2) . "\n"
                              . "- Net Amount: PHP " . number_format($matchedTransaction['net_amount'], 2) . "\n"
                              . "- Current Status: {$matchedTransaction['current_status']}\n"
                              . "- Requestor / Who Submitted: {$matchedTransaction['requestor_name']}\n"
                              . "- Remarks / Comments: " . ($matchedTransaction['remarks'] ?: 'No remarks/comments.') . "\n"
                              . "- Date Created / Submitted: {$matchedTransaction['created_at']}\n"
                              . "- DV (Document Voucher) Number: " . ($matchedTransaction['dv_number'] ?: 'Not assigned yet') . "\n"
                              . "- BIR 2307 Number: " . ($matchedTransaction['bir_2307_number'] ?: 'Not generated yet') . "\n"
                              . "- Tax Type Applied: " . ($matchedTransaction['tax_type'] ?: 'None') . "\n"
                              . "- Attached File(s): " . (function() use ($matchedTransaction) {
                                  $atts = json_decode($matchedTransaction['attachment_path'], true);
                                  if (is_array($atts)) {
                                      return implode(', ', array_map('basename', $atts));
                                  }
                                  return $matchedTransaction['attachment_path'] ? basename($matchedTransaction['attachment_path']) : 'No files attached';
                              })() . "\n"
                              . "- BAC Project Ref: " . ($matchedTransaction['bac_project_number'] ?: 'None') . " (" . ($matchedTransaction['bac_procurement_type'] ?: 'N/A') . ")\n\n";
        }

        if (!empty($recentTransactions)) {
            $realtimeContext .= "RECENT TRANSACTIONS VISIBLE TO THIS USER:\n";
            foreach ($recentTransactions as $index => $t) {
                $num = $index + 1;
                $fileName = (function() use ($t) {
                    $atts = json_decode($t['attachment_path'], true);
                    if (is_array($atts)) {
                        return implode(', ', array_map('basename', $atts));
                    }
                    return $t['attachment_path'] ? basename($t['attachment_path']) : 'None';
                })();
                $realtimeContext .= "{$num}. Tracking Number: {$t['tracking_number']} | Type: {$t['transaction_type']} | Event: {$t['event_name']} | Amount: PHP " . number_format($t['amount'], 2) . " | Net Amount: PHP " . number_format($t['net_amount'], 2) . " | Status: {$t['current_status']} | Attached File(s): {$fileName} | Date: {$t['created_at']} | Remarks: " . ($t['remarks'] ?: 'None') . "\n";
            }
        } else {
            $realtimeContext .= "No transactions found in user scope.\n";
        }

        if (!empty($taxConfigs)) {
            $realtimeContext .= "\nCURRENT LIVE SYSTEM TAX RATES:\n";
            foreach ($taxConfigs as $tax) {
                $realtimeContext .= "- {$tax['tax_type']}: {$tax['tax_percentage']}%\n";
            }
        }

        // Combine system prompt with live database data
        $systemPrompt = self::getSystemPrompt() . $realtimeContext;
        $reply = null;
        $providerUsed = null;

        // Fetch the last 5 turns of conversation to pass as history/context to the LLM
        $chatHistory = [];
        if ($userId !== null) {
            try {
                $stmt = $pdo->prepare("
                    SELECT user_message, bot_response 
                    FROM chatbot_logs 
                    WHERE user_id = :user_id 
                    ORDER BY created_at DESC, id DESC 
                    LIMIT 5
                ");
                $stmt->execute(['user_id' => $userId]);
                $chatHistory = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));
            } catch (PDOException $e) {
                error_log("Failed to fetch chat history for LLM: " . $e->getMessage());
            }
        }

        // 1. Attempt Groq (Llama 3.1 8B)
        $groqKey = env('GROQ_API_KEY');
        if (!empty($groqKey)) {
            $reply = self::queryGroq($sanitizedMessage, $systemPrompt, $groqKey, $chatHistory);
            if ($reply) {
                $providerUsed = 'Groq';
            }
        }

        // 2. Fall back to Gemini (Gemini 2.5 Flash)
        if (!$reply) {
            $geminiKey = env('GEMINI_API_KEY');
            if (!empty($geminiKey)) {
                $reply = self::queryGemini($sanitizedMessage, $systemPrompt, $geminiKey, $chatHistory);
                if ($reply) {
                    $providerUsed = 'Gemini';
                }
            }
        }

        // 3. Fall back to OpenRouter (Llama 3.1 8B)
        if (!$reply) {
            $openRouterKey = env('OPENROUTER_API_KEY');
            if (!empty($openRouterKey)) {
                $reply = self::queryOpenRouter($sanitizedMessage, $systemPrompt, $openRouterKey, $chatHistory);
                if ($reply) {
                    $providerUsed = 'OpenRouter';
                }
            }
        }

        // 4. Failed all fallbacks
        if (!$reply) {
            return [
                'success' => false,
                'message' => 'The FAST AI Chatbot service is currently offline. Please try again later or consult the help desk.'
            ];
        }

        // Clean up response formatting to strictly output HTML
        $reply = self::formatResponseToHtml($reply);

        // Save entry into chatbot_logs
        try {
            $stmt = $pdo->prepare("
                INSERT INTO chatbot_logs (user_id, user_message, bot_response, provider_used) 
                VALUES (:user_id, :user_message, :bot_response, :provider_used)
            ");
            $stmt->execute([
                'user_id' => $userId,
                'user_message' => $sanitizedMessage,
                'bot_response' => $reply,
                'provider_used' => $providerUsed
            ]);
        } catch (PDOException $e) {
            error_log("Failed to log chatbot interaction: " . $e->getMessage());
        }

        return [
            'success' => true,
            'message' => $reply,
            'provider' => $providerUsed
        ];
    }

    /**
     * Unified POST request runner supporting curl and fallback to stream wrapper.
     */
    private static function makePostRequest($url, $headers, $payload)
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_TIMEOUT, 8);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return [
                'code' => $httpCode,
                'response' => $response
            ];
        } else {
            $options = [
                'http' => [
                    'header' => implode("\r\n", $headers) . "\r\n",
                    'method' => 'POST',
                    'content' => json_encode($payload),
                    'timeout' => 8,
                    'ignore_errors' => true
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ];
            $context = stream_context_create($options);
            $response = @file_get_contents($url, false, $context);

            $httpCode = 500;
            if (isset($http_response_header) && is_array($http_response_header)) {
                if (preg_match('/HTTP\/\d\.\d\s+(\d+)/i', $http_response_header[0], $matches)) {
                    $httpCode = intval($matches[1]);
                }
            }

            return [
                'code' => $httpCode,
                'response' => $response
            ];
        }
    }

    /**
     * query to Groq
     */
    private static function queryGroq($message, $systemPrompt, $apiKey, $chatHistory = [])
    {
        $url = 'https://api.groq.com/openai/v1/chat/completions';
        
        $messages = [];
        $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        foreach ($chatHistory as $log) {
            $messages[] = ['role' => 'user', 'content' => $log['user_message']];
            $messages[] = ['role' => 'assistant', 'content' => $log['bot_response']];
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        $payload = [
            'model' => $_ENV['GROQ_MODEL'] ?? 'openai/gpt-oss-120b',
            'messages' => $messages,
            'temperature' => 0.2
        ];

        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ];

        $result = self::makePostRequest($url, $headers, $payload);

        if ($result['code'] === 200 && $result['response']) {
            $data = json_decode($result['response'], true);
            return $data['choices'][0]['message']['content'] ?? null;
        }

        error_log("Groq Chatbot API error code {$result['code']}: " . $result['response']);
        return null;
    }

    /**
     * query to Gemini
     */
    private static function queryGemini($message, $systemPrompt, $apiKey, $chatHistory = [])
    {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey;

        $contents = [];
        foreach ($chatHistory as $log) {
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $log['user_message']]]
            ];
            $contents[] = [
                'role' => 'model',
                'parts' => [['text' => $log['bot_response']]]
            ];
        }
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $message]]
        ];

        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemPrompt]
                ]
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.2
            ]
        ];

        $headers = [
            'Content-Type: application/json'
        ];

        $result = self::makePostRequest($url, $headers, $payload);

        if ($result['code'] === 200 && $result['response']) {
            $data = json_decode($result['response'], true);
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        }

        error_log("Gemini Chatbot API error code {$result['code']}: " . $result['response']);
        return null;
    }

    /**
     * query to OpenRouter
     */
    private static function queryOpenRouter($message, $systemPrompt, $apiKey, $chatHistory = [])
    {
        $url = 'https://openrouter.ai/api/v1/chat/completions';
        
        $messages = [];
        $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        foreach ($chatHistory as $log) {
            $messages[] = ['role' => 'user', 'content' => $log['user_message']];
            $messages[] = ['role' => 'assistant', 'content' => $log['bot_response']];
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        $payload = [
            'model' => 'meta-llama/llama-3.1-8b-instruct',
            'messages' => $messages,
            'temperature' => 0.2
        ];

        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
            'HTTP-Referer: http://localhost/fast',
            'X-Title: SDO FAST'
        ];

        $result = self::makePostRequest($url, $headers, $payload);

        if ($result['code'] === 200 && $result['response']) {
            $data = json_decode($result['response'], true);
            return $data['choices'][0]['message']['content'] ?? null;
        }

        error_log("OpenRouter Chatbot API error code {$result['code']}: " . $result['response']);
        return null;
    }

    /**
     * Clean up and post-process LLM response text into clean, valid HTML tags.
     * Prevents markdown leak (like **bold**, ### headers, etc.) by converting them.
     */
    public static function formatResponseToHtml($text)
    {
        if (empty($text)) {
            return $text;
        }

        // 1. Convert markdown bold (**text**) to HTML (<strong>text</strong>)
        $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
        
        // 2. Convert markdown bold (*text*) to HTML (<em>text</em>)
        $text = preg_replace('/\*([^\*]+)\*/', '<em>$1</em>', $text);

        // 3. Convert markdown headers (### Header) to strong titles
        $text = preg_replace('/^###\s+(.*?)$/m', '<h6 class="fw-bold mt-2 mb-1">$1</h6>', $text);
        $text = preg_replace('/^##\s+(.*?)$/m', '<h5 class="fw-bold mt-3 mb-1">$1</h5>', $text);
        $text = preg_replace('/^#\s+(.*?)$/m', '<h4 class="fw-bold mt-3 mb-1">$1</h4>', $text);

        // 4. Convert plain markdown lists if present
        if (strpos($text, '<ul>') === false && strpos($text, '<ol>') === false) {
            $text = preg_replace('/^\s*[-*+]\s+(.*?)$/m', '<li>$1</li>', $text);
            if (strpos($text, '<li>') !== false) {
                $text = preg_replace('/(<li>.*?<\/li>)+/s', '<ul>$0</ul>', $text);
            }
        }

        return $text;
    }
}
