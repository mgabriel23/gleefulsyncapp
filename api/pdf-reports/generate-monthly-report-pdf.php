<?php
    date_default_timezone_set('Asia/Manila');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include $_SERVER['DOCUMENT_ROOT'] . '/gleefulsync/config.php';
    require_once MPDF_LIBRARY_LINK;
    include DB_CONNECTION_LINK;
    
    $year = isset($_GET['year']) ? htmlspecialchars($_GET['year']) : 'N/A';
    $month_name = isset($_GET['month_name']) ? htmlspecialchars($_GET['month_name']) : 'N/A';
    $month_no = isset($_GET['month_no']) ? htmlspecialchars($_GET['month_no']) : 'N/A';

    // Generate the filename
    $currentDate = date("m-d His");
    $filename = "{$month_name} {$year} ({$currentDate}).pdf";

    // css styles
    $html = '
        <style>
            body { margin: 0; padding: 0; }
            h1, h3 { margin: 0px; padding: 0; text-align: center; }
            p { margin: 8px; text-align: center; }
            h1 { color: #113f67; /* Dark blue */ }
            h3 { color: #16a085; /* Teal */ }
            p { color: #34495e; margin-bottom: 32px; /* Dark gray */ }
            table { width: 100%; font-family: Arial; font-size: 12px; }
            table, thead tr th, tbody tr td { border: 2px solid white; }
            table thead tr { background-color: #113f67; }
            table thead tr th { color: white; }
            tr:nth-child(odd) { background-color: #F0F0F0; }
            tr:nth-child(even) { background-color: #FFFFFF; }
            table .tbl-ctr { width: 15%; text-align: center; }
            table tbody tr.header td { background-color: #113f67; color: white; font-weight: bold; }
            .text-center { text-align: center; }
        </style>
    ';

    $html .= "
        <div class='container'>
            <h1>$year</h1>
            <h3 style='margin-bottom: 32px;'>$month_name</h3>
    ";

    // table ctr var to determine table position
    $table_ctr = 1;

    // first retrieve and loop for the month weeks
    $query = "
        SELECT 
            MONTH(a.date_created) AS month_number, 
            WEEK(a.date_created, 1) AS week_number, 
            CASE 
                WHEN MONTH(DATE_SUB(a.date_created, INTERVAL WEEKDAY(a.date_created) DAY)) = MONTH(DATE_ADD(DATE_SUB(a.date_created, INTERVAL WEEKDAY(a.date_created) DAY), INTERVAL 6 DAY))
                THEN CONCAT(
                    DATE_FORMAT(DATE_SUB(a.date_created, INTERVAL WEEKDAY(a.date_created) DAY), '%M %d'), 
                    ' - ', 
                    DATE_FORMAT(DATE_ADD(DATE_SUB(a.date_created, INTERVAL WEEKDAY(a.date_created) DAY), INTERVAL 6 DAY), '%d')
                )
                ELSE CONCAT(
                    DATE_FORMAT(DATE_SUB(a.date_created, INTERVAL WEEKDAY(a.date_created) DAY), '%M %d'), 
                    ' - ', 
                    DATE_FORMAT(DATE_ADD(DATE_SUB(a.date_created, INTERVAL WEEKDAY(a.date_created) DAY), INTERVAL 6 DAY), '%M %d')
                )
            END AS week_range 
        FROM 
            attendance_tbl a 
        WHERE 
            MONTH(a.date_created) = :month_no 
        GROUP BY 
            month_number, week_number, week_range
    ";

    $stmt  = $pdo->prepare($query);
    $stmt->bindParam(':month_no', $month_no, PDO::PARAM_INT);
    $stmt->execute();
    $weeks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($weeks as $week) {
        $html .= '
            <div style="width: 49%; float: '.($table_ctr % 2 == 0 ? "right" : "left").';">
                <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th colspan="4">'. htmlspecialchars(strtoupper($week["week_range"])) .'</th>
                        </tr>
                    </thead>
                    <tbody>';
                        $query2 = "SELECT a.id, a.title FROM gatherings_tbl a";
                            
                        $stmt2 = $pdo->prepare($query2);
                        $stmt2->execute();
                        $gatherings = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($gatherings as $gathering) {
                            $html .= '
                                <tr class="header">
                                    <td>'. htmlspecialchars($gathering["title"]) .'</td>
                                    <td class="txt-center">LIVE</td>
                                    <td class="txt-center">VIEWING</td>
                                    <td class="txt-center">TOTAL</td>
                                </tr>
                            ';

                            $week_number = (int)$week["week_number"];
                            $gathering_id = (int)$gathering["id"];

                            $query3 = "
                                SELECT 
                                    a.id, 
                                    a.short_name, 
                                    (SELECT COUNT(*) FROM attendance_tbl att INNER JOIN batches_tbl btch ON att.batch_id = btch.id WHERE att.platform_id = a.id AND btch.is_live = 1 AND btch.gathering_id = $gathering_id AND WEEK(att.date_created, 1) = $week_number) AS live_total, 
                                    (SELECT COUNT(*) FROM attendance_tbl att INNER JOIN batches_tbl btch ON att.batch_id = btch.id WHERE att.platform_id = a.id AND btch.is_live != 1 AND btch.gathering_id = $gathering_id AND WEEK(att.date_created, 1) = $week_number) AS viewing_total 
                                FROM platforms_tbl a 
                            ";
                        
                            $stmt3 = $pdo->prepare($query3);
                            $stmt3->execute();
                            $platforms_attendees = $stmt3->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($platforms_attendees as $platforms_attendee) {
                                $total = $platforms_attendee["live_total"] + $platforms_attendee["viewing_total"];
                                $html .= '
                                    <tr>
                                        <td>'. htmlspecialchars($platforms_attendee["short_name"]) .'</td>
                                        <td class="text-center">'. htmlspecialchars($platforms_attendee["live_total"]) .'</td>
                                        <td class="text-center">'. htmlspecialchars($platforms_attendee["viewing_total"]) .'</td>
                                        <td class="text-center">'. $total .'</td>
                                    </tr>
                                ';
                            }
                        }
        $html .= '
                    </tbody>
                </table>
            </div>
        ';

        // add break line every 2 tables
        if($table_ctr % 2 == 0) {
            $html .= '<div style="clear: both;"></div>';
            $html .= '<br>';
        }

        $table_ctr++;
    }

    $html .= '
        </div>
    ';

    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($html);
    $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
?>