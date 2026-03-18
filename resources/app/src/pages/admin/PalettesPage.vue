<template>
  <q-page class="q-pa-md">
    <div class="row items-center justify-between q-mb-xs">
      <div>
        <div class="text-h5 text-weight-bold">Paleta de cores</div>
        <div class="text-body2 text-grey-7">Gerencie as paletas de cores disponíveis</div>
      </div>
      <q-btn
        color="primary"
        icon="add"
        label="Nova Paleta"
        @click="openDialog(null)"
      />
    </div>

    <!-- Table -->
    <q-table
      :rows="palettes"
      :columns="columns"
      row-key="id"
      :loading="loading"
      flat
      class="q-mt-md clickable-rows"
    >
      <template v-slot:body="props">
        <q-tr :props="props" @click="openDialog(props.row)" class="cursor-pointer">
          <q-td key="id" :props="props">
            {{ props.row.id }}
          </q-td>
          <q-td key="name" :props="props">
            {{ props.row.name }}
          </q-td>
          <q-td key="year" :props="props">
            {{ props.row.year }}
          </q-td>
          <q-td key="order" :props="props">
            {{ props.row.order }}
          </q-td>
          <q-td key="colors_count" :props="props">
            {{ props.row.colors?.length || 0 }} cores
          </q-td>
          <q-td key="active" :props="props">
            <q-badge :color="props.row.active ? 'positive' : 'grey'" :label="props.row.active ? 'Ativa' : 'Inativa'" />
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
                {{ isEdit ? 'Editar Paleta' : 'Criar Nova Paleta' }}
              </div>
              <div class="text-body2 text-grey-7">
                {{ isEdit ? 'Atualize as informações da paleta abaixo' : 'Preencha os dados para criar uma nova paleta' }}
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
              <q-tooltip>{{ form.colors?.length > 0 ? 'Inativar paleta' : 'Excluir paleta' }}</q-tooltip>
            </q-btn>
          </div>
        </q-card-section>

        <q-separator />

        <!-- Form -->
        <q-card-section class="q-pt-lg q-px-lg q-pb-md">
          <q-form @submit="savePalette">
            <div class="row q-col-gutter-md">
              <!-- Ano -->
              <div class="col-12">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Ano da paleta</div>
                <q-input
                  v-model.number="form.year"
                  type="number"
                  placeholder="Ex: 2026"
                  filled
                  dense
                  hide-bottom-space
                  :rules="[val => !!val || 'Ano é obrigatório']"
                >
                  <template v-slot:prepend>
                    <q-icon name="calendar_today" color="grey-6" />
                  </template>
                </q-input>
              </div>

              <!-- Ordem -->
              <div class="col-12">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Ordem de exibição</div>
                <div class="text-caption text-grey-6 q-mb-sm">
                  Define a ordem de apresentação da paleta
                </div>
                <q-input
                  v-model.number="form.order"
                  type="number"
                  placeholder="0"
                  filled
                  dense
                  hide-bottom-space
                >
                  <template v-slot:prepend>
                    <q-icon name="sort" color="grey-6" />
                  </template>
                </q-input>
              </div>

              <!-- Status Ativo -->
              <div class="col-12" v-if="isEdit">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Status</div>
                <q-toggle
                  v-model="form.active"
                  label="Paleta ativa"
                  color="positive"
                />
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
            @click="savePalette"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { paletteService } from 'src/services/paletteService'

export default defineComponent({
  name: 'PalettesPage',

  setup() {
    const $q = useQuasar()

    const palettes = ref([])
    const loading = ref(false)
    const saving = ref(false)
    const showDialog = ref(false)
    const isEdit = ref(false)

    const form = ref({
      id: null,
      year: null,
      order: 0,
      active: true,
      colors: []
    })

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
        name: 'year',
        label: 'Ano',
        field: 'year',
        align: 'left',
        sortable: true
      },
      {
        name: 'order',
        label: 'Ordem',
        field: 'order',
        align: 'center',
        sortable: true
      },
      {
        name: 'colors_count',
        label: 'Cores',
        field: row => row.colors?.length || 0,
        align: 'center',
        sortable: true
      },
      {
        name: 'active',
        label: 'Status',
        field: 'active',
        align: 'center',
        sortable: true
      }
    ]

    const openDialog = (palette) => {
      if (palette) {
        isEdit.value = true
        form.value = {
          id: palette.id,
          year: palette.year,
          order: palette.order,
          active: Boolean(palette.active),
          colors: palette.colors || []
        }
      } else {
        isEdit.value = false
        form.value = {
          id: null,
          year: null,
          order: 0,
          active: true,
          colors: []
        }
      }
      showDialog.value = true
    }

    const savePalette = async () => {
      saving.value = true

      try {
        const paletteData = {
          name: form.value.year.toString(),
          year: form.value.year,
          order: form.value.order,
          active: Boolean(form.value.active)
        }

        if (isEdit.value) {
          await paletteService.update(form.value.id, paletteData)
        } else {
          await paletteService.create(paletteData)
        }

        $q.notify({
          type: 'positive',
          message: `Paleta ${isEdit.value ? 'atualizada' : 'criada'} com sucesso!`,
          position: 'top'
        })

        showDialog.value = false
        await loadPalettes()
      } catch (error) {
        const errorMessage = error.response?.data?.message ||
                           error.response?.data?.errors?.year?.[0] ||
                           'Erro ao salvar paleta'

        $q.notify({
          type: 'negative',
          message: errorMessage,
          position: 'top'
        })
      } finally {
        saving.value = false
      }
    }

    const confirmDelete = (palette) => {
      const hasColors = palette.colors && palette.colors.length > 0

      $q.dialog({
        title: hasColors ? 'Inativar Paleta' : 'Confirmar Exclusão',
        message: hasColors
          ? `A paleta "${palette.year}" possui ${palette.colors.length} cores e será inativada. Deseja continuar?`
          : `Deseja realmente excluir a paleta "${palette.year}"?`,
        cancel: {
          label: 'Cancelar',
          flat: true
        },
        ok: {
          label: hasColors ? 'Inativar' : 'Excluir',
          color: 'negative'
        },
        persistent: true
      }).onOk(async () => {
        await deletePalette(palette.id)
      })
    }

    const deletePalette = async (paletteId) => {
      try {
        const result = await paletteService.delete(paletteId)

        $q.notify({
          type: 'positive',
          message: result.inactivated
            ? 'Paleta inativada com sucesso!'
            : 'Paleta excluída com sucesso!',
          position: 'top'
        })

        showDialog.value = false
        await loadPalettes()
      } catch {
        $q.notify({
          type: 'negative',
          message: 'Erro ao excluir paleta',
          position: 'top'
        })
      }
    }

    const loadPalettes = async () => {
      loading.value = true

      try {
        palettes.value = await paletteService.getAll()
      } catch {
        $q.notify({
          type: 'negative',
          message: 'Erro ao carregar paletas',
          position: 'top'
        })
      } finally {
        loading.value = false
      }
    }

    // Carregar dados ao montar
    onMounted(() => {
      loadPalettes()
    })

    return {
      palettes,
      columns,
      loading,
      saving,
      showDialog,
      isEdit,
      form,
      openDialog,
      savePalette,
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
