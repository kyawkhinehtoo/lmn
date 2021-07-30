<template>
<div class="w-full flex justify-end text-sm px-2">
<label class="text-sm mt-5"> Reload Alarm List</label> 
<button @click="getList()" class="my-2 ml-2 align-right text-center items-center px-4 py-3 bg-indigo-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-400 active:bg-indigo-600 focus:outline-none focus:border-gray-900 disabled:opacity-25 transition mr-1"> <i class="fas fa-sync opacity-75 text-sm"></i></button>
</div>
<div class="grid grid-cols-2 gap-2">

  <div v-if="bundle" class="col-span-1">
  
             
    <table class="min-w-full divide-y divide-gray-200 mt-1 shadow-xl  ">
                <thead class="bg-gray-50 rounded-t-lg block  ">
                  <tr>
                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket ID</th>
                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overdue</th>
                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SLA</th>
                  
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-sm text-center max-h-96 overflow-auto block scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-white">
                  <tr v-for="row in bundle" v-bind:key="row.id" class="cursor-pointer" >
                   <td class="px-2 py-2 whitespace-nowrap">{{ row.code }}</td>
                   <td class="px-2 py-2 whitespace-nowrap tooltip">
                     <span class="tooltiptext text-xs">
                       Time after Incident <br />
                       {{getDay(row.diff)}}</span>
                       {{getDay(row.over)}}
                   </td>
                   <td class="px-2 py-2 whitespace-nowrap">{{ row.percentage }}</td>
                    
                  </tr>
                </tbody>
    </table>
  </div>
  <div v-if="remain.length > 0" class="col-span-1">
 
    <table class="min-w-full divide-y divide-gray-200 mt-1 shadow-xl">
                <thead class="bg-gray-50 rounded-t-lg block  ">
                  <tr>
                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket ID</th>
                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remain</th>
                    <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SLA</th>
                  
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-sm text-center  max-h-96 overflow-auto block scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-white">
                  <tr v-for="row in remain" v-bind:key="row.id" class="cursor-pointer" >
                   <td class="px-2 py-2 whitespace-nowrap">{{ row.code }}</td>
                   <td class="px-2 py-2 whitespace-nowrap tooltip" >
                    
                     {{ getDay(row.diff) }}
                     
                   </td>
                   <td class="px-2 py-2 whitespace-nowrap">{{ row.percentage }}</td>
                    
                  </tr>
                </tbody>
    </table>
  </div>
</div>
</template>

<script>
import { ref, onMounted, onUpdated } from "vue";
import Label from '../Jetstream/Label.vue';
export default {
  components: { Label },
  name: "IncidentAlert",
  setup() {
    let bundle = ref("Loading ..");
    let remain = ref("Loading ..");
    const getData = async () => {
      let url = "/incidentOverdue";
      console.log(url);
      try {
        const res = await fetch(url);
        const data = await res.json();
        return data;
      } catch (err) {
        console.error(err);
      }
      
    }
    const getRemain = async () => {
      let url = "/incidentRemain";
      console.log(url);
      try {
        const res = await fetch(url);
        const data = await res.json();
        return data;
      } catch (err) {
        console.error(err);
      }
      
    }
    function getDay(data){
        let minutes = (data)%3600/60;
        let hours = (data)%(24*3600)/3600;
        let days = (data)/(24*3600);
        let value = null;
  
        if(data >= 86400){
          let hrs = days - Math.floor(days)
          days = days - hrs
          hrs = hrs * 24
          let min = hrs -  Math.floor(hrs);
          hrs = hrs - min;
          if(hrs != 0){
            value = days+" d, "+ hrs +" hr";
          }else{
            value = days+" d"
          }
          
        }
        else if(data > 1440){
            let min = hours -  Math.floor(hours);
            let hr = hours - min;
             min = Math.round(min * 60);
            if(hr != 0){
              value = hr +" hr," + min +" m" ;
            }else{
               value =  min +" m" ;
            }
            
        }else{
            value = Math.round(minutes)+" m"
        }
        return value;
    }
    function percenttosecond(data){
      let a_month = 2592000;
      let second = a_month - (data/100) * a_month;
      return second;
    }
    function getList(){
        getData().then((d) => {
        bundle.value = d
       });
        getRemain().then((d) => {
        remain.value = d
       });
     
    }
    onMounted(() => {
      getList();
      //let timer = setInterval(getList, 60000);
    });
    return { bundle, remain, getList ,getDay,percenttosecond};
  },
};
</script>
<style scoped>
.tooltip {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black;
}

.tooltip .tooltiptext {
  visibility: hidden;
  min-width: 120px;
  background-color: #4f46e5;
  color: #fff;
  text-align: center;
  border-radius: 5px;
  padding: 5px 0;
  left:-25px;
  top:-3px;
  /* Position the tooltip */
  position: absolute;
  z-index: 1;
  opacity:0.9;
}

.tooltip:hover .tooltiptext {
  visibility: visible;
}
</style>