<?php
// Compose inner content then include layout
ob_start();
?>
<div class="row g-3">
  <div class="col-12 col-xl-8">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h2 class="h6 mb-0">Traffic (last 7 days)</h2>
          <form id="rangeForm" class="d-flex gap-2 align-items-center">
            <input type="date" class="form-control form-control-sm" id="from">
            <input type="date" class="form-control form-control-sm" id="to">
            <button class="btn btn-sm btn-outline-primary" type="submit">Apply</button>
          </form>
        </div>
        <canvas id="trafficLine" height="120"></canvas>
      </div>
    </div>
  </div>
  <div class="col-12 col-xl-4">
    <div class="row g-3">
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <div class="text-muted small mb-1">Total hits</div>
            <div id="kpiTotal" class="display-6 fw-semibold">—</div>
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <div class="text-muted small mb-1">Unique IPs</div>
            <div id="kpiUnique" class="h3 mb-0">—</div>
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <div class="text-muted small mb-1">Avg. response</div>
            <div id="kpiAvgRt" class="h3 mb-0">— ms</div>
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <h2 class="h6 mb-3">Top Paths</h2>
            <ul id="topPaths" class="list-unstyled mb-0"></ul>
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h2 class="h6 mb-0">Latest Messages</h2>
              <a href="/admin/contacts" class="btn btn-sm btn-outline-secondary">Open</a>
            </div>
            <ul id="latestContacts" class="list-unstyled mb-0 small"></ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="module">
  import http from '/js/axiosClient.js'; // axios preconfigurato: withCredentials + interceptor 401→refresh→retry

  (() => {
    const qs = (s, el = document) => el.querySelector(s);

    // ---- API layer (Axios) ---------------------------------------------------
    const api = {
      summary: (from, to) =>
        http.get('/admin/api/traffic/summary', {
          params: {
            from: from || '',
            to: to || ''
          }
        }).then(r => r.data),

      timeseries: (from, to) =>
        http.get('/admin/api/traffic/timeseries', {
          params: {
            from: from || '',
            to: to || ''
          }
        }).then(r => r.data),

      latestContacts: (limit = 5) =>
        http.get('/admin/api/contacts/latest', {
          params: {
            limit
          }
        }).then(r => r.data),
    };

    // ---- Chart.js ------------------------------------------------------------
    const lineCtx = document.getElementById('trafficLine').getContext('2d');
    let lineChart;

    function renderLine(labels, values) {
      if (lineChart) lineChart.destroy();
      lineChart = new Chart(lineCtx, {
        type: 'line',
        data: {
          labels,
          datasets: [{
            label: 'Hits',
            data: values,
            tension: .3,
            fill: true,
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true
            }
          },
          plugins: {
            legend: {
              display: false
            }
          }
        }
      });
    }

    function renderTopPaths(list) {
      const ul = qs('#topPaths');
      ul.innerHTML = '';
      list.forEach(row => {
        const li = document.createElement('li');
        li.className = 'd-flex justify-content-between border-bottom py-1';
        li.innerHTML = `<span class="text-truncate" style="max-width:72%">${row.path}</span><span class="text-muted">${row.hits}</span>`;
        ul.appendChild(li);
      });
    }

    function esc(s) {
      return s == null ? '' : String(s).replace(/[&<>"']/g, c => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      } [c]));
    }

    async function loadLatestContacts(limit = 5) {
      const el = document.getElementById('latestContacts');
      if (!el) return;

      try {
        const data = await api.latestContacts(limit);

        if (!Array.isArray(data) || data.length === 0) {
          el.innerHTML = '<li class="text-muted">No messages yet.</li>';
          return;
        }

        el.innerHTML = data.map(m => `
          <li class="d-flex justify-content-between border-bottom py-1">
            <span class="text-truncate" style="max-width:70%">
              <strong>${esc(m.name)}</strong>
              — ${esc(m.subject || '(no subject)')}
            </span>
            <span class="text-muted">${esc(m.created_at)}</span>
          </li>
        `).join('');
      } catch (err) {
        // Se dopo il retry rimane 401, rimanda al login
        if (err?.response?.status === 401) {
          window.location.href = '/admin/login';
          return;
        }
        console.error('Failed to load latest contacts:', err);
      }
    }

    async function load(from, to) {
      try {
        const [sum, ts] = await Promise.all([api.summary(from, to), api.timeseries(from, to)]);

        // KPIs
        qs('#kpiTotal').textContent = sum.total ?? '0';
        qs('#kpiUnique').textContent = sum.unique_ips ?? '0';
        qs('#kpiAvgRt').textContent = (sum.avg_rt_ms ?? 0) + ' ms';
        renderTopPaths(sum.top_paths ?? []);

        // Timeseries
        const labels = (ts.daily ?? []).map(r => r.d);
        const values = (ts.daily ?? []).map(r => Number(r.hits));
        renderLine(labels, values);

        // Latest contacts
        loadLatestContacts(5);
      } catch (err) {
        if (err?.response?.status === 401) {
          window.location.href = '/admin/login';
          return;
        }
        console.error('Dashboard load failed:', err);
      }
    }

    // Date range form
    qs('#rangeForm').addEventListener('submit', (e) => {
      e.preventDefault();
      const from = qs('#from').value || '';
      const to = qs('#to').value || '';
      load(from, to);
    });

    // initial
    load();
  })();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../../layouts/admin.php';
