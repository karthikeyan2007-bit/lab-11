<?php
/**
 * Passport Control - Immigration Authentication Controller
 * Validates travel credentials against the SQLite database.
 * Returns the biometric passport record with ENTRY GRANTED rubber stamp,
 * or re-renders index.php with failure notification.
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/layout.php';

// Redirect GET requests back to the checkpoint
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = trim((string)($_POST['username'] ?? ''));
$password = (string)($_POST['password'] ?? '');

// Empty fields validation (HTTP 400)
if ($username === '' || $password === '') {
    http_response_code(400);
    $error = 'Please enter both your login name and your password.';
    require __DIR__ . '/index.php';
    exit;
}

// Validate credentials against users datastore
$user = validate_user($username, $password);

// Incorrect credentials validation (HTTP 401)
if ($user === null) {
    http_response_code(401);
    $error = 'The login name or password you entered does not match our records. Please try again.';
    require __DIR__ . '/index.php';
    exit;
}

/* ==========================================================================
   Successful Authentication: Passport Biometric Profile & Visa Stamps
   ========================================================================== */
// ASCII name parsing without mb_* functions
$nameClean = preg_replace('/^Dr\.\s*/', '', $user['full_name']);
$nameParts = explode(' ', $nameClean);
$initials  = substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1);
$surname   = strtoupper(end($nameParts));
$givenBase = implode(' ', array_slice($nameParts, 0, -1));
$givenNames = strtoupper(str_starts_with($user['full_name'], 'Dr.') ? 'Dr. ' . $givenBase : $givenBase);
if ($givenNames === '') {
    $givenNames = $surname;
}

$fmt = fn(?string $dt) => $dt ? date('j M Y, g:i A', strtotime($dt)) : null;

// Construct 2-line standard 44-character MRZ strip for the traveler
$mrzSur = preg_replace('/[^A-Z]/', '', $surname);
$mrzGiv = preg_replace('/[^A-Z]/', '', $givenNames);
$mrzLine1 = 'P<VRT' . $mrzSur . '<<' . $mrzGiv;
$mrzLine1 = substr(str_pad($mrzLine1, 44, '<'), 0, 44);

$userIdStr = sprintf('%04d', $user['id']);
$userCode  = strtoupper(substr(preg_replace('/[^A-Z0-9]/', '', $user['username']), 0, 8));
$logCount  = sprintf('%02d', min(99, (int)$user['login_count']));
$mrzLine2 = 'VRT' . $userIdStr . '<<<' . $userCode . '<<<<<<<<<<<<<<<<<<<' . $logCount;
$mrzLine2 = substr(str_pad($mrzLine2, 44, '<'), 0, 44);

