<template>
  <q-page class="q-pa-md">
    <div class="row items-center justify-between q-mb-lg">
      <div>
        <div class="text-h5 text-weight-bold">Landing Page</div>
        <div class="text-body2 text-grey-7">Gerencie os conteúdos e imagens da landing page</div>
      </div>
    </div>

    <q-card v-if="loading" flat class="q-pa-lg text-center">
      <q-spinner color="primary" size="50px" />
      <div class="q-mt-md text-grey-7">Carregando...</div>
    </q-card>

    <q-card v-else flat bordered>
      <q-card-section class="bg-grey-2">
        <div class="text-h6">Configurações da Campanha</div>
      </q-card-section>

      <q-separator />

      <q-card-section>
        <q-form @submit="saveCampaign">
          <div class="row q-col-gutter-md">
            <!-- Tabs para idiomas -->
            <div class="col-12">
              <q-tabs v-model="currentLang" dense class="text-grey" active-color="primary" indicator-color="primary" align="left">
                <q-tab name="pt" label="Português" icon="flag" />
                <q-tab name="en" label="English" icon="flag" />
                <q-tab name="es" label="Español" icon="flag" />
              </q-tabs>
              <q-separator />
            </div>

            <!-- Campos Português -->
            <div v-show="currentLang === 'pt'" class="col-12 row q-col-gutter-md">
              <div class="col-12 col-md-6">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Texto da Campanha (PT)</div>
                <div class="text-caption text-grey-7 q-mb-sm">Ex: "Campanha de"</div>
                <q-input v-model="form.campaign_text_pt" dense outlined />
              </div>
              <div class="col-12 col-md-6">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Texto do Lançamento (PT)</div>
                <div class="text-caption text-grey-7 q-mb-sm">
                  Use <code>\n</code> para quebra de linha. Ex: "Lança\nmento"
                </div>
                <q-input v-model="form.launch_text_pt" dense outlined />
              </div>
              <div class="col-12 col-md-6">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Título (PT)</div>
                <div class="text-caption text-grey-7 q-mb-sm">Ex: "DAY BY DAY"</div>
                <q-input v-model="form.title_pt" dense outlined />
              </div>
              <div class="col-12 col-md-6">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Temporada (PT)</div>
                <div class="text-caption text-grey-7 q-mb-sm">Ex: "Inverno 2026"</div>
                <q-input v-model="form.season_pt" dense outlined />
              </div>
              <div class="col-12 col-md-6">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Texto do Botão (PT)</div>
                <div class="text-caption text-grey-7 q-mb-sm">Ex: "Assista agora"</div>
                <q-input v-model="form.button_text_pt" dense outlined />
              </div>
            </div>

            <!-- Campos English -->
            <div v-show="currentLang === 'en'" class="col-12 row q-col-gutter-md">
              <div class="col-12 col-md-6">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Campaign Text (EN)</div>
                <div class="text-caption text-grey-7 q-mb-sm">Ex: "Campaign"</div>
                <q-input v-model="form.campaign_text_en" dense outlined />
              </div>
              <div class="col-12 col-md-6">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Launch Text (EN)</div>
                <div class="text-caption text-grey-7 q-mb-sm">
                  Use <code>\n</code> for line break. Ex: "Launch"
                </div>
                <q-input v-model="form.launch_text_en" dense outlined />
              </div>
              <div class="col-12 col-md-6">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Title (EN)</div>
                <div class="text-caption text-grey-7 q-mb-sm">Ex: "DAY BY DAY"</div>
                <q-input v-model="form.title_en" dense outlined />
              </div>
              <div class="col-12 col-md-6">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Season (EN)</div>
                <div class="text-caption text-grey-7 q-mb-sm">Ex: "Winter 2026"</div>
                <q-input v-model="form.season_en" dense outlined />
              </div>
              <div class="col-12 col-md-6">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Button Text (EN)</div>
                <div class="text-caption text-grey-7 q-mb-sm">Ex: "Watch now"</div>
                <q-input v-model="form.button_text_en" dense outlined />
              </div>
            </div>

            <!-- Campos Español -->
            <div v-show="currentLang === 'es'" class="col-12 row q-col-gutter-md">
              <div class="col-12 col-md-6">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Texto de Campaña (ES)</div>
                <div class="text-caption text-grey-7 q-mb-sm">Ex: "Campaña de"</div>
                <q-input v-model="form.campaign_text_es" dense outlined />
              </div>
              <div class="col-12 col-md-6">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Texto de Lanzamiento (ES)</div>
                <div class="text-caption text-grey-7 q-mb-sm">
                  Use <code>\n</code> para salto de línea. Ex: "Lanza\nmiento"
                </div>
                <q-input v-model="form.launch_text_es" dense outlined />
              </div>
              <div class="col-12 col-md-6">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Título (ES)</div>
                <div class="text-caption text-grey-7 q-mb-sm">Ex: "DAY BY DAY"</div>
                <q-input v-model="form.title_es" dense outlined />
              </div>
              <div class="col-12 col-md-6">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Temporada (ES)</div>
                <div class="text-caption text-grey-7 q-mb-sm">Ex: "Invierno 2026"</div>
                <q-input v-model="form.season_es" dense outlined />
              </div>
              <div class="col-12 col-md-6">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Texto del Botón (ES)</div>
                <div class="text-caption text-grey-7 q-mb-sm">Ex: "Ver ahora"</div>
                <q-input v-model="form.button_text_es" dense outlined />
              </div>
            </div>

            <!-- URL do YouTube e Status -->
            <div class="col-12">
              <q-separator class="q-my-md" />
            </div>

            <div class="col-12 col-md-8">
              <div class="text-subtitle2 text-weight-medium q-mb-xs">URL do Vídeo YouTube</div>
              <div class="text-caption text-grey-7 q-mb-sm">Cole o link do vídeo no formato embed</div>
              <q-input v-model="form.youtube_url" dense outlined placeholder="https://www.youtube.com/embed/VIDEO_ID">
                <template v-slot:prepend>
                  <q-icon name="video_library" color="grey-6" />
                </template>
              </q-input>
            </div>
            <div class="col-12 col-md-4 flex items-center">
              <q-toggle
                v-model="form.active"
                label="Campanha ativa"
                color="positive"
                size="lg"
              />
            </div>

            <!-- Preview do vídeo -->
            <div v-if="form.youtube_url" class="col-12">
              <div class="text-subtitle2 text-weight-medium q-mb-sm">Preview do vídeo</div>
              <div class="video-preview">
                <iframe
                  :src="form.youtube_url"
                  width="100%"
                  height="400"
                  frameborder="0"
                  allow="autoplay; encrypted-media"
                  allowfullscreen
                ></iframe>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="row justify-end q-mt-lg q-gutter-sm">
            <q-btn
              type="submit"
              label="Salvar Alterações"
              color="primary"
              icon="save"
              :loading="saving"
              size="md"
            />
          </div>
        </q-form>
      </q-card-section>
    </q-card>

    <!-- Seção de Vídeo Hero -->
    <q-card flat bordered class="q-mt-lg">
      <q-card-section class="bg-grey-2">
        <div class="text-h6">Vídeo Hero Section</div>
        <div class="text-caption text-grey-7">Gerencie o vídeo de fundo da seção hero da landing page</div>
      </q-card-section>

      <q-separator />

      <q-card-section>
        <div v-if="loadingVideo" class="text-center q-pa-lg">
          <q-spinner color="primary" size="50px" />
          <div class="q-mt-md text-grey-7">Carregando vídeo...</div>
        </div>

        <div v-else class="row q-col-gutter-md">
          <div class="col-12">
            <div class="text-subtitle2 text-weight-medium q-mb-xs">Vídeo Hero Section</div>
            <div class="text-caption text-grey-7 q-mb-sm">Faça upload de um arquivo de vídeo (MP4, MOV, AVI - Máx: 100MB)</div>
            <q-btn
              color="primary"
              icon="upload"
              label="Alterar Vídeo"
              @click="openVideoUpload"
              unelevated
            />
          </div>

          <!-- Preview do vídeo atual -->
          <div v-if="heroVideo?.path && !heroVideo.path.startsWith('http')" class="col-12">
            <div class="row items-center justify-between q-mb-sm">
              <div class="text-subtitle2 text-weight-medium">Preview do vídeo atual</div>
              <q-btn
                flat
                dense
                color="negative"
                icon="delete"
                label="Excluir Vídeo"
                @click="confirmDeleteVideo"
                size="sm"
              />
            </div>
            <video
              controls
              :src="getVideoUrl(heroVideo.path)"
              style="width: 100%; max-height: 400px; border-radius: 8px; border: 1px solid #e0e0e0;"
            >
              Seu navegador não suporta a tag de vídeo.
            </video>
            <div class="q-mt-sm text-caption text-grey-7">
              {{ heroVideo.filename }}
              <q-badge v-if="heroVideo.size" :label="formatFileSize(heroVideo.size)" color="grey-6" class="q-ml-sm" />
            </div>
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Seção de Imagens -->
    <q-card flat bordered class="q-mt-lg">
      <q-card-section class="bg-grey-2">
        <div class="text-h6">Imagens da Landing Page</div>
        <div class="text-caption text-grey-7">Gerencie as imagens exibidas nos cards e seçõesss</div>
      </q-card-section>

      <q-separator />

      <q-card-section>
        <div v-if="loadingImages" class="text-center q-pa-lg">
          <q-spinner color="primary" size="50px" />
          <div class="q-mt-md text-grey-7">Carregando imagens...</div>
        </div>

        <div v-else class="row q-col-gutter-md">
          <div
            v-for="image in landingPageImages"
            :key="image.id"
            :class="image.type.startsWith('prancheta') ? 'col-12 col-md-6' : 'col-12 col-md-6 col-lg-3'"
          >
            <q-card flat bordered>
              <q-img v-if="image.path" :src="getImageUrl(image.path)" :style="image.type.startsWith('prancheta') ? 'height: 250px' : 'height: 200px'" />
              <div v-else class="bg-grey-3 flex items-center justify-center" :style="image.type.startsWith('prancheta') ? 'height: 250px' : 'height: 200px'">
                <q-icon name="image" size="48px" color="grey-5" />
              </div>
              <q-card-section>
                <div class="text-weight-bold">{{ image.title }}</div>
                <div class="text-caption text-grey-7">{{ image.filename }}</div>
                <q-badge
                  v-if="image.size > 5000000"
                  color="warning"
                  :label="formatFileSize(image.size)"
                  class="q-mt-xs"
                />
                <q-badge
                  v-else-if="image.size"
                  color="grey-6"
                  :label="formatFileSize(image.size)"
                  class="q-mt-xs"
                />
              </q-card-section>
              <q-separator />
              <q-card-actions align="right" class="q-px-md q-py-sm">
                <q-btn
                  flat
                  size="sm"
                  icon="upload"
                  label="Alterar"
                  color="primary"
                  @click="openImageUpload(image.type, image.title)"
                  no-caps
                />
              </q-card-actions>
            </q-card>
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Dialog de Upload de Vídeo -->
    <q-dialog v-model="showVideoUploadDialog" persistent>
      <q-card style="min-width: 400px">
        <q-card-section class="row items-center q-pb-sm">
          <div class="text-h6">Alterar Vídeo - Hero Section</div>
          <q-space />
          <q-btn icon="close" flat round dense @click="closeVideoUploadDialog" />
        </q-card-section>

        <q-separator />

        <q-card-section>
          <q-file
            v-model="selectedVideoFile"
            label="Selecionar Vídeo"
            placeholder="Escolha um arquivo de vídeo"
            filled
            accept="video/mp4,video/mov,video/avi"
          >
            <template v-slot:prepend>
              <q-icon name="videocam" />
            </template>
          </q-file>

          <div v-if="selectedVideoFile" class="q-mt-md">
            <div class="text-caption text-grey-7 q-mb-sm">Arquivo selecionado:</div>
            <div class="text-body2">{{ selectedVideoFile.name }}</div>
            <div class="text-caption text-grey-6">{{ formatFileSize(selectedVideoFile.size) }}</div>
          </div>
        </q-card-section>

        <q-separator />

        <q-card-actions align="right" class="q-px-md q-py-md">
          <q-btn label="Cancelar" color="grey-7" flat @click="closeVideoUploadDialog" />
          <q-btn label="Fazer Upload" color="primary" icon="upload" unelevated :loading="uploading" :disable="!selectedVideoFile" @click="uploadVideo" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Dialog de Upload de Imagens -->
    <q-dialog v-model="showUploadDialog" persistent>
      <q-card style="min-width: 400px">
        <q-card-section class="row items-center q-pb-sm">
          <div class="text-h6">Alterar Imagem - {{ uploadImageTitle }}</div>
          <q-space />
          <q-btn icon="close" flat round dense @click="closeUploadDialog" />
        </q-card-section>

        <q-separator />

        <q-card-section>
          <q-file
            v-model="selectedFile"
            label="Selecionar nova imagem"
            placeholder="Escolha um arquivo de imagem"
            filled
            accept="image/*"
            @update:model-value="onFileSelected"
          >
            <template v-slot:prepend>
              <q-icon name="image" />
            </template>
          </q-file>

          <div v-if="previewUrl" class="q-mt-md">
            <div class="text-caption text-grey-7 q-mb-sm">Preview:</div>
            <q-img :src="previewUrl" style="max-height: 300px; border-radius: 8px" />
          </div>
        </q-card-section>

        <q-separator />

        <q-card-actions align="right" class="q-px-md q-py-md">
          <q-btn label="Cancelar" color="grey-7" flat @click="closeUploadDialog" />
          <q-btn label="Fazer Upload" color="primary" icon="upload" unelevated :loading="uploading" :disable="!selectedFile" @click="uploadImage" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { ref, onMounted } from 'vue'
