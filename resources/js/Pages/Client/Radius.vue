<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Radius Report</h2>
    </template>

    <div class="py-2">
        <!-- Advance Search -->
      <div class="max-w-full mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="bg-white shadow sm:rounded-t-lg flex justify-between space-x-2 items-end py-2 px-2 md:px-2">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full">
            <div class="py-2 col-span-1 sm:col-span-1">
              <label for="sh_general" class="block text-sm font-medium text-gray-700">General </label>
              <div class="mt-1 flex rounded-md shadow-sm">
                <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                  <i class="fas fa-user"></i>
                </span>
                <input type="text" v-model="form.general" name="sh_general" id="sh_general" class="pl-10 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Ticket ID/Customer ID/Customer Name etc." tabindex="1" />
              </div>
            </div>
            <div class="py-2 col-span-3 sm:col-span-3">
              <label for="sh_type" class="block text-sm font-medium text-gray-700">Radius Status </label>
              <div class="mt-1 flex">
          
                    <label class="flex-auto items-center mt-1 ml-4"> <input type="radio" class="form-radio h-5 w-5 text-green-600" checked name="type" v-model="form.radius_status" value="online" /><span class="ml-2 text-gray-700">Online</span> </label>
                    <label class="flex-auto items-center mt-1 ml-4"> <input type="radio" class="form-radio h-5 w-5 text-blue-600" name="type" v-model="form.radius_status" value="offline" /><span class="ml-2 text-gray-700">Offline</span> </label>
                    <label class="flex-auto items-center mt-1 ml-4"> <input type="radio" class="form-radio h-5 w-5 text-red-500" name="type" v-model="form.radius_status" value="disabled" /><span class="ml-2 text-gray-700">Disabled</span> </label>
                    <label class="flex-auto items-center mt-1 ml-4"> <input type="radio" class="form-radio h-5 w-5 text-yellow-600" name="type" v-model="form.radius_status" value="not found" /><span class="ml-2 text-gray-700">Not Found</span> </label>
                    <label class="flex-auto items-center mt-1 ml-4"> <input type="radio" class="form-radio h-5 w-5 text-gray-600" name="type" v-model="form.radius_status" value="no account" /><span class="ml-2 text-gray-700">No Account</span> </label>
                 
                
              </div>
            </div>
            
      
          </div>
        </div>
        <div class="mb-2 py-2 px-2 md:px-2 bg-white shadow rounded-b-lg flex justify-between">
          <div class="flex">
            <a @click="submit" class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Search <i class="ml-1 fa fa-search text-white" tabindex="10"></i></a>
            <a @click="clearSearch" class="ml-2 cursor-pointer inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">Reset <i class="ml-1 fa fa-undo-alt text-white" tabindex="11"></i></a>
          </div>

          <div class="flex">
            <a @click="doExcel" class="cursor-pointer inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">Export Excel <i class="ml-1 fa fa-download text-white" tabindex="11"></i></a>
          </div>
        </div>
      </div>
      <!-- End of Advance Search -->
      <div class="max-w-full mx-auto sm:px-6 lg:px-8 mt-4">
        
         
    
        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="customers.data">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" v-if="radius">Radius</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" >ID</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" >Order Date</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" >Prefer Date</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" >Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Township</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="relative px-6 py-3">  <a @click="goAll" class="cursor-pointer items-center py-2 px-3 w-auto bg-white border border-transparent rounded-md font-semibold text-xs text-gray-200 tracking-widest hover:shadow-sm active:shadow-inner focus:outline-none focus:border-gray-50 focus:ring focus:ring-gray-100 disabled:opacity-25 transition"><i class="fa fas fa-undo"></i></a></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
              <tr v-for="row in customers.data" v-bind:key="row.id" :class='" text-"+row.color'>
              <td class="px-3 py-3 text-xs font-medium whitespace-nowrap" v-if="radius">
                <div class="text-xs inline-flex items-center font-medium leading-sm capitalize  px-3 py-1 rounded-full"
                :class="{'bg-green-200 text-green-700 ': row.radius_status == 'online',
                         'bg-blue-200 text-blue-700': row.radius_status == 'offline',
                         'bg-red-200 text-red-700': row.radius_status == 'disabled',
                         'bg-orange-200 text-orange-700': row.radius_status == 'not found',
                         'bg-white text-gray-700 border': row.radius_status == 'no account'
                
                }">{{ row.radius_status}}</div>
                 </td> 
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.ftth_id }}</td>
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.order_date }}</td>
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.prefer_install_date }}</td>
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.name }}</td>
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.package }}</td>
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.township }}</td>
                <td class="px-6 py-3 text-xs font-medium  whitespace-nowrap">{{ row.status }}</td>

                <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                  <inertia-link :href="route('customer.edit', row.id)" class="text-indigo-600 hover:text-indigo-900">Edit</inertia-link> |
                  <a href="#" @click="deleteRow(row)" class="text-red-600 hover:text-red-900">Delete</a>
                </td>
              </tr>
            </tbody>
          </table>

         
        </div>
         <span v-if="customers.total" class="w-full block mt-4">
            <label class="text-xs text-gray-600">{{ customers.data.length }} Customers in Current Page. Total Number of Customers :  {{ customers.total }}</label>
        </span>
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
import { useForm } from "@inertiajs/inertia-vue3";
import { onMounted, onUpdated, provide, ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import AdvanceSearch from "@/Components/AdvanceSearch";
export default {
  name: "Radius",
  components: {
    AppLayout,
    Pagination,
    AdvanceSearch,
  },
  props: {
    customers: Object,
    errors: Object,
    radius: Object
  },
  setup(props) {
     const form = useForm({
      general: null,
      radius_status: null,
    });

    function submit() {
      form.post(
        "/showRadius",
        {
          onSuccess: (page) => {
           
          },
          onError: (errors) => {
           
            console.log(errors);
          },
          onStart: () => {
           
          },
        },
        { preserveState: true }
      );
    }
 
   const doExcel = () => {
         axios.post("/RadiusExport", 
          form)
          .then(response => {
            console.log(response)
             var a = document.createElement("a");
              document.body.appendChild(a);
              a.style = "display: none";
            let blob = new Blob([response.data], { type: 'text/csv' }),
              url = window.URL.createObjectURL(blob)
              a.href = url;
              a.download = 'radius_users.csv';
              a.click();
              window.URL.revokeObjectURL(url);
           })
    };
    // const sortBy = (d) => {
    //   sort.value = d;
    //   sort.order = 'asc';
    //   if(sort.order == 'asc'){
    //     sort.order = 'desc';
    //   }
    //   console.log("search value is" + sort.value);
    //   Inertia.post('/customer/all/',{sort: sort.value, order:sort.order},{ preserveState: true });
    // };
     
     
   return { submit,form,doExcel};
  },
};
</script>
