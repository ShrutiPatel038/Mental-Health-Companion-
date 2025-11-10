
<?php

header('Content-Type: application/json');
require 'db.php';

if (!isset($_COOKIE['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$conn = get_db();

$userId = (int)$_COOKIE['user_id'];
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

    case 'get_journal_by_date':
        // The date will come from the frontend as 'YYYY-MM-DD'
        $date = $_POST['date'] ?? '';
        
        if (empty($date)) {
            echo json_encode(['success' => false, 'message' => 'Date is required.']);
            exit();
        }

        // Prepare the query to find entries for the start and end of the given day
        $startDate = $date . ' 00:00:00';
        $endDate = $date . ' 23:59:59';
        
        $stmt = $conn->prepare("SELECT entry, date FROM journals WHERE user_id = ? AND date BETWEEN ? AND ? ORDER BY date DESC");
        $stmt->bind_param("iss", $userId, $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $entries = [];
        while ($row = $result->fetch_assoc()) {
            $entries[] = [
                'entry' => $row['entry'],
                // Format the time for display
                'time' => date('h:i A', strtotime($row['date']))
            ];
        }
        
        echo json_encode(['success' => true, 'entries' => $entries]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        break;
}
?>