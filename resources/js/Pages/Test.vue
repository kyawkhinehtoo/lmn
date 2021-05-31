<template>
  <div>
     <button @click="handleClick" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-3">Create New Township</button>
    <span v-if="name">name</span>
  </div>
</template>

<script>
import {ref} from 'vue'
export default {
    name: 'Test',
    setup(){
        const name = ref('');

        const handleClick = () =>{
            console.log('click .. ')
            name.value="hello"
            
        }
    return {handleClick, name}
    }
}
</script>

<style>

</style>