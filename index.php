<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title>GleefulSyncApp - Locale Attendance Monitoring</title>
    <link rel="stylesheet" type="text/css" href="styles/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="fonts/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="styles/style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="manifest" href="_manifest.json">
    <meta id="theme-check" name="theme-color" content="#FFFFFF">
    <link rel="apple-touch-icon" sizes="180x180" href="app/icons/icon-192x192.png">
</head>

<body class="theme-dark">

    <div id="preloader"><div class="spinner-border color-highlight" role="status"></div></div>

    <!-- Page Wrapper-->
    <div id="page">
        <!-- Menu Bar -->
        <div id="footer-bar" class="footer-bar-1 footer-bar-detached">
            <a href="#metrics"><i class="bi bi-bar-chart-fill"></i><span>Metrics</span></a>
            <a href="#recents"><i class="bi bi-file-earmark-text-fill"></i><span>Recents</span></a>

            <a href="#" class="circle-nav-2">
                <i class="bi bi-qr-code"></i>
                <span>QR</span>
            </a>

            <a href="#reports"><i class="bi bi-file-earmark-arrow-down-fill"></i><span>Reports</span></a>
            <a href="#certificate"><i class="bi bi-patch-check-fill"></i><span>Certif.</span></a>
        </div>

        <div class="page-content footer-clear">
            <!-- Page Header-->
            <div id="metrics" class="pt-3">
                <div class="page-title d-flex">
                    <div class="align-self-center me-auto">
                        <p class="color-highlight header-date"></p>
                        <h1>Welcome</h1>
                    </div>
                    <div class="align-self-center ms-auto">
                        <a href="#"
                        id="notif-btn"
                        class="icon color-white shadow-bg shadow-bg-xs rounded-m">
                            <i class="bi bi-bell-fill font-17"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- active batch part -->
            <div class="pt-2 pb-4 text-center" id="batch-info">
                <h3 style="margin-bottom: 0;" id="gathering-type">Loading...</h3>
                <h5 id="day-name"></h5>
                <h4 class="color-highlight" id="batch-start-time"></h4>
            </div>

            <!-- attendance current batch summary part -->
            <div class="content" style="margin-top: 0; margin-bottom: 32px;" id="platforms">
                <div class="d-flex text-center">
                    <div class="m-auto">
                        <a href="#" data-bs-toggle="offcanvas" data-bs-target="#locale-attendees" class="icon icon-xxl rounded-m bg-theme shadow-m">
                            <i class="font-28 color-yellow-dark bi bi-house-fill"></i>
                            <em class="badge bg-yellow-dark color-white font-12" id="locale-badge" style="border-radius: 8px;">0</em>
                        </a>
                        <h6 class="font-13 opacity-80 font-500 mb-0 pt-2">Locale</h6>
                    </div>
                    <div class="m-auto">
                        <a href="#" data-bs-toggle="offcanvas" data-bs-target="#youtube-attendees" class="icon icon-xxl rounded-m bg-theme shadow-m">
                            <i class="font-28 color-red-dark bi bi-youtube"></i>
                            <em class="badge bg-red-dark color-white font-12" id="youtube-badge" style="border-radius: 8px;">0</em>
                        </a>
                        <h6 class="font-13 opacity-80 font-500 mb-0 pt-2">Youtube</h6>
                    </div>
                    <div data-bs-toggle="offcanvas" data-bs-target="#zoom-attendees" class="m-auto">
                        <a href="#" class="icon icon-xxl rounded-m bg-theme shadow-m">
                            <i class="font-28 color-blue-dark bi bi-camera-video-fill"></i>
                            <em class="badge bg-blue-dark color-white font-12" id="zoom-badge" style="border-radius: 8px;">0</em>
                        </a>
                        <h6 class="font-13 opacity-80 font-500 mb-0 pt-2">Zoom</h6>
                    </div>
                    <div data-bs-toggle="offcanvas" data-bs-target="#others-attendees" class="m-auto">
                        <a href="#" class="icon icon-xxl rounded-m bg-theme shadow-m">
                            <i class="font-28 color-brown-dark bi bi-three-dots"></i>
                            <em class="badge bg-brown-dark color-white font-12" id="others-badge" style="border-radius: 8px;">0</em>
                        </a>
                        <h6 class="font-13 opacity-80 font-500 mb-0 pt-2">Others</h6>
                    </div>
                </div>
            </div>
            <!-- attendance current batch summary part end -->

            <!-- Recent Activity Title -->
            <div id="recents" class="content my-0 mt-n2 px-1">
                <div class="d-flex">
                    <div class="align-self-center">
                        <h3 class="font-16 mb-2">Recent Activity</h3>
                    </div>
                    <div class="align-self-center ms-auto">
                        <a href="#" class="font-12 pt-1">View All</a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity History -->
            <div class="card card-style">
                <div class="content" id="logs-container"></div>
            </div>
            <!-- Recent Activity History End -->

            <!-- Reports Title -->
            <div class="content my-0 mt-n2 px-1">
                <div class="d-flex">
                    <div class="align-self-center">
                        <h3 class="font-16 mb-2">Reports</h3>
                    </div>
                </div>
            </div>

            <!-- reports content -->
            <div id="reports" class="card card-style">
                <div class="content">
                    <div class="tabs tabs-pill" id="tab-group-1">
                        <div class="tab-controls rounded-m p-1 overflow-visible">
                            <a class="font-13 rounded-m shadow-bg shadow-bg-s" data-bs-toggle="collapse" href="#tab-1" aria-expanded="true" id="day-tab-btn">Day</a>
                            <a class="font-13 rounded-m shadow-bg shadow-bg-s" data-bs-toggle="collapse" href="#tab-2" aria-expanded="false" id="weekly-tab-btn">Weekly</a>
                            <a class="font-13 rounded-m shadow-bg shadow-bg-s" data-bs-toggle="collapse" href="#tab-3" aria-expanded="false" id="monthly-tab-btn">Montly</a>
                        </div>
                        <div class="mt-3"></div>
                        <!-- daily report content -->
                        <div class="collapse show" id="tab-1" data-bs-parent="#tab-group-1">
                            <div class="divider my-2 opacity-50"></div>
                            <div id="daily-reports-list">
                                <span class="text-white">1</span>
                            </div>
                        </div>
                        <!-- Tab Group 2 -->
                        <div class="collapse" id="tab-2" data-bs-parent="#tab-group-1">
                            <div class="divider my-2 opacity-50"></div>
                            <div id="weekly-reports-list">
                                <span class="text-white">2</span>
                            </div>
                        </div>
                        <!-- Tab Group 3 -->
                        <div class="collapse" id="tab-3" data-bs-parent="#tab-group-1">
                            <div class="divider my-2 opacity-50"></div>
                            <div id="monthly-reports-list">
                                <span class="text-white">3</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- reports content end -->

            <!-- Certificate Title -->
            <div class="content my-0 mt-n2 px-1">
                <div class="d-flex">
                    <div class="align-self-center">
                        <h3 class="font-16 mb-2">Certificate</h3>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <!-- <div id="certificate" class="chart-section mt-1 mb-1">
                <div class="chart-container mx-auto" style="max-width:320px; max-height:320px;">
                    <div class="chart mx-auto no-click" id="chart-activity"></div>
                </div>
            </div> -->

            <!-- certificate content -->
            <div id="certificate" class="card card-style">
                <div class="content" style="margin-top: 0;">
                    <div class="divider my-2 opacity-50"></div>
                    <form action="">
                        <div class="form-custom form-label form-icon mb-3">
                            <i class="bi bi-person font-13"></i>
                            <select class="form-select rounded-xs color-theme" id="members-select" aria-label="Select member"></select>
                            <label for="members-select" class="color-theme">Select member</label>
                            <div class="valid-feedback">HTML5 does not offer Dates Field Validation!<!-- text for field valid--></div>
                        </div>
                        <span class="btn btn-full bg-blue-dark rounded-xs text-uppercase font-700 w-100 btn-s mt-4" id="generate-cert-btn">
                            Generate Certificate
                        </span>
                    </form>
                </div>
            </div>
            <!-- certificate content end -->`

            <div id="pdf-preview"></div>
        </div>

        <!-- modal / pop-ups part -->
        <div id="add-attendees" data-menu-load="api/templates/add-attendees.php"
            class="offcanvas offcanvas-bottom offcanvas-detached rounded-m">
        </div>

        <div id="locale-attendees" data-menu-load="api/templates/locale-attendees.php"
            class="offcanvas offcanvas-bottom offcanvas-detached rounded-m">
        </div>

        <div id="youtube-attendees" data-menu-load="api/templates/youtube-attendees.php"
            class="offcanvas offcanvas-bottom offcanvas-detached rounded-m">
        </div>

        <div id="zoom-attendees" data-menu-load="api/templates/zoom-attendees.php"
            class="offcanvas offcanvas-bottom offcanvas-detached rounded-m">
        </div>

        <div id="others-attendees" data-menu-load="api/templates/others-attendees.php"
            class="offcanvas offcanvas-bottom offcanvas-detached rounded-m">
        </div>
    </div>

    <script src="scripts/bootstrap.min.js"></script>
    <script src="scripts/custom.js"></script>

    <script>
        console.log("DOM fully loaded and parsed.");

        let preventSSEClose = true;

        const source = new EventSource('api/sse/updates.php');

        source.onmessage = function (event) {
            try {
                // Parse the incoming JSON data
                const data = JSON.parse(event.data);

                const counts = data.attendance_counts || {};
                document.getElementById('locale-badge').innerText = counts.locale_count || '0';
                document.getElementById('youtube-badge').innerText = counts.youtube_count || '0';
                document.getElementById('zoom-badge').innerText = counts.zoom_count || '0';
                document.getElementById('others-badge').innerText = counts.others_count || '0';

                const logsContainer = document.getElementById('logs-container');
                logsContainer.innerHTML = ''; // Clear previous logs

                const logs = data.recent_logs || [];
                if (logs.length === 0) {
                    logsContainer.innerHTML = '<p class="pt-1 mb-n1">No records to show.</p>';
                } else {
                    logs.forEach(log => {
                        const logCard = `
                            <a href="#" class="d-flex py-1">
                                <div class="align-self-center ps-1">
                                    <h5 class="pt-1 mb-n1">Attendance</h5>
                                    <p class="mb-0 font-11 opacity-50">${log.description || 'No description available'}</p>
                                </div>
                                <div class="align-self-center ms-auto text-end">
                                    <h4 class="pt-1 mb-n1 color-blue-dark">${formatTime(log.date_created)}</h4>
                                    <p class="mb-0 font-11">Sis Irma Emperado</p>
                                </div>
                            </a>
                            <div class="divider my-2 opacity-50"></div>
                        `;
                        logsContainer.innerHTML += logCard;
                    });
                }

            } catch (error) {
                console.error('Error parsing SSE data:', error);
            }
        };

        function formatTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
        }

        // window.addEventListener('beforeunload', function () {
        //     if (!preventSSEClose) {
        //         source.close(); // Close the SSE connection only if not prevented
        //         console.log("SSE connection closed.");
        //     } else {
        //         preventSSEClose = false;
        //         console.log("SSE connection preserved for PDF download.");
        //     }
        // });
        
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'hidden') {
                if (!preventSSEClose) {
                    source.close(); // Close the SSE connection only if not prevented
                    console.log("SSE connection closed.");
                } else {
                    preventSSEClose = false;
                    console.log("SSE connection preserved for PDF download.");
                }
            }
        });

        async function loadReportsList(url, containerId, mapReportToHTML, emptyMessage) {
            try {
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`Network error: ${response.statusText}`);
                }

                const data = await response.json();
                console.log(`Loading reports from ${url}`);

                const contentsDiv = document.getElementById(containerId);
                contentsDiv.innerHTML = '';

                if (data.length === 0) {
                    contentsDiv.innerHTML = `<p>${emptyMessage}</p>`;
                    return;
                }

                data.forEach(report => {
                    const reportElement = document.createElement('a');
                    reportElement.href = "#";
                    reportElement.className = "d-flex py-1";
                    reportElement.innerHTML = mapReportToHTML(report);
                    contentsDiv.appendChild(reportElement);
                });

                console.log(`Successfully loaded reports from ${url}`);
            } catch (error) {
                console.error(`Error fetching reports from ${url}:`, error);
            }
        }

        function getPDFAPIGeneratorLink(type) {
            switch (type) {
                case 'daily':
                    return `api/pdf-reports/generate-daily-report-pdf.php`;
                case 'weekly':
                    return `api/pdf-reports/generate-weekly-report-pdf.php`;
                case 'monthly':
                    return `api/pdf-reports/generate-monthly-report-pdf.php`;
                case 'certificate':
                    return `api/pdf-reports/generate-certificate-report-pdf.php`;
                default:
                    console.error('Unknown report type:', type);
                    return null;
            }
        }

        function getPDFReportParams(type, report) {
            let params = {};
            switch (type) {
                case 'daily':
                    params = {
                        batch_id: report.batch_id,
                        gathering_type: report.gathering_type,
                        gathering_time: report.gathering_time,
                        report_date: report.report_date
                    };
                    break;
                case 'weekly':
                    params = {
                        year: report.year,
                        week_no: report.week_no,
                        week_range: report.week_range
                    };
                    break;
                case 'monthly':
                    params = {
                        year: report.year,
                        month_name: report.month_name,
                        month_no: report.month_no
                    };
                    break;
                case 'certificate':
                    params = {
                        member_id: report.id,
                        member_name: report.name
                    };
                    break;
                default:
                    console.error('Unknown report type:', type);
                    return null;
            }
            return params;
        }

        function generatePDFReport(type, json) {
            preventSSEClose = true;

            try {
                const report = JSON.parse(atob(json)); // Decode and parse JSON

                if (!report) {
                    throw new Error('Failed to parse the report data.');
                }

                let apiUrl = getPDFAPIGeneratorLink(type);

                if (!apiUrl) {
                    throw new Error('Invalid or missing API URL.');
                }

                let params = getPDFReportParams(type, report);

                if (!params) {
                    throw new Error('Failed to generate report parameters.');
                }

                const queryParams = new URLSearchParams(params);
                console.log("Generating PDF Report: ", params);
                
                window.location.href = `${apiUrl}?${queryParams.toString()}`;
            } catch (error) {
                console.error(`Error generating PDF report: ${error.message}`);
            }
        }

        function dailyReportHTML(report) {
            return `
                <div class="align-self-center ps-1">
                    <h5 class="pt-1 mb-n1">${report.gathering_type}</h5>
                    <p class="mb-0 font-11 opacity-70">${report.gathering_time}</p>
                </div>
                <div class="align-self-center ms-auto text-end">
                    <span class="btn btn-xxs gradient-green shadow-bg shadow-bg-xs" 
                        onclick="generatePDFReport('daily', '${btoa(JSON.stringify(report))}')">
                        Save
                    </span>
                </div>
            `;
        }

        function weeklyReportHTML(report) {
            return `
                <div class="align-self-center ps-1">
                    <h5 class="pt-1 mb-n1">${report.week_range}</h5>
                    <p class="mb-0 font-11 opacity-70">PM / WS / TG</p>
                </div>
                <div class="align-self-center ms-auto text-end">
                    <span class="btn btn-xxs gradient-green shadow-bg shadow-bg-xs" 
                        onclick="generatePDFReport('weekly', '${btoa(JSON.stringify(report))}')">
                        Save
                    </span>
                </div>
            `;
        }

        function monthlyReportHTML(report) {
            return `
                <div class="align-self-center ps-1">
                    <h5 class="pt-1 mb-n1">${report.month_name}</h5>
                    <p class="mb-0 font-11 opacity-70">PM / WS / TG</p>
                </div>
                <div class="align-self-center ms-auto text-end">
                    <span class="btn btn-xxs gradient-green shadow-bg shadow-bg-xs" 
                        onclick="generatePDFReport('monthly', '${btoa(JSON.stringify(report))}')">
                        Save
                    </span>
                </div>
            `;
        }

        // Load daily reports by default
        loadReportsList(
            'api/json/daily-reports-list.php',
            'daily-reports-list',
            dailyReportHTML,
            'No records found for today.'
        );

        // Load weekly reports on tab click
        document.getElementById('weekly-tab-btn').addEventListener('click', function (event) {
            event.preventDefault();
            loadReportsList(
                'api/json/weekly-reports-list.php',
                'weekly-reports-list',
                weeklyReportHTML,
                'No records found for this week.'
            );
        });

        // Load monthly reports on tab click
        document.getElementById('monthly-tab-btn').addEventListener('click', function (event) {
            event.preventDefault();
            loadReportsList(
                'api/json/monthly-reports-list.php',
                'monthly-reports-list',
                monthlyReportHTML,
                'No records found for this month.'
            );
        });

        document.addEventListener("DOMContentLoaded", function () {
            let platformID = 0; // variable for holding platform id when button is clicked
            const batchInfoUrl = "api/json/active_batch.php";

            fetch(batchInfoUrl)
                .then(response => response.json())
                .then(data => {
                    console.log("Active Batch: " + JSON.stringify(data));
                    if (data.length > 0) {  // Ensure data is not an empty array
                        document.getElementById("gathering-type").innerText = data[0].gathering_type || "Unknown Gathering Type";
                        document.getElementById("day-name").innerText = data[0].day_name || "Unknown Day";
                        document.getElementById("batch-start-time").innerText = data[0].start_time || "Unknown Start Time";
                    } else {
                        document.getElementById("gathering-type").innerText = "No Active Batch";
                        document.getElementById("day-name").innerText = "";
                        document.getElementById("batch-start-time").innerText = "";
                    }
                })
                .catch(error => {
                    console.error("Error fetching batch info:", error);
                    document.getElementById("gathering-type").innerText = "Error Loading Batch Info";
                    document.getElementById("day-name").innerText = "";
                    document.getElementById("batch-start-time").innerText = "";
                });

            // Select option loading for certificate
            const select = document.getElementById('members-select');
            const button = document.getElementById('generate-cert-btn');

            // Ensure loadMembersInSelect is defined globally
            window.loadMembersInSelect = function (select) {
                fetch('api/json/fetch_members.php')
                .then(response => response.json())
                .then(data => {
                    select.innerHTML = ''; // Clear existing options

                    if (data.error) {
                        console.error(data.error);
                        select.innerHTML = `<option disabled>Error loading members</option>`;
                        return;
                    }

                    data.forEach(member => {
                        const option = document.createElement('option');
                        option.value = member.id; // Set value to member's ID
                        option.textContent = member.name; // Set visible text to member's name
                        option.setAttribute('data-name', member.name); // Add a data-name attribute
                        select.appendChild(option);
                    });

                    // Trigger change event to update the button on page load with the first option
                    select.dispatchEvent(new Event('change'));
                })
                .catch(error => console.error('Error fetching members:', error));
            }

            // Call loadMembersInSelect
            loadMembersInSelect(select);

            // Listen for changes in the select dropdown
            select.addEventListener('change', function () {
                const selectedOption = select.options[select.selectedIndex];
                const value = selectedOption.value;
                const dataName = selectedOption.getAttribute('data-name');

                // Update button attributes
                button.setAttribute('data-value', value);
                button.setAttribute('data-name', dataName);
            });

            document.getElementById('generate-cert-btn').addEventListener('click', function () {
                const memberId = select.value;
                const memberName = select.options[select.selectedIndex].getAttribute('data-name');

                if (!memberId || !memberName) {
                    console.error("Member ID or Name is missing!");
                    return;
                }

                const report = {
                    id: memberId,
                    name: memberName
                };

                generatePDFReport('certificate', btoa(JSON.stringify(report)));
            });

            document.addEventListener('click', (event) => {
                if (event.target && event.target.id === 'submit-attendance-btn') {
                    const selectedAttendees = Array.from(document.querySelectorAll('.attendee-checkbox:checked'))
                        .map(checkbox => checkbox.value);

                    if (selectedAttendees.length === 0) {
                        alert("No attendees selected.");
                        return;
                    }

                    if (!platformID) {
                        alert("No category selected. Please select one before submitting.");
                        return;
                    }

                    // Submit attendance with selected value
                    fetch('api/save/submit_attendance.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            attendees: selectedAttendees,
                            platform: platformID // Pass selected value as parameter
                        }),
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert("Attendance submitted successfully!");
                                resetCheckboxes();
                                location.reload();
                            } else {
                                alert("Failed to submit attendance. Please try again.");
                            }
                        })
                        .catch(error => {
                            console.error("Error submitting attendance:", error);
                            alert("An error occurred. Please try again.");
                        });
                }
            });

            function resetCheckboxes() {
                document.querySelectorAll('.attendee-checkbox:checked').forEach(checkbox => {
                    checkbox.checked = false;
                });
            }

            // set platform id when the plaltform button is click
            document.querySelectorAll('#platforms a').forEach(anchor => {
                anchor.addEventListener('click', function () {
                    const text = this.nextElementSibling?.innerText || '';
                    switch (text.trim()) {
                        case 'Locale':
                            platformID = 1;
                            break;
                        case 'Youtube':
                            platformID = 2;
                            break;
                        case 'Zoom':
                            platformID = 3;
                            break;
                        case 'Others':
                            platformID = 4;
                            break;
                        default:
                            platformID = 0;
                    }

                    // Log or use the value
                    console.log(`Selected platform: ${platformID}`);
                });
            });
        });
    </script>
</body>