<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">DN Setup</h2>

    </template>
    
    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between space-x-2 items-end mb-2 px-1 md:px-0">
          <div class="relative flex flex-wrap" >
           
            <span class="z-10 h-full leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"><i class="fas fa-search"></i></span>
            <input type="text" placeholder="Search here..." class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10" id="search" v-model="search" v-on:keyup.enter="searchPort" />
          
          </div>
          <button @click="()=>{showDN = true, editMode=false}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Create</button>
          </div>
         
          <!-- Tabs -->

        <!-- <div class="inline-flex w-full divide-y divide-gray-200">
          <ul id="tabs" class="flex">
            <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider" :class="[tab == 1 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#" @click="tabClick(1)" preserve-state>Genaral</a></li>
            <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-900 uppercase tracking-wider" :class="[tab == 2 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#" @click="tabClick(2)" preserve-state>Details</a></li>
          </ul>
        </div> -->
  
       <div class="col-1">
         
           <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="overall.data">
            <table class="min-w-full divide-y divide-gray-200 table-auto ">
            <thead class="bg-gray-50">
              <tr class="text-left">
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">No.</th>
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">DN Name</th>
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Total SN</th>
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Description</th>
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Location</th>
                <th scope="col" class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Input dbm</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in overall.data" v-bind:key="row.id">
                <td class="px-6 py-3 font-medium">{{ overall.from + index }}</td>
                <td class="px-6 py-3 font-medium">{{ row.name }}</td>
                <td class="px-6 py-3 font-medium">{{ row.ports }}</td>
                <td class="px-6 py-3 font-medium">{{ row.description }}</td>
                <td class="px-6 py-3 font-medium">{{ row.location }}</td>
                <td class="px-6 py-3 font-medium">{{ row.input_dbm }}</td>
                <td class="px-6 py-3 font-medium text-right">
                  <a href="#" @click="editDN(row)" class="text-indigo-600 hover:text-indigo-900">Edit</a> |
                  <a href="#" @click="confirmDelete(row.id)" class="text-red-600 hover:text-red-900">Delete</a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
          <span v-if="overall.total" class="w-full block mt-4">
            <label class="text-xs text-gray-600">{{ overall.data.length }} DN List in Current Page. Total Number of DNs :  {{ overall.total }}</label>
        </span>
        <span v-if="overall.links">
          <pagination class="mt-6" :links="dns.links" />
        </span>
     
       <!-- <div v-show="tab == 1">
       <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" v-if="overall">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DN Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Ports</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in overall.data" v-bind:key="row.name">
                <td class="px-6 py-3 whitespace-nowrap">{{ index + 1 }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.name }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.ports }}</td>
                <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                  <a href="#" @click="confirmDelete(row.name)" class="text-red-600 hover:text-red-900">Delete</a>
                </td>
              </tr>
            </tbody>
          </table>
           
        </div>
         <span v-if="dns.links">
          <pagination class="mt-6" :links="overall.links" />
        </span>
       </div> -->
       
       </div>
 
    
        
      </div>
    </div>
  </app-layout>
  <jet-confirmation-modal :show="dn_id" @close="dn_id = null">
    <template #title> Delete Node</template>
    <template #content> Are you sure you would like to delete this DN ? It might effect to Customer Data ! </template>
    <template #footer>
      <jet-secondary-button @click="dn_id = null"> Cancel </jet-secondary-button>
      <jet-danger-button class="ml-2" @click="deleteNode"> Delete </jet-danger-button>
    </template>
  </jet-confirmation-modal>
  <jet-dialog-modal :show="showDN" @close="showDN = false">
    <template #title> Add New Port </template>
    <template #content>
      <div>
        <div v-if="$page.props.errors[0]" class="text-red-500">{{ $page.props.errors[0] }}</div>
        <div class="mt-4 text-sm">
          <div class="mb-4 md:col-span-1">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">DN Name :</label>
            <input type="text"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              id="name" placeholder="Enter DN Name" v-model="form.name"/>
            <div v-if="$page.props.errors.name" class="text-red-500">{{ $page.props.errors.name }}
            </div>
          </div>
          <div class="mb-4 md:col-span-1">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description :</label>
            <textarea
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              id="description" placeholder="Enter Description" v-model="form.description"/>
            <div v-if="$page.props.errors.description" class="text-red-500">{{ $page.props.errors.description }}
            </div>
          </div>
          <div class="mb-4 md:col-span-1">
            <label for="location" class="block text-gray-700 text-sm font-bold mb-2">DN Location :</label>
            <input type="text"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              id="location" placeholder="Enter Location (Lat,Long)" v-model="form.location"/>
            <div v-if="$page.props.errors.location" class="text-red-500">{{ $page.props.errors.location }}
            </div>
          </div>
          <div class="mb-4 md:col-span-1">
            <label for="input_dbm" class="block text-gray-700 text-sm font-bold mb-2">DN Input dbm :</label>
            <input type="text"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              id="input_dbm" placeholder="Enter Input Dbm" v-model="form.input_dbm"/>
            <div v-if="$page.props.errors.input_dbm" class="text-red-500">{{ $page.props.errors.input_dbm }}
            </div>
          </div>
         
        </div>
      </div>
    </template>
    <template #footer>
      <jet-secondary-button @click="cancelDN"> Close </jet-secondary-button>
      <jet-button class="ml-2" @click="saveDN"> Save </jet-button>
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
  name: "ports",
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
    dns: Object,
    dns_all: Object,
    sns: Object,
    overall: Object,
    errors: Object,
  },
  setup(props) {
    let dn_id = ref(null);
    let showDN = ref(false);
    let search = ref('');
    let editMode = ref(false);


    const form = useForm({
      id: null,
      dn_id: null,
      dn: null,
      name: null,
      description: null,
      location: null,
      input_dbm: null,
      tab :1,
    });
    function confirmDelete(data) {
      dn_id.value = data;
    }
    function resetForm() {
      form.id = null;
      form.name = null;
      form.dn = null;
      form.description = null;
      form.location = null;
      form.input_dbm = null;
    }
    function editDN(data) {
      form.id = data.id;
      form.name = data.name;
      form.description = data.description;
      form.location = data.location;
      form.input_dbm = data.input_dbm;
      showDN.value = true;
      editMode.value = true;

    }

    function saveDN() {
      if (!editMode.value) {
        form._method = "POST";
        form.post("/port", {
          preserveState: true,
          onSuccess: (page) => {
            showDN.value = false;
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
        form.put("/port/"+form.id, {
          preserveState: true,
          onSuccess: (page) => {
            showDN.value = false;
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
    function cancelDN(){
        showDN.value = false;
        resetForm();
    }
    function searchPort(){
      Inertia.get('/port/', {keyword : search.value}, { preserveState: true })
    }
    function deleteNode() {
      let data = Object({});
      data.id = dn_id.value;
      data._method = "DELETE";

          Inertia.post("/port/" + data.id, data, {
          preserveScroll: true,
          preserveState: true,
          onSuccess: (page) => {
            dn_id.value = false;
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
    return { dn_id, saveDN,editDN,cancelDN , showDN, form,editMode, deleteNode, confirmDelete,searchPort,search
    
    };
  },
};
</script>

<style>
</style>