// script.js
document.addEventListener('DOMContentLoaded', () => {
    const moodForm = document.getElementById('mood-form');
    const journalForm = document.getElementById('journal-form');
    const moodHistoryList = document.getElementById('mood-history');

    // Function to fetch and display mood history
    const fetchMoodHistory = async () => {
        const formData = new FormData();
        formData.append('action', 'get_mood_history');

        const response = await fetch('api.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        moodHistoryList.innerHTML = ''; // Clear previous list
        if (data.success && data.history.length > 0) {
            data.history.forEach(mood => {
                const li = document.createElement('li');
                const emoji = ['😞', '😕', '😐', '😊', '😁'][mood.value - 1];
                li.textContent = `${emoji} - ${mood.date}`;
                moodHistoryList.appendChild(li);
            });
        } else {
            moodHistoryList.innerHTML = '<li>No moods recorded yet.</li>';
        }
    };

    // Handle mood form submission
    moodForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(moodForm);
        formData.append('action', 'submit_mood');

        const response = await fetch('api.php', { method: 'POST', body: formData });
        const result = await response.json();

        if (result.success) {
            alert('Mood saved successfully!');
            moodForm.reset();
            fetchMoodHistory(); // Refresh the history list
        } else {
            alert('Error: ' + result.message);
        }
    });

    // Handle journal form submission
    journalForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const entry = document.getElementById('journal-entry').value;
        const formData = new FormData();
        formData.append('action', 'submit_journal');
        formData.append('entry', entry);
        
        const response = await fetch('api.php', { method: 'POST', body: formData });
        const result = await response.json();

        if (result.success) {
            alert('Journal entry saved!');
            journalForm.reset();
        } else {
            alert('Error: ' + result.message);
        }
    });

    // Initial load of mood history when the page loads
    fetchMoodHistory();
});