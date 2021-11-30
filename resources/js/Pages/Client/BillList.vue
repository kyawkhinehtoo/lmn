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
        <button type="button" @click="sendAllEmail" class="h-10 text-md px-4 bg-yellow-600 rounded text-white hover:bg-yellow-700">Send SMS to All</button>
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

                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deliver SMS</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SMS Sent Date</th>
                <!-- <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th> -->
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt Status</th>
                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><i class="fa fa-print"></i> Print </th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only" v-if="invoiceEdit">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(row, index) in billings.data" v-bind:key="row.id">
                <td class="pl-4 px-2 py-3 text-xs whitespace-nowrap">{{ (index += billings.from) }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.bill_number }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.ftth_id }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.service_description }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.qty }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.usage_days }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.total_payable }}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">
                  <span v-if="row.total_payable > 0">
                    <span v-if="row.status">{{ row.status }}</span
                    ><span v-else><button type="button" @click="sendEmail(row.id)" class="h-8 text-md w-20 bg-yellow-600 rounded text-white hover:bg-yellow-700">Send</button></span>
                  </span>
                </td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">{{ row.sent_date ? row.sent_date : "None" }}</td>
                <!-- <td class="px-2 py-3 text-xs whitespace-nowrap"><a :href="`/pdfpreview2/${row.id}`" target="_blank"><i class="fa fas fa-eye text-gray-400"></i></a></td> -->
                
                <td class="px-2 py-3 text-xs whitespace-nowrap">
                  <button type="button" @click="openReceipt(row)" class="h-8 text-md w-24 bg-green-600 rounded text-white hover:bg-green-700" v-if="row.receipt_status">View Receipt</button>
                  <button type="button" @click="openReceipt(row)" class="h-8 text-md w-24 bg-green-400 rounded text-white hover:bg-green-500" v-else>Make Receipt</button>
                </td>
                <td class="px-2 py-3 text-xs whitespace-nowrap capitalize">{{ (row.receipt_status)?row.receipt_status.replace('_', ' '):''}}</td>
                <td class="px-2 py-3 text-xs whitespace-nowrap">
                   <span v-if="row.receipt_status">
                      <a :href="`/pdfpreview2/${row.id}`" target="_blank"><i class="fa fas fa-eye text-gray-400"></i></a>
                  </span>
                </td>
                <td class="px-6 py-3 text-xs whitespace-nowrap text-right font-medium" v-if="invoiceEdit">
                  <a href="#" @click="edit_invoice(row)" class="text-yellow-600 hover:text-yellow-900"><i class="fas fa-pen"></i></a> 
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <span v-if="billings.total" class="w-full block mt-4">
          <label class="text-xs text-gray-600">{{ billings.data.length }} Invoices in Current Page. Total Number of Invoices : {{ billings.total }}</label>
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
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-7xl sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex justify-between w-full">
            <h1 class="flex text-indigo-700 text-md uppercase font-bold block pt-1 no-underline">Receipt Form</h1>
            <i class="flex fa fa-2x fas fa-times-circle text-red-500 hover:text-red-800 cursor-pointer" @click="closeModal"></i>
          </div>
          <form @submit.prevent="submit">
            <div class="shadow overflow-hidden border-b border-gray-200 p-4">
               <p v-show="$page.props.errors.receipt_date" class="mt-2 text-sm text-red-500 block">{{ $page.props.errors.receipt_date }}</p> 
               <p v-show="$page.props.errors.collected_amount" class="mt-2 text-sm text-red-500 block">{{ $page.props.errors.collected_amount }}</p> 
              <div class="grid grid-cols-1 md:grid-cols-4 w-full">
                  
                <div class="col-span-2 sm:col-span-2 border-2 border-marga bg-marga">
                  <h1 class="text-gray-600 text-lg font-semibold mt-1 px-2">CASH RECEIPT</h1>
                </div>
                <div class="col-span-2 sm:col-span-2 border-b-2 border-marga justify-end flex">
                  
                  <span class="inline-flex text-sm p-2">Payment Date: </span><input type="date" class="py-2 focus:ring-indigo-500 focus:border-indigo-500 inline-flex sm:text-sm border-2 border-gray-300" v-model="form.receipt_date" />
                
                </div>
                
               
              </div>
              <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full py-4">
                <div class="col-span-3 sm:col-span-3 border-2 border-marga p-4">
                  <p>Payer Name : {{ form.bill_to }}</p>
                  <p>Payer Address : {{ form.attn }}</p>
                  <p>Payment Description : {{ form.service_description }}</p>
                  <p>Period Covered : {{ form.period_covered }}</p>
                </div>
                <div class="col-span-1 sm:col-span-1 flex flex-col justify-between">
                  <div class="border-2 border-marga p-2 text-center flex flex-col">
                    <span class="font-semibold text-md">Reference :</span> <span class="text-sm"> {{ receipt_number }}</span>
                  </div>
                  <div class="border-2 border-marga p-2 text-center flex flex-col mt-2">
                    <span class="font-semibold text-md">Bill Number:</span> <span class="text-sm"> {{ form.bill_number }}</span>
                  </div>
                  <div class="border-2 border-marga p-2 text-center flex flex-col mt-2">
                    <span class="font-semibold text-md">Customer ID:</span> <span class="text-sm"> {{ form.ftth_id }}</span>
                  </div>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full">
                <div class="py-4 col-span-1 sm:col-span-1 border-2 border-marga text-center flex flex-col">
                  <span class="font-semibold text-md">Amount (MMK):</span> <span class="text-sm"> {{ form.total_payable }}</span>
                </div>
                <div class="py-4 col-span-3 sm:col-span-3 border-2 border-marga text-center flex flex-col">
                  <span class="font-semibold text-md">Amount In Word:</span> <span class="text-sm"> {{ form.amount_in_word }}</span>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-6 w-full py-2 gap-6">
                <div class="col-span-1 sm:col-span-1">
                  <label class="block mt-2">Received Amount</label>
                </div>
                <div class="col-span-1 sm:col-span-1 border-b-2 border-marga"><input type="text" class="py-2 px-0 inline-flex sm:text-sm border-0 focus:ring-0 w-full" v-model="form.collected_amount" @change="calc" /></div>

                <div class="col-span-1 sm:col-span-1">
                  <label class="block mt-2">Payment Channel</label>
                </div>
                <div class="col-span-3 sm:col-span-3 border-b-2 border-marga">
                  <div class="flex">
                    <label class="flex-auto items-center mt-1"> <input type="radio" class="form-radio h-5 w-5 text-indigo-600" checked name="type" v-model="form.type" value="cb" /><span class="ml-2 text-gray-700">CB</span> </label>
                    <label class="flex-auto items-center mt-1"> <input type="radio" class="form-radio h-5 w-5 text-blue-600" name="type" v-model="form.type" value="kbz_pay" /><span class="ml-2 text-gray-700">KBZ Pay</span> </label>
                    <label class="flex-auto items-center mt-1"> <input type="radio" class="form-radio h-5 w-5 text-red-500" name="type" v-model="form.type" value="kbz_account" /><span class="ml-2 text-gray-700">KBZ Account</span> </label>
                    <label class="flex-auto items-center mt-1"> <input type="radio" class="form-radio h-5 w-5 text-green-600" name="type" v-model="form.type" value="nearme" /><span class="ml-2 text-gray-700">Near me</span> </label>
                    <label class="flex-auto items-center mt-1"> <input type="radio" class="form-radio h-5 w-5 text-yellow-600" name="type" v-model="form.type" value="cash" /><span class="ml-2 text-gray-700">Cash</span> </label>
                    <label class="flex-auto items-center mt-1"> <input type="radio" class="form-radio h-5 w-5 text-red-700" name="type" v-model="form.type" value="offset" /><span class="ml-2 text-gray-700">Offset</span> </label>
                  </div>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-6 w-full py-2 gap-6">
                <div class="col-span-1 sm:col-span-1">
                  <label class="block mt-2" v-if="outstanding">Outstanding Amount:</label>
                  <label class="block mt-2" v-else>Suplus Amount:</label>
                </div>
                <div class="col-span-1 sm:col-span-1 border-b-2 border-marga"><input type="text" class="py-2 px-0 inline-flex sm:text-sm border-0 focus:ring-0 w-full" v-model="form.extra_amount" /></div>
                <div class="col-span-1 sm:col-span-1">
                  <label class="block mt-2">Received By:</label>
                </div>
                <div class="col-span-3 sm:col-span-3 border-b-2 border-marga">
                   <multiselect :class="border-0" deselect-label="Selected already" :options="users" track-by="id" label="name" v-model="form.user" :allow-empty="false" ></multiselect>
                
                </div>
              </div>
                <div class="grid grid-cols-1 md:grid-cols-6 w-full py-2 gap-6">
                   <div class="col-span-1 sm:col-span-1">
                  <label class="block mt-2">Currency:</label>
                </div>
                <div class="col-span-1 sm:col-span-1">
                    <div class="flex">
                      <label class="flex-auto items-center mt-1"> <input type="radio" class="form-radio h-5 w-5 text-red-600" name="currency" v-model="form.currency" value="mmk" /><span class="ml-2 text-gray-700">MMK</span> </label>
                    <label class="flex-auto items-center mt-1"> <input type="radio" class="form-radio h-5 w-5 text-green-600" name="currency" v-model="form.currency" value="baht" /><span class="ml-2 text-gray-700">Thai baht</span> </label>
                    </div>
                </div>
                
                 <div class="col-span-1 sm:col-span-1">
                  <label class="block mt-2">Remark:</label>
                </div>
                <div class="col-span-3 sm:col-span-3 border-b-2 border-marga"><textarea class="py-2 px-0 inline-flex sm:text-sm border-0 focus:ring-0 w-full" v-model="form.remark"></textarea></div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                <button @click="saveReceipt" type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition" v-if="!form.receipt_status ">GO !</button>
              </span>
              <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                <button @click="closeModal" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">Cancel</button>
              </span>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div ref="editInvoice" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="editInvoice">
          <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity">
              <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-7xl sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
           
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 shadow sm:rounded-lg">
                  <h6 class="md:min-w-full text-indigo-700 text-sm uppercase font-bold block pt-1 no-underline">Billing Detail Information</h6>
                  <div class="hidden sm:block" aria-hidden="true">
                    <div class="py-5">
                      <div class="border-t border-gray-200"></div>
                    </div>
                  </div>
                  <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="mb-4 md:col-span-1">
                      <label for="bill_number" class="block text-gray-700 text-sm font-bold mb-2">Bill Number :</label>
                      <input type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="bill_number" placeholder="Enter Bill Number" v-model="form_2.bill_number" />
                      <div v-if="$page.props.errors.bill_number" class="text-red-500">{{ $page.props.errors.bill_number[0] }}</div>
                    </div>
                    <div class="mb-4 md:col-span-1">
                      <label for="period_covered" class="block text-gray-700 text-sm font-bold mb-2">Period Covered :</label>
                    
                      <litepie-datepicker placeholder="Enter Period Covered" :formatter="formatter" separator=" to " v-model="form_2.period_covered" ></litepie-datepicker>
          
                      <div v-if="$page.props.errors.period_covered" class="text-red-500">{{ $page.props.errors.period_covered[0] }}</div>
                    </div>
                    <div class="mb-4 md:col-span-1">
                      <label for="ftth_id" class="block text-gray-700 text-sm font-bold mb-2">Customer ID :</label>
                      <input type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="ftth_id" placeholder="Enter Customer ID" v-model="form_2.ftth_id" disabled />
                      <div v-if="$page.props.errors.ftth_id" class="text-red-500">{{ $page.props.errors.ftth_id[0] }}</div>
                    </div>
                  </div>
                  <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="mb-4 md:col-span-1">
                      <label for="date_issued" class="block text-gray-700 text-sm font-bold mb-2">Bill Issue Date :</label>
                      <input type="date" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="date_issued" placeholder="Enter Issue Date" v-model="form_2.date_issued" />
                      <div v-if="$page.props.errors.date_issued" class="text-red-500">{{ $page.props.errors.date_issued[0] }}</div>

                      <label for="payment_duedate" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Payment Due Date :</label>
                      <input type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="payment_duedate" placeholder="Enter Payment Due Date" v-model="form_2.payment_duedate" />
                      <div v-if="$page.props.errors.payment_duedate" class="text-red-500">{{ $page.props.errors.payment_duedate[0] }}</div>
                    </div>

                    <div class="mb-4 md:col-span-2">
                      <label for="bill_to" class="block text-gray-700 text-sm font-bold mb-2">Bill To :</label>
                      <input type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="bill_to" placeholder="Enter Bill To" v-model="form_2.bill_to" />
                      <div v-if="$page.props.errors.bill_to" class="text-red-500">{{ $page.props.errors.bill_to[0] }}</div>
                      <label for="attn" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Attention :</label>
                      <textarea class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="attn" placeholder="Enter Attention" v-model="form_2.attn"></textarea>
                      <div v-if="$page.props.errors.attn" class="text-red-500">{{ $page.props.errors.attn[0] }}</div>
                    </div>
                  </div>
                  <div class="hidden sm:block" aria-hidden="true">
                    <div class="py-5">
                      <div class="border-t border-gray-200"></div>
                    </div>
                  </div>
                  <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="mb-4 md:col-span-1">
                      <label for="service_description" class="block text-gray-700 text-sm font-bold mb-2">Service Description :</label>
                      <input type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="service_description" placeholder="Enter Service Description" v-model="form_2.service_description" />
                      <div v-if="$page.props.errors.service_description" class="text-red-500">{{ $page.props.errors.service_description[0] }}</div>

                      <label for="qty" class="mt-4 block text-gray-700 text-sm font-bold mb-2">QTY :</label>
                      <input type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="qty" placeholder="Enter QTY" v-model="form_2.qty" />
                      <div v-if="$page.props.errors.qty" class="text-red-500">{{ $page.props.errors.qty[0] }}</div>

                      <label for="normal_cost" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Original Package Price :</label>
                      <input type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="normal_cost" v-model="form_2.normal_cost" disabled />
                      <div v-if="$page.props.errors.normal_cost" class="text-red-500">{{ $page.props.errors.normal_cost[0] }}</div>

                      <label for="type" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Type :</label>
                      <input type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="type" placeholder="Enter Type" v-model="form_2.type" />
                      <div v-if="$page.props.errors.type" class="text-red-500">{{ $page.props.errors.type[0] }}</div>

                      <label for="usage_days" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Actual Usage (Days/Month) :</label>
                      <input type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="usage_days" v-model="form_2.usage_days" disabled />
                      <div v-if="$page.props.errors.usage_days" class="text-red-500">{{ $page.props.errors.usage_days[0] }}</div>

                      <label for="phone" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Phone :</label>
                      <input type="text" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="phone" placeholder="Enter Phone Number" v-model="form_2.phone" />
                      <div v-if="$page.props.errors.phone" class="text-red-500">{{ $page.props.errors.phone[0] }}</div>
                      <fieldset class="mt-4 border border-solid border-gray-300 p-3 rounded-md">
                          <legend class="text-gray-700 text-sm font-bold">Meta Data </legend>
                     
                      <div class="max-w-sm text-sm flex">
                       
                          <label class="inline-flex ml-2">
                          <input class="text-green-500 w-6 h-6 mr-2 focus:ring-green-400 focus:ring-opacity-25 border border-gray-300 rounded" type="checkbox" v-model="form_2.reset_email" />
                          Reset SMS
                        </label>
                        <div v-if="form_2.receipt_id">
                          <label class="inline-flex ml-2">
                          <input class="text-red-500 w-6 h-6 mr-2 focus:ring-red-400 focus:ring-opacity-25 border border-gray-300 rounded" type="checkbox" v-model="form_2.reset_receipt" />
                          Reset Receipt
                        </label>
                        </div>
                      </div>
                  </fieldset>
                    </div>

                    <div class="mb-4 md:col-span-2">
                      <label for="previous_balance" class="block text-gray-700 text-sm font-bold mb-2">Previous Balance :</label>
                      <input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="previous_balance" placeholder="Enter Previous Balance" v-model="form_2.previous_balance" @change="form2_calc" />
                      <div v-if="$page.props.errors.previous_balance" class="text-red-500">{{ $page.props.errors.previous_balance[0] }}</div>

                      <label for="current_charge" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Current Charge :</label>
                      <input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="current_charge" placeholder="Enter Current Charge" v-model="form_2.current_charge" @change="form2_calc" />
                      <div v-if="$page.props.errors.current_charge" class="text-red-500">{{ $page.props.errors.current_charge[0] }}</div>

                      <label for="sub_total" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Sub Total :</label>
                      <input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="total_mmk" placeholder="Enter Sub Total" v-model="form_2.sub_total" @change="form2_calc" />
                      <div v-if="$page.props.errors.sub_total" class="text-red-500">{{ $page.props.errors.sub_total[0] }}</div>

                      <label for="otc" class="mt-4 block text-gray-700 text-sm font-bold mb-2">OTC :</label>
                      <input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="otc" placeholder="Enter OTC" v-model="form_2.otc" @change="form2_calc" />
                      <div v-if="$page.props.errors.otc" class="text-red-500">{{ $page.props.errors.otc[0] }}</div>

                      <label for="compensation" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Compensation :</label>
                      <input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="compensation" placeholder="Enter Compensation" v-model="form_2.compensation" @change="form2_calc" />
                      <div v-if="$page.props.errors.compensation" class="text-red-500">{{ $page.props.errors.compensation[0] }}</div>

                      <label for="discount" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Discount :</label>
                      <input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="discount" placeholder="Enter Discount" v-model="form_2.discount" @change="form2_calc" />
                      <div v-if="$page.props.errors.discount" class="text-red-500">{{ $page.props.errors.discount[0] }}</div>

                      <label for="tax" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Commercial Tax (5%) : <button  type="button" class="inline-flex justify-center rounded-md border border-gray-300 px-4 py-2 bg-blue-500 text-base leading-6 font-medium text-white shadow-sm hover:text-white focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5" @click="calTax">Calculate </button> </label>
                      <input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="tax" v-model="form_2.tax" @change="form2_calc" />
                      <div v-if="$page.props.errors.tax" class="text-red-500">{{ $page.props.errors.tax[0] }}</div>

                      <label for="total_payable" class="mt-4 block text-gray-700 text-sm font-bold mb-2">Total Payable :</label>
                      <input type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" id="total_payable" placeholder="Enter Total Payable" v-model="form_2.total_payable" />
                      <div v-if="$page.props.errors.total_payable" class="text-red-500">{{ $page.props.errors.total_payable[0] }}</div>

                    </div>
                  </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                  <!-- <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                    <button wire:click.prevent="submit" type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition" v-show="!editMode">Save</button>
                  </span> -->
                  <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                    <button @click="updateInvoice" type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Update</button>
                  </span>
                  <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                    <button @click="closeEdit" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">Close</button>
                  </span>
                </div>
           
            </div>
          </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { useForm } from "@inertiajs/inertia-vue3";
