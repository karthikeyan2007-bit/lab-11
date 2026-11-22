# Lab 11 — Passport Control & Immigration Checkpoint

A user submits a login name and password to the server. The server checks them against the users stored in an SQLite database; if they match, a **successful-login page** ("ENTRY GRANTED" biometric passport page) is returned, otherwise a **failure message** ("ENTRY DENIED" rubber stamp) is shown.

Design: **"Passport Control"**:
- **Environment**: Deep oxblood passport cover (`#5A1A24`) with an embossed gold heraldic seal drawn in inline SVG.
- **Biometric Data Page**: Warm security paper (`#F6F0E1`) with fine guilloche security patterns generated using CSS `repeating-radial-gradient`.
- **Typography**: Google Fonts pairing *Cormorant Garamond* (headings/stamps), *Source Sans 3* (body), and *Courier Prime* (monospaced data fields & MRZ strip). Small caps security field labels.
- **Rubber Stamps**:
  - Failure: Red `#B3261E` rubber stamp **"ENTRY DENIED"** (rotated -8deg, double border, slightly uneven opacity, thump animation).
  - Success: Green `#1E6B3A` rubber stamp **"ENTRY GRANTED"** with inspection date and checkpoint stamp.
- **Traveler Profile**: Biometric portrait box with user initials, official Surname/Given names data mapping, and a "visa stamps" admission history row (total sign-ins, previous sign-in, member-since).
- **Machine Readable Zone (MRZ)**: Official two-line monospaced strip at the card footer formatted in Courier Prime.
- **Accessibility**: Every text/background pair meets WCAG AA 4.5:1 contrast standards. Visible focus rings, respect for `prefers-reduced-motion`, and fully responsive down to 320px with zero horizontal page scroll.

**Stack:** PHP 8 (built-in server) + SQLite 3 via PDO. No external PHP libraries, no Composer, no `mb_*` functions.

---

## Run

Run the PowerShell startup script:

```powershell
.\start.ps1
```

Then open:

```
http://localhost:8115
```

*(Equivalent manual command: `php -d extension_dir="<php_dir>\ext" -d extension=pdo_sqlite -S localhost:8115 -t .`)*

---

## Test Accounts

Created automatically in `data/users.db` the first time the app runs:

| Login name | Password | Registered Holder | Security Role |
| :--- | :--- | :--- | :--- |
| `admin` | `Admin@123` | Asha Raman | Administrator |
| `student` | `Student@123` | Rahul Menon | Student |
| `faculty` | `Faculty@123` | Dr. Meera Iyer | Faculty |

- **Login names** are case-insensitive (`COLLATE NOCASE`).
- **Passwords** are case-sensitive (hashed with bcrypt via `password_hash()`).
- Delete the `data/` folder to reset the database to the initial seed state.

---

## File Structure

| File | Description |
| :--- | :--- |
| `index.php` | Passport biometric inspection form; also re-rendered with HTTP 400/401 and the `ENTRY DENIED` stamp on error. Preserves submitted username. |
| `login.php` | Authentication controller; receives POST, validates against SQLite, and renders the `ENTRY GRANTED` passport profile or returns errors. |
| `db.php` | Database layer: connects to/creates `data/users.db`, seeds initial test accounts, defines `validate_user()` and `e()` escaping. |
| `layout.php` | Shared page shell: oxblood leather cover background, embossed SVG crest, checkpoint telemetry header, and footer. |
| `assets/style.css` | Comprehensive stylesheet: security paper texture, rubber stamp effects, MRZ strip, responsive layout, and WCAG AA contrast. |
| `start.ps1` | PowerShell launch script serving the app on port 8115 with `pdo_sqlite` enabled. |
| `.gitignore` | Ignores the SQLite `data/` directory to keep local state out of source control. |

---

## How Validation Works

1. **Submission**: `index.php` posts `username` and `password` to `login.php`.
2. **Input Presence**: `login.php` checks for empty fields. If either is blank, it responds with **HTTP 400** and re-renders `index.php` with an error message.
3. **Database Lookup**: `login.php` calls `validate_user($username, $password)` in `db.php`:
   - Executes a prepared query: `SELECT * FROM users WHERE username = ?`.
   - The username comparison uses SQLite `COLLATE NOCASE`.
4. **Password Verification**:
   - `password_verify($password, $user['password_hash'])` checks the cleartext password against the stored bcrypt hash.
5. **On Success (HTTP 200)**:
   - Updates `last_login = datetime('now', 'localtime')` and increments `login_count`.
   - Renders the passport data page with the green **"ENTRY GRANTED"** rubber stamp, user initials photo box, personal details, visa history stamps, and a customized 44-character MRZ strip.
6. **On Failure (HTTP 401)**:
   - If the user is not found or the password does not match, returns **HTTP 401**.
   - Re-renders `index.php` with the red **"ENTRY DENIED"** rubber stamp and a single generic message: *"The login name or password you entered does not match our records. Please try again."*
   - Never reveals whether the username or password was the cause of the failure.
