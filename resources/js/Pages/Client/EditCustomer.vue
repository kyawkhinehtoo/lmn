<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-white leading-tight">Customer Details</h2>
    </template>
    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mt-5 md:mt-0 md:col-span-2">
         <form @submit.prevent="submit">
            <div class="shadow sm:rounded-md sm:overflow-hidden">
              <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">Customer Information</h6>
                <div class="grid grid-cols-4 gap-2">
                  <div class="col-span-1 sm:col-span-1">
                    <label for="name" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> Contact Person Name </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-user"></i>
                      </span>
                      <input type="text" v-model="form.name" name="name" id="name" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Customer Name" required :disabled="checkPerm('name')" />
                    </div>
                    <p v-if="$page.props.errors.name" class="mt-2 text-sm text-red-500">{{ $page.props.errors.name }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="nrc" class="block text-sm font-medium text-gray-700"> NRC/Passport </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-id-card"></i>
                      </span>
                      <input type="text" v-model="form.nrc" name="nrc" id="nrc" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="e.g 12/AaBbCc(N)123456" :disabled="checkPerm('nrc')" />
                    </div>
                    <p v-if="$page.props.errors.nrc" class="mt-2 text-sm text-red-500">{{ $page.props.errors.nrc }}</p>
                  </div>

                  <div class="col-span-1 sm:col-span-1">
                    <label for="phone_1" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> Primary Phone Number </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-phone"></i>
                      </span>
                      <input type="number" v-model="form.phone_1" name="phone_1" id="phone_1" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="e.g 0945000111" v-on:keypress="isNumber(event)" required :disabled="checkPerm('phone_1')" />
                    </div>
                    <p v-if="$page.props.errors.phone_1" class="mt-2 text-sm text-red-500">{{ $page.props.errors.phone_1 }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="phone_2" class="block text-sm font-medium text-gray-700"> Secondary Phone Number </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-phone"></i>
                      </span>
                      <input type="number" v-model="form.phone_2" name="phone_2" id="phone_2" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="e.g 0945000111" v-on:keypress="isNumber(event)" :disabled="checkPerm('phone_2')" />
                    </div>
                    <p v-if="$page.props.errors.phone_2" class="mt-2 text-sm text-red-500">{{ $page.props.errors.phone_2 }}</p>
                  </div>
                </div>
                <div class="grid grid-cols-4 gap-2">
                  <div class="col-span-1 sm:col-span-1">
                    <label for="dob" class="block text-sm font-medium text-gray-700"> Date of Birth </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <input type="date" v-model="form.dob" name="dob" id="dob" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" :disabled="checkPerm('dob')" />
                    </div>
                    <p v-if="$page.props.errors.dob" class="mt-2 text-sm text-red-500">{{ $page.props.errors.dob }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="email" class="block text-sm font-medium text-gray-700"> Email Address </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-envelope"></i>
                      </span>
                      <input type="email" v-model="form.email" name="email" id="email" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="e.g johndoe@abc.com" :disabled="checkPerm('email')" />
                    </div>
                    <p v-if="$page.props.errors.email" class="mt-2 text-sm text-red-500">{{ $page.props.errors.email }}</p>
                  </div>
                  <div class="col-span-2 sm:col-span-2">
                    <label for="township" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> Township </label>
                    <div class="mt-1 flex rounded-md shadow-sm" v-if="townships.length !== 0">
                      <multiselect deselect-label="Selected already" :options="townships" track-by="id" label="name" v-model="form.township" :allow-empty="false" :disabled="checkPerm('township_id')"></multiselect>
                    </div>
                    <p v-if="$page.props.errors.township" class="mt-2 text-sm text-red-500">{{ $page.props.errors.township }}</p>
                  </div>
                </div>

                <div class="grid grid-cols-4 gap-2">
                  <div class="col-span-1 sm:col-span-1">
                    <label for="latitude" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> Latitude </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
                        <i class="fas fa-location-arrow"></i>
                      </span>
                      <input type="text" v-model="form.latitude" name="latitude" id="latitude" class="pl-10 mt-1 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" v-on:keypress="isNumber(event)" required :disabled="checkPerm('location')" />
                    </div>
                    <p v-if="$page.props.errors.latitude" class="mt-2 text-sm text-red-500">{{ $page.props.errors.latitude }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="longitude" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> Longitude </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
                        <i class="fas fa-location-arrow"></i>
                      </span>
                      <input type="text" v-model="form.longitude" name="longitude" id="longitude" class="pl-10 mt-1 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" v-on:keypress="isNumber(event)" required :disabled="checkPerm('location')" />
                    </div>
                    <p v-if="$page.props.errors.longitude" class="mt-2 text-sm text-red-500">{{ $page.props.errors.longitude }}</p>
                  </div>
                  <div class="col-span-2 sm:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> Full Address </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-map-marker-alt"></i>
                      </span>
                      <textarea v-model="form.address" name="address" id="address" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" required :disabled="checkPerm('address')" />
                    </div>
                    <p v-if="$page.props.errors.address" class="mt-2 text-sm text-red-500">{{ $page.props.errors.address }}</p>
                  </div>
                </div>
                <hr class="my-4 md:min-w-full" />
                <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">Company Information (if required)</h6>

                <div class="grid grid-cols-4 gap-2">
                  <div class="col-span-2 sm:col-span-2">
                    <label for="company_name" class="block text-sm font-medium text-gray-700"> Company Name </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-building"></i>
                      </span>
                      <input type="text" v-model="form.company_name" name="company_name" id="company_name" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" :disabled="checkPerm('company_name')" />
                    </div>
                    <p v-if="$page.props.errors.company_name" class="mt-2 text-sm text-red-500">{{ $page.props.errors.company_name }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="company_registration" class="block text-sm font-medium text-gray-700"> Company Registration No. </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-registered"></i>
                      </span>
                      <input type="text" v-model="form.company_registration" name="company_registration" id="company_registration" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" :disabled="checkPerm('company_registration')" />
                    </div>
                    <p v-if="$page.props.errors.company_registration" class="mt-2 text-sm text-red-500">{{ $page.props.errors.company_registration }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="typeof_business" class="block text-sm font-medium text-gray-700"> Type of Business </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-briefcase"></i>
                      </span>
                      <input type="text" v-model="form.typeof_business" name="typeof_business" id="typeof_business" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" :disabled="checkPerm('typeof_business')" />
                    </div>
                    <p v-if="$page.props.errors.typeof_business" class="mt-2 text-sm text-red-500">{{ $page.props.errors.typeof_business }}</p>
                  </div>
                </div>

                <hr class="my-4 md:min-w-full" />
                <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">Sale Information</h6>
                <div class="grid grid-cols-4 gap-2">
                  <div class="col-span-1 sm:col-span-1">
                    <div class="flex items-start">
                      <div class="flex items-center h-5">
                        <input id="order_form_sign_status" name="order_form_sign_status" v-model="form.order_form_sign_status" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                      </div>
                      <div class="ml-3 text-sm">
                        <label for="order_form_sign_status" class="font-medium text-gray-700">Order Form Status</label>
                        <p class="text-gray-500">Check and save once customer has signed the order form.</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="status" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> Customer Status </label>
                    <div class="mt-1 flex rounded-md shadow-sm" v-if="status_list.length !== 0">
                      <multiselect deselect-label="Selected already" :options="status_list" track-by="id" label="name" v-model="form.status" :allow-empty="false" :disabled="checkPerm('status_id')"></multiselect>
                    </div>
                    <p v-if="$page.props.errors.status" class="mt-2 text-sm text-red-500">{{ $page.props.errors.status }}</p>
                  </div>

                  <div class="col-span-1 sm:col-span-1">
                    <label for="order_date" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> Order Date </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <input type="date" v-model="form.order_date" name="order_date" id="order_date" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" required :disabled="checkPerm('order_date')" />
                    </div>
                    <p v-if="$page.props.errors.order_date" class="mt-2 text-sm text-red-500">{{ $page.props.errors.order_date }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="sale_person" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> Sale Person </label>
                    <div class="mt-1 flex rounded-md shadow-sm" v-if="sale_persons.length !== 0">
                      <multiselect deselect-label="Selected already" :options="sale_persons" track-by="id" label="name" v-model="form.sale_person" :allow-empty="false" :disabled="checkPerm('sale_person_id')"></multiselect>
                    </div>
                    <p v-if="$page.props.errors.sale_person" class="mt-2 text-sm text-red-500">{{ $page.props.errors.sale_person }}</p>
                  </div>
                </div>

                <div class="grid grid-cols-4 gap-2">
                  <div class="col-span-1 sm:col-span-1">
                    <label for="package" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> Package </label>
                    <div class="mt-1 flex rounded-md shadow-sm" v-if="packages.length !== 0">
                      <multiselect deselect-label="Selected already" :options="packages" track-by="id" label="item_data" v-model="form.package" :allow-empty="false" :disabled="checkPerm('package_id')"></multiselect>
                    </div>
                    <p v-if="$page.props.errors.package" class="mt-2 text-sm text-red-500">{{ $page.props.errors.package }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="sale_channel" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> Sale Channel </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-external-link-alt"></i>
                      </span>
                      <input type="text" v-model="form.sale_channel" name="sale_channel" id="sale_channel" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="e.g Facebook/ Person Name" required :disabled="checkPerm('sale_channel')" />
                    </div>
                    <p v-if="$page.props.errors.sale_channel" class="mt-2 text-sm text-red-500">{{ $page.props.errors.sale_channel }}</p>
                  </div>

                  <div class="col-span-2 sm:col-span-2">
                    <label for="remark" class="block text-sm font-medium text-gray-700"> Remark </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-comment"></i>
                      </span>
                      <textarea name="remark" v-model="form.remark" id="remark" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" :disabled="checkPerm('remark')" />
                    </div>
                    <p v-if="$page.props.errors.remark" class="mt-2 text-sm text-red-500">{{ $page.props.errors.remark }}</p>
                  </div>
                </div>

                <hr class="my-4 md:min-w-full" />
                <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">Installation Information</h6>
                <div class="grid grid-cols-4 gap-2">
                  <div class="col-span-1 sm:col-span-1">
                    <label for="prefer_install_date" class="block text-sm font-medium text-gray-700"> Prefer Installation Date </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-tools"></i>
                      </span>
                      <input v-model="form.prefer_install_date" type="date" name="prefer_install_date" id="prefer_install_date" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" :disabled="checkPerm('prefer_install_date')" />
                    </div>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="installation_date" class="block text-sm font-medium text-gray-700"> Actual Installed Date </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-tools"></i>
                      </span>
                      <input v-model="form.installation_date" type="date" name="installation_date" id="installation_date" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" :disabled="checkPerm('installation_date')" />
                    </div>
                  </div>
                  <p v-if="$page.props.errors.installation_date" class="mt-2 text-sm text-red-500">{{ $page.props.errors.installation_date }}</p>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="project_id" class="block text-sm font-medium text-gray-700"> Project/ Partner </label>
                    <div class="mt-1 flex rounded-md shadow-sm" v-if="projects.length !== 0">
                      <multiselect deselect-label="Selected already" :options="projects" track-by="id" label="name" v-model="form.project" :allow-empty="true" :disabled="checkPerm('project_id')"></multiselect>
                    </div>
                    <p v-if="$page.props.errors.project" class="mt-2 text-sm text-red-500">{{ $page.props.errors.project }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="subcom" class="block text-sm font-medium text-gray-700"> Subcom </label>
                    <div class="mt-1 flex rounded-md shadow-sm" v-if="subcoms.length !== 0">
                      <multiselect deselect-label="Selected already" :options="subcoms" track-by="id" label="name" v-model="form.subcom" :allow-empty="true" :disabled="checkPerm('subcom_id')"></multiselect>
                    </div>
                    <p v-if="$page.props.errors.subcom" class="mt-2 text-sm text-red-500">{{ $page.props.errors.subcom }}</p>
                  </div>
                </div>

                <hr class="my-5 md:min-w-full" />
                <h6 class="md:min-w-full text-indigo-700 text-xs uppercase font-bold block pt-1 no-underline">Billing Information</h6>
                <div class="grid grid-cols-4 gap-2">
                  <div class="col-span-1 sm:col-span-1">
                    <label for="ftth_id" class="block text-sm font-medium text-gray-700"><span class="text-red-500">*</span> Customer ID </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-key"></i>
                      </span>
                      <input type="text" v-model="form.ftth_id" name="ftth_id" id="ftth_id" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" :disabled="checkPerm('ftth_id')" required />
                    </div>
                    <p v-if="$page.props.errors.ftth_id" class="mt-2 text-sm text-red-500">{{ $page.props.errors.ftth_id }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="billing_attention" class="block text-sm font-medium text-gray-700">Billing Attention </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-user"></i>
                      </span>
                      <input type="text" v-model="form.billing_attention" name="billing_attention" id="billing_attention" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Billing Attention Name" required :disabled="checkPerm('billing_attention')" />
                    </div>
                    <p v-if="$page.props.errors.billing_attention" class="mt-2 text-sm text-red-500">{{ $page.props.errors.billing_attention }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="billing_phone" class="block text-sm font-medium text-gray-700"> Billing Phone </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-mobile"></i>
                      </span>
                      <input type="text" v-model="form.billing_phone" name="billing_phone" id="billing_phone" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="e.g 09123456" :disabled="checkPerm('billing_phone')" />
                    </div>
                    <p v-if="$page.props.errors.billing_phone" class="mt-2 text-sm text-red-500">{{ $page.props.errors.billing_phone }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="billing_email" class="block text-sm font-medium text-gray-700"> Billing Email </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-at"></i>
                      </span>
                      <input type="text" v-model="form.billing_email" name="billing_email" id="billing_email" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="e.g johndoe@abc.com" :disabled="checkPerm('billing_email')" />
                    </div>
                    <p v-if="$page.props.errors.billing_email" class="mt-2 text-sm text-red-500">{{ $page.props.errors.billing_email }}</p>
                  </div>
                </div>

                <div class="grid grid-cols-4 gap-2">
                  <div class="col-span-2 sm:col-span-2">
                    <label for="billing_address" class="block text-sm font-medium text-gray-700">Billing Address </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-map-marker-alt"></i>
                      </span>
                      <textarea v-model="form.billing_address" name="billing_address" id="billing_address" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" required :disabled="checkPerm('billing_address')" />
                    </div>
                    <p v-if="$page.props.errors.billing_address" class="mt-2 text-sm text-red-500">{{ $page.props.errors.billing_address }}</p>
                  </div>

                  <div class="col-span-1 sm:col-span-1">
                    <label for="bill_start_date" class="block text-sm font-medium text-gray-700"> Billing Start Date </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <input type="date" v-model="form.bill_start_date" name="bill_start_date" id="bill_start_date" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" :disabled="checkPerm('bill_start_date')" />
                    </div>
                    <p v-if="$page.props.errors.bill_start_date" class="mt-2 text-sm text-red-500">{{ $page.props.errors.bill_start_date }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="deposit_receive_date" class="block text-sm font-medium text-gray-700"> Deposit Received Date </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <input type="date" v-model="form.deposit_receive_date" name="deposit_receive_date" id="deposit_receive_date" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" :disabled="checkPerm('deposit_receive_date')" />
                    </div>
                    <p v-if="$page.props.errors.deposit_receive_date" class="mt-2 text-sm text-red-500">{{ $page.props.errors.deposit_receive_date }}</p>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                  <div class="col-span-1 sm:col-span-1">
                    <label for="deposit_receive_from" class="block text-sm font-medium text-gray-700"> Deposit Received From </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-file-invoice-dollar"></i>
                      </span>
                      <input type="text" v-model="form.deposit_receive_from" name="deposit_receive_from" id="deposit_receive_from" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="e.g Kpay, WaveMoney" :disabled="checkPerm('deposit_receive_from')" />
                    </div>
                    <p v-if="$page.props.errors.deposit_receive_from" class="mt-2 text-sm text-red-500">{{ $page.props.errors.deposit_receive_from }}</p>
                  </div>
                  <div class="col-span-1 sm:col-span-1">
                    <label for="deposit_receive_amount" class="block text-sm font-medium text-gray-700"> Deposit Amount </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <span class="z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                        <i class="fas fa-money-bill"></i>
                      </span>
                      <input type="number" v-model="form.deposit_receive_amount" name="deposit_receive_amount" id="deposit_receive_amount" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300" placeholder="Amount in MMK" v-on:keypress="isNumber(event)" :disabled="checkPerm('deposit_receive_amount')" />
                      <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm"> Kyats </span>
                    </div>
                    <p v-if="$page.props.errors.deposit_receive_amount" class="mt-2 text-sm text-red-500">{{ $page.props.errors.deposit_receive_amount }}</p>
                  </div>
                </div>
              </div>
              <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <inertia-link :href="route('customer.index')" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 shadow-sm focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150">Back</inertia-link>

                <button @click="resetForm" type="button" class="ml-2 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 shadow-sm focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150">Reset</button>

                <button wire:click.prevent="submit" type="submit" class="ml-2 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" v-show="!editMode">Save</button>
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
import Multiselect from "@suadelabs/vue3-multiselect";
import { reactive,ref, onMounted } from "vue";
import { Inertia } from "@inertiajs/inertia";
export default {
  name: "EditCustomer",
  components: {
    AppLayout,
    Multiselect,
  },
  props: {
    packages: Object,
    projects: Object,
    sale_persons: Object,
    townships: Object,
    errors: Object,
    customer: Object,
    status_list: Object,
    subcoms: Object,
    roles: Object,
    users: Object,
  },
  setup(props) {
      let lat_long = props.customer.location.split(",", 2); 
    const form = reactive({
      id: props.customer.id,
      name: props.customer.name,
      nrc: props.customer.nrc,
      dob: props.customer.dob,
      phone_1: props.customer.phone_1,
      phone_2: props.customer.phone_2,
      email: props.customer.email,
      address: props.customer.address,
      latitude: lat_long[0],
      longitude: lat_long[1],
      order_form_sign_status: (props.customer.order_form_sign_status)?true:false,
      order_date: props.customer.order_date,
      sale_channel: props.customer.sale_channel,
      remark: props.customer.remark,
      installation_date:props.customer.installation_date,
      ftth_id: props.customer.ftth_id,
      bill_start_date: props.customer.bill_start_date,
      deposit_receive_date: props.customer.deposit_receive_date,
      deposit_receive_from: props.customer.deposit_receive_from,
      deposit_receive_amount: props.customer.deposit_receive_amount,
      township: "",
      sale_person: "",
      package: "",
      project: "",
      status: "",
      subcom: "",
      company_name: props.customer.company_name,
      company_registration: props.customer.company_registration,
      typeof_business: props.customer.typeof_business,
      prefer_install_date: props.customer.prefer_install_date,
      billing_attention: props.customer.billing_attention,
      billing_phone: props.customer.billing_phone,
      billing_email: props.customer.billing_email,
      billing_address:props.customer.billing_address,
    });

    function submit() {
   
        form._method = "PUT";
        Inertia.post("/customer/" + form.id, form, {
          onSuccess: (page) => {
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
    function checkPerm(data){
      const my_role = props.roles.filter((d)=> d.id == props.users.role)[0];
      let role_arr = my_role.permission.split(',');
      let disable = role_arr.includes(data);
      return !disable;
    }
      function isNumber(evt) {
      evt = evt ? evt : window.event;
      var charCode = evt.which ? evt.which : evt.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
        evt.preventDefault();
      } else {
        return true;
      }
    }
    onMounted(() => {
      form.township = props.townships.filter((d) => d.id == props.customer.township_id)[0],
      form.sale_person = props.sale_persons.filter((d) => d.id == props.customer.sale_person_id)[0],
      form.package = props.packages.filter((d) => d.id == props.customer.package_id)[0],
      form.project = props.projects.filter((d) => d.id == props.customer.project_id)[0],
      form.status = props.status_list.filter((d) => d.id == props.customer.status_id)[0],
      form.subcom = props.subcoms.filter((d) => d.id == props.customer.subcom_id)[0],
      props.packages.map(function (x) {
        return (x.item_data = `${x.name} - ${x.contract_period} Months`);
      })
    });
    return { form,submit,isNumber,checkPerm };
  },
};
</script>
<style>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
/* .multiselect__input, .multiselect__single{
    outline:none !important;
    font-size: 0.875rem !important;
    line-height: 1.25rem !important;
    margin-bottom: 2px !important;
    margin-top: 2px !important;
}
.multiselect__input select{
    font-size: 0.875rem !important;
}
.multiselect__input:focus{
    outline-offset: 0;
    padding-top:0px;
} */
</style>
