<template>
	<v-card>
		<v-card-title>
			Data Sms
			<v-spacer></v-spacer>
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
			>
				<template v-slot:item.dateSent="{ item }">{{ item.dateSent.date }}</template>
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
          text: 'From',
          value: 'from',
        },
        {
          text: 'To',
          value: 'to',
        },
        {
          text: 'Status',
          value: 'status',
        },
        {
          text: 'Body',
          value: 'body',
        },
        {
          text: 'Direction',
          value: 'direction',
        },
        {
          text: 'Date',
          value: 'dateSent',
        },
      ],
      data: [],
      levelType: null,
      startLevel: null,
      endLevel: null,
    }
  },
  create: {},
  methods: {
    getDataLevel() {
      axios
        .get('api/twilio')
        .then((response) => {
          this.data = response.data
        })
        .catch((error) => {
          console.log(error)
        })
    },
  },
  mounted() {
    this.getDataLevel()
  },
}
</script>
