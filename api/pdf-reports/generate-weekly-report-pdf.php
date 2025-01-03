<?php
    date_default_timezone_set('Asia/Manila');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include $_SERVER['DOCUMENT_ROOT'] . '/gleefulsync/config.php';
    require_once MPDF_LIBRARY_LINK;
    include DB_CONNECTION_LINK;
    
    $year = isset($_GET['year']) ? htmlspecialchars($_GET['year']) : 'N/A';
    $week_no = isset($_GET['week_no']) ? htmlspecialchars($_GET['week_no']) : 'N/A';
    $week_range = isset($_GET['week_range']) ? htmlspecialchars($_GET['week_range']) : 'N/A';

    // Generate the filename
    $currentDate = date("m-d His"); 
    $filename = "{$week_range}, {$year} ({$currentDate}).pdf";

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
            .members-ctr { background-color: #113f67; text-align: center; color: white; font-weight: bold; }
        </style>
    ';

    // Prepare the HTML content
    $html .= "
        <div class='container'>
            <h1>$year</h1>
            <h3 style='margin-bottom: 32px;'>$week_range</h3>
    ";

    // table ctr var to determine table position
    $table_ctr = 1;

    // first retrieve and loop for groups table
    $query = "SELECT id, group_no FROM groups_tbl";
    $stmt  = $pdo->prepare($query);
    $stmt->execute();
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($groups as $group) {
        $html .= '
            <div style="width: 49%; float: '.($table_ctr % 2 == 0 ? "right" : "left").';">
                <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th colspan="2">Group '. htmlspecialchars(strtoupper($group["group_no"])) .'</th>
                            <th class="text-center">WS</th>
                            <th class="text-center">TG</th>
                            <th class="text-center">PM</th>
                        </tr>
                    </thead>
                    <tbody>';

                        $query2 = "
                            SELECT b.name, 
                                (SELECT COUNT(*) FROM attendance_tbl a INNER JOIN batches_tbl b ON a.batch_id = b.id WHERE YEAR(a.date_created) = {$year} AND WEEK(a.date_created, 1) = {$week_no} AND a.member_id = gm.member_id AND b.gathering_id = '2') AS ws_attendance_flag, 
                                (SELECT COUNT(*) FROM attendance_tbl a INNER JOIN batches_tbl b ON a.batch_id = b.id WHERE YEAR(a.date_created) = {$year} AND WEEK(a.date_created, 1) = {$week_no} AND a.member_id = gm.member_id AND b.gathering_id = '3') AS tg_attendance_flag, 
                                (SELECT COUNT(*) FROM attendance_tbl a INNER JOIN batches_tbl b ON a.batch_id = b.id WHERE YEAR(a.date_created) = {$year} AND WEEK(a.date_created, 1) = {$week_no} AND a.member_id = gm.member_id AND b.gathering_id = '1') AS pm_attendance_flag 
                            FROM group_members_tbl gm 
                            INNER JOIN members_tbl b ON gm.member_id = b.id 
                            WHERE gm.group_id = :group_id 
                        ";
                            
                        $stmt2 = $pdo->prepare($query2);
                        $stmt2->bindParam(':group_id', $group["id"], PDO::PARAM_STR);
                        $stmt2->execute();
                        $members_attendances = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                        $attendees_ctr = 1;

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

                            $html .= '
                                <tr>
                                    <td class="members-ctr">'. $attendees_ctr .'</td>
                                    <td>'. htmlspecialchars($member_attendance["name"]) .'</td>
                                    <td style="'. $ws_color_flag .'">'. $ws_status .'</td>
                                    <td style="'. $tg_color_flag .'">'. $tg_status .'</td>
                                    <td style="'. $pm_color_flag .'">'. $pm_status .'</td>
                                </tr>
                            ';

                            $attendees_ctr++;
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