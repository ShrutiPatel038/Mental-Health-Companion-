// script.js (Complete & Corrected)

document.addEventListener('DOMContentLoaded', () => {

    // --- 1. ELEMENT SELECTORS (Get all elements at the start) ---
    const moodForm = document.getElementById('mood-form');
    const journalForm = document.getElementById('journal-form');
    const showJournalForm = document.getElementById('show-journal-form');
    
    const moodHistoryList = document.getElementById('mood-history');
    const journalEntriesContainer = document.getElementById('journal-entries-container');

    // --- 2. MOOD TRACKER LOGIC ---

    // Function to fetch and display mood history
    const fetchMoodHistory = async () => {
        // Only run this code if the mood history list actually exists on the page
        if (!moodHistoryList) return;

        const formData = new FormData();
        formData.append('action', 'get_mood_history');

        const response = await fetch('api.php', { method: 'POST', body: formData });
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

    // Event listener for the mood form
    if (moodForm) {
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
    }

    // --- 3. JOURNAL SUBMISSION LOGIC ---

    // Event listener for the journal submission form
    if (journalForm) {
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
    }

    // --- 4. SHOW JOURNAL ENTRIES BY DATE LOGIC ---

    // Event listener for the "Show Entries" form
    if (showJournalForm) {
        showJournalForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const dateInput = document.getElementById('journal-date');
            const selectedDate = dateInput.value;

            if (!selectedDate) {
                alert('Please select a date.');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'get_journal_by_date');
            formData.append('date', selectedDate);

            journalEntriesContainer.innerHTML = '<p>Loading entries...</p>';
            
            const response = await fetch('api.php', { method: 'POST', body: formData });
            const data = await response.json();

            journalEntriesContainer.innerHTML = '';

            if (data.success && data.entries.length > 0) {
                data.entries.forEach(item => {
                    const entryDiv = document.createElement('div');
                    entryDiv.classList.add('journal-entry-item');
                    
                    const entryText = document.createElement('p');
                    entryText.textContent = item.entry;

                    const entryTime = document.createElement('small');
                    entryTime.textContent = `Saved at ${item.time}`;

                    entryDiv.appendChild(entryText);
                    entryDiv.appendChild(entryTime);
                    journalEntriesContainer.appendChild(entryDiv);
                });
            } else if (data.success) {
                journalEntriesContainer.innerHTML = '<p>No entries found for this date.</p>';
            } else {
                journalEntriesContainer.innerHTML = `<p class="error">${data.message || 'Could not fetch entries.'}</p>`;
            }
        });
    }

    // --- 5. INITIAL DATA LOAD ---
    // Fetch initial mood history when the dashboard loads
    fetchMoodHistory();

});