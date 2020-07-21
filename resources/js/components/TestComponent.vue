<template>
	<v-card>
		<v-card-title>
			<v-list>
				<v-list-item :key="index" v-for="(item, index) in dataObs">
					<v-list-item-content>
						<span color="#e3342f">{{ Object.keys(dataObs).find(key => dataObs[key] === item) }}</span>
					</v-list-item-content>
					<v-list-item-content class="align-end">: {{item.toFixed(4)}} %</v-list-item-content>
				</v-list-item>
			</v-list>
		</v-card-title>
	</v-card>
</template>

<script>
export default {
  data() {
    return {
      search: '',
      headers: [
        {
          text: 'Level',
          value: 'level'
        },
        {
          text: 'Map Level',
          value: 'mapLevel'
        },
        {
          text: 'Target',
          value: 'target'
        },
        {
          text: 'Count Target',
          value: 'countTarget'
        },
        {
          text: 'Obstacle',
          value: 'obstacle'
        },
        {
          text: 'Move',
          value: 'move'
        },
        {
          text: 'Hard Level',
          value: 'hardLevel'
        },
        {
          text: 'Drop Rate (%)',
          value: 'droprate'
        },
        {
          text: 'Attempts',
          value: 'attempts'
        },
        {
          text: 'Conversion',
          value: 'conversion'
        }
      ],
      data: [
        {
          droprate: 50
        },
        {
          droprate: 250
        },
        {
          droprate: 150
        }
      ],
      dataObs: [],
      levelType: 0,
      startLevel: 1,
      endLevel: 690
    }
  },
  create: {},
  methods: {
    getColor(val) {
      if (val < 100) {
        return '#e3342f'
      } else if (val < 200) {
        return '#f6993f'
      } else {
        return '#38c172'
      }
    },

    getObs() {
      axios
        .get('api/readDataMapABC', {
          params: {
            levelType: 0,
            startLevel: 1,
            endLevel: 690
          }
        })
        .then(response => {
          this.dataObs = response.data
        })
        .catch(error => {
          console.log(error)
        })
    }
  },
  mounted() {
    this.getObs()
  }
}
</script>
