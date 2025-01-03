<?php
    date_default_timezone_set('Asia/Manila');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include $_SERVER['DOCUMENT_ROOT'] . '/gleefulsync/config.php';
    require_once MPDF_LIBRARY_LINK;
    include DB_CONNECTION_LINK;

    // Retrieve parameters from the URL
    $batch_id = isset($_GET['batch_id']) ? htmlspecialchars($_GET['batch_id']) : 'N/A';
    $gathering_type = isset($_GET['gathering_type']) ? htmlspecialchars($_GET['gathering_type']) : 'N/A';
    $gathering_time = isset($_GET['gathering_time']) ? htmlspecialchars($_GET['gathering_time']) : 'N/A';
    $report_date = isset($_GET['report_date']) ? htmlspecialchars($_GET['report_date']) : 'N/A';

    // Parse the report_date and gathering_time
    $report_datetime = DateTime::createFromFormat('Y-m-d h:i a', "$report_date $gathering_time");
    $gathering_time = DateTime::createFromFormat('h:i a', "$gathering_time");

    // If parsing fails, use the current timestamp as a fallback
    if (!$report_datetime) {
        $report_datetime = new DateTime();
    }

    $timestamp = $report_datetime->format('M j Y');
    $timestamp = str_replace(':', '', $timestamp); // Remove the colon from the time for filename compatibility

    // Generate the filename
    $currentDate = date("m-d His"); 
    $filename = "{$gathering_type} {$timestamp} ({$currentDate}).pdf";

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
            table thead tr th { color: white; text-align: left; }
            tr:nth-child(odd) { background-color: #F0F0F0; }
            tr:nth-child(even) { background-color: #FFFFFF; }
            table .tbl-ctr { width: 15%; text-align: center; }
        </style>
    ';

    // Prepare the HTML content
    $html .= "
        <div class='container'>
            <h1>$gathering_type</h1>
            <h3>" . $report_datetime->format('F j, Y') . "</h3>
            <p>".$gathering_time->format('h:i a')."</p>
    ";

    // table ctr var to determine table position
    $table_ctr = 1;

    // first retrieve and loop for platforms table
    $query = "SELECT id, short_name FROM platforms_tbl";
    $stmt  = $pdo->prepare($query);
    $stmt->execute();
    $platforms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($platforms as $platform) {
        $html .= '
            <div style="width: 49%; float: '.($table_ctr % 2 == 0 ? "right" : "left").';">
                <h3>'. htmlspecialchars(strtoupper($platform["short_name"])) .'</h3>
                <br>
                <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th class="tbl-ctr">#</th>
                            <th>NAME</th>
                        </tr>
                    </thead>
                    <tbody>';

                        $query2 = "
                            SELECT b.name, a.batch_id FROM attendance_tbl a 
                                INNER JOIN members_tbl b ON a.member_id = b.id 
                            WHERE DATE(a.date_created) = :report_date AND platform_id = :platform 
                        ";
                            
                        $stmt2 = $pdo->prepare($query2);
                        $stmt2->bindParam(':report_date', $report_date, PDO::PARAM_STR);
                        $stmt2->bindParam(':platform', $platform["id"], PDO::PARAM_INT);
                        $stmt2->execute();
                        $members = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                        $attendees_ctr = 1;

                        if($members) {
                            foreach ($members as $member) {
                                $html .= '
                                    <tr>
                                        <td  class="tbl-ctr">'. $attendees_ctr .'</td>
                                        <td>'. htmlspecialchars($member["name"]) .'</td>
                                    </tr>
                                ';
    
                                $attendees_ctr++;
                            }
                        } else {
                            $html .= '
                                <tr><td colspan="2">No attendees found on this platform.</td></tr>
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

    $html .= '
        </div>
    ';

    // Create the PDF
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($html);
    $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
?>