<template>
	<div class="calc_form">
		<div class="modal-card">
			<header class="modal-card-head">
				<p class="modal-card-title">Калькулятор доставки</p>
			</header>

			<section class="modal-card-body">
				<div class="columns">
					<div class="column is-6">
						<b-field label="Вес, кг">
							<b-input type="text" v-model="ves"></b-input>
						</b-field>
					</div>
					<div class="column is-6">
						<b-field label="Объем, м3">
							<b-input type="text" v-model="obem"></b-input>
						</b-field>
					</div>
				</div>

				<div class="columns">
					<div class="column is-3">
						<b-field label="Тип оплаты">
							<b-select expanded v-model="pay_type" @input="changePay">
								<option
									v-for="pay_types in const_pay_types"
									:value="pay_types.id"
									:key="pay_types.id">
									{{ pay_types.name }}
								</option>
							</b-select>
						</b-field>
					</div>
					<div class="column is-3">
						<b-field label="Цена за кг, руб">
							<b-select expanded v-model="sum_kg">
								<option
									v-for="sel_ves in const_ves[pay_type]"
									:value="sel_ves"
									:key="sel_ves">
									{{ sel_ves }}
								</option>
							</b-select>
						</b-field>
					</div>
					<div class="column is-4">
						<b-field label="Цена за м3, руб">
							<b-select expanded v-model="sum_m3">
								<option
									v-for="sel_obem in const_obem"
									:value="sel_obem"
									:key="sel_obem">
									{{ sel_obem }}
								</option>
							</b-select>
						</b-field>
					</div>
					<div class="column is-2">
						<b-field label="Вес куба, кг">
							<b-input type="text" v-model="ves_kub" readonly></b-input>
						</b-field>
					</div>
				</div>

				<div class="columns">
					<div class="column is-3">
						<b-field label="Использовать">
				            <b-radio v-model="radio_perevozka"
				                native-value="1" :disabled="!sum_perevozka_kg">
				                Расчет по кг
				            </b-radio>
						</b-field>
					</div>
					<div class="column is-3">
						<b-field label="Стоимость перевозки по кг">
							<b-input type="text" v-model="sum_perevozka_kg" readonly></b-input>
						</b-field>
					</div>
					<div class="column is-3">
						<b-field label="Использовать">
				            <b-radio v-model="radio_perevozka"
				                native-value="2" :disabled="!sum_perevozka_m3">
				                Расчет по м3
				            </b-radio>
						</b-field>
					</div>
					<div class="column is-3">
						<b-field label="Стоимость перевозки по м3">
							<b-input type="text" v-model="sum_perevozka_m3" readonly></b-input>
						</b-field>
					</div>
				</div>

				<div class="columns">
					<div class="column is-3">
						<b-field label="Применить скидку">
				            <b-checkbox v-model="is_skidka" style="padding-top:8px;"
				                true-value="2"
				                false-value="1">
				                {{ skidkaCaption }}
				            </b-checkbox>
						</b-field>
					</div>
					<div class="column is-4">
						<b-field label="Выберите скидку">
							<b-select expanded v-model="skidka_val" :disabled="is_skidka==1">
								<option
									v-for="sel_skidka in const_skidka"
									:value="sel_skidka"
									:key="sel_skidka">
									{{ sel_skidka }} %
								</option>
							</b-select>
						</b-field>
					</div>
					<div class="column is-5">
						<b-field label="Итого стоимость перевозки, руб">
							<b-input type="text" v-model="sum_perevozka" readonly></b-input>
						</b-field>
					</div>
				</div>

				<div class="columns">
					<div class="column is-3">
						<b-field label="Экспедиция забора">
				            <b-checkbox v-model="is_zabor" style="padding-top:8px;" @input="zaborRaschet">
				                Использовать
				            </b-checkbox>
						</b-field>
					</div>
					<div class="column is-3">
						<b-field label="Тип авто">
							<b-select expanded v-model="zabor_auto_name" :disabled="!is_zabor" @input="zaborRaschet">
								<option
									v-for="auto_name in const_auto_name"
									:value="auto_name.id"
									:key="auto_name.id">
									{{ auto_name.name }}
								</option>
							</b-select>
						</b-field>
					</div>
					<div class="column is-3">
						<b-field label="Тип оплаты">
							<b-select expanded v-model="zabor_auto_type" :disabled="!is_zabor" @input="zaborRaschet">
								<option
									v-for="auto_type in const_auto_type"
									:value="auto_type.id"
									:key="auto_type.id">
									{{ auto_type.name }}
								</option>
							</b-select>
						</b-field>
					</div>
					<div class="column is-3">
						<b-field label="Стоимость забора, руб">
							<b-input type="text" v-model="zabor_auto_sum" :disabled="!is_zabor" :readonly="zabor_auto_type<3"></b-input>
						</b-field>
					</div>
				</div>

				<div class="columns">
					<div class="column is-3">
						<b-field label="Экспедиция доставки">
				            <b-checkbox v-model="is_dostavka" style="padding-top:8px;" @input="dostavkaRaschet">
				                Использовать
				            </b-checkbox>
						</b-field>
					</div>
					<div class="column is-3">
						<b-field label="Тип авто">
							<b-select expanded v-model="dostavka_auto_name" :disabled="!is_dostavka" @input="dostavkaRaschet">
								<option
									v-for="auto_name in const_auto_name"
									:value="auto_name.id"
									:key="auto_name.id">
									{{ auto_name.name }}
								</option>
							</b-select>
						</b-field>
					</div>
					<div class="column is-3">
						<b-field label="Тип оплаты">
							<b-select expanded v-model="dostavka_auto_type" :disabled="!is_dostavka" @input="dostavkaRaschet">
								<option
									v-for="auto_type in const_auto_type"
									:value="auto_type.id"
									:key="auto_type.id">
									{{ auto_type.name }}
								</option>
							</b-select>
						</b-field>
					</div>
					<div class="column is-3">
						<b-field label="Стоимость доставки, руб">
							<b-input type="text" v-model="dostavka_auto_sum" :disabled="!is_dostavka" :readonly="dostavka_auto_type<3"></b-input>
						</b-field>
					</div>
				</div>

				<div class="columns">
					<div class="column is-12">
						<b-field>
							<b-input type="textarea" rows="4" v-model="sum_info" readonly></b-input>
						</b-field>
					</div>
				</div>
			</section>	

			<footer class="modal-card-foot">
				<button class="button is-primary close-button" @click="copyForm">Скопировать</button>
				<button class="button is-danger close-button" @click="clearForm">Очистить</button>
				<button class="button is-success close-button" @click="closeForm">Закрыть</button>
			</footer>
		</div>
	</div>
