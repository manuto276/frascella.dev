<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons (optional) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
  <main class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-9 col-lg-6 col-xl-5">
        <div class="card shadow border-0">
          <div class="card-header border-0 text-white" style="background: linear-gradient(90deg,#4f46e5,#7c3aed);">
            <div class="py-2">
              <h1 class="h5 mb-0">Admin Login</h1>
              <small class="text-white-50">Sign in to access the dashboard</small>
            </div>
          </div>

          <div class="card-body p-4 p-lg-5">
            <?php if (!empty($loginError ?? '')): ?>
              <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($loginError) ?>
              </div>
            <?php endif; ?>

            <form method="post" action="/admin/login" class="needs-validation" novalidate>
              <div class="mb-3">
                <label class="form-label" for="email">Email address</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                  <input id="email" type="email" name="email" class="form-control" required
                         value="<?= htmlspecialchars($email ?? '') ?>" placeholder="you@domain.com" autocomplete="username">
                  <div class="invalid-feedback">Please provide a valid email.</div>
                </div>
              </div>

              <div class="mb-2">
                <label class="form-label" for="password">Password</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-lock"></i></span>
                  <input id="password" type="password" name="password" class="form-control" required
                         autocomplete="current-password">
                  <div class="invalid-feedback">Please enter your password.</div>
                </div>
              </div>

              <div class="d-flex align-items-center justify-content-between mt-3">
                <a class="text-decoration-none" href="/"><i class="bi bi-arrow-left"></i> Back to site</a>
                <button class="btn btn-primary px-4" type="submit">Log In</button>
              </div>
            </form>
          </div>

          <div class="card-footer bg-white border-0 text-muted small py-3">
            <div class="d-flex justify-content-between">
              <span>Secure area</span>
              <span>Portfolio Admin</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Bootstrap JS (optional for validation UX) -->
  <script>
    (function () {
      'use strict';
      const forms = document.querySelectorAll('.needs-validation');
      Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
          if (!form.checkValidity()) { event.preventDefault(); event.stopPropagation(); }
          form.classList.add('was-validated');
        }, false);
      });
    })();
  </script>
</body>
</html>
