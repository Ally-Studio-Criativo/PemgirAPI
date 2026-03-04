import { defineRouter } from '#q-app/wrappers'
import { createRouter, createMemoryHistory, createWebHistory, createWebHashHistory } from 'vue-router'
import routes from './routes'
import { useAuthStore } from 'src/stores/auth-store'

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Router instance.
 */

export default defineRouter(function (/* { store, ssrContext } */) {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : (process.env.VUE_ROUTER_MODE === 'history' ? createWebHistory : createWebHashHistory)

  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,

    // Leave this as is and make changes in quasar.conf.js instead!
    // quasar.conf.js -> build -> vueRouterMode
    // quasar.conf.js -> build -> publicPath
    history: createHistory(process.env.VUE_ROUTER_BASE)
  })

  // Navigation guard for protected routes
  Router.beforeEach((to, from, next) => {
    const authStore = useAuthStore()

    // Initialize auth from localStorage if not already done
    if (!authStore.user && !authStore.token) {
      authStore.initAuth()
    }

    // Check if route requires authentication
    if (to.matched.some(record => record.meta.requiresAuth)) {
      if (!authStore.isAuthenticated) {
        // Redirect to login if not authenticated
        next({
          path: '/acessar',
          query: { redirect: to.fullPath }
        })
      } else {
        next()
      }
    } else if (to.path === '/acessar' && authStore.isAuthenticated) {
      // Redirect to admin if already authenticated and trying to access login
      next('/admin')
    } else {
      next()
    }
  })

  return Router
})
