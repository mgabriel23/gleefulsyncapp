<?php
    header('Content-Type: application/json');
    header('Cache-Control: no-cache');

    session_start();

    include $_SERVER['DOCUMENT_ROOT'] . '/gleefulsync/config.php';
    include DB_CONNECTION_LINK;

    // Query to retrieve active batches
    $query = "
        SELECT 
            CASE 
                WHEN a.day = 0 THEN 'Monday' 
                WHEN a.day = 1 THEN 'Tuesday' 
                WHEN a.day = 2 THEN 'Wednesday' 
                WHEN a.day = 3 THEN 'Thursday' 
                WHEN a.day = 4 THEN 'Friday' 
                WHEN a.day = 5 THEN 'Saturday' 
                WHEN a.day = 6 THEN 'Sunday' 
            END AS day_name, 
            b.title AS gathering_type, 
            DATE_FORMAT(a.start_time, '%l:%i %p') AS formatted_start_time, 
            a.id AS batch_id 
            FROM batches_tbl a 
            INNER JOIN gatherings_tbl b ON a.gathering_id = b.id 
            WHERE CURRENT_TIME() BETWEEN a.start_time AND a.end_time 
            AND a.day = WEEKDAY(CURRENT_DATE())
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $active_batches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($active_batches as $active_batch) {
        $data[] = [
            'gathering_type' => htmlspecialchars($active_batch['gathering_type']), 
            'day_name' => htmlspecialchars($active_batch['day_name']), 
            'start_time' => htmlspecialchars($active_batch['formatted_start_time']) 
        ];

        $_SESSION['batch_id'] = $active_batch['batch_id'];
    }
    
    echo json_encode($data);
?>