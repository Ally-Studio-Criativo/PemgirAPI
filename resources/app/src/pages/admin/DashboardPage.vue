<template>
  <q-page class="q-pa-md">
    <div class="q-mb-xs">
      <div class="text-h4 text-weight-bold">Dashboard</div>
      <div class="text-body2 text-grey-7">Visão geral do sistema</div>
    </div>

    <div class="row q-col-gutter-md q-mt-sm">
      <!-- Card Usuários -->
      <div class="col-12 col-sm-6 col-md-3">
        <q-card bordered flat class="dashboard-card">
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6 text-weight-bold">{{ stats.users }}</div>
                <div class="text-grey-7">Usuários</div>
              </div>
              <div class="col-auto">
                <q-icon name="people" size="48px" color="primary" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Card Categorias -->
      <div class="col-12 col-sm-6 col-md-3">
        <q-card bordered flat class="dashboard-card">
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6 text-weight-bold">{{ stats.categories }}</div>
                <div class="text-grey-7">Categorias</div>
              </div>
              <div class="col-auto">
                <q-icon name="category" size="48px" color="orange" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Card Produtos -->
      <div class="col-12 col-sm-6 col-md-3">
        <q-card bordered flat class="dashboard-card">
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6 text-weight-bold">{{ stats.products }}</div>
                <div class="text-grey-7">Produtos</div>
              </div>
              <div class="col-auto">
                <q-icon name="inventory_2" size="48px" color="green" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Card Cores -->
      <div class="col-12 col-sm-6 col-md-3">
        <q-card bordered flat class="dashboard-card">
          <q-card-section>
            <div class="row items-center">
              <div class="col">
                <div class="text-h6 text-weight-bold">{{ stats.colors }}</div>
                <div class="text-grey-7">Cores</div>
              </div>
              <div class="col-auto">
                <q-icon name="palette" size="48px" color="purple" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Welcome Message -->
    <q-card class="q-mt-md" flat>
      <div class="text-h6 text-weight-bold q-mb-sm">Bem-vindo ao Painel Administrativo</div>
      <div class="text-body2 text-grey-7">
        Use o menu lateral para navegar entre as diferentes seções do sistema.
      </div>
    </q-card>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { userService } from 'src/services/userService'
import { categoryService } from 'src/services/categoryService'
import { productService } from 'src/services/productService'
import { colorService } from 'src/services/colorService'

export default defineComponent({
  name: 'DashboardPage',

  setup() {
    const stats = ref({
      users: 0,
      categories: 0,
      products: 0,
      colors: 0
    })

    const loadStats = async () => {
      try {
        // Carregar todas as estatísticas em paralelo
        const [users, categories, products, colors] = await Promise.all([
          userService.getAll(),
          categoryService.getAll(),
          productService.getAll(),
          colorService.getAll()
        ])

        stats.value = {
          users: users.length,
          categories: categories.length,
          products: products.length,
          colors: colors.length
        }
      } catch (error) {
        console.error('Erro ao carregar estatísticas:', error)
      }
    }

    onMounted(() => {
      loadStats()
    })

    return {
      stats
    }
  }
})
</script>

<style scoped>
.dashboard-card {
  border-left: 4px solid currentColor;
}
</style>
