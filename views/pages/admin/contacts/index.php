<?php ob_start(); ?>
<div class="card shadow-sm border-0">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="h6 mb-0">Contacts</h2>
      <form id="filters" class="row g-2">
        <div class="col-auto">
          <select id="status" class="form-select form-select-sm">
            <option value="">All</option>
            <option>new</option><option>read</option><option>replied</option><option>archived</option>
          </select>
        </div>
        <div class="col-auto"><input type="date" id="from" class="form-control form-control-sm"></div>
        <div class="col-auto"><input type="date" id="to"   class="form-control form-control-sm"></div>
        <div class="col-auto"><button class="btn btn-sm btn-outline-primary">Apply</button></div>
      </form>
    </div>

    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Subject</th><th>Status</th><th></th></tr></thead>
        <tbody id="rows"></tbody>
      </table>
    </div>

    <nav><ul class="pagination pagination-sm" id="pager"></ul></nav>
  </div>
</div>

<script type="module">
  import http from '/js/axiosClient.js'; // Axios preconfigurato: withCredentials + 401→refresh→retry

  (() => {
    const q = s => document.querySelector(s);
    const rows = q('#rows'), pager = q('#pager');

    function esc(s){return s==null?'':String(s).replace(/[&<>"']/g,c=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;' }[c]));}

    // ---- API layer (Axios) ---------------------------------------------------
    const api = {
      list: (page = 1) => {
        const params = {
          status: q('#status').value || '',
          from:   q('#from').value   || '',
          to:     q('#to').value     || '',
          page,
          per: 20
        };
        return http.get('/admin/api/contacts', { params }).then(r => r.data);
      },
      setStatus: (id, status) => {
        // server si aspetta x-www-form-urlencoded
        const body = new URLSearchParams({ status });
        return http.post(`/admin/api/contacts/${id}/status`, body, {
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });
      }
    };

    function render(list){
      rows.innerHTML = '';
      list.data.forEach(m => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${esc(m.created_at)}</td>
          <td>${esc(m.name)}</td>
          <td><a href="mailto:${esc(m.email)}">${esc(m.email)}</a></td>
          <td>${esc(m.subject||'-')}</td>
          <td>
            <select class="form-select form-select-sm status" data-id="${m.id}">
              ${['new','read','replied','archived'].map(s=>`<option value="${s}" ${s===m.status?'selected':''}>${s}</option>`).join('')}
            </select>
          </td>
          <td><a class="btn btn-sm btn-outline-secondary" href="/admin/contacts/${m.id}">View</a></td>`;
        rows.appendChild(tr);
      });

      // pager
      const pages = Math.max(1, Math.ceil(list.total / list.per));
      pager.innerHTML = '';
      for (let p=1; p<=pages; p++){
        const li = document.createElement('li');
        li.className = 'page-item'+(p===list.page?' active':'');
        li.innerHTML = `<a class="page-link" href="#">${p}</a>`;
        li.addEventListener('click', e => { e.preventDefault(); load(p); });
        pager.appendChild(li);
      }

      rows.querySelectorAll('.status').forEach(sel=>{
        sel.addEventListener('change', async () => {
          try {
            await api.setStatus(sel.dataset.id, sel.value);
          } catch (err) {
            if (err?.response?.status === 401) {
              window.location.href = '/admin/login';
            } else {
              console.error('Failed to update status:', err);
            }
          }
        });
      });
    }

    async function load(page=1){
      try {
        const data = await api.list(page);
        render(data);
      } catch (err) {
        if (err?.response?.status === 401) {
          window.location.href = '/admin/login';
        } else {
          console.error('Contacts load failed:', err);
        }
      }
    }

    q('#filters').addEventListener('submit', e=>{ e.preventDefault(); load(1); });
    load();
  })();
</script>

<?php $content = ob_get_clean(); include __DIR__.'/../../../layouts/admin.php'; ?>
