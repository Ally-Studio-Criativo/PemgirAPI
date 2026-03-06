<template>
  <q-page class="q-pa-md">
    <!-- Seção: Textos da Landing Page -->
    <div class="q-mb-xl">
      <div class="row items-center justify-between q-mb-md">
        <div>
          <div class="text-h5 text-weight-bold">Textos da Landing Page</div>
          <div class="text-body2 text-grey-7">Edite os textos em português, inglês e espanhol</div>
        </div>
        <q-btn color="primary" icon="save" label="Salvar Textos" @click="saveAllTexts" :loading="savingTexts" />
      </div>

      <q-card v-if="!loadingTexts" bordered flat>
        <q-tabs v-model="currentTextSection" dense class="text-grey" active-color="primary" indicator-color="primary" align="left">
          <q-tab name="about_company" label="Sobre a Empresa" />
          <q-tab name="about_products" label="Mais de 130 Produtos" />
          <q-tab name="hero_video" label="Hero Video" />
        </q-tabs>

        <q-separator />

        <q-tab-panels v-model="currentTextSection" animated>
          <!-- Sobre a Empresa -->
          <q-tab-panel name="about_company">
            <div class="q-gutter-md">
              <div v-for="text in getSectionTexts('about_company')" :key="text.id" class="q-mb-md">
                <div class="text-overline">{{ text.key.toUpperCase() }}</div>
                <q-input v-model="text.text_pt" label="Português" filled dense class="q-mb-sm" :type="text.key === 'description' ? 'textarea' : 'text'" />
                <q-input v-model="text.text_en" label="English" filled dense class="q-mb-sm" :type="text.key === 'description' ? 'textarea' : 'text'" />
                <q-input v-model="text.text_es" label="Español" filled dense :type="text.key === 'description' ? 'textarea' : 'text'" />
              </div>
            </div>
          </q-tab-panel>

          <!-- Mais de 130 Produtos -->
          <q-tab-panel name="about_products">
            <div class="q-gutter-md">
              <div v-for="text in getSectionTexts('about_products')" :key="text.id" class="q-mb-md">
                <div class="text-overline">{{ text.key.toUpperCase() }}</div>
                <q-input v-model="text.text_pt" label="Português" filled dense class="q-mb-sm" :type="text.key === 'description' ? 'textarea' : 'text'" />
                <q-input v-model="text.text_en" label="English" filled dense class="q-mb-sm" :type="text.key === 'description' ? 'textarea' : 'text'" />
                <q-input v-model="text.text_es" label="Español" filled dense :type="text.key === 'description' ? 'textarea' : 'text'" />
              </div>
            </div>
          </q-tab-panel>

          <!-- Hero Video -->
          <q-tab-panel name="hero_video">
            <div class="q-gutter-md">
              <div v-for="text in getSectionTexts('hero_video')" :key="text.id" class="q-mb-md">
                <div class="text-overline">{{ text.key.toUpperCase() }}</div>
                <q-input v-model="text.text_pt" label="Português" filled dense class="q-mb-sm" />
                <q-input v-model="text.text_en" label="English" filled dense class="q-mb-sm" />
                <q-input v-model="text.text_es" label="Español" filled dense />
              </div>
            </div>
          </q-tab-panel>
        </q-tab-panels>
      </q-card>

      <q-skeleton v-else height="400px" />
    </div>

    <q-separator class="q-my-xl" />

    <!-- Seção: Vídeo Hero -->
    <div class="q-mb-xl">
      <div class="row items-center justify-between q-mb-md">
        <div>
          <div class="text-h5 text-weight-bold">Vídeo Hero Section</div>
          <div class="text-body2 text-grey-7">Gerencie o vídeo de fundo da página inicial</div>
        </div>
      </div>

      <q-card bordered flat v-if="!loading">
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6">
              <q-input
                v-model="heroVideoUrl"
                label="URL do Vídeo (YouTube/Vimeo)"
                filled
                dense
                hint="Cole o link embed do YouTube ou Vimeo"
              >
                <template v-slot:prepend>
                  <q-icon name="link" />
                </template>
              </q-input>
            </div>
            <div class="col-12 col-md-6">
              <div class="row q-gutter-sm">
                <div class="col-auto">
                  <q-btn
                    color="primary"
                    icon="save"
                    label="Salvar URL"
                    @click="saveVideoUrl"
                    :loading="savingVideoUrl"
                  />
                </div>
                <div class="col-auto">
                  <q-btn
                    outline
                    color="primary"
                    icon="videocam"
                    label="Fazer Upload"
                    @click="openVideoUpload"
                  />
                </div>
              </div>
            </div>
            <div class="col-12">
              <q-separator class="q-my-md" />
              <div class="text-caption text-grey-7">
                <strong>URL/Arquivo atual:</strong><br>
                {{ images.find(i => i.type === 'hero_video')?.path || 'Não definido' }}
              </div>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <q-skeleton v-else height="150px" />
    </div>

    <!-- Seção: Imagens da Landing Page -->
    <div>
      <div class="row items-center justify-between q-mb-md">
        <div>
          <div class="text-h5 text-weight-bold">Imagens da Landing Page</div>
          <div class="text-body2 text-grey-7">Gerencie as imagens dos cards e seções</div>
        </div>
      </div>

      <!-- Grid de Imagens -->
      <div v-if="!loading" class="row q-col-gutter-md">
        <div v-for="image in images.filter(i => i.type !== 'hero_video')" :key="image.id" class="col-12 col-sm-6 col-md-4">
          <q-card bordered flat class="landing-card">
            <q-img
              v-if="image.path"
              :src="getImageUrl(image.path)"
              style="height: 200px"
            />
            <div v-else class="bg-grey-3 flex flex-center" style="height: 200px">
              <q-icon name="image" size="64px" color="grey-5" />
            </div>

            <q-card-section>
              <div class="text-weight-bold">{{ image.title }}</div>
              <div class="text-caption text-grey-6">Tipo: {{ image.type }}</div>
              <div class="text-caption text-grey-6">Tamanho: {{ formatSize(image.size) }}</div>
            </q-card-section>

            <q-separator />
            <q-card-actions align="center">
              <q-btn
                flat
                icon="upload"
                label="Atualizar"
                color="primary"
                @click="openUploadDialog(image)"
                no-caps
              />
            </q-card-actions>
          </q-card>
        </div>
      </div>

      <div v-else class="row q-col-gutter-md">
        <div v-for="i in 6" :key="i" class="col-12 col-sm-6 col-md-4">
          <q-skeleton height="200px" />
          <q-skeleton height="80px" class="q-mt-sm" />
        </div>
      </div>
    </div>

    <!-- Dialog Upload -->
    <q-dialog v-model="showUploadDialog" persistent>
      <q-card style="min-width: 500px">
        <q-card-section class="row items-center q-pb-sm">
          <div class="text-h6">Atualizar {{ currentImage?.title }}</div>
          <q-space />
          <q-btn icon="close" flat round dense @click="closeUploadDialog" />
        </q-card-section>

        <q-separator />

        <q-card-section class="q-pt-md">
          <q-file
            v-model="uploadFile"
            :label="currentImage?.type === 'hero_video' ? 'Selecionar Vídeo' : 'Selecionar Imagem'"
            :placeholder="currentImage?.type === 'hero_video' ? 'Escolha um arquivo de vídeo' : 'Escolha um arquivo de imagem'"
            filled
            :accept="currentImage?.type === 'hero_video' ? 'video/mp4,video/mov,video/avi' : 'image/jpeg,image/png,image/jpg,image/webp'"
            @update:model-value="onFileSelected"
            :hint="currentImage?.type === 'hero_video' ? 'Tamanho máximo: 100MB' : 'Tamanho máximo: 20MB'"
          >
            <template v-slot:prepend>
              <q-icon :name="currentImage?.type === 'hero_video' ? 'videocam' : 'image'" />
            </template>
          </q-file>

          <div v-if="uploadPreview && currentImage?.type !== 'hero_video'" class="q-mt-md">
            <div class="text-caption text-grey-7 q-mb-sm">Preview:</div>
            <q-img
              :src="uploadPreview"
              style="max-height: 300px; border-radius: 8px"
              class="bg-grey-2"
              fit="contain"
            />
          </div>

          <div v-if="uploadFile && currentImage?.type === 'hero_video'" class="q-mt-md">
            <div class="text-caption text-grey-7 q-mb-sm">Arquivo selecionado:</div>
            <div class="text-body2">{{ uploadFile.name }}</div>
            <div class="text-caption text-grey-6">{{ formatSize(uploadFile.size) }}</div>
          </div>
        </q-card-section>

        <q-separator />

        <q-card-actions align="right" class="q-px-md q-py-md">
          <q-btn label="Cancelar" color="grey-7" flat @click="closeUploadDialog" />
          <q-btn
            label="Enviar"
            color="primary"
            icon="upload"
            unelevated
            :loading="uploading"
            :disable="!uploadFile"
            @click="uploadImage"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { api } from 'src/boot/axios'

