<?php
session_start();
if (!isset($_SESSION['judge_id'])) {
    // Show login form if not logged in
    $show_login = true;
} else {
    $show_login = false;
    $judge_id = $_SESSION['judge_id'];
    $judge_name = $_SESSION['judge_name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judge Portal - Scoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style>
        :root {
            --primary-color: #D81F26; /* Netflix Red */
            --background-color: #121212; /* Dark Gray */
            --text-color: #FFFFFF; /* White */
            --secondary-text: #D1D5DB; /* Light Gray */
            --card-background: rgba(255, 255, 255, 0.05);
            --table-header: #333333;
            --table-row-even: #222222;
            --table-row-odd: #1A1A1A;
            --input-background: #222222;
            --input-border: #444444;
            --gradient-start: #D81F26; /* Red */
            --gradient-end: #FFD700; /* Gold */
        }
        body {
            background: var(--background-color);
            color: var(--text-color);
            font-family: 'Inter', sans-serif;
            margin: 0;
        }
        .glassmorphism {
            background: var(--card-background);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .header-gradient {
            background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
            padding: 2rem;
            border-radius: 0 0 1rem 1rem;
        }
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.75rem;
            background: var(--input-background);
            border: 1px solid var(--input-border);
            border-radius: 0.5rem;
            color: var(--text-color);
            transition: border-color 0.3s, transform 0.3s;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            transform: scale(1.02);
        }
        .form-group label {
            position: absolute;
            top: 0.75rem;
            left: 0.75rem;
            color: var(--secondary-text);
            transition: all 0.2s;
            pointer-events: none;
        }
        .form-group input:focus + label,
        .form-group input:not(:placeholder-shown) + label,
        .form-group select:focus + label {
            top: -0.75rem;
            left: 0.5rem;
            font-size: 0.75rem;
            color: var(--primary-color);
            background: var(--input-background);
            padding: 0 0.25rem;
        }
        .btn-glow {
            background: var(--primary-color);
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            color: var(--text-color);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .btn-glow:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(216, 31, 38, 0.5);
        }
        .spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid var(--text-color);
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            margin-left: 0.5rem;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .table-row {
            transition: transform 0.3s, background-color 0.3s;
        }
        .table-row:hover {
            transform: translateY(-2px);
            background-color: rgba(255, 255, 255, 0.15);
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .modal {
            background: rgba(31, 41, 55, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
        }
        aside {
            background: #1A1A1A;
            transition: transform 0.3s ease-in-out;
        }
        aside a:hover {
            color: var(--primary-color);
        }
        table {
            background: var(--table-row-odd);
        }
        tr:nth-child(even) {
            background: var(--table-row-even);
        }
        th {
            background: var(--table-header);
        }
        #sidebar-toggle {
            display: none;
        }
        @media (max-width: 768px) {
            aside {
                transform: translateX(-100%);
                position: fixed;
                z-index: 50;
            }
            aside.open {
                transform: translateX(0);
            }
            main {
                margin-left: 0;
            }
            #sidebar-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>
    <button id="sidebar-toggle" class="md:hidden fixed top-4 left-4 z-50 bg-red-600 text-white p-2 rounded">
        ☰
    </button>
    <div class="flex min-h-screen">
        <aside class="w-64 p-4 h-full" id="sidebar">
            <h2 class="text-2xl font-bold text-white mb-6 animate-fade-in">Navigation</h2>
            <ul class="space-y-4">
                <li><a href="admin.php" class="text-white text-lg hover:underline">Dashboard</a></li>
                <li><a href="judge.php" class="text-white text-lg hover:underline">Judge Portal</a></li>
                <li><a href="index.php" class="text-white text-lg hover:underline">Scoreboard</a></li>
            </ul>
        </aside>
        <main class="flex-1 p-6">
            <?php if ($show_login): ?>
                <div class="glassmorphism p-6 max-w-md mx-auto animate-fade-in">
                    <h2 class="text-2xl font-semibold text-white mb-4">Judge Login</h2>
                    <form id="login-form">
                        <div class="form-group">
                            <input type="text" id="judge-id" name="judge_id" placeholder=" " required maxlength="50">
                            <label for="judge-id">Judge ID</label>
                        </div>
                        <button type="submit" class="btn-glow">
                            Login <span class="spinner"></span>
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <header class="header-gradient mb-8 animate-fade-in">
                    <h1 class="text-4xl font-bold text-white">Judge Portal - Welcome, <?php echo htmlspecialchars($judge_name); ?></h1>
                    <p class="text-gray-300">Submit and manage your scores</p>
                    <button id="logout-btn" class="mt-2 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Logout</button>
                </header>
                <div class="glassmorphism p-6 mb-8 animate-fade-in">
                    <h2 class="text-2xl font-semibold text-white mb-4">Judge Dashboard</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="glassmorphism p-4">
                            <h3 class="text-lg font-semibold text-red-500">Total Scores Submitted</h3>
                            <p id="total-scores" class="text-3xl text-white">0</p>
                        </div>
                        <div class="glassmorphism p-4">
                            <h3 class="text-lg font-semibold text-green-500">Participants Scored</h3>
                            <p id="participants-scored" class="text-3xl text-white">0</p>
                        </div>
                        <div class="glassmorphism p-4">
                            <h3 class="text-lg font-semibold text-purple-500">Average Score</h3>
                            <p id="average-score" class="text-3xl text-white">0</p>
                        </div>
                    </div>
                </div>
                <div class="glassmorphism p-6 mb-8 animate-fade-in">
                    <h2 class="text-2xl font-semibold text-white mb-4">Submit Score</h2>
                    <form id="submit-score-form" class="mb-6">
                        <div class="form-group">
                            <select id="participant-id" name="participant_id" required>
                                <option value="" disabled selected>Select Participant</option>
                            </select>
                            <label for="participant-id">Participant</label>
                        </div>
                        <div class="form-group">
                            <input type="number" id="score" name="score" placeholder=" " required min="1" max="10">
                            <label for="score">Score (1-10)</label>
                        </div>
                        <button type="submit" class="btn-glow">
                            Submit Score <span class="spinner"></span>
                        </button>
                    </form>
                </div>
                <div class="glassmorphism p-6 mb-8 animate-fade-in">
                    <h2 class="text-2xl font-semibold text-white mb-4">Score History</h2>
                    <input type="text" id="score-search" placeholder="Search scores..." class="w-full p-2 mb-4 bg-gray-700 text-white border border-gray-600 rounded">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="py-3 px-4 text-left text-white">Participant ID</th>
                                    <th class="py-3 px-4 text-left text-white">Participant Name</th>
                                    <th class="py-3 px-4 text-left text-white">Score</th>
                                    <th class="py-3 px-4 text-left text-white">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="scores-table-body"></tbody>
                        </table>
                    </div>
                </div>
                <div class="glassmorphism p-6 animate-fade-in">
                    <h2 class="text-2xl font-semibold text-white mb-4">Scoring Analytics</h2>
                    <canvas id="score-chart" width="400" height="200"></canvas>
                </div>
                <div id="edit-score-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
                    <div class="modal p-6 rounded-lg w-full max-w-md">
                        <h2 class="text-xl font-bold text-white mb-4">Edit Score</h2>
                        <form id="edit-score-form">
                            <div class="form-group">
                                <input type="text" id="edit-participant-id" readonly class="w-full p-2 bg-gray-700 text-white border border-gray-600 rounded">
                                <label for="edit-participant-id">Participant ID</label>
                            </div>
                            <div class="form-group">
                                <input type="number" id="edit-score" placeholder=" " required min="1" max="10" class="w-full p-2 bg-gray-700 text-white border border-gray-600 rounded">
                                <label for="edit-score">Score (1-10)</label>
                            </div>
                            <button type="submit" class="btn-glow">Save</button>
                            <button type="button" id="close-score-modal" class="ml-2 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Cancel</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
    <script>
        let scoreChart;

        function showToast(message, type) {
            Toastify({
                text: message,
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: type === 'success' ? "#10b981" : "#ef4444",
                className: "animate-slide-in",
            }).showToast();
        }

        <?php if ($show_login): ?>
            document.getElementById('login-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const judgeId = document.getElementById('judge-id').value.trim();
                const submitButton = this.querySelector('button');
                const spinner = submitButton.querySelector('.spinner');

                if (judgeId.length < 1) {
                    showToast('Please enter a Judge ID', 'error');
                    return;
                }

                submitButton.disabled = true;
                spinner.style.display = 'inline-block';

                const formData = new FormData();
                formData.append('judge_id', judgeId);

                fetch('api/judge_login.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        submitButton.disabled = false;
                        spinner.style.display = 'none';
                        if (data.success) {
                            window.location.reload();
                        } else {
                            showToast(data.message || 'Invalid Judge ID', 'error');
                        }
                    })
                    .catch(error => {
                        submitButton.disabled = false;
                        spinner.style.display = 'none';
                        showToast('Error logging in', 'error');
                        console.error(error);
                    });
            });
        <?php else: ?>
            function loadData() {
                fetch('api/get_judge_data.php')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('total-scores').textContent = data.metrics.total_scores;
                        document.getElementById('participants-scored').textContent = data.metrics.participants_scored;
                        document.getElementById('average-score').textContent = data.metrics.average_score.toFixed(2);

                        const participantSelect = document.getElementById('participant-id');
                        participantSelect.innerHTML = '<option value="" disabled selected>Select Participant</option>';
                        data.participants.forEach(participant => {
                            const option = document.createElement('option');
                            option.value = participant.id;
                            option.textContent = `${participant.name} (${participant.id})`;
                            participantSelect.appendChild(option);
                        });

                        const scoresTbody = document.getElementById('scores-table-body');
                        scoresTbody.innerHTML = '';
                        data.scores.forEach(score => {
                            const row = document.createElement('tr');
                            row.className = 'table-row';
                            row.innerHTML = `
                                <td class="py-2 px-4">${score.participant_id}</td>
                                <td class="py-2 px-4">${score.participant_name}</td>
                                <td class="py-2 px-4">${score.score}</td>
                                <td class="py-2 px-4">
                                    <button class="edit-score text-blue-400 hover:text-blue-300" data-participant-id="${score.participant_id}" data-score="${score.score}">Edit</button>
                                    <button class="delete-score text-red-400 hover:text-red-300 ml-2" data-participant-id="${score.participant_id}">Delete</button>
                                </td>
                            `;
                            scoresTbody.appendChild(row);
                        });

                        document.querySelectorAll('.edit-score').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const participantId = this.getAttribute('data-participant-id');
                                const score = this.getAttribute('data-score');
                                document.getElementById('edit-participant-id').value = participantId;
                                document.getElementById('edit-score').value = score;
                                document.getElementById('edit-score-modal').classList.remove('hidden');
                            });
                        });

                        document.querySelectorAll('.delete-score').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const participantId = this.getAttribute('data-participant-id');
                                if (confirm(`Are you sure you want to delete the score for participant ${participantId}?`)) {
                                    fetch('api/delete_score.php', {
                                        method: 'POST',
                                        body: new URLSearchParams({ participant_id: participantId })
                                    })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success) {
                                                showToast('Score deleted successfully', 'success');
                                                loadData();
                                            } else {
                                                showToast(data.message || 'Error deleting score', 'error');
                                            }
                                        })
                                        .catch(error => {
                                            showToast('Error deleting score', 'error');
                                            console.error(error);
                                        });
                                }
                            });
                        });
                    })
                    .catch(error => {
                        showToast('Error loading data', 'error');
                        console.error(error);
                    });

                fetch('api/get_judge_score_analytics.php')
                    .then(response => response.json())
                    .then(data => {
                        if (scoreChart) scoreChart.destroy();
                        const ctx = document.getElementById('score-chart').getContext('2d');
                        scoreChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: data.map(item => item.participant_name),
                                datasets: [{
                                    label: 'Average Score',
                                    data: data.map(item => item.average_score),
                                    backgroundColor: 'var(--primary-color)',
                                    borderColor: 'var(--primary-color)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: { beginAtZero: true, max: 10, title: { display: true, text: 'Average Score', color: 'var(--text-color)' } },
                                    x: { title: { display: true, text: 'Participants', color: 'var(--text-color)' } }
                                },
                                plugins: {
                                    legend: { labels: { color: 'var(--text-color)' } },
                                    title: { display: true, text: 'Your Scoring Patterns', color: 'var(--text-color)' }
                                }
                            }
                        });
                    })
                    .catch(error => {
                        showToast('Error loading score chart', 'error');
                        console.error(error);
                    });
            }

            document.getElementById('submit-score-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const participantId = document.getElementById('participant-id').value;
                const score = document.getElementById('score').value;
                const submitButton = this.querySelector('button');
                const spinner = submitButton.querySelector('.spinner');

                if (!participantId || score < 1 || score > 10) {
                    showToast('Please select a participant and enter a score between 1 and 10', 'error');
                    return;
                }

                submitButton.disabled = true;
                spinner.style.display = 'inline-block';

                const formData = new FormData();
                formData.append('participant_id', participantId);
                formData.append('score', score);

                fetch('api/submit_score.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        submitButton.disabled = false;
                        spinner.style.display = 'none';
                        if (data.success) {
                            showToast('Score submitted successfully!', 'success');
                            this.reset();
                            loadData();
                        } else {
                            showToast(data.message || 'Error submitting score', 'error');
                        }
                    })
                    .catch(error => {
                        submitButton.disabled = false;
                        spinner.style.display = 'none';
                        showToast('Error submitting score', 'error');
                        console.error(error);
                    });
            });

            document.getElementById('edit-score-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const participantId = document.getElementById('edit-participant-id').value;
                const score = document.getElementById('edit-score').value;

                if (score < 1 || score > 10) {
                    showToast('Please enter a score between 1 and 10', 'error');
                    return;
                }

                fetch('api/update_score.php', {
                    method: 'POST',
                    body: new URLSearchParams({ participant_id: participantId, score: score })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Score updated successfully', 'success');
                            document.getElementById('edit-score-modal').classList.add('hidden');
                            loadData();
                        } else {
                            showToast(data.message || 'Error updating score', 'error');
                        }
                    })
                    .catch(error => {
                        showToast('Error updating score', 'error');
                        console.error(error);
                    });
            });

            document.getElementById('close-score-modal').addEventListener('click', function() {
                document.getElementById('edit-score-modal').classList.add('hidden');
            });

            document.getElementById('score-search').addEventListener('input', function() {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll('#scores-table-body tr');
                rows.forEach(row => {
                    const participantId = row.cells[0].textContent.toLowerCase();
                    const participantName = row.cells[1].textContent.toLowerCase();
                    row.style.display = (participantId.includes(filter) || participantName.includes(filter)) ? '' : 'none';
                });
            });

            document.getElementById('logout-btn').addEventListener('click', function() {
                fetch('api/judge_logout.php')
                    .then(() => window.location.reload())
                    .catch(error => {
                        showToast('Error logging out', 'error');
                        console.error(error);
                    });
            });

            loadData();
        <?php endif; ?>

        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        });
    </script>
</body>
</html>