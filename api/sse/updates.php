<?php
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Access-Control-Allow-Origin: *');

    include $_SERVER['DOCUMENT_ROOT'] . '/gleefulsync/config.php';
    include DB_CONNECTION_LINK;

    function fetchAttendanceCounts($pdo) {
        return [
            'locale_count' => $pdo->query("SELECT COUNT(*) FROM attendance_tbl a INNER JOIN locales_tbl b ON a.locale_id = b.id INNER JOIN members_tbl c ON a.member_id = c.id INNER JOIN batches_tbl d ON a.batch_id = d.id INNER JOIN platforms_tbl e ON a.platform_id = e.id INNER JOIN group_members_tbl f ON a.member_id = f.member_id WHERE e.short_name = 'Locale' AND CURRENT_TIME() BETWEEN d.start_time AND d.end_time AND d.day = (WEEKDAY(CURRENT_DATE())) AND DATE(a.date_created) = CURRENT_DATE() ORDER BY f.group_id ASC")->fetchColumn(),
            'youtube_count' => $pdo->query("SELECT COUNT(*) FROM attendance_tbl a INNER JOIN locales_tbl b ON a.locale_id = b.id INNER JOIN members_tbl c ON a.member_id = c.id INNER JOIN batches_tbl d ON a.batch_id = d.id INNER JOIN platforms_tbl e ON a.platform_id = e.id INNER JOIN group_members_tbl f ON a.member_id = f.member_id WHERE e.short_name = 'Youtube' AND CURRENT_TIME() BETWEEN d.start_time AND d.end_time AND d.day = (WEEKDAY(CURRENT_DATE())) AND DATE(a.date_created) = CURRENT_DATE() ORDER BY f.group_id ASC")->fetchColumn(),
            'zoom_count' => $pdo->query("SELECT COUNT(*) FROM attendance_tbl a INNER JOIN locales_tbl b ON a.locale_id = b.id INNER JOIN members_tbl c ON a.member_id = c.id INNER JOIN batches_tbl d ON a.batch_id = d.id INNER JOIN platforms_tbl e ON a.platform_id = e.id INNER JOIN group_members_tbl f ON a.member_id = f.member_id WHERE e.short_name = 'Zoom' AND CURRENT_TIME() BETWEEN d.start_time AND d.end_time AND d.day = (WEEKDAY(CURRENT_DATE())) AND DATE(a.date_created) = CURRENT_DATE() ORDER BY f.group_id ASC")->fetchColumn(),
            'others_count' => $pdo->query("SELECT COUNT(*) FROM attendance_tbl a INNER JOIN locales_tbl b ON a.locale_id = b.id INNER JOIN members_tbl c ON a.member_id = c.id INNER JOIN batches_tbl d ON a.batch_id = d.id INNER JOIN platforms_tbl e ON a.platform_id = e.id INNER JOIN group_members_tbl f ON a.member_id = f.member_id WHERE e.short_name = 'Others' AND CURRENT_TIME() BETWEEN d.start_time AND d.end_time AND d.day = (WEEKDAY(CURRENT_DATE())) AND DATE(a.date_created) = CURRENT_DATE() ORDER BY f.group_id ASC")->fetchColumn(),
        ];
    }

    // Fetch recent logs
    function fetchRecentLogs($pdo, $limit = 5) {
        $stmt = $pdo->prepare("SELECT description, date_created FROM logs ORDER BY date_created DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    while (true) {
        try {
            $counts = fetchAttendanceCounts($pdo);
            $logs = fetchRecentLogs($pdo);

            $data = [
                'attendance_counts' => $counts,
                'recent_logs' => $logs,
            ];
    
            echo "data: " . json_encode($data) . "\n\n";
    
            ob_flush();
            flush();
            
            sleep(10);
        } catch (Exception $e) {
            echo "data: {\"error\": \"An error occurred while fetching data.\"}\n\n";
            ob_flush();
            flush();
            break;
        }
    }
?>