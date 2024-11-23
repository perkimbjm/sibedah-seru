<template>
    <!-- Main modal -->
    <div
        id="featureModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-500 bg-opacity-75 hidden"
    >
        <div
            class="bg-white rounded-lg overflow-hidden shadow-lg max-w-lg w-full modal-content"
        >
            <div class="flex justify-between items-center p-4 border-b">
                <h5 class="text-lg font-semibold" id="feature-title"></h5>
                <button
                    type="button"
                    class="text-gray-500 hover:text-gray-700"
                    @click="toggleModal(false)"
                >
                    &times;
                </button>
            </div>
            <div class="p-4" id="feature-info"></div>
            <div class="flex justify-end p-4 border-t">
                <button
                    type="button"
                    class="bg-green-400 text-white rounded px-4 py-2 hover:bg-green-500"
                    @click="toggleModal(false)"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineExpose } from "vue";
const toggleModal = (show) => {
    const modal = document.getElementById("featureModal");
    if (modal) {
        if (show) {
            modal.classList.remove("hidden");
        } else {
            modal.classList.add("hidden");
        }
    }
};

defineExpose({ toggleModal });
</script>

<style scoped>
#featureModal {
    align-items: center;
    justify-content: center;
    position: fixed;
    top: 0;
    left: 0;
    background-color: rgba(0, 0, 0, 0.3);
    z-index: 9999;
    overflow-y: auto;
}

.modal-content {
    background-color: rgb(223, 223, 223);
    padding: 20px;
    border-radius: 8px;
}
</style>
