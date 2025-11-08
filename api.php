<?php
// api.php
session_start();
header('Content-Type: application/json');
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$conn = get_db();
$userId = (int)$_SESSION['user_id'];
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'submit_mood':
        $moodValue = (int)($_POST['mood'] ?? 0);
        if ($moodValue >= 1 && $moodValue <= 5) {
            $stmt = $conn->prepare("INSERT INTO moods (user_id, value) VALUES (?, ?)");
            $stmt->bind_param("ii", $userId, $moodValue);
            $stmt->execute();
            echo json_encode(['success' => true, 'message' => 'Mood saved!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid mood value.']);
        }
        break;

    case 'get_mood_history':
        $stmt = $conn->prepare("SELECT value, date FROM moods WHERE user_id = ? ORDER BY date DESC LIMIT 7");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $history = [];
        while ($row = $result->fetch_assoc()) {
            $history[] = [
                'value' => (int)$row['value'],
                'date' => date('M d, Y', strtotime($row['date']))
            ];
        }
        echo json_encode(['success' => true, 'history' => $history]);
        break;

    case 'submit_journal':
        $entry = trim($_POST['entry'] ?? '');
        if (!empty($entry)) {
            $stmt = $conn->prepare("INSERT INTO journals (user_id, entry) VALUES (?, ?)");
            $stmt->bind_param("is", $userId, htmlspecialchars($entry));
            $stmt->execute();
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