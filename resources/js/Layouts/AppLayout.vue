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
            <div class="form-control hidden md:block relative">
                <form @submit.prevent="goSearch" class="join">
                    <input 
                        v-model="search" 
                        @input="onSearchInput"
                        @focus="search.length > 1 && fetchSuggestions()"
                                                 @blur="() => setTimeout(() => showSuggestions = false, 200)"
                        type="text" 
                        placeholder="Buscar en todo el sitio..." 
                        class="join-item input input-bordered w-48 md:w-auto" 
                    />
                    <button type="submit" class="join-item btn btn-primary">
                        <MagnifyingGlassIcon class="h-5 w-5" />
                    </button>
                </form>
                
                <!-- Search Suggestions Dropdown -->
                <div v-if="showSuggestions && searchSuggestions.length > 0" 
                     class="absolute top-12 left-0 right-0 bg-base-100 border border-base-300 rounded-lg shadow-xl z-50 max-h-60 overflow-y-auto">
                    <div v-for="(suggestion, index) in searchSuggestions" :key="index" 
                         @click="search = suggestion.nombre_doc || suggestion.nombre_carrera || suggestion.titulo || suggestion.name; goSearch()"
                         class="px-4 py-2 hover:bg-base-200 cursor-pointer border-b border-base-300 last:border-b-0">
                        <div class="text-sm font-medium">{{ suggestion.nombre_doc || suggestion.nombre_carrera || suggestion.titulo || suggestion.name }}</div>
                        <div class="text-xs text-base-content/70">
                            {{ suggestion.cod_doc ? 'Docente' : suggestion.cod_carrera ? 'Programa' : suggestion.email ? 'Usuario' : 'Tesis' }}
                        </div>
                    </div>
                </div>
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
        <transition-group name="flash" tag="div" class="space-y-2">
          <div v-if="flash.success && showFlashSuccess" key="success" role="alert" class="alert alert-success shadow-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              <span>{{ flash.success }}</span>
              <button @click="showFlashSuccess = false" class="btn btn-sm btn-ghost text-success-content">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
          </div>
          <div v-if="flash.error && showFlashError" key="error" role="alert" class="alert alert-error shadow-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              <span>{{ flash.error }}</span>
              <button @click="showFlashError = false" class="btn btn-sm btn-ghost text-error-content">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
          </div>
          <div v-if="flash.warning && showFlashWarning" key="warning" role="alert" class="alert alert-warning shadow-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
              <span>{{ flash.warning }}</span>
              <button @click="showFlashWarning = false" class="btn btn-sm btn-ghost text-warning-content">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
          </div>
          <div v-if="flash.info && showFlashInfo" key="info" role="alert" class="alert alert-info shadow-lg">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              <span>{{ flash.info }}</span>
              <button @click="showFlashInfo = false" class="btn btn-sm btn-ghost text-info-content">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
          </div>
        </transition-group>
      </div>

      <!-- Main Content -->
      <main class="flex-1 p-6">
        <slot />
      </main>

      <!-- Footer -->
      <footer class="footer footer-center p-4 bg-base-300 text-base-content">
        <aside>
          <div class="grid grid-flow-col gap-4">
            <p>© {{ new Date().getFullYear() }} {{ appName }} - {{ system?.universidad || 'UAGRM' }}</p>
            <div class="divider divider-horizontal"></div>
            <p>{{ system?.direccion || 'Dirección de Postgrado' }}</p>
          </div>
          <div class="flex gap-4 text-sm opacity-70">
            <span>Visitas a esta página: <strong>{{ visitas }}</strong></span>
            <span v-if="user">•</span>
            <span v-if="user">Usuario: {{ user.name }}</span>
            <span v-if="system?.version">• Versión {{ system.version }}</span>
          </div>
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
import { computed, ref, onMounted, watch } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import {
  Bars3Icon,
  MagnifyingGlassIcon,
  SunIcon,
  MoonIcon,
  ComputerDesktopIcon,
  UserCircleIcon,
  ChevronDownIcon,
  HeartIcon,
  SparklesIcon
} from '@heroicons/vue/24/outline';


const page = usePage();

