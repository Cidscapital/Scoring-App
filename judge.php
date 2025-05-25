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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
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
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            word-break: break-word;
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
        /* DataTables and Bootstrap color overrides */
        table.dataTable {
            background: var(--table-row-odd) !important;
            color: var(--text-color) !important;
        }
        table.dataTable thead th {
            background: var(--table-header) !important;
            color: var(--text-color) !important;
        }
        table.dataTable tbody tr.even {
            background: var(--table-row-even) !important;
        }
        table.dataTable tbody tr.odd {
            background: var(--table-row-odd) !important;
        }
        table.dataTable tbody tr:hover {
            background: rgba(255,255,255,0.08) !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: var(--text-color) !important;
            background: var(--background-color) !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-color) !important;
            color: #fff !important;
        }
        .dataTables_wrapper .dataTables_filter input {
            background: var(--input-background);
            color: var(--text-color);
            border: 1px solid var(--input-border);
        }
        .dataTables_wrapper .dataTables_length select {
            background: var(--input-background);
            color: var(--text-color);
            border: 1px solid var(--input-border);
        }
        /* Responsive tweaks */
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            color: var(--secondary-text);
        }
        /* Remove Tailwind's table block display on mobile, keep table structure */
        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: table !important;
                width: 100% !important;
            }
        }
        @media (max-width: 1024px) {
            .glassmorphism, .header-gradient {
                padding: 1rem !important;
            }
            main {
                padding: 1rem !important;
            }
        }
        @media (max-width: 900px) {
            .grid-cols-3 {
                grid-template-columns: 1fr !important;
            }
        }
        @media (max-width: 768px) {
            aside {
                transform: translateX(-100%);
                position: fixed;
                z-index: 50;
                width: 80vw;
                min-width: 220px;
                max-width: 320px;
                height: 100vh;
                top: 0;
                left: 0;
                background: #1A1A1A;
                box-shadow: 2px 0 10px rgba(0,0,0,0.2);
            }
            aside.open {
                transform: translateX(0);
            }
            main {
                margin-left: 0;
                padding: 0.5rem !important;
            }
            #sidebar-toggle {
                display: block;
            }
            .glassmorphism, .header-gradient {
                padding: 0.5rem !important;
            }
            .grid-cols-3 {
                grid-template-columns: 1fr !important;
            }
            .overflow-x-auto {
                overflow-x: auto;
            }
            table {
                min-width: 600px;
                width: 100%;
            }
        }
        @media (max-width: 480px) {
            .glassmorphism, .header-gradient {
                padding: 0.25rem !important;
            }
            main {
                padding: 0.25rem !important;
            }
            table {
                min-width: 400px;
            }
        }
    </style>
