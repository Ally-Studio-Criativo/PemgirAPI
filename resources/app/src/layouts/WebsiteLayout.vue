<template>
  <q-layout view="hhh lpr fFf">
    <q-header
      class="text-white"
      :style="headerStyle"
      height-hint="98"
      style="z-index: 1000 !important"
    >
      <div v-if="isProductsPage" class="absolute-full bg-black" style="opacity: 0.3"></div>
      <q-toolbar class="q-pa-md" style="position: relative; z-index: 1001">
        <img src="/icons/Logo.png" alt="Pemgir Logo" style="height: 50px; width: auto" />

        <!-- Campo de pesquisa -->
        <div class="q-mx-md search-container-desktop">
          <q-input
            v-model="searchQuery"
            rounded outlined
            dense
            placeholder="Pesquise um produto por nome ou referência"
            bg-color="white"
            color="grey-8"
            @focus="searchFocused = true"
            @blur="handleSearchBlur"
          >
            <template v-slot:append>
              <q-icon name="search" color="grey-8" />
            </template>
          </q-input>

          <!-- Autocomplete dropdown -->
          <q-card
            v-if="searchFocused && searchResults.length > 0"
            class="search-results-dropdown"
            flat
            bordered
          >
            <q-list separator>
              <q-item
                v-for="product in searchResults"
                :key="product.id"
                clickable
                v-ripple
                @mousedown="selectProduct(product)"
                class="search-result-item"
              >
                <q-item-section avatar>
                  <q-avatar rounded size="50px">
                    <img :src="product.image" />
                  </q-avatar>
                </q-item-section>
                <q-item-section>
                  <q-item-label class="text-weight-medium">{{ product.name }}</q-item-label>
                  <q-item-label caption>
                    <span class="text-primary">{{ product.ref }}</span>
                    <span v-if="product.categoryLabel" class="q-ml-sm text-grey-7">
                      | {{ product.categoryLabel }}
                    </span>
                  </q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card>
        </div>

        <q-space />

        <div class="row q-gutter-x-md gt-sm">
          <q-btn flat :label="t('menu.home')" @click="navigateToSection('hero-section')" />
          <q-btn flat :label="t('menu.products')" @click="$router.push('/produtos')" />
          <q-btn flat :label="t('menu.about')" @click="navigateToSection('about')" />
          <q-btn flat :label="t('menu.testimonials')" @click="navigateToSection('testimony')" />
          <q-btn flat :label="t('menu.contact')" @click="navigateToSection('footer')" />
          <LanguageSwitcher />
        </div>

        <!-- Menu mobile -->
        <q-btn
          v-if="$q.platform.is.mobile"
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="leftDrawerOpen = !leftDrawerOpen"
        />
      </q-toolbar>
    </q-header>

    <!-- Drawer para mobile -->
    <q-drawer
      v-model="leftDrawerOpen"
      side="left"
      overlay
      behavior="mobile"
      class="mobile-menu-drawer"
      width="280"
    >
      <!-- Header do Menu -->
      <div class="mobile-menu-header">
        <div class="menu-header-content">
          <h2 class="menu-title">{{ t('ui.menu') }}</h2>
          <q-btn
            flat
            round
            dense
            icon="close"
            size="lg"
            class="close-btn"
            @click="leftDrawerOpen = false"
          />
        </div>
      </div>

      <!-- Lista de itens do menu -->
      <div class="mobile-menu-content">
        <!-- Campo de pesquisa mobile -->
        <div class="q-pa-md">
          <div style="position: relative">
            <q-input
              v-model="searchQuery"
              dense
              rounded
              placeholder="Pesquise por nome ou ref do produto"
              bg-color="white"
              @focus="searchFocused = true"
              @blur="handleSearchBlur"
            >
              <template v-slot:append>
                <q-icon name="search" color="grey-8" />
              </template>
            </q-input>

            <!-- Autocomplete dropdown mobile -->
            <q-card
              v-if="searchFocused && searchResults.length > 0"
              class="search-results-dropdown"
              flat
              bordered
            >
              <q-list separator>
                <q-item
                  v-for="product in searchResults"
                  :key="product.id"
                  clickable
                  v-ripple
                  @mousedown="selectProduct(product)"
                  class="search-result-item"
                >
                  <q-item-section avatar>
                    <q-avatar rounded size="50px">
                      <img :src="product.image" />
                    </q-avatar>
                  </q-item-section>
                  <q-item-section>
                    <q-item-label class="text-weight-medium">{{ product.name }}</q-item-label>
                    <q-item-label caption>
                      <span class="text-primary">{{ product.ref }}</span>
                      <span v-if="product.categoryLabel" class="q-ml-sm text-grey-7">
                        | {{ product.categoryLabel }}
                      </span>
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-card>
          </div>
        </div>

        <q-list class="mobile-menu-list">
          <q-item
            class="mobile-menu-item"
            clickable
            v-close-popup
            @click="navigateToSection('hero-section')"
          >
            <q-item-section>
              <q-item-label class="menu-item-text">{{ t('menu.home') }}</q-item-label>
            </q-item-section>
          </q-item>

          <q-separator class="menu-separator" />

          <q-item
            clickable
            v-close-popup
            @click="$router.push('/produtos')"
            class="mobile-menu-item"
          >
            <q-item-section>
              <q-item-label class="menu-item-text">{{ t('menu.products') }}</q-item-label>
            </q-item-section>
          </q-item>

          <q-separator class="menu-separator" />

          <q-item
            clickable
            v-close-popup
            @click="navigateToSection('about')"
            class="mobile-menu-item"
          >
            <q-item-section>
              <q-item-label class="menu-item-text">{{ t('menu.about') }}</q-item-label>
            </q-item-section>
          </q-item>

          <q-separator class="menu-separator" />

          <q-item
            clickable
            v-close-popup
            @click="navigateToSection('testimony')"
            class="mobile-menu-item"
          >
            <q-item-section>
              <q-item-label class="menu-item-text">{{ t('menu.testimonials') }}</q-item-label>
            </q-item-section>
          </q-item>

          <q-separator class="menu-separator" />

          <q-item
            clickable
            v-close-popup
            @click="navigateToSection('footer')"
            class="mobile-menu-item"
          >
            <q-item-section>
              <q-item-label class="menu-item-text">{{ t('menu.contact') }}</q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </div>
    </q-drawer>

    <q-page-container>
      <router-view />

      <!-- Botão flutuante do WhatsApp -->
      <div class="whatsapp-float-container">
        <q-btn
          fab
          icon="img:/icons/whatsapp.svg"
          color="green"
          size="lg"
          @click="openWhatsApp"
          class="whatsapp-float-btn"
        >
          <q-tooltip anchor="center left" self="center right" :offset="[10, 0]" class="bg-green text-body2">
            Fale conosco pelo WhatsApp
          </q-tooltip>
        </q-btn>
      </div>
    </q-page-container>

    <q-footer
      id="footer"
      style="
        background-image: url('/images/rodape.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
      "
    >
      <div class="absolute-full bg-black" style="opacity: 0.1"></div>

      <div
        class="container text-white"
        style="position: relative; z-index: 2; padding: 50px 0 !important"
      >
        <div
          :class="`row justify-center ${$q.platform.is.desktop ? 'q-col-gutter-md' : 'q-col-gutter-y-xl'}`"
        >
          <!-- Coluna esquerda - Logo -->
          <div class="col-12 col-md-1 flex flex-center">
            <img
              src="/icons/Logo.png"
              alt="Pemgir Logo"
              :style="`height: ${$q.platform.is.desktop ? '80px' : '80%'}; width: auto`"
            />
          </div>

          <div v-if="$q.platform.is.desktop" class="col-12 col-md-2 flex flex-center">
            <q-list class="text-white">
              <q-item
                clickable
                :class="$q.platform.is.desktop ? '' : 'q-pa-none'"
                @click="navigateToSection('hero-section')"
              >
                <q-item-section>
                  <div class="text-h6">{{ t('menu.home').toUpperCase() }}</div>
                </q-item-section>
              </q-item>

              <q-separator />

              <q-item
                clickable
                :class="$q.platform.is.desktop ? '' : 'q-pa-none'"
                @click="$router.push('/produtos')"
              >
                <q-item-section>
                  <div class="text-h6">PRODUTOS</div>
                </q-item-section>
              </q-item>

              <q-separator />

              <q-item
                clickable
                :class="$q.platform.is.desktop ? '' : 'q-pa-none'"
                @click="navigateToSection('about')"
              >
                <q-item-section>
                  <div class="text-h6">{{ t('menu.about').toUpperCase() }}</div>
                </q-item-section>
              </q-item>

              <q-separator />

              <q-item
                clickable
                :class="$q.platform.is.desktop ? '' : 'q-pa-none'"
                @click="navigateToSection('testimony')"
              >
                <q-item-section>
                  <div class="text-h6">{{ t('menu.testimonials').toUpperCase() }}</div>
                </q-item-section>
              </q-item>

              <q-separator />

              <q-item
                clickable
                :class="$q.platform.is.desktop ? '' : 'q-pa-none'"
                @click="navigateToSection('footer')"
              >
                <q-item-section>
                  <div class="text-h6">{{ t('menu.contact').toUpperCase() }}</div>
                </q-item-section>
              </q-item>
            </q-list>
          </div>

          <!-- Coluna central - Informações de compra -->
          <div class="col-12 col-md-2 flex flex-center">
            <div class="glass-effect">
              <div>
                <!-- BNDES Card -->
                <div class="q-mt-none">
                  <div
                    class="bndes-card q-pa-md"
                    style="
                      background: rgba(255, 255, 255, 0.08);
                      border-radius: 8px;
                      border: 1px solid rgba(255, 255, 255, 0.2);
                    "
                  >
                    <div class="row items-center q-gutter-sm">
                      <div class="col-auto">
                        <img
                          src="/images/bnds.png"
                          alt="BNDES Logo"
                          style="height: 45px; width: auto"
                        />
                      </div>
                      <div class="col">
                        <div class="text-body1 text-weight-light text-white">
                          {{ t('footer.buy') }}
                        </div>
                        <div class="text-h6 text-white text-bold">{{ t('footer.bndes') }}</div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- NFe Download -->
                <div class="q-pa-md">
                  <a
                    href="http://www.nfe.fazenda.gov.br/portal/download.aspx?tipoconteudo=s/eylu5e+y4"
                    target="_blank"
                    class="text-white no-text-decoration"
                  >
                    <div class="row items-center q-gutter-sm">
                      <div class="col-auto">
                        <q-icon name="download" color="white" size="24px" />
                      </div>
                      <div class="col">
                        <div class="text-body1 text-white text-bold">
                          {{ t('footer.download') }} {{ t('footer.free') }}
                        </div>
                        <div class="text-body2 text-weight-light text-grey-3">
                          {{ t('footer.nfe') }}
                        </div>
                      </div>
                    </div>
                  </a>
                </div>

                <!-- Painel Administrativo -->
                <div class="q-pa-md">
                  <router-link
                    to="/acessar"
                    class="text-white no-text-decoration"
                  >
                    <div class="row items-center q-gutter-sm">
                      <div class="col-auto">
                        <q-icon name="admin_panel_settings" color="white" size="24px" />
                      </div>
                      <div class="col">
                        <div class="text-body1 text-white text-bold">
                          {{ t('footer.adminPanel') }}
                        </div>
                      </div>
                    </div>
                  </router-link>
                </div>
              </div>
            </div>
          </div>

          <!-- Coluna direita - Contatos -->
          <div class="col-12 col-md-3 flex flex-center">
            <q-list :class="`text-body1 ${$q.platform.is.desktop ? '' : ' q-px-xl q-gutter-y-md'}`">
              <q-item class="column">
                <div class="row items-center">
                  <q-icon name="phone" color="white" size="18px" />
                  <div class="q-ml-md">47 3308-3500</div>
                </div>

                <div class="row items-center">
                  <q-icon name="phone" color="white" size="18px" />
                  <div class="q-ml-md">47 9 9293-4775</div>
                </div>

                <div class="row items-center">
                  <q-icon name="email" color="white" size="18px" />
                  <div class="q-ml-md">pemgir@pemgir.com.br</div>
                </div>
              </q-item>

              <q-item clickable @click="openLinkedIn">
                <q-item-section side>
                  <q-icon name="img:https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/linkedin.svg" color="white" size="18px" style="filter: brightness(0) invert(1);" />
                </q-item-section>
                <q-item-section>
                  <div class="text-body1">
                    LinkedIn
                  </div>
                </q-item-section>
              </q-item>

              <q-item>
                <q-item-section side>
                  <q-icon name="place" color="white" size="18px" />
                </q-item-section>
                <q-item-section>
                  <div class="text-body1">
                    Rua Luiz Gonzaga Werner,<br />
                    45 - Bateas C.Postal 1600 -<br />
                    88355-365 - Brusque/SC
                  </div>
                </q-item-section>
              </q-item>

              <q-item>
                <q-item-section side>
                  <q-icon name="business" color="white" size="18px" />
                </q-item-section>
                <q-item-section>
                  <div class="text-grey-3">
                    00.087.731/0001-16<br />
                    Ind. e Com. de Malhas Pemgir Ltda
                  </div>
                </q-item-section>
              </q-item>

              <q-item>
                <q-item-section side>
                  <q-icon name="security" color="white" size="18px" />
                </q-item-section>
                <q-item-section>
                  <div class="text-grey-3">
                    {{ t('footer.data') }}
                  </div>
                </q-item-section>
              </q-item>
            </q-list>
          </div>
        </div>

        <div class="col-12 q-pa-md text-center">
          <div class="text-body2 text-grey-4 q-pa-sm">
            {{ t('footer.developed') }} v.1.3
          </div>
        </div>
      </div>
    </q-footer>

  </q-layout>
</template>

<script>
import { defineComponent, ref, computed, watch } from 'vue'
import { useQuasar } from 'quasar'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import LanguageSwitcher from 'src/components/LanguageSwitcher.vue'
import { productService } from 'src/services/productService'

export default defineComponent({
  name: 'WebsiteLayout',
  components: {
    LanguageSwitcher,
  },
  setup() {
    const $q = useQuasar()
    const { t } = useI18n()
    const $router = useRouter()
    const leftDrawerOpen = ref(false)
    const searchQuery = ref('')
    const searchFocused = ref(false)
    const searchResults = ref([])
    const allProducts = ref([])

    const scrollTo = (id, offset = 0) => {
      const el = document.getElementById(id)
      if (el) {
        const y = el.getBoundingClientRect().top + window.pageYOffset + offset
        window.scrollTo({ top: y, behavior: 'smooth' })
      }
    }

    const navigateToSection = (section) => {
      // Se estivermos na página de produtos, primeiro navegar para home
      if ($router.currentRoute.value.path !== '/') {
        $router.push('/').then(() => {
          // Aguarda um pequeno delay para a página carregar
          setTimeout(() => {
            scrollTo(section, section === 'testimony' ? -100 : 0)
          }, 100)
        })
      } else {
        // Se já estivermos na home, apenas fazer scroll
        scrollTo(section, section === 'testimony' ? -100 : 0)
      }
    }

    // Computed property to check if we're on products page
    const isProductsPage = computed(() => {
      return $router.currentRoute.value.path.startsWith('/produtos')
    })

    // Computed property for header style
    const headerStyle = computed(() => {
      if (isProductsPage.value) {
        return {
          backgroundImage: "url('/images/rodape.jpg')",
          backgroundSize: 'cover',
          backgroundPosition: 'center',
          backgroundRepeat: 'no-repeat',
          position: 'relative',
        }
      }
      return { background: 'transparent' }
    })

    const openWhatsApp = () => {
      const message = encodeURIComponent('Olá! Gostaria de mais informações sobre os produtos.')
      window.open(`https://wa.me/5547992934775?text=${message}`, '_blank')
    }

    const openLinkedIn = () => {
      window.open('https://www.linkedin.com/in/pemgir-malhas-80a719395/', '_blank')
    }

    // Função para carregar todos os produtos
    const loadAllProducts = async () => {
      try {
        const products = await productService.getAll()

        // Função helper para limpar o path da imagem
        const cleanImagePath = (path) => {
          if (!path) return null
          return path.replace(/^storage\//, '')
        }

        allProducts.value = products.map(p => ({
          id: p.id,
          name: p.name,
          ref: p.reference || '',
          categoryLabel: p.category?.name || '',
          image: p.images && p.images.length > 0
            ? `${process.env.API_URL_IMG}/${cleanImagePath(p.images[0].path)}`
            : null,
        }))
      } catch (error) {
        console.error('Erro ao carregar produtos para busca:', error)
      }
    }

    // Carregar produtos ao montar o componente
    loadAllProducts()

    // Watch para buscar produtos quando o usuário digita
    watch(searchQuery, (newValue) => {
      if (!newValue || newValue.trim().length < 2) {
        searchResults.value = []
        return
      }

      const query = newValue.toLowerCase().trim()
      searchResults.value = allProducts.value
        .filter(product => {
          const name = product.name.toLowerCase()
          const ref = product.ref.toLowerCase()
          return name.includes(query) || ref.includes(query)
        })
        .slice(0, 5) // Limitar a 5 resultados
    })

    // Watch para fechar/abrir drawer quando houver resultados de busca
    watch([searchFocused, searchResults], ([focused, results]) => {
      // Se o campo está focado e há resultados, emitir evento para fechar drawer
      if (focused && results.length > 0) {
        window.dispatchEvent(new CustomEvent('close-products-drawer'))
      } else if (!focused || results.length === 0) {
        // Se perdeu o foco ou não há resultados, reabrir drawer
        window.dispatchEvent(new CustomEvent('open-products-drawer'))
      }
    })

    // Função para selecionar um produto
    const selectProduct = (product) => {
      searchQuery.value = ''
      searchFocused.value = false
      searchResults.value = []
      leftDrawerOpen.value = false // Fechar menu mobile se estiver aberto
      $router.push(`/produtos/${product.id}`)
    }

    // Função para lidar com o blur do campo de busca
    const handleSearchBlur = () => {
      // Delay para permitir o clique no item
      setTimeout(() => {
        searchFocused.value = false
      }, 200)
    }

    return {
      leftDrawerOpen,
      $q,
      t,
      navigateToSection,
      isProductsPage,
      headerStyle,
      openWhatsApp,
      openLinkedIn,
      searchQuery,
      searchFocused,
      searchResults,
      selectProduct,
      handleSearchBlur,
    }
  },
})
</script>

<style scoped>
.glass-effect {
  background: rgba(0, 0, 0, 0.32);
  border-radius: 16px;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(0, 0, 0, 0.63);
}

.q-page-container {
  padding: 0 !important;
}

.no-text-decoration {
  text-decoration: none;
}

.no-text-decoration:hover {
  text-decoration: none;
  opacity: 0.8;
}

.bndes-card {
  transition: all 0.3s ease;
  cursor: pointer;
}

.bndes-card:hover {
  background: rgba(255, 255, 255, 0.15) !important;
  transform: translateY(-1px);
}

/* Mobile Menu Styles */
.mobile-menu-drawer {
  background: #ffffff !important;
  box-shadow: 0 0 40px rgba(0, 0, 0, 0.1) !important;
  color: #212529 !important;
}

.mobile-menu-drawer .q-item-label {
  color: #212529 !important;
}

.mobile-menu-drawer * {
  color: #212529 !important;
}

.mobile-menu-drawer .q-item {
  color: #212529 !important;
}

.mobile-menu-drawer .q-item-label {
  color: #212529 !important;
}

/* Force dark text on all menu elements */
.mobile-menu-drawer,
.mobile-menu-drawer .q-list,
.mobile-menu-drawer .q-item,
.mobile-menu-drawer .q-item-section,
.mobile-menu-drawer .q-item-label,
.mobile-menu-drawer h2,
.mobile-menu-drawer .q-btn {
  color: #212529 !important;
}

.mobile-menu-drawer .q-btn .q-icon {
  color: #6c757d !important;
}

.mobile-menu-header {
  background: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
  padding: 0;
  position: sticky;
  top: 0;
  z-index: 1000;
}

.menu-header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  min-height: 80px;
}

.menu-title {
  font-size: 24px;
  font-weight: 600;
  color: #f8f9fa !important;
  margin: 0;
  letter-spacing: -0.5px;
}

.close-btn {
  color: #6c757d !important;
  background: rgba(0, 0, 0, 0.05);
  border-radius: 50%;
  transition: all 0.2s ease;
}

.close-btn:hover {
  background: rgba(0, 0, 0, 0.1) !important;
  color: #212529 !important;
  transform: scale(1.05);
}

.mobile-menu-content {
  padding: 0;
  height: calc(100vh - 80px);
  overflow-y: auto;
}

.mobile-menu-list {
  padding: 0;
}

.mobile-menu-item {
  padding: 20px 24px;
  min-height: 64px;
  transition: all 0.2s ease;
  border: none;
  background: transparent;
  color: red;
}

.mobile-menu-item:hover {
  background: #f8f9fa !important;
}

.mobile-menu-item:active {
  background: #e9ecef !important;
}

.menu-item-text {
  font-size: 18px !important;
  font-weight: 500 !important;
  color: #212529 !important;
  letter-spacing: -0.3px;
  text-transform: capitalize;
}

.menu-separator {
  background: #e9ecef !important;
  margin: 0 24px;
  opacity: 0.6;
}

/* WhatsApp Floating Button */
.whatsapp-float-container {
  position: fixed !important;
  bottom: 18px !important;
  right: 18px !important;
  z-index: 9999 !important;
}

.whatsapp-float-btn {
  box-shadow: 0 4px 12px rgba(37, 211, 102, 0.5) !important;
  transition: all 0.3s ease !important;
}

.whatsapp-float-btn:hover {
  box-shadow: 0 6px 20px rgba(37, 211, 102, 0.7) !important;
  transform: scale(1.1) !important;
}

.whatsapp-float-btn img {
  filter: brightness(0) invert(1);
}

/* Fix para remover top e bottom indesejados no drawer */
#q-app > div > div.q-page-container > main > div.q-drawer-container > aside {
  top: 0 !important;
  bottom: 0 !important;
}

