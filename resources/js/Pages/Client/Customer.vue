<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Project Setup</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
         <!--  Search -->
        <div class="flex justify-between space-x-2 items-end mb-2 px-1 md:px-0">
          <div class="relative flex flex-wrap">
            <span class="z-10 h-full leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"><i class="fas fa-search"></i></span>
            <input type="text" placeholder="Search here..." class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10 py-2.5" id="search" v-model="search" v-on:keyup.enter="searchTsp" />
             <a href="#" class="mt-1 w-full text-right font-semibold text-xs underline" v-on:click="toggleAdv">Advance Search</a>
             <i class="fas fa-chevron-right text-blueGray-400" v-show="!show_search"></i>
              <i class="fas fa-chevron-down text-blueGray-400" v-show="show_search"></i>
          </div>
         <inertia-link :href="'/customer/create'" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Create</inertia-link>
        </div>
         <!-- End of Search -->
        <div v-show="show_search" >
         <advance-search @search_parameter="goSearch" /> 
        </div>
        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="customers.data">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" >Customer ID</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" >Order Date</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" >Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Township</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
              <tr v-for="row in customers.data" v-bind:key="row.id" :class='" text-"+row.color'>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.ftth_id.substring(0,7) }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.order_date }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.name }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.package }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.township }}</td>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.status }}</td>

                <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                  <inertia-link :href="route('customer.edit', row.id)" class="text-indigo-600 hover:text-indigo-900">Edit</inertia-link> |
                  <a href="#" @click="deleteRow(row)" class="text-red-600 hover:text-red-900">Delete</a>
                </td>
              </tr>
            </tbody>
          </table>

         
        </div>
        <span v-if="customers.links">
          <pagination class="mt-6" :links="customers.links" />
        </span>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Pagination from "@/Components/Pagination";
import { onMounted, onUpdated, provide, ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import AdvanceSearch from "@/Components/AdvanceSearch";
export default {
  name: "Customer",
  components: {
    AppLayout,
    Pagination,
    AdvanceSearch,
  },
  props: {
    customers: Object,
    packages: Object,
    projects: Object,
    townships: Object,
    status: Object,
    errors: Object,
  },
  setup(props) {
     provide('packages', props.packages);
     provide('projects', props.projects);
     provide('townships', props.townships);
     provide('status', props.status);
     const search = ref("");
     const sort = ref("");
     let show_search = ref(false);
    const searchTsp = () => {
      Inertia.post("/customer/search", { keyword: search.value }, { preserveState: true });
    };
    const goSearch = (parm) =>{
       let url = "/customer/search";
      Inertia.post(url,parm, { preserveState: true });
    }
    const toggleAdv =()=>{
      show_search.value = !show_search.value;
    }
    // const sortBy = (d) => {
    //   sort.value = d;
    //   sort.order = 'asc';
    //   if(sort.order == 'asc'){
    //     sort.order = 'desc';
    //   }
    //   console.log("search value is" + sort.value);
    //   Inertia.post('/customer/all/',{sort: sort.value, order:sort.order},{ preserveState: true });
    // };
    const deleteRow =(data)=>{
       if (!confirm("Are you sure want to Delete?")) return;
      data._method = "DELETE";
      Inertia.post("/customer/" + data.id, data);
    }
   return {  deleteRow, searchTsp,toggleAdv,goSearch,sort, search,show_search };
  },
};
</script>
