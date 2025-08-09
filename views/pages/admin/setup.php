<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Setup</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons (optional) -->
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
                <i class="bi bi-shield-lock-fill fs-5"></i>
              </div>
              <div>
                <h1 class="h5 mb-0">Admin Setup</h1>
                <small class="text-white-50">Create your first administrator account</small>
              </div>
            </div>
          </div>

          <div class="card-body p-4 p-lg-5">
            <?php if (!empty($formErrors ?? [])): ?>
              <div class="alert alert-danger" role="alert">
                <div class="fw-semibold mb-1">Please fix the following:</div>
                <ul class="mb-0 ps-3">
                  <?php foreach ($formErrors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

            <form method="post" action="/setup" class="needs-validation" novalidate>
              <div class="mb-3">
                <label class="form-label" for="name">Full Name</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-person"></i></span>
                  <input id="name" type="text" name="name" class="form-control" required
                         value="<?= htmlspecialchars($name ?? '') ?>" placeholder="Jane Doe">
                  <div class="invalid-feedback">Please enter your name.</div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label" for="email">Email address</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                  <input id="email" type="email" name="email" class="form-control" required
                         value="<?= htmlspecialchars($email ?? '') ?>" placeholder="you@domain.com" autocomplete="email">
                  <div class="invalid-feedback">Please provide a valid email.</div>
                </div>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label" for="password">Password</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input id="password" type="password" name="password" class="form-control" required
                           placeholder="Minimum 8 characters" autocomplete="new-password">
                    <div class="invalid-feedback">Please enter a password (min 8 chars).</div>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="password_confirm">Confirm Password</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-check2-circle"></i></span>
                    <input id="password_confirm" type="password" name="password_confirm" class="form-control" required
                           autocomplete="new-password">
                    <div class="invalid-feedback">Please confirm your password.</div>
                  </div>
                </div>
              </div>

              <div class="d-flex align-items-center justify-content-between mt-4">
                <a class="text-decoration-none" href="/"><i class="bi bi-arrow-left"></i> Back to site</a>
                <button class="btn btn-primary px-4" type="submit">Create Admin</button>
              </div>
            </form>
          </div>

          <div class="card-footer bg-white border-0 text-muted small py-3">
            <div class="d-flex justify-content-between">
              <span>Initial setup</span>
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