import { api } from 'src/boot/axios'
import { useQuasar } from 'quasar'

export default {
  name: 'CampaignsPage',

  setup() {
    const $q = useQuasar()

    const loading = ref(false)
    const saving = ref(false)
    const uploading = ref(false)
    const loadingImages = ref(false)
    const loadingVideo = ref(false)
    const currentLang = ref('pt')
    const showUploadDialog = ref(false)
    const showVideoUploadDialog = ref(false)
    const selectedFile = ref(null)
    const selectedVideoFile = ref(null)
    const previewUrl = ref(null)
    const currentImageType = ref('')
    const uploadImageTitle = ref('')
    const landingPageImages = ref([])
    const heroVideo = ref(null)

    const form = ref({
      id: null,
      campaign_text_pt: '',
      campaign_text_en: '',
      campaign_text_es: '',
      launch_text_pt: '',
      launch_text_en: '',
      launch_text_es: '',
      title_pt: '',
      title_en: '',
      title_es: '',
      season_pt: '',
      season_en: '',
      season_es: '',
      button_text_pt: '',
      button_text_en: '',
      button_text_es: '',
      youtube_url: '',
      active: true
    })

    const loadCampaign = async () => {
      loading.value = true
      try {
        const response = await api.get('/campaign/active')
        if (response.data) {
          form.value = response.data
        }
      } catch (error) {
        if (error.response?.status === 404) {
          $q.notify({
            type: 'info',
            message: 'Nenhuma campanha cadastrada. Preencha os dados abaixo.'
          })
        } else {
          $q.notify({
            type: 'negative',
            message: 'Erro ao carregar campanha'
          })
        }
      } finally {
        loading.value = false
      }
    }

    const saveCampaign = async () => {
      saving.value = true
      try {
        if (form.value.id) {
          await api.put(`/campaigns/${form.value.id}`, form.value)
          $q.notify({
            type: 'positive',
            message: 'Campanha atualizada com sucesso!',
            icon: 'check_circle'
          })
        } else {
          const response = await api.post('/campaigns', form.value)
          form.value = response.data
          $q.notify({
            type: 'positive',
            message: 'Campanha criada com sucesso!',
            icon: 'check_circle'
          })
        }
      } catch {
        $q.notify({
          type: 'negative',
          message: 'Erro ao salvar campanha'
        })
      } finally {
        saving.value = false
      }
    }

    const loadLandingPageImages = async () => {
      loadingImages.value = true
      try {
        const response = await api.get('/landing-page-images')
        // Filtrar apenas imagens, excluindo hero_video
        landingPageImages.value = response.data.filter(img => img.type !== 'hero_video')
      } catch (error) {
        console.error('Erro ao carregar imagens:', error)
        $q.notify({
          type: 'negative',
          message: 'Erro ao carregar imagens da landing page'
        })
      } finally {
        loadingImages.value = false
      }
    }

    const formatFileSize = (bytes) => {
      if (!bytes) return 'N/A'
      if (bytes < 1024) return bytes + ' B'
      if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB'
      return (bytes / 1048576).toFixed(1) + ' MB'
    }

    const getImageUrl = (path) => {
      if (!path) return null
      // Get backend URL (remove /api/v1 from the base URL)
      const backendUrl = api.defaults.baseURL.replace('/api/v1', '') || 'http://localhost'
      return `${backendUrl}/${path}`
    }

    const getVideoUrl = (path) => {
      if (!path) return null
      // Get backend URL (remove /api/v1 from the base URL)
      const backendUrl = api.defaults.baseURL.replace('/api/v1', '') || 'http://localhost'
      return `${backendUrl}/${path}`
    }

    const openImageUpload = (type, title) => {
      currentImageType.value = type
      uploadImageTitle.value = title
      showUploadDialog.value = true
    }

    const onFileSelected = (file) => {
      if (file) {
        const reader = new FileReader()
        reader.onload = (e) => {
          previewUrl.value = e.target.result
        }
        reader.readAsDataURL(file)
      } else {
        previewUrl.value = null
      }
    }

    const closeUploadDialog = () => {
      showUploadDialog.value = false
      selectedFile.value = null
      previewUrl.value = null
      currentImageType.value = ''
    }

    const uploadImage = async () => {
      if (!selectedFile.value) return

      uploading.value = true
      try {
        const formData = new FormData()

        // Check if it's a video or image
        if (currentImageType.value === 'hero_video') {
          formData.append('video', selectedFile.value)
        } else {
          formData.append('image', selectedFile.value)
        }
        formData.append('type', currentImageType.value)

        await api.post('/landing-page-images', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })

        $q.notify({
          type: 'positive',
          message: currentImageType.value === 'hero_video' ? 'Vídeo atualizado com sucesso!' : 'Imagem atualizada com sucesso!',
          icon: 'check_circle'
        })

        closeUploadDialog()

        // Recarregar as imagens e vídeo
        if (currentImageType.value === 'hero_video') {
          await loadHeroVideo()
        } else {
          await loadLandingPageImages()
        }
      } catch (error) {
        console.error('Erro ao fazer upload:', error)
        $q.notify({
          type: 'negative',
          message: 'Erro ao fazer upload'
        })
      } finally {
        uploading.value = false
      }
    }

    const loadHeroVideo = async () => {
      loadingVideo.value = true
      try {
        const response = await api.get('/landing-page-images')
        const videoRecord = response.data.find(img => img.type === 'hero_video')
        if (videoRecord) {
          heroVideo.value = videoRecord
        }
      } catch (error) {
        console.error('Erro ao carregar vídeo hero:', error)
      } finally {
        loadingVideo.value = false
      }
    }

    const openVideoUpload = () => {
      selectedVideoFile.value = null
      showVideoUploadDialog.value = true
    }

    const closeVideoUploadDialog = () => {
      showVideoUploadDialog.value = false
      selectedVideoFile.value = null
    }

    const uploadVideo = async () => {
      if (!selectedVideoFile.value) return

      uploading.value = true
      try {
        const formData = new FormData()
        formData.append('video', selectedVideoFile.value)
        formData.append('type', 'hero_video')

        await api.post('/landing-page-images', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })

        $q.notify({
          type: 'positive',
          message: 'Vídeo atualizado com sucesso!',
          icon: 'check_circle'
        })

        closeVideoUploadDialog()
        await loadHeroVideo()
      } catch (error) {
        console.error('Erro ao fazer upload:', error)
        $q.notify({
          type: 'negative',
          message: 'Erro ao fazer upload do vídeo'
        })
      } finally {
        uploading.value = false
      }
    }

    const confirmDeleteVideo = () => {
      $q.dialog({
        title: 'Confirmar exclusão',
        message: 'Tem certeza que deseja excluir o vídeo hero? Esta ação não pode ser desfeita.',
        cancel: {
          label: 'Cancelar',
          color: 'grey-7',
          flat: true
        },
        ok: {
          label: 'Excluir',
          color: 'negative',
          unelevated: true
        },
        persistent: true
      }).onOk(() => {
        deleteVideo()
      })
    }

    const deleteVideo = async () => {
      try {
        await api.delete(`/admin/landing-page-images/hero_video`)

        $q.notify({
          type: 'positive',
          message: 'Vídeo excluído com sucesso!',
          icon: 'check_circle'
        })

        heroVideo.value = null
        await loadHeroVideo()
      } catch (error) {
        console.error('Erro ao excluir vídeo:', error)
        $q.notify({
          type: 'negative',
          message: 'Erro ao excluir vídeo'
        })
      }
    }

    onMounted(() => {
      loadCampaign()
      loadLandingPageImages()
      loadHeroVideo()
    })

    return {
      loading,
      saving,
      uploading,
      loadingImages,
      loadingVideo,
      currentLang,
      form,
      landingPageImages,
      heroVideo,
      showUploadDialog,
      showVideoUploadDialog,
      selectedFile,
      selectedVideoFile,
      previewUrl,
      uploadImageTitle,
      saveCampaign,
      formatFileSize,
      getImageUrl,
      getVideoUrl,
      openImageUpload,
      openVideoUpload,
      closeVideoUploadDialog,
      uploadVideo,
      confirmDeleteVideo,
      onFileSelected,
      closeUploadDialog,
      uploadImage
    }
  }
}
</script>

<style scoped>
.video-preview {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  overflow: hidden;
}
</style>
