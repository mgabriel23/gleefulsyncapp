<?php
    header('Content-Type: application/json');
    header('Cache-Control: no-cache');

    include $_SERVER['DOCUMENT_ROOT'] . '/gleefulsync/config.php';
    include DB_CONNECTION_LINK;

    $query = "
        SELECT 
            YEAR(a.date_created) AS year, 
            DATE_FORMAT(a.date_created, '%M') AS month_name, 
            DATE_FORMAT(a.date_created, '%m') AS month_no  
        FROM 
            attendance_tbl a 
        GROUP BY 
            year, month_name, month_no 
        ORDER BY 
            year DESC, month_no DESC 
        LIMIT 5
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $monthly_reports_lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($monthly_reports_lists as $monthly_reports_list) {
        $data[] = [
            'year' => htmlspecialchars($monthly_reports_list['year']), 
            'month_name' => htmlspecialchars($monthly_reports_list['month_name']), 
            'month_no' => htmlspecialchars($monthly_reports_list['month_no'])
        ];
    }

    echo json_encode($data);
?>