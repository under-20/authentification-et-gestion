<?php
// Include configuration file
require_once('../../config/config.php');

// Check if this is an AJAX request for chart data
if (isset($_GET['get_chart_data'])) {
    // Query to get role counts
    $stmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    $roleResults = $stmt->fetchAll();

    // Query to get sex counts
    $stmt = $pdo->query("SELECT sexe, COUNT(*) as count FROM users GROUP BY sexe");
    $sexResults = $stmt->fetchAll();

    // Prepare data for role chart
    $roleLabels = [];
    $roleData = [];
    $roleColors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'];

    foreach ($roleResults as $row) {
        $roleLabels[] = ucfirst($row['role']);
        $roleData[] = $row['count'];
    }

    // If no role data, show some default values
    if (empty($roleResults)) {
        $roleLabels = ['Admin', 'Client', 'Superadmin'];
        $roleData = [0, 0, 0];
    }

    // Prepare data for sex chart
    $sexLabels = [];
    $sexData = [];
    $sexColors = ['#4BC0C0', '#FF9F40']; // Different colors for homme/femme

    foreach ($sexResults as $row) {
        $sexLabels[] = ucfirst($row['sexe']);
        $sexData[] = $row['count'];
    }

    // If no sex data, show some default values
    if (empty($sexResults)) {
        $sexLabels = ['Homme', 'Femme'];
        $sexData = [0, 0];
    }

    // Return data as JSON
    header('Content-Type: application/json');
    echo json_encode([
        'roleLabels' => $roleLabels,
        'roleData' => $roleData,
        'roleColors' => array_slice($roleColors, 0, count($roleLabels)),
        'sexLabels' => $sexLabels,
        'sexData' => $sexData,
        'sexColors' => array_slice($sexColors, 0, count($sexLabels))
    ]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Statistics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .charts-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .chart-container {
            position: relative;
            height: 400px;
            width: 48%;
            min-width: 350px;
            margin-bottom: 20px;
        }
        .loading {
            text-align: center;
            padding: 50px;
            font-size: 18px;
            color: #666;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #36A2EB;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .chart-container {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>User Statistics</h1>
        <div class="charts-container">
            <div class="chart-container">
                <canvas id="rolesChart"></canvas>
                <div id="rolesLoading" class="loading">Loading roles data...</div>
            </div>
            <div class="chart-container">
                <canvas id="sexChart"></canvas>
                <div id="sexLoading" class="loading">Loading sex distribution data...</div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rolesCtx = document.getElementById('rolesChart');
            const sexCtx = document.getElementById('sexChart');
            const rolesLoading = document.getElementById('rolesLoading');
            const sexLoading = document.getElementById('sexLoading');
            
            // Hide the canvases initially, show loading messages
            rolesCtx.style.display = 'none';
            sexCtx.style.display = 'none';
            
            fetch(window.location.pathname + '?get_chart_data=1')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Hide loading messages, show charts
                    rolesLoading.style.display = 'none';
                    sexLoading.style.display = 'none';
                    rolesCtx.style.display = 'block';
                    sexCtx.style.display = 'block';
                    
                    // Create roles chart
                    new Chart(rolesCtx, {
                        type: 'pie',
                        data: {
                            labels: data.roleLabels,
                            datasets: [{
                                data: data.roleData,
                                backgroundColor: data.roleColors,
                                borderWidth: 1,
                                hoverOffset: 10
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        padding: 20,
                                        font: {
                                            size: 14
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.raw || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = Math.round((value / total) * 100);
                                            return `${label}: ${value} user(s) (${percentage}%)`;
                                        }
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Distribution of User Roles',
                                    font: {
                                        size: 16
                                    },
                                    padding: {
                                        top: 10,
                                        bottom: 20
                                    }
                                }
                            }
                        }
                    });

                    // Create sex distribution chart
                    new Chart(sexCtx, {
                        type: 'pie',
                        data: {
                            labels: data.sexLabels,
                            datasets: [{
                                data: data.sexData,
                                backgroundColor: data.sexColors,
                                borderWidth: 1,
                                hoverOffset: 10
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        padding: 20,
                                        font: {
                                            size: 14
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.raw || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = Math.round((value / total) * 100);
                                            return `${label}: ${value} user(s) (${percentage}%)`;
                                        }
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Distribution by Sex',
                                    font: {
                                        size: 16
                                    },
                                    padding: {
                                        top: 10,
                                        bottom: 20
                                    }
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error fetching chart data:', error);
                    rolesLoading.textContent = 'Error loading chart data. Please try again.';
                    sexLoading.textContent = 'Error loading chart data. Please try again.';
                });
        });
    </script>
</body>
</html>