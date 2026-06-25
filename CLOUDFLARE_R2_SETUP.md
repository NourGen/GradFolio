# Cloudflare R2 Storage Configuration Guide

This guide describes how to configure the **GradFolio** application to use **Cloudflare R2** for persistent user uploads (profile pictures, covers, project attachments, and CVs) when deploying on Railway.

---

## 1. Creating a Cloudflare R2 Bucket

1. Log in to your **Cloudflare Dashboard**.
2. Click on **R2** (or **Object Storage**) in the left sidebar.
3. Click **Create Bucket**.
4. Give your bucket a name (e.g., `gradfolio-storage`).
5. Choose **Automatic** location or select a region close to your target audience.
6. Click **Create bucket**.

---

## 2. Generating API Credentials

Laravel requires S3 API credentials (Access Key ID and Secret Access Key) to connect to Cloudflare R2.

1. In the R2 home dashboard (not inside the bucket), click **Manage R2 API Tokens** on the right side.
2. Click **Create API token**.
3. Configure the token:
   * **Token name**: `gradfolio-api-token`
   * **Permissions**: Select **Admin Read & Write** (or **Object Read & Write**).
   * **Bucket scope**: You can select **Apply to specific buckets** and select your `gradfolio-storage` bucket for maximum security.
4. Click **Create API Token**.
5. **CRITICAL**: Copy the following values immediately (they will not be shown again):
   * **Access Key ID** (corresponds to `AWS_ACCESS_KEY_ID`)
   * **Secret Access Key** (corresponds to `AWS_SECRET_ACCESS_KEY`)

---

## 3. Configuring Bucket Public Permissions (Custom Domains)

By default, R2 buckets are private. Public assets (profile pictures, project images) need a public URL to render in browsers.

1. Go back to your bucket (`gradfolio-storage`) in the Cloudflare dashboard.
2. Navigate to the **Settings** tab.
3. Scroll down to the **Public Access** section.
4. Choose one of the following:
   * **Connect a Custom Domain** (Recommended for production): Map a subdomain (e.g., `assets.b-s-a.co` or `assets.gradfolio.com`) to the bucket. Cloudflare will automatically provision an SSL certificate for it.
   * **R2.dev Subdomain**: Enable the default R2 subdomain provided by Cloudflare (e.g., `https://pub-xxx.r2.dev`). This is suitable for testing but has rate limits.
5. Copy the full URL of the custom domain or R2 subdomain (including `https://`). This corresponds to `AWS_URL`.

---

## 4. Required Environment Variables

Add these variables to your `.env` locally (for testing R2) or in your **Railway Service Settings** (for production):

```env
FILESYSTEM_DISK=r2

# AWS/R2 Credentials
AWS_ACCESS_KEY_ID=your_r2_access_key_id
AWS_SECRET_ACCESS_KEY=your_r2_secret_access_key
AWS_DEFAULT_REGION=auto
AWS_BUCKET=gradfolio-storage

# R2 Endpoint URL (Remove the bucket name from the end if present)
# Format: https://<cloudflare-account-id>.r2.cloudflarestorage.com
AWS_ENDPOINT=https://your_cloudflare_account_id.r2.cloudflarestorage.com

# Public Custom Domain / Subdomain pointing to the bucket (with trailing slash excluded)
# Format: https://assets.yourdomain.com OR https://pub-xxx.r2.dev
AWS_URL=https://pub-your-subdomain.r2.dev
```

> [!NOTE]
> On Railway, make sure to add these variables in the **Variables** tab of the `GradFolio` service panel. Railway will automatically redeploy the service once saved.

---

## 5. Security & Private Files (CVs)

* **Public Assets**: Profile photos and project covers/galleries are uploaded to the bucket and resolved using their public URLs via the `AWS_URL` prefix.
* **Private Assets**: Uploaded CV files (PDFs) are stored in the same bucket but are **not** accessed using public URLs. Instead, the application continues to download them securely via authenticated endpoints (e.g. `/p/{slug}/cv`). The controller streams the private file content on the backend using the S3 client, ensuring files remain protected.

---

## 6. Testing the Integration

### Test Uploads & URL Generation
1. Log in to a graduate dashboard (e.g. `sarah@example.com` / `password`).
2. Go to the dashboard and upload a profile picture.
3. Inspect the image element in your browser. The URL should point to your Cloudflare domain (e.g. `https://pub-your-subdomain.r2.dev/profiles/uuid.webp`).
4. Check your Cloudflare R2 bucket; you should see a `profiles/` folder containing the WebP image.

### Test Deletions
1. Delete a project or project image.
2. Confirm the file is removed from the R2 bucket.

---

## 7. Common Errors

### 1. `Driver [s3] is not supported`
* **Cause**: The Flysystem S3 package `league/flysystem-aws-s3-v3` is missing.
* **Solution**: Run `composer require league/flysystem-aws-s3-v3` (already included in our production dependencies).

### 2. `SignatureDoesNotMatch` or `403 Forbidden`
* **Cause**: Incorrect Access Key, Secret Key, or Account ID in the endpoint.
* **Solution**: Re-verify your credentials and ensure the Account ID in `AWS_ENDPOINT` is correct.

### 3. `InvalidRegion`
* **Cause**: R2 does not use standard AWS regions.
* **Solution**: Ensure `AWS_DEFAULT_REGION` is set to `auto`.
