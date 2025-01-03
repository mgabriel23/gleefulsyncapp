<?php
    header('Content-Type: application/json');
    header('Cache-Control: no-cache');

    include $_SERVER['DOCUMENT_ROOT'] . '/gleefulsync/config.php';
    include DB_CONNECTION_LINK;

    $query = "
        SELECT YEAR(attendance_tbl.date_created) AS year, 
            WEEK(attendance_tbl.date_created, 1) AS week_no, 
            MAX(CASE 
                WHEN MONTH(DATE_SUB(attendance_tbl.date_created, INTERVAL WEEKDAY(attendance_tbl.date_created) DAY)) = MONTH(DATE_ADD(DATE_SUB(attendance_tbl.date_created, INTERVAL WEEKDAY(attendance_tbl.date_created) DAY), INTERVAL 6 DAY))
                THEN CONCAT(
                    DATE_FORMAT(DATE_SUB(attendance_tbl.date_created, INTERVAL WEEKDAY(attendance_tbl.date_created) DAY), '%M %d'), 
                    ' - ', 
                    DATE_FORMAT(DATE_ADD(DATE_SUB(attendance_tbl.date_created, INTERVAL WEEKDAY(attendance_tbl.date_created) DAY), INTERVAL 6 DAY), '%d')
                )
                ELSE CONCAT(
                    DATE_FORMAT(DATE_SUB(attendance_tbl.date_created, INTERVAL WEEKDAY(attendance_tbl.date_created) DAY), '%M %d'), 
                    ' - ', 
                    DATE_FORMAT(DATE_ADD(DATE_SUB(attendance_tbl.date_created, INTERVAL WEEKDAY(attendance_tbl.date_created) DAY), INTERVAL 6 DAY), '%M %d')
                )
            END) AS week_range 
        FROM attendance_tbl 
        WHERE YEAR(attendance_tbl.date_created) = YEAR(CURRENT_DATE()) 
        GROUP BY year, week_no  
        ORDER BY week_no DESC 
        LIMIT 5
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $weekly_reports_lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($weekly_reports_lists as $weekly_reports_list) {
        $data[] = [
            'year' => htmlspecialchars($weekly_reports_list['year']), 
            'week_no' => htmlspecialchars($weekly_reports_list['week_no']), 
            'week_range' => htmlspecialchars($weekly_reports_list['week_range'])
        ];
    }

    echo json_encode($data);
?>