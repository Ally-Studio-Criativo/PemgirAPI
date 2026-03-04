<template>
  <q-page class="q-pa-md">
    <div class="row items-center justify-between q-mb-xs">
      <div>
        <div class="text-h5 text-weight-bold">Produtos</div>
        <div class="text-body2 text-grey-7">Gerencie os produtos do catálogo</div>
      </div>
      <div class="row q-gutter-sm items-center">
        <q-select
          v-model="selectedCategory"
          :options="categoryOptions"
          option-value="value"
          option-label="label"
          emit-value
          map-options
          label="Filtrar por categoria"
          filled
          dense
          clearable
          style="min-width: 220px"
          @update:model-value="loadProducts"
        >
          <template v-slot:prepend>
            <q-icon name="filter_list" color="grey-6" />
          </template>
        </q-select>
        <q-btn-toggle
          v-model="launchFilter"
          toggle-color="primary"
          :options="[
            { label: 'Todos', value: null },
            { label: 'Lançamentos', value: true }
          ]"
          @update:model-value="loadProducts"
        />
        <q-btn color="primary" icon="add" label="Novo Produto" @click="openProductDialog(null)" />
      </div>
    </div>

    <div v-if="!loading" class="row q-col-gutter-md q-mt-sm">
      <div v-for="product in products" :key="product.id" class="col-12 col-sm-6 col-md-4 col-lg-3">
        <q-card bordered flat class="product-card">
          <q-img v-if="product.images && product.images.length > 0" :src="getImageUrl(product.images[0].path)" style="height: 200px">
            <q-badge color="dark" text-color="white" :label="`${product.images.length} ${product.images.length === 1 ? 'foto' : 'fotos'}`" class="absolute-bottom-right q-ma-sm" style="padding: 10px !important;" />
          </q-img>
          <div v-else class="bg-grey-3 flex flex-center" style="height: 200px">
            <q-icon name="image" size="64px" color="grey-5" />
          </div>

          <q-card-section>
            <div class="text-weight-bold text-body2 ellipsis-2-lines" style="min-height: 40px">{{ product.name }}</div>
            <div class="text-caption text-grey-7 q-mt-xs">Ref: {{ product.reference || 'N/A' }}</div>
            <div class="text-caption text-grey-7">
              {{ product.categories && product.categories.length > 0
                ? product.categories.map(c => c.name).join(', ')
                : (product.category?.name || 'Sem categoria') }}
            </div>
            <q-badge v-if="product.is_launch" color="orange" text-color="white" label="LANÇAMENTO" class="q-mt-xs" />
          </q-card-section>

          <q-separator />
          <q-card-actions align="right" class="q-px-md q-py-sm">
            <q-btn flat size="sm" icon="photo_library" label="Imagens" color="primary" @click="openImagesDialog(product)" no-caps />
            <q-btn flat size="sm" icon="edit" label="Editar" color="primary" @click="openProductDialog(product)" no-caps />
            <q-btn flat size="sm" icon="delete" color="negative" @click="confirmDelete(product)" no-caps />
          </q-card-actions>
        </q-card>
      </div>

      <div v-if="products.length === 0" class="col-12 text-center q-pa-xl text-grey-6">Nenhum produto cadastrado</div>
    </div>

    <div v-else class="row q-col-gutter-md">
      <div v-for="i in 8" :key="i" class="col-12 col-sm-6 col-md-4 col-lg-3">
        <q-skeleton height="200px" />
        <q-skeleton height="80px" class="q-mt-sm" />
      </div>
    </div>

    <q-dialog v-model="showProductDialog" persistent>
      <q-card style="min-width: 600px; max-width: 800px; max-height: 70vh; display: flex; flex-direction: column;">
        <!-- Header fixo -->
        <q-card-section class="q-pb-sm" style="flex-shrink: 0;">
          <div class="text-h5 text-weight-medium q-mb-xs">
            {{ isEdit ? 'Editar Produto' : 'Criar Novo Produto' }}
          </div>
          <div class="text-body2 text-grey-7">
            {{ isEdit ? 'Atualize as informações do produto abaixo' : 'Preencha os dados para criar um novo produto' }}
          </div>
        </q-card-section>

        <q-separator />

        <!-- Form com scroll -->
        <q-card-section class="q-pt-lg q-px-lg q-pb-md" style="flex: 1; overflow-y: auto;">
          <q-form @submit="saveProduct">
            <div class="row q-col-gutter-md">
              <!-- Nome do Produto -->
              <div class="col-12">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Nome do produto</div>
                <q-input
                  v-model="productForm.name"
                  placeholder="Digite o nome do produto"
                  filled
                  hide-bottom-space
                  dense
                  :rules="[val => !!val || 'Nome é obrigatório']"
                >
                  <template v-slot:prepend>
                    <q-icon name="inventory_2" color="grey-6" />
                  </template>
                </q-input>
              </div>

              <!-- Categorias (múltipla seleção) -->
              <div class="col-12">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Categorias</div>
                <q-select
                  v-model="productForm.category_ids"
                  :options="categories"
                  option-value="id"
                  option-label="name"
                  emit-value
                  map-options
                  multiple
                  use-chips
                  placeholder="Selecione uma ou mais categorias"
                  filled
                  dense
                  hide-bottom-space
                  :rules="[val => val && val.length > 0 || 'Selecione pelo menos uma categoria']"
                >
                  <template v-slot:prepend>
                    <q-icon name="category" color="grey-6" />
                  </template>
                </q-select>
              </div>

              <!-- Referência e Ordem -->
              <div class="col-10">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Referência</div>
                <q-input
                  v-model="productForm.reference"
                  placeholder="Código de referência"
                  filled
                  dense
                  hide-bottom-space
                >
                  <template v-slot:prepend>
                    <q-icon name="tag" color="grey-6" />
                  </template>
                </q-input>
              </div>

              <div class="col-2">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Ordem</div>
                <q-input
                  v-model.number="productForm.order"
                  type="number"
                  placeholder="0"
                  filled
                  dense
                  hide-bottom-space
                >
                  <template v-slot:prepend>
                    <q-icon name="sort" color="grey-6" />
                  </template>
                </q-input>
              </div>

              <!-- Composição -->
              <div class="col-12">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Composição</div>
                <div class="text-caption text-grey-6 q-mb-sm">Ex: 100% Algodão</div>
                <q-input
                  v-model="productForm.composition"
                  placeholder="Digite a composição do produto"
                  filled
                  dense
                  hide-bottom-space
                >
                  <template v-slot:prepend>
                    <q-icon name="layers" color="grey-6" />
                  </template>
                </q-input>
              </div>

              <!-- Gramatura, Largura e Rendimento -->
              <div class="col-4">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Gramatura</div>
                <q-input
                  v-model.number="productForm.gramatura"
                  type="number"
                  placeholder="0"
                  filled
                  dense
                  hide-bottom-space
                  suffix="g/m²"
                >
                  <template v-slot:prepend>
                    <q-icon name="straighten" color="grey-6" />
                  </template>
                </q-input>
              </div>

              <div class="col-4">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Largura</div>
                <q-input
                  v-model="productForm.width"
                  placeholder="1,80m"
                  filled
                  dense
                  hide-bottom-space
                >
                  <template v-slot:prepend>
                    <q-icon name="width_normal" color="grey-6" />
                  </template>
                </q-input>
              </div>

              <div class="col-4">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Rendimento</div>
                <q-input
                  v-model="productForm.yield"
                  placeholder="2,85m/kg"
                  filled
                  dense
                  hide-bottom-space
                >
                  <template v-slot:prepend>
                    <q-icon name="speed" color="grey-6" />
                  </template>
                </q-input>
              </div>

              <!-- Observações -->
              <div class="col-12">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Observações</div>
                <div class="text-caption text-grey-6 q-mb-sm">Informações adicionais sobre o produto</div>
                <q-input
                  v-model="productForm.observations"
                  placeholder="Digite observações sobre o produto"
                  filled
                  dense
                  type="textarea"
                  rows="3"
                  hide-bottom-space
                >
                  <template v-slot:prepend>
                    <q-icon name="notes" color="grey-6" />
                  </template>
                </q-input>
              </div>

              <!-- Lançamento -->
              <div class="col-12">
                <q-checkbox
                  v-model="productForm.is_launch"
                  label="Marcar como Lançamento"
                  color="orange"
                />
              </div>
            </div>
          </q-form>
        </q-card-section>

        <q-separator />

        <!-- Actions fixas -->
        <q-card-actions align="right" class="q-px-lg q-py-md" style="flex-shrink: 0;">
          <q-btn
            label="Cancelar"
            color="grey-7"
            flat
            padding="sm lg"
            @click="closeProductDialog"
          />
          <q-btn
            label="Salvar"
            color="primary"
            unelevated
            padding="sm lg"
            :loading="saving"
            @click="saveProduct"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="showImagesDialog" persistent>
      <q-card style="width: 90vw; max-width: 1200px; height: 85vh; display: flex; flex-direction: column;">
        <!-- Header fixo -->
        <q-card-section class="row items-center q-pb-sm" style="flex-shrink: 0;">
          <div class="text-h6">Gerenciar Imagens - {{ currentProduct?.name }}</div>
          <q-space />
          <q-btn icon="close" flat round dense @click="closeImagesDialog" />
        </q-card-section>

        <q-separator />

        <!-- Section com scroll -->
        <q-card-section style="flex: 1; overflow-y: auto;">
          <div class="row items-center justify-between q-mb-md">
            <div class="text-subtitle2">Imagens do Produto ({{ productImages.length }})</div>
            <q-btn color="primary" icon="add" label="Adicionar Imagem" @click="showAddImageDialog = true" unelevated />
          </div>

          <div v-if="loadingImages" class="row q-col-gutter-md">
            <div v-for="i in 4" :key="i" class="col-6 col-md-3"><q-skeleton height="200px" /></div>
          </div>

          <div v-else-if="productImages.length > 0" class="row q-col-gutter-md">
            <div v-for="(image, index) in productImages" :key="image.id" class="col-6 col-md-3">
              <q-card class="image-card">
                <div style="position: relative; width: 100%; height: 250px;">
                  <q-img :src="getImageUrl(image.path)" style="width: 100%; height: 100%;" fit="cover">
                    <q-badge color="dark" text-color="white" :label="`#${index + 1}`" class="absolute-bottom-right q-ma-sm" style="padding: 4px 10px !important;" />
                  </q-img>
                </div>
                <q-card-section class="q-pa-sm">
                  <div class="text-caption text-weight-bold">{{ image.ref || 'Sem REF' }}</div>
                  <div class="text-caption text-grey-7">{{ image.color_name || 'Sem nome' }}</div>
                  <div class="text-caption text-grey-6">{{ image.image_type || 'Sem tipo' }}</div>
                </q-card-section>
                <q-card-actions align="right">
                  <q-btn flat dense icon="edit" label="Editar" color="primary" size="sm" @click="editImageMetadata(image)" no-caps />
                  <q-btn flat dense icon="delete" color="negative" size="sm" @click="deleteProductImage(image)"><q-tooltip>Excluir</q-tooltip></q-btn>
                </q-card-actions>
              </q-card>
            </div>
          </div>

          <div v-else class="text-center q-pa-xl text-grey-6">Nenhuma imagem adicionada ainda</div>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="showAddImageDialog" persistent>
      <q-card style="min-width: 500px; max-width: 600px;">
        <q-card-section class="row items-center q-pb-sm">
          <div class="text-h6">Adicionar Nova Imagem</div>
          <q-space />
          <q-btn icon="close" flat round dense @click="showAddImageDialog = false" />
        </q-card-section>

        <q-separator />

        <q-card-section>
          <q-file v-model="newImage.file" label="Selecionar Imagem" filled accept="image/*" @update:model-value="onImageSelected">
            <template v-slot:prepend><q-icon name="image" /></template>
          </q-file>

          <div v-if="newImagePreview" class="q-mt-md">
            <div class="text-caption text-grey-7 q-mb-sm">Preview e Informações:</div>
            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-5">
                <q-img :src="newImagePreview" style="max-height: 250px; border-radius: 8px;" class="bg-grey-2" fit="contain" />
              </div>
              <div class="col-12 col-md-7">
                <q-input v-model="newImage.ref" label="REF (Código)" filled dense class="q-mb-sm" hint="Ex: 0613" />
                <q-input v-model="newImage.color_name" label="Nome/Cor" filled dense class="q-mb-sm" hint="Ex: MARINHO" />
                <q-input v-model="newImage.image_type" label="Tipo" filled dense class="q-mb-sm" hint="Ex: esc_fte" />
                <div class="q-mt-md">
                  <div class="q-mb-sm">
                    <q-checkbox v-model="newImage.in_2027_palette" label="Disponível na cartela 2027" color="primary" size="md" />
                  </div>
                  <div>
                    <q-input
                      v-model="newImage.has_cuff_collar"
                      label="Gola e punho"
                      placeholder="Descreva sobre gola e punho"
                      filled
                      dense
                      class="q-mb-sm"
                      :outlined="!!newImage.has_cuff_collar"
                      :class="{'text-field-with-asterisk': !!newImage.has_cuff_collar}"
                    >
                      <template v-slot:before v-if="newImage.has_cuff_collar">
                        <span class="text-red text-h6">*</span>
                      </template>
                      <template v-slot:hint v-if="newImage.has_cuff_collar">
                        <span>{{ newImage.has_cuff_collar }}</span>
                      </template>
                    </q-input>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </q-card-section>

        <q-separator />

        <q-card-actions align="right" class="q-px-md q-py-md">
          <q-btn label="Cancelar" color="grey-7" flat @click="showAddImageDialog = false" />
          <q-btn label="Adicionar Imagem" color="primary" icon="add" unelevated :loading="uploading" :disable="!newImage.file" @click="uploadImage" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="showEditImageDialog" persistent>
      <q-card style="min-width: 400px">
        <q-card-section><div class="text-h6">Editar Informações da Imagem</div></q-card-section>
        <q-card-section class="q-pt-none">
          <q-form @submit="saveImageMetadata">
            <q-input v-model="editingImage.ref" label="REF (Código)" filled dense class="q-mb-md" />
            <q-input v-model="editingImage.color_name" label="Nome/Cor" filled dense class="q-mb-md" />
            <q-input v-model="editingImage.image_type" label="Tipo" filled dense class="q-mb-md" />
            <div class="q-mb-md">
              <div>
                <q-input
                  v-model="editingImage.has_cuff_collar"
                  label="Observações"
                  placeholder="Uma observação sobre o produto"
                  filled
                  dense
                  class="q-mb-sm"
                  :outlined="!!editingImage.has_cuff_collar"
                  :class="{'text-field-with-asterisk': !!editingImage.has_cuff_collar}"
                >
                  <template v-slot:before v-if="editingImage.has_cuff_collar">
                    <span class="text-red text-h6">*</span>
                  </template>
                  <template v-slot:hint v-if="editingImage.has_cuff_collar">
                    <span>{{ editingImage.has_cuff_collar }}</span>
                  </template>
                </q-input>
              </div>
              <div class="q-mb-sm">
                <q-checkbox v-model="editingImage.in_2027_palette" label="Disponível na cartela 2027" color="primary" size="md" />
              </div>
            </div>
            <div class="row justify-end q-gutter-sm">
              <q-btn label="Cancelar" color="grey-7" flat @click="showEditImageDialog = false" />
              <q-btn type="submit" label="Salvar" color="primary" :loading="saving" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { productService } from 'src/services/productService'