export default defineComponent({
  name: 'LandingPageManager',
  setup() {
    const $q = useQuasar()
    const STORAGE_URL = process.env.API_URL_IMG

    // Textos
    const landingTexts = ref([])
    const loadingTexts = ref(false)
    const savingTexts = ref(false)
    const currentTextSection = ref('about_company')

    // Imagens
    const images = ref([])
    const loading = ref(false)
    const showUploadDialog = ref(false)
    const currentImage = ref(null)
    const uploadFile = ref(null)
    const uploadPreview = ref(null)
    const uploading = ref(false)
    const heroVideoUrl = ref('')
    const savingVideoUrl = ref(false)

    const getImageUrl = (path) => {
      if (!path) return null
      if (path.startsWith('http')) return path
      return `${STORAGE_URL}/${path}`
    }

    const formatSize = (bytes) => {
      if (!bytes) return 'N/A'
      const kb = bytes / 1024
      const mb = kb / 1024
      return mb > 1 ? `${mb.toFixed(2)} MB` : `${kb.toFixed(2)} KB`
    }

    const loadImages = async () => {
      loading.value = true
      try {
        const response = await api.get('/landing-page-images')
        images.value = response.data

        // Carregar URL do vídeo hero
        const heroVideo = images.value.find(i => i.type === 'hero_video')
        if (heroVideo) {
          heroVideoUrl.value = heroVideo.path || ''
        }
      } catch (error) {
        console.error('Erro ao carregar imagens:', error)
        $q.notify({
          type: 'negative',
          message: 'Erro ao carregar imagens',
          position: 'top'
        })
      } finally {
        loading.value = false
      }
    }

    const openUploadDialog = (image) => {
      currentImage.value = image
      showUploadDialog.value = true
    }

    const openVideoUpload = () => {
      const heroVideo = images.value.find(i => i.type === 'hero_video')
      if (heroVideo) {
        openUploadDialog(heroVideo)
      } else {
        $q.notify({
          type: 'warning',
          message: 'Registro de vídeo hero não encontrado',
          position: 'top'
        })
      }
    }

    const closeUploadDialog = () => {
      showUploadDialog.value = false
      currentImage.value = null
      uploadFile.value = null
      uploadPreview.value = null
    }

    const onFileSelected = (file) => {
      if (file) {
        const reader = new FileReader()
        reader.onload = (e) => {
          uploadPreview.value = e.target.result
        }
        reader.readAsDataURL(file)
      } else {
        uploadPreview.value = null
      }
    }

    const uploadImage = async () => {
      if (!uploadFile.value) return

      uploading.value = true
      try {
        const formData = new FormData()
        formData.append('file', uploadFile.value)
        formData.append('type', currentImage.value.type)

        await api.post('/admin/landing-page-images', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        $q.notify({
          type: 'positive',
          message: 'Imagem atualizada com sucesso!',
          position: 'top'
        })

        closeUploadDialog()
        await loadImages()
      } catch (error) {
        console.error('Erro ao enviar imagem:', error)
        $q.notify({
          type: 'negative',
          message: error.response?.data?.message || 'Erro ao enviar imagem',
          position: 'top'
        })
      } finally {
        uploading.value = false
      }
    }

    const saveVideoUrl = async () => {
      savingVideoUrl.value = true
      try {
        await api.post('/admin/landing-page-images/video-url', {
          url: heroVideoUrl.value,
          type: 'hero_video'
        })

        $q.notify({
          type: 'positive',
          message: 'URL do vídeo atualizada com sucesso!',
          position: 'top'
        })

        await loadImages()
      } catch (error) {
        console.error('Erro ao salvar URL:', error)
        $q.notify({
          type: 'negative',
          message: error.response?.data?.message || 'Erro ao salvar URL',
          position: 'top'
        })
      } finally {
        savingVideoUrl.value = false
      }
    }

    // Carregar textos da API
    const loadTexts = async () => {
      loadingTexts.value = true
      try {
        const response = await api.get('/landing-page-texts')
        landingTexts.value = response.data
      } catch (error) {
        console.error('Erro ao carregar textos:', error)
        $q.notify({
          type: 'negative',
          message: 'Erro ao carregar textos',
          position: 'top'
        })
      } finally {
        loadingTexts.value = false
      }
    }

    // Obter textos de uma seção específica
    const getSectionTexts = (section) => {
      return landingTexts.value.filter(text => text.section === section)
    }

    // Salvar todos os textos
    const saveAllTexts = async () => {
      savingTexts.value = true
      try {
        const textsToUpdate = landingTexts.value.map(text => ({
          id: text.id,
          text_pt: text.text_pt,
          text_en: text.text_en,
          text_es: text.text_es
        }))

        await api.post('/landing-page-texts/bulk-update', {
          texts: textsToUpdate
        })

        $q.notify({
          type: 'positive',
          message: 'Textos atualizados com sucesso!',
          position: 'top'
        })
      } catch (error) {
        console.error('Erro ao salvar textos:', error)
        $q.notify({
          type: 'negative',
          message: error.response?.data?.message || 'Erro ao salvar textos',
          position: 'top'
        })
      } finally {
        savingTexts.value = false
      }
    }

    onMounted(() => {
      loadTexts()
      loadImages()
    })

    return {
      // Textos
      landingTexts,
      loadingTexts,
      savingTexts,
      currentTextSection,
      getSectionTexts,
      saveAllTexts,

      // Imagens
      images,
      loading,
      showUploadDialog,
      currentImage,
      uploadFile,
      uploadPreview,
      uploading,
      heroVideoUrl,
      savingVideoUrl,
      getImageUrl,
      formatSize,
      openUploadDialog,
      openVideoUpload,
      closeUploadDialog,
      onFileSelected,
      uploadImage,
      saveVideoUrl
    }
  }
})
</script>

<style scoped>
.landing-card {
  height: 100%;
}
</style>
