<?php
/**
 * Passport Control Layout Component
 * Republic of Verity Border Inspection & Identity Verification Agency
 *
 * $state parameter: 'idle' | 'success' | 'failure'
 */

function page_start(string $title, string $state = 'idle'): void
{
    $stateLabels = [
        'idle'    => 'AWAITING INSPECTION',
        'success' => 'ENTRY GRANTED',
        'failure' => 'ENTRY DENIED',
    ];
    $statusText = $stateLabels[$state] ?? $stateLabels['idle'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?> · Passport Control · Republic of Verity</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,600&family=Courier+Prime:ital,wght@0,400;0,700;1,400&family=Source+Sans+3:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><circle cx='16' cy='16' r='14' fill='%235A1A24' stroke='%23C5A059' stroke-width='2'/><path d='M16 6l2.5 5 5.5.8-4 3.9 1 5.3-5-2.6-5 2.6 1-5.3-4-3.9 5.5-.8z' fill='%23C5A059'/></svg>">
</head>
<body class="state-<?= e($state) ?>">

<div class="desk-container">
  <!-- Passport Cover Embossed Header -->
  <header class="passport-cover-header" aria-label="Official Authority Header">
    <div class="crest-wrap" aria-hidden="true">
      <svg class="passport-crest" viewBox="0 0 120 120" role="img" aria-label="Embossed Coat of Arms">
        <circle cx="60" cy="60" r="54" fill="none" stroke="#D4AF37" stroke-width="1.8" stroke-dasharray="2,3" opacity="0.7"/>
        <circle cx="60" cy="60" r="50" fill="none" stroke="#D4AF37" stroke-width="1.2" opacity="0.6"/>
        <path d="M60 22 L72 38 L90 40 L76 53 L80 72 L60 62 L40 72 L44 53 L30 40 L48 38 Z" fill="none" stroke="#D4AF37" stroke-width="1.8" opacity="0.85"/>
        <path d="M42 80 C48 88, 72 88, 78 80" fill="none" stroke="#D4AF37" stroke-width="1.5" opacity="0.7"/>
        <path d="M35 84 C45 95, 75 95, 85 84" fill="none" stroke="#D4AF37" stroke-width="1.2" opacity="0.6"/>
        <circle cx="60" cy="50" r="6" fill="#D4AF37" opacity="0.75"/>
        <path d="M60 14 L60 22 M60 78 L60 86" stroke="#D4AF37" stroke-width="1.5" opacity="0.6"/>
      </svg>
    </div>
    <div class="cover-titles">
      <p class="authority-sub">IMMIGRATION & BORDER CONTROL AGENCY</p>
      <h1 class="authority-title">REPUBLIC OF VERITY</h1>
      <p class="authority-doc">BIOMETRIC IDENTITY DOCUMENT · LAB 11 CHECKPOINT</p>
    </div>
    <div class="checkpoint-badge" aria-label="Checkpoint Telemetry">
      <span class="badge-port">STATION: PORT 8115</span>
      <span class="badge-status status-<?= e($state) ?>"><?= e($statusText) ?></span>
    </div>
  </header>

  <!-- Open Passport Document Frame -->
  <main class="passport-desk" id="main-content">
<?php
}

function page_end(): void
{
?>
  </main>

  <!-- Passport Control Footer -->
  <footer class="passport-footer">
    <div class="footer-inner">
      <p class="security-notice">
        OFFICIAL BIOMETRIC TRAVEL SPECIFICATION &bull; PASSWORDS SECURED VIA BCRYPT HASH &bull; SQLITE CREDENTIAL STORE
      </p>
      <p class="copy-notice">
        Republic of Verity Immigration Service &bull; Station 8115 &bull; Strictly for Authorized Access
      </p>
    </div>
  </footer>
</div>

</body>
</html>
<?php
}
