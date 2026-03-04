<template>
  <q-page class="q-pa-md">
    <div class="row items-center justify-between q-mb-xs">
      <div>
        <div class="text-h5 text-weight-bold">Cores</div>
        <div class="text-body2 text-grey-7">Gerencie as cores das paletas</div>
      </div>
      <div class="row q-gutter-sm items-center">
        <q-btn
          flat
          round
          dense
          icon="settings"
          color="grey-7"
          @click="$router.push('/admin/paletas')"
        >
          <q-tooltip>Gerenciar paletas</q-tooltip>
        </q-btn>
        <q-select
          v-model="selectedPalette"
          :options="paletteOptions"
          option-value="value"
          option-label="label"
          emit-value
          map-options
          label="Filtrar por paleta"
          filled
          dense
          clearable
          style="min-width: 180px"
          @update:model-value="loadColors"
        >
          <template v-slot:prepend>
            <q-icon name="filter_list" color="grey-6" />
          </template>
        </q-select>
        <q-btn
          color="primary"
          icon="add"
          label="Nova Cor"
          @click="openDialog(null)"
        />
      </div>
    </div>

    <!-- Grid de Cores -->
    <div v-if="!loading" class="row q-col-gutter-md q-mt-sm">
      <div v-for="color in colors" :key="color.id" class="col-12 col-sm-6 col-md-4 col-lg-3">
        <q-card bordered flat class="color-card">
          <!-- Imagem da Cor -->
          <q-img
            v-if="color.image"
            :src="getImageUrl(color.image)"
            style="height: 250px"
          />
          <div v-else class="bg-grey-3 flex flex-center" style="height: 250px">
            <q-icon name="image" size="64px" color="grey-5" />
          </div>

          <!-- Informações -->
          <q-card-section>
            <div class="text-weight-bold">{{ color.name }}</div>
            <div v-if="color.description" class="text-caption text-grey-7 q-mt-xs ellipsis-2-lines">{{ color.description }}</div>
            <div class="text-caption text-grey-6 q-mt-xs">Paleta: {{ color.palette?.name || 'N/A' }}</div>
            <div class="text-caption text-grey-6">Ordem: {{ color.order }}</div>
            <div class="q-mt-sm q-gutter-xs">
              <q-badge v-if="color.in_2027_palette" color="blue" text-color="white" label="Cartela 2027" />
              <q-badge v-if="color.has_cuff_collar" color="green" text-color="white" label="Com Gola/Punho" />
              <q-badge v-else color="orange" text-color="white" label="Só Gola" />
            </div>
          </q-card-section>

          <!-- Ações -->
          <q-separator />
          <q-card-actions align="right" class="q-px-md q-py-sm">
            <q-btn
              flat
              size="sm"
              icon="edit"
              label="Editar"
              color="primary"
              @click="openDialog(color)"
              no-caps
            />
            <q-btn
              flat
              size="sm"
              icon="delete"
              color="negative"
              @click="confirmDelete(color)"
              no-caps
            />
          </q-card-actions>
        </q-card>
      </div>

      <div v-if="colors.length === 0" class="col-12 text-center q-pa-xl text-grey-6">
        Nenhuma cor cadastrada
      </div>
    </div>

    <!-- Loading -->
    <div v-else class="row q-col-gutter-md">
      <div v-for="i in 8" :key="i" class="col-12 col-sm-6 col-md-4 col-lg-3">
        <q-skeleton height="250px" />
        <q-skeleton height="60px" class="q-mt-sm" />
      </div>
    </div>

    <!-- Dialog Form -->
    <q-dialog v-model="showDialog" persistent>
      <q-card style="min-width: 500px; max-width: 600px">
        <!-- Header -->
        <q-card-section class="q-pb-sm">
          <div class="text-h5 text-weight-medium q-mb-xs">
            {{ isEdit ? 'Editar Cor' : 'Criar Nova Cor' }}
          </div>
          <div class="text-body2 text-grey-7">
            {{ isEdit ? 'Atualize as informações da cor abaixo' : 'Preencha os dados para criar uma nova cor' }}
          </div>
        </q-card-section>

        <q-separator />

        <!-- Form -->
        <q-card-section class="q-pt-lg q-px-lg q-pb-md">
          <q-form @submit="saveColor">
            <div class="row q-col-gutter-md">
              <!-- REF e Nome da Cor -->
              <div class="col-4">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">REF</div>
                <q-input
                  v-model="form.ref"
                  placeholder="Ex: 0613"
                  filled
                  dense
                  hide-bottom-space
                >
                  <template v-slot:prepend>
                    <q-icon name="tag" color="grey-6" />
                  </template>
                </q-input>
              </div>

              <div class="col-8">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Nome da cor</div>
                <q-input
                  v-model="form.name"
                  placeholder="Digite o nome da cor"
                  filled
                  dense
                  hide-bottom-space
                  :rules="[val => !!val || 'Nome é obrigatório']"
                >
                  <template v-slot:prepend>
                    <q-icon name="palette" color="grey-6" />
                  </template>
                </q-input>
              </div>

              <!-- Descrição -->
              <div class="col-12">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Descrição</div>
                <q-input
                  v-model="form.description"
                  placeholder="Descrição sobre a cor"
                  filled
                  dense
                  type="textarea"
                  rows="2"
                  hide-bottom-space
                >
                  <template v-slot:prepend>
                    <q-icon name="description" color="grey-6" />
                  </template>
                </q-input>
              </div>

              <!-- Tipo -->
              <div class="col-12">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Tipo</div>
                <q-input
                  v-model="form.type"
                  placeholder="Ex: Clara, Escura, Forte, Neon"
                  filled
                  dense
                  hide-bottom-space
                >
                  <template v-slot:prepend>
                    <q-icon name="category" color="grey-6" />
                  </template>
                </q-input>
              </div>

              <!-- Paleta e Ordem -->
              <div class="col-8">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Paleta</div>
                <q-select
                  v-model="form.palette_id"
                  :options="paletteOptions"
                  option-value="value"
                  option-label="label"
                  emit-value
                  map-options
                  placeholder="Selecione a paleta"
                  filled
                  dense
                  hide-bottom-space
                  :rules="[val => !!val || 'Paleta é obrigatória']"
                >
                  <template v-slot:prepend>
                    <q-icon name="palette" color="grey-6" />
                  </template>
                </q-select>
              </div>

              <div class="col-4">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Ordem</div>
                <q-input
                  v-model.number="form.order"
                  type="number"
                  placeholder="0"
                  filled
                  dense
                  hide-bottom-space
                  :rules="[val => val !== null && val !== undefined || 'Ordem é obrigatória']"
                >
                  <template v-slot:prepend>
                    <q-icon name="sort" color="grey-6" />
                  </template>
                </q-input>
              </div>

              <!-- Upload de Imagem -->
              <div class="col-12">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Imagem da cor</div>
                <q-file
                  v-model="form.image"
                  placeholder="Selecionar imagem"
                  filled
                  dense
                  hide-bottom-space
                  accept="image/jpeg,image/png,image/jpg"
                  @update:model-value="onImageSelected"
                  :rules="[val => isEdit || !!val || 'Imagem é obrigatória']"
                >
                  <template v-slot:prepend>
                    <q-icon name="image" color="grey-6" />
                  </template>
                </q-file>
              </div>

              <!-- Preview da Imagem -->
              <div v-if="imagePreview" class="col-12">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Preview</div>
                <q-img
                  :src="imagePreview"
                  style="max-width: 100%; max-height: 300px"
                  class="rounded-borders"
                  fit="contain"
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
            @click="saveColor"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { colorService } from 'src/services/colorService'
