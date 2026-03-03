// Deprecated: This file/component is not used anymore.
// Kept for historical/reference purposes. May be removed in the future.

//two intervals, one every 10 seconds to update global assets and every 3 seconds to update project progress (if we are on the projects page)
/*let projectProgressInterval = null;
let updateInterval = null;

// Update project progress (if we are on the projects page)
function updateProjectProgress() {
    const projectCards = document.querySelectorAll('.project-card');
    //check if there are project cards on the page, if there aren't exit the function
    if (projectCards.length === 0) return;
    projectCards.forEach(card => {
        const projectId = card.dataset.projectId;
        const gameId = card.dataset.gameId;
        fetch(`/games/${gameId}/projects/${projectId}/progress`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                const initialComplexity = parseInt(card.dataset.initialComplexity) || 100;
                const remainingComplexity = data.complex;
                const percentage = Math.max(0, Math.min(100, ((initialComplexity - remainingComplexity) / initialComplexity * 100)));

                const progressBar = card.querySelector('.progress-bar');
                const progressPercentage = card.querySelector('.progress-percentage');
                const complexityValue = card.querySelector('.complexity-value');

                if (progressBar) {
                    progressBar.style.width = percentage + '%';
                }
                if (progressPercentage) {
                    progressPercentage.textContent = Math.round(percentage) + '%';
                }
                if (complexityValue) {
                    complexityValue.textContent = remainingComplexity;
                }

                // Se il progetto è completato, ricarica la pagina
                if (data.status === 'done') {
                    setTimeout(() => location.reload(), 500);
                }
            })
            .catch(error => console.error('Errore aggiornamento progresso:', error));
    });
}
//Updates projects globally (useful for when you're not in a game; make sure it also updates other games)
//Since tickall only takes the ones in done and processes them, it's missing the step to automatically send them to done.
function updateProjectProgressGlobal() {
    const gameCard = document.querySelector('[data-game-id]');
    if (gameCard) {
        const gameId = gameCard.dataset.gameId;
        fetch(`/games/${gameId}/update-all-projects`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .catch(error => console.error('Errore aggiornamento progetti globalmente:', error));
    }
}

// Update assets globally (useful for the games/index page) and the finish/pause/resume button
function updateAllGamePatrimonios() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    fetch(`/games/tick-all`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken || '',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            for (const gameId in data.games) {
                const gameData = data.games[gameId];
                const gameCard = document.querySelector(`[data-game-id="${gameId}"]`);
                //also the statistics components
                if (gameCard) {
                    // Update patrimonio
                    const patrSpan = gameCard.querySelector('.game-patrimonio');
                    if (patrSpan && gameData.patrimonio !== undefined) {
                        patrSpan.textContent = '€ ' + new Intl.NumberFormat('it-IT').format(Math.round(gameData.patrimonio));
                        patrSpan.classList.toggle('text-green-500', gameData.patrimonio > 0);
                        patrSpan.classList.toggle('text-red-500', gameData.patrimonio <= 0);
                    }
                    const statSpam = gameCard.querySelector('.game-stato');
                    if (statSpam && gameData.state !== undefined) {
                        statSpam.textContent = gameData.state === 'in_progress' ? 'In corso' : (gameData.state === 'paused' ? 'In pausa' : 'Terminato');
                        statSpam.classList.toggle('text-green-500', gameData.state === 'in_progress');
                        statSpam.classList.toggle('text-yellow-500', gameData.state === 'paused');
                        statSpam.classList.toggle('text-red-500', gameData.state === 'finish');
                    }

                    // Update button/status by id play-button
                    const actionDiv = gameCard.querySelector('.play-button');
                    if (actionDiv) {
                        if (gameData.state === 'paused') {
                            actionDiv.innerHTML = `
                    <form method="POST" action="/games/${gameId}/resume" style="display: inline">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <button type="submit" class="bg-blue-200 text-black rounded py-2 px-6 font-bold hover:bg-blue-100">
                            Riprendi
                        </button>
                    </form>
                `;
                        } else if (gameData.state === 'finish' || gameData.patrimonio <= 0) {
                            actionDiv.innerHTML = `
                    <span class="bg-transparent py-2 px-6 font-bold">
                        Game Over
                    </span>
                `;
                        } else {
                            actionDiv.innerHTML = `
                    <form method="POST" action="/games/${gameId}/pause" style="display: inline">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <button type="submit" class="bg-blue-400 text-black rounded py-2 px-6 font-bold hover:bg-blue-100">
                            Pausa
                        </button>
                    </form>
                `;
                        }
                    }
                }
            }
        })
}
        
function startAutoUpdates() {
    if (updateInterval === null && projectProgressInterval === null) {
        //updateProjectProgress();
        //updateAllGamePatrimonios();
        //updateProjectProgressGlobal()
        //autoProcaccia();

        //update updateAllGamePatrimonios every 10 seconds and updateProjectProgressGlobal() and updateProjectProgress every 3 seconds
        //updateInterval = setInterval(updateAllGamePatrimonios, 10000);
        projectProgressInterval = setInterval(() => {
            updateProjectProgress();
            updateProjectProgressGlobal();
            //autoProcaccia();
        }, 3000);

    }
}

function stopAutoUpdates() {
    if (updateInterval !== null) {
        clearInterval(updateInterval);
        updateInterval = null;
    }
    if (projectProgressInterval !== null) {
        clearInterval(projectProgressInterval);
        projectProgressInterval = null;
    }
}

// Initialize when the DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('game-container');
    if (container && container.dataset.gameState === 'in_progress') {
        startAutoUpdates();
        return;
    }
    // Check if there are any games in progress (index page)
    const gamesInProgress = document.querySelectorAll('[data-game-id][data-game-state="in_progress"]');
    if (gamesInProgress.length > 0) {
        startAutoUpdates();
    }
});
*/