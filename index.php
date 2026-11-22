<?php
/**
 * Passport Control - Immigration Checkpoint Login
 * Renders the biometric passport inspection form.
 * Re-rendered with $error when credentials fail validation.
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/layout.php';

$error    = $error    ?? null;
$username = $username ?? '';

page_start($error ? 'Entry Denied' : 'Immigration Inspection', $error ? 'failure' : 'idle');
?>
<article class="passport-page" aria-labelledby="page-title">
  <!-- Top Passport Security Header -->
  <header class="page-security-bar">
    <div class="security-meta-left">
      <span class="doc-code">PASSPORT / PASSEPORT</span>
      <span class="doc-type">TYPE: P</span>
      <span class="doc-country">COUNTRY CODE: VRT</span>
    </div>
    <div class="security-meta-right">
      <span class="doc-number">DOC NO: VRT-8115-SEC</span>
    </div>
  </header>

  <div class="page-title-banner">
    <h2 id="page-title" class="page-heading">PASSPORT CONTROL &bull; BORDER CHECKPOINT</h2>
    <p class="page-subheading">Present authorized travel credentials for server-side verification against the central datastore.</p>
  </div>

  <?php if ($error): ?>
  <!-- Rubber Stamp Failure Banner -->
  <aside class="denial-container" role="alert" id="login-error">
    <div class="stamp-denied" aria-label="Stamp: Entry Denied">
      <div class="stamp-denied-inner">
        <span class="stamp-agency">REPUBLIC OF VERITY</span>
        <span class="stamp-word">ENTRY DENIED</span>
        <span class="stamp-meta"><?= strtoupper(date('d M Y')) ?> &bull; STATION 8115</span>
      </div>
    </div>
    <div class="denial-details">
      <strong class="denial-heading">BORDER CONTROL NOTIFICATION:</strong>
      <p class="denial-message"><?= e($error) ?></p>
      <p class="denial-subtext">Security protocol: System does not disclose whether the login name or the password was incorrect.</p>
    </div>
  </aside>
  <?php endif; ?>

  <!-- Inspection Form styled as Passport Document Fields -->
  <form class="passport-form" action="login.php" method="post" novalidate data-login-form>
    <div class="passport-fields-grid">
      <!-- Username Field -->
      <div class="passport-field">
        <label for="username" class="field-label">
          <span class="label-primary">01. LOGIN NAME / IDENTIFIANT</span>
          <span class="label-sub">(SURNAME OR USERNAME &bull; CASE-INSENSITIVE)</span>
        </label>
        <div class="field-input-box">
          <input id="username" name="username" type="text"
                 autocomplete="username" autocapitalize="none" spellcheck="false"
                 class="input-data"
                 value="<?= e($username) ?>" required maxlength="64"
                 placeholder="e.g. admin, student, faculty"
                 <?= $error ? 'aria-describedby="login-error" aria-invalid="true"' : '' ?>
                 <?= $username === '' ? 'autofocus' : '' ?>>
        </div>
        <p class="field-validation-msg" id="username-hint" hidden>Document holder login name is required.</p>
      </div>

      <!-- Password Field -->
      <div class="passport-field">
        <label for="password" class="field-label">
          <span class="label-primary">02. TRAVEL PASSKEY / MOT DE PASSE</span>
          <span class="label-sub">(PASSWORD &bull; CASE-SENSITIVE)</span>
        </label>
        <div class="field-input-box password-box">
          <input id="password" name="password" type="password"
                 autocomplete="current-password"
                 class="input-data"
                 required maxlength="128"
                 placeholder="Enter travel passkey"
                 <?= $error ? 'aria-describedby="login-error" aria-invalid="true"' : '' ?>
                 <?= $username !== '' ? 'autofocus' : '' ?>>
          <button type="button" class="btn-reveal" aria-label="Show password" aria-pressed="false" data-reveal>
            <span class="reveal-text">SHOW</span>
          </button>
        </div>
        <p class="field-validation-msg" id="password-hint" hidden>Travel passkey is required.</p>
        <p class="caps-warning-msg" id="caps-hint" hidden>Caps Lock is ON &mdash; Passwords are case-sensitive.</p>
      </div>
    </div>

    <!-- Submission Control -->
    <div class="form-actions-bar">
      <button class="btn-stamp-action" type="submit" data-submit>
        <span class="btn-action-label">VERIFY CREDENTIALS &bull; GRANT ENTRY</span>
      </button>
    </div>
  </form>

  <!-- Authorized Registry Reference -->
  <section class="ledger-section" aria-labelledby="ledger-title">
    <div class="ledger-header">
      <h3 id="ledger-title" class="ledger-title">AUTHORIZED BORDER CONTROL REGISTRY (SEEDED TEST ACCOUNTS)</h3>
      <span class="ledger-note">Store: data/users.db (SQLite 3)</span>
    </div>
    <div class="ledger-table-wrapper">
      <table class="ledger-table">
        <thead>
          <tr>
            <th scope="col">LOGIN NAME</th>
            <th scope="col">PASSKEY</th>
            <th scope="col">REGISTERED HOLDER</th>
            <th scope="col">SECURITY ROLE</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><code class="code-val">admin</code></td>
            <td><code class="code-val">Admin@123</code></td>
            <td>Asha Raman</td>
            <td><span class="role-pill">Administrator</span></td>
          </tr>
          <tr>
            <td><code class="code-val">student</code></td>
            <td><code class="code-val">Student@123</code></td>
            <td>Rahul Menon</td>
            <td><span class="role-pill">Student</span></td>
          </tr>
          <tr>
            <td><code class="code-val">faculty</code></td>
            <td><code class="code-val">Faculty@123</code></td>
            <td>Dr. Meera Iyer</td>
            <td><span class="role-pill">Faculty</span></td>
          </tr>
        </tbody>
      </table>
    </div>
    <p class="ledger-footer-info">
      Login names are matched case-insensitively (<code>COLLATE NOCASE</code>). Passwords are verified on the server against bcrypt hashes using <code>password_verify()</code>.
    </p>
  </section>

  <!-- Machine Readable Zone (MRZ) Strip -->
  <footer class="mrz-strip" aria-label="Passport Machine Readable Zone">
    <div class="mrz-line">P&lt;VRT&lt;&lt;IMMIGRATION&lt;CHECKPOINT&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;</div>
    <div class="mrz-line">VRT8115&lt;&lt;4SEC&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;01</div>
  </footer>
</article>

<script>
(() => {
  const form = document.querySelector('[data-login-form]');
  if (!form) return;
  const pwInput = form.password;
  const userInput = form.username;

  // Show / Hide Password Toggle
  const revealBtn = form.querySelector('[data-reveal]');
  if (revealBtn && pwInput) {
    revealBtn.addEventListener('click', () => {
      const isShowing = pwInput.type === 'text';
      pwInput.type = isShowing ? 'password' : 'text';
      revealBtn.setAttribute('aria-pressed', String(!isShowing));
      revealBtn.setAttribute('aria-label', isShowing ? 'Show password' : 'Hide password');
      const textSpan = revealBtn.querySelector('.reveal-text');
      if (textSpan) textSpan.textContent = isShowing ? 'SHOW' : 'HIDE';
      pwInput.focus();
    });
  }

  // Caps Lock Warning Detection
  const capsHint = document.getElementById('caps-hint');
  if (capsHint && pwInput) {
    pwInput.addEventListener('keyup', ev => {
      capsHint.hidden = !ev.getModifierState?.('CapsLock');
    });
    pwInput.addEventListener('blur', () => {
      capsHint.hidden = true;
    });
  }

  // Client-Side Required Field Verification (Server checks again)
  const validateField = input => {
    const hint = document.getElementById(input.id + '-hint');
    const isEmpty = input.value.trim() === '';
    if (hint) hint.hidden = !isEmpty;
    if (isEmpty) {
      input.setAttribute('aria-invalid', 'true');
    } else if (!document.getElementById('login-error')) {
      input.removeAttribute('aria-invalid');
    }
    return !isEmpty;
  };

  [userInput, pwInput].forEach(field => {
    if (!field) return;
    field.addEventListener('input', () => {
      const hint = document.getElementById(field.id + '-hint');
      if (hint && !hint.hidden) validateField(field);
    });
  });

  form.addEventListener('submit', ev => {
    const userValid = validateField(userInput);
    const pwValid = validateField(pwInput);

    if (!userValid || !pwValid) {
      ev.preventDefault();
      (userValid ? pwInput : userInput).focus();
      return;
    }

    const submitBtn = form.querySelector('[data-submit]');
    if (submitBtn) {
      const label = submitBtn.querySelector('.btn-action-label');
      if (label) label.textContent = 'INSPECTING CREDENTIALS\u2026';
      submitBtn.classList.add('is-submitting');
    }
  });
})();
</script>
<?php page_end();