</head>
<body>
    <button id="sidebar-toggle" class="md:hidden fixed top-4 left-4 z-50 bg-red-600 text-white p-2 rounded">
        ☰
    </button>
    <div class="flex min-h-screen flex-col md:flex-row">
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
                    <h2 class="text-2xl font-semibold text-white mb-4 text-center md:text-left">Judge Login</h2>
                    <form id="login-form" class="flex flex-col gap-4">
                        <div class="form-group">
                            <input type="text" id="judge-id" name="judge_id" placeholder=" " required maxlength="50">
                            <label for="judge-id">Judge ID</label>
                        </div>
                        <button type="submit" class="btn-glow w-full">
                            Login <span class="spinner"></span>
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <header class="header-gradient mb-8 animate-fade-in">
                    <h1 class="text-4xl font-bold text-white text-center md:text-left">Judge Portal - Welcome, <?php echo htmlspecialchars($judge_name); ?></h1>
                    <p class="text-gray-300 text-center md:text-left">Submit and manage your scores</p>
                    <button id="logout-btn" class="mt-2 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 block mx-auto md:mx-0">Logout</button>
                </header>
                <div class="glassmorphism p-6 mb-8 animate-fade-in">
                    <h2 class="text-2xl font-semibold text-white mb-4 text-center md:text-left">Judge Dashboard</h2>
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
                    <h2 class="text-2xl font-semibold text-white mb-4 text-center md:text-left">Submit Score</h2>
                    <form id="submit-score-form" class="mb-6 flex flex-col md:flex-row md:items-end gap-4">
                        <div class="form-group flex-1">
                            <select id="participant-id" name="participant_id" required>
                                <option value="" disabled selected>Select Participant</option>
                            </select>
                            <label for="participant-id">Participant</label>
                        </div>
                        <div class="form-group flex-1">
                            <input type="number" id="score" name="score" placeholder=" " required min="1" max="10">
                            <label for="score">Score (1-10)</label>
                        </div>
                        <button type="submit" class="btn-glow w-full md:w-auto">
                            Submit Score <span class="spinner"></span>
                        </button>
                    </form>
                </div>
                <div class="glassmorphism p-6 mb-8 animate-fade-in">
                    <h2 class="text-2xl font-semibold text-white mb-4 text-center md:text-left">Score History</h2>
                    <!-- Remove old search input, DataTables will add its own -->
                    <div class="overflow-x-auto">
                        <table id="scores-table" class="min-w-full table table-striped table-bordered" style="width:100%">
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
                    <h2 class="text-2xl font-semibold text-white mb-4 text-center md:text-left">Scoring Analytics</h2>
                    <canvas id="score-chart" width="400" height="200"></canvas>
                </div>
                <div id="edit-score-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center d-none" style="display:none;">
                    <div class="modal p-6 rounded-lg w-full max-w-md bg-gray-800">
                        <h2 class="text-xl font-bold text-white mb-4">Edit Score</h2>
                        <form id="edit-score-form">
                            <div class="form-group">
                                <input type="text" id="edit-participant-id" readonly class="w-full p-2 bg-gray-700 text-white border border-gray-600 rounded" name="edit_participant_id">
                                <label for="edit-participant-id">Participant ID</label>
                            </div>
                            <div class="form-group">
                                <input type="number" id="edit-score" placeholder=" " required min="1" max="10" class="w-full p-2 bg-gray-700 text-white border border-gray-600 rounded" name="edit_score">
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
                style: { background: type === 'success' ? "#10b981" : "#ef4444" },
                className: "animate-slide-in",
            }).showToast();
        }

        // Helper to show/hide modal
        function showEditScoreModal() {
            const modal = document.getElementById('edit-score-modal');
            modal.style.display = 'flex';
            modal.classList.remove('d-none', 'hidden');
        }
        function hideEditScoreModal() {
            const modal = document.getElementById('edit-score-modal');
            modal.style.display = 'none';
            modal.classList.add('d-none');
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
                        data.scores.forEach((score, idx) => {
                            const row = document.createElement('tr');
                            row.className = idx % 2 === 0 ? 'odd' : 'even';
                            row.innerHTML = `
                                <td>${score.participant_id}</td>
                                <td>${score.participant_name}</td>
                                <td>${score.score}</td>
                                <td>
                                    <button class="edit-score text-blue-400 hover:text-blue-300 btn btn-sm btn-link" data-participant-id="${score.participant_id}" data-score="${score.score}">Edit</button>
                                    <button class="delete-score text-red-400 hover:text-red-300 ml-2 btn btn-sm btn-link" data-participant-id="${score.participant_id}">Delete</button>
                                </td>
                            `;
                            scoresTbody.appendChild(row);
                        });

                        // Use jQuery for DataTables check and initialization
                        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.DataTable && window.jQuery.fn.DataTable.isDataTable('#scores-table')) {
                            $('#scores-table').DataTable().destroy();
                        }
                        $('#scores-table').DataTable({
                            responsive: true,
                            paging: true,
                            searching: true,
                            ordering: true,
                            info: true,
                            autoWidth: false,
                            lengthMenu: [5, 10, 25, 50],
                            pageLength: 10,
                            language: {
                                search: "Search:",
                                lengthMenu: "Show _MENU_ entries",
                                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                                paginate: {
                                    previous: "Prev",
                                    next: "Next"
                                }
                            },
                            createdRow: function(row, data, dataIndex) {
                                // Apply color classes for even/odd rows
                                if (dataIndex % 2 === 0) {
                                    row.classList.add('odd');
                                } else {
                                    row.classList.add('even');
                                }
                            }
                        });

                        document.querySelectorAll('.edit-score').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const participantId = this.getAttribute('data-participant-id');
                                const score = this.getAttribute('data-score');
                                document.getElementById('edit-participant-id').value = participantId;
                                document.getElementById('edit-score').value = score;
                                showEditScoreModal();
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
                            hideEditScoreModal();
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
                hideEditScoreModal();
            });

            // Also close modal when clicking outside the modal content
            document.getElementById('edit-score-modal').addEventListener('mousedown', function(e) {
                if (e.target === this) {
                    hideEditScoreModal();
                }
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

        // Sidebar close on outside click (mobile)
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebar-toggle');
            if (window.innerWidth <= 768 && sidebar.classList.contains('open')) {
                if (!sidebar.contains(e.target) && e.target !== toggle) {
                    sidebar.classList.remove('open');
                }
            }
        });

        // Accessibility: close sidebar with ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === "Escape") {
                document.getElementById('sidebar').classList.remove('open');
            }
        });
    </script>
</body>
</html>