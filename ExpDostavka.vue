<template>
  <div class="exp_dostavka">

    <b-modal
      :active.sync="ExpDostavkaFormActive"
      :component="ExpDostavkaForm"
      :canCancel="['x']"
      :has-modal-card="true"
      :props="ExpDostavkaFormProps">
    </b-modal>


    <nav class="level" style="margin-bottom:10px;">
      <div class="level-left">
        <div class="level-item">
          <div class="buttons has-addons">
            <span class="button" style="width:70px;">Эксп:</span>
            <span class="button" style="width:100px;"><b>{{ dostavkaCount }}</b></span>
            <span class="button" style="width:70px;">Заявок:</span>
            <span class="button" style="width:100px;"><b>{{ ordersCount }}</b></span>
          </div>
        </div>
        <div class="level-item">
          <div class="buttons has-addons">
            <span class="button" style="width:70px;">Объем:</span>
            <span class="button" style="width:100px;"><b>{{ dostavkaObem }}</b></span>
            <span class="button" style="width:70px;">Вес:</span>
            <span class="button" style="width:100px;"><b>{{ dostavkaVes }}</b></span>
          </div>
        </div>
        <div class="level-item">
          <div class="buttons has-addons">
            <span class="button" style="width:80px;">Приход:</span>
            <span class="button" style="width:110px;"><b>{{ dostavkaPrihod }}</b></span>
            <span class="button" style="width:120px;">Расход план:</span>
            <span class="button" style="width:100px;"><b>{{ dostavkaRashodPlan }}</b></span>
            <span class="button" style="width:120px;">Расход факт:</span>
            <span class="button" style="width:100px;"><b>{{ dostavkaRashodFact }}</b></span>
            <span class="button" style="width:80px;">Прибыль:</span>
            <span class="button" style="width:100px;"><b>{{ dostavkaSum }}</b></span>
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
      :data="dostavka_list_filtered"
      :loading="(!is_dostavka_loaded || ExpDostavkaFormActive)"
      :bordered="false"
      :striped="true"
      :hoverable="true"
      :narrowed="true"
      :paginated="true"
      :per-page="40"
		:detailed="true"
		detail-key="id"
		:opened-detailed="openedDetailed"
		@select="expandRow"
      :row-class="rowClass">

      <template slot-scope="props">

        <b-table-column label="#">
          {{ props.row.id }}
        </b-table-column>

        <b-table-column label="Номер">
          {{ props.row.number }}
        </b-table-column>

        <b-table-column label="Дата">
          {{ props.row.date }}
        </b-table-column>

       <b-table-column label=" " width="92" v-on:click.native="expandRow(props.row)">

          <a class="button is-success" title="Редактировать"
            @click="editDostavka(props.row.id)">
            <b-icon icon="pencil"></b-icon>
          </a>

          <a class="button is-danger" title="Удалить"
            @click="delDostavka(props.row.id, props.row.number, props.row.status_id)">
            <b-icon icon="close"></b-icon>
          </a>

          <a class="button is-info" title="Завершить"
            @click="doneDostavka(props.row.id, props.row.number)" v-if="props.row.status_id==1">
            <b-icon icon="package-down"></b-icon>
          </a>

          <a class="button is-info" title="На формировку"
            @click="returnDostavka(props.row.id, props.row.number)" v-if="props.row.status_id==2">
            <b-icon icon="package-up"></b-icon>
          </a>

          <a class="button is-warning" title="В архив"
            @click="archDostavka(props.row.id, props.row.number)" v-if="props.row.status_id==2">
            <b-icon icon="redo"></b-icon>
          </a>

        </b-table-column>

        <b-table-column label="Автомобиль">
          <div> {{ props.row.car }} </div>
        </b-table-column>

        <b-table-column label="Исполнитель">
          <div> {{ props.row.client_name }} </div>
        </b-table-column>
 
        <b-table-column label="Заявок">
          <div> {{ props.row.count_orders }} </div>
        </b-table-column>

        <b-table-column label="Вес">
          {{ props.row.sum_ves }} / {{ props.row.car_ves }}
        </b-table-column>

         <b-table-column label="Объем">
          {{ props.row.sum_obem }} / {{ props.row.car_obem }}
        </b-table-column>

         <b-table-column label="Мест">
          {{ props.row.sum_mest }} / {{ props.row.car_mest }}
        </b-table-column>

        <b-table-column label="Палет">
          {{ props.row.sum_palet }} / {{ props.row.car_palet }}
        </b-table-column>

         <b-table-column label="Приход">
          {{ props.row.sum_prihod }}
        </b-table-column>

         <b-table-column label="Расход план">
          {{ props.row.sum_rashod_plan }}
        </b-table-column>

         <b-table-column label="Расход факт">
          {{ props.row.sum_rashod_fact }}
        </b-table-column>

         <b-table-column label="Прибыль">
          {{ props.row.sum_dostavka }}
        </b-table-column>

      </template>

      <template slot="detail" slot-scope="props">

		<b-table
	      :data="order_list_detailed"
	      :bordered="true"
	      :striped="true"
	      :hoverable="true"
	      :narrowed="true"
	      :paginated="false">

	      <template slot-scope="props">

      		<b-table-column label="Номер">
      		  {{ props.row.number }}
      		</b-table-column>

      		<b-table-column label="Получатель">
      		  {{ props.row.poluchatel_name }}
      		</b-table-column>

      		<b-table-column label="Адрес доставки">
      		  {{ props.row.dostavka_addr }}
      		</b-table-column>

      		<b-table-column label="Груз">
      		  {{ props.row.gruz_name }}
      		</b-table-column>

      		<b-table-column label="Вес">
      		  {{ props.row.ves }}
      		</b-table-column>

      		<b-table-column label="Объем">
      		  {{ props.row.obem }}
      		</b-table-column>

      		<b-table-column label="Мест">
      		  {{ props.row.mest }}
      		</b-table-column>

      		<b-table-column label="Палет">
      		  {{ props.row.palet }}
      		</b-table-column>

	      </template>

	    </b-table>

      </template>

    </b-table>
    </div>

  </div>
