<template>
  <q-page class="q-pa-md">
    <div class="row items-center justify-between q-mb-xs">
      <div>
        <div class="text-h5 text-weight-bold">Categorias</div>
        <div class="text-body2 text-grey-7">Gerencie as categorias de produtos</div>
      </div>
      <q-btn
        color="primary"
        icon="add"
        label="Nova Categoria"
        @click="openDialog(null)"
      />
    </div>

    <!-- Table -->
    <q-table
      :rows="categories"
      :columns="columns"
      row-key="id"
      :loading="loading"
      flat
      class="q-mt-md clickable-rows"
    >
      <template v-slot:body="props">
        <q-tr :props="props" @click="openDialog(props.row)" class="cursor-pointer">
          <q-td v-for="col in props.cols" :key="col.name" :props="props">
            {{ col.value }}
          </q-td>
        </q-tr>
      </template>
    </q-table>

    <!-- Dialog Form -->
    <q-dialog v-model="showDialog" persistent>
      <q-card style="min-width: 500px; max-width: 600px">
        <!-- Header -->
        <q-card-section class="q-pb-sm">
          <div class="row items-start justify-between">
            <div>
              <div class="text-h5 text-weight-medium q-mb-xs">
                {{ isEdit ? 'Editar Categoria' : 'Criar Nova Categoria' }}
              </div>
              <div class="text-body2 text-grey-7">
                {{ isEdit ? 'Atualize as informações da categoria abaixo' : 'Preencha os dados para criar uma nova categoria' }}
              </div>
            </div>
            <q-btn
              v-if="isEdit"
              flat
              round
              dense
              icon="delete"
              color="negative"
              @click="confirmDelete(form)"
            >
              <q-tooltip>Excluir categoria</q-tooltip>
            </q-btn>
          </div>
        </q-card-section>

        <q-separator />

        <!-- Form -->
        <q-card-section class="q-pt-lg q-px-lg q-pb-md">
          <q-form @submit="saveCategory">
            <div class="row q-col-gutter-md">
              <!-- Nome -->
              <div class="col-12">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Nome da categoria</div>
                <q-input
                  v-model="form.name"
                  dense
                  placeholder="Digite o nome da categoria"
                  filled
                  hide-bottom-space
                  :rules="[val => !!val || 'Nome é obrigatório']"
                >
                  <template v-slot:prepend>
                    <q-icon name="category" color="grey-6" />
                  </template>
                </q-input>
              </div>

              <!-- Produto em Destaque -->
              <div class="col-12">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Produto em destaque</div>
                <div class="text-caption text-grey-6 q-mb-sm">
                  Produto que representa a categoria na landing page
                </div>
                <q-select
                  v-model="form.featured_product_id"
                  :options="categoryProducts"
                  option-value="id"
                  option-label="name"
                  emit-value
                  map-options
                  dense
                  placeholder="Selecione um produto"
                  filled
                  hide-bottom-space
                  clearable
                  :disable="!isEdit || categoryProducts.length === 0"
                >
                  <template v-slot:prepend>
                    <q-icon name="star" color="grey-6" />
                  </template>
                  <template v-slot:no-option>
                    <q-item>
                      <q-item-section class="text-grey">
                        {{ isEdit ? 'Nenhum produto nesta categoria' : 'Salve a categoria primeiro para adicionar produtos' }}
                      </q-item-section>
                    </q-item>
                  </template>
                </q-select>
              </div>

              <!-- Path (URL) -->
              <div class="col-12">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">URL da categoria</div>
                <div class="text-caption text-grey-6 q-mb-sm">
                  URL para acessar os produtos desta categoria
                </div>
                <q-input
                  v-model="form.path"
                  dense
                  placeholder="Ex: /produtos?categoria=COTTONS"
                  filled
                  hide-bottom-space
                >
                  <template v-slot:prepend>
                    <q-icon name="link" color="grey-6" />
                  </template>
                </q-input>
              </div>

              <!-- Ordem -->
              <div class="col-12">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Ordem de exibição</div>
                <div class="text-caption text-grey-6 q-mb-sm">
                  Define a ordem de apresentação da categoria
                </div>
                <q-input
                  v-model.number="form.order"
                  type="number"
                  dense
                  placeholder="0"
                  filled
                  hide-bottom-space
                >
                  <template v-slot:prepend>
                    <q-icon name="sort" color="grey-6" />
                  </template>
                </q-input>
              </div>
            </div>
          </q-form>
        </q-card-section>

        <q-separator />

        <!-- Actions -->
        <q-card-actions align="right" class="q-px-lg q-py-md">
          <q-btn
            label="Cancelar"
            color="grey-7"
            flat
            padding="sm lg"
            @click="showDialog = false"
          />
          <q-btn
            label="Salvar"
            color="primary"
            unelevated
            padding="sm lg"
            :loading="saving"
            @click="saveCategory"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { categoryService } from 'src/services/categoryService'
