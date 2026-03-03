<template>
    <div class="border-3 border-blue-100 bg-blue-950 rounded px-5 py-4 mx-50 flex items-center justify-between">
        <div class="font-semibold">
        <h2 class="text-2xl font-bold mb-6">Partita: {{ game.name }}</h2>
        <div>
            Patrimonio:
            <span
            class="game-patrimonio font-bold"
            :class="{
                'text-green-500': game.patrimonio > 0,
                'text-red-500': game.patrimonio <= 0
            }">
                € {{ formatPatrimonio(game.patrimonio) }}
            </span>
        </div>
        <div class="mt-1">
            Stato:
            <span
            class="game-stato font-bold"
            :class="{
                'text-yellow-600': game.state==='paused',
                'text-red-800': game.state==='finish',
                'text-green-300': game.state==='in_progress'
            }">
                {{ statoLabel }}
            </span>
        </div>
        </div>
        <div style="flex:1; text-align: right;">
        <form v-if="game.state==='paused'" :action="`/games/${game.id}/resume`" method="POST" style="display:inline;">
            <input type="hidden" name="_token" :value="csrf" />
            <button
            type="submit"
            class="bg-blue-200 text-black rounded py-2 px-6 font-bold hover:bg-blue-100"
            >Riprendi</button>
        </form>
        <span v-else-if="game.state==='finish'" class="bg-transparent py-2 px-6 font-bold">Game Over</span>
        <form v-else :action="`/games/${game.id}/pause`" method="POST" style="display:inline;">
            <input type="hidden" name="_token" :value="csrf" />
            <button
            type="submit"
            class="bg-blue-400 text-black rounded py-2 px-6 font-bold hover:bg-blue-100"
            >Pausa</button>
        </form>
        </div>
    </div>
</template>

<script setup>
    import { ref, onMounted, onUnmounted, computed } from 'vue'
    import axios from 'axios'

    const props = defineProps({
        gameId: [String, Number],
        initialGame: Object,
    })

    const game = ref({ ...props.initialGame })
    const csrf = document.querySelector('meta[name=csrf-token]')?.content || ''

    //state's label
    const statoLabel = computed(() => {
        if (game.value.state === 'paused') return 'In pausa'
        if (game.value.state === 'finish') return 'Terminata'
        if (game.value.state === 'in_progress') return 'In corso'
        return game.value.state
    })

    function formatPatrimonio(val) {
        return Number(val).toLocaleString('it-IT', { maximumFractionDigits: 0 })
    }

    // Polling to updat the datas
    let polling = null
    let pollingProj= null
    async function fetchProj() {
        if (game.value.state === 'in_progress')
            try {
                const res = await axios.get(`/games/${props.gameId}/update-all-projects`)
                game.value = res.data
            } catch { /* errors */ }
    }
    async function fetchStats() {
    if (game.value.state === 'in_progress')
        try {
            const res = await axios.get(`/api/games/${props.gameId}/statistics`)
            game.value = res.data
        } catch { /* errors */ }
    }
    onMounted(() => {
        pollingProj = setInterval(fetchProj,3000)
        polling = setInterval(fetchStats, 10000)
    })
    onUnmounted(() => clearInterval(polling))
</script>