// Computed properties for Inertia props
const menu = computed(() => page.props.menu || []);
const user = computed(() => page.props.auth?.user || null);
const flash = computed(() => page.props.flash || {});
const visitas = computed(() => page.props.visitas_pagina || 0);
const appName = computed(() => page.props.app?.name || page.props.appName || 'Postgrado');
const system = computed(() => page.props.system || null);

// Search functionality
const search = ref('');
const searchSuggestions = ref([]);
const showSuggestions = ref(false);

// Flash messages control
const showFlashSuccess = ref(true);
const showFlashError = ref(true);
const showFlashWarning = ref(true);
const showFlashInfo = ref(true);

// Watch flash messages to reset visibility when they change
watch(() => flash.value.success, (newVal) => {
  if (newVal) {
    showFlashSuccess.value = true;
    setTimeout(() => showFlashSuccess.value = false, 5000);
  }
});

watch(() => flash.value.error, (newVal) => {
  if (newVal) {
    showFlashError.value = true;
    setTimeout(() => showFlashError.value = false, 8000);
  }
});

watch(() => flash.value.warning, (newVal) => {
  if (newVal) {
    showFlashWarning.value = true;
    setTimeout(() => showFlashWarning.value = false, 6000);
  }
});

watch(() => flash.value.info, (newVal) => {
  if (newVal) {
    showFlashInfo.value = true;
    setTimeout(() => showFlashInfo.value = false, 5000);
  }
});

// Función para obtener sugerencias de búsqueda
const fetchSuggestions = async () => {
  if (search.value.length < 2) {
    searchSuggestions.value = [];
    showSuggestions.value = false;
    return;
  }
  
     try {
     const response = await fetch(`/api/search?q=${encodeURIComponent(search.value)}&suggest=true`);
     if (!response.ok) {
       throw new Error(`HTTP error! status: ${response.status}`);
     }
     const data = await response.json();
     searchSuggestions.value = Object.values(data).flat().slice(0, 5);
     showSuggestions.value = true;
   } catch (error) {
     console.error('Error fetching suggestions:', error);
     searchSuggestions.value = [];
     showSuggestions.value = false;
   }
};

// Debounce para evitar demasiadas peticiones
let searchTimeout;
const onSearchInput = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(fetchSuggestions, 300);
};

function goSearch() {
  if (search.value.trim().length > 1) {
    showSuggestions.value = false;
    router.get(route('search.index'), { q: search.value });
  }
}

// Theme logic
const themes = [
    { name: 'light', label: 'Modo Día', icon: SunIcon },
    { name: 'dracula', label: 'Modo Noche', icon: MoonIcon },
    { name: 'cupcake', label: 'Tema Niños (Día)', icon: SparklesIcon },
    // Temas adicionales
    { name: 'forest', label: 'Bosque (Noche)', icon: MoonIcon },
    { name: 'synthwave', label: 'Synthwave (Noche)', icon: SparklesIcon },
    { name: 'valentine', label: 'San Valentín (Día)', icon: HeartIcon },
    { name: 'cyberpunk', label: 'Cyberpunk', icon: ComputerDesktopIcon },
    { name: 'aqua', label: 'Aqua (Día)', icon: SunIcon },
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
        // Por defecto, modo noche entre 7pm y 6am
        if (hour >= 19 || hour < 6) {
            applyTheme('dracula'); // Cargar Modo Noche por defecto
        } else {
            applyTheme('light'); // Cargar Modo Día por defecto
        }
    }
    // Set initial font size
    document.documentElement.style.fontSize = localStorage.getItem('fontSize') || '16px';
    
    // Auto-hide flash messages
    if (flash.value.success) {
        setTimeout(() => showFlashSuccess.value = false, 5000);
    }
    if (flash.value.error) {
        setTimeout(() => showFlashError.value = false, 8000);
    }
    if (flash.value.warning) {
        setTimeout(() => showFlashWarning.value = false, 6000);
    }
    if (flash.value.info) {
        setTimeout(() => showFlashInfo.value = false, 5000);
    }
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
/* Flash messages transitions */
.flash-enter-active, .flash-leave-active {
  transition: all 0.3s ease;
}
.flash-enter-from {
  opacity: 0;
  transform: translateX(100%);
}
.flash-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
.flash-move {
  transition: transform 0.3s ease;
}
</style> 