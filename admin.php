<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Scoring System</title>
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
      .form-group input {
          width: 100%;
          padding: 0.75rem;
          background: var(--input-background);
          border: 1px solid var(--input-border);
          border-radius: 0.5rem;
          color: var(--text-color);
          transition: border-color 0.3s, transform 0.3s;
      }
      .form-group input:focus {
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
      .form-group input:not(:placeholder-shown) + label {
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
          <header class="header-gradient mb-8 animate-fade-in">
              <h1 class="text-4xl font-bold text-white">Admin Panel</h1>
              <p class="text-gray-300">Manage judges and participants for the scoring system</p>
          </header>
          <div class="glassmorphism p-6 mb-8 animate-fade-in">
              <h2 class="text-2xl font-semibold text-white mb-4">Dashboard Overview</h2>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div class="glassmorphism p-4">
                      <h3 class="text-lg font-semibold text-red-500">Total Judges</h3>
                      <p id="total-judges" class="text-3xl text-white">0</p>
                  </div>
                  <div class="glassmorphism p-4">
                      <h3 class="text-lg font-semibold text-green-500">Total Participants</h3>
                      <p id="total-participants" class="text-3xl text-white">0</p>
                  </div>
                  <div class="glassmorphism p-4">
                      <h3 class="text-lg font-semibold text-purple-500">Total Scores</h3>
                      <p id="total-scores" class="text-3xl text-white">0</p>
                  </div>
              </div>
          </div>
          <div class="glassmorphism p-6 mb-8 animate-fade-in">
              <h2 class="text-2xl font-semibold text-white mb-4">Manage Judges</h2>
              <input type="text" id="judge-search" placeholder="Search judges..." class="w-full p-2 mb-4 bg-gray-700 text-white border border-gray-600 rounded">
              <form id="add-judge-form" class="mb-6">
                  <div class="form-group">
                      <input type="text" id="judge-id" name="judge_id" placeholder=" " required maxlength="50">
                      <label for="judge-id">Judge ID</label>
                  </div>
                  <div class="form-group">
                      <input type="text" id="judge-name" name="judge_name" placeholder=" " required maxlength="100">
                      <label for="judge-name">Judge Name</label>
                  </div>
                  <button type="submit" class="btn-glow">
                      Add Judge <span class="spinner"></span>
                  </button>
              </form>
              <div class="overflow-x-auto">
                  <table class="min-w-full">
                      <thead>
                          <tr>
                              <th class="py-3 px-4 text-left text-white">ID</th>
                              <th class="py-3 px-4 text-left text-white">Name</th>
                              <th class="py-3 px-4 text-left text-white">Actions</th>
                          </tr>
                      </thead>
                      <tbody id="judges-table-body"></tbody>
                  </table>
              </div>
          </div>
          <div class="glassmorphism p-6 mb-8 animate-fade-in">
              <h2 class="text-2xl font-semibold text-white mb-4">Manage Participants</h2>
              <input type="text" id="participant-search" placeholder="Search participants..." class="w-full p-2 mb-4 bg-gray-700 text-white border border-gray-600 rounded">
              <form id="add-participant-form" class="mb-6">
                  <div class="form-group">
                      <input type="text" id="participant-id" name="participant_id" placeholder=" " required maxlength="50">
                      <label for="participant-id">Participant ID</label>
                  </div>
                  <div class="form-group">
                      <input type="text" id="participant-name" name="participant_name" placeholder=" " required maxlength="100">
                      <label for="participant-name">Participant Name</label>
                  </div>
                  <button type="submit" class="btn-glow">
                      Add Participant <span class="spinner"></span>
                  </button>
              </form>
              <div class="overflow-x-auto">
                  <table class="min-w-full">
                      <thead>
                          <tr>
                              <th class="py-3 px-4 text-left text-white">ID</th>
                              <th class="py-3 px-4 text-left text-white">Name</th>
                              <th class="py-3 px-4 text-left text-white">Actions</th>
                          </tr>
                      </thead>
                      <tbody id="participants-table-body"></tbody>
                  </table>
              </div>
          </div>
          <div class="glassmorphism p-6 animate-fade-in">
              <h2 class="text-2xl font-semibold text-white mb-4">Score Distribution</h2>
              <canvas id="score-chart" width="400" height="200"></canvas>
          </div>
          <div id="edit-judge-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
              <div class="modal p-6 rounded-lg w-full max-w-md">
                  <h2 class="text-xl font-bold text-white mb-4">Edit Judge</h2>
                  <form id="edit-judge-form">
                      <div class="form-group">
                          <input type="text" id="edit-judge-id" readonly class="w-full p-2 bg-gray-700 text-white border border-gray-600 rounded">
                          <label for="edit-judge-id">Judge ID</label>
                      </div>
                      <div class="form-group">
                          <input type="text" id="edit-judge-name" placeholder=" " required maxlength="100" class="w-full p-2 bg-gray-700 text-white border border-gray-600 rounded">
                          <label for="edit-judge-name">Judge Name</label>
                      </div>
                      <button type="submit" class="btn-glow">Save</button>
                      <button type="button" id="close-judge-modal" class="ml-2 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Cancel</button>
                  </form>
              </div>
          </div>
          <div id="edit-participant-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
              <div class="modal p-6 rounded-lg w-full max-w-md">
                  <h2 class="text-xl font-bold text-white mb-4">Edit Participant</h2>
                  <form id="edit-participant-form">
                      <div class="form-group">
                          <input type="text" id="edit-participant-id" readonly class="w-full p-2 bg-gray-700 text-white border border-gray-600 rounded">
                          <label for="edit-participant-id">Participant ID</label>
                      </div>
                      <div class="form-group">
                          <input type="text" id="edit-participant-name" placeholder=" " required maxlength="100" class="w-full p-2 bg-gray-700 text-white border border-gray-600 rounded">
                          <label for="edit-participant-name">Participant Name</label>
                      </div>
                      <button type="submit" class="btn-glow">Save</button>
                      <button type="button" id="close-participant-modal" class="ml-2 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Cancel</button>
                  </form>
              </div>
          </div>
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

      function loadData() {
          fetch('api/get_dashboard_data.php')
              .then(response => response.json())
              .then(data => {
                  document.getElementById('total-judges').textContent = data.counts.total_judges;
                  document.getElementById('total-participants').textContent = data.counts.total_participants;
                  document.getElementById('total-scores').textContent = data.counts.total_scores;

                  const judgesTbody = document.getElementById('judges-table-body');
                  judgesTbody.innerHTML = '';
                  data.judges.forEach(judge => {
                      const row = document.createElement('tr');
                      row.className = 'table-row';
                      row.innerHTML = `
                          <td class="py-2 px-4">${judge.id}</td>
                          <td class="py-2 px-4">${judge.name}</td>
                          <td class="py-2 px-4">
                              <button class="edit-judge text-blue-400 hover:text-blue-300" data-id="${judge.id}" data-name="${judge.name}">Edit</button>
                              <button class="delete-judge text-red-400 hover:text-red-300 ml-2" data-id="${judge.id}">Delete</button>
                          </td>
                      `;
                      judgesTbody.appendChild(row);
                  });

                  const participantsTbody = document.getElementById('participants-table-body');
                  participantsTbody.innerHTML = '';
                  data.participants.forEach(participant => {
                      const row = document.createElement('tr');
                      row.className = 'table-row';
                      row.innerHTML = `
                          <td class="py-2 px-4">${participant.id}</td>
                          <td class="py-2 px-4">${participant.name}</td>
                          <td class="py-2 px-4">
                              <button class="edit-participant text-blue-400 hover:text-blue-300" data-id="${participant.id}" data-name="${participant.name}">Edit</button>
                              <button class="delete-participant text-red-400 hover:text-red-300 ml-2" data-id="${participant.id}">Delete</button>
                          </td>
                      `;
                      participantsTbody.appendChild(row);
                  });

                  document.querySelectorAll('.edit-judge').forEach(btn => {
                      btn.addEventListener('click', function() {
                          const id = this.getAttribute('data-id');
                          const name = this.getAttribute('data-name');
                          document.getElementById('edit-judge-id').value = id;
                          document.getElementById('edit-judge-name').value = name;
                          document.getElementById('edit-judge-modal').classList.remove('hidden');
                      });
                  });

                  document.querySelectorAll('.delete-judge').forEach(btn => {
                      btn.addEventListener('click', function() {
                          const id = this.getAttribute('data-id');
                          if (confirm(`Are you sure you want to delete judge ${id}?`)) {
                              fetch('api/delete_judge.php', {
                                  method: 'POST',
                                  body: new URLSearchParams({ judge_id: id })
                              })
                                  .then(response => response.json())
                                  .then(data => {
                                      if (data.success) {
                                          showToast('Judge deleted successfully', 'success');
                                          loadData();
                                      } else {
                                          showToast(data.message || 'Error deleting judge', 'error');
                                      }
                                  })
                                  .catch(error => {
                                      showToast('Error deleting judge', 'error');
                                      console.error(error);
                                  });
                          }
                      });
                  });

                  document.querySelectorAll('.edit-participant').forEach(btn => {
                      btn.addEventListener('click', function() {
                          const id = this.getAttribute('data-id');
                          const name = this.getAttribute('data-name');
                          document.getElementById('edit-participant-id').value = id;
                          document.getElementById('edit-participant-name').value = name;
                          document.getElementById('edit-participant-modal').classList.remove('hidden');
                      });
                  });

                  document.querySelectorAll('.delete-participant').forEach(btn => {
                      btn.addEventListener('click', function() {
                          const id = this.getAttribute('data-id');
                          if (confirm(`Are you sure you want to delete participant ${id}?`)) {
                              fetch('api/delete_participant.php', {
                                  method: 'POST',
                                  body: new URLSearchParams({ participant_id: id })
                              })
                                  .then(response => response.json())
                                  .then(data => {
                                      if (data.success) {
                                          showToast('Participant deleted successfully', 'success');
                                          loadData();
                                      } else {
                                          showToast(data.message || 'Error deleting participant', 'error');
                                      }
                                  })
                                  .catch(error => {
                                      showToast('Error deleting participant', 'error');
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

          fetch('api/get_participant_scores.php')
              .then(response => response.json())
              .then(data => {
                  if (scoreChart) scoreChart.destroy();
                  const ctx = document.getElementById('score-chart').getContext('2d');
                  scoreChart = new Chart(ctx, {
                      type: 'bar',
                      data: {
                          labels: data.map(item => item.name),
                          datasets: [{
                              label: 'Total Scores',
                              data: data.map(item => item.total_score),
                              backgroundColor: 'var(--primary-color)',
                              borderColor: 'var(--primary-color)',
                              borderWidth: 1
                          }]
                      },
                      options: {
                          scales: {
                              y: { beginAtZero: true, title: { display: true, text: 'Total Score', color: 'var(--text-color)' } },
                              x: { title: { display: true, text: 'Participants', color: 'var(--text-color)' } }
                          },
                          plugins: {
                              legend: { labels: { color: 'var(--text-color)' } },
                              title: { display: true, text: 'Score Distribution', color: 'var(--text-color)' }
                          }
                      }
                  });
              })
              .catch(error => {
                  showToast('Error loading score chart', 'error');
                  console.error(error);
              });
      }

      document.getElementById('add-judge-form').addEventListener('submit', function(e) {
          e.preventDefault();
          const judgeId = document.getElementById('judge-id').value.trim();
          const judgeName = document.getElementById('judge-name').value.trim();
          const submitButton = this.querySelector('button');
          const spinner = submitButton.querySelector('.spinner');

          if (judgeId.length < 1 || judgeName.length < 1) {
              showToast('Please fill in all fields', 'error');
              return;
          }

          submitButton.disabled = true;
          spinner.style.display = 'inline-block';

          const formData = new FormData();
          formData.append('judge_id', judgeId);
          formData.append('judge_name', judgeName);

          fetch('api/add_judge.php', {
              method: 'POST',
              body: formData
          })
              .then(response => response.json())
              .then(data => {
                  submitButton.disabled = false;
                  spinner.style.display = 'none';
                  if (data.success) {
                      showToast('Judge added successfully!', 'success');
                      this.reset();
                      loadData();
                  } else {
                      showToast(data.message || 'Error adding judge', 'error');
                  }
              })
              .catch(error => {
                  submitButton.disabled = false;
                  spinner.style.display = 'none';
                  showToast('Error adding judge', 'error');
                  console.error(error);
              });
      });

      document.getElementById('add-participant-form').addEventListener('submit', function(e) {
          e.preventDefault();
          const participantId = document.getElementById('participant-id').value.trim();
          const participantName = document.getElementById('participant-name').value.trim();
          const submitButton = this.querySelector('button');
          const spinner = submitButton.querySelector('.spinner');

          if (participantId.length < 1 || participantName.length < 1) {
              showToast('Please fill in all fields', 'error');
              return;
          }

          submitButton.disabled = true;
          spinner.style.display = 'inline-block';

          const formData = new FormData();
          formData.append('participant_id', participantId);
          formData.append('participant_name', participantName);

          fetch('api/add_participant.php', {
              method: 'POST',
              body: formData
          })
              .then(response => response.json())
              .then(data => {
                  submitButton.disabled = false;
                  spinner.style.display = 'none';
                  if (data.success) {
                      showToast('Participant added successfully!', 'success');
                      this.reset();
                      loadData();
                  } else {
                      showToast(data.message || 'Error adding participant', 'error');
                  }
              })
              .catch(error => {
                  submitButton.disabled = false;
                  spinner.style.display = 'none';
                  showToast('Error adding participant', 'error');
                  console.error(error);
              });
      });

      document.getElementById('edit-judge-form').addEventListener('submit', function(e) {
          e.preventDefault();
          const id = document.getElementById('edit-judge-id').value;
          const name = document.getElementById('edit-judge-name').value.trim();

          if (name.length < 1) {
              showToast('Please enter a name', 'error');
              return;
          }

          fetch('api/update_judge.php', {
              method: 'POST',
              body: new URLSearchParams({ judge_id: id, judge_name: name })
          })
              .then(response => response.json())
              .then(data => {
                  if (data.success) {
                      showToast('Judge updated successfully', 'success');
                      document.getElementById('edit-judge-modal').classList.add('hidden');
                      loadData();
                  } else {
                      showToast(data.message || 'Error updating judge', 'error');
                  }
              })
              .catch(error => {
                  showToast('Error updating judge', 'error');
                  console.error(error);
              });
      });

      document.getElementById('edit-participant-form').addEventListener('submit', function(e) {
          e.preventDefault();
          const id = document.getElementById('edit-participant-id').value;
          const name = document.getElementById('edit-participant-name').value.trim();

          if (name.length < 1) {
              showToast('Please enter a name', 'error');
              return;
          }

          fetch('api/update_participant.php', {
              method: 'POST',
              body: new URLSearchParams({ participant_id: id, participant_name: name })
          })
              .then(response => response.json())
              .then(data => {
                  if (data.success) {
                      showToast('Participant updated successfully', 'success');
                      document.getElementById('edit-participant-modal').classList.add('hidden');
                      loadData();
                  } else {
                      showToast(data.message || 'Error updating participant', 'error');
                  }
              })
              .catch(error => {
                  showToast('Error updating participant', 'error');
                  console.error(error);
              });
      });

      document.getElementById('close-judge-modal').addEventListener('click', function() {
          document.getElementById('edit-judge-modal').classList.add('hidden');
      });

      document.getElementById('close-participant-modal').addEventListener('click', function() {
          document.getElementById('edit-participant-modal').classList.add('hidden');
      });

      document.getElementById('judge-search').addEventListener('input', function() {
          const filter = this.value.toLowerCase();
          const rows = document.querySelectorAll('#judges-table-body tr');
          rows.forEach(row => {
              const id = row.cells[0].textContent.toLowerCase();
              const name = row.cells[1].textContent.toLowerCase();
              row.style.display = (id.includes(filter) || name.includes(filter)) ? '' : 'none';
          });
      });

      document.getElementById('participant-search').addEventListener('input', function() {
          const filter = this.value.toLowerCase();
          const rows = document.querySelectorAll('#participants-table-body tr');
          rows.forEach(row => {
              const id = row.cells[0].textContent.toLowerCase();
              const name = row.cells[1].textContent.toLowerCase();
              row.style.display = (id.includes(filter) || name.includes(filter)) ? '' : 'none';
          });
      });

      document.getElementById('sidebar-toggle').addEventListener('click', function() {
          const sidebar = document.getElementById('sidebar');
          sidebar.classList.toggle('open');
      });

      loadData();
  </script>
</body>
</html>