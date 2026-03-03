<template>
    <div style="display: flex; align-items: center; justify-content: space-between; border: 1px solid #aaa; border-radius: 12px; margin-bottom: 20px; padding: 16px; min-width: 400px;" data-game-id="{{ $game->id }}" data-game-state="{{ $game->state }}">
        <div style="flex:1; font-size: 1.25rem; font-weight: bold;">
            <a :href="`/games/${id}`" class="text-white hover:text-blue-300 transition-colors duration-150">
                {{ name }}
            </a>
        </div>
        <div style="flex:1; text-align: center;">  
            <span class="game-patrimonio text-2xl font-bold" :class="patrimonio > 0 ? 'text-green-500' : 'text-red-500'">
                € {{ formatPatrimonio(patrimonio) }}
            </span>
        </div>
        <div style="flex:1; text-align: right;">
            <form v-if="state === 'paused'" :action="`/games/${id}/resume`" method="POST" style="display: inline">
                <input type="hidden" name="_token" :value="csrf" />
                <button class="bg-blue-200 text-black rounded py-2 px-6 font-bold hover:bg-blue-100">
                Riprendi
                </button>
            </form>
            <span v-else-if="state === 'finish'" class="bg-transparent py-2 px-6 font-bold">Game Over</span>
            <form v-else :action="`/games/${id}/pause`" method="POST" style="display: inline">
                <input type="hidden" name="_token" :value="csrf" />
                <button class="bg-blue-400 text-black rounded py-2 px-6 font-bold hover:bg-blue-100">
                Pausa
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
    import { ref, onMounted, onUnmounted } from 'vue'
    import axios from 'axios'

    const props = defineProps({
        id: [Number, String],
        name: String,
        patrimonio: Number,
        state: String,
    })
    const patrimonio = ref(props.patrimonio)
    const state = ref(props.state)

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || ''

    function formatPatrimonio(val) {
    return Number(val).toLocaleString('it-IT', { maximumFractionDigits: 0 })
    }

    let polling = null

    async function fetchStats() {
        if(state.value==='in_progress')
            try {
                const res = await axios.get(`/api/games/${props.id}/statistics`)
                patrimonio.value = res.data.patrimonio
                state.value = res.data.state
            } catch (e) { /* errors */ }
        }
    onMounted(() => {
    polling = setInterval(fetchStats, 10000)
    })
    onUnmounted(() => clearInterval(polling))
</script>