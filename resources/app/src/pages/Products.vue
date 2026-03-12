<template>
  <q-page padding>
    <q-drawer
      v-model="drawerCategory"
      :side="$q.platform.is.desktop ? 'left' : 'right'"
      :behavior="$q.platform.is.desktop ? 'desktop' : 'mobile'"
      :width="$q.platform.is.desktop ? 320 : 300"
      class="full-height bg-grey-2 products-drawer"
      :overlay="!$q.platform.is.desktop"
      :persistent="$q.platform.is.desktop"
    >
      <div class="q-pa-md">
        <div class="header-content">
          <div class="text-h5 text-weight-bold">{{ t('menu.products') }}</div>
          <div class="header-actions">
            <!-- Botão X para fechar drawer no mobile -->
            <q-btn
              v-if="!$q.platform.is.desktop"
              flat
              round
              dense
              icon="close"
              @click="drawerCategory = false"
              class="text-grey-6 close-drawer-btn"
            />
            <q-btn
              v-if="panel === 'product'"
              flat
              round
              dense
              icon="arrow_back"
              @click="$router.push('/produtos')"
              class="text-grey-6"
            />
          </div>
        </div>

        <q-input
          dense
          v-model="filterCategory"
          :placeholder="t('ui.search')"
          class="search-input"
          bg-color="grey-2"
        >
          <template v-slot:append>
            <q-icon name="search" color="grey-6" />
          </template>
        </q-input>
      </div>

      <q-scroll-area class="categories-scroll bg-grey-2">
        <q-list class="categories-list">
          <!-- Item fixo LANÇAMENTOS -->
          <q-item
            :active="filter.isLaunchFilter"
            clickable
            v-ripple
            @click="filterLaunchProducts"
            class="category-item featured-category"
            active-class="active-category"
          >
            <q-item-section>
              <q-item-label class="category-label">
                LANÇAMENTOS
                <q-icon
                  name="new_releases"
                  size="16px"
                  color="amber-7"
                  class="q-ml-xs"
                />
              </q-item-label>
            </q-item-section>
            <q-item-section side v-if="filter.isLaunchFilter">
              <q-icon name="radio_button_checked" color="primary" />
            </q-item-section>
          </q-item>

          <!-- Categorias normais -->
          <q-item
            v-for="category in filteredCategories"
            :key="category.value"
            :active="filter.category === category.value && !filter.isLaunchFilter"
            clickable
            v-ripple
            @click="filterCategoryFn(category.value)"
            class="category-item"
            active-class="active-category"
          >
            <q-item-section>
              <q-item-label class="category-label">
                {{ normalizeDisplayText(category.label) }}
              </q-item-label>
            </q-item-section>
            <q-item-section side v-if="filter.category === category.value && !filter.isLaunchFilter">
              <q-icon name="radio_button_checked" color="primary" />
            </q-item-section>
          </q-item>
        </q-list>
      </q-scroll-area>
    </q-drawer>

    <div class="products-container full-height">
      <div
        id="products"
        class="products-content"
        :class="$q.platform.is.desktop && drawerCategory ? 'with-drawer' : 'without-drawer'"
      >
        <q-tab-panels v-model="panel" animated>
          <q-tab-panel name="products" class="products-panel">
            <!-- Mensagem de produto não encontrado -->
            <div v-if="productNotFound" class="row justify-center q-mb-lg">
              <div class="error-message-container">
                <div class="error-content">
                  <q-icon name="search_off" size="48px" color="grey-6" class="error-icon" />
                  <div class="error-title">{{ t('products.notFound.title') }}</div>
                  <div class="error-message">{{ t('products.notFound.message') }}</div>
                  <div class="error-countdown">
                    {{ t('products.notFound.redirecting', { count: redirectCountdown }) }}
                  </div>
                  <div class="progress-container">
                    <div
                      class="progress-bar"
                      :style="{ width: `${((5 - redirectCountdown) / 5) * 100}%` }"
                    ></div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row justify-between">
              <div :class="`col-12 ${$q.platform.is.desktop ? 'text-h2' : 'text-h3'} text-bold`">
                {{ t('products.title') }}
              </div>
              <div class="col-12 col-md-8 text-h6 q-mb-lg">
                {{
                  filter.isLaunchFilter
                    ? 'LANÇAMENTOS'
                    : filter.category
                    ? normalizeDisplayText(
                        categories.find((c) => c.value === filter.category)?.label,
                      )
                    : t('ui.allProducts')
                }}
                | {{ filteredProducts().length }} {{ t('ui.productsFound') }}
              </div>
              <div class="col-12 col-md-3 text-right">
                <div class="row q-gutter-sm justify-end">
                  <div class="col-auto">
                    <q-btn
                      outline
                      round
                      icon="share"
                      color="grey-8"
                      v-if="filter.category"
                      @click="shareCategory"
                    >
                      <q-tooltip>{{ t('ui.shareCategory') }}</q-tooltip>
                    </q-btn>
                  </div>
                  <!-- Botão Filtrar apenas no mobile -->
                  <div v-if="!$q.platform.is.desktop" class="col-auto">
                    <q-btn
                      outline
                      icon="filter_list"
                      :label="t('ui.filter')"
                      color="grey-8"
                      @click="drawerCategory = true"
                      class="filter-btn-mobile"
                    >
                      <q-badge
                        v-if="filter.category"
                        floating
                        rounded
                        style="margin-top: 17px; margin-right: -2px"
                        color="amber"
                      />
                    </q-btn>
                  </div>
                  <div class="col">
                    <q-input
                      outlined
                      dense
                      color="grey-8"
                      rounded
                      v-model="filter.ref"
                      :placeholder="t('ui.searchProducts')"
                    >
                      <template v-slot:append>
                        <q-icon name="search" />
                      </template>
                    </q-input>
                  </div>
                </div>
              </div>
            </div>

            <div style="position: relative">
              <q-table
                :columns="columns"
                :rows="filteredProducts()"
                grid
                flat
                v-model:pagination="pagination"
                :filter="filter.ref"
                :filter-method="customFilterFn"
              >
                <template v-slot:item="props">
                  <div class="q-pa-sm q-pt-md col-12 col-sm-4 col-md-3 flex">
                    <q-card
                      class="full-width column shadow-8 cursor-pointer"
                      @click="getProduct(props.row)"
                    >
                      <q-img :src="props.row.image" fit="cover" style="height: 200px" />
                      <div class="q-pa-md bg-white full-width">
                        <div class="column">
                          <div class="text-uppercase text-grey-7 text-body2">
                            {{ props.row.ref }}
                          </div>
                          <div class="text-subtitle1 text-weight-bold text-wrap">
                            {{ normalizeDisplayText(props.row.name) }}
                          </div>
                        </div>
                      </div>
                    </q-card>
                  </div>
                </template>
              </q-table>
            </div>
          </q-tab-panel>

          <q-tab-panel name="product" style="overflow: hidden; padding-bottom: 100px;">
            <!-- Header com botões de navegação -->
            <div class="row justify-between items-center q-pa-sm q-mb-lg">
              <div class="col-auto">
                <q-btn
                  outline
                  rounded
                  no-caps
                  :label="t('ui.back')"
                  icon="arrow_back"
                  @click="$router.push('/produtos')"
                  color="grey-8"
                  size="md"
                >
                  <q-tooltip class="bg-grey-8">{{ t('ui.backToProducts') }}</q-tooltip>
                </q-btn>
              </div>
              <div class="col-auto">
                <q-btn outline round icon="share" color="grey-8" size="md" @click="shareProduct">
                  <q-tooltip class="bg-grey-8">{{ t('ui.shareProduct') }}</q-tooltip>
                </q-btn>
              </div>
            </div>

            <div :class="`row justify-center ${$q.platform.is.desktop ? 'q-px-lg' : 'q-px-none'}`">
              <div
                :class="`col-12 col-md-8 ${$q.platform.is.desktop ? 'text-h4' : 'text-h4'} text-center text-bold`"
              >
                {{ normalizeDisplayText(product?.name) }}
              </div>
              <div class="col-12 text-center q-mb-lg">
                <div class="text-h6 text-bold">
                  {{ normalizeDisplayText(product?.categoryLabel) }} |
                  <span class="text-weight-regular">{{ product?.ref }}</span>
                </div>
              </div>

              <div class="row full-width justify-center items-center">
                <div class="col-12 col-md-5" style="position: relative; z-index: 2">
                  <div class="bg-square-2" v-if="$q.platform.is.desktop"></div>
                  <q-card class="shadow-19">
                    <div style="position: relative">
                      <q-img
                        :src="selectedImage || product?.image"
                        fit="cover"
                        style="height: 300px"
                      />
                      <!-- Overlay com REF, cor e tamanho -->
                      <div
                        v-if="selectedImageInfo.ref && selectedImageInfo.color"
                        class="image-color-overlay"
                      >
                        <div class="image-color-ref">{{ selectedImageInfo.ref }}</div>
                        <div class="image-color-name">{{ selectedImageInfo.color }}</div>
                        <div v-if="selectedImageInfo.type" class="image-color-type">
                          {{ selectedImageInfo.type }}
                        </div>
                        <div v-if="selectedImageInfo.in_2027_palette" class="image-color-flag">
                          Disponível na cartela 2027
                        </div>
                        <div v-if="selectedImageInfo.has_cuff_collar" class="image-color-flag has-asterisk">
                          <span class="asterisk-red">*</span>
                          <span style="margin-left: 13px">
                            {{ selectedImageInfo.has_cuff_collar }}
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="row q-pa-md bg-white full-width">
                      <div class="col-12">
                        <div class="row q-gutter-xs">
                          <div
                            v-for="(image, index) in product?.allImages"
                            :key="index"
                            class="image-tab cursor-pointer"
                            :class="{
                              selected: selectedImage === image,
                              'has-cuff-collar': product?.images[index]?.has_cuff_collar
                            }"
                            @click="selectImage(image, index)"
                          >
                            <q-img
                              :src="image"
                              fit="cover"
                              style="border-radius: 6px"
                            />
                          </div>
                        </div>
                      </div>
                      <div class="col-12 q-mt-sm">
                        <div class="disclaimer-text">
                          <q-icon name="info" size="14px" class="q-mr-xs" />
                          As imagens são meramente ilustrativas e podem apresentar variações de cor
                          e tonalidade. As imagens com bordas vermelhas acompanham gola e punho
                        </div>
                      </div>
                    </div>
                  </q-card>
                </div>
                <div class="col-12 col-md-4">
                  <q-card class="q-pa-lg card-infos" flat>
                    <div
                      :class="`${$q.platform.is.desktop ? 'text-h4' : 'text-h5'} text-bold q-mb-md`"
                    >
                      {{ t('productDetails.characteristics') }}
                      <div class="text-weight-regular">
                        {{ t('productDetails.characteristicsOf') }}
                      </div>
                    </div>

                    <div class="text-h6 text-bold q-mb-md">
                      {{ t('productDetails.composition') }}:
                      <span class="text-weight-regular">{{ product?.composition }}</span>
                    </div>

                    <div class="text-h6 text-bold q-mb-md">
                      {{ t('productDetails.weight') }}:
                      <span class="text-weight-regular">{{ product?.gramatura }} m²</span>
                    </div>

                    <div class="text-h6 text-bold q-mb-md">
                      {{ t('productDetails.width') }}:
                      <span class="text-weight-regular">{{ product?.width }}</span>
                    </div>

                    <div class="text-h6 text-bold q-mb-md">
                      {{ t('productDetails.yield') }}:
                      <span class="text-weight-regular">{{ product?.yield }}</span>
                    </div>

                    <div v-if="product?.observations" class="text-h6 text-weight-regular q-mb-lg">
                      {{ product?.observations }}
                    </div>
                    <div v-else class="q-mb-lg"></div>

                    <div class="card-infos-btn-wrapper">
                      <q-btn color="green" rounded size="lg" no-caps @click="sendWhatsApp">
                        <img
                          src="/icons/whatsapp.svg"
                          alt="WhatsApp"
                          style="width: 25px; height: 25px; margin-right: 8px"
                        />
                        {{ t('ui.send') }}
                      </q-btn>
                    </div>
                  </q-card>
                </div>
              </div>
            </div>
          </q-tab-panel>
        </q-tab-panels>
      </div>
    </div>
  </q-page>