</template>

<script>
import bus from "@/components/bus"
import ExpDostavkaForm from '@/components/ExpDostavkaForm'

export default {
  name: 'ExpDostavka',
  components: {
    ExpDostavkaForm
  },
  data () {
    return {
 		openedDetailed: [],
    	is_filter1: false,
    	is_filter2: false,
    	is_filterSearch: '',
    	dostavka_list: [],
    	order_list: [],
    	is_dostavka_loaded: false,
      user_id: 0,
    	ExpDostavkaForm,
    	ExpDostavkaFormActive: false,
    	ExpDostavkaFormProps: { id: 0, is_saveallow: 0 }
    }
  },
  created() {
    this.user_id = this.$store.getters['auth/userInfo']['id'];
    this.runner();
  },
  mounted() {
    bus.$on('dostavka_submit', this.onDostavkaSubmit);
  },
  computed: {
    dostavkaPrihod() {
      let sum = 0;
      for (let i = this.dostavka_list_filtered.length - 1; i >= 0; i--) {
        sum = sum + Number(this.dostavka_list_filtered[i].sum_prihod)
      }
      return sum;
    },
    dostavkaRashodPlan() {
      let sum = 0;
      for (let i = this.dostavka_list_filtered.length - 1; i >= 0; i--) {
        sum = sum + Number(this.dostavka_list_filtered[i].sum_rashod_plan)
      }
      return sum;
    },
    dostavkaRashodFact() {
      let sum = 0;
      for (let i = this.dostavka_list_filtered.length - 1; i >= 0; i--) {
        sum = sum + Number(this.dostavka_list_filtered[i].sum_rashod_fact)
      }
      return sum;
    },
    dostavkaSum() {
      let sum = 0;
      for (let i = this.dostavka_list_filtered.length - 1; i >= 0; i--) {
        sum = sum + Number(this.dostavka_list_filtered[i].sum_dostavka)
      }
      return sum;
    },
    ordersCount() {
      let sum = 0;
      for (let i = this.dostavka_list_filtered.length - 1; i >= 0; i--) {
        sum = sum + Number(this.dostavka_list_filtered[i].count_orders)
      }
      return sum;
     },
    dostavkaCount() {
      return this.dostavka_list_filtered.length;
    },
    dostavkaObem() {
      let sum = 0;
      for (let i = this.dostavka_list_filtered.length - 1; i >= 0; i--) {
        sum = sum + Number(this.dostavka_list_filtered[i].sum_obem)
      }
      return Math.round(sum * 100) / 100
    },
    dostavkaVes() {
      let sum = 0;
      for (let i = this.dostavka_list_filtered.length - 1; i >= 0; i--) {
        sum = sum + Number(this.dostavka_list_filtered[i].sum_ves)
      }
      return Math.round(sum * 100) / 100
    },
    dostavka_list_filtered() {
      return this.dostavka_list.filter((el) => {
        let filter_search = true;
        if (this.is_filterSearch) {
          let search_str = new RegExp(this.is_filterSearch, 'i');
          filter_search = (el.number.match(search_str) ||
                            el.car.match(search_str) ||
                            el.client_name.match(search_str))
        }
        return (filter_search);
     })
    },
    order_list_detailed() {
      return this.order_list.filter((el) => {
        let filter_search = true;
        filter_search = (el.dostavka_id == this.openedDetailed[0])
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
    // set row color
    rowClass(row, index) {
       if (row.status_id == 2) {
         return 'is-green'
       }
       return ''
    },
	// expand row (open detailed view) on click
	expandRow (row) {
		if (this.openedDetailed[0] == [row.id][0]) {
			this.openedDetailed = []
		} else {
			this.openedDetailed = [row.id]
		}
	},
    editDostavka(id) {
      this.ExpDostavkaFormProps['id'] = Number(id);
      this.ExpDostavkaFormProps['is_saveallow'] = 1;
      this.ExpDostavkaFormActive = true;
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
    delDostavka(id, number, status_id) {
      if ((status_id == 2) && (this.user_id != 3)) { return; }
      this.$dialog.confirm({
        type: 'is-danger',
        title: 'Удалить экспедицию № '+ number +' ?',
        message: 'Подтвердите удаление!',
        confirmText: 'Да',
        cancelText: 'Отмена',
        onConfirm: (value) => {
          this.axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
          let qs = require('qs');
          let data_post = qs.stringify({
            id: id,
            del_note: 'delete'
          });
          this.axios.post('http://xxx.ru/api/dostavka_del.php', data_post)
          .then((response) => {
            let result = response.data;
            if (result.error) {
              this.msgError(result.error_text);
            } else  {
              this.onDostavkaSubmit();
            }
            return null;
          })
          .catch((error) => {
            console.log(error);
            this.msgError(error);
          });
        }
      })
    },
    doneDostavka(id, number) {
      this.$dialog.confirm({
        type: 'is-info',
        title: '"Экспедиция" № '+ number +' завершена?',
        message: 'Подтвердите завершение!',
        confirmText: 'Да',
        cancelText: 'Отмена',
        onConfirm: (value) => {
          this.axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
          let qs = require('qs');
          let data_post = qs.stringify({
            id: id,
            status: '2'
          });
          this.axios.post('http://xxx.ru/api/dostavka_set_status.php', data_post)
          .then((response) => {
            let result = response.data;
            if (result.error) {
              this.msgError(result.error_text);
            } else  {
              this.onDostavkaSubmit();
            }
            return null;
          })
          .catch((error) => {
            console.log(error);
            this.msgError(error);
          });
        }
      })
    },
    returnDostavka(id, number) {
      this.$dialog.confirm({
        type: 'is-info',
        title: '"Вернуть экспедицию" № '+ number +' на формировку?',
        message: 'Подтвердите возврат!',
        confirmText: 'Да',
        cancelText: 'Отмена',
        onConfirm: (value) => {
          this.axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
          let qs = require('qs');
          let data_post = qs.stringify({
            id: id,
            status: '1'
          });
          this.axios.post('http://xxx.ru/api/dostavka_set_status.php', data_post)
          .then((response) => {
            let result = response.data;
            if (result.error) {
              this.msgError(result.error_text);
            } else  {
              this.onDostavkaSubmit();
            }
            return null;
          })
          .catch((error) => {
            console.log(error);
            this.msgError(error);
          });
        }
      })
    },
    archDostavka(id, number) {
      this.$dialog.confirm({
        type: 'is-info',
        title: 'Переместить экспедицию № '+ number +' в архив?',
        message: 'Подтвердите передачу в архив!',
        confirmText: 'Да',
        cancelText: 'Отмена',
        onConfirm: (value) => {
          this.axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
          let qs = require('qs');
          let data_post = qs.stringify({
            id: id,
            status: '3',
            set_date: '1'
          });
          this.axios.post('http://xxx.ru/api/dostavka_set_status.php', data_post)
          .then((response) => {
            let result = response.data;
            if (result.error) {
              this.msgError(result.error_text);
            } else  {
              this.onDostavkaSubmit();
            }
            return null;
          })
          .catch((error) => {
            console.log(error);
            this.msgError(error);
          });
        }
      })
    },
    filterSearchClean() {
      this.is_filterSearch = '';
    },
    onDostavkaSubmit() {
      this.is_dostavka_loaded = false;
      this.getData();
    },
    getData() {
      this.axios.get('http://xxx.ru/api/dostavka_list_get.php').then((response) => {
        if (response.data.error == 0) {
          this.dostavka_list = response.data.data.dostavkas
          this.order_list = response.data.data.orders
         this.is_dostavka_loaded = true
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


<style>
  tr.is-yellow {
    background: yellow !important;
  }
  tr.is-green {
    background: lightgreen !important;
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
