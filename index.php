<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Scoreboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
            --gradient-start: #D81F26;
            --gradient-end: #FFD700;
        }
        body {
            background: var(--background-color);
            color: var(--text-color);
            font-family: 'Roboto', sans-serif;
            margin: 0;
        }
        .header-gradient {
            background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
            padding: 2rem;
            border-radius: 0 0 1rem 1rem;
        }
        .glassmorphism {
            background: var(--card-background);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .top-card {
            padding: 1.5rem;
            margin: 1rem;
            border-radius: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s;
        }
        .top-card:hover {
            transform: scale(1.05);
        }
        .top-card.first-place {
            border-left: 5px solid #D4AF37;
        }
        .top-card.second-place {
            border-left: 5px solid #C0C0C0;
        }
        .top-card.third-place {
            border-left: 5px solid #CD7F32;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
        }
        th {
            background: var(--table-header);
        }
        tr:nth-child(even) {
            background: var(--table-row-even);
        }
        tr:nth-child(odd) {
            background: var(--table-row-odd);
        }
        tr:hover {
            background: #333333;
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
        .live-badge {
            background: #4CAF50;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            margin-left: 0.5rem;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 768px) {
            .top3 {
                flex-direction: column;
            }
            table, thead, tbody, th, td, tr {
                display: block;
            }
            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }
            tr {
                margin-bottom: 1rem;
            }
            td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }
            td:before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 1rem;
                font-weight: bold;
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="container mx-auto p-4">
        <header class="header-gradient mb-8 animate-fade-in">
            <h1 class="text-4xl font-bold text-white">Public Scoreboard <span class="live-badge">Live</span></h1>
            <p class="text-gray-300">Real-time scores of all participants</p>
        </header>
        <div id="scoreboard-container">
            <div class="mb-8 animate-fade-in">
                <h2 class="text-2xl font-semibold text-white mb-4">Top 3 Leaders</h2>
                <div class="flex flex-col md:flex-row gap-4 top3" id="top3"></div>
            </div>
            <div class="glassmorphism p-6 animate-fade-in">
                <h2 class="text-2xl font-semibold text-white mb-4">Full Scoreboard</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="py-3 px-4 text-left text-white">Rank</th>
                                <th class="py-3 px-4 text-left text-white">Participant</th>
                                <th class="py-3 px-4 text-left text-white">Total Score</th>
                            </tr>
                        </thead>
                        <tbody id="scoreboard-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <p id="last-updated" class="text-gray-400 mt-4">Last updated: loading...</p>
        <button id="refresh-btn" class="btn-glow mt-4">Refresh Now</button>
    </div>
    <script>
        function updateScoreboard() {
            const loading = document.createElement('div');
            loading.textContent = 'Updating...';
            loading.className = 'text-gray-400 mt-4';
            document.getElementById('scoreboard-container').appendChild(loading);

            fetch('api/get_scoreboard.php')
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    const top3Container = document.getElementById('top3');
                    top3Container.innerHTML = '';
                    for (let i = 0; i < 3; i++) {
                        if (data[i]) {
                            const participant = data[i];
                            const medal = ['🥇', '🥈', '🥉'][i];
                            const card = document.createElement('div');
                            card.className = `glassmorphism p-4 text-center flex-1 top-card ${i === 0 ? 'first-place' : i === 1 ? 'second-place' : 'third-place'}`;
                            card.innerHTML = `
                                <div class="text-4xl mb-2">${medal}</div>
                                <div class="text-xl font-bold">${participant.name}</div>
                                <div class="text-lg">${participant.total_score}</div>
                            `;
                            top3Container.appendChild(card);
                        }
                    }

                    const tbody = document.getElementById('scoreboard-body');
                    tbody.innerHTML = '';
                    let currentRank = 1;
                    let prevScore = data.length > 0 ? data[0].total_score : null;
                    data.forEach((participant, index) => {
                        if (index > 0 && participant.total_score < prevScore) {
                            currentRank = index + 1;
                        }
                        prevScore = participant.total_score;
                        const row = document.createElement('tr');
                        row.className = 'table-row';
                        row.innerHTML = `
                            <td class="py-2 px-4" data-label="Rank">${currentRank}</td>
                            <td class="py-2 px-4" data-label="Participant">${participant.name}</td>
                            <td class="py-2 px-4" data-label="Total Score">${participant.total_score}</td>
                        `;
                        tbody.appendChild(row);
                    });

                    loading.remove();
                    document.getElementById('last-updated').textContent = `Last updated: ${new Date().toLocaleTimeString()}`;
                })
                .catch(error => {
                    console.error('Error fetching scoreboard:', error);
                    loading.textContent = 'Error loading data';
                });
        }

        updateScoreboard();
        setInterval(updateScoreboard, 10000);
        document.getElementById('refresh-btn').addEventListener('click', updateScoreboard);
    </script>
</body>
</html>