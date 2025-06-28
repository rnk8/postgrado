<template>
  <div class="drawer lg:drawer-open">
    <input id="my-drawer-2" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content flex flex-col bg-base-100">
      <!-- Navbar -->
      <nav class="navbar bg-base-200 shadow-md sticky top-0 z-30">
        <div class="navbar-start">
            <label for="my-drawer-2" class="btn btn-ghost lg:hidden">
                <Bars3Icon class="h-6 w-6"/>
            </label>
        </div>
        <div class="navbar-center">
            <!-- Global Search -->
            <div class="form-control hidden md:block">
                <form @submit.prevent="goSearch" class="join">
                    <input v-model="search" type="text" placeholder="Buscar en todo el sitio..." class="join-item input input-bordered w-48 md:w-auto" />
                    <button type="submit" class="join-item btn btn-primary">
                        <MagnifyingGlassIcon class="h-5 w-5" />
                    </button>
                </form>
            </div>
        </div>
        <div class="navbar-end gap-2">
            <!-- Theme Selector -->
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost">
                    <SunIcon class="h-5 w-5"/>
                    <ChevronDownIcon class="h-4 w-4"/>
                </div>
                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-300 rounded-box w-52">
                    <li v-for="theme in themes" :key="theme.name">
                        <a @click.prevent="applyTheme(theme.name)" :class="{ 'active': activeTheme === theme.name }">
                            <component :is="theme.icon" class="h-4 w-4"/> {{ theme.label }}
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Accessibility Controls -->
             <div class="hidden lg:flex items-center">
                <button @click="changeFontSize('decrease')" class="btn btn-ghost btn-sm">A-</button>
                <button @click="changeFontSize('increase')" class="btn btn-ghost btn-sm">A+</button>
            </div>
            <!-- User Profile Dropdown -->
            <div class="dropdown dropdown-end" v-if="user">
                <div tabindex="0" role="button" class="btn btn-ghost">
                     <UserCircleIcon class="h-6 w-6"/>
                     <span class="hidden md:inline">{{ user.name }}</span>
                </div>
                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-300 rounded-box w-52">
                    <li><Link href="/profile">Mi Perfil</Link></li>
                    <li><Link href="/logout" method="post" as="button" class="w-full text-left">Cerrar Sesión</Link></li>
                </ul>
            </div>
             <Link v-else href="/login" class="btn btn-ghost">Iniciar Sesión</Link>
        </div>
      </nav>

       <!-- Page Heading -->
        <header v-if="$slots.title" class="bg-base-100 shadow">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                 <h1 class="font-semibold text-xl text-base-content">
                    <slot name="title" />
                </h1>
            </div>
        </header>

      <!-- Flash Messages -->
      <div class="fixed top-20 right-6 z-50 space-y-2">
        <div v-if="flash.success" role="alert" class="alert alert-success shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ flash.success }}</span>
        </div>
        <div v-if="flash.error" role="alert" class="alert alert-error shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ flash.error }}</span>
        </div>
        <div v-if="flash.warning" role="alert" class="alert alert-warning shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            <span>{{ flash.warning }}</span>
        </div>
        <div v-if="flash.info" role="alert" class="alert alert-info shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ flash.info }}</span>
        </div>
      </div>

      <!-- Main Content -->
      <main class="flex-1 p-6">
        <slot />
      </main>

      <!-- Footer -->
      <footer class="footer footer-center p-4 bg-base-300 text-base-content">
        <aside>
          <p>Copyright © {{ new Date().getFullYear() }} - Todos los derechos reservados por {{ appName }}</p>
          <p>Visitas a esta página: {{ visitas }}</p>
        </aside>
      </footer>
    </div>
    <!-- Sidebar / Drawer Side -->
    <div class="drawer-side">
      <label for="my-drawer-2" aria-label="close sidebar" class="drawer-overlay"></label>
      <ul class="menu p-4 w-64 min-h-full bg-base-200 text-base-content">
         <li class="font-bold text-lg p-4 text-center">
            <Link href="/dashboard">{{ appName }}</Link>
        </li>
        <!-- Dynamic Menu -->
        <template v-for="item in menu" :key="item.id">
            <li v-if="!item.children || item.children.length === 0">
                <Link :href="route(item.ruta)" :class="{ 'active': route().current(item.ruta) }">
                    {{ item.titulo }}
                </Link>
            </li>
            <li v-else>
                <details>
                    <summary>{{ item.titulo }}</summary>
                    <ul>
                        <li v-for="child in item.children" :key="child.id">
                           <Link :href="route(child.ruta)" :class="{ 'active': route().current(child.ruta) }">
                                {{ child.titulo }}
                           </Link>
                        </li>
                    </ul>
                </details>
            </li>
        </template>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import {
  Bars3Icon,
  MagnifyingGlassIcon,
  SunIcon,
  MoonIcon,
  ComputerDesktopIcon,
  UserCircleIcon,
  ArrowRightOnRectangleIcon,
  ChevronDownIcon
} from '@heroicons/vue/24/outline';


const page = usePage();

// Computed properties for Inertia props
const menu = computed(() => page.props.menu || []);
const user = computed(() => page.props.auth?.user || null);
const flash = computed(() => page.props.flash || {});
const visitas = computed(() => page.props.visitas_pagina || 0);
const appName = computed(() => page.props.app?.name || page.props.appName || 'Postgrado');

// Search functionality
const search = ref('');
function goSearch() {
  if (search.value.trim().length > 1) {
    router.get(route('search.index'), { q: search.value });
  }
}

// Theme logic
const themes = [
    { name: 'light', label: 'Jóvenes (Día)', icon: SunIcon },
    { name: 'cupcake', label: 'Niños', icon: SunIcon },
    { name: 'dracula', label: 'Adultos (Noche)', icon: MoonIcon }
];
const activeTheme = ref(localStorage.getItem('theme') || 'light');

function applyTheme(themeName) {
  activeTheme.value = themeName;
  document.documentElement.setAttribute('data-theme', themeName);
  localStorage.setItem('theme', themeName);
}

onMounted(() => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        applyTheme(savedTheme);
    } else {
        const hour = new Date().getHours();
        if (hour >= 19 || hour < 6) {
            applyTheme('dracula');
        } else {
            applyTheme('light');
        }
    }
    // Set initial font size
    document.documentElement.style.fontSize = localStorage.getItem('fontSize') || '16px';
});

// Accessibility Font Size
function changeFontSize(direction) {
    const currentSize = parseFloat(getComputedStyle(document.documentElement).fontSize);
    let newSize;
    if (direction === 'increase') {
        newSize = Math.min(currentSize + 2, 24); // max 24px
    } else {
        newSize = Math.max(currentSize - 2, 12); // min 12px
    }
    const newSizePx = `${newSize}px`;
    document.documentElement.style.fontSize = newSizePx;
    localStorage.setItem('fontSize', newSizePx);
}
</script>

<style scoped>
/***** Muy poco estilo para mantenerlo simple *****/
</style> 