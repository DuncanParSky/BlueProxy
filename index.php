<?php

// Check if a URL is provided
if (isset($_GET['url'])) {
    $url = $_GET['url'];

    // Fetch the content of the URL
    $content = fetchContent($url);

    // Replace all occurrences of the original URL with the proxy URL
    $content = updateURLs($content, $url);

    // Forward the content with appropriate headers
    header('Content-Type: text/html');
    echo $content;
    exit;
}

// Check if a file URL is provided
if (isset($_GET['file'])) {
    $fileUrl = $_GET['file'];

    // Fetch the content of the file
    $fileContent = fetchContent($fileUrl);

    // Forward the content with appropriate headers
    $fileExtension = pathinfo($fileUrl, PATHINFO_EXTENSION);
    $contentType = getContentType($fileExtension);
    header("Content-Type: $contentType");
    echo $fileContent;
    exit;
}

function fetchContent($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}

function updateURLs($content, $baseUrl)
{
    $dom = new DOMDocument;
    libxml_use_internal_errors(true);
    $dom->loadHTML($content);
    libxml_clear_errors();

    // Update image URLs
    $images = $dom->getElementsByTagName('img');
    foreach ($images as $image) {
        $src = $image->getAttribute('src');
        if (!filter_var($src, FILTER_VALIDATE_URL)) {
            $src = resolveUrl($baseUrl, $src);
        }
        $image->setAttribute('src', 'proxy.php?file=' . urlencode($src));
    }

    // Update CSS URLs
    $links = $dom->getElementsByTagName('link');
    foreach ($links as $link) {
        if ($link->getAttribute('rel') === 'stylesheet') {
            $href = $link->getAttribute('href');
            if (!filter_var($href, FILTER_VALIDATE_URL)) {
                $href = resolveUrl($baseUrl, $href);
            }
            $link->setAttribute('href', 'proxy.php?file=' . urlencode($href));
        }
    }

    // Update script URLs
    $scripts = $dom->getElementsByTagName('script');
    foreach ($scripts as $script) {
        $src = $script->getAttribute('src');
        if (!empty($src) && !filter_var($src, FILTER_VALIDATE_URL)) {
            $src = resolveUrl($baseUrl, $src);
            $script->setAttribute('src', 'proxy.php?file=' . urlencode($src));
        }
    }

    // Update anchor URLs
    $anchors = $dom->getElementsByTagName('a');
    foreach ($anchors as $anchor) {
        $href = $anchor->getAttribute('href');
        if (!empty($href) && !filter_var($href, FILTER_VALIDATE_URL)) {
            $href = resolveUrl($baseUrl, $href);
            $anchor->setAttribute('href', 'proxy.php?url=' . urlencode($href));
        }
    }

    return $dom->saveHTML();
}

function resolveUrl($baseUrl, $url)
{
    if (strpos($url, '//') === 0) {
        $parsedUrl = parse_url($baseUrl);
        $scheme = $parsedUrl['scheme'];
        $url = $scheme . ':' . $url;
    } elseif (strpos($url, '/') === 0) {
        $parsedUrl = parse_url($baseUrl);
        $url = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $url;
    } elseif (strpos($url, '../') === 0) {
        $baseUrl = rtrim($baseUrl, '/');
        while (strpos($url, '../') === 0) {
            $baseUrl = substr($baseUrl, 0, strrpos($baseUrl, '/'));
            $url = substr($url, 3);
        }
        $url = $baseUrl . '/' . $url;
    } elseif (strpos($url, './') === 0) {
        $baseUrl = rtrim($baseUrl, '/');
        $url = $baseUrl . substr($url, 1);
    } elseif (strpos($url, '#') === 0) {
        $parsedUrl = parse_url($baseUrl);
        $url = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'] . $url;
    } elseif (!preg_match('/^https?:\/\//i', $url)) {
        $parsedUrl = parse_url($baseUrl);
        $url = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . dirname($parsedUrl['path']) . '/' . $url;
    }

    return $url;
}

function getContentType($fileExtension)
{
    // Add more MIME types as needed
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif'
    ];

    return isset($mimeTypes[$fileExtension]) ? $mimeTypes[$fileExtension] : 'text/plain';
}

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
    <title>Web Proxy</title>
    <style>
        body {
            margin: 0;
            overflow: hidden;
            font-family: "Roboto", Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        input[type="text"],
        button {
            margin: 10px;
        }
    </style>
    <script>
        // Add your custom JavaScript code here
    </script>
</head>
<body>
    <link rel="stylesheet" href="index.css">
    <form action="proxy.php" method="get">
        <label for="url">Enter URL:</label>
        <input type="text" id="url" name="url" placeholder="https://example.com" required>
        <button type="submit">Go</button>
    </form>

    <!-- Load CSS and JavaScript files from the entered URL -->
    <?php if (isset($_GET['url'])): ?>
        <?php
        $url = $_GET['url'];

        // Fetch the content of the URL
        $content = fetchContent($url);

        // Replace all occurrences of the original URL with the proxy URL
        $content = updateURLs($content, $url);

        // Forward the content with appropriate headers
        header('Content-Type: text/html');
        echo $content;
        ?>
    <?php endif; ?>

    <!-- Load CSS and JavaScript files from the current URL -->
    <?php
    $currentUrl = $_SERVER['REQUEST_URI'];
    $currentContent = fetchContent($currentUrl);
    $currentContent = updateURLs($currentContent, $currentUrl);
    echo $currentContent;
    ?>
</body>
</html>
