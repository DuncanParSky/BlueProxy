# BlueProxy
**BlueProxy** is an unfinished and discontinued proxy written in PHP.
**BlueProxy** has no affiliation with **Blue Proxy: proxy browser VPN**

**BlueProxy** is a PHP-based web proxy script that allows you to fetch and display content from remote URLs while acting as an intermediary. This script enables you to proxy web content, including HTML, images, stylesheets, and scripts, and serves as a starting point for creating web proxies or content transformation tools.

## How **BlueProxy** Works
**BlueProxy** intercepts incoming HTTP requests and processes them based on the provided query parameters. It supports two main modes of operation:

Proxy URL Content (url Parameter): When the url parameter is provided in the query string, **BlueProxy** fetches the content of the specified URL and performs the following tasks:

Replaces all occurrences of the original URL with the proxy URL to ensure that all subsequent requests are directed through the proxy.
Processes HTML content to update image URLs, CSS file URLs, script URLs, and anchor URLs, so they are also proxied through the script.
Proxy File Content (file Parameter): When the file parameter is provided in the query string, **BlueProxy** fetches the content of the specified file URL and serves it with the appropriate Content-Type header.

## Proxying URLs
**BlueProxy** utilizes cURL to fetch the content of URLs and DOM manipulation to update URLs within the fetched content. It performs the following tasks:

Fetching Content: The fetchContent($url) function uses cURL to retrieve the content of a given URL. It follows redirects and returns the fetched content.

Updating URLs: The updateURLs($content, $baseUrl) function parses the fetched content using DOMDocument. It iterates through various HTML elements (images, stylesheets, scripts, and anchors) and updates their URLs to be proxied through the proxy.php script.

Resolving URLs: The resolveUrl($baseUrl, $url) function resolves relative URLs and handles different URL formats such as //example.com, /path/file.ext, ../folder/file.ext, ./file.ext, and fragment URLs (#section).

Content Types: The getContentType($fileExtension) function returns the appropriate Content-Type header based on the file extension. It supports common MIME types for CSS, JavaScript, images (JPG, JPEG, PNG, GIF), and falls back to text/plain for unknown types.

## Usage
To use **BlueProxy**, you need to include the proxy.php script in your web server's directory and access it through your web browser. You can provide either the url or file parameter in the query string to proxy the content from a remote URL or file URL, respectively.

## Examples
Proxying a URL:

To proxy the content of a remote URL, append the url parameter to the proxy script's URL:

http://your-domain.com/proxy.php?url=https://example.com

Proxying a File URL:

To proxy the content of a file URL, append the file parameter to the proxy script's URL:

http://your-domain.com/proxy.php?file=https://example.com/path/to/file.css

## Customization
The **BlueProxy** script is designed to be a starting point for building web proxies or content transformation tools. You can extend and customize the script according to your needs. Here are a few customization options:

Adding More MIME Types: You can modify the $mimeTypes array in the getContentType($fileExtension) function to support additional file types and their corresponding Content-Type headers.

Additional Processing: You can extend the script's capabilities by adding more processing to the fetched content or modifying the way URLs are handled.

Security Considerations: Keep in mind that a web proxy can be used to bypass security measures, so it's important to implement proper security measures and filtering to prevent misuse.

## Disclaimer
**BlueProxy** is provided as a basic example of a web proxy script. It may not cover all use cases, and its security and performance aspects may need further consideration and improvements.

Note: This documentation is intended as a guide and starting point for understanding and using the **BlueProxy** script. It's recommended to review and test the code thoroughly before deploying it in a production environment.
