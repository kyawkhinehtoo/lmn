<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Packages</h2>
    </template>
    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mt-5 md:mt-0 md:col-span-2">
          <form action="#" method="POST">
            <div class="shadow sm:rounded-md sm:overflow-hidden">
              <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                <div class="grid grid-cols-2 gap-6">
                  <div class="col-span-3 sm:col-span-1">
                    <div class="py-2">
                      <label for="company_website" class="block text-sm font-medium text-gray-700"> Enter Package Name </label>
                      <div class="mt-1 flex rounded-md shadow-sm">
                        <input type="text" name="company_website" id="company_website" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Package Name" />
                      </div>
                    </div>
                    <div class="py-2">
                      <label for="company_website" class="block text-sm font-medium text-gray-700"> Enter Bandwidth </label>
                      <div class="mt-1 flex rounded-md shadow-sm">
                        <input type="text" name="company_website" id="company_website" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Bandwidth in Mbps" />
                      </div>
                    </div>
                    <div class="py-2">
                      <label for="company_website" class="block text-sm font-medium text-gray-700"> Contract Term </label>
                      <div class="mt-1 flex rounded-md shadow-sm">
                        <select id="country" name="country" autocomplete="country" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                          <option>United States</option>
                          <option>Canada</option>
                          <option>Mexico</option>
                        </select>
                      </div>
                    </div>
                    <div class="py-2 grid grid-cols-5 gap-2">
                      <div class="col-span-3 sm:col-span-3">
                        <label for="company_website" class="block text-sm font-medium text-gray-700"> Contract Term </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <select id="country" name="country" autocomplete="country" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option>United States</option>
                            <option>Canada</option>
                            <option>Mexico</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <label for="company_website" class="block text-sm font-medium text-gray-700"> QTY </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <input type="number" name="company_website" id="company_website" class="mt-1 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
                        </div>
                      </div>
                      <div class="col-span-1 sm:col-span-1">
                        <div class="mt-1 flex rounded-md shadow-sm">
                          <button type="button" class="mt-6 justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Add >></button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-span-3 sm:col-span-1">
                    <div class="py-2">
                      <label for="company_website" class="block text-sm font-medium text-gray-700"> Bundle Equiptment </label>
                      <div class="mt-1 flex rounded-md shadow-sm max-h-80 overflow-y-scroll">
                        <ul class="px-0 w-full">
                          <li class="border list-none rounded-sm px-3 py-3 flex" style="border-bottom-width: 0">
                            <div class="text-sm text-gray-700 flex-1">Mesh Router</div>
                            <div class="text-sm text-gray-700 flex-1">2 Qty</div>
                            <div class="text-red-400 justify-end"><i class="fas fa-times-circle mr-2 text-red-700 hover:text-red-600 cursor-pointer text-base"></i></div>
                          </li>
                          <li class="border list-none rounded-sm px-3 py-3 flex" style="border-bottom-width: 0">
                            <div class="text-sm text-gray-700 flex-1">VOIP Number</div>
                            <div class="text-sm text-gray-700 flex-1">2 Qty</div>
                            <div class="text-red-400 justify-end"><i class="fas fa-times-circle mr-2 text-red-700 hover:text-red-600 cursor-pointer text-base"></i></div>
                          </li>
                          <li class="border list-none rounded-sm px-3 py-3 flex" style="border-bottom-width: 0">
                            <div class="text-sm text-gray-700 flex-1">Air Purifier</div>
                            <div class="text-sm text-gray-700 flex-1">2 Qty</div>
                            <div class="text-red-400 justify-end"><i class="fas fa-times-circle mr-2 text-red-700 hover:text-red-600 cursor-pointer text-base"></i></div>
                          </li>
                          <li class="border list-none rounded-sm px-3 py-3 flex" style="border-bottom-width: 0">
                            <div class="text-sm text-gray-700 flex-1">CCTV</div>
                            <div class="text-sm text-gray-700 flex-1">2 Qty</div>
                            <div class="text-red-400 justify-end"><i class="fas fa-times-circle mr-2 text-red-700 hover:text-red-600 cursor-pointer text-base"></i></div>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Save</button>
              </div>
            </div>
          </form>
        </div>
        <!-- end of mt-5 md: -->
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
export default {
  name: "Test",
  components: {
    AppLayout,
  },
  setup() {},
};
</script>

<style></style>
