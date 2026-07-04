<?php
require_once __DIR__ . '/../../config/session.php';

header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Session refreshed.']);
