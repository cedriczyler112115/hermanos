<?php
echo "Testing CORS proxy approach...\n\n";

$channelId = 'UU0jDnZ1GfhkQLOAmYPH0V_g';
$youtubeUrl = "https://www.youtube.com/feeds/videos.xml?channel_id={$channelId}";

$proxyUrls = [
    "https://api.allorigins.win/raw?url=" . urlencode($youtubeUrl),
    "https://corsproxy.io/?" . urlencode($youtubeUrl),
    "https://api.codetabs.com/v1/proxy?quest=" . urlencode($youtubeUrl),
];

foreach ($proxyUrls as $url) {
    echo "Testing: $url\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "HTTP $httpCode - Length: " . strlen($response) . "\n";

    if ($httpCode === 200 && strlen($response) > 100) {
        if (strpos($response, '<entry>') !== false) {
            echo "SUCCESS - Found YouTube RSS entries!\n";
            $xml = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
            if ($xml && isset($xml->entry)) {
                echo "Entry count: " . count($xml->entry) . "\n";
                $entry = $xml->entry[0];
                echo "First title: " . $entry->title . "\n";
            }
            break;
        }
    }
    echo "\n";
}
