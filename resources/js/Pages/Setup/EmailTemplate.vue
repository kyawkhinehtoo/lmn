<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">SMS - Email Template Setup</h2>
    </template>

    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between space-x-2 items-end mb-2 px-1 md:px-0">
          <div class="relative flex flex-wrap">
            <span class="z-10 h-full leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3"><i class="fas fa-search"></i></span>
            <input type="text" placeholder="Search here..." class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10" id="search" v-model="search" v-on:keyup.enter="searchTsp" />
          </div>

          <button @click="openModal" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">Create</button>
        </div>
        <div class="bg-white overflow-auto shadow-xl sm:rounded-lg" v-if="templates">
          <!-- <button @click="openModal" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-3">Create New package</button>
                 <input class="w-half rounded py-2 my-3 float-right" type="text" placeholder="Search package" v-model="search" v-on:keyup.enter="searchTsp">
                    -->

          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Default</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Action</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="row in templates" v-bind:key="row.id" :class="{ 'text-gray-400': !row.status }">
                <td class="px-6 py-3 text-left text-sm font-medium whitespace-nowrap">{{ row.id }}</td>
                <td class="px-6 py-3 text-left text-sm font-medium whitespace-nowrap">{{ row.name }}</td>
                <td class="px-6 py-3 text-left text-sm font-medium whitespace-nowrap uppercase">{{ row.type }}</td>
                <td class="px-6 py-3 text-left text-sm font-medium whitespace-nowrap"><i class="fa fas fa-star" :class="row.default==1? 'text-yellow-600':'text-gray-200'"></i></td>
                <td class="px-6 py-3 text-left text-sm font-medium whitespace-nowrap text-right">
                  <a href="#" @click="edit(row)" class="text-indigo-600 hover:text-indigo-900">Edit</a> |
                  <a href="#" @click="deleteRow(row)" class="text-red-600 hover:text-red-900">Delete</a>
                </td>
              </tr>
            </tbody>
          </table>

          <div ref="isOpen" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
              <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
              </div>
              ​
              <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <form @submit.prevent="submit">
                  <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-2 sm:p-6">
                      <div class="py-2">
                        <label for="name" class="block text-sm font-medium text-gray-700"> Enter Template Name </label>
                        <div class="mt-1 rounded-md shadow-sm">
                          <input type="text" v-model="form.name" name="name" id="name" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Template Name" required />
                        </div>
                      </div>
                       
                      <div class="py-2">
                      <label for="name" class="block text-sm font-medium text-gray-700"> Template Type</label>
                       <div class="mt-1">
                      <label class="flex-auto items-center text-sm font-medium text-gray-700 " >SMS</label>
                          <Switch v-model="form.type" :class="form.type ? 'bg-green-700' : 'bg-indigo-700'" class="relative inline-flex items-center h-6 rounded-full w-11 mx-2">
                            <span class="sr-only">Template Type </span>
                            <span :class="form.type ? 'translate-x-6' : 'translate-x-1'" class="inline-block w-4 h-4 transform bg-white rounded-full" />
                          </Switch>
                        <label class="flex-auto items-center text-sm font-medium text-gray-700 ">Email</label>
                        </div>
                      </div>
                        <div class="py-2" v-if="form.type">
                        <label for="name" class="block text-sm font-medium text-gray-700"> Emails Title </label>
                        <div class="mt-1 rounded-md shadow-sm">
                          <input type="text" v-model="form.title" name="title" id="title" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Enter Emails Title"/>
                        </div>
                      </div>
                         <div class="py-2" v-if="form.type">
                        <label for="name" class="block text-sm font-medium text-gray-700"> Emails for CC </label>
                        <div class="mt-1 rounded-md shadow-sm">
                          <input type="text" v-model="form.cc_email" name="cc_email" id="cc_email" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Enter Emails in Comma"/>
                        </div>
                      </div>
                      <div class="py-2">
                        <label for="type" class="block text-sm font-medium text-gray-700"><input type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded mr-2 " name="status" v-model="form.default" value="true" /> Default  </label>
                        
                      </div>
                      <div class="py-2" v-if="form.type">
                        <label for="type" class="block text-sm font-medium text-gray-700"> Body </label>
                        <div class="mt-1">
                          <QuillEditor theme="snow" v-model:content="form.body" contentType="html" toolbar="full" />
                        </div>
                      </div>
                       <div class="py-2" v-else>
                        <label for="type" class="block text-sm font-medium text-gray-700"> Body </label>
                        <div class="mt-1">
                          <textarea v-model="form.body" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-md sm:text-sm border-gray-300"></textarea>
                        </div>
                      </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                      <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                          <button wire:click.prevent="submit" type="submit" class="inline-flex justify-center w-full px-4 py-2 bg-gray-800 border border-gray-300 rounded-md font-medium text-base leading-6 sm:text-sm text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150" v-show="!editMode">Save</button>
                        </span>
                        <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                          <button wire:click.prevent="submit" type="submit" class="inline-flex justify-center w-full px-4 py-2 bg-gray-800 border border-gray-300 rounded-md font-medium text-base leading-6 sm:text-sm text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150" v-show="editMode">Update</button>
                        </span>
                        <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                          <button @click="closeModal" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">Cancel</button>
                        </span>
                      </div>
                    </div>
                  </div>
                </form>
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
import { Inertia } from "@inertiajs/inertia";
import { QuillEditor } from "@vueup/vue-quill";
import { Switch } from "@headlessui/vue";
export default {
  name: "package",
  components: {
    AppLayout,
    QuillEditor,
    Switch,
  },
  //props: ['packages', 'errors'],
  props: {
    templates: Object,
    errors: Object,
  },
  setup(props) {
    const form = reactive({
      id: null,
      name: null,
      title: null,
      cc_email: null,
      body: null,
      type:ref(false),
      default: null,
    });
    const search = ref("");
    let editMode = ref(false);
    let isOpen = ref(false);
    function resetForm() {
      form.id = null;
      form.name = null;
      form.title = null;
      form.cc_email = null;
      form.body = null;
      form.type = ref(false);
      form.default = null;
    }
    function submit() {
      if (!editMode.value) {
        form._method = "POST";
        Inertia.post("/template", form, {
          preserveState: true,
          onSuccess: (page) => {
            closeModal();
            resetForm();
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },
          onError: (errors) => {
            closeModal();
            console.log("error ..".errors);
          },
        });
      } else {
        form._method = "PUT";
        Inertia.post("/template/" + form.id, form, {
          onSuccess: (page) => {
            form.componentKey++;
            closeModal();
            resetForm();
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
          },

          onError: (errors) => {
            closeModal();
            console.log("error ..".errors);
          },
        });
      }
    }
    function edit(data) {
      form.id = data.id;
      form.name = data.name;
      form.title = data.title;
      form.cc_email = data.cc_email;
      form.body = data.body;
      form.type = (data.type=='email')?true:false;
      form.default = (data.default ==1)?true:false;
      editMode.value = true;
      openModal();
    }

    function deleteRow(data) {
      if (!confirm("Are you sure want to remove?")) return;
      data._method = "DELETE";
      Inertia.post("/template/" + data.id, data);
      closeModal();
      resetForm();
    }
    function openModal() {
      isOpen.value = true;
    }
    const closeModal = () => {
      isOpen.value = false;
      resetForm();
      editMode.value = false;
    };

    return { form, submit, editMode, isOpen, openModal, closeModal, edit, deleteRow };
  },
};
</script>
<style scoped>
@import "@vueup/vue-quill/dist/vue-quill.snow.css";
input[type="number"]::-webkit-inner-spin-button {
  opacity: 1;
}
.noborder {
  border: none;
}
/* CHECKBOX TOGGLE SWITCH */
/* @apply rules for documentation, these do not work as inline style */

.toggle-checkbox:checked {
  @apply: right-0 border-green-400;
  right: 0;
  border-color: #68d391;
}
.toggle-checkbox:checked + .toggle-label {
  @apply: bg-green-400;
  background-color: #68d391;
}
.toggle-checkbox:active,
.toggle-checkbox:focus {
  outline: 0px;
  outline-offset: 0px;
  --tw-ring-inset: var(--tw-empty, /*!*/ /*!*/);
  --tw-ring-offset-width: 0px;
  --tw-ring-offset-color: #fff;
  --tw-ring-color: rgb(0 0 0 / 6%);
  --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
  --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
  box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
}
</style>
