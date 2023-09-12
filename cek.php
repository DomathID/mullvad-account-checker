<?php
$fileName = 'accounts.txt';
$accounts = file($fileName, FILE_IGNORE_NEW_LINES);

$proxyList = [
    'ip.proxy.com:8080',
    'ip.proxy.com:8080',
    'ip.proxy.com:8080',
];

foreach ($accounts as $account) {
    $url = 'https://api.mullvad.net/public/accounts/v1/' . $account;
    $proxy = $proxyList[array_rand($proxyList)];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_PROXY, $proxy);

    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);

    if (isset($responseData['id']) && isset($responseData['expiry'])) {
        $accountToken = $responseData['id'];
        $expires = $responseData['expiry'];

        if ($accountToken !== '') {
            echo "Akun Valid: " . $accountToken . "\n";
            $today = date('Y-m-d');
            if (strtotime($expires) >= strtotime($today)) {
                echo "Status: Active\n";
            } else {
                echo "Status: Expired\n";
            }

            echo "Expired Date: " . $expires . "\n";
        } else {
            echo "Akun Not Found\n";
        }
    } else {
        echo "Account: " . $account . " Not Found\n";
    }

    sleep(5);
}

