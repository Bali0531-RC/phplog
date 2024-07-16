<?php
// Retrieve the posted data
$data = json_decode(file_get_contents('php://input'), true);

// Append data to user_data.json
$file = 'user_data.json';
if (file_exists($file)) {
    $currentData = json_decode(file_get_contents($file), true);
    $currentData[] = $data;
} else {
    $currentData = array($data);
}
file_put_contents($file, json_encode($currentData, JSON_PRETTY_PRINT));

// Prepare the Discord embed message
$fields = [];
foreach ($data as $key => $value) {
    $fields[] = [
        'name' => ucfirst(str_replace('_', ' ', $key)),
        'value' => $value,
        'inline' => false
    ];
}

$webhook_url = 'YOUR_DISCORD_WEBHOOK_URL';
$discord_message = [
    'embeds' => [
        [
            'title' => 'New user data logged',
            'fields' => $fields,
            'color' => hexdec('00FF00') // Green color
        ]
    ]
];

// Send the embed message to the webhook
$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($discord_message),
    ],
];
$context  = stream_context_create($options);
$result = file_get_contents($webhook_url, false, $context);

// Check if there is a referrer ID and send a ping message if present
if (!empty($data['referrer']) && $data['referrer'] !== 'no referrer') {
    // Extract the ID from the referrer field
    preg_match('/<@(\w+)>/', $data['referrer'], $matches);
    if (!empty($matches[1])) {
        $referrer_id = $matches[1];
        $ping_message = [
            'content' => "<@$referrer_id>"
        ];
        $ping_options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($ping_message),
            ],
        ];
        $ping_context  = stream_context_create($ping_options);
        file_get_contents($webhook_url, false, $ping_context);
    }
}

echo json_encode(['status' => 'success']);
?>
