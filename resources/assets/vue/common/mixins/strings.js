export default {
  methods:{
    _ix(message,context){
      return _ix(message,context)
    },
    intNumber(value){
      return numeral(value).format('0,0.')
    },
    decNumber(value){
      return numeral(value).format('0,0.00')
    },
    sprintf(fmt){
      return sprintf.apply(fmt,arguments)
    },
    ucfirst(string){
      return string.charAt(0).toUpperCase()+string.substr(1)
    }
  }
}
