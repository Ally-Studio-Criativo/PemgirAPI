<template>
  <q-page class="login-page flex flex-center">
    <div class="login-container">
      <!-- Logo/Brand Section -->
      <div class="text-center q-mb-md">
        <img src="/icons/Logo.png" alt="Pemgir Logo" style="height: 120px; width: auto" />
      </div>

      <!-- Login Card -->
      <q-card class="login-card shadow-10">
        <q-card-section class="q-pa-lg">
          <div class="text-h5 text-bold text-grey-9 q-mb-md">{{ t('login.title') }}</div>
          <div class="text-body2 text-grey-6 q-mb-lg">{{ t('login.description') }}</div>

          <q-form @submit="handleLogin" class="q-gutter-md">
            <!-- Email Input -->
            <q-input
              v-model="form.email"
              :label="t('login.email')"
              type="email"
              outlined
              dense
              :rules="[
                val => !!val || t('login.validation.emailRequired'),
                val => isValidEmail(val) || t('login.validation.emailInvalid')
              ]"
            >
              <template v-slot:prepend>
                <q-icon name="email" color="primary" />
              </template>
            </q-input>

            <!-- Password Input -->
            <q-input
              v-model="form.password"
              :label="t('login.password')"
              :type="isPwd ? 'password' : 'text'"
              outlined
              dense
              :rules="[val => !!val || t('login.validation.passwordRequired')]"
            >
              <template v-slot:prepend>
                <q-icon name="lock" color="primary" />
              </template>
              <template v-slot:append>
                <q-icon
                  :name="isPwd ? 'visibility_off' : 'visibility'"
                  class="cursor-pointer"
                  @click="isPwd = !isPwd"
                />
              </template>
            </q-input>

            <!-- Submit Button -->
            <q-btn
              type="submit"
              color="primary"
              :label="t('login.button')"
              class="full-width"
              size="md"
              rounded
              no-caps
              :loading="loading"
            />
          </q-form>
        </q-card-section>
      </q-card>

      <!-- Back to Home -->
      <div class="text-center q-mt-lg">
        <q-btn
          flat
          rounded
          no-caps
          color="white"
          text-color="white"
          icon="arrow_back"
          :label="t('login.backToHome')"
          @click="$router.push('/')"
        />
      </div>
    </div>
  </q-page>
</template>

<script>
import { defineComponent, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useAuthStore } from 'src/stores/auth-store'

export default defineComponent({
  name: 'LoginPage',
  setup() {
    const { t } = useI18n()
    const $router = useRouter()
    const $q = useQuasar()
    const authStore = useAuthStore()

    const form = ref({
      email: '',
      password: ''
    })

    const isPwd = ref(true)

    const isValidEmail = (email) => {
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      return emailPattern.test(email)
    }

    const handleLogin = async () => {
      // Clear previous errors
      authStore.clearError()

      const result = await authStore.login({
        email: form.value.email,
        password: form.value.password
      })

      if (result.success) {
        $q.notify({
          type: 'positive',
          message: t('login.success'),
          position: 'top',
          timeout: 2000
        })

        // Redirecionar para painel admin após login bem-sucedido
        setTimeout(() => {
          $router.push('/admin')
        }, 500)
      } else {
        $q.notify({
          type: 'negative',
          message: result.error,
          position: 'top',
          timeout: 3000
        })
      }
    }

    return {
      form,
      isPwd,
      loading: authStore.isLoading,
      isValidEmail,
      handleLogin,
      t
    }
  }
})
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  position: relative;
  overflow: hidden;
}

.login-page::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image:
    radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
  pointer-events: none;
}

.login-container {
  width: 100%;
  max-width: 450px;
  padding: 20px;
  position: relative;
  z-index: 1;
}

.login-card {
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.98);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

@media (max-width: 600px) {
  .login-container {
    padding: 16px;
  }

  .login-card {
    border-radius: 12px;
  }
}
</style>
