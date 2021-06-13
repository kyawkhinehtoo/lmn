<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">
                Packages
            </h2>
        </template>

        <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between space-x-2 items-end mb-2 px-1 md:px-0">
            <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full md:w-64" id="search" type="text" placeholder="Search . . ."  v-model="search" v-on:keyup.enter="searchTsp">
           
            <button @click="openModal"  class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
              Create</button>
            </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg"  v-if="packages.data">
                 <!-- <button @click="openModal" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-3">Create New package</button>
                 <input class="w-half rounded py-2 my-3 float-right" type="text" placeholder="Search package" v-model="search" v-on:keyup.enter="searchTsp">
                    -->
         
                  
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bundle Equiptment</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contract Terms</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="row in packages.data " v-bind:key="row.id">
                                <td class="px-6 py-3 whitespace-nowrap">{{ row.id }}</td>
                                <td class="px-6 py-3 whitespace-nowrap">{{ row.name }}</td>
                                <td class="px-6 py-3 whitespace-nowrap"><Bundle :data="row.id" /></td>
                                <td class="px-6 py-3 whitespace-nowrap">{{ row.contract_period }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" @click="edit(row)"  class="text-indigo-600 hover:text-indigo-900">Edit</a> | 
                                    <a href="#" @click="deleteRow(row)"  class="text-red-600 hover:text-red-900">Delete</a>
                                    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div ref="isOpen" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
                      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        
                        <div class="fixed inset-0 transition-opacity">
                          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                        </div>
                        <!-- This element is to trick the browser into centering the modal contents. -->
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                        <form @submit.prevent="submit">
                          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="">
                                  <div class="mb-4">
                                      <label for="city" class="block text-gray-700 text-sm font-bold mb-2">City:</label>
                                      <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="city" placeholder="Enter City" v-model="form.city">
                                      <div v-if="$page.props.errors.city" class="text-red-500">{{ $page.props.errors.city[0] }}</div>
                               
                                  </div>
                                  <div class="mb-4">
                                      <label for="package" class="block text-gray-700 text-sm font-bold mb-2">package:</label>
                                      <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="package" placeholder="Enter package" v-model="form.name">
                                      <div v-if="$page.props.errors.name" class="text-red-500">{{ $page.props.errors.name[0] }}</div>
                               
                                  </div>
                                  <div class="mb-4">
                                      <label for="short_code" class="block text-gray-700 text-sm font-bold mb-2">Short Code:</label>
                                      <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="short_code" placeholder="Enter Short Code" v-model="form.short_code">
                                      <div v-if="$page.props.errors.short_code" class="text-red-500">{{ $page.props.errors.short_code[0] }}</div>
                               
                                  </div>
                            </div>
                          </div>
                          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                              <button wire:click.prevent="submit" type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition" v-show="!editMode">
                                Save
                              </button>
                            </span>
                            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                              <button wire:click.prevent="submit" type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition" v-show="editMode">
                                Update
                              </button>
                            </span>
                            <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                              
                              <button @click="closeModal" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                                Cancel
                              </button>
                            </span>
                          </div>
                          </form>
                          
                        </div>
                      </div>
                    </div>
                </div>
            <span v-if="packages.links">
                <pagination class="mt-6" :links="packages.links"  />
            </span>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout';
    import Pagination from '@/Components/Pagination';
    import Bundle from '@/Components/Bundle';
    import { reactive, ref, onMounted } from 'vue';
    import { Inertia } from '@inertiajs/inertia'
    export default {
    name: 'package',
    components: {
              AppLayout,
              Pagination,
              Bundle
          },
    //props: ['packages', 'errors'], 
    props: {
        packages : Object,
        errors : Object,
        bundles: String
    },
   setup(props){

      const form = reactive({
          id : null,
          name: null,
          city: 'Yangon',
          short_code : null
        })
        const search = ref('')
        let editMode = ref(false)
        let isOpen = ref(false)
        let bundle = ref('')
        let id = ref(null)
        function resetForm(){
              form.name = null
              form.city = 'Yangon'
              form.short_code = null    
        }
        function submit() {
           if(! editMode.value){
            form._method = 'POST';
            Inertia.post('/package', form, {
            preserveState: true,
            onSuccess: (page) => {
                closeModal()
                resetForm()
                Toast.fire(
                  {
                    icon:'success',
                    title: page.props.flash.message
                  }
                )
               
            },
            onError: (errors) => {
                 closeModal()
                 console.log('error ..'. errors)
            }
            });
           }else{
                
                form._method = 'PUT';form._method = 'PUT';

                Inertia.post('/package/' + form.id, form,{
                onSuccess: (page) => {
                closeModal()
                resetForm()
                Toast.fire(
                  {
                    icon:'success',
                    title: page.props.flash.message
                  })
                },

                onError: (errors) => {
                    closeModal()
                    console.log('error ..'. errors)
                }

                })
                
           }

        }
        function edit (data) {
              form.id = data.id
              form.name = data.name
              form.city = data.city
              form.short_code = data.short_code  
              editMode.value = true
              openModal()
        }

        function deleteRow(data) {
                if (!confirm('Are you sure want to remove?')) return;
                data._method = 'DELETE';
                Inertia.post('/package/' + data.id, data)
                closeModal()
                resetForm()
        }
        function openModal() {
          isOpen.value = true
          console.log(props.packages.name)
        }
        const closeModal = () =>{
          isOpen.value = false
          resetForm()
          editMode.value=false
        }
        const searchTsp = () =>{
          console.log('search value is' + search.value)
          Inertia.get('/package/', {package : search.value}, { preserveState: true })
        }
        // const getBundles = async($id) => {
        //       let url = '/getpackage/'+$id;
        //       try{
        //         const res = await fetch(url);
        //         const data = await res.json();
        //         return data;
        //       }catch(err){
        //         console.error(err)
        //       }      
        //  }
        //  const showBundle =($id)=> {
        //    console.log('id is ' + $id)
        //   const data = getBundles($id).then((data) => {
        //     data.map(x=> x.bundle_name + ' ' )
        //    })
        //    this.props.packages.filter(o => o.id === id).forEach(o => o.bundles = data );
        // }
        // const showBundle = computed(($id)=>{
        //   getBundles($id).then((data) => {
        //   const list = data.map(x=> x.bundle_name + ' ' )
        //   console.log(list)
        //   });
        // })
      //   onMounted(() => {
        
      //     this.props.packages.forEach(o => {
      //       console.log('id is ' + o.id)
      //       this.showBundle(o.id)
      //     } );
      //  })

       return { form, submit,editMode,isOpen,openModal, closeModal, edit,deleteRow,searchTsp, search}
      }
      
  }
</script>
