const drawBtn = document.getElementById('draw-btn');
const resultContainer = document.getElementById('result-container');
const displayName = document.getElementById('display-name');
const displayDept = document.getElementById('display-department');
const displayQuestion = document.getElementById('display-question');
const counterDisplay = document.getElementById('counter');

async function updateCounter() {
    try {
        const response = await fetch('/api/creativa/submissions/pool');
        const pool = await response.json();
        
        const allResponse = await fetch('/api/creativa/submissions');
        const all = await allResponse.json();
        
        counterDisplay.innerText = `${pool.length} questions remaining in pool (Total: ${all.length})`;
        return pool;
    } catch (error) {
        console.error('Error fetching pool:', error);
        counterDisplay.innerText = 'Error connecting to database';
        return [];
    }
}

updateCounter();

drawBtn.addEventListener('click', async function() {
    const pool = await updateCounter();

    if (pool.length === 0) {
        drawBtn.innerText = "No Questions Available!";
        setTimeout(() => drawBtn.innerText = "🎲 Start Random Draw", 3000);
        return;
    }

    // Start Draw Animation
    drawBtn.disabled = true;
    drawBtn.innerText = "Drawing...";
    resultContainer.classList.remove('active');
    
    // Simulate shuffling/spinning
    let shuffleCount = 0;
    const maxShuffle = 20;
    const interval = setInterval(() => {
        const tempIndex = Math.floor(Math.random() * pool.length);
        const temp = pool[tempIndex];
        
        displayName.innerText = temp.name;
        displayDept.innerText = temp.department;
        displayQuestion.innerText = "???";
        
        shuffleCount++;
        if (shuffleCount >= maxShuffle) {
            clearInterval(interval);
            finalizeDraw(pool);
        }
    }, 100);
});

async function finalizeDraw(pool) {
    const randomIndex = Math.floor(Math.random() * pool.length);
    const selected = pool[randomIndex];

    try {
        // Mark as used in DB
        await fetch(`/api/creativa/submissions/mark-used/${selected.id}`, { 
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        // Display Result
        displayName.innerText = selected.name;
        displayDept.innerText = selected.department;
        displayQuestion.innerText = `"${selected.question}"`;
        
        resultContainer.classList.add('active');
        
        drawBtn.disabled = false;
        drawBtn.innerText = "🎲 Draw Next Random";
        
        updateCounter();
    } catch (error) {
        console.error('Error finalizing draw:', error);
        alert('Failed to save draw status to server.');
        drawBtn.disabled = false;
    }
}

// Auto refresh pool counter every 10 seconds
setInterval(updateCounter, 10000);

// Window focus listener to refresh counter
window.addEventListener('focus', updateCounter);
