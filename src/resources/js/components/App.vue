<template>
  <div class="container pt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">Оформление заказа</div>
          <div class="card-body">
            <form>
              <div class="form-group">
                <label>Имя</label>
                <input type="text" v-model="name" class="form-control" />
              </div>
              <div class="form-group">
                <label>Телефон</label>
                <input type="tel" v-model="phone" class="form-control" />
              </div>
              <div class="form-group">
                <label>Адрес</label>
                <input type="text" v-model="address" class="form-control" />
              </div>
              <div class="form-group row">
                <div class="col-6">
                  <label>Тариф</label>
                  <select type="text" class="form-control" @change="selectTarif()" v-model="tarrif">
                    <option
                      v-for="(item, index) in info"
                      :key="item.id"
                      :value="index"
                    >{{item.name}}</option>
                  </select>
                </div>
                <div class="col-6">
                  <label>Первый день доставки</label>
                  <datepicker
                    format="DD-MM-YYYY"
                    name="date3"
                    :disabled-date="disabledDate"
                    :send-date="sendDate"
                  ></datepicker>
                </div>
              </div>

              <button type="submit" @click.prevent="sendOrder" class="btn btn-primary">Оформить</button>
            </form>
          </div>
          <div v-if="message" class="alert alert-success" role="alert">{{message}}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data: function () {
    return {
      info: null,
      tarrif: null,
      weekDays: null,
      name: "",
      phone: "",
      address: "",
      tarrif_id: null,
      myDate: null,
      message: false,
    };
  },

  methods: {
    disabledDate(date) {
      if (this.weekDays != null) {
        if (this.weekDays[date.getDay()] == "off") {
          return true;
        }
      }
    },
    sendDate(date) {
      this.myDate = date;
    },
    selectTarif() {
      this.weekDays = JSON.parse(this.info[this.tarrif].weekdays);
      this.tarrif_id = this.info[this.tarrif].id;
      this.myDate = null;
    },

    getTariffs() {
      axios.get("/api/getTariffs").then((response) => {
        this.info = response.data;
      });
    },

    sendOrder() {
      console.log(this.myDate);
      if (
        this.name === "" ||
        this.address === "" ||
        this.phone === "" ||
        this.tarrif_id === null ||
        this.myDate === null
      ) {
        return alert("Пожалуйста заполните все поля");
      } else {
        let data = {
          name: this.name,
          address: this.address,
          phone: this.phone,
          tarrif_id: this.tarrif_id,
          start: this.myDate,
        };
        axios.post("/api/createOrder", data).then((response) => {
          if (response.status == 200) {
            this.message = response.data;
            this.name = "";
            this.phone = "";
            this.address = "";
          }
        });
      }
    },
  },
  mounted() {
    this.getTariffs();
  },
};
</script>
