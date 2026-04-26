document.getElementById('submission-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('submit-btn');
    const name = document.getElementById('name').value;
    const department = document.getElementById('department').value;
    const question = document.getElementById('question').value;

    submitBtn.disabled = true;
    submitBtn.innerText = 'Submitting...';

    const submission = {
        name: name,
        department: department,
        question: question
    };

    try {
        const response = await fetch('/api/creativa/submit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(submission)
        });

        if (response.ok) {
            // Show success message
            document.getElementById('form-container').style.display = 'none';
            document.getElementById('success-container').style.display = 'block';
        } else {
            alert('Failed to submit. Please try again.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Network error. Is the server running?');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerText = '🚀 Submit Question';
    }
});

function resetForm() {
    document.getElementById('submission-form').reset();
    document.getElementById('form-container').style.display = 'block';
    document.getElementById('success-container').style.display = 'none';
}
