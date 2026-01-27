<?php

$sourceDir = __DIR__;
$targetDir = __DIR__ . '/app/modules/ui/Views';
$backupDir = __DIR__ . '/legacy_backup';

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

$files = glob($sourceDir . '/*.html');

foreach ($files as $file) {
    if (basename($file) === 'index.html') {
        // index.html is likely the dashboard, already handled by Home module, but let's migrate it as 'dashboard_legacy' or similar just in case,
        // OR migrate it to 'index_legacy.php'.
        // Actually, user wants "all html pages... should work".
        // Let's migrate it too.
    }

    $content = file_get_contents($file);

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $nodes = $xpath->query('//*[contains(@class, "main-content")]');

    $finalContent = '';

    if ($nodes->length > 0) {
        $mainContentNode = $nodes->item(0);
        // We want the CHILDREN of main-content
        // Actually, main-content contains section__content usually.
        // base.html.php has:
        /*
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <?= $content ?? '' ?>
                    </div>
                </div>
            </div>
        */
        // So effectively we want the content inside <div class="container-fluid"> INSIDE <div class="section__content"> INSIDE <div class="main-content">.
        // Let's try to find that specific container-fluid.

        $innerNodes = $xpath->query('.//*[contains(@class, "section__content")]/*[contains(@class, "container-fluid")]', $mainContentNode);

        if ($innerNodes->length > 0) {
            $containerNode = $innerNodes->item(0);
            foreach ($containerNode->childNodes as $child) {
                $finalContent .= $dom->saveHTML($child);
            }
        } else {
            // Fallback: just take children of main content node
            foreach ($mainContentNode->childNodes as $child) {
                $finalContent .= $dom->saveHTML($child);
            }
        }
    } else {
        // Fallback if no main-content found (e.g. login.html might be different)
        // Login.html usually has .login-content
        $nodesLogin = $xpath->query('//*[contains(@class, "login-content")]');
        if ($nodesLogin->length > 0) {
            // For login, we might want the whole thing or just the form.
            // base.html.php wraps content in .main-content -> .section -> .container.
            // Login page css might break if wrapped in that.
            // But let's extract it anyway.
            foreach ($nodesLogin->item(0)->childNodes as $child) {
                $finalContent .= $dom->saveHTML($child);
            }
        } else {
            // Just take body content?
            // Use body content
            $body = $dom->getElementsByTagName('body')->item(0);
            if ($body) {
                foreach ($body->childNodes as $child) {
                    $finalContent .= $dom->saveHTML($child);
                }
            } else {
                $finalContent = $content; // Worst case
            }
        }
    }

    // 2. Fix Assets
    // images/ -> /SGA-SEBANA/public/assets/img/
    $finalContent = str_replace('src="images/', 'src="/SGA-SEBANA/public/assets/img/', $finalContent);
    $finalContent = str_replace('href="images/', 'href="/SGA-SEBANA/public/assets/img/', $finalContent); // for favicons or links
    $finalContent = str_replace('url(\'images/', 'url(\'/SGA-SEBANA/public/assets/img/', $finalContent);
    $finalContent = str_replace('url("images/', 'url("/SGA-SEBANA/public/assets/img/', $finalContent);

    // css/ -> /SGA-SEBANA/public/assets/css/ (though usually css links are in <head> which is stripped)
    // But inline styles or specific things might exist.

    // 3. Fix Links
    // href="something.html" -> href="/SGA-SEBANA/public/ui/something"
    $finalContent = preg_replace('/href="([a-zA-Z0-9_-]+)\.html"/', 'href="/SGA-SEBANA/public/ui/$1"', $finalContent);

    // Special case for index.html -> /SGA-SEBANA/public/home or /SGA-SEBANA/public/ui/index
    $finalContent = str_replace('href="/SGA-SEBANA/public/ui/index"', 'href="/SGA-SEBANA/public/home"', $finalContent);

    // 4. Wrap in Output Buffering
    $phpContent = "<?php\nob_start();\n?>\n\n" . $finalContent . "\n\n<?php\n\$content = ob_get_clean();\nrequire BASE_PATH . '/public/templates/base.html.php';\n?>";

    // 5. Save
    $filename = basename($file, '.html');
    file_put_contents($targetDir . '/' . $filename . '.php', $phpContent);

    // 6. Move Original
    rename($file, $backupDir . '/' . basename($file));

    echo "Migrated: " . $filename . "\n";
}