import { api } from 'src/boot/axios'

export default defineComponent({
  name: 'ColorsPage',

  setup() {
    const $q = useQuasar()
    const STORAGE_URL = process.env.API_URL_IMG

    const colors = ref([])
    const palettes = ref([])
    const paletteOptions = ref([])
    const loading = ref(false)
    const saving = ref(false)
    const showDialog = ref(false)
    const isEdit = ref(false)
    const imagePreview = ref(null)
    const selectedPalette = ref(null)

    const form = ref({
      id: null,
      ref: '',
      name: '',
      description: '',
      type: '',
      palette_id: null,
      order: 0,
      image: null
    })

    const getImageUrl = (imagePath) => {
      if (!imagePath) return null
      const url = `${STORAGE_URL}/${imagePath}`
      console.log('Image URL:', url, 'STORAGE_URL:', STORAGE_URL, 'imagePath:', imagePath)
      return url
    }

    const loadPalettes = async () => {
      try {
        const response = await api.get('/palettes', { params: { active: 1 } })
        palettes.value = response.data
        paletteOptions.value = [...palettes.value.map(p => ({ label: p.name, value: p.id }))]
      } catch (error) {
        console.error('Erro ao carregar paletas:', error)
      }
    }

    const openDialog = (color) => {
      if (color) {
        isEdit.value = true
        form.value = {
          id: color.id,
          ref: color.ref || '',
          name: color.name,
          description: color.description || '',
          type: color.type || '',
          palette_id: color.palette_id,
          order: color.order,
          image: null
        }
        imagePreview.value = getImageUrl(color.image)
      } else {
        isEdit.value = false
        form.value = {
          id: null,
          ref: '',
          name: '',
          description: '',
          type: '',
          palette_id: selectedPalette.value,
          order: 0,
          image: null
        }
        imagePreview.value = null
      }
      showDialog.value = true
    }

    const closeDialog = () => {
      showDialog.value = false
      imagePreview.value = null
    }

    const onImageSelected = (file) => {
      if (file) {
        const reader = new FileReader()
        reader.onload = (e) => {
          imagePreview.value = e.target.result
        }
        reader.readAsDataURL(file)
      } else {
        imagePreview.value = null
      }
    }

    const saveColor = async () => {
      saving.value = true

      try {
        const colorData = {
          ref: form.value.ref,
          name: form.value.name,
          description: form.value.description,
          type: form.value.type,
          palette_id: form.value.palette_id,
          order: form.value.order,
          image: form.value.image
        }

        if (isEdit.value) {
          await colorService.update(form.value.id, colorData)
        } else {
          await colorService.create(colorData)
        }

        $q.notify({
          type: 'positive',
          message: `Cor ${isEdit.value ? 'atualizada' : 'criada'} com sucesso!`,
          position: 'top'
        })

        closeDialog()
        await loadColors()
      } catch (error) {
        console.error('Erro ao salvar cor:', error)
        $q.notify({
          type: 'negative',
          message: error.response?.data?.message || 'Erro ao salvar cor',
          position: 'top'
        })
      } finally {
        saving.value = false
      }
    }

    const confirmDelete = (color) => {
      $q.dialog({
        title: 'Confirmar Exclusão',
        message: `Deseja realmente excluir a cor "${color.name}"?`,
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
        try {
          await colorService.delete(color.id)

          $q.notify({
            type: 'positive',
            message: 'Cor excluída com sucesso!',
            position: 'top'
          })

          await loadColors()
        } catch (error) {
          console.error('Erro ao excluir cor:', error)
          $q.notify({
            type: 'negative',
            message: error.response?.data?.message || 'Erro ao excluir cor',
            position: 'top'
          })
        }
      })
    }

    const loadColors = async () => {
      loading.value = true

      try {
        const params = selectedPalette.value ? { palette_id: selectedPalette.value } : {}
        colors.value = await colorService.getAll(params)
      } catch (error) {
        console.error('Erro ao carregar cores:', error)
        $q.notify({
          type: 'negative',
          message: 'Erro ao carregar cores',
          position: 'top'
        })
      } finally {
        loading.value = false
      }
    }

    onMounted(async () => {
      await loadPalettes()
      // Define paleta 2026 como padrão
      const palette2026 = palettes.value.find(p => p.name === '2026')
      if (palette2026) {
        selectedPalette.value = palette2026.id
      }
      await loadColors()
    })

    return {
      colors,
      palettes,
      paletteOptions,
      loading,
      saving,
      showDialog,
      isEdit,
      form,
      imagePreview,
      selectedPalette,
      getImageUrl,
      openDialog,
      closeDialog,
      onImageSelected,
      saveColor,
      confirmDelete,
      loadColors
    }
  }
})
</script>

<style scoped>
.color-card {
  height: 100%;
}

.ellipsis-2-lines {
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
}
</style>
