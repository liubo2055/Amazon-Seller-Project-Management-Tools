<template>
  <popup
    :title="_ix('Payments','To-Dos')"
    @close="$emit('close')"
  >
    <template v-for="(payment,index) in payments">
      <hr v-if="index>0">
      <div class="row">
        <div class="col-sm-4">
          <h5>{{ _ix('Date','To-Dos') }}</h5>
        </div>
        <div class="col-sm-8">{{ payment.date }}</div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <h5>{{ _ix('Code','To-Dos') }}</h5>
        </div>
        <div class="col-sm-8">
          <span class="break-word">{{ payment.code }}</span>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <h5>{{ _ix('Amount','To-Dos') }}</h5>
        </div>
        <div class="col-sm-8">{{ sprintf(_ix('RMB %s','To-Dos'),decNumber(payment.amount)) }}</div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <h5>{{ _ix('Amount due','To-Dos') }}</h5>
        </div>
        <div class="col-sm-8">{{ sprintf(_ix('RMB %s','To-Dos'),decNumber(payment.amountDue)) }}</div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <h5>{{ _ix('Amount paid','To-Dos') }}</h5>
        </div>
        <div class="col-sm-8">{{ sprintf(_ix('RMB %s','To-Dos'),decNumber(payment.amountPaid)) }}</div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <h5>{{ _ix('Status','To-Dos') }}</h5>
        </div>
        <div class="col-sm-8">{{ payment.status }}</div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <h5>{{ _ix('Buyer email','To-Dos') }}</h5>
        </div>
        <div class="col-sm-8">
          <span class="break-word">{{ payment.buyerEmail }}</span>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <h5>{{ _ix('Buyer ID','To-Dos') }}</h5>
        </div>
        <div class="col-sm-8">
          <span class="break-word">{{ payment.buyerId }}</span>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <h5>{{ _ix('Notify ID','To-Dos') }}</h5>
        </div>
        <div class="col-sm-8">
          <span class="break-word">{{ payment.notifyId }}</span>
        </div>
      </div>
      <div
        class="row"
        v-if="payment.response"
      >
        <div class="col-sm-4">
          <h5>{{ _ix('Response','To-Dos') }}</h5>
        </div>
        <div class="col-sm-8 payment-popup-response">
          <pre v-html="payment.response"></pre>
        </div>
      </div>
      <div
        class="row text-danger"
        v-if="payment.error"
      >
        <div class="col-sm-4">
          <h5>{{ _ix('Error','To-Dos') }}</h5>
        </div>
        <div class="col-sm-8">{{ payment.error }}</div>
      </div>
    </template>
  </popup>
</template>
<script>
  import Popup from 'common/components/Popup'

  export default {
    components:{Popup},
    props:{
      loadUrl:{
        type:String,
        required:true
      }
    },
    data(){
      return {
        payments:{}
      }
    },
    created(){
      axios.post(this.loadUrl)
        .then(response=>{
          this.payments=response.data.payments
        })
    }
  }
</script>
