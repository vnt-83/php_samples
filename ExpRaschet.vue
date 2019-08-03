<template>
	<div class="exp_raschet">

		<b-modal
			:active.sync="ExpRaschetPayFormActive"
			:component="ExpRaschetPayForm"
			:canCancel="['x']"
			:has-modal-card="true"
			:props="ExpRaschetPayFormProps">
		</b-modal>

		<b-modal
			:active.sync="ExpRaschetSverkaFormActive"
			:component="ExpRaschetSverkaForm"
			:canCancel="true"
			:has-modal-card="true"
			:props="ExpRaschetSverkaFormProps">
		</b-modal>


		<nav class="level" style="margin-bottom:10px;">
			<div class="level-left">
				<div class="level-item">
					<div class="buttons has-addons">
						<span class="button" style="width:120px;">Количество:</span>
						<span class="button" style="width:100px;"><b>{{ raschetCount }}</b></span>
					</div>
				</div>
				<div class="level-item">
					<div class="buttons has-addons">
						<span class="button" style="width:100px;">Приход:</span>
						<span class="button" style="width:110px;"><b>{{ raschetPrihod }}</b></span>
						<span class="button" style="width:120px;">Расход план:</span>
						<span class="button" style="width:100px;"><b>{{ raschetRashodPlan }}</b></span>
						<span class="button" style="width:120px;">Расход факт:</span>
						<span class="button" style="width:100px;"><b>{{ raschetRashodFact }}</b></span>
						<span class="button" style="width:100px;">Прибыль:</span>
						<span class="button" style="width:100px;"><b>{{ raschetSum }}</b></span>
						<span class="button" style="width:100px;">Платежи:</span>
						<span class="button" style="width:100px;"><b>{{ raschetPlat }}</b></span>
						<span class="button" style="width:100px;">Остаток:</span>
						<span class="button" style="width:100px;"><b>{{ raschetOstatok }}</b></span>
					</div>
				</div>
			</div>

			<div class="level-right">
				<div class="level-item">
					<b-field style="width:354px;">
						<p class="control">
							<b-dropdown hoverable>
								<button class="button" :class="{'button-filter-color': (is_filter1 || is_filter2)}" slot="trigger">
									<span>Фильтры</span>
									<b-icon icon="menu-down"></b-icon>
								</button>
								<!-- <b-dropdown-item>
									<b-checkbox v-on:input="filterPays">Показать только оплаченные</b-checkbox>
								</b-dropdown-item> -->
							</b-dropdown>
						</p>
						<b-input expanded style="z-index:1;"
							v-model="is_filterSearch"
							@keydown.native.esc.prevent="is_filterSearch=''"
							placeholder="Поиск..."
							icon="magnify">
						</b-input>
						<p class="control">
							<button class="button filter-clear" v-if="is_filterSearch" @click="filterSearchClean">
								<b-icon icon="close"></b-icon>
							</button>
						</p>
					</b-field>
				</div>
			</div>
		</nav>


		<div class="table-div-scroll">
		<b-table
			:data="raschet_list_filtered"
			:loading="(!is_raschet_loaded || ExpRaschetPayFormActive)"
			:bordered="false"
			:striped="true"
			:hoverable="true"
			:narrowed="true"
			:paginated="true"
			:per-page="40">


			<template slot-scope="props">

				<b-table-column label="Исполнитель">
					<b-icon v-if="props.row.client_type == 1" icon="account" style="color:green;padding-top:10px;padding-right:10px;" title="Физ. лицо"></b-icon>
					<b-icon v-if="props.row.client_type == 2" icon="office" style="color:green;padding-top:10px;padding-right:10px;" title="Юр. лицо"></b-icon>
					{{ props.row.client_name }}
				</b-table-column>

			 <b-table-column label=" " width="92">

					<a class="button is-danger" title="Внести платёж"
						@click="addPay(props.row.client_id, props.row.client_type, props.row.client_name)">
						<b-icon icon="cash-100" style="color:LightCyan;"></b-icon>	
					</a>

					<a class="button is-success" title="Посмотреть сверку"
						@click="showSverka(props.row.client_id, props.row.client_type, props.row.client_name)">
						<b-icon icon="clipboard-text"></b-icon>
					</a>

				</b-table-column>

				 <b-table-column label="Приход">
					{{ props.row.prihod }}
				</b-table-column>

				 <b-table-column label="Расход план">
					{{ props.row.rashod_plan }}
				</b-table-column>

				 <b-table-column label="Расход факт">
					{{ props.row.rashod_fact }}
				</b-table-column>

				 <b-table-column label="Прибыль">
					{{ props.row.sum_exp }}
				</b-table-column>

				 <b-table-column label="Платежи">
					{{ props.row.sum_pays }}
				</b-table-column>

				 <b-table-column label="Остаток">
					{{ props.row.ostatok }}
				</b-table-column>

			</template>

		</b-table>
		</div>

	</div>
</template>

<script>
import bus from "@/components/bus"
import ExpRaschetPayForm from '@/components/ExpRaschetPayForm'
import ExpRaschetSverkaForm from '@/components/ExpRaschetSverkaForm'

