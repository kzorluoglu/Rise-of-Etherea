<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    buildings: {
        type: Object,
    },
    playerBuildings: {
        type: Object,
    },
    ongoingConstructions: {
        type: Object,
    },
    user: {
        type: Object,
    },
    authToken: {
        type: String
    }
});
</script>

<template>
    <Head title="Game" />

    <AuthenticatedLayout>
<!--        <template #header>-->
<!--            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Game</h2>-->
<!--        </template>-->
        <UserInformationBar :user="user" />
        <div class="container">

            <div class="py-12">
                <BuildingComponent
                    v-for="building in buildings"
                    :key="building.name"
                    :building="building"
                    :buildings="buildings"
                    :player-buildings="playerBuildings"
                    :ongoing-constructions="ongoingConstructions"
                    @start-construction="startConstruction"
                ></BuildingComponent>
            </div>
        </div>


    </AuthenticatedLayout>
</template>

<script>
import BuildingComponent from '../Components/BuildingComponent.vue';
import UserInformationBar from "@/Components/UserInformationBar.vue";
export default {
    components: { BuildingComponent, UserInformationBar },
    data() {
        return {
        };
    },
    mounted() {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.authToken}`;
    },
    methods: {
        startConstruction(building) {
            // Implement the logic for starting the construction
            console.log('Starting construction of', building);
        },
    },
}
</script>
<style>
/* General styles */
body, html {
    height: 100%;
    margin: 0;
}

body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    background: url('path-to-your-village-background-image.jpg') no-repeat center center fixed;
    background-size: cover;
}

.container {
    position: relative;
    height: 100%;
    padding: 20px;
}

/* Style for the building component */
.building {
    position: absolute;
    background-color: rgba(249, 249, 249, 0.8);
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    padding: 20px;
    max-width: 200px;
    text-align: center;
}

/* Specific building positions */
.building:nth-child(1) { top: 10%; left: 5%; }
.building:nth-child(2) { top: 20%; left: 20%; }
.building:nth-child(3) { top: 10%; right: 10%; }
.building:nth-child(4) { top: 30%; left: 40%; }
.building:nth-child(5) { top: 40%; right: 20%; }
/* ... you can add more positions for additional buildings ... */

/* Heading styles */
.building h2 {
    font-size: 1.2em;
    margin-bottom: 10px;
}

/* Paragraph styles */
.building p {
    margin-bottom: 10px;
    font-size: 0.8em;
}

/* Button styles */
.building button {
    background-color: #4CAF50;
    border: none;
    color: white;
    padding: 8px 16px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 5px;
}

.building button:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
}

/* Alert styles */
.alert {
    background-color: #f44336;
    color: white;
    padding: 10px;
    border-radius: 4px;
    text-align: center;
    margin: 10px 0;
}

/* User Information Bar */
.user-info-bar {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    background-color: rgba(51, 51, 51, 0.8);
    color: #fff;
    padding: 10px;
    z-index: 1000;
}


</style>
