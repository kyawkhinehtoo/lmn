<template>
  <div v-if="data">
    <div v-html="bundle" >
    </div>
  </div>
</template>
  
<script>
 import { ref, onMounted } from 'vue';
export default {
    
    name:'Bundle',
    props: ['data'],
    setup(props){
    
    let bundle = ref('Loading ..')
    const getBundles = async() => {
              let url = '/getpackage/'+ props.data;
              console.log(url)
              try{
                const res = await fetch(url);
                const data = await res.json();
                return data;
              }catch(err){
                console.error(err)
              }      
    }
    onMounted(() => {
        
         getBundles().then((d) => {
           bundle.value = ''
           bundle.value += d.map( x=> { return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">'
             +x.bundle_name+ ' x '+ x.qty+ '</span>'; } )
         });
      
    })
    return {bundle};
    }
    
}
        
</script> 