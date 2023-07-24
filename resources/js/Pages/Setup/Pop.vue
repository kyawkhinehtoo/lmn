<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">POP Setup</h2>

    </template>
    
    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between space-x-2 items-end mb-2 px-1 md:px-0">
          <div class="relative flex flex-wrap" >
           
            <span class="z-10 h-full leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"><i class="fas fa-search"></i></span>
            <input type="text" placeholder="Search here..." class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10" id="search" v-model="search" v-on:keyup.enter="searchPort" />
          
          </div>
          <button @click="()=>{show = true, editMode=false}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Create</button>
          </div>
         
          <!-- Tabs -->

        <!-- <div class="inline-flex w-full divide-y divide-gray-200">
          <ul id="tabs" class="flex">
            <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider" :class="[tab == 1 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#" @click="tabClick(1)" preserve-state>Genaral</a></li>
            <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider" :class="[tab == 2 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#" @click="tabClick(2)" preserve-state>Details</a></li>
          </ul>
        </div> -->
  
       <div class="col-1">
         
           <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="pops.data">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site Description</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated At</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in pops.data" v-bind:key="row.id">
                <td class="px-6 py-3 whitespace-nowrap">{{ pops.from + index }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.site_name }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.site_description }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.created_at }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.updated_at }}</td>
                <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                  <a href="#" @click="edit(row)" class="text-indigo-600 hover:text-indigo-900">Edit</a> |
                  <a href="#" @click="confirmDelete(row.id)" class="text-red-600 hover:text-red-900">Delete</a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
          <span v-if="pops.total" class="w-full block mt-4">
            <label class="text-xs text-gray-600">{{ pops.data.length }} DN List in Current Page. Total Number of POP :  {{ pops.total }}</label>
        </span>
        <span v-if="pops.links">
          <pagination class="mt-6" :links="pops.links" />
        </span>
       </div>
 
    
        
      </div>
    </div>
  </app-layout>
  <jet-confirmation-modal :show="form.id" @close="form.id = null">
    <template #title> Delete Node</template>
    <template #content> Are you sure you would like to delete this DN ? It might effect to Customer Data ! </template>
    <template #footer>
      <jet-secondary-button @click="form.id = null"> Cancel </jet-secondary-button>
      <jet-danger-button class="ml-2" @click="deleteNode"> Delete </jet-danger-button>
    </template>
  </jet-confirmation-modal>
  <jet-dialog-modal :show="show" @close="show = false">
    <template #title> Add New Port </template>
    <template #content>
      <div>
        <div v-if="$page.props.errors[0]" class="text-red-500">{{ $page.props.errors[0] }}</div>
        <div class="mt-4 text-sm">
          <jet-input type="text" class="mt-1 block w-full" placeholder="POP Name" ref="text" v-model="form.site_name" />
          <jet-input-error :message="form.error" class="mt-2" />
       
          <jet-input type="text" class="mt-1 block w-full" placeholder="POP Description" ref="text" v-model="form.site_description" />

          <jet-input-error :message="form.error" class="mt-2" />
        </div>
      </div>
    </template>
    <template #footer>
      <jet-secondary-button @click="cancel"> Close </jet-secondary-button>
      <jet-button class="ml-2" @click="save"> Save </jet-button>
    </template>
  </jet-dialog-modal>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import JetButton from "@/Jetstream/Button";
import JetDialogModal from "@/Jetstream/DialogModal";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import JetDangerButton from "@/Jetstream/DangerButton";
import JetInput from "@/Jetstream/Input";
import JetInputError from "@/Jetstream/InputError";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import { reactive, ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { useForm } from "@inertiajs/inertia-vue3";
import Multiselect from "@suadelabs/vue3-multiselect";
export default {
  name: "pop",
  components: {
    AppLayout,
    Pagination,
    JetButton,
    JetDialogModal,
    JetSecondaryButton,
    JetDangerButton,
    JetConfirmationModal,
    JetInput,
    JetInputError,
    useForm,
    Multiselect,
  },
  props: {
    pops: Object,
    errors: Object,
  },
  setup(props) {
    let show = ref(false);
    let search = ref('');
    let editMode = ref(false);


    const form = useForm({
      id: null,
      site_name: null,
      site_description: null,

    });
    function confirmDelete(data) {
      form.id = data;
    }
  
    function resetForm() {
      form.id = null;
      form.site_name = null;
      form.site_description = null;
    }
    function edit(data) {
      form.id = data.id;
      form.site_name = data.site_name;
      form.site_description = data.site_description;
      show.value = true;
      editMode.value = true;
    }
    function cancel(){
      resetForm();
      show.value = false;
      editMode = false;
    }
    function save() {
      if (!editMode.value) {
        form._method = "POST";
        form.post("/pop", {
          preserveState: true,
          onSuccess: (page) => {
            show.value = false;
            resetForm();
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
          
            console.log("error ..".errors);
          },
        });
       
      } else {
        form._method = "PUT";
        form.put("/pop/"+form.id, {
          preserveState: true,
          onSuccess: (page) => {
            show.value = false;
            resetForm();
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
           
            console.log("error ..".errors);
          },
        });
      }
    }
    function searchPort(){
      Inertia.get('/pop/', {keyword : search.value}, { preserveState: true })
    }
    function deleteNode() {
      let data = Object({});
      data.id = form.id;
      data._method = "DELETE";
          Inertia.post("/pop/" + data.id, data, {
          preserveScroll: true,
          preserveState: true,
          onSuccess: (page) => {
            form.id = null;
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
            showSN.value = false;
            console.log("error ..".errors);
          },
          });

    }
    return {  save,edit, show, form,editMode,cancel, deleteNode, confirmDelete,searchPort,search };
  },
};
</script>

<style>
</style>