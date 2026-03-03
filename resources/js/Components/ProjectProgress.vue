<template>
    <div>
        <div class="w-full bg-blue-300 border border-blue-900 rounded-full h-4">
                <div
                    class="bg-blue-800 h-4 rounded-full transition-all duration-300 progress-bar"
                    :style="{ width: percentage + '%' }"
                ></div>
        </div>
        <div class="text-xs text-right text-blue-900 mt-1">
            Progresso: {{ Math.round(percentage) }}% &nbsp;|&nbsp;
            Complessità rimanente: {{ currentComplexity }}
        </div>
    </div>
</template>

<script setup>
    import { ref, onMounted, onUnmounted, computed } from 'vue'
    import axios from 'axios'

    const props = defineProps({
        projectId: [String, Number],
        gameId: [String, Number],
        initialComplexity: Number,
        initialComplexityDb: Number,
        gameState: String,
    })

    const currentComplexity = ref(props.initialComplexityDb ?? props.initialComplexity)
    const status = ref('in_progress') 

    const percentage = computed(() => {
        if (!props.initialComplexity) return 0
        return Math.max(0, Math.min(100, ((props.initialComplexity - currentComplexity.value) / props.initialComplexity) * 100))
    })

    let polling = null

    async function fetchProgress() {
        if(props.gameState === 'in_progress')
            try {
                const res = await axios.post(`/games/${props.gameId}/projects/${props.projectId}/progress`)
                currentComplexity.value = res.data.complex
                status.value = res.data.status
            } catch(e) {
                // errors
            }
    }

    onMounted(() => {
        fetchProgress()
        polling = setInterval(fetchProgress, 3000)
    })
    onUnmounted(() => clearInterval(polling))
</script>