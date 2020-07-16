<template>
	<v-card>
		<v-card-title>
			Level Tracking Data Rush
			<v-spacer></v-spacer>
			<v-text-field append-icon="mdi-magnify" hide-details label="Search" single-line v-model="search"></v-text-field>
		</v-card-title>
		<div class="container">
			<v-data-table
				:headers="headers"
				:items="data"
				:items-per-page="data.length"
				:search="search"
				class="elevation-1"
				disable-items-per-page
				item-key="name"
			>
				<template v-slot:item.droprate="{ item }">{{ convertNumber(item.droprate)}}</template>
				<template v-slot:item.obstacle="{ item }">{{item.obstacle}}</template>
				<template slot="body.append" slot-scope="props">
					<tr class="pink--text">
						<th class="title"></th>
						<th class="title"></th>
						<th class="title"></th>
						<th class="title"></th>
						<th class="title"></th>
						<th class="title"></th>
						<th class="title"></th>
						<th class="title">AVG</th>
						<th class="title">{{ sumField('droprate',props.items) }}</th>
						<th class="title">{{ sumField('attempts',props.items) }}</th>
						<th class="title">{{ sumField('conversion',props.items) }}</th>
					</tr>
				</template>
			</v-data-table>
		</div>
	</v-card>
</template>

<script>
export default {
  data() {
    return {
      search: '',
      headers: [
        {
          text: 'Sub Level',
          value: 'sublevel'
        },
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
      data: [{}],
      levelType: null,
      startLevel: null,
      endLevel: null,
      sublevel: null
    }
  },
  create: {},
  methods: {
    getDataLevel() {
      axios
        .get('api/readDataMapApi', {
          params: {
            levelType: 1,
            startLevel: 1,
            endLevel: 15,
            sublevel: 1
          }
        })
        .then(response => {
          this.data = response.data
        })
        .catch(error => {
          console.log(error)
        })
    },
    convertNumber(number) {
      if (number) {
        return number.toFixed(4)
      } else return 0
    },
    getColor(str) {
      if (str == 'Yes') return 'red'
      else return 'green'
    },
    getColorDropRate(number) {
      if (number > 20) return 'red'
      else if (number > 10) return 'orange'
      else return 'green'
    },

    sumField(key, items) {
      return (
        items.reduce((accumulator, currentValue) => {
          return Number(accumulator) + Number(currentValue[key] || 0)
        }, 0) / 15
      ).toFixed(4)
    },
    showObs(items, value) {
      return Object.keys(items).find(key => items[key] === value)
    }
  },
  mounted() {
    this.getDataLevel()
  }
}
</script>