import { productService } from 'src/services/productService'

export default defineComponent({
  name: 'CategoriesPage',

  setup() {
    const $q = useQuasar()

    const categories = ref([])
    const loading = ref(false)
    const saving = ref(false)
    const showDialog = ref(false)
    const isEdit = ref(false)

    const form = ref({
      id: null,
      name: '',
      featured_product_id: null,
      path: '',
      order: 0
    })

    const categoryProducts = ref([])

    const columns = [
      {
        name: 'id',
        label: 'ID',
        field: 'id',
        align: 'left',
        sortable: true
      },
      {
        name: 'name',
        label: 'Nome',
        field: 'name',
        align: 'left',
        sortable: true
      },
      {
        name: 'order',
        label: 'Ordem',
        field: 'order',
        align: 'center',
        sortable: true
      }
    ]

    const openDialog = async (category) => {
      if (category) {
        isEdit.value = true
        form.value = { ...category }
        // Carregar produtos da categoria
        await loadCategoryProducts(category.id)
      } else {
        isEdit.value = false
        form.value = {
          id: null,
          name: '',
          featured_product_id: null,
          path: '',
          order: 0
        }
        categoryProducts.value = []
      }
      showDialog.value = true
    }

    const loadCategoryProducts = async (categoryId) => {
      try {
        const allProducts = await productService.getAll()
        categoryProducts.value = allProducts.filter(p => p.category_id === categoryId)
      } catch (error) {
        console.error('Erro ao carregar produtos da categoria:', error)
        categoryProducts.value = []
      }
    }

    const saveCategory = async () => {
      saving.value = true

      try {
        const categoryData = {
          name: form.value.name,
          order: form.value.order,
          featured_product_id: form.value.featured_product_id,
          path: form.value.path
        }

        if (isEdit.value) {
          await categoryService.update(form.value.id, categoryData)
        } else {
          await categoryService.create(categoryData)
        }

        $q.notify({
          type: 'positive',
          message: `Categoria ${isEdit.value ? 'atualizada' : 'criada'} com sucesso!`,
          position: 'top'
        })

        showDialog.value = false
        await loadCategories()
      } catch (error) {
        const errorMessage = error.response?.data?.message ||
                           error.response?.data?.errors?.name?.[0] ||
                           'Erro ao salvar categoria'

        $q.notify({
          type: 'negative',
          message: errorMessage,
          position: 'top'
        })
      } finally {
        saving.value = false
      }
    }

    const confirmDelete = (category) => {
      $q.dialog({
        title: 'Confirmar Exclusão',
        message: `Deseja realmente excluir a categoria "${category.name}"?`,
        cancel: {
          label: 'Cancelar',
          flat: true
        },
        ok: {
          label: 'Excluir',
          color: 'negative'
        },
        persistent: true
      }).onOk(async () => {
        await deleteCategory(category.id)
      })
    }

    const deleteCategory = async (categoryId) => {
      try {
        await categoryService.delete(categoryId)

        $q.notify({
          type: 'positive',
          message: 'Categoria excluída com sucesso!',
          position: 'top'
        })

        showDialog.value = false
        await loadCategories()
      } catch {
        $q.notify({
          type: 'negative',
          message: 'Erro ao excluir categoria',
          position: 'top'
        })
      }
    }

    const loadCategories = async () => {
      loading.value = true

      try {
        categories.value = await categoryService.getAll()
      } catch {
        $q.notify({
          type: 'negative',
          message: 'Erro ao carregar categorias',
          position: 'top'
        })
      } finally {
        loading.value = false
      }
    }

    // Carregar dados ao montar
    onMounted(() => {
      loadCategories()
    })

    return {
      categories,
      columns,
      loading,
      saving,
      showDialog,
      isEdit,
      form,
      categoryProducts,
      openDialog,
      saveCategory,
      confirmDelete
    }
  }
})
</script>

<style scoped>
.clickable-rows :deep(tbody tr) {
  transition: background-color 0.2s;
}

.clickable-rows :deep(tbody tr:hover) {
  background-color: rgba(0, 0, 0, 0.03);
}
</style>
