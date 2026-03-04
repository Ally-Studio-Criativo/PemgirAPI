<template>
  <q-page class="q-pa-md">
    <div class="row items-center justify-between q-mb-xs">
      <div>
        <div class="text-h5 text-weight-bold">Usuários</div>
        <div class="text-body2 text-grey-7">Gerencie os usuários do sistema</div>
      </div>
      <q-btn
        color="primary"
        icon="add"
        label="Novo Usuário"
        @click="openDialog(null)"
      />
    </div>

    <!-- Table -->
    <q-table
      :rows="users"
      :columns="columns"
      row-key="id"
      :loading="loading"
      flat
      class="q-mt-md clickable-rows"
    >
      <template v-slot:body="props">
        <q-tr :props="props" @click="openDialog(props.row)" class="cursor-pointer">
          <q-td v-for="col in props.cols" :key="col.name" :props="props">
            {{ col.value }}
          </q-td>
        </q-tr>
      </template>
    </q-table>

    <!-- Dialog Form -->
    <q-dialog v-model="showDialog" persistent>
      <q-card style="min-width: 500px; max-width: 600px">
        <!-- Header -->
        <q-card-section class="q-pb-sm">
          <div class="row items-start justify-between">
            <div>
              <div class="text-h5 text-weight-medium q-mb-xs">
                {{ isEdit ? 'Editar Usuário' : 'Criar Novo Usuário' }}
              </div>
              <div class="text-body2 text-grey-7">
                {{ isEdit ? 'Atualize as informações do usuário abaixo' : 'Preencha os dados para criar um novo usuário' }}
              </div>
            </div>
            <q-btn
              v-if="isEdit"
              flat
              round
              dense
              icon="delete"
              color="negative"
              @click="confirmDelete(form)"
            >
              <q-tooltip>Excluir usuário</q-tooltip>
            </q-btn>
          </div>
        </q-card-section>

        <q-separator />

        <!-- Form -->
        <q-card-section class="q-pt-lg q-px-lg q-pb-md">
          <q-form @submit="saveUser">
            <div class="row q-col-gutter-md">
              <!-- Nome -->
              <div class="col-12">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">Nome completo</div>
                <q-input
                  v-model="form.name"
                  dense
                  placeholder="Digite o nome completo"
                  filled
                  hide-bottom-space
                  :rules="[val => !!val || 'Nome é obrigatório']"
                >
                  <template v-slot:prepend>
                    <q-icon name="person" color="grey-6" />
                  </template>
                </q-input>
              </div>

              <!-- E-mail -->
              <div class="col-12">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">E-mail</div>
                <q-input
                  dense
                  v-model="form.email"
                  type="email"
                  placeholder="exemplo@email.com"
                  filled
                  hide-bottom-space
                  :rules="[
                    val => !!val || 'E-mail é obrigatório',
                    val => /.+@.+\..+/.test(val) || 'E-mail inválido'
                  ]"
                >
                  <template v-slot:prepend>
                    <q-icon name="email" color="grey-6" />
                  </template>
                </q-input>
              </div>

              <!-- Senha -->
              <div class="col-12">
                <div class="text-subtitle2 text-weight-medium q-mb-xs">
                  {{ isEdit ? 'Nova Senha' : 'Senha' }}
                </div>
                <div v-if="isEdit" class="text-caption text-grey-6 q-mb-sm">
                  Deixe em branco para manter a senha atual
                </div>
                <q-input
                  v-model="form.password"
                  :placeholder="isEdit ? 'Digite a nova senha (opcional)' : 'Digite uma senha segura'"
                  :type="isPwd ? 'password' : 'text'"
                  dense
                  filled
                  hide-bottom-space
                  :rules="!isEdit ? [val => !!val || 'Senha é obrigatória'] : []"
                >
                  <template v-slot:prepend>
                    <q-icon name="lock" color="grey-6" />
                  </template>
                  <template v-slot:append>
                    <q-icon
                      :name="isPwd ? 'visibility_off' : 'visibility'"
                      class="cursor-pointer"
                      color="grey-6"
                      @click="isPwd = !isPwd"
                    />
                  </template>
                </q-input>
              </div>
            </div>
          </q-form>
        </q-card-section>

        <q-separator />

        <!-- Actions -->
        <q-card-actions align="right" class="q-px-lg q-py-md">
          <q-btn
            label="Cancelar"
            color="grey-7"
            flat
            padding="sm lg"
            @click="showDialog = false"
          />
          <q-btn
            label="Salvar"
            color="primary"
            unelevated
            padding="sm lg"
            :loading="saving"
            @click="saveUser"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { userService } from 'src/services/userService'

