<style>
    /* reset and base styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Schibsted Grotesk";
    }

    body {
        background-color: #fff;
        padding: 20px;
    }

    /* header styles */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .header h1 {
        color: #1a1a1a;
    }

    /*      .header-buttons button {
            padding: 8px 16px;
            margin-left: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    */
    .primary-btn {
        background-color: #1a73e8;
        color: white;
    }

    /* stats grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #FFD700;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .stat-card h3 {
        color: #666;
        font-size: 0.9em;
        margin-bottom: 8px;
    }

    .stat-card p {
        font-size: 1.5em;
        font-weight: bold;
        color: #1a1a1a;
    }

    /* main grid */
    .main-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: 1px solid #FFD700;
    }

    /* alerts */
    .alert {
        padding: 10px;
        margin: 10px 0;
        border-radius: 4px;
    }

    .alert.warning { background-color: #fff3cd; }
    .alert.error { background-color: #f8d7da; }
    .alert.info { background-color: #cce5ff; }

    /* activity feed */
    .activity-item {
        background: white;
        display: flex;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .activity-icon {
        background: #FFD700;
        padding: 10px;
        border-radius: 50%;
        margin-right: 10px;
    }

    /*!* quick actions *!*/
    /*.quick-actions {*/
    /*    display: grid;*/
    /*    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));*/
    /*    gap: 10px;*/
    /*}*/

    /*.action-btn {*/
    /*    padding: 15px;*/
    /*    background: #f8f9fa;*/
    /*    border: none;*/
    /*    border-radius: 8px;*/
    /*    cursor: pointer;*/
    /*    transition: background 0.3s;*/
    /*}*/

    /*.action-btn:hover {*/
    /*    background: #e9ecef;*/
    /*}*/

    /* responsive design for different devices. how do i do ts for main?? :(( */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .main-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <title>dashboard</title>
</head>
<body>
<div class="header">
    <h1>Dashboard</h1>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Ranking?</h3>
        <p>1</p>
    </div>
    <div class="stat-card">
        <h3>Average Grade</h3>
        <p>10000%</p>
    </div>
    <div class="stat-card">
        <h3>Current Period</h3>
        <p>xx</p>
    </div>
    <div class="stat-card">
        <h3>Next Period</h3>
        <p>wenis</p>
    </div>
</div>

<div class="main-grid">
    <div class="card">
        <h2>Alerts</h2>
        <div class="alert warning">hassan gon bomb the building soon!</div>
        <div class="alert error">heritage is calling you...</div>
        <div class="alert info">exams due next week eek</div>
    </div>

    <div class="card">
        <h2>Grade Distribution</h2>
        <canvas id="gradeChart"></canvas>
    </div>

    <div class="card">
        <h2>Recent Activity</h2>
        <div class="activity-item">
            <div class="activity-icon">📝</div>
            <div>
                <p>grade entered for Physics</p>
                <small>2 mins ago</small>
            </div>
        </div>
        <div class="activity-item">
            <div class="activity-icon">💬</div>
            <div>
                <p>grades entered for spanish sad...</p>
                <small>15 mins ago</small>
            </div>
        </div>
        <div class="activity-item">
            <div class="activity-icon">📚</div>
            <div>
                <p>grades updated for math</p>
                <small>1 hour ago</small>
            </div>
        </div>
    </div>
</div>

<!--<div class="card">-->
<!--    <h2>Quick Actions</h2>-->
<!--    <div class="quick-actions">-->
<!--        <button class="action-btn"></button>-->
<!--        <button class="action-btn">Take Attendance</button>-->
<!--        <button class="action-btn">Add Comment</button>-->
<!--        <button class="action-btn">Generate Report</button>-->
<!--    </div>-->
<!--</div>-->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // initialize chart
    const ctx = document.getElementById('gradeChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['English', 'Math', 'C++', 'Literature', 'Spanish'],
            datasets: [{
                label: 'Average in Class',
                data: [83, 96, 99, 98, 89],
                backgroundColor: '#FFD700'
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    ticks: {
                        color: '#141729' // Change x-axis text color
                    }
                },
                y: {
                    ticks: {
                        color: '#141729',
                        font : {
                            weight: 'bold',
                            family: 'Schibsted Grotesk'
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#141729',
                        font: {
                            weight: 'bold',
                            family: 'Schibsted Grotesk'
                        }
                    }
                }
            }
        }
    });

    // adds interactivity to buttons
    document.querySelectorAll('button').forEach(button => {
        button.addEventListener('click', function() {
            alert(`Clicked: ${this.textContent}`);
        });
    });

    // adds hover effects to cards
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'transform 0.3s';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
</script>
</body>
</html>