</template>

<script>
export default {
	name: 'CalcForm',
	props: {},
	data () {
		return {
			settings: {},
			const_pay_types: [{id:0, name:'Наличная оплата'}, {id: 1, name: 'Безналичная оплата'}],
			const_ves: [],
			const_obem: [],
			const_auto_name: [],
			const_auto_type: [],
			const_auto_sum: [],
			const_skidka: [],
			ves: '',
			obem: '',
			pay_type: 0,
			sum_kg: '',
			sum_m3: '',
			radio_perevozka: 0,
			is_skidka: 1,
			skidka_val: '',
			number: '',
			is_zabor: true,
			zabor_auto_name: '',
			zabor_auto_type: '',
			zabor_auto_sum: 0,
			is_dostavka: true,
			dostavka_auto_name: '',
			dostavka_auto_type: '',
			dostavka_auto_sum: 0
		}
	},
	created() {
	},
	mounted() {
		this.getSettings();
	},
	computed: {
		sum_info() {
			if (Number(this.sum_perevozka) == 0) {
				return '';
			}
			let text = '';
			let sum_all_str = 'Полная стоимость доставки = ' + this.sum_all + ' руб.\n';
			let skidka_str = '';
			if ((this.is_skidka == 2) && this.skidka_val) {
				skidka_str = ' (в том числе, скидка ' + this.skidka_val + '%)';
			}
			let sum_perevozka_str = 'Стоимость перевозки = ' + this.sum_perevozka + ' руб.' + skidka_str + '\n';
			let sum_zabor_str = '';
			if (Number(this.zabor_auto_sum > 0)) {
				sum_zabor_str = 'Стоимость экспедиции забора = ' + this.zabor_auto_sum + ' руб.\n';
			}
			let sum_dostavka_str = '';
			if (Number(this.dostavka_auto_sum > 0)) {
				sum_dostavka_str = 'Стоимость экспедиции доставки = ' + this.dostavka_auto_sum + ' руб.\n';
			}
			text = sum_all_str + sum_perevozka_str + sum_zabor_str + sum_dostavka_str;
			return text;
		},
		skidkaCaption() {
			if (this.is_skidka == 1) { return 'Нет'; }
			if (this.is_skidka == 2) { return 'Да'; }
		},
		ves_kub() {
			if (this.ves && this.obem) {
				return (Number(this.ves) / Number(this.obem));
			}
			return '';
		},
		sum_perevozka_kg() {
			if (this.ves && this.sum_kg) {
				return (Number(this.ves) * Number(this.sum_kg));
			}
			return '';
		},
		sum_perevozka_m3() {
			if (this.obem && this.sum_m3) {
				return (Number(this.obem) * Number(this.sum_m3));
			}
			return '';
		},
		// Perevozka itogo sum
		sum_perevozka() {
			let perevozka_sum = 0;
			if (this.radio_perevozka == 0) {
				return perevozka_sum;
			}
			if (this.radio_perevozka == 1) {
				perevozka_sum = this.sum_perevozka_kg;
			}
			if (this.radio_perevozka == 2) {
				perevozka_sum = this.sum_perevozka_m3;
			}
			if ((this.is_skidka == 2) && this.skidka_val) {
				perevozka_sum = Number(perevozka_sum) / 100 * (100 - Number(this.skidka_val));
				perevozka_sum = Math.round(perevozka_sum * 100) / 100;
			}
			return perevozka_sum;
		},
		sum_all() {
			return Number(this.sum_perevozka) + Number(this.zabor_auto_sum) + Number(this.dostavka_auto_sum);
		},
	},
	methods: {
		zaborRaschet() {
			if (!this.is_zabor) {
				this.zabor_auto_sum = 0;
				return;
			}
			if (this.is_zabor && (this.zabor_auto_name !== '') && (this.zabor_auto_type !== '')) {
				if (this.zabor_auto_type<3) {
					this.zabor_auto_sum = this.const_auto_sum[this.zabor_auto_name][this.zabor_auto_type];
					return;
				}
				if (this.zabor_auto_type === 3) {
					this.zabor_auto_sum = '';
					return;
				}
			}
		},
		dostavkaRaschet() {
			if (!this.is_dostavka) {
				this.dostavka_auto_sum = 0;
				return;
			}
			if (this.is_dostavka && (this.dostavka_auto_name !== '') && (this.dostavka_auto_type !== '')) {
				if (this.dostavka_auto_type<3) {
					this.dostavka_auto_sum = this.const_auto_sum[this.dostavka_auto_name][this.dostavka_auto_type];
					return;
				}
				if (this.dostavka_auto_type === 3) {
					this.dostavka_auto_sum = '';
					return;
				}
			}
		},
		changePay() {
			this.sum_kg = '';
		},
		closeForm() {
			this.$emit('close');
		},
		copyForm() {
			this.$copyText(this.sum_info);
			this.$toast.open({
				duration: 700,
				message: 'Текст скопирован в буфер обмена',
				position: 'is-bottom'
			})
		},
		clearForm() {
			this.ves = '';
			this.obem = '';
			this.pay_type = 0;
			this.sum_kg = '';
			this.sum_m3 = '';
			this.radio_perevozka = 0;
			this.is_skidka = 1;
			this.skidka_val = '';
			this.number = '';
			this.is_zabor = true;
			this.zabor_auto_name = '';
			this.zabor_auto_type = '';
			this.zabor_auto_sum = 0;
			this.is_dostavka = true;
			this.dostavka_auto_name = '';
			this.dostavka_auto_type = '';
			this.dostavka_auto_sum = 0;
		},
		getSettings() {
		    this.axios.get('http://nevcab.ru/api/settings_get.php?type=calc').then((response) => {
		        if (response.data.error == 0) {
		        	this.settings = response.data.data
		        	this.const_ves = [[this.settings['calc_ves_nal_sum1'], this.settings['calc_ves_nal_sum2']], [this.settings['calc_ves_beznal_sum1'], this.settings['calc_ves_beznal_sum2']]];
		        	this.const_obem = [this.settings['calc_obem_sum1'], this.settings['calc_obem_sum2'], this.settings['calc_obem_sum3'], this.settings['calc_obem_sum4']],
		        	this.const_auto_name = [{id:0, name: this.settings['calc_auto_type1']}, {id:1, name: this.settings['calc_auto_type2']}, {id:2, name: this.settings['calc_auto_type3']}];
		        	this.const_auto_type = [{id:0, name: this.settings['calc_auto_pay1']}, {id:1, name: this.settings['calc_auto_pay2']}, {id:2, name: this.settings['calc_auto_pay3']}, {id:3, name: 'Вручную'}];
					this.const_auto_sum = [
						[this.settings['calc_auto_sum_11'], this.settings['calc_auto_sum_12'], this.settings['calc_auto_sum_13'], ''],
						[this.settings['calc_auto_sum_21'], this.settings['calc_auto_sum_22'], this.settings['calc_auto_sum_23'], ''],
						[this.settings['calc_auto_sum_31'], this.settings['calc_auto_sum_32'], this.settings['calc_auto_sum_33'], ''],
					];
					this.const_skidka = [this.settings['calc_skidka1'], this.settings['calc_skidka2'], this.settings['calc_skidka3']];
		        }
		    })
		}
	}
}
</script>

<style scoped>
	.modal-card {
		width: 960px;
	}
	.close-button {
		width:33%;
		height:50px;
	}
</style>
