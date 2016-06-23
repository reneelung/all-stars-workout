<?php

    require('lib.php');
    const URL = 'https://api.imgflip.com/caption_image';

    $payload = $_POST['text'];
    $token = $_POST['token'];
    $respond_to = $_POST['response_url'];
    $phrases = extract_phrases($_POST);

    if (!$phrases) {
        $phrases = generate_phrases();
    }

    $data = img_request_data($phrases);

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ),
    );

    $context  = stream_context_create($options);
    $result = json_decode(file_get_contents(URL, false, $context), true);

    if (!$result['success'] && is_slack($token)) {
        echo "very sadness, so cry";
        exit;
    } else if (!$result['success']) {
        echo "<html><body><img src='https://media.giphy.com/media/JEVqknUonZJWU/giphy.gif'></body></html>";
        exit;
    } else if ($result['success'] && is_slack($token)) {
        $doge_says = array(
            'response_type' => 'in_channel',
            'text' => 'So Doge, Very Slack!',
            'attachments' => array(
                array (
                    'fallback' => 'Very Doge!',
                    'title' => preg_replace('/,/g', ', ', $payload),
                    'title_link' => $result['data']['page_url'],
                    'image_url' => $result['data']['url']
                )
            )
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $respond_to);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($doge_says));

        $response = curl_exec($ch);
    } else {
        echo "<html><body><img src='{$result['data']['url']}'></body></html>";
    }
?>
