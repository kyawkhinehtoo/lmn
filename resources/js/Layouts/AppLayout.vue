<template>
    <div id="root">
          <nav class="md:left-0 md:block md:fixed md:top-0 md:bottom-0 md:overflow-y-auto md:flex-row md:flex-nowrap md:overflow-hidden shadow-xl bg-white flex flex-wrap items-center justify-between relative md:w-64 z-10 py-4 px-6">
            <div class="md:flex-col md:items-stretch md:min-h-full md:flex-nowrap px-0 flex flex-wrap items-center justify-between w-full mx-auto">
                <button class="cursor-pointer text-black opacity-50 md:hidden px-3 py-1 text-xl leading-none bg-transparent rounded border border-solid border-transparent"
                    type="button" onclick="toggleNavbar('example-collapse-sidebar')">
                    <i class="fas fa-bars"></i></button>
                <a class="md:block text-left md:pb-2 text-blueGray-600 mr-0 inline-block whitespace-nowrap text-sm uppercase font-bold p-4 px-0"
                    href="javascript:void(0)">
                    Marga Global Telecom
                </a>
                <ul class="md:hidden items-center flex flex-wrap list-none">
                    <li class="inline-block relative">
                        <a class="text-blueGray-500 block py-1 px-3" href="#pablo"
                            onclick="openDropdown(event,'notification-dropdown')"><i class="fas fa-bell"></i></a>
                        <div class="hidden bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg mt-1"
                            style="min-width: 12rem;" id="notification-dropdown">
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
                        <a class="text-blueGray-500 block" href="#pablo"
                            onclick="openDropdown(event,'user-responsive-dropdown')">
                            <div class="items-center flex">
                                <span
                                    class="w-12 h-12 text-sm text-white bg-blueGray-200 inline-flex items-center justify-center rounded-full"><img
                                        alt="..." class="w-full rounded-full align-middle border-none shadow-lg"
                                        src="" /></span>
                            </div>
                        </a>
                        <div class="hidden bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg mt-1"
                            style="min-width: 12rem;" id="user-responsive-dropdown">
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
                <div class="md:flex md:flex-col md:items-stretch md:opacity-100 md:relative md:mt-4 md:shadow-none shadow absolute top-0 left-0 right-0 z-40 overflow-y-auto overflow-x-hidden h-auto items-center flex-1 rounded hidden"
                    id="example-collapse-sidebar">
                    <div class="md:min-w-full md:hidden block pb-4 mb-4 border-b border-solid border-blueGray-200">
                        <div class="flex flex-wrap">
                            <div class="w-6/12">
                                <a class="md:block text-left md:pb-2 text-blueGray-600 mr-0 inline-block whitespace-nowrap text-sm uppercase font-bold p-4 px-0"
                                    href="javascript:void(0)">
                                    Tailwind Starter Kit Mobile <!--mobile -->
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
                                class="border-0 px-3 py-2 h-12 border border-solid  border-blueGray-500 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-base leading-snug shadow-none outline-none focus:outline-none w-full font-normal" />
                        </div>
                    </form>
                    <ul class="md:flex-col md:min-w-full flex flex-col list-none">
                        <li class="items-center">
                            <jet-nav-link :href="route('dashboard')" :active="route().current('dashboard')">
                                   <i class="fas fa-tv  opacity-75 mr-2 text-sm"></i> Dashboard
                                </jet-nav-link>
                        </li>
                        <li>
                              <jet-nav-link :href="route('township.index')" :active="route().current('township.index')">
                                   <i class="fas fa-newspaper  opacity-75 mr-2 text-sm"></i> Township
                                </jet-nav-link>
                        </li>
                        <li>
                              <jet-nav-link :href="route('package.index')" :active="route().current('package.index')">
                                   <i class="fas fa-newspaper  opacity-75 mr-2 text-sm"></i> Packages
                                </jet-nav-link>
                        </li>
                       
                    </ul>
                    <hr class="my-4 md:min-w-full" />
                    <h6 class="md:min-w-full text-blueGray-500 text-xs uppercase font-bold block pt-1 pb-4 no-underline">
                        Documentation
                    </h6>
                    <ul class="md:flex-col md:min-w-full flex flex-col list-none md:mb-4">
                        <li class="inline-flex">
                            <a class="text-blueGray-700 hover:text-blueGray-500 text-sm block mb-4 no-underline font-semibold"
                                href="#/documentation/styles"><i
                                    class="fas fa-paint-brush mr-2 text-blueGray-400 text-base"></i>
                                Styles</a>
                        </li>
                        <li class="inline-flex">
                            <a class="text-blueGray-700 hover:text-blueGray-500 text-sm block mb-4 no-underline font-semibold"
                                href="#/documentation/alerts"><i
                                    class="fab fa-css3-alt mr-2 text-blueGray-400 text-base"></i>
                                CSS Components</a>
                        </li>
                        <li class="inline-flex">
                            <a class="text-blueGray-700 hover:text-blueGray-500 text-sm block mb-4 no-underline font-semibold"
                                href="#/documentation/vue/alerts"><i
                                    class="fab fa-vuejs mr-2 text-blueGray-400 text-base"></i>
                                VueJS</a>
                        </li>
                        <li class="inline-flex">
                            <a class="text-blueGray-700 hover:text-blueGray-500  text-sm block mb-4 no-underline font-semibold"
                                href="#/documentation/react/alerts"><i
                                    class="fab fa-react mr-2 text-blueGray-400 text-base"></i>
                                React</a>
                        </li>
                        <li class="inline-flex">
                            <a class="text-blueGray-700 hover:text-blueGray-500  text-sm block mb-4 no-underline font-semibold"
                                href="#/documentation/angular/alerts"><i
                                    class="fab fa-angular mr-2 text-blueGray-400 text-base"></i>
                                Angular</a>
                        </li>
                        <li class="inline-flex">
                            <a class="text-blueGray-700 hover:text-blueGray-500  text-sm block mb-4 no-underline font-semibold"
                                href="#/documentation/javascript/alerts"><i
                                    class="fab fa-js-square mr-2 text-blueGray-400 text-base"></i>
                                Javascript</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
      
      <div class="relative md:ml-64 bg-blueGray-50"> <!--Body Start -->
              <!-- Page Heading -->
            <header class="relative w-full bg-blue-900 md:flex-row md:flex-nowrap md:justify-start flex items-center py-4" v-if="$slots.header">
                <div class="px-4 md:px-10 mx-auto w-full my-4">
                    <slot name="header">
                        
                    </slot>
                    
                </div>
                <div class="ml-3 absolute absolute top-8 right-8">
                                <jet-dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" />
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                                {{ $page.props.user.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Manage Account
                                        </div>

                                        <jet-dropdown-link :href="route('profile.show')">
                                            Profile
                                        </jet-dropdown-link>

                                        <jet-dropdown-link :href="route('api-tokens.index')" v-if="$page.props.jetstream.hasApiFeatures">
                                            API Tokens
                                        </jet-dropdown-link>

                                        <div class="border-t border-gray-100"></div>

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <jet-dropdown-link as="button">
                                                Log Out
                                            </jet-dropdown-link>
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
                                <ul class="flex flex-wrap list-none md:justify-end  justify-center">
                                   
                                    <li class="text-sm text-blueGray-500 font-semibold py-1">
                                        Marga Global Telecom
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
    import JetApplicationMark from '@/Jetstream/ApplicationMark'
    import JetBanner from '@/Jetstream/Banner'
    import JetDropdown from '@/Jetstream/Dropdown'
    import JetDropdownLink from '@/Jetstream/DropdownLink'
    import JetNavLink from '@/Jetstream/NavLink'
    import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'

    export default {
        components: {
            JetApplicationMark,
            JetBanner,
            JetDropdown,
            JetDropdownLink,
            JetNavLink,
            JetResponsiveNavLink,
        },

        data() {
            return {
                showingNavigationDropdown: false,
            }
        },

        methods: {
            switchToTeam(team) {
                this.$inertia.put(route('current-team.update'), {
                    'team_id': team.id
                }, {
                    preserveState: false
                })
            },

            logout() {
                this.$inertia.post(route('logout'));
            },
        }
    }
    
</script>
