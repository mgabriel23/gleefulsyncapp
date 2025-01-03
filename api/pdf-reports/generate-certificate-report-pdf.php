<?php
    date_default_timezone_set('Asia/Manila');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include $_SERVER['DOCUMENT_ROOT'] . '/gleefulsync/config.php';
    require_once MPDF_LIBRARY_LINK;
    include DB_CONNECTION_LINK;

    // Retrieve parameters from the URL
    $year = date("Y");
    $member_id = isset($_GET['member_id']) ? htmlspecialchars($_GET['member_id']) : 'N/A';
    $member_name = isset($_GET['member_name']) ? htmlspecialchars($_GET['member_name']) : 'N/A';
    $endDate = date('Y-m-d', strtotime('next Sunday'));
    $startDate = date('Y-m-d', strtotime('-6 months'));
    
    // Generate the filename
    $currentDate = date("m-d His"); 
    $filename = "{$member_name} ({$currentDate}).pdf";
    
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
            .week-range-column { width: 60%; font-size: 12px; font-weight: normal; }
        </style>
    ';

    // Prepare the HTML content
    $html .= "
        <div class='container'>
            <h1>ATTENDANCE REPORT</h1>
            <h3>LAST 6 MONTHS OVERVIEW</h3>
            <h3 style='margin-bottom: 32px;'>".strtoupper($member_name)."</h3>
    ";

    // table ctr var to determine table position
    $table_ctr = 1;

    // variables for report summary
    $presents = 0;
    $absents = 0;
    $total = 0;

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
            a.date_created BETWEEN :start_date AND :end_date 
        GROUP BY 
            week_number, week_range 
    ";

    $stmt  = $pdo->prepare($query);
    $stmt->bindParam(':start_date', $startDate, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $endDate, PDO::PARAM_STR);
    $stmt->execute();
    $weeks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($weeks as $week) {
        $html .= '
            <div style="width: 49%; float: '.($table_ctr % 2 == 0 ? "right" : "left").';">
                <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th rowspan="2" class="week-range-column">'. htmlspecialchars(strtoupper($week["week_range"])) .'</th>
                            <th class="text-center">WS</th>
                            <th class="text-center">TG</th>
                            <th class="text-center">PM</th>
                        </tr>
                    </thead>
                    <tbody>';
                        $query2 = "
                            SELECT b.name, 
                                (SELECT COUNT(*) FROM attendance_tbl a INNER JOIN batches_tbl b ON a.batch_id = b.id WHERE YEAR(a.date_created) = {$year} AND WEEK(a.date_created, 1) = {$week["week_number"]} AND a.member_id = gm.member_id AND b.gathering_id = '2') AS ws_attendance_flag, 
                                (SELECT COUNT(*) FROM attendance_tbl a INNER JOIN batches_tbl b ON a.batch_id = b.id WHERE YEAR(a.date_created) = {$year} AND WEEK(a.date_created, 1) = {$week["week_number"]} AND a.member_id = gm.member_id AND b.gathering_id = '3') AS tg_attendance_flag, 
                                (SELECT COUNT(*) FROM attendance_tbl a INNER JOIN batches_tbl b ON a.batch_id = b.id WHERE YEAR(a.date_created) = {$year} AND WEEK(a.date_created, 1) = {$week["week_number"]} AND a.member_id = gm.member_id AND b.gathering_id = '1') AS pm_attendance_flag 
                            FROM group_members_tbl gm 
                            INNER JOIN members_tbl b ON gm.member_id = b.id 
                            WHERE gm.member_id = :member_id 
                        ";
                            
                        $stmt2 = $pdo->prepare($query2);
                        $stmt2->bindParam(':member_id', $member_id, PDO::PARAM_STR);
                        $stmt2->execute();
                        $members_attendances = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($members_attendances as $member_attendance) {
                            $ws_color_flag = $member_attendance["ws_attendance_flag"] == 1 
                                    ? "text-align: center; color: black;" 
                                    : "text-align: center; background-color: #A2A8d3; color: white;";

                            $tg_color_flag = $member_attendance["tg_attendance_flag"] == 1 
                                ? "text-align: center; color: black;" 
                                : "text-align: center; background-color: #A2A8d3; color: white;";
                                
                            $pm_color_flag = $member_attendance["pm_attendance_flag"] == 1 
                                ? "text-align: center; color: black;" 
                                : "text-align: center; background-color: #A2A8d3; color: white;";

                            $ws_status = $member_attendance["ws_attendance_flag"] == 1 ? "P" : "A";
                            $tg_status = $member_attendance["tg_attendance_flag"] == 1 ? "P" : "A";
                            $pm_status = $member_attendance["pm_attendance_flag"] == 1 ? "P" : "A";

                            $presents += ($ws_status == "P") + ($tg_status == "P") + ($pm_status == "P");
                            $absents += ($ws_status == "A") + ($tg_status == "A") + ($pm_status == "A");

                            $html .= '
                                <tr>
                                    <td style="'. $ws_color_flag .'">'. $ws_status .'</td>
                                    <td style="'. $tg_color_flag .'">'. $tg_status .'</td>
                                    <td style="'. $pm_color_flag .'">'. $pm_status .'</td>
                                </tr>
                            ';
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

    $total = $presents + $absents;

    // reports summary part
    $html .= '
        <div style="clear: both;"></div>
        <br/>
        <h2 style="font-family: Arial, sans-serif; color: #333;">Attendance Summary</h2>
        <span style="font-family: Arial, sans-serif; font-size: 14px; color: #555;">
            <strong>Total Present:</strong> '.$presents.' out of '.$total.'
        </span>
        <br/>
        <span style="font-family: Arial, sans-serif; font-size: 14px; color: #555;">
            <strong>Total Absent:</strong> '.$absents.' out of '.$total.'
        </span>
    ';

    $html .= '
        </div>
    ';

    // Create the PDF
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($html);
    $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
?>