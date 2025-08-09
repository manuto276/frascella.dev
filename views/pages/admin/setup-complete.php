<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Setup Complete</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
  <main class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-10 col-lg-7">
        <div class="card shadow border-0">
          <div class="card-header border-0 text-white" style="background: linear-gradient(90deg,#4f46e5,#7c3aed);">
            <div class="d-flex align-items-center gap-3 py-2">
              <div class="bg-white bg-opacity-25 rounded-3 d-inline-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                <i class="bi bi-check2-circle fs-5"></i>
              </div>
              <div>
                <h1 class="h5 mb-0">Setup Complete</h1>
                <small class="text-white-50">Your administrator account has been created.</small>
              </div>
            </div>
          </div>

          <div class="card-body p-4 p-lg-5">
            <p class="mb-4">You can now access the admin area or return to the website.</p>
            <div class="d-grid d-sm-flex gap-2">
              <!-- Link directly to dashboard.
                   If not authenticated, JwtAuth will redirect to /admin/login -->
              <a href="/admin/dashboard" class="btn btn-primary">
                <i class="bi bi-speedometer2 me-1"></i> Go to Admin Panel
              </a>
              <a href="/" class="btn btn-outline-secondary">
                <i class="bi bi-house-door me-1"></i> Back to Site
              </a>
            </div>
          </div>

          <div class="card-footer bg-white border-0 text-muted small py-3">
            <div class="d-flex justify-content-between">
              <span>Setup wizard</span>
              <span>Portfolio Admin</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>
</html>
