import './bootstrap'; import './game-ticker';
import { createApp, h } from 'vue'
import Statistics from './Components/GameStatistics.vue'
import ProjectProgress from './Components/ProjectProgress.vue'
import GameCard from './Components/GameCard.vue'

//Games's statistics
const el = document.getElementById('statistics-wrapper')
if (el) {
    createApp(Statistics, {
        gameId: el.dataset.gameId,
        initialGame: JSON.parse(el.dataset.game)
    }).mount(el)
}

//progress bar for projects
document.querySelectorAll('.progress-vue').forEach(el => {
    const projectId = el.dataset.projectId
    const gameId = el.dataset.gameId
    const gameState = el.dataset.gameState
    const initialComplexity = Number(el.dataset.initialComplexity)
    const initialComplexityDb = Number(el.dataset.initialComplexityDb)
    createApp(ProjectProgress, {
        projectId,
        gameId,
        initialComplexity,
        initialComplexityDb,
        gameState,
    }).mount(el)
})

//game's card for the index page
document.querySelectorAll('.game-card-vue').forEach(el => {
    createApp(GameCard, {
        id: el.dataset.gameId,
        name: el.dataset.gameName,
        patrimonio: Number(el.dataset.gamePatrimonio),
        state: el.dataset.gameState,
    }).mount(el)
})