export default defineComponent({
  name: 'UsersPage',

  setup() {
    const $q = useQuasar()

    const users = ref([])
    const loading = ref(false)
    const saving = ref(false)
    const showDialog = ref(false)
    const isEdit = ref(false)
    const isPwd = ref(true)

    const form = ref({
      id: null,
      name: '',
      email: '',
      password: ''
    })

    const columns = [
      {
        name: 'id',
        label: 'ID',
        field: 'id',
        align: 'left',
        sortable: true
      },
      {
        name: 'name',
        label: 'Nome',
        field: 'name',
        align: 'left',
        sortable: true
      },
      {
        name: 'email',
        label: 'E-mail',
        field: 'email',
        align: 'left',
        sortable: true
      }
    ]

    const openDialog = (user) => {
      if (user) {
        isEdit.value = true
        form.value = {
          id: user.id,
          name: user.name,
          email: user.email,
          password: ''
        }
      } else {
        isEdit.value = false
        form.value = {
          id: null,
          name: '',
          email: '',
          password: ''
        }
      }
      showDialog.value = true
    }

    const saveUser = async () => {
      saving.value = true

      try {
        const userData = {
          name: form.value.name,
          email: form.value.email
        }

        // Adicionar senha apenas se preenchida
        if (form.value.password) {
          userData.password = form.value.password
          userData.password_confirmation = form.value.password
        }

        if (isEdit.value) {
          // Atualizar usuário existente
          await userService.update(form.value.id, userData)
        } else {
          // Criar novo usuário
          await userService.create(userData)
        }

        $q.notify({
          type: 'positive',
          message: `Usuário ${isEdit.value ? 'atualizado' : 'criado'} com sucesso!`,
          position: 'top'
        })

        showDialog.value = false
        await loadUsers()
      } catch (error) {
        const errorMessage = error.response?.data?.message ||
                           error.response?.data?.errors?.email?.[0] ||
                           'Erro ao salvar usuário'

        $q.notify({
          type: 'negative',
          message: errorMessage,
          position: 'top'
        })
      } finally {
        saving.value = false
      }
    }

    const confirmDelete = (user) => {
      $q.dialog({
        title: 'Confirmar Exclusão',
        message: `Deseja realmente excluir o usuário "${user.name}"?`,
        cancel: {
          label: 'Cancelar',
          flat: true
        },
        ok: {
          label: 'Excluir',
          color: 'negative'
        },
        persistent: true
      }).onOk(async () => {
        await deleteUser(user.id)
      })
    }

    const deleteUser = async (userId) => {
      try {
        await userService.delete(userId)

        $q.notify({
          type: 'positive',
          message: 'Usuário excluído com sucesso!',
          position: 'top'
        })

        await loadUsers()
      } catch {
        $q.notify({
          type: 'negative',
          message: 'Erro ao excluir usuário',
          position: 'top'
        })
      }
    }

    const loadUsers = async () => {
      loading.value = true

      try {
        users.value = await userService.getAll()
      } catch {
        $q.notify({
          type: 'negative',
          message: 'Erro ao carregar usuários',
          position: 'top'
        })
      } finally {
        loading.value = false
      }
    }

    // Carregar dados ao montar
    onMounted(() => {
      loadUsers()
    })

    return {
      users,
      columns,
      loading,
      saving,
      showDialog,
      isEdit,
      isPwd,
      form,
      openDialog,
      saveUser,
      confirmDelete
    }
  }
})
</script>

<style scoped>
.clickable-rows :deep(tbody tr) {
  transition: background-color 0.2s;
}

.clickable-rows :deep(tbody tr:hover) {
  background-color: rgba(0, 0, 0, 0.03);
}
</style>