import { categoryService } from 'src/services/categoryService'

export default defineComponent({
  name: 'ProductsPage',
  setup() {
    const $q = useQuasar()
    const STORAGE_URL = process.env.API_URL_IMG
    const products = ref([])
    const categories = ref([])
    const categoryOptions = ref([])
    const selectedCategory = ref(null)
    const launchFilter = ref(null)
    const loading = ref(false)
    const saving = ref(false)
    const uploading = ref(false)
    const loadingImages = ref(false)
    const showProductDialog = ref(false)
    const showImagesDialog = ref(false)
    const showAddImageDialog = ref(false)
    const showEditImageDialog = ref(false)
    const isEdit = ref(false)
    const currentProduct = ref(null)
    const productImages = ref([])
    const productForm = ref({ id: null, name: '', category_id: null, category_ids: [], reference: '', composition: '', gramatura: null, width: '', yield: '', observations: '', order: 0, is_launch: false })
    const newImage = ref({ file: null, ref: '', color_name: '', image_type: '', in_2027_palette: false, has_cuff_collar: '' })
    const newImagePreview = ref(null)
    const editingImage = ref({ id: null, ref: '', color_name: '', image_type: '', in_2027_palette: false, has_cuff_collar: '' })

    const getImageUrl = (imagePath) => imagePath ? `${STORAGE_URL}/${imagePath}` : null

    const loadCategories = async () => {
      try {
        categories.value = await categoryService.getAll()
        categoryOptions.value = [...categories.value.map(c => ({ label: c.name, value: c.id })), { label: 'Todas', value: null }]
      } catch (error) { console.error('Erro ao carregar categorias:', error) }
    }

    const loadProducts = async () => {
      loading.value = true
      try {
        const params = {}
        if (selectedCategory.value) params.category_id = selectedCategory.value
        if (launchFilter.value !== null) params.is_launch = launchFilter.value
        products.value = await productService.getAll(params)
      } catch (error) {
        console.error('Erro ao carregar produtos:', error)
        $q.notify({ type: 'negative', message: 'Erro ao carregar produtos', position: 'top' })
      } finally { loading.value = false }
    }

    const openProductDialog = (product) => {
      if (product) {
        isEdit.value = true
        // Converter categories array para category_ids
        const categoryIds = product.categories?.map(c => c.id) || []
        productForm.value = {
          ...product,
          category_ids: categoryIds.length > 0 ? categoryIds : (product.category_id ? [product.category_id] : [])
        }
      } else {
        isEdit.value = false
        productForm.value = { id: null, name: '', category_id: null, category_ids: [], reference: '', composition: '', gramatura: null, width: '', yield: '', observations: '', order: 0, is_launch: false }
      }
      showProductDialog.value = true
    }

    const closeProductDialog = () => { showProductDialog.value = false }

    const saveProduct = async () => {
      saving.value = true
      try {
        let savedProduct
        if (isEdit.value) {
          savedProduct = await productService.update(productForm.value.id, productForm.value)
        } else {
          savedProduct = await productService.create(productForm.value)
        }

        $q.notify({ type: 'positive', message: `Produto ${isEdit.value ? 'atualizado' : 'criado'} com sucesso!`, position: 'top' })
        closeProductDialog()
        await loadProducts()

        // Se foi criação de novo produto, perguntar se quer adicionar fotos
        if (!isEdit.value && savedProduct) {
          $q.dialog({
            title: 'Adicionar Fotos',
            message: 'Deseja adicionar fotos ao produto agora?',
            cancel: { label: 'Não', flat: true, color: 'grey-7' },
            ok: { label: 'Sim', color: 'primary', unelevated: true },
            persistent: false
          }).onOk(async () => {
            // Abrir dialog de gerenciar imagens
            await openImagesDialog(savedProduct)
          })
        }
      } catch (error) {
        console.error('Erro ao salvar produto:', error)
        $q.notify({ type: 'negative', message: error.response?.data?.message || 'Erro ao salvar produto', position: 'top' })
      } finally { saving.value = false }
    }

    const confirmDelete = (product) => {
      $q.dialog({ title: 'Confirmar Exclusão', message: `Deseja realmente excluir o produto "${product.name}"?`, cancel: { label: 'Cancelar', flat: true }, ok: { label: 'Excluir', color: 'negative' }, persistent: true }).onOk(async () => {
        try {
          await productService.delete(product.id)
          $q.notify({ type: 'positive', message: 'Produto excluído com sucesso!', position: 'top' })
          await loadProducts()
        } catch (error) {
          console.error('Erro ao excluir produto:', error)
          $q.notify({ type: 'negative', message: 'Erro ao excluir produto', position: 'top' })
        }
      })
    }

    const openImagesDialog = async (product) => {
      currentProduct.value = product
      showImagesDialog.value = true
      await loadProductImages()
    }

    const closeImagesDialog = () => {
      showImagesDialog.value = false
      currentProduct.value = null
      productImages.value = []
      newImage.value = { file: null, ref: '', color_name: '', image_type: '', in_2027_palette: false, has_cuff_collar: '' }
      newImagePreview.value = null
    }

    const loadProductImages = async () => {
      if (!currentProduct.value) return
      loadingImages.value = true
      try { productImages.value = await productService.getImages(currentProduct.value.id) }
      catch (error) { console.error('Erro ao carregar imagens:', error) }
      finally { loadingImages.value = false }
    }

    const onImageSelected = (file) => {
      if (file) {
        const reader = new FileReader()
        reader.onload = (e) => { newImagePreview.value = e.target.result }
        reader.readAsDataURL(file)
      } else { newImagePreview.value = null }
    }

    const uploadImage = async () => {
      if (!newImage.value.file) {
        $q.notify({ type: 'warning', message: 'Selecione uma imagem', position: 'top' })
        return
      }
      uploading.value = true
      try {
        await productService.uploadImage(currentProduct.value.id, {
          image: newImage.value.file,
          ref: newImage.value.ref,
          color_name: newImage.value.color_name,
          image_type: newImage.value.image_type,
          in_2027_palette: newImage.value.in_2027_palette ? 1 : 0,
          has_cuff_collar: newImage.value.has_cuff_collar || null
        })
        $q.notify({ type: 'positive', message: 'Imagem adicionada com sucesso!', position: 'top' })
        newImage.value = { file: null, ref: '', color_name: '', image_type: '', in_2027_palette: false, has_cuff_collar: '' }
        newImagePreview.value = null
        showAddImageDialog.value = false
        await loadProductImages()
        await loadProducts()
      } catch (error) {
        console.error('Erro ao enviar imagem:', error)
        $q.notify({ type: 'negative', message: 'Erro ao enviar imagem', position: 'top' })
      } finally { uploading.value = false }
    }

    const editImageMetadata = (image) => {
      editingImage.value = { ...image }
      showEditImageDialog.value = true
    }

    const saveImageMetadata = async () => {
      saving.value = true
      try {
        await productService.updateImage(currentProduct.value.id, editingImage.value.id, {
          ref: editingImage.value.ref,
          color_name: editingImage.value.color_name,
          image_type: editingImage.value.image_type,
          in_2027_palette: editingImage.value.in_2027_palette ? 1 : 0,
          has_cuff_collar: editingImage.value.has_cuff_collar || null
        })
        $q.notify({ type: 'positive', message: 'Informações atualizadas com sucesso!', position: 'top' })
        showEditImageDialog.value = false
        await loadProductImages()
      } catch (error) {
        console.error('Erro ao atualizar imagem:', error)
        $q.notify({ type: 'negative', message: 'Erro ao atualizar informações', position: 'top' })
      } finally { saving.value = false }
    }

    const deleteProductImage = (image) => {
      $q.dialog({ title: 'Confirmar Exclusão', message: 'Deseja realmente excluir esta imagem?', cancel: { label: 'Cancelar', flat: true }, ok: { label: 'Excluir', color: 'negative' }, persistent: true }).onOk(async () => {
        try {
          await productService.deleteImage(currentProduct.value.id, image.id)
          $q.notify({ type: 'positive', message: 'Imagem excluída com sucesso!', position: 'top' })
          await loadProductImages()
          await loadProducts()
        } catch (error) {
          console.error('Erro ao excluir imagem:', error)
          $q.notify({ type: 'negative', message: 'Erro ao excluir imagem', position: 'top' })
        }
      })
    }

    onMounted(async () => {
      await loadCategories()
      await loadProducts()
    })

    return { products, categories, categoryOptions, selectedCategory, launchFilter, loading, saving, uploading, loadingImages, showProductDialog, showImagesDialog, showAddImageDialog, showEditImageDialog, isEdit, currentProduct, productImages, productForm, newImage, newImagePreview, editingImage, getImageUrl, loadProducts, openProductDialog, closeProductDialog, saveProduct, confirmDelete, openImagesDialog, closeImagesDialog, onImageSelected, uploadImage, editImageMetadata, saveImageMetadata, deleteProductImage }
  }
})
</script>

<style scoped>
.product-card {
  height: 100%;
}
.image-card {
  transition: transform 0.2s;
}
.image-card:hover {
  transform: scale(1.02);
}
.ellipsis-2-lines {
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
}
</style>
