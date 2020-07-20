<template>
	<v-card>
		<v-row :key="'row'+kr" v-for="(kr,i) in 8">
			<v-col :key="'col'+ch" class="ma-1" cols="8" md="1" v-for="(ch,k) in 8">{{i + '-' +k}}</v-col>
		</v-row>

		<v-card-title>
			Level Tracking Data Saga
			<v-spacer></v-spacer>
			<v-text-field append-icon="mdi-magnify" hide-details label="Search" single-line v-model="search"></v-text-field>
		</v-card-title>
		<div class="container">
			<v-data-table
				:headers="headers"
				:items="data"
				:items-per-page="15"
				:search="search"
				class="elevation-1"
				disable-items-per-page
				item-key="name"
			>
				<template v-slot:item.droprate="{ item }">
					<v-chip :color="getColor(item.droprate)" dark>{{item.droprate}}</v-chip>
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
      levelType: null,
      startLevel: null,
      endLevel: null
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
    }
  },
  mounted() {}
}
</script>
