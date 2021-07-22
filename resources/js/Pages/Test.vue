<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Customer Details</h2>
    </template>
    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 w-full">
          <div class="col-span-2 sm:col-span-2">
            <div class="bg-white rounded-lg w-full mx-auto mt-4 shadow-xl divide-y divide-gray-200">
              <!-- Tabs -->
              <ul id="tabs" class="inline-flex w-full bg-gray-50 rounded-t-lg ">
                <li class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" :class="[tab1 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><inertia-link href="#" @click="tabClick(1)" preserve-state>Genaral</inertia-link></li>
                <li class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" :class="[tab2 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><inertia-link href="#" @click="tabClick(2)" preserve-state>Tasks</inertia-link></li>
                <li class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" :class="[tab3 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><inertia-link href="#" @click="tabClick(3)" preserve-state>Files</inertia-link></li>
                <li class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" :class="[tab4 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><inertia-link href="#" @click="tabClick(4)" preserve-state>History</inertia-link></li>
              </ul>

              <!-- Tab Contents -->
              <div id="tab-contents">
                <div id="first" class="p-4" :class="[tab1 ? '' : 'hidden']">First tab</div>
                <div id="second" class="p-4" :class="[tab2 ? '' : 'hidden']">Second tab</div>
                <div id="third" class="p-4" :class="[tab3 ? '' : 'hidden']">Third tab</div>
                <div id="fourth" class="p-4" :class="[tab4 ? '' : 'hidden']">Fourth tab</div>
              </div>
            </div>
          </div>
          <div class="col-span-3 sm:col-span-3">
            <div class="bg-white rounded-lg w-full mx-auto mt-4 shadow-xl">
              <!-- Tabs -->
              <ul id="tabs" class="inline-flex w-full bg-indigo-100 rounded-t-lg">
                <li class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" :class="[tab1 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><inertia-link href="#" @click="tabClick(1)" preserve-state>Genaral</inertia-link></li>
                <li class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" :class="[tab2 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><inertia-link href="#" @click="tabClick(2)" preserve-state>Tasks</inertia-link></li>
                <li class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" :class="[tab3 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><inertia-link href="#" @click="tabClick(3)" preserve-state>Files</inertia-link></li>
                <li class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" :class="[tab4 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><inertia-link href="#" @click="tabClick(4)" preserve-state>History</inertia-link></li>
              </ul>

              <!-- Tab Contents -->
              <div id="tab-contents">
                <div id="first" class="p-4" :class="[tab1 ? '' : 'hidden']">First tab</div>
                <div id="second" class="p-4" :class="[tab2 ? '' : 'hidden']">Second tab</div>
                <div id="third" class="p-4" :class="[tab3 ? '' : 'hidden']">Third tab</div>
                <div id="fourth" class="p-4" :class="[tab4 ? '' : 'hidden']">Fourth tab</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import { reactive, ref } from "vue";
export default {
  name: "Test",
  components: {
    AppLayout,
  },
  setup() {
    let tab1 = ref(true);
    let tab2 = ref(false);
    let tab3 = ref(false);
    let tab4 = ref(false);

    function tabClick(tab) {
      console.log(tab);
      if (tab == 1) {
        tab1.value = true;
        tab2.value = false;
        tab3.value = false;
        tab4.value = false;
      }
      if (tab == 2) {
        tab1.value = false;
        tab2.value = true;
        tab3.value = false;
        tab4.value = false;
      }
      if (tab == 3) {
        tab1.value = false;
        tab2.value = false;
        tab3.value = true;
        tab4.value = false;
      }
      if (tab == 4) {
        tab1.value = false;
        tab2.value = false;
        tab3.value = false;
        tab4.value = true;
      }
    }
    return { tabClick, tab1, tab2, tab3, tab4 };
  },
};
</script>

<style></style>