import { reactive, ref, provide, onMounted } from "vue";
import { Inertia } from "@inertiajs/inertia";
import Multiselect from "@suadelabs/vue3-multiselect";
import Pagination from "@/Components/Pagination";
import BillingSearch from "@/Components/BillingSearch";
import LitepieDatepicker from "litepie-datepicker";
export default {
  name: "BillList",
  components: {
    AppLayout,
    Pagination,
    BillingSearch,
    Multiselect,
    LitepieDatepicker,
  },
  props: {
    lists: Object,
    billings: Object,
    packages: Object,
    projects: Object,
    townships: Object,
    status: Object,
    errors: Object,
    user:Object,
    users:Object,
    roles:Object,
    max_receipt:Object,
  },
  setup(props) {
    provide("packages", props.packages);
    provide("projects", props.projects);
    provide("townships", props.townships);
    provide("status", props.status);
    const formatter = ref({
      date: "YYYY-MM-DD",
      month: "MMM",
    });
    let show_search = ref(false);
    let loading = ref(false);
    let parameter = ref("");
    let isOpen = ref(false);
    let editMode = ref(false);
    let editInvoice = ref(false);
    let outstanding = ref(false);
    let invoiceEdit = ref(false);
    let receipt_number = ref(0);
    const form = reactive({
      id: null,
      bill_id : null,
      customer_id: null,
      period_covered: null,
      bill_number: null,
      ftth_id: null,
      date_issued: null,
      receipt_date: null,
      bill_to: null,
      attn: null,
      service_description: null,
      total_payable: null,
      bill_year: null,
      bill_month: null,
      amount_in_word: null,
      user:null,
      type:"cash",
      currency:"baht",
      collected_amount:0,
      extra_amount:0,
      remark:null,
      receipt_status:null,
    });

    
    function resetForm(){
      form.id = null;
      form.bill_id  = null;
      form.customer_id = null;
      form.period_covered = null;
      form.bill_number = null;
      form.ftth_id = null;
      form.date_issued = null;
      form.receipt_date = null;
      form.bill_to = null;
      form.attn = null;
      form.service_description = null;
      form.total_payable = null;
      form.bill_year = null;
      form.bill_month = null;
      form.amount_in_word = null;
      form.user = null;
      form.type = "cash";
      form.currency= "mmk";
      form.collected_amount= 0;
      form.extra_amount= 0;
      form.remark= null;
      form.receipt_status= null;
      receipt_number.value = 0;
    }

      const form_2 = useForm({
      id: null,
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
      type: null,
      total_payable: null,
      discount: null,
      tax:null,
      email: null,
      phone: null,
      reset_pdf: null,
      reset_email: null,
      reset_receipt: null,
      receipt_id: null,
    });
    function edit_invoice(data) {
      console.log(data);
      form_2.id = data.id;
      form_2.customer_id = data.customer_id;
      form_2.period_covered = data.period_covered;
      form_2.bill_number = data.bill_number;
      form_2.ftth_id = data.ftth_id;
      form_2.date_issued = data.date_issued;
      form_2.bill_to = data.bill_to;
      form_2.attn = data.attn;
      form_2.previous_balance = data.previous_balance;
      form_2.current_charge = data.current_charge;
      form_2.compensation = data.compensation;
      form_2.otc = data.otc;
      form_2.sub_total = data.sub_total;
      form_2.payment_duedate = data.payment_duedate;
      form_2.service_description = data.service_description;
      form_2.qty = data.qty;
      form_2.usage_days = data.usage_days;
      form_2.tax = data.tax;
      form_2.normal_cost = data.normal_cost;
      form_2.type = data.type;
      form_2.total_payable = data.total_payable;
      form_2.discount = data.discount;
      form_2.email = data.email;
      form_2.phone = data.phone;
      form_2.reset_pdf = data.reset_pdf;
      form_2.reset_email = data.reset_email;
      form_2.reset_receipt = data.reset_receipt;
      form_2.receipt_id = data.receipt_id;
      editMode.value = true;
      openEdit();
    }
    function resetEdit() {
        form_2.reset();
    }
    function openEdit() {
        editInvoice.value = true;
      }
    function closeEdit() {
        editInvoice.value = false;
        resetEdit();
        editMode.value = false;
    }
      function updateInvoice() {
      form_2.post("/updateInvoice", {
        onSuccess: (page) => {
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
        },
        onError: (errors) => {
          console.log(errors);
        },
      });
    }
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
      let parm = Object.create({});
      parm.id = form.id;
      parameter.value = parm;
      form._method = "GET";
      Inertia.get("/showbill", form, {
        preserveState: true,
        resetOnSuccess: true,
        onSuccess: (page) => {},
        onError: (errors) => {
          console.log("error ..".errors);
        },
      });
    }
    function getFile($data) {
      if ($data) {
        return "<a href=" + $data + ">Download</a>";
      } else {
        return "No File";
      }
    }
    function generateReceiptPDF(data){
       Inertia.post("/getReceiptPDF/" + data, data, {
        preserveState: true,
        onSuccess: (page) => {
          loading.value = false;
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
     
        },
        onError: (errors) => {
  
          console.log("error ..".errors);
        },
        onStart: (pending) => {
          console.log("Loading .." + pending);
          loading.value = true;
        },
      });
    }
    function generatePDF(data) {
      Inertia.post("/getSinglePDF/" + data, data, {
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
        onStart: (pending) => {
          console.log("Loading .." + pending);
          loading.value = true;
        },
      });
    }
    function generateAllPDF() {
      Inertia.post("/getAllPDF", parameter.value, {
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
        onStart: (pending) => {
          console.log("Loading .." + pending);
          loading.value = true;
        },
      });
    }
    function sendEmail(data) {
      Inertia.post("/sendSingleEmail/" + data, data, {
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
        onStart: (pending) => {
          console.log("Loading .." + pending);
        },
      });
    }
    function sendAllEmail(){
      let data = parameter.value;
        Inertia.post("/sendAllEmail",data, {
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
    function doExcel() {
      axios.post("/exportBillingExcel", parameter.value).then((response) => {
        console.log(response);
        var a = document.createElement("a");
        document.body.appendChild(a);
        a.style = "display: none";
        let blob = new Blob([response.data], { type: "text/csv" }),
          url = window.URL.createObjectURL(blob);
        a.href = url;
        a.download = "billings.csv";
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
    function openReceipt(data) {
      form.id = data.id;
      form.bill_id = data.bill_id;
      form.customer_id = data.customer_id;
      form.period_covered = data.period_covered;
      form.bill_number = data.bill_number;
      form.ftth_id = data.ftth_id;
      form.date_issued = data.date_issued;
      form.bill_to = data.bill_to;
      form.attn = data.attn;
      form.service_description = data.service_description;
      form.total_payable = data.total_payable;
      form.bill_year = data.bill_year;
      form.bill_month = data.bill_month;
      form.amount_in_word = data.amount_in_word;
      form.receipt_date = data.receipt_date;
      form.user = (data.collected_person)? props.users.filter((d) => d.id == data.collected_person)[0]:null;
      form.collected_amount = data.collected_amount;
      form.type = (data.payment_channel)?data.payment_channel:'cash';
      form.receipt_status = data.receipt_status;
      if(data.receipt_number)
      receipt_number.value = 'R'+data.bill_number.substring(0, 4)+'-'+data.ftth_id+'-'+('00000'+data.receipt_number).slice(-5);
      else
      receipt_number.value = 'R'+data.bill_number.substring(0, 4)+'-'+data.ftth_id+'-'+('00000'+(props.max_receipt.max_receipt_number+1)).slice(-5);
      calc();
      openModal();
    }
    function saveReceipt(){
      form._method = "POST";
       Inertia.post("/saveReceipt", form, {
        preserveState: true,
        onSuccess: (page) => {
          loading.value = false;
          Toast.fire({
            icon: "success",
            title: page.props.flash.message,
          });
          closeModal();
        },
        onError: (errors) => {
        
           loading.value = false;
          console.log("error ..".errors);
        },
        onStart: (pending) => {
          console.log("Loading .." + pending);
          loading.value = true;
        },
      });
    }
    function calc() {
      if(parseInt(form.collected_amount) < parseInt(form.total_payable)){
        outstanding.value = true;
        form.extra_amount = parseInt(form.total_payable) - parseInt(form.collected_amount);
      }else{
        outstanding.value = false;
        form.extra_amount = parseInt(form.collected_amount) - parseInt(form.total_payable);
      }
     
    }
    function calTax(){
      form_2.sub_total = parseInt(form_2.previous_balance) + parseInt(form_2.current_charge) + parseInt(form_2.otc) - parseInt(form_2.compensation);
      form_2.tax =  Math.round((parseInt(form_2.sub_total)/100) * 5);
      form2_calc();
    }
    function form2_calc() {
      form_2.sub_total = parseInt(form_2.previous_balance) + parseInt(form_2.current_charge) + parseInt(form_2.otc) - parseInt(form_2.compensation);
  
      form_2.total_payable = parseInt(form_2.sub_total) - parseInt(form_2.discount);
      if(form_2.tax){
        form_2.total_payable = parseInt(form_2.total_payable) + parseInt(form_2.tax);
      }
    }
    function checkEdit(){
      const my_role = props.roles.filter((d)=> d.id == props.user.role)[0];
          if(my_role.edit_invoice){
            return true;
          }
    }
    function getMonth(m) {
      return Intl.DateTimeFormat("en", { month: "long" }).format(new Date(m));
    }
    onMounted(() => {
      props.packages.map(function (x) {
        return (x.item_data = `${x.speed}Mbps - ${x.name} - ${x.contract_period} Months`);
      });
      invoiceEdit.value = checkEdit();
    });
    return { form, form_2, view, show_search, toggleAdv, goSearch, getFile, generatePDF, loading, generateAllPDF, sendEmail, parameter,sendAllEmail, doExcel, openReceipt, closeModal, getMonth, calc, form2_calc,calTax,isOpen,outstanding,saveReceipt,updateInvoice,generateReceiptPDF ,receipt_number ,editInvoice,edit_invoice, openEdit,closeEdit ,invoiceEdit,formatter};
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
.bg-marga {
  background: #fed406;
  color: #ffffff;
}
.border-marga {
  border-color: #255978;
}
</style>