<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Creativa Hub Alexandria - Random Draw</title>
    <link rel="stylesheet" href="{{ asset('creativa-assets/style.css') }}">
</head>
<body>
    <header class="page-header">
        <div class="logo-banner-top">
            <img src="{{ asset('creativa-assets/creativa.png') }}" alt="Creativa Hub Alexandria Project Partners">
        </div>
    </header>

    <div class="container screen-container">
        <div class="glass-card">
            <h1>Winner Draw</h1>
            <div class="display-area">
                <div id="result-container" class="result-container">
                    <div id="display-name" class="name-display">---</div>
                    <div id="display-department" class="dept-display">---</div>
                    <div id="display-question" class="question-display">"???"</div>
                </div>
                <div id="counter">Loading pool stats...</div>
                <button id="draw-btn">🎲 Start Random Draw</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('creativa-assets/draw.js') }}"></script>
</body>
</html>
