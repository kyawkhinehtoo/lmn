<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Bill List</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex">
        <label for="name" class="block text-sm font-bold text-gray-700 w-24 mt-3">Billing List :</label>
        <select id="id" v-model="form.id" name="id" class="ml-2 py-2.5 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" tabindex="1" required>
          <option value="0">-Choose Package-</option>
          <option v-for="row in lists" v-bind:key="row.id" :value="row.id">{{ row.name }}</option>
        </select>

        <a @click="view" class="ml-2 cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">View <i class="ml-1 fa fa-search text-white" tabindex="10"></i></a>
      </div>
   
      <div class="max-w-full mx-auto sm:px-6 lg:px-8 mt-4 flex justify-between">
      <div class="flex">
        <a href="#" class="w-full text-right font-semibold text-xs underline mr-2" v-on:click="toggleAdv">Advance Search</a>
        <i class="fas fa-chevron-right text-blueGray-400" v-show="!show_search"></i>
        <i class="fas fa-chevron-down text-blueGray-400" v-show="show_search"></i>
        
      </div>
      <div class="flex">
          <a @click="doExcel" class="cursor-pointer inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">Export Excel <i class="ml-1 fa fa-download text-white" tabindex="12"></i></a>
          </div>
      </div>
         <div v-show="show_search" class="max-w-full mx-auto sm:px-6 lg:px-8 mt-4">
          <billing-search @search_parameter="goSearch" />
        </div>
      <div v-if="billings" class="max-w-full mx-auto sm:px-6 lg:px-8 mt-4">
        <div class="p-3 inline-flex bg-white rounded-md mb-2 shadow-sm flex justify-between w-full">
            <button type="button" @click="generateAllPDF" class="h-10 text-md px-4 bg-blue-600 rounded text-white hover:bg-blue-700">Generate All PDF</button>
            <button type="button" @click="sendAllEmail" class="h-10 text-md px-4 bg-yellow-600 rounded text-white hover:bg-yellow-700">Send SMS to All </button>
            
            </div>
        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="billings.data">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="pl-3 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bill Number</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer ID</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Speed</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Payable</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PDF</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SMS Status</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SMS Sent Date</th>
                <!-- <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th> -->
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt Status</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row,index) in billings.data" v-bind:key="row.id">
                <td class="pl-4 px-2 py-3 text-xs whitespace-nowrap">{{ index += billings.from }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.bill_number }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.ftth_id }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.service_description }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.qty }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.usage_days }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.total_payable }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap"><span v-if="row.total_payable > 0">
                  <span v-if="row.file"><a :href="'/s/'+row.url">Download</a></span
                  ><span v-else><button type="button" @click="generatePDF(row.id)" class="h-8 text-md w-24 bg-blue-600 rounded text-white hover:bg-blue-700">Generate PDF</button></span>
                </span>
                </td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">
                  <span v-if="row.total_payable > 0">
                  <span v-if="row.status">{{ row.status }}</span
                  ><span v-else><button type="button" @click="sendEmail(row.id)" class="h-8 text-md w-20 bg-yellow-600 rounded text-white hover:bg-yellow-700">Send SMS</button></span>
                  </span>
                </td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.sent_date ? row.sent_date : "None" }}</td>
                <!-- <td class="px-2 py-3 text-xs whitespace-nowrap"><a :href="`/pdfpreview2/${row.id}`" target="_blank"><i class="fa fas fa-eye text-gray-400"></i></a></td> -->
                <!-- <td class="px-6 py-3 text-xs whitespace-nowrap text-right font-medium">
                  <a href="#" @click="edit(row)" class="text-yellow-600 hover:text-yellow-900"><i class="fas fa-pen"></i></a> |
                  <a href="#" @click="deleteRow(row)" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></a>
                </td> -->
                <td class="px-2 py-3 text-xs whitespace-nowrap">
                <button type="button" @click="openReceipt(row)" class="h-8 text-md w-24 bg-green-600 rounded text-white hover:bg-green-700">Open Receipt</button>
                </td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <span v-if="billings.total" class="w-full block mt-4">
            <label class="text-xs text-gray-600">{{ billings.data.length }} Invoices in Current Page. Total Number of Invoices :  {{ billings.total }}</label>
        </span>
        <span v-if="billings.links">
          <pagination class="mt-6" :links="billings.links" />
        </span>
           <div v-if="loading" wire:loading class="fixed top-0 left-0 right-0 bottom-0 w-full h-screen z-50 overflow-hidden bg-gray-700 opacity-75 flex flex-col items-center justify-center">
              <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12 mb-4"></div>
              <h2 class="text-center text-white text-xl font-semibold">Loading...</h2>
              <p class="w-1/3 text-center text-white">This may take a few seconds, please don't close this page.</p>
          </div>
      </div>
    </div>
     <div ref="isOpen" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
          <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity">
              <div class="absolute inset-0 bg-gray-500 opacity-75">
              </div>
            </div>
            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-7xl sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
              <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex justify-between w-full">
                <h1 class="flex text-indigo-700 text-md uppercase font-bold block pt-1 no-underline">Receipt Form</h1>
               <i class="flex fa fa-2x fas fa-times-circle text-red-500 hover:text-red-800 cursor-pointer" @click="closeModal"></i> 
              </div>
              <form @submit.prevent="submit">
        <div class="shadow overflow-hidden border-b border-gray-200">
          <table class="divide-y divide-gray-200 w-full">
            <tbody class="bg-white divide-y divide-gray-200">
              <tr>
                <td class="px-6 py-4 whitespace-nowrap" colspan="5">Client Name : {{form.bill_to}}</td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="2" rowspan="2">Package : {{form.service_description}} </td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap" colspan="5">Client ID : {{form.ftth_id}} </td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap" colspan="5">Address : {{form.attn}} </td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="2" rowspan="2">Internet Speed : {{ form.qty}}</td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap" colspan="5">Contact No. {{form.phone}}</td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">No.</td>
                <td class="px-6 py-4 whitespace-nowrap">Description</td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="2">Details</td>
                <td class="px-6 py-4 whitespace-nowrap">Price (USD/Kyat)</td>
                <td class="px-6 py-4 whitespace-nowrap">QTY</td>
                <td class="px-6 py-4 whitespace-nowrap">Amount (USD/Kyat)</td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">1</td>
                <td class="px-6 py-4 whitespace-nowrap">Internet Bill</td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="2">Internet bill for {{ getMonth(form.bill_month) }}/{{form.bill_year}}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{form.total_payable}}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{form.usage_days}}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{form.total_payable}}</td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">2</td>
                <td class="px-6 py-4 whitespace-nowrap">Device Name</td>

                <td class="px-6 py-4 whitespace-nowrap" colspan="2"><input type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="device_name"  v-model="form.device_name" /></td>

               

                <td class="px-6 py-4 whitespace-nowrap"><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="device_price"  v-model="form.device_price" @mouseleave="calc" /></td>
                 <td class="px-6 py-4 whitespace-nowrap" ><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="device_name_qty"  v-model="form.device_name_qty" @mouseleave="calc"  /></td>
                <td class="px-6 py-4 whitespace-nowrap"><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="device_amount"  v-model="form.device_amount" @mouseleave="calc" /></td>
              </tr>
               <tr>
                <td class="px-6 py-4 whitespace-nowrap">3</td>
                <td class="px-6 py-4 whitespace-nowrap">LAN</td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="2"><input type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="lan"  v-model="form.lan" /></td>
                <td class="px-6 py-4 whitespace-nowrap"><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="lan_price"  v-model="form.lan_price" @mouseleave="calc" /></td>
                 <td class="px-6 py-4 whitespace-nowrap"><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="lan_qty"  v-model="form.lan_qty" @mouseleave="calc" /></td>
                <td class="px-6 py-4 whitespace-nowrap"><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="lan_amount"  v-model="form.lan_amount" @mouseleave="calc" /></td>
              </tr>
               <tr>
                <td class="px-6 py-4 whitespace-nowrap">4</td>
                <td class="px-6 py-4 whitespace-nowrap">Setup Fees</td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="2"><input type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="setup_fees"  v-model="form.setup_fees"  /></td>
                <td class="px-6 py-4 whitespace-nowrap"><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="setup_fees_price"  v-model="form.setup_fees_price" @mouseleave="calc" /></td>
                <td class="px-6 py-4 whitespace-nowrap"><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="setup_fees_qty"  v-model="form.setup_fees_qty" @mouseleave="calc" /></td>
                <td class="px-6 py-4 whitespace-nowrap"><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="setup_fees_amount"  v-model="form.setup_fees_amount" @mouseleave="calc" /></td>
              </tr>
               <tr>
                <td class="px-6 py-4 whitespace-nowrap">5</td>
                <td class="px-6 py-4 whitespace-nowrap">FOC</td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="2"><input type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="foc"  v-model="form.foc"  /></td>
                <td class="px-6 py-4 whitespace-nowrap"><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="foc_price"  v-model="form.foc_price" @mouseleave="calc" /></td>
                <td class="px-6 py-4 whitespace-nowrap"><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="foc_qty"  v-model="form.foc_qty" @mouseleave="calc" /></td>
                <td class="px-6 py-4 whitespace-nowrap"><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="foc_amount"  v-model="form.foc_amount" @mouseleave="calc" /></td>
              </tr>
               <tr>
                <td class="px-6 py-4 whitespace-nowrap">6</td>
                <td class="px-6 py-4 whitespace-nowrap">Product ID</td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="2"><input type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="product_id"  v-model="form.product_id" /></td>
                <td class="px-6 py-4 whitespace-nowrap"><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="product_id_price"  v-model="form.product_id_price" @mouseleave="calc" /></td>
                 <td class="px-6 py-4 whitespace-nowrap"><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="product_id_qty"  v-model="form.product_id_qty" @mouseleave="calc" /></td>
                <td class="px-6 py-4 whitespace-nowrap"><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="product_id_amount"  v-model="form.product_id_amount" @mouseleave="calc" /></td>
              </tr>
               <tr>
                <td class="px-6 py-4 whitespace-nowrap">7</td>
                <td class="px-6 py-4 whitespace-nowrap">Device Rental</td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="2"><input type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="device_rental"  v-model="form.device_rental" /></td>
                <td class="px-6 py-4 whitespace-nowrap"><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="device_rental_price"  v-model="form.device_rental_price" @mouseleave="calc" /></td>
                <td class="px-6 py-4 whitespace-nowrap"><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="device_rental_qty"  v-model="form.device_rental_qty" @mouseleave="calc" /></td>
                <td class="px-6 py-4 whitespace-nowrap"><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="device_rental_amount"  v-model="form.device_rental_amount" @mouseleave="calc" /></td>
              </tr>
               <tr>
                <td class="px-6 py-4 whitespace-nowrap text-right" colspan="6">Sub Total (W/O commercial tax)</td>
                <td class="px-6 py-4 whitespace-nowrap"><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="sub_total"  v-model="form.sub_total" disabled /></td>
              </tr>
               <tr>
                <td class="px-6 py-4 whitespace-nowrap text-right" colspan="6">Total commercial tax</td>
                <td class="px-6 py-4 whitespace-nowrap"><span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                  <i class="fas fa-percent"></i>
                      </span><input type="number" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" id="commercial_tax"  v-model="form.commercial_tax" /></td>
              </tr>
               <tr>
                <td class="px-6 py-4 whitespace-nowrap text-right" colspan="6">Final Payment</td>
                <td class="px-6 py-4 whitespace-nowrap"><input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="final_payment"  v-model="form.final_payment" disabled /></td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">Cover Period</td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="6">{{form.period_covered}} </td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap"><strong>Remark</strong></td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="6"><textarea class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea></td>
              </tr>
            </tbody>
          </table>
        </div>
           <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                  <button @click="closeModal" type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">GO !</button>
                </span>
                <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                  <button @click="closeModal" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">Cancel</button>
                </span>
              </div>
              </form>
            </div>
          </div>
        </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { reactive, ref, provide, onMounted } from "vue";
