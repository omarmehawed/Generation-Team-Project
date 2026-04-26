<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Creativa Hub Alexandria - Submit Question</title>
    <link rel="stylesheet" href="{{ asset('creativa-assets/style.css') }}">
</head>
<body>
    <header class="page-header">
        <div class="logo-banner-top">
            <img src="{{ asset('creativa-assets/creativa.png') }}" alt="Creativa Hub Alexandria Project Partners">
        </div>
    </header>

    <div class="container">
        <div class="glass-card" id="form-container">
            <h1>Innovation Tech Day</h1>
            <form id="submission-form">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" placeholder="Enter your full name" required>
                </div>
                <div class="form-group">
                    <label for="department">Department / Section</label>
                    <input type="text" id="department" placeholder="Enter your department" required>
                </div>
                <div class="form-group">
                    <label for="question">Your Question</label>
                    <textarea id="question" rows="4" placeholder="What would you like to ask?" required></textarea>
                </div>
                <button type="submit" id="submit-btn">🚀 Submit Question</button>
            </form>
        </div>

        <div class="glass-card success-message" id="success-container">
            <h2>Submission Received!</h2>
            <p>Thank you for participating. Your question has been added to the pool.</p>
            <button onclick="resetForm()">Submit Another</button>
        </div>
    </div>

    <script src="{{ asset('creativa-assets/submit.js') }}"></script>
</body>
</html>
