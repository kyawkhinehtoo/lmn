<template>
  <div id="root">
    <nav
      class="md:left-0 md:block md:fixed md:top-0 md:bottom-0 md:overflow-y-auto md:flex-row md:flex-nowrap md:overflow-hidden shadow-xl bg-white flex flex-wrap items-center justify-between relative md:w-64 z-10 py-4 px-6 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-200">
      <div
        class="md:flex-col md:items-stretch md:min-h-full md:flex-nowrap px-0 flex items-center justify-between w-full mx-auto">
        <button
          class="cursor-pointer text-black opacity-50 md:hidden px-3 py-1 text-xl leading-none bg-transparent rounded border border-solid border-transparent"
          type="button" onclick="toggleNavbar('example-collapse-sidebar')">
          <i class="fas fa-bars"></i>
        </button>
        <a class="flex md:min-w-full md:grid md:grid-cols-2 items-center align-middle uppercase"
          href="javascript:void(0)">
          <div class=" col-span-1">
            <img src="/storage/images/logo.png" class="md:w-full w-32" />
          </div>
          <div class=" col-span-1 w-2/3">
            <span class="text-sm font-bold antialiased text-lm-orange whitespace-nowrap">
              Long Mao
            </span>
            <span class="text-sm font-bold antialiased text-lm-gray">
              Network
            </span>
          </div>

        </a>
        <ul class="md:hidden items-center flex flex-wrap list-none">
          <li class="inline-block relative">
            <a class="text-blueGray-500 block py-1 px-3" href="#pablo"
              onclick="openDropdown(event,'notification-dropdown')"><i class="fas fa-bell"></i></a>
            <div class="hidden bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg mt-1"
              style="min-width: 12rem" id="notification-dropdown">
              <a href="#pablo"
                class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700">Action</a><a
                href="#pablo"
                class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700">Another
                action</a><a href="#pablo"
                class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700">Something
                else here</a>
              <div class="h-0 my-2 border border-solid border-blueGray-100"></div>
              <a href="#pablo"
                class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700">Seprated
                link</a>
            </div>
          </li>
          <li class="inline-block relative">
            <a class="text-blueGray-500 block" href="#pablo" onclick="openDropdown(event,'user-responsive-dropdown')">
              <div class="items-center flex">
                <span
                  class="w-12 h-12 text-sm text-white bg-blueGray-200 inline-flex items-center justify-center rounded-full"><img
                    alt="..." class="w-full rounded-full align-middle border-none shadow-lg" src="" /></span>
              </div>
            </a>
            <div class="hidden bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg mt-1"
              style="min-width: 12rem" id="user-responsive-dropdown">
              <a href="#pablo"
                class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700">Action</a><a
                href="#pablo"
                class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700">Another
                action</a><a href="#pablo"
                class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700">Something
                else here</a>
              <div class="h-0 my-2 border border-solid border-blueGray-100"></div>
              <a href="#pablo"
                class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-blueGray-700">Seprated
                link</a>
            </div>
          </li>
        </ul>
        <div
          class="md:flex md:flex-col md:items-stretch md:opacity-100 md:relative md:mt-4 md:shadow-none shadow absolute top-0 left-0 right-0 z-40 overflow-y-auto overflow-x-hidden h-auto items-center flex-1 rounded hidden"
          id="example-collapse-sidebar">
          <div class="md:min-w-full md:hidden block pb-4 mb-4 border-b border-solid border-blueGray-200">
            <div class="flex flex-wrap">
              <div class="w-6/12">
                <a class="md:block text-left md:pb-2 text-blueGray-600 mr-0 inline-block whitespace-nowrap text-sm uppercase font-bold p-4 px-0"
                  href="javascript:void(0)">
                  Tailwind Starter Kit Mobile
                  <!--mobile -->
                </a>
              </div>
              <div class="w-6/12 flex justify-end">
                <button type="button"
                  class="cursor-pointer text-black opacity-50 md:hidden px-3 py-1 text-xl leading-none bg-transparent rounded border border-solid border-transparent"
                  onclick="toggleNavbar('example-collapse-sidebar')">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </div>
          <form class="mt-6 mb-4 md:hidden">
            <div class="mb-3 pt-0">
              <input type="text" placeholder="Search"
                class="border-0 px-3 py-2 h-12 border-solid border-blueGray-500 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-base leading-snug shadow-none outline-none focus:outline-none w-full font-normal" />
            </div>
          </form>

          <ul class="md:flex-col md:min-w-full flex flex-col list-none">
            <li class="items-center">
              <jet-nav-link :href="route('dashboard')" :active="route().current('dashboard')"> <i
                  class="fas fa-tv opacity-75 mr-2 text-sm w-6"></i> Dashboard </jet-nav-link>
            </li>
          </ul>
          <hr class="my-4 md:min-w-full" />
          <div v-if="$page.props.user.role == 1 || $page.props.user.role == 2">

            <h6 @click="toggleMenu('admin')"
              class="mb-2 text-gray-600 hover:gray-blue-500 text-xs uppercase py-3 font-bold block px-4 rounded-md bg-blueGray-100 shadow-sm cursor-pointer flex justify-between z-10">
              <label class="flex"> Admin Panel </label> <i class="fa fas text-right flex"
                :class="{ 'fa-caret-right': !admin, 'fa-caret-down': admin }"></i>
            </h6>
            <TransitionRoot :show="admin" enter="transition opacity duration-300 transform" enter-from="opacity-0"
              leave="transition opacity duration-100 transform" enter-to="opacity-100" leave-from="opacity-100"
              leave-to="opacity-0">
              <ul class="md:flex-col md:min-w-full flex flex-col list-none">
                <li>
                  <jet-nav-link :href="route('user.index')" :active="route().current('user.index')"> <i
                      class="fas fa-user opacity-75 mr-2 text-sm w-6"></i> User Setup </jet-nav-link>
                </li>
                <li>
                  <jet-nav-link :href="route('role.index')" :active="route().current('role.index')"> <i
                      class="fas fa-user-tag opacity-75 mr-2 text-sm w-6"></i> Role Setup </jet-nav-link>
                </li>
                <li>
                  <jet-nav-link :href="route('city.index')" :active="route().current('city.index')"> <i
                      class="fas fa-city opacity-75 mr-2 text-sm w-6"></i> City Setup </jet-nav-link>
                </li>
                <li>
                  <jet-nav-link :href="route('township.index')" :active="route().current('township.index')"> <i
                      class="fas fa-city opacity-75 mr-2 text-sm w-6"></i> Township Setup </jet-nav-link>
                </li>

                <li>
                  <jet-nav-link :href="route('equiptment.index')" :active="route().current('equiptment.index')"> <i
                      class="fas fa-gamepad opacity-75 mr-2 text-sm w-6"></i> Bundle Setup </jet-nav-link>
                </li>
                <li>
                  <jet-nav-link :href="route('pop.index')" :active="route().current('pop.index')"> <i
                      class="fas fa-building opacity-75 mr-2 text-sm w-6"></i> POP Setup </jet-nav-link>
                </li>
                <li>
                  <jet-nav-link :href="route('port.index')" :active="route().current('port.index')"> <i
                      class="fas fa-server opacity-75 mr-2 text-sm w-6"></i> DN Setup </jet-nav-link>
                </li>
                <li>
                  <jet-nav-link :href="route('snport.index')" :active="route().current('snport.index')"> <i
                      class="fas fa-network-wired opacity-75 mr-2 text-sm w-6"></i> SN Setup </jet-nav-link>
                </li>
                <li>
                  <jet-nav-link :href="route('sla.index')" :active="route().current('sla.index')"> <i
                      class="fas fa-percentage opacity-75 mr-2 text-sm w-6"></i> SLA Setup </jet-nav-link>
                </li>
                <li>
                  <jet-nav-link :href="route('package.index')" :active="route().current('package.index')"> <i
                      class="fas fa-cube opacity-75 mr-2 text-sm w-6"></i> Package Setup </jet-nav-link>
                </li>
                <li>
                  <jet-nav-link :href="route('project.index')" :active="route().current('project.index')"> <i
                      class="fas fa-handshake opacity-75 mr-2 text-sm w-6"></i> Project Setup </jet-nav-link>
                </li>
                <li>
                  <jet-nav-link :href="route('status.index')" :active="route().current('status.index')"> <i
                      class="fas fa-user-tag opacity-75 mr-2 text-sm w-6"></i> Customer Status </jet-nav-link>
                </li>
                <li>
                  <jet-nav-link :href="route('template.index')" :active="route().current('template.index')"> <i
                      class="fas fa-envelope opacity-75 mr-2 text-sm w-6"></i> Template </jet-nav-link>
                </li>
                <li>
                  <jet-nav-link :href="route('announcement.list')" :active="route().current('announcement.*')"> <i
                      class="fas fa-bullhorn opacity-75 mr-2 -mt-1 text-sm w-6"></i> Announcement </jet-nav-link>
                </li>
                <li>
                  <jet-nav-link :href="route('smsgateway.index')" :active="route().current('smsgateway.*')"> <i
                      class="fas fa-sms opacity-75 mr-2 -mt-1 text-sm w-6"></i> SMS Gateway </jet-nav-link>
                </li>
                <li>
                  <jet-nav-link :href="route('radiusconfig.index')" :active="route().current('radiusconfig.*')"> <i
                      class="fas fa-sms opacity-75 mr-2 -mt-1 text-sm w-6"></i> Radius Config </jet-nav-link>
                </li>
                <li>
                  <jet-nav-link :href="route('billconfig.index')" :active="route().current('billconfig.*')"> <i
                      class="fas fa-sms opacity-75 mr-2 -mt-1 text-sm w-6"></i> Billing Config </jet-nav-link>
                </li>
              </ul>
            </TransitionRoot>
            <hr class="my-4 md:min-w-full" />
          </div>
          <h6 @click="toggleMenu('user')"
            class="mb-2 text-gray-600 hover:gray-blue-500 text-xs uppercase py-3 font-bold block px-4 rounded-md bg-blueGray-100 shadow-sm cursor-pointer flex justify-between z-10">
            <label class="flex"> User Panel </label> <i class="fa fas text-right flex"
              :class="{ 'fa-caret-right': !user, 'fa-caret-down': user }"></i>
          </h6>
          <TransitionRoot :show="user" enter="transition opacity duration-300 transform" enter-from="opacity-0"
            leave="transition opacity duration-100 transform" enter-to="opacity-100" leave-from="opacity-100"
            leave-to="opacity-0">
            <ul class="md:flex-col md:min-w-full flex flex-col list-none md:mb-4">
              <li>
                <jet-nav-link :href="route('customer.index')" :active="route().current('customer.*')"> <i
                    class="fas fa-users opacity-75 mr-2 text-sm w-6"></i> Customer </jet-nav-link>
              </li>
              <li>
                <jet-nav-link :href="route('servicerequest.index')" :active="route().current('servicerequest.*')"> <i
                    class="fas fa-tasks opacity-75 mr-2 text-sm w-6"></i> Service Request </jet-nav-link>
              </li>
              <li>
                <jet-nav-link :href="route('incident.index')" :active="route().current('incident.index')"> <i
                    class="fas fa-users opacity-75 mr-2 text-sm w-6"></i> Incident Panel </jet-nav-link>
              </li>
            </ul>
          </TransitionRoot>
          <span
            v-if="$page.props.role.bill_generation || $page.props.role.bill_receipt || $page.props.role.bill_readonly">
            <hr class="my-4 md:min-w-full" />
            <h6 @click="toggleMenu('billing')"
              class="mb-2 text-gray-600 hover:gray-blue-500 text-xs uppercase py-3 font-bold block px-4 rounded-md bg-blueGray-100 shadow-sm cursor-pointer flex justify-between z-10">
              <label class="flex"> Billing Panel </label> <i class="fa fas text-right flex"
                :class="{ 'fa-caret-right': !billing, 'fa-caret-down': billing }"></i>
            </h6>
            <TransitionRoot :show="billing" enter="transition opacity duration-300 transform" enter-from="opacity-0"
              leave="transition opacity duration-100 transform" enter-to="opacity-100" leave-from="opacity-100"
              leave-to="opacity-0">
              <ul class="md:flex-col md:min-w-full flex flex-col list-none md:mb-4">
                <li v-if="$page.props.role.bill_generation">
                  <jet-nav-link :href="route('billGenerator')" :active="route().current('billGenerator')"> <i
                      class="fas fa-cogs opacity-75 mr-2 text-sm w-6"></i> Bill Generator </jet-nav-link>
                </li>

                <li v-if="$page.props.role.bill_generation">
                  <jet-nav-link :href="route('tempBilling')" :active="route().current('tempBilling')"> <i
                      class="fas fa-clipboard-list opacity-75 mr-2 text-sm w-6"></i>Temp Bill List </jet-nav-link>
                </li>
                <li v-if="$page.props.role.bill_generation || $page.props.role.bill_readonly">
                  <jet-nav-link :href="route('showbill')" :active="route().current('showbill')"> <i
                      class="fas fa-coins opacity-75 mr-2 text-sm w-6"></i> Final Bill List </jet-nav-link>
                </li>
                <li v-if="$page.props.role.bill_receipt">
                  <jet-nav-link :href="route('receipt.index')" :active="route().current('receipt.*')"> <i
                      class="fas fa-file-invoice-dollar opacity-75 mr-2 text-sm w-6"></i> Bill Receipt </jet-nav-link>
                </li>
              </ul>
            </TransitionRoot>
          </span>
          <span
            v-if="$page.props.role.incident_report || $page.props.role.bill_report || $page.props.role.radius_report">
            <hr class="my-4 md:min-w-full" />

            <h6 @click="toggleMenu('report')"
              class="mb-2 text-gray-600 hover:gray-blue-500 text-xs uppercase py-3 font-bold block px-4 rounded-md bg-blueGray-100 shadow-sm cursor-pointer flex justify-between z-10">
              <label class="flex"> Report Panel </label> <i class="fa fas text-right flex"
                :class="{ 'fa-caret-right': !report, 'fa-caret-down': report }"></i>
            </h6>
            <TransitionRoot :show="report" enter="transition ease-in duration-300 transform" enter-from="opacity-0"
              leave="ease-out duration-150" enter-to="opacity-100" leave-from="opacity-100" leave-to="opacity-0">
              <ul class="md:flex-col md:min-w-full flex flex-col list-none md:mb-4">
                <li v-if="$page.props.role.incident_report">
                  <jet-nav-link :href="route('incidentReport')" :active="route().current('incidentReport')"> <i
                      class="fas fa-users opacity-75 mr-2 text-sm w-6"></i> Incident Report </jet-nav-link>
                </li>
                <li v-if="$page.props.role.bill_report">
                  <jet-nav-link :href="route('dailyreceipt')" :active="route().current('dailyreceipt')"> <i
                      class="fa fa-money-bill opacity-75 mr-2 text-sm w-6"></i> Bill Report </jet-nav-link>
                </li>
                <li v-if="$page.props.role.radius_report">
                  <jet-nav-link :href="route('showRadius')" :active="route().current('showRadius')"> <i
                      class="fas fa-server opacity-75 mr-2 text-sm w-6"></i> Radius User Report </jet-nav-link>
                </li>
                <li v-if="$page.props.role.ip_report">
                  <jet-nav-link :href="route('publicIpReport')" :active="route().current('publicIpReport')"> <i
                      class="fas fa-server opacity-75 mr-2 text-sm w-6"></i> IP Usages Report </jet-nav-link>
                </li>

              </ul>
            </TransitionRoot>
          </span>
        </div>
      </div>
    </nav>

    <div
      class="relative md:ml-64 bg-blueGray-50 scrollbar-thin scrollbar-thumb-blue-900 scrollbar-track-gray-200 block overflow-auto h-screen">
      <!--Body Start -->
      <!--Body Start -->
      <!-- Page Heading -->
      <header class="relative w-full bg-blue-900 md:flex-row md:flex-nowrap md:justify-start flex items-center py-4"
        v-if="$slots.header">
        <div class="px-4 md:px-10 mx-auto w-full my-4">
          <slot name="header"> </slot>
        </div>
        <div class="ml-3 absolute top-8 right-8">
          <jet-dropdown align="right" width="48">
            <template #trigger>
              <button v-if="$page.props.jetstream.managesProfilePhotos"
                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.user.profile_photo_url"
                  :alt="$page.props.user.name" />
              </button>

              <span v-else class="inline-flex rounded-md">
                <button type="button"
                  class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                  {{ $page.props.user.name }}

                  <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd" />
                  </svg>
                </button>
              </span>
            </template>

            <template #content>
              <!-- Account Management -->
              <div class="block px-4 py-2 text-xs text-gray-400">Manage Account</div>

              <jet-dropdown-link :href="route('profile.show')"> Profile </jet-dropdown-link>

              <jet-dropdown-link :href="route('api-tokens.index')" v-if="$page.props.jetstream.hasApiFeatures"> API
                Tokens </jet-dropdown-link>

              <div class="border-t border-gray-100"></div>

              <!-- Authentication -->
              <form @submit.prevent="logout">
                <jet-dropdown-link as="button"> Log Out </jet-dropdown-link>
              </form>
            </template>
          </jet-dropdown>
        </div>
      </header>

      <!-- Page Content -->

      <main>
        <slot></slot>
      </main>

      <footer class="block py-4">
        <div class="container mx-auto px-4">
          <hr class="mb-4 border-b-1 border-blueGray-200" />
          <div class="flex flex-wrap items-center md:justify-between justify-center">
            <div class="w-full md:w-4/12 px-4">
              <div class="text-sm text-blueGray-500 font-semibold py-1">
                Copyright © <span id="javascript-date"></span>

                {{ new Date().getFullYear() }}
              </div>
            </div>
            <div class="w-full md:w-8/12 px-4">
              <ul class="flex flex-wrap list-none md:justify-end justify-center">
                <li class="text-sm text-blueGray-500 font-semibold py-1">
                  <span class="text-lm-orange ">
                    Long Mao
                  </span>
                  <span class="text-lm-gray">
                    Network
                  </span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