/* Search Container Desktop */
.search-container-desktop {
  width: 450px;
  position: relative;
}

@media (max-width: 1280px) {
  .search-container-desktop {
    width: 350px;
  }
}

/* Search Input Styles */
.search-input-header {
  background: white;
  border-radius: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
  transition: box-shadow 0.3s ease;
}

.search-input-header:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.search-input-header.q-field--focused {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
}

.search-input-header .q-field__control {
  height: 40px;
  border-radius: 24px;
  padding: 0 16px;
}

.search-input-header .q-field__native {
  color: #212529 !important;
  font-weight: 500;
  padding-left: 8px;
}

.search-input-header input::placeholder {
  color: #6c757d !important;
  opacity: 0.8;
  font-weight: 400;
}

.search-results-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  margin-top: 4px;
  max-height: 400px;
  overflow-y: auto;
  background: white;
  z-index: 10000 !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-radius: 8px;
}

@media (max-width: 599px) {
  .search-results-dropdown {
    position: fixed;
    top: 80px;
    left: 0;
    right: 0;
    width: 100vw;
    margin: 0;
    border-radius: 0;
    max-height: calc(100vh - 80px);
  }
}

.search-result-item {
  transition: background 0.2s ease;
  padding: 12px 16px;
}

.search-result-item:hover {
  background: #f5f5f5;
}

.search-result-item .q-item__label {
  color: #212529 !important;
  font-size: 14px;
}

.search-result-item .q-item__label--caption {
  margin-top: 4px;
  color: #6c757d !important;
  font-size: 13px;
}

.search-result-item .q-item__label--caption .text-primary {
  color: #1976d2 !important;
  font-weight: 600;
}

.search-result-item .q-item__label--caption .text-grey-7 {
  color: #6c757d !important;
}
</style>
