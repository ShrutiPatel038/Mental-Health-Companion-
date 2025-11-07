<?php
// api.php
session_start();
header('Content-Type: application/json'); // We will always return JSON
require 'db.php';

// First, check if the user is logged in for all actions
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$db = get_db();
$userId = new MongoDB\BSON\ObjectId($_SESSION['user_id']);
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'submit_mood':
        $moodValue = (int)($_POST['mood'] ?? 0);
        if ($moodValue >= 1 && $moodValue <= 5) {
            $db->moods->insertOne([
                'userId' => $userId,
                'value' => $moodValue,
                'date' => new MongoDB\BSON\UTCDateTime()
            ]);
            echo json_encode(['success' => true, 'message' => 'Mood saved!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid mood value.']);
        }
        break;

    case 'get_mood_history':
        $moods = $db->moods->find(
            ['userId' => $userId],
            ['sort' => ['date' => -1], 'limit' => 7] // Get last 7 entries
        );
        $history = [];
        foreach ($moods as $mood) {
            $history[] = [
                'value' => $mood['value'],
                'date' => $mood['date']->toDateTime()->format('M d, Y')
            ];
        }
        echo json_encode(['success' => true, 'history' => $history]);
        break;

    case 'submit_journal':
        $entry = trim($_POST['entry'] ?? '');
        if (!empty($entry)) {
            $db->journals->insertOne([
                'userId' => $userId,
                'entry' => htmlspecialchars($entry), // Sanitize input
                'date' => new MongoDB\BSON\UTCDateTime()
            ]);
            echo json_encode(['success' => true, 'message' => 'Journal entry saved!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Journal entry cannot be empty.']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        break;
}
?>