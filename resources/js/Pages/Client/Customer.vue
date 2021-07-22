<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Project Setup</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between space-x-2 items-end mb-2 px-1 md:px-0">
          <div class="relative flex flex-wrap">
            <span class="z-10 h-full leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"><i class="fas fa-search"></i></span>
            <input type="text" placeholder="Search here..." class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10" id="search" v-model="search" v-on:keyup.enter="searchTsp" />
             <a href="#" class="mt-1 w-full text-right font-semibold text-xs underline" v-on:click="show = !show"><label v-if="show">Hide</label><label v-if="!show">Show</label> Advance Search</a>
       
          </div>
         
         
          <inertia-link :href="'/customer/create'" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Create</inertia-link>
        </div>
        <form @submit.prevent="submit">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg flex justify-between space-x-2 items-end mb-2 py-2 px-2 md:px-2" v-if="show">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full">
            <div class="col-span-1 sm:col-span-1">
              <div class="py-2">
                      <label for="first_name" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> First Name </label>
                      <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                          <i class="fas fa-user"></i>
                        </span>
                        <input type="text" v-model="form.first_name"  name="first_name" id="first_name" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Customer/Company Name"  />
                      
                      </div>
                      <p v-if="$page.props.errors.name" class="mt-2 text-sm text-red-500">{{ $page.props.errors.name }}</p>
                </div>
                <div class="py-2">
                      <label for="name" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> Enter Customer Name </label>
                      <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                          <i class="fas fa-user"></i>
                        </span>
                        <input type="text"  name="name" id="name" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Customer/Company Name"    />
                      
                      </div>
                      <p v-if="$page.props.errors.name" class="mt-2 text-sm text-red-500">{{ $page.props.errors.name }}</p>
                </div>
            </div>
            <div class="col-span-1 sm:col-span-1">
              <div class="py-2">
                      <label for="name" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> Enter Customer Info </label>
                      <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                          <i class="fas fa-user"></i>
                        </span>
                        <input type="text"  name="name" id="name" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Customer/Company Name"   />
                      
                      </div>
                      <p v-if="$page.props.errors.name" class="mt-2 text-sm text-red-500">{{ $page.props.errors.name }}</p>
                </div>
              <div class="py-2">
                      <label for="name" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> Enter Customer Info </label>
                      <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                          <i class="fas fa-user"></i>
                        </span>
                        <input type="text"  name="name" id="name" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Customer/Company Name"   />
                      
                      </div>
                      <p v-if="$page.props.errors.name" class="mt-2 text-sm text-red-500">{{ $page.props.errors.name }}</p>
                </div>
            </div>
            <div class="col-span-1 sm:col-span-1">
              <div class="py-2">
                      <label for="name" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> Enter Customer Info </label>
                      <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                          <i class="fas fa-user"></i>
                        </span>
                        <input type="text"  name="name" id="name" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Customer/Company Name"   />
                      
                      </div>
                      <p v-if="$page.props.errors.name" class="mt-2 text-sm text-red-500">{{ $page.props.errors.name }}</p>
                </div>
              <div class="py-2">
                      <label for="name" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> Enter Customer Info </label>
                      <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                          <i class="fas fa-user"></i>
                        </span>
                        <input type="text"  name="name" id="name" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Customer/Company Name"   />
                      
                      </div>
                      <p v-if="$page.props.errors.name" class="mt-2 text-sm text-red-500">{{ $page.props.errors.name }}</p>
                </div>
            </div>
            <div class="col-span-1 sm:col-span-1">
              <div class="py-2">
                      <label for="name" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> Enter Customer Info </label>
                      <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                          <i class="fas fa-user"></i>
                        </span>
                        <input type="text"  name="name" id="name" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Customer/Company Name"    />
                      
                      </div>
                      <p v-if="$page.props.errors.name" class="mt-2 text-sm text-red-500">{{ $page.props.errors.name }}</p>
                </div>
              <div class="py-2">
                      <label for="name" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> Enter Customer Info </label>
                      <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                          <i class="fas fa-user"></i>
                        </span>
                        <input type="text"  name="name" id="name" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Customer/Company Name"    />
                      
                      </div>
                      <p v-if="$page.props.errors.name" class="mt-2 text-sm text-red-500">{{ $page.props.errors.name }}</p>
                </div>
                <inertia-link :href="'/customer/create'" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Create</inertia-link>
        
            </div>
          </div>
        </div>
        </form>
        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="customers.data.length !== 0">
          <!-- <button @click="openModal" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-3">Create New Township</button>
                 <input class="w-half rounded py-2 my-3 float-right" type="text" placeholder="Search Township" v-model="search" v-on:keyup.enter="searchTsp">
                    -->

          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" @click="sortBy('cid')" >Customer ID</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" @click="sortBy('order')" >Order Date</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" @click="sortBy('cname')">Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" @click="sortBy('package')">Package</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" @click="sortBy('township')">Township</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" @click="sortBy('staus')">Status</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
              <tr v-for="row in customers.data" v-bind:key="row.id" :class='" text-"+row.color'>
                <td class="px-6 py-3 whitespace-nowrap">{{ row.ftth_id.substring(0,5) }}</td>
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
import { reactive, ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { useRemember } from '@inertiajs/inertia-vue3';

export default {
  name: "Customer",
  components: {
    AppLayout,
    Pagination,
  },
  props: {
    customers: Object,
    errors: Object,
  },
  setup(props) {
    const search = ref("");
    const sort = ref("");
    let show = ref(false);
    const form = useRemember({
        first_name: null,
        last_name: null,
    },'/customer');
  function deleteRow(data) {
      if (!confirm("Are you sure want to remove?")) return;
      data._method = "DELETE";
      Inertia.post("/customer/" + data.id, data);
      closeModal();
      resetForm();
    }
    const searchTsp = () => {
      console.log("search value is" + search.value);
      Inertia.post("/customer/all/", { keyword: search.value }, { preserveState: true });
    };
    const sortBy = (d) => {
      sort.value = d;
      sort.order = 'asc';
      if(sort.order == 'asc'){
        sort.order = 'desc';
      }
      console.log("search value is" + sort.value);
      Inertia.post('/customer/all/',{sort: sort.value, order:sort.order},{ preserveState: true });
    };

    return {  deleteRow, searchTsp, sortBy, form,sort, search,show };
  },
};
</script>
