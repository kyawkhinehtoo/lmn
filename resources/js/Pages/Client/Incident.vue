<template>
  <app-layout>
    <div class="py-1">
      <div class="mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-6 w-full" v-if="permission[0].read_incident ==1 || permission[0].write_incident ==1 ">
          <!--ticket list -->
          <div class="col-span-4 sm:col-span-4">
            <div class="py-2">
              <div class="inline-flex justify-between w-full gap-4">
                <div class="flex w-full">
                  <span class="z-10 mt-1 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                    <i class="fas fa-search"></i>
                  </span>
                  <input type="text" placeholder="Ticket/Customer" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none w-full pl-10" id="search" v-model="search" v-on:keyup.enter="searchIncident" />
                  
                   </div>
                <div class="flex  w-full  ">
                  <span class="z-10 mt-1 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2">
                    <i class="fas fa-sliders-h"></i>
                  </span>
                  <select v-model="incidentStatus" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none w-full pl-10" @change="changeStatus">
                    <option value="0">All</option>
                    <option value="1">WIP</option>
                    <option value="2">Escalated</option>
                    <option value="3">Closed</option>
                    <option value="4">Deleted</option>
                  </select>
                </div>
                <div class="flex w-1/2 justify-end">
                <button @click="newTicket()" class="text-center items-center px-4 py-3 bg-indigo-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-400 active:bg-indigo-600 focus:outline-none focus:border-gray-900 disabled:opacity-25 transition mr-1" v-if="permission[0].write_incident ==1">New Ticket<i class="fas fa-plus-circle opacity-75 lg:ml-1 text-sm"></i></button>
               </div>
              </div>
            </div>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mt-1" v-if="incidents.data">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"  @click="sortBy('date')">Date <i class="fas fa-sort text-gray-400"></i></th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortBy('code')">Ticket <i class="fas fa-sort text-gray-400"></i></th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortBy('ftth_id')">User <i class="fas fa-sort text-gray-400"></i> </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortBy('type')">Type <i class="fas fa-sort text-gray-400"></i> </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>

                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                  <tr v-for="row in incidents.data" v-bind:key="row.id" :class="[row.id == selected_id ? 'bg-indigo-200' : '']" @click="edit(row)" class="cursor-pointer">
                    <td class="px-6 py-3 whitespace-nowrap"><i :class="'fa fa-circle text-' + row.color"></i></td>
                    <td class="px-6 py-3 whitespace-nowrap">{{ row.date}} {{ row.time }}</td>
                    <td class="px-6 py-3 whitespace-nowrap">{{ row.code }}</td>
                    <td class="px-6 py-3 whitespace-nowrap" v-if="row.ftth_id">{{ row.ftth_id }}</td>
                    <td class="px-6 py-3 whitespace-nowrap capitalize">{{ row.type.replace("_", " ") }}</td>
                    <td class="px-6 py-3 whitespace-nowrap">{{ getStatus(row.status) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <span v-if="incidents.links">
              <pagination class="mt-6" :links="incidents.links" />
            </span> 
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mt-1 py-4 flex flex-col items-center"  v-if="!incidents.data">
             <no-data />
            </div>
          </div>
          <!-- end of ticket list -->
          <!--alarm panel -->
          <div class="col-span-2">
            <div class="bg-white rounded-lg w-full mx-auto mt-1 shadow-xl divide-y divide-gray-200 py-2 px-2">
              <div class="grid grid-cols-3 gap-2">
                <div class="col-span-1">
                  <label class="block text-sm font-normal text-center">Critical</label>
                  <span class="block bg-red-600 py-2 px-2 rounded-md text-center text-white text-sm">{{ critical }}</span>
                </div>
                <div class="col-span-1">
                  <label class="block text-sm font-normal text-center">High</label>
                  <span class="block bg-yellow-600 py-2 px-2 rounded-md text-center text-white text-sm">{{ high }}</span>
                </div>
                <div class="col-span-1">
                  <label class="block text-sm font-normal text-center">Normal</label>
                  <span class="block bg-yellow-400 py-2 px-2 rounded-md text-center text-white text-sm">{{ normal }}</span>
                </div>
              </div>
            </div>
            <incident-alert />
          </div>
          <!--end of alarm panel -->
        </div>
        <div class="flex justify-center" v-else>
          <span class="px-20 py-10 center bg-white inline-flex">
            <i class="fa fas fa-exclamation-triangle fa-2x text-yellow-600 mr-2"></i>
            <label class="font-bold mt-2 text-sm"> You Don't Have Permission To Access This Page</label>
         </span>
        </div>
      </div>
    </div>
    <div ref="isOpen" class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        ​

        <div class="bg-gray-50 rounded-sm pt-1 inline-block align-bottom overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
          <form @submit.prevent="submit">
            <div class="">
              <!-- ticket input panel -->
              <div class="">
                <!-- panel buttons -->
                <div class="py-2 divide-y divide-yellow-500">
                  <div class="mt-1 px-3 flex w-full justify-between">
                    <div class="flex">
                      <h2 class="font-semibold text-xl text-gray-800 leading-tight mt-2">Ticket Detail</h2>
                    </div>
                    <div class="flex">
                      <span class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 relative bg-white bg-white rounded-l-md text-sm shadow outline-none focus:outline-none"> Ticket ID </span>
                      <input type="text" v-model="form.code" name="code" id="code" class="mr-2 border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 relative bg-white bg-white rounded-r-md text-sm shadow outline-none focus:outline-none" disabled />
                      <ul class="flex col-span-1">
                        <li class="px-2 lg:px-3">
                          <label class="block text-sm">Critical</label>
                          <input v-model="form.priority" name="priority" value="critical" type="radio" class="form-radio h-5 w-5 text-red-600 bg-red-600" title="(1-4hrs)" />
                        </li>
                        <li class="px-2 lg:px-3">
                          <label class="block text-sm">High</label>
                          <input v-model="form.priority" name="priority" value="high" type="radio" class="form-radio h-5 w-5 text-yellow-600 bg-yellow-600" title="(1-12hrs)" />
                        </li>
                        <li class="px-2 lg:px-3">
                          <label class="block text-sm">Normal</label>
                          <input v-model="form.priority" name="priority" value="normal" type="radio" class="form-radio h-5 w-5 text-yellow-300 bg-yellow-300" title="(1-48hrs)" />
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
                <!-- end of panel buttons -->
                <div class="bg-white rounded-t-lg w-full mx-auto mt-1 shadow-xl divide-y divide-gray-200">
                  <!-- Tabs -->
                  <div class="inline-flex w-full bg-gray-50 rounded-t-lg">
                    <ul id="tabs" class="flex">
                      <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" :class="[tab == 1 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#" @click="tabClick(1)" preserve-state>Genaral</a></li>
                      <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" :class="[tab == 2 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#" @click="tabClick(2)" preserve-state>Tasks</a></li>
                      <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" :class="[tab == 3 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#" @click="tabClick(3)" preserve-state>Files</a></li>
                      <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" :class="[tab == 4 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#" @click="tabClick(4)" preserve-state>History</a></li>
                      <li class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" :class="[tab == 5 ? 'border-b-2 border-indigo-400 -mb-px' : 'opacity-50']"><a href="#" @click="tabClick(5)" preserve-state>LOG</a></li>
                    </ul>
                  </div>
                  <!-- Tab Contents -->
                  <div id="tab-contents">
                    <!-- tab1 -->
                    <div class="p-4" :class="[tab == 1 ? '' : 'hidden']">
                      <div class="grid grid-cols-5 gap-2">
                        <!-- date -->
                        <div class="py-2 col-span-1 sm:col-span-1">
                          <div class="mt-1 flex">
                            <label for="date" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Incident Detail : </label>
                          </div>
                        </div>
                        <div class="py-2 col-span-2 sm:col-span-2">
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="date" v-model="form.date" name="date" id="date" class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
                           
                          </div>
                           <p v-if="$page.props.errors.date" class="mt-2 text-sm text-red-500">{{ $page.props.errors.date }}</p>
                        </div>
                        <div class="py-2 col-span-2 sm:col-span-2">
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="time" v-model="form.time" name="time" class="form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
                           </div>
                           <p v-if="$page.props.errors.time" class="mt-2 text-sm text-red-500">{{ $page.props.errors.time }}</p>
                        </div>
                        <!-- end of date -->
                        <!-- ticket id -->
                        <!--
                    <div class="py-2 col-span-2 sm:col-span-2">
                      <div class="mt-1 flex">
                        <input type="text" v-model="form.code" name="code" id="code" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
                        <p v-if="$page.props.errors.code" class="mt-2 text-sm text-red-500">{{ $page.props.errors.code }}</p>
                      </div>
                    </div>
                    -->
                        <!-- end of ticket id -->
                        <!-- user id -->
                        <div class="py-2 col-span-1 sm:col-span-1">
                          <div class="mt-1 flex">
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> User ID : </label>
                          </div>
                        </div>
                        <div class="py-2 col-span-4 sm:col-span-4">
                          <div class="mt-1 flex rounded-md shadow-sm" v-if="customers.length !== 0">
                            <multiselect deselect-label="Selected already" :options="customers" track-by="id" label="ftth_id" v-model="form.customer_id" :allow-empty="false"></multiselect>
                          </div>
                           <p v-if="$page.props.errors.customer" class="mt-2 text-sm text-red-500">{{ $page.props.errors.customer }}</p>
                          </div>
                        <!-- end of user id -->
                        <!-- person incharge  -->
                        <div class="py-2 col-span-1 sm:col-span-1">
                          <div class="mt-1 flex">
                            <label for="incharge" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Person Incharge : </label>
                          </div>
                        </div>
                        <div class="py-2 col-span-4 sm:col-span-4">
                          <div class="mt-1 flex rounded-md shadow-sm" v-if="team.length !== 0">
                            <multiselect deselect-label="Selected already" :options="team" track-by="id" label="name" v-model="form.incharge_id" :allow-empty="false" :disabled="true"></multiselect>
                          </div>
                          <p v-if="$page.props.errors.incharge" class="mt-2 text-sm text-red-500">{{ $page.props.errors.incharge }}</p>
                        </div>
                        <!-- end of person incharge -->
                        <!-- type -->
                        <div class="py-2 col-span-1 sm:col-span-1">
                          <div class="mt-1 flex">
                            <label for="type" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Type : </label>
                          </div>
                        </div>
                        <div class="py-2 col-span-4 sm:col-span-4">
                          <div class="mt-1 flex">
                            <select v-model="form.type" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" required>
                              <option value="default">Please Choose Ticket Type</option>
                              <option value="service_complaint">Service Complaint</option>
                              <option value="relocation">Relocation</option>
                              <option value="plan_change">Plan Change</option>
                              <option value="information_update">Information Update</option>
                              <option value="suspension">Suspension</option>
                              <option value="resume">Resume</option>
                              <option value="termination">Termination</option>
                            </select>
                           </div>
                          <p v-if="$page.props.errors.type" class="mt-2 text-sm text-red-500">{{ $page.props.errors.type }}</p>
                       </div>
                        <!-- end of type -->
                        <!-- topic -->
                        <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'service_complaint'">
                          <div class="mt-1 flex">
                            <label for="topic" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Topic : </label>
                          </div>
                        </div>
                        <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'service_complaint'">
                          <div class="mt-1 flex">
                            <select v-model="form.topic" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300">
                              <option value="no_internet">No Internet</option>
                              <option value="los_redlight">LOS Redlight</option>
                              <option value="slow_performance">Slow Performance</option>
                              <option value="game_issue">Game Issue</option>
                              <option value="website_issue">Website Issue</option>
                              <option value="wifi_issue">Wifi Issue</option>
                              <option value="onu_issue">ONU Issue</option>
                              <option value="password_change">Password Changed</option>
                              <option value="other">Other</option>
                            </select>
                           </div>
                            <p v-if="$page.props.errors.topic" class="mt-2 text-sm text-red-500">{{ $page.props.errors.topic }}</p>
                        </div>
                        <!-- end of topic -->
                        <!-- status -->
                        <div class="py-2 col-span-1 sm:col-span-1">
                          <div class="mt-1 flex">
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Status : </label>
                          </div>
                        </div>
                        <div class="py-2 col-span-4 sm:col-span-4">
                          <div class="mt-1 flex">
                            <select v-model="form.status" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300">
                              <option value="1">WIP</option>
                              <option value="2">Escalated</option>
                              <option value="3">Closed</option>
                              <option value="4">Deleted</option>
                            </select>
                          </div>
                           <p v-if="$page.props.errors.status" class="mt-2 text-sm text-red-500">{{ $page.props.errors.status }}</p>
                        </div>
                        <!-- end of status -->
                        <!-- suspension -->
                        <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'suspension'">
                          <div class="mt-1 flex">
                            <label for="suspense" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Suspense Period : </label>
                          </div>
                        </div>
                        <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'suspension'">
                          <div class="grid grid-cols-2 gap-2">
                            <div class="col-span-1 sm:col-span-1">
                              <div class="mt-1 flex rounded-md shadow-sm">
                                <span class="-mt-1 z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
                                  <i class="fas fa-pause"></i>
                                </span>
                                <input type="date" v-model="form.suspense_from" name="suspense_from" id="suspense_from" class="pl-10 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
                              </div>
                              <p v-if="$page.props.errors.suspense_from" class="mt-2 text-sm text-red-500">{{ $page.props.errors.suspense_from }}</p>
                            </div>
                            <div class="col-span-1 sm:col-span-1">
                              <div class="mt-1 flex rounded-md shadow-sm">
                                <span class="-mt-1 z-10 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
                                  <i class="fas fa-play"></i>
                                </span>
                                <input type="date" v-model="form.suspense_to" name="suspense_to" id="suspense_to" class="pl-10 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
                              </div>
                              <p v-if="$page.props.errors.suspense_to" class="mt-2 text-sm text-red-500">{{ $page.props.errors.suspense_to }}</p>
                            </div>
                          </div>
                        </div>
                        <!-- end of suspension -->
                        <!-- resume -->
                        <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'resume'">
                          <div class="mt-1 flex">
                            <label for="resume" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Resume Date : </label>
                          </div>
                        </div>
                        <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'resume'">
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="z-10 -mt-1 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
                              <i class="fas fa-play"></i>
                            </span>
                            <input type="date" v-model="form.resume" name="resume" id="resume" class="pl-10 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
                          </div>
                             <p v-if="$page.props.errors.resume" class="mt-2 text-sm text-red-500">{{ $page.props.errors.resume }}</p>
                        </div>
                        <!-- end of resume -->
                        <!-- termination -->
                        <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'termination'">
                          <div class="mt-1 flex">
                            <label for="termination" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Termination Date : </label>
                          </div>
                        </div>
                        <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'termination'">
                          <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="z-10 -mt-1 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
                              <i class="fas fa-stop"></i>
                            </span>
                            <input type="date" v-model="form.termination" name="termination" id="termination" class="pl-10 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" />
                         </div>
                          <p v-if="$page.props.errors.termination" class="mt-2 text-sm text-red-500">{{ $page.props.errors.termination }}</p>
                        </div>
                        <!-- end of termination -->
                        <!-- relocation address -->
                        <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'relocation'">
                          <div class="mt-1 flex">
                            <label for="new_address" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> New Address : </label>
                          </div>
                        </div>
                        <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'relocation'">
                          <div class="mt-1 flex">
                            <textarea v-model="form.new_address" name="new_address" id="new_address" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"> </textarea>
                           </div>
                            <p v-if="$page.props.errors.new_address" class="mt-2 text-sm text-red-500">{{ $page.props.errors.new_address }}</p>
                        </div>
                        <!-- end of relocation address -->
                        <!-- relocation latlong -->
                        <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'relocation'">
                          <div class="mt-1 flex">
                            <label for="new_latlong" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> New Location : </label>
                          </div>
                        </div>
                        <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'relocation'">
                          <div class="grid grid-cols-2 gap-2">
                            <div class="col-span-1 sm:col-span-1">
                              <div class="mt-1 flex rounded-md shadow-sm">
                                <span class="z-10 -mt-1 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
                                  <i class="fas fa-location-arrow"></i>
                                </span>
                                <input type="text" v-model="form.latitude" name="latitude" id="latitude" class="pl-10 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Latitude" />
                              </div>
                              <p v-if="$page.props.errors.latitude" class="mt-2 text-sm text-red-500">{{ $page.props.errors.latitude }}</p>
                            </div>
                            <div class="col-span-1 sm:col-span-1">
                              <div class="mt-1 flex rounded-md shadow-sm">
                                <span class="z-10 -mt-1 leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
                                  <i class="fas fa-location-arrow"></i>
                                </span>
                                <input type="text" v-model="form.longitude" name="longitude" id="longitude" class="pl-10 form-input focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Longitude" />
                              </div>
                              <p v-if="$page.props.errors.longitude" class="mt-2 text-sm text-red-500">{{ $page.props.errors.longitude }}</p>
                            </div>
                          </div>
                        </div>
                        <!-- end of relocation latlong -->
                        <!-- plan change -->
                        <div class="py-2 col-span-1 sm:col-span-1" v-if="form.type == 'plan_change'">
                          <div class="mt-1 flex">
                            <label for="new_package" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> New Package: </label>
                          </div>
                        </div>
                        <div class="py-2 col-span-4 sm:col-span-4" v-if="form.type == 'plan_change'">
                          <div class="mt-1 flex rounded-md shadow-sm" v-if="packages.length !== 0">
                            <multiselect deselect-label="Selected already" :options="packages" track-by="id" label="item_data" v-model="form.package_id" :allow-empty="false"></multiselect>
                          </div>
                          <p v-if="$page.props.errors.package" class="mt-2 text-sm text-red-500">{{ $page.props.errors.package }}</p>
                        </div>
                        <!-- end of plan change -->
                        <!-- detail -->
                        <div class="py-2 col-span-1 sm:col-span-1">
                          <div class="mt-1 flex">
                            <label for="detail" class="block text-sm font-medium text-gray-700 mt-2 mr-2"> Detail : </label>
                          </div>
                        </div>
                        <div class="py-2 col-span-4 sm:col-span-4">
                          <div class="mt-1 flex">
                            <textarea v-model="form.description" name="detail" id="detail" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"> </textarea>
                             </div>
                            <p v-if="$page.props.errors.detail" class="mt-2 text-sm text-red-500">{{ $page.props.errors.detail }}</p>
                         </div>
                        <!-- end of detail -->
                      </div>
                    </div>
                    <!-- end of tab1 -->
                    <div class="p-4" :class="[tab == 2 ? '' : 'hidden']"><keep-alive><task :data="selected_id" :key="page_update" v-if="tab == 2" /></keep-alive></div>
                    <div class="p-4" :class="[tab == 3 ? '' : 'hidden']"><keep-alive><file :data="selected_id"  :key="page_update" v-if="tab == 3"/></keep-alive></div>
                    <div class="p-4" :class="[tab == 4 ? '' : 'hidden']"><keep-alive><history :data="selected_id" :key="page_update" v-if="tab == 4"/></keep-alive></div>
                    <div class="p-4" :class="[tab == 5 ? '' : 'hidden']"><keep-alive><log :data="selected_id" :key="page_update" v-if="tab == 5" /></keep-alive></div>
                  </div>
                </div>
              </div>
              <!-- end of ticket input panel -->
            </div>
             <div class="bg-indigo-50 px-3 py-3 sm:px-6 sm:flex sm:flex-row" :class="[tab==1? 'justify-between':'justify-end']">
              <div class="flex" v-if="tab ==1 &&  permission[0].write_incident ==1">
              <button class="inline-flex items-center px-4 py-3 bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-400 active:bg-indigo-600 focus:outline-none focus:border-indigo-900 disabled:opacity-25 transition mr-1" @click="submit()"><span v-if="editMode">Update</span><span v-if="editMode == false">Save</span><i class="fas fa-save opacity-75 lg:ml-1 text-sm"></i></button>
              <button @click="deleteIncident" class="inline-flex items-center px-4 py-3 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-400 active:bg-red-600 focus:outline-none focus:border-gray-500 disabled:opacity-25 transition mr-1"  v-show="selected_id">Delete<i class="lg:ml-1 fas fa-trash opacity-75 text-sm"></i></button>
              </div>
              <div class="flex" >             
              <button @click="closeModal" type="button" class="inline-flex items-center px-4 py-3 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-600 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 disabled:opacity-25 transition">Close <i class="lg:ml-1 fas fa-times-circle opacity-75 text-sm"></i></button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div v-if="loading" wire:loading class="fixed top-0 left-0 right-0 bottom-0 w-full h-screen z-50 overflow-hidden bg-gray-700 opacity-75 flex flex-col items-center justify-center">
              <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12 mb-4"></div>
              <h2 class="text-center text-white text-xl font-semibold">Loading...</h2>
              <p class="w-1/3 text-center text-white">This may take a few seconds, please don't close this page.</p>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout2";
import IncidentAlert from "@/Components/IncidentAlert";
import Task from "@/Components/Task";
import Log from "@/Components/Log";
import History from "@/Components/History";
import File from "@/Components/File";
import NoData from "@/Components/NoData";
import Pagination from "@/Components/Pagination";
import { reactive, ref, onMounted, onUpdated,provide,auth  } from "vue";
import Multiselect from "@suadelabs/vue3-multiselect";
import { Inertia } from "@inertiajs/inertia";
import { useForm } from '@inertiajs/inertia-vue3';

export default {
  name: "Incident",
  components: {
    AppLayout,
    Pagination,
    IncidentAlert,
    Multiselect,
    Task,
    File,
    Log,
    History,
    NoData,
  },
  props: {
    incidents: Object,
    noc: Object,
    team: Object,
    townships: Object,
    customers: Object,
    packages: Object,
    critical: Object,
    high: Object,
    normal: Object,
    errors: Object,
    permission: Object,
    user: Object,
  },
  setup(props) {
    const search = ref("");
    const sort = ref("");
    const page_update = ref(1);
    let show = ref(false);
    let editMode = ref(false);
    let selected_id = ref("");
    let incidentStatus = ref(0);
    let isOpen = ref(false);
    let loading = ref(false);
    provide('noc', props.noc);
    provide('permission', props.permission);
   
    const form = useForm({
      id: null,
      code: null,
      priority: "normal",
      customer_id: null,
      incharge_id: props.team.filter((d) => d.id == props.user.id)[0],
      type: "default",
      topic: null,
      status: 1,
      suspense_from: null,
      suspense_to: null,
      resume: null,
      termination: null,
      new_address: null,
      latitude: null,
      longitude: null,
      package_id: null,
      description: null,
      date: null,
      time: null,
    });
    let tab = ref(true);
    let selection = ref("");
    function typeChange(type) {
      selection.value = type;
    }
    function tabClick(val) {
      if(selected_id.value != null)
      tab.value = val;
    }

    function clearform() {
      selected_id.value = null;
      editMode.value = false;
      form.id = "";
      form.code = "";
      form.priority = "normal";
      form.customer_id = "";
      form.incharge_id = props.team.filter((d) => d.id == props.user.id)[0];
      form.type = "default";
      form.topic = "";
      form.status = 1;
      form.suspense_from = "";
      form.suspense_to = "";
      form.resume = "";
      form.termination = "";
      form.new_address = "";
      form.latitude = "";
      form.longitude = "";
      form.package_id = "";
      form.description = "";
      form.date = "";
      form.time = "";
    
    }
    function openModal() {
      tab.value = 1;
      isOpen.value = true;
    }
    function closeModal() {
      selected_id.value = null;
      isOpen.value = false;
      selected_id.value = null;
      errors.map(function (x) {
         console.log(x);
      });
    }
    function newTicket() {
      clearform();
      openModal();
    }
    function submit() {
      if (editMode.value != true) {
       
        Inertia.post("/incident",form, {
          preserveState: true,
          onSuccess: (page) => {
            loading.value = false;
            selected_id.value = page.props.response.id;
            let response = props.incidents.data.filter((d) => d.id == selected_id.value)[0];
            edit(response);
            page_update.value +=1;
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
           
          },
          onError: (errors) => {
            loading.value = false;
            console.log("error ..");
          },
          onStart:(pending) =>{
            console.log("Loading .." + pending);
               loading.value = true;
          },
        });
      } else {
        form._method = "PUT";
        
        Inertia.post("incident/" + form.id,form, {
          onSuccess: (page) => {
             loading.value = false;
            page_update.value +=1;
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
          onStart:(pending) =>{
            console.log("Loading .." + pending);
               loading.value = true;
          },
        });
      }
    }
    function edit(data) {
      let lat_long = null;
      selected_id.value = data.id;
     
      editMode.value = true;
      if (data.location != null) {
        lat_long = data.location.split(",", 2);
        form.latitude = lat_long[0];
        form.longitude = lat_long[1];
      }
      form.id = data.id;
      form.code = data.code;
      form.priority = data.priority;
      form.customer_id = props.customers.filter((d) => d.id == data.customer_id)[0];
      form.incharge_id = props.noc.filter((d) => d.id == data.incharge_id)[0];
      form.type = data.type;
      form.topic = data.topic;
      form.status = data.status;
      form.suspense_from = data.suspense_from;
      form.suspense_to = data.suspense_to;
      form.resume = data.resume;
      form.termination = data.termination;
      form.new_address = data.new_address;
      form.package_id = props.packages.filter((d) => d.id == data.package_id)[0];
      form.description = data.description;
      form.date = data.date;
      form.time = data.time;
      openModal();
    }
    function getStatus(data) {
      let status = "WIP";
      if (data == 1) {
        status = "WIP";
      } else if (data == 2) {
        status = "Escalated";
      } else if (data == 3){
        status = "Closed";
      } else if ( data == 4){
        status = "Deleted";
      }
      return status;
    }
    function deleteIncident(data) {
      if (!confirm("Are you sure want to remove?")) return;
      form._method = "PUT";
      form.status = 4;
        Inertia.post("/incident/" + form.id, form, {
          onSuccess: (page) => {
            page_update.value +=1;
            Toast.fire({
              icon: "success",
              title: page.props.flash.message,
            });
            closeModal();
          },
          onError: (errors) => {
            console.log("error ..".errors);
          },
        });
    }
    const searchIncident = () => {

      let url = "/incident/";
      if(incidentStatus.value != null){
        url = url+"?status="+incidentStatus.value
      }
      Inertia.get(url, { keyword: search.value }, { preserveState: true });
    };
    const changeStatus =() =>{
       let url = "/incident/";
      if(search.value != null){
        url = url+"?keyword="+search.value
      }
     
      Inertia.get(url, { status: incidentStatus.value }, { preserveState: true });
    }
    const sortBy = (d) => {
      let url = "/incident/?";
     
      sort.value = d;
      sort.order  = sort.order  !== 'asc' ? 'asc' : 'desc';
      let suburl = getURL();
      Inertia.get(url+suburl, { sort: sort.value, order: sort.order }, { preserveState: true });
    };
    const getURL = () =>{
      let suburl = "";
      if(search.value != null){
        suburl = "&keyword="+search.value
      }
      if(incidentStatus.value != null){
        suburl += "&status="+incidentStatus.value
      }
      return suburl;
    }
    function priorityColor() {
      props.incidents.data.map(function (x) {
        if (x.priority == "high") {
          x.color = x.status != 3 && x.status != 4 ? "yellow-600" : "gray-200";
        } else if (x.priority == "critical") {
          x.color = x.status != 3 && x.status != 4 ? "red-600" : "gray-200";
        } else {
          x.color = x.status != 3 && x.status != 4 ? "yellow-300" : "gray-200";
        }
      });
    }
 
    onMounted(() => {
      props.packages.map(function (x) {
        return (x.item_data = `${x.name} - ${x.contract_period} Months`);
      });
      priorityColor();
    });
    onUpdated(() => {
      priorityColor();
    });
    return { loading,openModal, closeModal, newTicket, isOpen, deleteIncident, searchIncident, edit, sortBy, submit, getStatus, clearform, changeStatus,form, sort, search, show, tabClick, tab, selection, selected_id, editMode, typeChange,incidentStatus,page_update};
  },
};
</script>
