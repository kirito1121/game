<template>
	<v-card>
		<v-card-title>
			Level Tracking Data Rush
			<v-spacer></v-spacer>
			<v-row justify="space-between">
				<v-col cols="12" md="4">
					<v-text-field
						:rules="sublevelRules"
						hide-details
						label="Sub Level"
						required
						single-line
						v-model="searchData.sublevel"
					></v-text-field>
				</v-col>

				<v-col cols="12" md="4">
					<div class="my-2">
						<v-btn @click="searchLevel()" color="#3490dc" dark small>Search</v-btn>
					</div>
				</v-col>
			</v-row>
		</v-card-title>
		<div class="container">
			<v-data-table
				:headers="headers"
				:items="data"
				:items-per-page="data.length"
				:loading="true"
				:search="search"
				class="elevation-1"
				disable-items-per-page
				item-key="name"
				loading-text="Loading... Please wait"
			>
				<template v-slot:item.droprate="{ item }">
					<v-chip :color="getColor(convertNumber(item.droprate))" dark>{{convertNumber(item.droprate)}}</v-chip>
				</template>
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
						<th class="title">
							<v-chip
								:color="getColorConversion(sumField('conversion',props.items))"
								dark
							>{{ sumField('conversion',props.items) }}</v-chip>
						</th>
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
      data: [],
      searchData: {
        levelType: 1,
        startLevel: 1,
        endLevel: 15,
        sublevel: 1
      },
      sublevelRules: [v => !!v || 'Sub level is required']
    }
  },
  create: {},
  methods: {
    getDataLevel() {
      axios
        .get('api/readDataMapApi', {
          params: this.searchData
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
    getColor(val) {
      if (Number(val) > 15) return '#e3342f'
      else if (Number(val) > 10) return '#f6993f'
      else return '#38c172'
    },
    getColorDropRate(number) {
      if (number > 20) return 'red'
      else if (number > 10) return 'orange'
      else return 'green'
    },
    getColorConversion(number) {
      if (number < 1) return '#e3342f'
      else if (number < 2) return '#f6993f'
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
    },

    searchLevel() {
      if (this.searchData.sublevel) {
        this.getDataLevel()
      }
    }
  },
  mounted() {
    this.getDataLevel()
  }
}
</script>
