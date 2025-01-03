<!-- menu-size will be the dimension of your menu. If you set it to smaller than your content it will scroll-->
<div class="menu-size" style="height:365px;">
    <div class="d-flex mx-3 mt-3 mb-3 py-1">
        <div class="align-self-center">
            <h1 class="mb-0">Zoom</h1>
        </div>
        <div class="align-self-center ms-auto">
            <div data-bs-toggle="offcanvas" data-bs-target="#add-attendees" class="m-auto">
                <span class="btn btn-xxs gradient-blue shadow-bg shadow-bg-xs">Add Attendee</span>
            </div>
        </div>
    </div>
    <div class="content mt-0">
        <?php
            include $_SERVER['DOCUMENT_ROOT'] . '/gleefulsync/config.php';
            include DB_CONNECTION_LINK;

            $query = '
                SELECT groups_tbl.group_no AS group_no, 
                    members_tbl.name AS member_name 
                FROM attendance_tbl 
                    INNER JOIN locales_tbl ON attendance_tbl.locale_id = locales_tbl.id 
                    INNER JOIN members_tbl ON attendance_tbl.member_id = members_tbl.id 
                    INNER JOIN group_members_tbl ON members_tbl.id = group_members_tbl.member_id 
                    INNER JOIN groups_tbl ON group_members_tbl.group_id = groups_tbl.id 
                    INNER JOIN batches_tbl ON attendance_tbl.batch_id = batches_tbl.id 
                    INNER JOIN platforms_tbl ON attendance_tbl.platform_id = platforms_tbl.id 
                WHERE platforms_tbl.short_name = "Zoom" 
                    AND CURRENT_TIME() BETWEEN batches_tbl.start_time AND batches_tbl.end_time 
                    AND batches_tbl.day = (WEEKDAY(CURRENT_DATE())) 
                    AND DATE(attendance_tbl.date_created) = CURRENT_DATE() 
                    ORDER BY members_tbl.id
            ';

            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $zoom_attendees = $stmt->fetchAll();
        ?>
            <div class="table-responsive">
                <table class="table color-theme mb-2">
                    <thead>
                        <tr>
                        <th class="border-fade-blue" scope="col" style="width: 30%;">Group #</th>
                        <th class="border-fade-blue" scope="col">Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($zoom_attendees): ?>
                            <?php foreach ($zoom_attendees as $zoom_attendee): ?>
                                <tr class="border-fade-blue">
                                    <td><?php echo htmlspecialchars($zoom_attendee['group_no']); ?></td>
                                    <td><?php echo htmlspecialchars($zoom_attendee['member_name']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="text-center">No attendees found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
    </div>
</div>