page_start('Entry Granted: ' . $user['full_name'], 'success');
?>
<article class="passport-page page-granted" aria-labelledby="profile-title">
  <!-- Top Passport Security Header -->
  <header class="page-security-bar">
    <div class="security-meta-left">
      <span class="doc-code">PASSPORT / PASSEPORT</span>
      <span class="doc-type">TYPE: P</span>
      <span class="doc-country">COUNTRY CODE: VRT</span>
    </div>
    <div class="security-meta-right">
      <span class="doc-number">DOC NO: VRT-<?= sprintf('%04d', (int)$user['id']) ?>-SEC</span>
    </div>
  </header>

  <div class="page-title-banner">
    <h2 id="profile-title" class="page-heading">REPUBLIC OF VERITY &bull; PASSPORT IDENTITY RECORD</h2>
    <p class="page-subheading">Identity verified through server-side password_verify(). Clearance confirmed.</p>
  </div>

  <!-- Entry Granted Rubber Stamp & Clearance Status -->
  <aside class="grant-stamp-banner" aria-label="Official Clearance Stamp">
    <div class="stamp-granted" aria-label="Stamp: Entry Granted">
      <div class="stamp-granted-inner">
        <span class="stamp-agency">REPUBLIC OF VERITY IMMIGRATION</span>
        <span class="stamp-word">ENTRY GRANTED</span>
        <span class="stamp-date"><?= strtoupper(date('d M Y')) ?> &bull; PORT 8115</span>
        <span class="stamp-officer">INSPECTOR: SYS-AUTH-01</span>
      </div>
    </div>
    <div class="grant-welcome">
      <p class="grant-eyebrow">IDENTITY CONFIRMED &bull; PASSKEY VALIDATED</p>
      <h3 class="grant-name">Welcome, <?= e($user['full_name']) ?></h3>
      <p class="grant-desc">Your login identifier and passkey matched the hashed record in <code>data/users.db</code>.</p>
    </div>
  </aside>

  <!-- Biometric Passport Data Card -->
  <div class="passport-bio-grid">
    <!-- Official Photo Box with Initials -->
    <div class="bio-photo-cell" aria-label="Official Identification Photo">
      <div class="photo-frame">
        <div class="photo-initials" aria-hidden="true"><?= e(strtoupper($initials)) ?></div>
        <div class="photo-watermark" aria-hidden="true">
          <span>VRT</span><span>BIO</span><span>8115</span>
        </div>
      </div>
      <p class="photo-caption">BIOMETRIC PHOTO ID</p>
      <p class="photo-subcaption">REPUBLIC OF VERITY</p>
    </div>

    <!-- Official Data Fields with Surname / Given Names mapped labels -->
    <dl class="bio-data-fields">
      <div class="bio-field-row">
        <div class="bio-field-item">
          <dt class="bio-label">01. SURNAME / NOM</dt>
          <dd class="bio-value"><?= e($surname) ?></dd>
        </div>
        <div class="bio-field-item">
          <dt class="bio-label">02. GIVEN NAMES / PR&Eacute;NOMS</dt>
          <dd class="bio-value"><?= e($givenNames) ?></dd>
        </div>
      </div>

      <div class="bio-field-row">
        <div class="bio-field-item">
          <dt class="bio-label">03. OFFICIAL EMAIL / COURRIEL</dt>
          <dd class="bio-value bio-email"><?= e($user['email']) ?></dd>
        </div>
        <div class="bio-field-item">
          <dt class="bio-label">04. CLEARANCE ROLE / R&Ocirc;LE</dt>
          <dd class="bio-value"><span class="role-badge"><?= e($user['role']) ?></span></dd>
        </div>
      </div>

      <div class="bio-field-row">
        <div class="bio-field-item">
          <dt class="bio-label">05. LOGIN NAME / IDENTIFIANT</dt>
          <dd class="bio-value"><code class="bio-code"><?= e($user['username']) ?></code></dd>
        </div>
        <div class="bio-field-item">
          <dt class="bio-label">06. NATIONALITY / NATIONALIT&Eacute;</dt>
          <dd class="bio-value">VERITY (VRT)</dd>
        </div>
      </div>
    </dl>
  </div>

  <!-- Visa Stamps Row (Total Sign-ins, Previous Sign-in, Member Since) -->
  <section class="visa-stamps-section" aria-labelledby="stamps-heading">
    <div class="visa-stamps-header">
      <h3 id="stamps-heading" class="visa-section-title">IMMIGRATION VISA STAMPS &bull; ADMISSION HISTORY</h3>
      <span class="visa-section-sub">RECORDED IN SQLITE DATASTORE</span>
    </div>

    <div class="visa-stamps-grid">
      <!-- Stamp 1: Total Sign-ins -->
      <div class="visa-stamp-box stamp-box-count">
        <div class="stamp-box-border">
          <span class="stamp-box-header">IMMIGRATION CONTROL</span>
          <strong class="stamp-box-val"><?= (int)$user['login_count'] ?></strong>
          <span class="stamp-box-label">TOTAL SIGN-INS</span>
          <span class="stamp-box-foot">ENTRIES RECORDED</span>
        </div>
      </div>

      <!-- Stamp 2: Previous Sign-in -->
      <div class="visa-stamp-box stamp-box-prev">
        <div class="stamp-box-border">
          <span class="stamp-box-header">PRIOR ADMISSION</span>
          <strong class="stamp-box-val val-prev"><?= e($fmt($user['last_login']) ?? 'First time — welcome!') ?></strong>
          <span class="stamp-box-label">PREVIOUS SIGN-IN</span>
          <span class="stamp-box-foot">PORT 8115 CHECKPOINT</span>
        </div>
      </div>

      <!-- Stamp 3: Member Since -->
      <div class="visa-stamp-box stamp-box-since">
        <div class="stamp-box-border">
          <span class="stamp-box-header">DOCUMENT ISSUED</span>
          <strong class="stamp-box-val val-date"><?= e(date('j M Y', strtotime($user['created_at']))) ?></strong>
          <span class="stamp-box-label">MEMBER SINCE</span>
          <span class="stamp-box-foot">REGISTRY ARCHIVE</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Sign-Out Action Bar -->
  <div class="passport-exit-bar">
    <a class="btn-stamp-action btn-signout" href="index.php">
      <span class="btn-action-label">SURRENDER PASSPORT &bull; EXIT CHECKPOINT (SIGN OUT)</span>
    </a>
  </div>

  <!-- Machine Readable Zone (MRZ) Strip with Personalized Traveler Data -->
  <footer class="mrz-strip" aria-label="Passport Machine Readable Zone">
    <div class="mrz-line"><?= e($mrzLine1) ?></div>
    <div class="mrz-line"><?= e($mrzLine2) ?></div>
  </footer>
</article>
<?php page_end();