import { Inertia } from "@inertiajs/inertia";
import Pagination from "@/Components/Pagination";
import BillingSearch from "@/Components/BillingSearch";
export default {
  name: "BillList",
  components: {
    AppLayout,
    Pagination,
    BillingSearch,
  },
  props: {
    lists: Object,
    billings: Object,
    packages: Object,
    projects: Object,
    townships: Object,
    status: Object,
    errors: Object,
  },
  setup(props) {

    provide("packages", props.packages);
    provide("projects", props.projects);
    provide("townships", props.townships);
    provide("status", props.status);

    let show_search = ref(false);
    let loading = ref(false);
    let parameter = ref("");
    let isOpen = ref(false);


    const form = reactive({
      id:null,
      customer_id: null,
      period_covered: null,
      bill_number: null,
      ftth_id: null,
      date_issued: null,
      bill_to: null,
      attn: null,
      previous_balance: null,
      current_charge: null,
      compensation: null,
      otc: null,
      sub_total: null,
      payment_duedate: null,
      service_description: null,
      qty: null,
      usage_days: null,
      normal_cost: null,
      total_payable: null,
      discount: null,
      email: null,
      phone: null,
      bill_year: null,
      bill_month: null,
      device_rental_amount:null, 
      device_rental_price:null,
      device_rental_qty:0,
      product_id_amount:null,
      product_id_price:null,
      product_id_qty:0,
      foc_amount:null,
      foc_price:null,
      foc_qty:0,
      setup_fees_amount:null,
      setup_fees_price:null,
      setup_fees_qty:0,
      lan_amount:null,
      lan_price:null,
      lan_qty:0,
      device_amount:null,
      device_price:null,
      device_name_qty:0,
      sub_total:null,
      commercial_tax: 5,
      final_payment: null,
    });
    const goSearch = (parm) => {
      let url = "/showbill";
      if (form.id != null) {
        parm.id = form.id;
      }
      parameter.value = parm;
      Inertia.get(url, parm, { preserveState: true });
    };
    const toggleAdv = () => {
      show_search.value = !show_search.value;
    console.log(props.route);
    };

    function view() {
      //form._method = "POST";
      let parm = Object.create( {} ); 
      parm.id = form.id;
       parameter.value = parm;
      Inertia.get("/showbill", form, {
        preserveState: true,
        resetOnSuccess: true,
        onSuccess: (page) => {
        
        },
        onError: (errors) => {
      
          console.log("error ..".errors);
        },
      });
    }
    function getFile($data){
      if($data){
        return "<a href="+$data+">Download</a>";
      }else{
        return "No File";
      }
    }
    function generatePDF(data){

        Inertia.post("/getSinglePDF/"+data, data, {
          preserveState: true,
          onSuccess: (page) => {
            loading.value = false;
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
            closeModal();
            console.log("error ..".errors);
          },
          onStart:(pending) =>{
            console.log("Loading .." + pending);
               loading.value = true;
          },
        });
    }
    function generateAllPDF(){
        Inertia.post("/getAllPDF",parameter.value, {
          preserveState: true,
          onSuccess: (page) => {
            loading.value = false;
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
            closeModal();
            console.log("error ..".errors);
          },
          onStart:(pending) =>{
            console.log("Loading .." + pending);
               loading.value = true;
          },
        });
    }
    function sendEmail(data){

        Inertia.post("/sendSingleEmail/"+data, data, {
          preserveState: true,
          onSuccess: (page) => {
            loading.value = false;
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
            closeModal();
            console.log("error ..".errors);
          },
          onStart:(pending) =>{
            console.log("Loading .." + pending);
              
          },
        });
    }
      function doExcel(){
       axios.post("/exportBillingExcel", 
          parameter.value)
          .then(response => {
            console.log(response)
             var a = document.createElement("a");
              document.body.appendChild(a);
              a.style = "display: none";
            let blob = new Blob([response.data], { type: 'text/csv' }),
              url = window.URL.createObjectURL(blob)
              a.href = url;
              a.download = 'billings.csv';
              a.click();
              window.URL.revokeObjectURL(url);
           });

     
    }
     function openModal() {
      isOpen.value = true;
    }
    function closeModal() {
      isOpen.value = false;
      resetForm();
      editMode.value = false;
    }
    function openReceipt(data){
      form.id = data.id;
      form.customer_id = data.customer_id;
      form.period_covered = data.period_covered;
      form.bill_number = data.bill_number;
      form.ftth_id = data.ftth_id;
      form.date_issued = data.date_issued;
      form.bill_to = data.bill_to;
      form.attn = data.attn;
      form.previous_balance = data.previous_balance;
      form.current_charge = data.current_charge;
      form.compensation = data.compensation;
      form.otc = data.otc;
      form.sub_total = data.sub_total;
      form.payment_duedate = data.payment_duedate;
      form.service_description = data.service_description;
      form.qty = data.qty;
      form.usage_days = data.usage_days;
      form.customer_status = data.customer_status;
      form.normal_cost = data.normal_cost;
      form.total_payable = data.total_payable;
      form.discount = data.discount;
      form.phone = data.phone;
      form.bill_year = data.bill_year;
      form.bill_month = data.bill_month;
      
      openModal();
    }
    function calc(){
      form.device_rental_amount = form.device_rental_price * form.device_rental_qty;
      form.product_id_amount = form.product_id_price * form.product_id_qty;
      form.foc_amount = form.foc_price * form.foc_qty;
      form.setup_fees_amount = form.setup_fees_price * form.setup_fees_qty;
      form.lan_amount = form.lan_price * form.lan_qty;
      form.device_amount = form.device_price * form.device_name_qty;
      form.sub_total = form.device_amount + form.lan_amount + form.setup_fees_amount + form.foc_amount + form.product_id_amount + form.device_rental_amount + form.total_payable;
      form.final_payment = form.sub_total + ((form.sub_total/100)*form.commercial_tax);

    }
    function getMonth(m){
      return  Intl.DateTimeFormat('en', { month: 'long' }).format(new Date(m));
    }
    onMounted(() => {
      props.packages.map(function (x) {
        return (x.item_data = `${x.speed}Mbps - ${x.name} - ${x.contract_period} Months`);
      });
    });
    return { form,isOpen,getMonth, view, show_search, toggleAdv, goSearch,getFile,generatePDF,loading,generateAllPDF,sendEmail,parameter,doExcel,openReceipt,closeModal ,calc};
  },
};
</script>
<style scoped>
.loader {
	border-top-color: #3498db;
	-webkit-animation: spinner 1.5s linear infinite;
	animation: spinner 1.5s linear infinite;
}

@-webkit-keyframes spinner {
	0% {
		-webkit-transform: rotate(0deg);
	}
	100% {
		-webkit-transform: rotate(360deg);
	}
}

@keyframes spinner {
	0% {
		transform: rotate(0deg);
	}
	100% {
		transform: rotate(360deg);
	}
}

</style>