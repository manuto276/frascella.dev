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

<script>
(() => {
  const q = s => document.querySelector(s);
  const rows = q('#rows'), pager = q('#pager');

  function esc(s){return s==null?'':String(s).replace(/[&<>"']/g,c=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;' }[c]));}

  async function fetchList(page=1){
    const params = new URLSearchParams({
      status: q('#status').value || '',
      from: q('#from').value || '',
      to: q('#to').value || '',
      page, per: 20
    });
    const r = await fetch(`/admin/api/contacts?${params}`, {credentials:'same-origin', headers:{'Accept':'application/json'}});
    if (r.status===401) { location.href='/admin/login'; return; }
    return r.json();
  }

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
        <td><button class="btn btn-sm btn-outline-secondary view" data-id="${m.id}">View</button></td>`;
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
      sel.addEventListener('change', async e=>{
        const id = sel.dataset.id;
        await fetch(`/admin/api/contacts/${id}/status`, {
          method:'POST', credentials:'same-origin',
          headers:{'Content-Type':'application/x-www-form-urlencoded','Accept':'application/json'},
          body:new URLSearchParams({status: sel.value}).toString()
        });
      });
    });
  }

  async function load(page=1){ render(await fetchList(page)); }

  q('#filters').addEventListener('submit', e=>{e.preventDefault(); load(1);});
  load();
})();
</script>
<?php $content = ob_get_clean(); include __DIR__.'/../../../layouts/admin.php'; ?>