export default {
	name: 'ExpRaschet',
	props: {
		exp_type: {type: String, required: true},
	},
	components: {
		ExpRaschetPayForm,
		ExpRaschetSverkaForm
	},
	data () {
		return {
			is_filter1: false,
			is_filter2: false,
			is_filterSearch: '',
			raschet_list: [],
			is_raschet_loaded: false,
			ExpRaschetPayForm,
			ExpRaschetPayFormActive: false,
			ExpRaschetPayFormProps: { client_id: 0, client_type: 0, client_name: '', exp_type: 0 },
			ExpRaschetSverkaForm,
			ExpRaschetSverkaFormActive: false,
			ExpRaschetSverkaFormProps: { client_id: 0, client_type: 0, client_name: '', exp_type: 0 }
		}
	},
	beforeRouteEnter (to, from, next) {
		next(vm => {
	    vm.onAddPay();
	  });
	},
	created() {
		this.runner();
	},
	mounted() {
		bus.$on('add_pay', this.onAddPay);
	},
	computed: {
		raschetPrihod() {
			let sum = 0;
			for (let i = this.raschet_list_filtered.length - 1; i >= 0; i--) {
				sum = sum + Number(this.raschet_list_filtered[i].prihod)
			}
			return sum;
		},
		raschetRashodPlan() {
			let sum = 0;
			for (let i = this.raschet_list_filtered.length - 1; i >= 0; i--) {
				sum = sum + Number(this.raschet_list_filtered[i].rashod_plan)
			}
			return sum;
		},
		raschetRashodFact() {
			let sum = 0;
			for (let i = this.raschet_list_filtered.length - 1; i >= 0; i--) {
				sum = sum + Number(this.raschet_list_filtered[i].rashod_fact)
			}
			return sum;
		},
		raschetSum() {
			let sum = 0;
			for (let i = this.raschet_list_filtered.length - 1; i >= 0; i--) {
				sum = sum + Number(this.raschet_list_filtered[i].sum_exp)
			}
			return sum;
		},
		raschetPlat() {
			let sum = 0;
			for (let i = this.raschet_list_filtered.length - 1; i >= 0; i--) {
				sum = sum + Number(this.raschet_list_filtered[i].sum_pays)
			}
			return sum;
		},
		raschetOstatok() {
			let sum = 0;
			for (let i = this.raschet_list_filtered.length - 1; i >= 0; i--) {
				sum = sum + Number(this.raschet_list_filtered[i].ostatok)
			}
			return sum;
		},
		raschetCount() {
			return this.raschet_list_filtered.length;
		},
		raschet_list_filtered() {
			return this.raschet_list.filter((el) => {
				let filter_search = true;
				if (this.is_filterSearch) {
					let search_str = new RegExp(this.is_filterSearch, 'i');
					filter_search = (el.client_name.match(search_str))
				}
				return (filter_search);
		 })
		}
	},
	methods: {
		// run functions in cicle
		runner: function() {
			if (this.$store.getters['auth/isLogin']) {
				this.getData();
			}
			setTimeout(this.runner, 60000);
		},
		addPay(client_id, client_type, client_name) {
			this.ExpRaschetPayFormProps['client_id'] = Number(client_id);
			this.ExpRaschetPayFormProps['client_type'] = Number(client_type);
			this.ExpRaschetPayFormProps['client_name'] = client_name;
			this.ExpRaschetPayFormProps['exp_type'] = Number(this.exp_type);
			this.ExpRaschetPayFormActive = true;
		},
		 showSverka(client_id, client_type, client_name) {
			this.ExpRaschetSverkaFormProps['client_id'] = Number(client_id);
			this.ExpRaschetSverkaFormProps['client_type'] = Number(client_type);
			this.ExpRaschetSverkaFormProps['client_name'] = client_name;
			this.ExpRaschetSverkaFormProps['exp_type'] = Number(this.exp_type);
			this.ExpRaschetSverkaFormActive = true;
		},
		msgError: function (text) {
			this.$dialog.alert({
				title: 'Ошибка',
				message: text,
				type: 'is-danger',
				hasIcon: false,
				canCancel: false,
				cancelText: '',
				confirmText: 'OK'
			});
		},
		filterSearchClean() {
			this.is_filterSearch = '';
		},
		onAddPay() {
			this.is_raschet_loaded = false;
			this.getData();
		},
		getData() {
			this.axios.get('http://xxx.ru/api/raschet_list_get.php?exp_type=' + this.exp_type).then((response) => {
				if (response.data.error == 0) {
					this.raschet_list = response.data.data
					this.is_raschet_loaded = true
				}
			})
		},
	}
}
</script>


<style>
	.dialog .modal-card {
		min-width: 430px;
		max-width: 960px;
	}
</style>


<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
	.table-div-scroll {
			width: auto;
			height: calc(100vh - 163px);
			margin-right: -10px;
			overflow-x: auto;
			overflow-y: auto;
			white-space: nowrap;
	}
	.button-filter-color {
		color: blue;
	}
	.button-filter-color:hover {
		color: blue;
	}
	.filter-clear {
		margin-left: -34px;
		margin-top: 2px;
		height: 32px;
		z-index: 4;
		border: none;
		color: lightgray;
	}
	.filter-clear:hover {
		margin-left: -34px;
		margin-top: 2px;
		height: 32px;
		z-index: 4;
		border: none;
		color:black;
	}
	.filter-clear:focus {
		margin-left: -34px;
		margin-top: 2px;
		height: 32px;
		z-index: 4;
		border: none;
		color:black;
	}
</style>
