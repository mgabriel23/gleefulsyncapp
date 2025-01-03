<?php
    header('Content-Type: application/json');
    header('Cache-Control: no-cache');

    include $_SERVER['DOCUMENT_ROOT'] . '/gleefulsync/config.php';
    include DB_CONNECTION_LINK;

    $query = "
        SELECT batches_tbl.id AS batch_id, 
            gatherings_tbl.title AS gathering_type, 
            batches_tbl.start_time AS gathering_time, 
            DATE(attendance_tbl.date_created) AS report_date 
        FROM attendance_tbl 
            INNER JOIN batches_tbl ON attendance_tbl.batch_id = batches_tbl.id 
            INNER JOIN gatherings_tbl ON batches_tbl.gathering_id = gatherings_tbl.id 
        GROUP BY batches_tbl.start_time 
        ORDER BY attendance_tbl.date_created DESC, 
            batches_tbl.start_time DESC 
        LIMIT 5
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $daily_reports_lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($daily_reports_lists as $daily_reports_list) {
        $time = DateTime::createFromFormat('H:i:s', $daily_reports_list['gathering_time']);
        $formattedTime = $time->format('h:i a');
    
        $data[] = [
            'batch_id' => htmlspecialchars($daily_reports_list['batch_id']), 
            'gathering_type' => htmlspecialchars($daily_reports_list['gathering_type']), 
            'gathering_time' => htmlspecialchars($formattedTime), 
            'report_date' => htmlspecialchars($daily_reports_list['report_date'])
        ];
    }
    
    echo json_encode($data);
?>