<template>
  <q-page class="q-pa-md">
    <div class="row items-center justify-between q-mb-xs">
      <div>
        <div class="text-h5 text-weight-bold">Paletas de Cores</div>
        <div class="text-body2 text-grey-7">Gerencie as paletas disponíveis</div>
      </div>
      <q-btn color="primary" icon="add" label="Nova Paleta" @click="openDialog(null)" />
    </div>

    <!-- Lista de Paletas -->
    <div v-if="!loading" class="q-mt-md">
      <q-list bordered separator>
        <q-item v-for="palette in palettes" :key="palette.id" class="q-py-md">
          <q-item-section avatar>
            <q-avatar :color="palette.active ? 'primary' : 'grey'" text-color="white" icon="palette" />
          </q-item-section>

          <q-item-section>
            <q-item-label class="text-weight-bold text-body1">{{ palette.name }}</q-item-label>
            <q-item-label caption>
              Ano: {{ palette.year }} • Ordem: {{ palette.order }} • {{ palette.colors?.length || 0 }} cores
            </q-item-label>
          </q-item-section>

          <q-item-section side>
            <div class="row q-gutter-xs items-center">
              <q-badge :color="palette.active ? 'positive' : 'grey'" :label="palette.active ? 'Ativa' : 'Inativa'" />
              <q-btn flat dense round icon="edit" color="primary" @click="openDialog(palette)">
                <q-tooltip>Editar</q-tooltip>
              </q-btn>
              <q-btn flat dense round icon="delete" color="negative" @click="confirmDelete(palette)">
                <q-tooltip>{{ palette.colors?.length > 0 ? 'Inativar' : 'Excluir' }}</q-tooltip>
              </q-btn>
            </div>
          </q-item-section>
        </q-item>

        <q-item v-if="palettes.length === 0">
          <q-item-section class="text-center text-grey-6">
            Nenhuma paleta cadastrada
          </q-item-section>
        </q-item>
      </q-list>
    </div>

    <!-- Loading -->
    <div v-else class="q-mt-md">
      <q-skeleton v-for="i in 5" :key="i" height="80px" class="q-mb-sm" />
    </div>

    <!-- Dialog Form -->
    <q-dialog v-model="showDialog" persistent>
      <q-card style="min-width: 500px; max-width: 600px">
        <!-- Header -->
        <q-card-section class="q-pb-sm">
          <div class="text-h5 text-weight-medium q-mb-xs">
            {{ isEdit ? 'Editar Paleta' : 'Criar Nova Paleta' }}
          </div>
          <div class="text-body2 text-grey-7">
            {{ isEdit ? 'Atualize as informações da paleta abaixo' : 'Preencha os dados para criar uma nova paleta' }}
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
            @click="closeDialog"
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
      active: true
    })

    const openDialog = (palette) => {
      if (palette) {
        isEdit.value = true
        form.value = {
          id: palette.id,
          year: palette.year,
          active: palette.active
        }
      } else {
        isEdit.value = false
        form.value = {
          id: null,
          year: null,
          active: true
        }
      }
      showDialog.value = true
    }

    const closeDialog = () => {
      showDialog.value = false
    }

    const savePalette = async () => {
      saving.value = true

      try {
        // Calcular ordem baseado no ano (ano - 2025 = ordem)
        const order = form.value.year - 2025

        const paletteData = {
          name: form.value.year.toString(),
          year: form.value.year,
          order: order,
          active: form.value.active
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

        closeDialog()
        await loadPalettes()
      } catch (error) {
        console.error('Erro ao salvar paleta:', error)
        $q.notify({
          type: 'negative',
          message: error.response?.data?.message || 'Erro ao salvar paleta',
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
          ? `A paleta "${palette.name}" possui ${palette.colors.length} cores e será inativada. Deseja continuar?`
          : `Deseja realmente excluir a paleta "${palette.name}"?`,
        cancel: {
          label: 'Cancelar',
          flat: true,
          color: 'grey-7'
        },
        ok: {
          label: hasColors ? 'Inativar' : 'Excluir',
          color: 'negative',
          unelevated: true
        },
        persistent: true
      }).onOk(async () => {
        try {
          const result = await paletteService.delete(palette.id)

          $q.notify({
            type: 'positive',
            message: result.inactivated
              ? 'Paleta inativada com sucesso!'
              : 'Paleta excluída com sucesso!',
            position: 'top'
          })

          await loadPalettes()
        } catch (error) {
          console.error('Erro ao excluir paleta:', error)
          $q.notify({
            type: 'negative',
            message: error.response?.data?.message || 'Erro ao excluir paleta',
            position: 'top'
          })
        }
      })
    }

    const loadPalettes = async () => {
      loading.value = true

      try {
        palettes.value = await paletteService.getAll()
      } catch (error) {
        console.error('Erro ao carregar paletas:', error)
        $q.notify({
          type: 'negative',
          message: 'Erro ao carregar paletas',
          position: 'top'
        })
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadPalettes()
    })

    return {
      palettes,
      loading,
      saving,
      showDialog,
      isEdit,
      form,
      openDialog,
      closeDialog,
      savePalette,
      confirmDelete,
      loadPalettes
    }
  }
})
</script>
