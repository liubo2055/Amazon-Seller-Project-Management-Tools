<template>
  <div class="pagination">
    <template v-if="visiblePages[0]!==1">
      <button
        class="btn btn-default btn-sm"
        type="button"
        @click="setPage(1)"
      >
        1
      </button>
      ...
    </template>
    <button
      type="button"
      v-for="buttonPage in visiblePages"
      :class="[
        'btn',
        page!==buttonPage?'btn-default':'btn-primary',
        'btn-sm'
      ]"
      @click="setPage(buttonPage)"
    >
      {{ buttonPage }}
    </button>
    <template v-if="visiblePages.indexOf(pages)===-1">
      ...
      <button
        class="btn btn-default btn-sm"
        type="button"
        @click="setPage(pages)"
      >
        {{ pages }}
      </button>
    </template>
  </div>
</template>
<script>
  export default {
    props:{
      page:{
        type:Number,
        required:true
      },
      pages:{
        type:Number,
        required:true
      }
    },
    computed:{
      visiblePages(){
        let page=1
        let end=this.pages

        if(this.pages>10){
          page=Math.max(this.page-5,1)
          end=page+9

          if(end>this.pages){
            end=this.pages
            page=end-9
          }
        }

        const pages=[]
        for(; page<=end; page++)
          pages.push(page)

        return pages
      }
    },
    methods:{
      setPage(page){
        this.$emit('page',{page})
      }
    }
  }
</script>
