<template>
    <div class="top-bar">
        <div class="user-info">
            <span class="info-label">Username:</span>
            <span class="info-value">{{ user.name }}</span>
        </div>
        <div class="user-info">
            <span class="info-label">Gold:</span>
            <span class="info-value">{{ user.gold }}</span>
        </div>
        <div class="user-info">
            <span class="info-label">Total Buildings:</span>
            <span class="info-value">{{ user.totalBuildings }}</span>
        </div>
        <div class="user-info">
            <span class="info-label">Total Experience:</span>
            <span class="info-value">{{ user.totalExperience }}</span>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        user: {
            type: Object,
            required: true
        }
    }
}
</script>

<style scoped>
.top-bar {
    background-color: #f2f2f2;
    padding: 10px;
    display: flex;
    justify-content: space-between;
}

.user-info {
    display: flex;
    align-items: center;
}

.info-label {
    margin-right: 5px;
}

.info-value {
    font-weight: bold;
}
</style>