</template>

<script>
import { defineComponent, ref, computed, watch, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter, useRoute } from 'vue-router'
import { useQuasar } from 'quasar'
import { api } from 'src/boot/axios'
import { productService } from 'src/services/productService'

export default defineComponent({
  name: 'OurProducts',
  components: {},
  setup() {
    const $q = useQuasar()
    const drawer = true
    const drawerCategory = ref($q.platform.is.desktop)
    const panel = ref('products')
    const product = ref(null)
    const selectedImage = ref(null)
    const selectedImageRef = ref(null)
    const productNotFound = ref(false)
    const redirectCountdown = ref(5)
    const { t } = useI18n()
    const $router = useRouter()
    const $route = useRoute()

    // Categorias e produtos carregados dinamicamente da API
    const categories = ref([])
    const productsFromAPI = ref([])
    const loadingProducts = ref(false)

    // Função para carregar categorias
    const loadCategories = async () => {
      try {
        const response = await api.get('/categories')
        categories.value = response.data.map((cat) => ({
          label: cat.name,
          value: cat.id,
        }))

        // Definir LANÇAMENTOS como categoria padrão se não houver filtro de categoria na URL
        if (!$route.query.categoria && filter.value.category === null) {
          const lancamentos = categories.value.find(cat => cat.label === 'LANÇAMENTOS')
          if (lancamentos) {
            filter.value.category = lancamentos.value
          }
        }
      } catch (error) {
        console.error('Erro ao carregar categorias:', error)
        $q.notify({
          message: 'Erro ao carregar categorias da API',
          caption: `Usuário: ${error.response?.data?.user || 'Desconhecido'}`,
          color: 'negative',
          position: 'top',
          timeout: 10000,
          icon: 'error'
        })
        categories.value = []
      }
    }

    // Função helper para limpar o path da imagem (remove 'storage/' se existir)
    const cleanImagePath = (path) => {
      if (!path) return null
      return path.replace(/^storage\//, '')
    }

    // Função para carregar produtos da API
    const loadProducts = async () => {
      loadingProducts.value = true
      try {
        const params = filter.value.category ? { category_id: filter.value.category } : {}
        const apiProducts = await productService.getAll(params)

        // Transformar produtos da API para o formato esperado pelo template
        productsFromAPI.value = apiProducts.map(p => ({
          id: p.id,
          name: p.name,
          categoryId: p.category_id,
          category: p.category?.name || '',
          categoryLabel: p.category?.name || '',
          categories: p.categories || [], // Múltiplas categorias
          image: p.images && p.images.length > 0 ? `${process.env.API_URL_IMG}/${cleanImagePath(p.images[0].path)}` : null,
          images: p.images || [],
          ref: p.reference || '',
          allImages: p.images ? p.images.map(img => `${process.env.API_URL_IMG}/${cleanImagePath(img.path)}`) : [],
          composition: p.composition || '-',
          gramatura: p.gramatura || '-',
          width: p.width || '-',
          yield: p.yield || '-',
          observations: p.observations || null,
          is_launch: p.is_launch || false,
        }))
      } catch (error) {
        console.error('Erro ao carregar produtos:', error)
        $q.notify({
          message: 'Erro ao carregar produtos da API',
          caption: `Usuário: ${error.response?.data?.user || 'Desconhecido'}`,
          color: 'negative',
          position: 'top',
          timeout: 10000,
          icon: 'error'
        })
        productsFromAPI.value = []
      } finally {
        loadingProducts.value = false
      }
    }

    // Função para sanitizar nomes de arquivos/pastas
    const sanitizeFileName = (name) => {
      // Substituir / por - (ex: "28/1" vira "28-1")
      // Substituir % por _ (ex: "12%" vira "12_")
      // Manter vírgulas, pois os nomes das pastas já as contêm
      return name.replace(/\//g, '-').replace(/%/g, '_')
    }

    // Função para normalizar o texto para exibição
    // Converte underscores e hífens de volta para caracteres originais
    const normalizeDisplayText = (text) => {
      if (!text) return text

      // Detecta padrões comuns e converte _ e - para o caractere apropriado
      return text
        .replace(/^\s*-\s*/, '') // Remove hífen no início
        .replace(/(\d+)[-_](\d+)/g, '$1/$2') // 30_1 ou 30-1 -> 30/1 (fração)
        .replace(/(\d+)_/g, '$1%') // 12_ -> 12% (porcentagem)
        .replace(/_/g, '') // Remove qualquer _ restante
    }

    // Função para extrair REF, cor e tamanho do nome da imagem
    // Suporta três padrões:
    // 1. "REF 1234 - Azul - P" (padrão com 3 partes)
    // 2. "REF 1234 - Azul" (padrão com 2 partes, sem tamanho)
    // 3. "REF 1234 NOME DO PRODUTO tipo" (última palavra em minúscula é o tipo)
    const extractImageInfo = (imageName) => {
      try {
        // Remove a extensão do arquivo
        const nameWithoutExt = imageName.replace(/\.(png|jpg|jpeg)$/i, '')

        // Tenta primeiro o padrão com 3 partes: "REF XXXX - COR - TAMANHO"
        const matchThreeParts = nameWithoutExt.match(/^(REF\s+[\w\d]+)\s*-\s*([^-]+)\s*-\s*(.+)/)

        if (matchThreeParts) {
          const type = matchThreeParts[3].trim().replace(/[\s]+$/, '')
          return {
            ref: normalizeDisplayText(matchThreeParts[1].trim()),
            color: normalizeDisplayText(matchThreeParts[2].trim()),
            type: normalizeDisplayText(type),
          }
        }

        // Tenta padrão com 2 partes: "REF XXXX - COR"
        const matchTwoParts = nameWithoutExt.match(/^(REF\s+[\w\d]+)\s*-\s*(.+)/)

        if (matchTwoParts) {
          return {
            ref: normalizeDisplayText(matchTwoParts[1].trim()),
            color: normalizeDisplayText(matchTwoParts[2].trim()),
            type: '',
          }
        }

        // Padrão alternativo: "REF XXXX NOME DO PRODUTO tipo"
        // Extrai REF + número
        const refMatch = nameWithoutExt.match(/^(REF\s+[\w\d]+)\s+(.+)/)

        if (refMatch) {
          const ref = refMatch[1].trim()
          const remaining = refMatch[2].trim()

          // Separa as palavras do restante
          const words = remaining.split(/\s+/)

          // Verifica se a última palavra começa com letra minúscula (é um tipo)
          const lastWord = words[words.length - 1]
          const isType = lastWord && /^[a-z]/.test(lastWord)

          if (isType && words.length > 1) {
            // Última palavra é o tipo, o resto é o nome/cor
            const type = lastWord
            const color = words.slice(0, -1).join(' ')

            return {
              ref: normalizeDisplayText(ref),
              color: normalizeDisplayText(color),
              type: normalizeDisplayText(type),
            }
          } else {
            // Não tem tipo, tudo é o nome/cor
            return {
              ref: normalizeDisplayText(ref),
              color: normalizeDisplayText(remaining),
              type: '',
            }
          }
        }

        return { ref: '', color: '', type: '' }
      } catch {
        return { ref: '', color: '', type: '' }
      }
    }

    // Computed para obter informações da imagem selecionada
    const selectedImageInfo = computed(() => {
      if (!product.value || !product.value.images) return { ref: '', color: '', type: '', in_2027_palette: false, has_cuff_collar: true }

      // Encontrar o índice da imagem selecionada
      const selectedUrl = selectedImage.value || product.value.image
      const selectedIndex = product.value.allImages?.indexOf(selectedUrl) ?? 0

      const imageData = product.value.images[selectedIndex]

      // Se a imagem tem os campos diretos da API (ref, color_name, image_type)
      if (imageData && typeof imageData === 'object' && imageData.ref !== undefined) {
        return {
          ref: imageData.ref || '',
          color: imageData.color_name || '',
          type: imageData.image_type || '',
          in_2027_palette: imageData.in_2027_palette || false,
          has_cuff_collar: imageData.has_cuff_collar !== undefined ? imageData.has_cuff_collar : true
        }
      }

      // Fallback: extrair do nome do arquivo (dados antigos do JSON)
      const imageName = typeof imageData === 'string' ? imageData : (imageData?.filename || imageData?.path || '')
      return extractImageInfo(imageName)
    })

    // Função para obter a URL da imagem
    const getImageUrl = (categoryName, productName, imageName) => {
      try {
        // Sanitizar os nomes para corresponder aos nomes reais das pastas
        const sanitizedCategory = sanitizeFileName(categoryName)
        const sanitizedProduct = sanitizeFileName(productName)
        const sanitizedImage = sanitizeFileName(imageName)

        // Codificar cada parte do caminho separadamente para lidar com caracteres especiais
        // Preservar vírgulas após encoding (elas devem permanecer como vírgulas, não %2C)
        const encodedCategory = encodeURIComponent(sanitizedCategory).replace(/%2C/g, ',')
        const encodedProduct = encodeURIComponent(sanitizedProduct).replace(/%2C/g, ',')
        const encodedImage = encodeURIComponent(sanitizedImage).replace(/%2C/g, ',')

        return `/categories/${encodedCategory}/${encodedProduct}/${encodedImage}`
      } catch (error) {
        console.warn(
          `Imagem não encontrada: ${categoryName}/${productName}/${imageName}. Erro: ${error.message}`,
        )
        return null
      }
    }

    // Produtos vindos da API
    const products = computed(() => {
      return productsFromAPI.value
    })

    const getProduct = (item) => {
      // Navegar para URL com ID do produto
      $router.push(`/produtos/${item.id}`)
    }

    // Função para extrair REF do nome da imagem
    const extractRefFromImageName = (imageName) => {
      if (!imageName) return null
      // Remove a extensão e extrai o padrão "REF XXXX - NOME" (aceita números e letras no REF)
      const nameWithoutExt = imageName.replace(/\.(png|jpg|jpeg|webp)$/i, '')
      const match = nameWithoutExt.match(
        /^(REF\s+[\w]+\s*-\s*.+?)(?:\s*-\s*(?:única|clara|escura|esc_fte|extra))?$/i,
      )
      return match ? match[1].trim() : null
    }

    const selectImage = (image, index) => {
      selectedImage.value = image
      // Extrair REF do nome do arquivo da imagem
      const imageName = product.value.images[index]
      selectedImageRef.value = extractRefFromImageName(imageName)
    }

    const filter = ref({
      category: null,
      ref: null,
      isLaunchFilter: false,
      colors: ['#e1e0e8', '#181619', '#2e3142', '#4b4f34', '#c0aba8', '#785d40'],
    })

    const pagination = ref({
      rowsPerPage: 8,
      page: 1,
    })

    const filterCategory = ref('')
    const filteredCategories = computed(() => {
      if (!filterCategory.value) return categories.value
      return categories.value.filter((cat) =>
        cat.label.toLowerCase().includes(filterCategory.value.toLowerCase()),
      )
    })

    const filteredProducts = () => {
      let data = products.value

      // Filtro de lançamentos
      if (filter.value.isLaunchFilter) {
        data = data.filter((product) => product.is_launch === true)
      }
      // Filtro por categoria (suporta múltiplas categorias)
      else if (filter.value.category) {
        data = data.filter((product) => {
          // Verificar se o produto tem a categoria selecionada (compatível com múltiplas categorias)
          if (product.categories && Array.isArray(product.categories)) {
            return product.categories.some(cat => cat.id === filter.value.category)
          }
          // Fallback para compatibilidade com category_id antigo
          return product.categoryId === filter.value.category
        })
      }

      return data
    }

    // Função de filtro customizada para o q-table
    // Aplica normalizeDisplayText nos campos antes de fazer a comparação
    const customFilterFn = (rows, terms) => {
      if (!terms || terms.trim() === '') {
        return rows
      }

      const lowerTerms = terms.toLowerCase()

      return rows.filter((row) => {
        // Normalizar os campos antes de comparar
        const normalizedName = normalizeDisplayText(row.name).toLowerCase()
        const normalizedRef = normalizeDisplayText(row.ref).toLowerCase()
        const normalizedCategory = normalizeDisplayText(row.categoryLabel).toLowerCase()

        // Verificar se algum dos campos normalizados contém o termo de busca
        return (
          normalizedName.includes(lowerTerms) ||
          normalizedRef.includes(lowerTerms) ||
          normalizedCategory.includes(lowerTerms)
        )
      })
    }

    const filterLaunchProducts = () => {
      // Alternar filtro de lançamentos
      if (filter.value.isLaunchFilter) {
        filter.value.isLaunchFilter = false
      } else {
        filter.value.isLaunchFilter = true
        filter.value.category = null // Limpar filtro de categoria
      }
      // Voltar para a tab de produtos (tabela)
      panel.value = 'products'
      // Só fechar o drawer no mobile
      if (!$q.platform.is.desktop) {
        drawerCategory.value = false
      }
    }

    const filterCategoryFn = (category) => {
      // Limpar filtro de lançamentos ao selecionar categoria
      filter.value.isLaunchFilter = false

      // Se clicar na mesma categoria, desmarcar
      if (filter.value.category === category) {
        filter.value.category = null
      } else {
        filter.value.category = category
      }
      // Voltar para a tab de produtos (tabela)
      panel.value = 'products'
      // Só fechar o drawer no mobile
      if (!$q.platform.is.desktop) {
        drawerCategory.value = false
      }
    }

    const navigateToSection = (section) => {
      // Navegar para home e depois fazer scroll para a seção
      $router.push('/').then(() => {
        // Aguarda um pequeno delay para a página carregar
        setTimeout(() => {
          const el = document.getElementById(section)
          if (el) {
            const offset = section === 'testimony' ? -100 : 0
            const y = el.getBoundingClientRect().top + window.pageYOffset + offset
            window.scrollTo({ top: y, behavior: 'smooth' })
          }
        }, 100)
      })
    }

    const shareCategory = () => {
      if (filter.value.category) {
        const selectedCategory = categories.value.find((cat) => cat.value === filter.value.category)
        if (selectedCategory) {
          // Mapear label para nome da URL
          const categoryUrlMapping = {
            Cottons: 'COTTONS',
            Helancas: 'HELANCAS',
            'Malhas para Camisetas': 'MALHAS%20PARA%20CAMISETAS',
            Modinhas: 'MODINHAS',
            'Moletons Carecas': 'MOLETONS%20CARECAS',
            Piquê: 'PIQU%C3%8A',
            Poliplex: 'POLIPLEX',
            Viscose: 'VISCOSE',
          }

          const categoryParam = categoryUrlMapping[selectedCategory.label] || selectedCategory.label
          const shareUrl = `${window.location.origin}/produtos?categoria=${categoryParam}`

          if (navigator.share) {
            navigator.share({
              title: `Produtos - ${selectedCategory.label}`,
              text: `Confira nossos produtos da categoria ${selectedCategory.label}`,
              url: shareUrl,
            })
          } else {
            // Fallback: copiar para clipboard
            navigator.clipboard.writeText(shareUrl).then(() => {
              $q.notify({
                message: t('ui.linkCopied'),
                color: 'positive',
                position: 'top',
              })
            })
          }
        }
      }
    }

    const shareProduct = () => {
      if (product.value) {
        const shareUrl = `${window.location.origin}/produtos/${product.value.id}`

        if (navigator.share) {
          navigator.share({
            title: `${product.value.name} - Pemgir`,
            text: `Confira este produto: ${product.value.name} - ${product.value.ref}`,
            url: shareUrl,
          })
        } else {
          // Fallback: copiar para clipboard
          navigator.clipboard.writeText(shareUrl).then(() => {
            $q.notify({
              message: t('ui.productLinkCopied'),
              color: 'positive',
              position: 'top',
            })
          })
        }
      }
    }

    // Função para encontrar produto pelo ID (primeiro no array, senão busca da API)
    const findProductById = async (productId) => {
      // Primeiro tenta encontrar no array de produtos já carregados
      const localProduct = products.value.find((p) => p.id === parseInt(productId))
      if (localProduct) {
        return localProduct
      }

      // Se não encontrou, busca da API
      try {
        const apiProduct = await productService.getById(productId)

        // Transformar para o formato esperado
        return {
          id: apiProduct.id,
          name: apiProduct.name,
          categoryId: apiProduct.category_id,
          category: apiProduct.category?.name || '',
          categoryLabel: apiProduct.category?.name || '',
          categories: apiProduct.categories || [], // Múltiplas categorias
          image: apiProduct.images && apiProduct.images.length > 0 ? `${process.env.API_URL_IMG}/${cleanImagePath(apiProduct.images[0].path)}` : null,
          images: apiProduct.images || [],
          ref: apiProduct.reference || '',
          allImages: apiProduct.images ? apiProduct.images.map(img => `${process.env.API_URL_IMG}/${cleanImagePath(img.path)}`) : [],
          composition: apiProduct.composition || '-',
          gramatura: apiProduct.gramatura || '-',
          width: apiProduct.width || '-',
          yield: apiProduct.yield || '-',
          observations: apiProduct.observations || null,
          is_launch: apiProduct.is_launch || false,
        }
      } catch (error) {
        console.error('Erro ao buscar produto:', error)
        return null
      }
    }

    // Função para carregar produto da URL
    const loadProductFromUrl = async () => {
      const productId = $route.params.productId
      const categoryParam = $route.query.categoria

      // Reset do estado de produto não encontrado
      productNotFound.value = false
      redirectCountdown.value = 5

      // Se há parâmetro de categoria, aplicar filtro
      if (categoryParam) {
        let decodedParam
        try {
          decodedParam = decodeURIComponent(categoryParam)
        } catch {
          // Se a URI estiver malformada, usar o valor original
          decodedParam = categoryParam
        }

        // Verificar se é o filtro especial de LANÇAMENTOS
        if (decodedParam.toUpperCase() === 'LANÇAMENTOS' || decodedParam.toUpperCase() === 'LANCAMENTOS') {
          filter.value.isLaunchFilter = true
          filter.value.category = null
        } else {
          // Buscar categoria pelo nome (case-insensitive)
          const categoryFound = categories.value.find(
            (cat) =>
              cat.label.toLowerCase() === decodedParam.toLowerCase(),
          )

          if (categoryFound) {
            filter.value.isLaunchFilter = false
            filter.value.category = categoryFound.value
          }
        }
      }

      if (productId) {
        const foundProduct = await findProductById(productId)
        if (foundProduct) {
          product.value = foundProduct
          selectedImage.value =
            foundProduct.allImages && foundProduct.allImages.length > 0
              ? foundProduct.allImages[0]
              : foundProduct.image
          // Definir REF inicial da primeira imagem
          if (foundProduct.images && foundProduct.images.length > 0) {
            // Se a imagem tem REF direto (vindo da API), usar ele
            if (foundProduct.images[0].ref) {
              selectedImageRef.value = foundProduct.images[0].ref
            } else {
              // Caso contrário, tentar extrair do nome do arquivo (dados antigos do JSON)
              selectedImageRef.value = extractRefFromImageName(foundProduct.images[0].filename || foundProduct.images[0])
            }
          }
          panel.value = 'product'

          // Atualizar filtro da categoria
          filter.value.category = foundProduct.categoryId
        } else {
          // Produto não encontrado, mostrar mensagem e iniciar contador
          productNotFound.value = true
          panel.value = 'products'

          const countdown = setInterval(() => {
            redirectCountdown.value--
            if (redirectCountdown.value <= 0) {
              clearInterval(countdown)
              productNotFound.value = false
              $router.replace('/produtos')
            }
          }, 1000)
        }
      } else {
        // Sem productId, mostrar lista
        panel.value = 'products'
        product.value = null
        selectedImage.value = null
      }
    }

    // Observar mudanças no filtro de categoria para recarregar produtos
    watch(() => filter.value.category, () => {
      loadProducts()
    })

    // Carregar categorias e produtos quando o componente montar
    onMounted(async () => {
      await loadCategories()
      await loadProductFromUrl() // Carregar produto/categoria da URL primeiro
      await loadProducts()

      // Listener para fechar drawer quando busca estiver ativa
      window.addEventListener('close-products-drawer', () => {
        if ($q.platform.is.desktop) {
          drawerCategory.value = false
        }
      })

      // Listener para reabrir drawer quando busca for limpa
      window.addEventListener('open-products-drawer', () => {
        if ($q.platform.is.desktop) {
          drawerCategory.value = true
        }
      })
    })

    // Observar mudanças na rota (não executar imediatamente, pois já executa no onMounted)
    watch(() => [$route.params.productId, $route.query.categoria], loadProductFromUrl)

    // Função para enviar mensagem pelo WhatsApp
    const sendWhatsApp = () => {
      if (product.value) {
        const productName = normalizeDisplayText(product.value.name)
        const productRef = product.value.ref
        const message = encodeURIComponent(`Olá! Gostaria de mais informações sobre o produto:\n${productName} - ${productRef}`)
        window.open(`https://wa.me/5547992934775?text=${message}`, '_blank')
      }
    }

    return {
      drawer,
      drawerCategory,
      panel,
      product,
      selectedImage,
      selectedImageInfo,
      products,
      filter,
      filterCategory,
      filterCategoryFn,
      filterLaunchProducts,
      selectImage,
      categories,
      filteredCategories,
      filteredProducts,
      customFilterFn,
      pagination,
      getImageUrl,
      normalizeDisplayText,
      navigateToSection,
      shareCategory,
      shareProduct,
      productNotFound,
      redirectCountdown,
      sendWhatsApp,
      t,
      $q,
      columns: [
        { name: 'ref', label: 'Ref.', field: 'ref', align: 'left', sortable: true },
        {
          name: 'title',
          label: 'Título',
          field: 'name',
          align: 'left',
          sortable: true,
        },
        { name: 'image', label: 'Imagem', field: 'image', align: 'center' },
      ],
      getProduct,
    }
  },
})
</script>

<style>
.bg-square {
  position: absolute;
  top: 10%;
  left: 50%;
  transform: translateX(-50%);
  width: 60%;
  height: 80%;
  background: #2e3142;
  z-index: 0;
  border-radius: 25px;
  pointer-events: none;
}

.bg-square-2 {
  position: absolute;
  left: -20px;
  bottom: -40px;
  z-index: -1;
  background: #2e3142;
  width: 250px;
  height: 300px;
  border-radius: 25px;
}

.card-infos {
  border: 2px solid #121136cc;
  border-left: none;
  border-radius: 0 25px 25px 0;
  height: 60%;
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
}

.card-infos-btn-wrapper {
  display: flex;
  justify-content: center;
  position: absolute;
  left: 0;
  right: 0;
  bottom: -22px;
  z-index: 2;
}

@media (max-width: 599px) {
  .card-infos {
    border: none;
  }
}

#q-app > div > div.q-page-container > main > div.q-drawer-container > aside {
  top: 0 !important;
  bottom: 0 !important;
}

/* Forçar z-index do drawer de produtos abaixo do header */
.products-drawer {
  z-index: 500 !important;
}

.categories-drawer {
  background: linear-gradient(180deg, #fafafa 0%, #f5f5f5 100%);
  border-right: 1px solid #e0e0e0;
}

.drawer-header {
  padding: 24px 20px 16px 20px;
  background: white;
  border-bottom: 1px solid #e0e0e0;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.header-actions {
  display: flex;
  gap: 8px;
}

.search-input {
  width: 100%;
}

.categories-scroll {
  height: calc(100vh - 140px);
  width: 100%;
}

.categories-list {
  padding: 8px;
}

.category-item {
  margin-bottom: 4px;
  border-radius: 8px;
  transition: all 0.2s ease;
  background: transparent;
}

.category-item:hover {
  background: rgba(25, 118, 210, 0.08);
}

.featured-category {
  background: linear-gradient(135deg, rgba(255, 193, 7, 0.08) 0%, rgba(255, 152, 0, 0.08) 100%) !important;
  border-left: 3px solid #ffc107;
  font-weight: 600 !important;
}

.featured-category:hover {
  background: linear-gradient(135deg, rgba(255, 193, 7, 0.15) 0%, rgba(255, 152, 0, 0.15) 100%) !important;
}

.featured-category.active-category {
  background: linear-gradient(135deg, rgba(255, 193, 7, 0.2) 0%, rgba(255, 152, 0, 0.2) 100%) !important;
  border-left: 3px solid #ff9800;
}

.active-category {
  background: rgba(25, 118, 210, 0.12) !important;
  color: #1976d2 !important;
}

.category-label {
  font-weight: 500;
  text-transform: uppercase;
  font-size: 0.85rem;
  letter-spacing: 0.5px;
}

/* Products Content */
.full-height {
  height: 100vh;
  min-height: 100vh;
}

.products-container {
  display: flex;
  flex: 1;
}

.products-content {
  flex: 1;
  transition: margin-left 0.3s ease;
}

.products-content.with-drawer {
  margin-left: 320px;
  padding-left: 20px;
}

.products-panel {
  position: relative;
  z-index: 2;
  height: 100%;
  padding: 0;
}

@media (max-width: 1024px) {
  .products-content.with-drawer {
    margin-left: 0;
    padding-left: 20px;
  }
}

.image-tab {
  border: 2px solid transparent;
  border-radius: 8px;
  transition: all 0.3s ease;
  width: 38px !important;
  height: 38px !important;
  flex-shrink: 0;
  padding: 0 !important;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.image-tab .q-img {
  width: 100% !important;
  height: 100% !important;
}

.image-tab:hover {
  border-color: #2e3142;
}

.image-tab.selected {
  border-color: #1976d2;
  box-shadow: 0 2px 8px rgba(25, 118, 210, 0.3);
}

.image-tab.has-cuff-collar {
  border-color: #c10015;
  border-width: 3px;
  box-shadow: 0 2px 8px rgba(193, 0, 21, 0.4);
}

.image-tab.has-cuff-collar.selected {
  border-color: #c10015;
  box-shadow: 0 2px 12px rgba(193, 0, 21, 0.6);
}

/* Garantir que os cards tenham altura uniforme */
.q-table__grid-item {
  display: flex !important;
}

/* Error message styles */
.error-message-container {
  max-width: 400px;
  width: 100%;
  padding: 40px 24px;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 16px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.error-content {
  text-align: center;
}

.error-icon {
  margin-bottom: 16px;
  opacity: 0.7;
}

.error-title {
  font-size: 24px;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 12px;
  line-height: 1.3;
}

.error-message {
  font-size: 16px;
  color: #5a6c7d;
  margin-bottom: 24px;
  line-height: 1.5;
}

.error-countdown {
  font-size: 14px;
  font-weight: 500;
  color: #7b8794;
  margin-bottom: 16px;
}

.progress-container {
  width: 100%;
  height: 4px;
  background: #e9ecef;
  border-radius: 2px;
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
  border-radius: 2px;
  transition: width 0.3s ease;
}

@media (max-width: 599px) {
  .error-message-container {
    max-width: 90%;
    padding: 32px 20px;
  }

  .error-title {
    font-size: 20px;
  }

  .error-message {
    font-size: 14px;
  }
}

/* Mobile Filter Button */
.filter-btn-mobile {
  min-width: auto;
  padding: 8px 12px;
  font-size: 14px;
  border-radius: 20px;
  transition: all 0.2s ease;
}

.filter-btn-mobile:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(25, 118, 210, 0.2);
}

/* Close Drawer Button */
.close-drawer-btn {
  transition: all 0.2s ease;
}

.close-drawer-btn:hover {
  background: rgba(0, 0, 0, 0.1) !important;
  color: #333 !important;
}

/* Image Color Overlay */
.image-color-overlay {
  position: absolute;
  bottom: 12px;
  left: 12px;
  background: rgba(0, 0, 0, 0.85);
  padding: 10px 14px;
  border-radius: 10px;
  backdrop-filter: blur(8px);
  z-index: 10;
  display: flex;
  flex-direction: column;
  gap: 4px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.image-color-ref {
  font-size: 14px;
  font-weight: 700;
  color: #ffffff;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  line-height: 1.2;
}

.image-color-name {
  font-size: 13px;
  font-weight: 500;
  color: #f0f0f0;
  text-transform: capitalize;
  letter-spacing: 0.4px;
  line-height: 1.3;
  margin-top: 1px;
}

.image-color-type {
  font-size: 12px;
  font-weight: 400;
  color: #cccccc;
  text-transform: lowercase;
  letter-spacing: 0.3px;
  line-height: 1.3;
  font-style: italic;
  opacity: 0.9;
}

.text-red {
  color: #c10015 !important;
  font-weight: 700 !important;
  font-size: 26px !important;
}

.asterisk-red {
  position: absolute;
  left: -4px;
  top: -2px;
  color: #c10015;
  font-weight: 700;
  font-size: 28px;
  line-height: 1.4;
}

.image-color-flag {
  font-size: 11px;
  font-weight: 400;
  color: #e0e0e0;
  letter-spacing: 0.2px;
  line-height: 1.4;
  margin-top: 6px;
}

.image-color-flag.has-asterisk {
  position: relative;
  padding-top: 6px;
  border-top: 1px solid rgba(255, 255, 255, 0.15);
}

.image-color-flag:first-of-type {
  margin-top: 8px;
}
</style>
