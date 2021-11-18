<template>
  <app-layout
    ><template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Temporary Billing View</h2>
    </template>

    <div class="py-6">
      <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <div ref="isOpen" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
          <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity">
              <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-7xl sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
              <form @submit.prevent="submit">
        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
          <table class="divide-y divide-gray-200 w-full">
            <tbody class="bg-white divide-y divide-gray-200">
              <tr>
                <td class="px-6 py-4 whitespace-nowrap" colspan="5">Client Name : Kyaw Khine Htoo</td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="2" rowspan="2">Package : </td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap" colspan="5">Client ID</td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap" colspan="5">Address : No.1 ABC</td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="2" rowspan="2">Internet Speed :</td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap" colspan="5">Contact No.</td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">No.</td>
                <td class="px-6 py-4 whitespace-nowrap">Description</td>
                <td class="px-6 py-4 whitespace-nowrap">Year</td>
                <td class="px-6 py-4 whitespace-nowrap">Month</td>
                <td class="px-6 py-4 whitespace-nowrap">Day</td>
                <td class="px-6 py-4 whitespace-nowrap">Price (USD/Kyat)</td>
                <td class="px-6 py-4 whitespace-nowrap">Amount (USD/Kyat)</td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">1</td>
                <td class="px-6 py-4 whitespace-nowrap">Internet Bill</td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">2</td>
                <td class="px-6 py-4 whitespace-nowrap">Device Name</td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="3"></td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
              </tr>
               <tr>
                <td class="px-6 py-4 whitespace-nowrap">3</td>
                <td class="px-6 py-4 whitespace-nowrap">LAN</td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="3"></td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
              </tr>
               <tr>
                <td class="px-6 py-4 whitespace-nowrap">4</td>
                <td class="px-6 py-4 whitespace-nowrap">Setup Fees</td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="3"></td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
              </tr>
               <tr>
                <td class="px-6 py-4 whitespace-nowrap">5</td>
                <td class="px-6 py-4 whitespace-nowrap">FOC</td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="3"></td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
              </tr>
               <tr>
                <td class="px-6 py-4 whitespace-nowrap">6</td>
                <td class="px-6 py-4 whitespace-nowrap">Product ID</td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="3"></td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
              </tr>
               <tr>
                <td class="px-6 py-4 whitespace-nowrap">7</td>
                <td class="px-6 py-4 whitespace-nowrap">Device Rental</td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="3"></td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
              </tr>
               <tr>
                <td class="px-6 py-4 whitespace-nowrap" colspan="6">Sub Total (W/O commercial tax)</td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
              </tr>
               <tr>
                <td class="px-6 py-4 whitespace-nowrap" colspan="6">Total commercial tax</td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
              </tr>
               <tr>
                <td class="px-6 py-4 whitespace-nowrap" colspan="6">Final Payment</td>
                <td class="px-6 py-4 whitespace-nowrap"></td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">Cover Period</td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="6">From .....D/.....M/.....Y    To .....D/.....M/.....Y </td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap"><strong>Remark</strong></td>
                <td class="px-6 py-4 whitespace-nowrap" colspan="6"></td>
              </tr>
            </tbody>
          </table>
        </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import { ref, reactive, emits } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout";

export default {
  components: {
    AppLayout,
  },
  name: "Test",
  setup(props) {
    const form = useForm({
      name: null,
      file: null,
      incident_id: props.data,
    });
    let displayingToken = ref(false);
    let apiTokenBeingDeleted = ref(null);
    function deleteApiToken(e) {
      console.log(e);
    }
    return { form };
  },
};
</script>
