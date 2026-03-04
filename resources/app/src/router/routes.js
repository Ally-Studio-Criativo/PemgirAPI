const routes = [
  {
    path: '/',
    component: () => import('layouts/WebsiteLayout.vue'),
    children: [
      { path: '/', component: () => import('pages/IndexPage.vue') },
      { path: '/produtos/:productId?', component: () => import('pages/Products.vue') },
    ],
  },

  // Login page with clear layout
  {
    path: '/acessar',
    component: () => import('layouts/ClearLayout.vue'),
    children: [
      { path: '', component: () => import('pages/LoginPage.vue') },
    ],
  },

  // Admin routes
  {
    path: '/admin',
    component: () => import('layouts/AdminLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      { path: '', component: () => import('pages/admin/DashboardPage.vue') },
      { path: 'usuarios', component: () => import('pages/admin/UsersPage.vue') },
      { path: 'categorias', component: () => import('pages/admin/CategoriesPage.vue') },
      { path: 'produtos', component: () => import('pages/admin/ProductsPage.vue') },
      { path: 'cores', component: () => import('pages/admin/ColorsPage.vue') },
      { path: 'paletas', component: () => import('pages/admin/PalettesPage.vue') },
      { path: 'campanhas', component: () => import('pages/admin/CampaignsPage.vue') },
      { path: 'landing-page', component: () => import('pages/admin/LandingPageManager.vue') },
    ],
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
]

export default routes