</template>

<script>
import JetApplicationMark from "@/Jetstream/ApplicationMark";
import JetBanner from "@/Jetstream/Banner";
import JetDropdown from "@/Jetstream/Dropdown";
import JetDropdownLink from "@/Jetstream/DropdownLink";
import JetNavLink from "@/Jetstream/NavLink";
import JetResponsiveNavLink from "@/Jetstream/ResponsiveNavLink";
import { TransitionRoot, TransitionChild } from "@headlessui/vue";
export default {
  components: {
    TransitionRoot,
    TransitionChild,
    JetApplicationMark,
    JetBanner,
    JetDropdown,
    JetDropdownLink,
    JetNavLink,
    JetResponsiveNavLink,
  },

  data() {
    return {
      showingNavigationDropdown: true,
      billing: false,
      admin: false,
      user: false,
      report: false,
    };
  },

  methods: {
    switchToTeam(team) {
      this.$inertia.put(
        route("current-team.update"),
        {
          team_id: team.id,
        },
        {
          preserveState: false,
        }
      );
    },
    toggleMenu(menu) {
      if (menu == 'billing') {
        this.billing = (this.billing) ? false : true;
        this.user = false;
        this.admin = false;
        this.report = false;
      }
      if (menu == 'user') {
        this.billing = false;
        this.admin = false;
        this.user = (this.user) ? false : true;
        this.report = false;
      }
      if (menu == 'admin') {
        this.billing = false;
        this.user = false;
        this.admin = (this.admin) ? false : true;
        this.report = false;
      }
      if (menu == 'report') {
        this.billing = false;
        this.user = false;
        this.admin = false;
        this.report = (this.report) ? false : true;
      }


    },
    logout() {
      this.$inertia.post(route("logout"));
    },
  },
  created: function () {
    if (route().current('receipt.index') || route().current('billGenerator') || route().current('tempBilling') || route().current('showbill')) {
      this.billing = true;
      this.user = false;
      this.admin = false;
      this.report = false;
    }
    else if (route().current('customer.*') || route().current('servicerequest.*')) {
      this.billing = false;
      this.user = true;
      this.admin = false;
      this.report = false;
    }
    else if (route().current('user.*') || route().current('role.*') || route().current('township.*') || route().current('city.*') || route().current('equiptment.*') || route().current('sla.*') || route().current('package.*') || route().current('project.*') || route().current('status.*') || route().current('template.*') || route().current('pop.*') || route().current('port.*') || route().current('snport.*') || route().current('announcement.*') || route().current('smsgateway.*') || route().current('radiusconfig.*') || route().current('billconfig.*')) {
      this.billing = false;
      this.user = false;
      this.admin = true;
      this.report = false;
    }
    else if (route().current('incidentReport') || route().current('dailyreceipt.*') || route().current('showRadius') || route().current('publicIpReport')) {
      this.billing = false;
      this.user = false;
      this.admin = false;
      this.report = true;
    }
  },
};
</script>
