<template>
  <q-btn-dropdown
    flat
    :label="currentLanguage.label"
    :icon="currentLanguage.icon"
    auto-close
    class="language-switcher"
  >
    <q-list>
      <q-item
        v-for="language in languages"
        :key="language.value"
        clickable
        @click="changeLanguage(language.value)"
        :class="{ 'bg-grey-2': language.value === currentLocale }"
      >
        <q-item-section avatar>
          <q-icon :name="language.icon" />
        </q-item-section>
        <q-item-section>
          <q-item-label>{{ language.label }}</q-item-label>
          <q-item-label caption>{{ language.nativeName }}</q-item-label>
        </q-item-section>
        <q-item-section side v-if="language.value === currentLocale">
          <q-icon name="check" color="primary" />
        </q-item-section>
      </q-item>
    </q-list>
  </q-btn-dropdown>
</template>

<script>
import { defineComponent, computed } from 'vue'
import { useI18n } from 'vue-i18n'

export default defineComponent({
  name: 'LanguageSwitcher',
  setup() {
    const { locale } = useI18n()

    const languages = [
      {
        value: 'pt-BR',
        label: 'Português',
        nativeName: 'Português (Brasil)',
        icon: 'img:https://flagcdn.com/32x24/br.png'
      },
      {
        value: 'en-US',
        label: 'English',
        nativeName: 'English (US)',
        icon: 'img:https://flagcdn.com/32x24/us.png'
      },
      {
        value: 'es-ES',
        label: 'Español',
        nativeName: 'Español (España)',
        icon: 'img:https://flagcdn.com/32x24/es.png'
      }
    ]

    const currentLocale = computed(() => locale.value)
    
    const currentLanguage = computed(() => {
      return languages.find(lang => lang.value === currentLocale.value) || languages[0]
    })

    const changeLanguage = (newLocale) => {
      locale.value = newLocale
      // Save to localStorage for persistence
      localStorage.setItem('language', newLocale)
    }

    // Load saved language from localStorage on component mount
    const savedLanguage = localStorage.getItem('language')
    if (savedLanguage && languages.some(lang => lang.value === savedLanguage)) {
      locale.value = savedLanguage
    }

    return {
      languages,
      currentLocale,
      currentLanguage,
      changeLanguage
    }
  }
})
</script>

<style scoped>
.language-switcher {
  min-width: 120px;
}

.language-switcher :deep(.q-icon img) {
  width: 24px;
  height: 18px;
  object-fit: cover;
  border-radius: 2px;
}
</style>