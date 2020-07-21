<template>
	<v-card>
		<v-card-title>
			Level Tracking Data Conversion
			<v-spacer></v-spacer>
			<v-row align="center" class="ml-5">
				<v-col cols="12" md="3">
					<v-text-field hide-details label="Sub Level" single-line v-model="subLevel"></v-text-field>
				</v-col>
				<v-col cols="12" md="3">
					<v-select :items="levelTypeLise" label="Level Type" v-model="levelType"></v-select>
				</v-col>
				<v-col cols="12" sm="3">
					<div class="my-2">
						<v-btn @click="searchData()" color="#3490dc" dark small>Search</v-btn>
					</div>
				</v-col>
			</v-row>
			<v-text-field append-icon="mdi-magnify" hide-details label="Search" single-line v-model="search"></v-text-field>
		</v-card-title>
		<div class="container">
			<v-data-table
				:headers="headers"
				:items="data"
				:items-per-page="15"
				:loading="true"
				:search="search"
				class="elevation-1"
				disable-items-per-page
				item-key="name"
				loading-text="Loading... Please wait"
			></v-data-table>
		</div>
	</v-card>
</template>

<script>
export default {
  data() {
    return {
      search: '',
      levelTypeLise: ['Saga', 'Rush', 'Other'],
      headers: [
        {
          text: 'Level',
          value: 'Level'
        },
        {
          text: 'Sub Level',
          value: 'SubLevel'
        },
        {
          text: 'Used Coins',
          value: 'usedCoins'
        },
        {
          text: 'RainBow',
          value: 'rainBow'
        },
        {
          text: 'TowTruck',
          value: 'towTruck'
        },
        {
          text: 'Police Car',
          value: 'police'
        },
        {
          text: 'Buy More Move',
          value: 'buyMoreMove'
        },
        {
          text: 'Play time',
          value: 'playTime'
        }
      ],
      data: [],
      levelType: 'Saga',
      subLevel: 0
    }
  },
  create: {},
  methods: {
    getDataLevel() {
      axios
        .get('api/conversion', {
          params: {
            levelType: this.levelType,
            subLevel: this.subLevel
          }
        })
        .then(response => {
          this.data = response.data
        })
        .catch(error => {
          console.log(error)
        })
    },
    // convertNumber(number) {
    //   if (number) {
    //     return number.toFixed(4)
    //   } else return 0
    // },
    // getColor(val) {
    //   if (Number(val) > 15) return '#e3342f'
    //   else if (Number(val) > 10) return '#f6993f'
    //   else return '#38c172'
    // },
    // getColorHardLevel(str) {
    //   if (str == 'Yes') return '#e3342f'
    //   else return '#38c172'
    // },
    // getColorConversion(number) {
    //   if (number < 1) return '#e3342f'
    //   else if (number < 2) return '#f6993f'
    //   else return 'green'
    // },

    // sumField(key, items) {
    //   return (
    //     items.reduce((accumulator, currentValue) => {
    //       return Number(accumulator) + Number(currentValue[key] || 0)
    //     }, 0) / 15
    //   ).toFixed(4)
    // },
    // showObs(items, value) {
    //   return Object.keys(items).find(key => items[key] === value)
    // }
    searchData() {
      this.data = []
      this.getDataLevel()
    }
  },
  mounted() {
    this.getDataLevel()
  }
}
</script>
