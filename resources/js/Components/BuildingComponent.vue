<template>
    <div class="building">
        <h2>{{ building.name }}</h2>
        <p>{{ building.description }}</p>
        <p>Cost: {{ building.cost }}</p>
        <p>Construction Time: {{ building.construction_time }}</p>
        <p style="color:red" v-if="building.requirements.length > 0">Requirements:
            <span v-for="requirement in building.requirements" :key="requirement">
                {{ getBuildingName(requirement) }}
                <span v-if="!isLastRequirement(requirement)">,</span>
                &nbsp;
            </span>
        </p>
        <div v-if="isUnderConstruction || constructionInProgress">
            <p>Under construction, Remaining Time: {{ remainingTime }}</p>
        </div>
        <div v-else>
            <div v-if="isBuilt">
                You have already built this building.
            </div>
            <div v-else>
                <button v-on:click="startConstruction" :disabled="!requirementsMet">Build</button>
            </div>
        </div>
        <!-- Popup alert -->
        <div v-if="showAlert" class="alert">
            {{ popupMessage }}
        </div>
    </div>
</template>

<script>
export default {
    props: ['building', 'buildings', 'playerBuildings', 'ongoingConstructions'],
    data() {
        return {
            constructionInProgress: false,
            remainingTime: 0,
            timer: null,
            showAlert: false,
            popupMessage: null,
        }
    },
    computed: {
        isBuilt: function() {
            return this.playerBuildings.some(playerBuilding => playerBuilding.pivot.building_id === this.building.id);
        },
        isUnderConstruction: function() {
            return this.ongoingConstructions.some(construction => construction.building_id === this.building.id.toString());
        },
        requirementsMet: function() {

            return true;
            // Check if the player has all the required buildings
            // return this.building.requirements.every(req => {
            //     return this.playerBuildings.some(playerBuilding => playerBuilding.building.id === req);
            // });
        },
    },
    mounted() {
        // Iterate over each ongoing construction
        this.ongoingConstructions.forEach(construction => {
            // Check if this component's building is under construction
            if(construction.building_id === this.building.id.toString()) {
                // Calculate the remaining time in seconds
                let endTime = new Date(construction.end_time);
                this.remainingTime = Math.ceil((endTime - new Date()) / 1000);
                // Start the timer
                this.timer = setInterval(this.updateTime, 1000);
                // Set the constructionInProgress flag
                this.constructionInProgress = true;
            }
        });
    },
    methods: {
        startConstruction: function() {
            // Start the construction of the building
            if(this.requirementsMet && !this.isBuilt && !this.isUnderConstruction) {
                this.$emit('start-construction', this.building);

                axios.post('/api/start-construction', {
                    building_id: this.building.id,
                })
                    .then(response => {
                        this.constructionInProgress = true;
                        let endTime = new Date(response.data.end_time);
                        this.remainingTime = Math.ceil((endTime - new Date()) / 1000);
                        this.timer = setInterval(this.updateTime, 1000);
                    })      .catch(error => {
                    if (error.response && error.response.data && error.response.data.error) {
                        console.log("bakcend hata dşndğrdğ");
                        this.showAlert = true;
                        this.popupMessage = error.response.data.error;
                        setTimeout(() => {
                            console.log("timetou calistimi")
                            this.showAlert = false;
                        }, 5000);
                    } else {
                        // Handle other types of errors
                        console.error('An error occurred:', error);
                    }
                });
            } else if(this.isUnderConstruction) {
                alert('This building is already under construction.');
            } else {
                alert('You do not meet the requirements for this building.');
            }
        },
        updateTime: function() {
            if(this.remainingTime > 0) {
                this.remainingTime--;
            } else {
                this.$emit('end-construction', this.building);
                axios.post('/api/end-construction', {
                    building_id: this.building.id,
                });
                clearInterval(this.timer);
                this.constructionInProgress = false;
                this.timer = null;
            }
        },
        getBuildingName(requirement) {
            const matchingBuilding = this.buildings.find(
                playerBuilding => playerBuilding.id === requirement
            );
            return matchingBuilding ? matchingBuilding.name : '';
        },
        isLastRequirement(requirement) {
            return requirement === this.building.requirements[this.building.requirements.length - 1];
        },
    },
}
</script>

<style scoped>
.alert {
    background-color: #f44336;
    color: white;
    padding: 10px;
    border-radius: 4px;
}
</style>
