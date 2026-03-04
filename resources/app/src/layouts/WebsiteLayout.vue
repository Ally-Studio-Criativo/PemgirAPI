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
    </q-page-container>

    <q-footer
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
import { defineComponent, ref, computed } from 'vue'
import { useQuasar } from 'quasar'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import LanguageSwitcher from 'src/components/LanguageSwitcher.vue'

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

    return {
      leftDrawerOpen,
      $q,
      t,
      navigateToSection,
      isProductsPage,
      headerStyle,
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
</style>
