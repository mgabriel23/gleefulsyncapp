<?php
    header('Content-Type: application/json');
    header('Cache-Control: no-cache');

    session_start();

    include $_SERVER['DOCUMENT_ROOT'] . '/gleefulsync/config.php';
    include DB_CONNECTION_LINK;

    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (empty($data['attendees']) || !is_array($data['attendees'])) {
        echo json_encode(['success' => false, 'message' => 'No attendees provided']);
        exit;
    }

    if (empty($data['platform'])) {
        echo json_encode(['success' => false, 'message' => 'No platform provided']);
        exit;
    }

    if (empty($_SESSION['batch_id'])) {
        echo json_encode(['success' => false, 'message' => 'Batch ID not available.']);
        exit;
    }

    $platformId = (int)$data['platform'];
    $batchId = (int)$_SESSION['batch_id'];

    try {
        $pdo->beginTransaction();

        $query = "INSERT INTO attendance_tbl (locale_id, member_id, batch_id, platform_id) VALUES ('1', :member_id, :batch_id, :platform_id)";
        $stmt = $pdo->prepare($query);
        
        foreach ($data['attendees'] as $memberId) {
            $stmt->execute([
                ':member_id' => $memberId, 
                ':batch_id' => $batchId, 
                ':platform_id' => $platformId 
            ]);
        }

        // logs the activity
        $logDescription = "For " . count($data['attendees']) . " attendees.";
        
        $query2 = "INSERT INTO logs (description, created_by) VALUES (:description, 1)";
        $stmt2 = $pdo->prepare($query2);
        $stmt2->execute([':description' => $logDescription]);

        $pdo->commit();
    
        echo json_encode(['success' => true, 'message' => 'Attendance submitted successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to submit attendance: ' . $e->getMessage()]);
    }
?>