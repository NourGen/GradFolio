# Implementation Report: Cloudflare R2 Storage Migration

We have successfully migrated the file storage layer of the **GradFolio** platform from local disk storage to **Cloudflare R2** (S3-compatible object storage). This allows user files (profile pictures, covers, project attachments, and CVs) to persist permanently, surviving Railway service restarts and redeployments.

---

## 1. Files Modified

The following files have been modified or added during the migration:

* **Configuration**:
  * [config/filesystems.php](file:///c:/Users/Nour/Desktop/G-S/config/filesystems.php): Configured S3-compatible `r2` (public) and `r2_private` (private) disks with automatic fallback to local directories if credentials are not set.
  * [composer.json](file:///c:/Users/Nour/Desktop/G-S/composer.json): Added dependency on the `league/flysystem-aws-s3-v3` package.
  * [composer.lock](file:///c:/Users/Nour/Desktop/G-S/composer.lock): Updated lock file.
  * [.env.example](file:///c:/Users/Nour/Desktop/G-S/.env.example): Added documentation for Cloudflare R2 keys.

* **Services**:
  * [app/Services/ImageOptimizer.php](file:///c:/Users/Nour/Desktop/G-S/app/Services/ImageOptimizer.php): Modified to write optimized avatars, project covers, and gallery images to the `r2` disk instead of local `public`.

* **Controllers**:
  * [app/Http/Controllers/PortfolioDashboardController.php](file:///c:/Users/Nour/Desktop/G-S/app/Http/Controllers/PortfolioDashboardController.php): Updated profile picture uploads/deletes to use `r2`, and CV uploads/deletes to use `r2_private`.
  * [app/Http/Controllers/ProjectController.php](file:///c:/Users/Nour/Desktop/G-S/app/Http/Controllers/ProjectController.php): Updated project cover and gallery uploads/deletes to use `r2`.
  * [app/Http/Controllers/Admin/ProjectController.php](file:///c:/Users/Nour/Desktop/G-S/app/Http/Controllers/Admin/ProjectController.php): Updated project deletion file cleanup to target `r2`.
  * [app/Http/Controllers/PublicPortfolioController.php](file:///c:/Users/Nour/Desktop/G-S/app/Http/Controllers/PublicPortfolioController.php): Updated CV secure download to check existence and download from the `r2_private` disk.

* **Models**:
  * [app/Models/Portfolio.php](file:///c:/Users/Nour/Desktop/G-S/app/Models/Portfolio.php): Added `profilePictureUrl()` and `cvUrl()` helpers to dynamically construct URLs.
  * [app/Models/Project.php](file:///c:/Users/Nour/Desktop/G-S/app/Models/Project.php): Updated `coverUrl()` to check existence on and resolve URLs from the `r2` disk.
  * [app/Models/ProjectImage.php](file:///c:/Users/Nour/Desktop/G-S/app/Models/ProjectImage.php): Updated `url()` and `thumbnailUrl()` to resolve through the `r2` disk.

* **Views**:
  * Updated all references to `asset('storage/' . $portfolio->profile_picture_path)` to the dynamic `$portfolio->profilePictureUrl()` method in:
    * [resources/views/dashboard/index.blade.php](file:///c:/Users/Nour/Desktop/G-S/resources/views/dashboard/index.blade.php)
    * [resources/views/dashboard/projects/create.blade.php](file:///c:/Users/Nour/Desktop/G-S/resources/views/dashboard/projects/create.blade.php)
    * [resources/views/dashboard/projects/edit.blade.php](file:///c:/Users/Nour/Desktop/G-S/resources/views/dashboard/projects/edit.blade.php)
    * [resources/views/dashboard/projects/index.blade.php](file:///c:/Users/Nour/Desktop/G-S/resources/views/dashboard/projects/index.blade.php)
    * [resources/views/public/directory.blade.php](file:///c:/Users/Nour/Desktop/G-S/resources/views/public/directory.blade.php)
    * [resources/views/public/portfolio.blade.php](file:///c:/Users/Nour/Desktop/G-S/resources/views/public/portfolio.blade.php)

* **Database & Migrations**:
  * [database/migrations/2026_01_01_000024_sanitize_storage_paths.php](file:///c:/Users/Nour/Desktop/G-S/database/migrations/2026_01_01_000024_sanitize_storage_paths.php): Created a migration that sanitizes existing database records by stripping `/storage/` or `storage/` prefixes, leaving only clean relative paths (e.g. `profiles/xyz.webp`).

* **Tests**:
  * [tests/Feature/ImageOptimizationSystemTest.php](file:///c:/Users/Nour/Desktop/G-S/tests/Feature/ImageOptimizationSystemTest.php): Updated mock disk assertions to fake and assert on the `r2` disk.
  * [tests/Feature/AdminDashboardExtensionsTest.php](file:///c:/Users/Nour/Desktop/G-S/tests/Feature/AdminDashboardExtensionsTest.php): Updated CV secure download test assertions to use the `r2_private` disk.

---

## 2. Storage Architecture

We configured a dual-disk architecture in Laravel (`config/filesystems.php`) pointing to the same Cloudflare R2 bucket:

1. **`r2` (Public Access Disk)**:
   * Used for avatars, project covers, and gallery images.
   * Generates URLs using the public domain prefix (`AWS_URL` mapping to the Cloudflare Custom Domain or R2 subdomain).
2. **`r2_private` (Private Access Disk)**:
   * Used for CV uploads.
   * Does not have a public URL prefix. Files are retrieved from R2 and streamed to authorized users in chunks on the server side via standard controller downloads.

### Local Development Fallback
Both `r2` and `r2_private` disks contain fallback definitions. If no `AWS_ACCESS_KEY_ID` is present in the environment (e.g. on a local machine or during CI testing), they revert to local storage (`public` and `private` disks respectively). This ensures **zero setup** is required to run the codebase locally.

---

## 3. Security Decisions

* **CV File Protection**: CVs contain personal contact info. To prevent unauthorized direct downloads from the R2 custom domain, they are requested via the `/p/{slug}/cv` endpoint, which performs authorization checks and rate-limiting before reading and streaming the file securely.
* **Unguessable Hashes**: Files uploaded to R2 are automatically assigned UUID filenames (e.g. `profiles/de305d54-75b4-431b-adb2-eb6b9e546013.webp`), preventing attackers from enumerating or guessing URLs of other assets.

---

## 4. Migration & Backward Compatibility Notes

* **Clean Database Paths**: Previously, local paths in the database were sometimes stored as `/storage/profiles/...` or `storage/profiles/...`. A data sanitation migration has been added to automatically strip these prefixes from the database.
* **Dynamic URL Helper**: The `profilePictureUrl()`, `coverUrl()`, and `url()` methods dynamically check if a path is already a full URL or is relative, strip the legacy prefixes if they exist, and format the output URL using the active disk config.

---

## 5. Production Readiness Status

* **Status**: **PRODUCTION READY**
* **Verification**: All automated tests (`ImageOptimizationSystemTest`, `AdminDashboardExtensionsTest`) pass cleanly locally.
* **Deployment Instructions**: Once the repository is pushed, the user must update their Railway environment variables with R2 details, and run migrations via:
  ```bash
  php artisan migrate --force
  ```
