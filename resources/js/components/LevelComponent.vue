<template>
	<v-card>
		<v-card-title>
			Data Level
			<v-spacer></v-spacer>
			<v-row justify="space-between">
				<v-col cols="12" md="4">
					<v-text-field hide-details label="Level" single-line v-model="searchData.level"></v-text-field>
				</v-col>
				<v-col cols="12" md="4">
					<v-text-field hide-details label="Level Type" single-line v-model="searchData.levelType"></v-text-field>
				</v-col>
				<v-col cols="12" md="4">
					<v-text-field hide-details label="Version" single-line v-model="searchData.version"></v-text-field>
				</v-col>
				<v-col cols="12" md="4">
					<v-text-field hide-details label="App Version" single-line v-model="searchData.appVersion"></v-text-field>
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
				:expanded="expanded"
				:headers="headers"
				:items="data"
				:loading="true"
				@click:row="clicked"
				class="elevation-1"
				hide-default-footer
				item-key="name"
				loading-text="Loading... Please wait"
				show-expand
			>
				<template v-slot:expanded-item="{ item,headers }">
					<td :colspan="headers.length" class="text-start p-3">
						<v-card class="p-2" v-if="item['i']">
							<v-card-title>{{item.obstacle}}</v-card-title>
							<v-row :key="'row'+kr" class="ml-2" v-for="(kr,i) in Number(item['g'])">
								<v-col
									:cols="item['g']"
									:key="'col'+ch"
									md="1"
									style="padding: 0;"
									v-for="(ch,k) in Number(item['h'])"
								>
									<v-img src="/storage/TrafficPuzzleCar/Cell1.png" v-if="(i+k)%2 == 0"></v-img>
									<v-img src="/storage/TrafficPuzzleCar/Cell2.png" v-if="(i+k)%2 != 0"></v-img>
								</v-col>
							</v-row>
						</v-card>
					</td>
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
      expanded: ['Donut'],
      singleExpand: false,
      headers: [
        {
          text: 'Level',
          value: 'level'
        },
        {
          text: 'Sub Level',
          value: 'sublevel'
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
        // {
        //   text: 'Map data',
        //   value: 'obstacle'
        // },
        {
          text: 'Move',
          value: 'move'
        },
        {
          text: 'Hard Level',
          value: 'hardLevel'
        },
        {
          text: 'Version',
          value: 'version'
        },
        {
          text: 'App Version',
          value: 'appVersion'
        }
      ],
      data: [{}],

      searchData: {
        version: null,
        level: null,
        levelType: null,
        appVersion: null
      },
      a: 0
    }
  },
  methods: {
    getDataLevel(params) {
      axios
        .get('api/viewLevel', {
          params: params
        })
        .then(response => {
          this.data = response.data
          console.log(this.data)
        })
        .catch(error => {
          console.log(error)
        })
    },

    searchLevel() {
      //   console.log(this.searchData)
      //   console.log('click')
      //   if (this.searchLevel.level && this.searchLevel.level) {
      this.getDataLevel(this.searchData)
      //   }
    },
    clicked(value) {
      this.expanded.push(value)
    },
    show(value) {
      console.log(value)
    }
  },
  mounted() {}
}
</script>
