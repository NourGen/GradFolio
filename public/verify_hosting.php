<?php
/**
 * GradFolio — cPanel Hosting Compatibility Verifier
 * Visit this file in the browser after uploading to public_html.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$requiredPhpVersion = '8.3.0';
$requiredExtensions = [
    'fileinfo'  => 'Required for validating MIME types of profile pictures and projects.',
    'gd'        => 'Required for Intervention Image v4 GD driver to resize and convert uploads.',
    'pdo_mysql' => 'Required to connect to the MySQL database.',
    'mbstring'  => 'Required for string helpers and unicode characters support.',
    'openssl'   => 'Required for application encryption and secure cookie hashing.',
    'curl'      => 'Required for API requests and background notifications.',
    'zip'       => 'Required for extracting deployment ZIP files on the server.'
];

// Check PHP Version
$phpVersion = PHP_VERSION;
$versionOk = version_compare($phpVersion, $requiredPhpVersion, '>=');

// Check Extensions
$extensionResults = [];
foreach ($requiredExtensions as $ext => $desc) {
    $extensionResults[$ext] = [
        'enabled' => extension_loaded($ext),
        'desc' => $desc
    ];
}

// Check Directory Permissions
$baseDir = dirname(__DIR__);
$pathsToCheck = [
    'Storage (recursively)' => $baseDir . '/storage',
    'Bootstrap Cache' => $baseDir . '/bootstrap/cache',
    'Public Web Root' => __DIR__
];
$pathResults = [];
foreach ($pathsToCheck as $label => $path) {
    $exists = file_exists($path);
    $writable = $exists && is_writable($path);
    $pathResults[$label] = [
        'exists' => $exists,
        'writable' => $writable,
        'path' => $path
    ];
}

// Check Symlink Support
$symlinkOk = false;
$symlinkMsg = 'Untested';
$testTarget = __FILE__;
$testLink = __DIR__ . '/test_symlink_temp';
if (file_exists($testLink)) {
    @unlink($testLink);
}
try {
    $symlinkOk = @symlink($testTarget, $testLink);
    if ($symlinkOk) {
        $symlinkMsg = 'Supported and fully enabled!';
        @unlink($testLink);
    } else {
        $error = error_get_last();
        $symlinkMsg = 'Failed to create symlink: ' . ($error['message'] ?? 'Permission denied');
    }
} catch (\Throwable $e) {
    $symlinkMsg = 'Error checking symlinks: ' . $e->getMessage();
}

// Check Shell Command Execution (for Composer/Artisan)
$disabledFunctions = explode(',', ini_get('disable_functions'));
$disabledFunctions = array_map('trim', $disabledFunctions);
$execOk = !in_array('exec', $disabledFunctions) && function_exists('exec');
$shellExecOk = !in_array('shell_exec', $disabledFunctions) && function_exists('shell_exec');

// Final Verdict calculation
$allOk = $versionOk;
foreach ($extensionResults as $r) {
    if (!$r['enabled']) $allOk = false;
}
foreach ($pathResults as $r) {
    if ($r['exists'] && !$r['writable']) $allOk = false;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cPanel Hosting Compatibility Verifier — GradFolio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #C4783A;
            --bg: #1A0E08;
            --bg-end: #0D0704;
            --surface: #251508;
            --surface-light: #32200F;
            --border: rgba(196, 120, 58, 0.2);
            --text: #FAF7F2;
            --text-muted: #9C7B65;
            --success: #4ade80;
            --danger: #f87171;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(180deg, var(--bg) 0%, var(--bg-end) 100%);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            padding: 3rem 1.5rem;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        header {
            text-align: center;
            margin-bottom: 2.5rem;
            border-bottom: 1px solid var(--border);
            padding-bottom: 1.5rem;
        }
        h1 {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: 2.2rem;
            color: var(--text);
            margin-bottom: 0.5rem;
        }
        .subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        .status-card {
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
            font-weight: bold;
            font-size: 1.2rem;
            border: 1px solid;
        }
        .status-card.pass {
            background: rgba(74, 222, 128, 0.1);
            border-color: var(--success);
            color: var(--success);
        }
        .status-card.fail {
            background: rgba(248, 113, 113, 0.1);
            border-color: var(--danger);
            color: var(--danger);
        }
        h2 {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: 1.5rem;
            margin: 1.8rem 0 1rem;
            color: var(--primary);
            border-bottom: 1px solid rgba(196,120,58,0.1);
            padding-bottom: 0.3rem;
        }
        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.03);
        }
        .row:last-child { border-bottom: none; }
        .label {
            font-weight: 500;
        }
        .desc {
            font-size: 0.8rem;
            color: var(--text-muted);
            display: block;
            margin-top: 0.1rem;
        }
        .badge {
            padding: 0.25rem 0.6rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .badge.pass { background: var(--success); color: #000; }
        .badge.fail { background: var(--danger); color: #fff; }
        .warning-box {
            background: rgba(196, 120, 58, 0.08);
            border-left: 4px solid var(--primary);
            padding: 1rem;
            border-radius: 0 8px 8px 0;
            margin: 1.5rem 0;
            font-size: 0.88rem;
        }
        footer {
            text-align: center;
            margin-top: 3rem;
            color: var(--text-muted);
            font-size: 0.8rem;
        }
    </div>
</body>
</html>
