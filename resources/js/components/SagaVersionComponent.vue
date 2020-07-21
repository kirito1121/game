<template>
	<v-card>
		<v-card-title>Tracking Data Saga By Version</v-card-title>
		<v-card-title>
			<v-row align="center" class="ml-5">
				<v-col cols="12" sm="6">
					<v-select :items="versions" label="Version" v-model="dataSearch.appVersion"></v-select>
				</v-col>
				<v-col cols="12" sm="6">
					<div class="my-2">
						<v-btn @click="searchData()" color="#3490dc" dark small>Search</v-btn>
					</div>
				</v-col>
			</v-row>
			<v-spacer></v-spacer>
			<v-text-field append-icon="mdi-magnify" hide-details label="Search" single-line v-model="search"></v-text-field>
		</v-card-title>
		<div class="container">
			<v-data-table
				:headers="headers"
				:items="data"
				:items-per-page="15"
				:rows="[10,20,30]"
				:search="search"
				class="elevation-1"
				disable-items-per-page
				item-key="name"
				loading
				loading-text="Loading... Please wait"
			>
				<template v-slot:item.droprate="{ item }">
					<v-chip :color="getColor(convertNumber(item.droprate))" dark>{{convertNumber(item.droprate)}}</v-chip>
				</template>
				<template v-slot:item.attempts="{ item }">
					<v-chip
						:color="getColorAttempts(convertNumber(item.attempts))"
						dark
					>{{convertNumber(item.attempts)}}</v-chip>
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
          text: 'Level',
          value: 'Level'
        },
        {
          text: 'User',
          value: 'userCount'
        },
        {
          text: 'User win',
          value: 'userWin'
        },
        {
          text: 'User lose',
          value: 'userLose'
        },
        {
          text: 'Play Win',
          value: 'winCount'
        },
        {
          text: 'Play Lose',
          value: 'loseCount'
        },
        {
          text: 'PlayTime',
          value: 'playTime'
        },
        {
          text: 'Drop Rate (%)',
          value: 'droprate'
        },
        {
          text: 'Attempts',
          value: 'attempts'
        }
      ],
      data: [],
      dataSearch: {
        startLevel: 1,
        endLevel: 1,
        appVersion: '1.2.9'
      },
      versions: []
    }
  },
  create: {},
  methods: {
    getDataLevel() {
      this.data = []
      axios
        .get('api/trackDataLevelByVersion', {
          params: {
            appVersion: this.dataSearch.appVersion
          }
        })
        .then(response => {
          this.data = response.data
        })
        .catch(error => {
          console.log(error)
        })
    },
    getVersions() {
      axios
        .get('api/versions')
        .then(response => {
          console.log(response.data)
          this.versions = response.data
        })
        .catch(error => {
          console.log(error)
        })
    },
    getColor(val) {
      if (Number(val) > 15) return '#e3342f'
      else if (Number(val) > 10) return '#f6993f'
      else return '#38c172'
    },
    getColorAttempts(val) {
      if (Number(val) > 10) return '#e3342f'
      else if (Number(val) > 5) return '#f6993f'
      else return '#38c172'
    },
    convertNumber(number) {
      if (number) {
        return number.toFixed(4)
      } else return 0
    },
    searchData() {
      this.data = []
      this.getDataLevel()
    }
  },
  mounted() {
    this.getVersions()
    this.getDataLevel()
  }
}
</script>
