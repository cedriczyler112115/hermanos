<?php
echo "Trying various YouTube RSS formats...\n";

$urls = [
    'https://www.youtube.com/feeds/videos.xml?channel_id=UC0jDnZ1GfhkQLOAmYPH0V_g',
    'https://www.youtube.com/feeds/videos.xml?channel_id=UUp2rz0j0RrA5MmQW78Z3_qg',
    'https://www.youtube.com/feeds/videos.xml?user=CantoresHermanos1999',
    'https://www.youtube.com/feeds/videos.xml?handle=@CantoresHermanos1999',
    'https://www.youtube.com/feeds/videos.xml?for=CantoresHermanos1999',
];

foreach ($urls as $url) {
    echo "\nURL: $url\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200 && strlen($response) > 100) {
        echo "HTTP $httpCode - Response length: " . strlen($response) . "\n";
        if (strpos($response, '<entry>') !== false) {
            echo "SUCCESS - Found entries!\n";
            preg_match_all('/<entry>.*?<\/entry>/s', $response, $matches);
            echo "Entry count: " . count($matches[0]) . "\n";
            break;
        }
    } else {
        echo "HTTP $httpCode\n";
    }
}
