<?php

if (!isset($_COOKIE['user_id'])) {
    header('Location: login.php');
    exit();
}

$dayOfYear = date('z'); // Get the day of the year (0-365)
$challenges = [
    "Take 5 deep, slow breaths, focusing only on your breathing.",
    "Step outside for 2 minutes and notice something in nature.",
    "Write down one thing you are proud of yourself for.",
    "Stretch your arms, neck, and back for 60 seconds.",
    "Drink a full glass of water mindfully.",
    "Listen to one of your favorite calming songs without distractions."
];
$journalPrompts = [
    "What is one small joy you experienced today?",
    "Describe something you are looking forward to this week.",
    "Who is someone that made you smile recently, and why?",
    "What is a skill you have that you are thankful for?",
    "Write about a simple pleasure you often take for granted."
];
$dailyChallenge = $challenges[$dayOfYear % count($challenges)];
$dailyPrompt = $journalPrompts[$dayOfYear % count($journalPrompts)];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindful Moments Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
   
    <header>
        
        <h1>Mindful Moments</h1>
        <a href="logout.php">Logout</a>
        
    </header>

    <main class="container">
        <section class="card">
            <h2>Today's Mindful Challenge</h2>
            <p><?php echo $dailyChallenge; ?></p>
        </section>

        <section class="card">
            <h2>How are you feeling right now?</h2>
            <form id="mood-form">
                <div class="mood-selector">
                    <label>😞<input type="radio" name="mood" value="1" required></label>
                    <label>😕<input type="radio" name="mood" value="2"></label>
                    <label>😐<input type="radio" name="mood" value="3"></label>
                    <label>😊<input type="radio" name="mood" value="4"></label>
                    <label>😁<input type="radio" name="mood" value="5"></label>
                </div>
                <button type="submit">Save Mood</button>
            </form>
            <h3>Recent Moods:</h3>
            <ul id="mood-history"></ul>
        </section>

        <section class="card">
            <h2>Gratitude Journal</h2>
            <p class="prompt"><?php echo $dailyPrompt; ?></p>
            <form id="journal-form">
                <textarea id="journal-entry" placeholder="Write your thoughts here..." required></textarea>
                <button type="submit">Save Entry</button>
            </form>
        </section>

        <section class="card">
            <h2>My Journal Entries</h2>
            <form id="show-journal-form">
                <label for="journal-date">Show entries for date:</label>
                <input type="date" id="journal-date" required>
                <button type="submit">Show Entries</button>
            </form>
            
            <!-- This is where the results will be displayed -->
            <div id="journal-entries-container" class="entries-container">
                <!-- Entries will be dynamically added here by JavaScript -->
            </div>
        </section>
    </main>


    <script src="script.js"></script>
</body>
</html>