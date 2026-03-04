<template>
  <q-layout view="hHh lpR fFf">
    <!-- Header -->
    <q-header elevated class="bg-primary text-white">
      <q-toolbar>
        <q-btn
          dense
          flat
          round
          icon="menu"
          @click="toggleLeftDrawer"
        />

        <q-toolbar-title>
          <div class="row items-center">
            <div class="text-h6 text-weight-bold">PEMGIR</div>
            <q-separator vertical inset class="q-mx-sm" />
            <div class="text-subtitle2">Painel Administrativo</div>
          </div>
        </q-toolbar-title>

        <!-- User Menu -->
        <div class="q-mr-sm text-body2">{{ authStore.user?.name }}</div>
        <q-btn-dropdown flat rounded dense icon="account_circle">
          <q-list>
            <q-item clickable v-close-popup @click="handleLogout">
              <q-item-section avatar>
                <q-icon name="logout" color="negative" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Sair</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
      </q-toolbar>
    </q-header>

    <!-- Drawer / Sidebar -->
    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      :width="280"
      :breakpoint="500"
      class="bg-grey-2"
    >
      <q-scroll-area class="fit">
        <q-list padding>
          <!-- Dashboard -->
          <q-item
            clickable
            v-ripple
            :active="$route.path === '/admin'"
            active-class="text-primary bg-blue-1"
            @click="$router.push('/admin')"
          >
            <q-item-section avatar>
              <q-icon name="dashboard" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Dashboard</q-item-label>
            </q-item-section>
          </q-item>

          <q-separator class="q-my-md" />

          <!-- Usuários -->
          <q-item
            clickable
            v-ripple
            :active="$route.path.startsWith('/admin/usuarios')"
            active-class="text-primary bg-blue-1"
            @click="$router.push('/admin/usuarios')"
          >
            <q-item-section avatar>
              <q-icon name="people" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Usuários</q-item-label>
            </q-item-section>
          </q-item>

          <!-- Categorias -->
          <q-item
            clickable
            v-ripple
            :active="$route.path.startsWith('/admin/categorias')"
            active-class="text-primary bg-blue-1"
            @click="$router.push('/admin/categorias')"
          >
            <q-item-section avatar>
              <q-icon name="category" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Categorias</q-item-label>
            </q-item-section>
          </q-item>

          <!-- Produtos -->
          <q-item
            clickable
            v-ripple
            :active="$route.path.startsWith('/admin/produtos')"
            active-class="text-primary bg-blue-1"
            @click="$router.push('/admin/produtos')"
          >
            <q-item-section avatar>
              <q-icon name="inventory_2" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Produtos</q-item-label>
            </q-item-section>
          </q-item>

          <!-- Cores -->
          <q-item
            clickable
            v-ripple
            :active="$route.path.startsWith('/admin/cores')"
            active-class="text-primary bg-blue-1"
            @click="$router.push('/admin/cores')"
          >
            <q-item-section avatar>
              <q-icon name="palette" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Cores</q-item-label>
            </q-item-section>
          </q-item>

          <!-- Landing Page -->
          <q-item
            clickable
            v-ripple
            :active="$route.path.startsWith('/admin/campanhas')"
            active-class="text-primary bg-blue-1"
            @click="$router.push('/admin/campanhas')"
          >
            <q-item-section avatar>
              <q-icon name="web" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Landing Page</q-item-label>
            </q-item-section>
          </q-item>

          <q-separator class="q-my-md" />

          <!-- Voltar ao Site -->
          <q-item
            clickable
            v-ripple
            @click="$router.push('/')"
          >
            <q-item-section avatar>
              <q-icon name="arrow_back" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Voltar ao Site</q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </q-scroll-area>
    </q-drawer>

    <!-- Page Content -->
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { defineComponent, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useAuthStore } from 'src/stores/auth-store'

export default defineComponent({
  name: 'AdminLayout',

  setup() {
    const $router = useRouter()
    const $q = useQuasar()
    const authStore = useAuthStore()
    const leftDrawerOpen = ref(false)

    const toggleLeftDrawer = () => {
      leftDrawerOpen.value = !leftDrawerOpen.value
    }

    const handleLogout = async () => {
      $q.dialog({
        title: 'Confirmar Saída',
        message: 'Deseja realmente sair do sistema?',
        cancel: {
          label: 'Cancelar',
          flat: true
        },
        ok: {
          label: 'Sair',
          color: 'negative'
        },
        persistent: true
      }).onOk(async () => {
        await authStore.logout()
        $q.notify({
          type: 'positive',
          message: 'Logout realizado com sucesso',
          position: 'top'
        })
        $router.push('/acessar')
      })
    }

    return {
      leftDrawerOpen,
      toggleLeftDrawer,
      handleLogout,
      authStore
    }
  }
})
</script>

<style scoped>
.q-item {
  border-radius: 8px;
  margin: 4px 8px;
}

.q-item:hover {
  background-color: rgba(0, 0, 0, 0.05);
}
</style>
