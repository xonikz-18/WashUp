<template>
  <div class="dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar">

      <div class="sidebar-logo">
        <img
          src="../assets/droplet.png"
          alt=""
          class="logo-droplet"
        />

        <img
          src="../assets/washupblue.png"
          alt="WashUP"
          class="logo-text"
        />
      </div>

      <nav class="sidebar-nav">

        <button
          type="button"
          class="nav-item"
          @click="goTo('Dashboard')"
        >
          <img
            src="../assets/icondashboardblue.png"
            alt=""
          />
          <span>Dashboard</span>
        </button>

        <button
          type="button"
          class="nav-item"
          @click="goTo('Orders')"
        >
          <img
            src="../assets/iconordergray.png"
            alt=""
          />
          <span>Orders</span>
        </button>

        <button
          type="button"
          class="nav-item active"
        >
          <img
            src="../assets/iconservicesgray.png"
            alt=""
          />
          <span>Services &amp; Add-ons</span>
        </button>

        <button
          type="button"
          class="nav-item"
          @click="goTo('Reports')"
        >
          <img
            src="../assets/iconreportsgray.png"
            alt=""
          />
          <span>Reports</span>
        </button>

      </nav>

      <div class="sidebar-bottom">

        <button
          class="nav-item logout"
          type="button"
          @click="handleLogout"
        >
          <img
            src="../assets/iconlogoutgray.png"
            alt=""
          />
          <span>Logout</span>
        </button>

      </div>

    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

      <!-- TOP HEADER -->
      <TopHeader />

      <!-- PAGE CONTENT -->
      <div class="content">

        <!-- PAGE HEADER -->
        <div class="page-header">

          <div>
            <h1>Services &amp; Add-ons</h1>

            <p>
              Manage core services and optional add-ons used across customer orders.
            </p>
          </div>

        </div>

        <!-- =========================
             SERVICES
        ========================== -->

        <section class="management-section">

          <div class="section-title-row">

            <h2>Services</h2>

            <button
              type="button"
              class="add-button"
              @click="openAddService"
            >
              <span class="plus">+</span>
              Add Service
            </button>

          </div>

          <div class="management-card">

            <div class="table-wrapper">

              <table>

                <thead>
                  <tr>
                    <th>Service Name</th>
                    <th>Price per kg (₱)</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody>

                  <tr
                    v-for="service in services"
                    :key="service.id"
                  >

                    <td class="name-cell">
                      {{ service.service_name }}
                    </td>

                    <td>
                      ₱{{ formatPrice(service.price_per_kg) }}/kg
                    </td>

                    <td>
                      <span
                        class="status-badge"
                        :class="service.is_active ? 'active' : 'inactive'"
                      >
                        <span class="status-dot"></span>
                        {{ service.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </td>

                    <td>
                      <button
                        type="button"
                        class="edit-button"
                        @click="openEditService(service)"
                      >
                        Edit
                      </button>
                    </td>

                  </tr>

                  <tr v-if="services.length === 0">
                    <td
                      colspan="4"
                      class="empty-state"
                    >
                      No services found.
                    </td>
                  </tr>

                </tbody>

              </table>

            </div>

          </div>

        </section>


        <!-- =========================
             ADD-ONS
        ========================== -->

        <section class="management-section addons-section">

          <div class="section-title-row">

            <h2>Add-ons</h2>

            <button
              type="button"
              class="add-button"
              @click="openAddAddon"
            >
              <span class="plus">+</span>
              Add Add-on
            </button>

          </div>

          <div class="management-card">

            <div class="table-wrapper">

              <table>

                <thead>
                  <tr>
                    <th>Add-on Name</th>
                    <th>Price (₱)</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody>

                  <tr
                    v-for="addon in addons"
                    :key="addon.id"
                  >

                    <td class="name-cell">
                      {{ addon.addon_name }}
                    </td>

                    <td>
                      ₱{{ formatPrice(addon.addon_price) }}
                    </td>

                    <td>
                      <span
                        class="status-badge"
                        :class="addon.is_active ? 'active' : 'inactive'"
                      >
                        <span class="status-dot"></span>
                        {{ addon.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </td>

                    <td>
                      <button
                        type="button"
                        class="edit-button"
                        @click="openEditAddon(addon)"
                      >
                        Edit
                      </button>
                    </td>

                  </tr>

                  <tr v-if="addons.length === 0">
                    <td
                      colspan="4"
                      class="empty-state"
                    >
                      No add-ons found.
                    </td>
                  </tr>

                </tbody>

              </table>

            </div>

          </div>

        </section>

        <!-- ERROR MESSAGE -->
        <div
          v-if="errorMessage"
          class="error-message"
        >
          {{ errorMessage }}
        </div>

      </div>

    </main>


    <!-- =========================
         MODAL
    ========================== -->

    <div
      v-if="showModal"
      class="modal-overlay"
      @click.self="closeModal"
    >

      <div class="modal">

        <div class="modal-header">

          <div>
            <h2>
              {{ modalMode === 'add'
                ? (modalType === 'service'
                  ? 'Add Service'
                  : 'Add Add-on')
                : (modalType === 'service'
                  ? 'Edit Service'
                  : 'Edit Add-on')
              }}
            </h2>

            <p>
              {{
                modalMode === 'add'
                  ? 'Add a new option to your WashUp system.'
                  : 'Update the details of this option.'
              }}
            </p>
          </div>

          <button
            type="button"
            class="close-button"
            @click="closeModal"
          >
            ×
          </button>

        </div>


        <form
          class="modal-form"
          @submit.prevent="saveItem"
        >

          <!-- SERVICE NAME -->
          <div class="form-group">

            <label>
              {{ modalType === 'service'
                ? 'Service Name'
                : 'Add-on Name'
              }}
            </label>

            <input
              v-model="form.name"
              type="text"
              :placeholder="
                modalType === 'service'
                  ? 'e.g. Wash & Dry'
                  : 'e.g. Fabric Conditioner'
              "
              required
            />

          </div>


          <!-- PRICE -->
          <div class="form-group">

            <label>
              {{ modalType === 'service'
                ? 'Price per kg (₱)'
                : 'Price (₱)'
              }}
            </label>

            <input
              v-model="form.price"
              type="number"
              min="0"
              step="0.01"
              placeholder="0.00"
              required
            />

          </div>


          <!-- STATUS -->
          <div class="form-group">

            <label>Status</label>

            <select
              v-model="form.is_active"
              required
            >
              <option :value="true">
                Active
              </option>

              <option :value="false">
                Inactive
              </option>
            </select>

          </div>


          <!-- FORM ACTIONS -->
          <div class="modal-actions">

            <button
              type="button"
              class="cancel-button"
              @click="closeModal"
            >
              Cancel
            </button>

            <button
              type="submit"
              class="save-button"
              :disabled="saving"
            >
              {{ saving ? 'Saving...' : 'Save' }}
            </button>

          </div>

        </form>

      </div>

    </div>

  </div>
</template>


<script setup>
import { computed, ref, watch } from 'vue'
import TopHeader from './TopHeader.vue'

const props = defineProps({
  services: {
    type: Array,
    default: () => []
  },
  addons: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits([
  'navigate',
  'logout',
  'services-updated',
  'addons-updated'
])

const services = ref([...props.services])
const addons = ref([...props.addons])
const saving = ref(false)
const errorMessage = ref('')

watch(() => props.services, (newServices) => {
  services.value = [...newServices]
}, { deep: true })

watch(() => props.addons, (newAddons) => {
  addons.value = [...newAddons]
}, { deep: true })

const showModal = ref(false)
const modalType = ref('service')
const modalMode = ref('add')
const editingId = ref(null)

const form = ref({
  name: '',
  price: '',
  is_active: true
})

const currentDate = computed(() => {
  return new Date().toLocaleDateString('en-PH', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    weekday: 'long'
  })
})

const getToken = () => {
  return localStorage.getItem('washup_token')
}

const getHeaders = () => {
  const token = getToken()

  return {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    Authorization: `Bearer ${token}`
  }
}

const formatPrice = (price) => {
  const value = Number(price || 0)

  return value.toLocaleString('en-PH', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const openAddService = () => {
  modalType.value = 'service'
  modalMode.value = 'add'
  editingId.value = null

  form.value = {
    name: '',
    price: '',
    is_active: true
  }

  errorMessage.value = ''
  showModal.value = true
}

const openEditService = (service) => {
  modalType.value = 'service'
  modalMode.value = 'edit'
  editingId.value = service.id

  form.value = {
    name: service.service_name,
    price: service.price_per_kg,
    is_active: Boolean(service.is_active)
  }

  errorMessage.value = ''
  showModal.value = true
}

const openAddAddon = () => {
  modalType.value = 'addon'
  modalMode.value = 'add'
  editingId.value = null

  form.value = {
    name: '',
    price: '',
    is_active: true
  }

  errorMessage.value = ''
  showModal.value = true
}

const openEditAddon = (addon) => {
  modalType.value = 'addon'
  modalMode.value = 'edit'
  editingId.value = addon.id

  form.value = {
    name: addon.addon_name,
    price: addon.addon_price,
    is_active: Boolean(addon.is_active)
  }

  errorMessage.value = ''
  showModal.value = true
}

const closeModal = () => {
  if (saving.value) return
  showModal.value = false
}

const saveItem = async () => {
  if (saving.value) return

  const name = form.value.name.trim()
  const price = Number(form.value.price)

  if (!name) {
    errorMessage.value = 'Please enter a name.'
    return
  }

  if (Number.isNaN(price) || price < 0) {
    errorMessage.value = 'Please enter a valid price.'
    return
  }

  saving.value = true
  errorMessage.value = ''

  try {
    const isService = modalType.value === 'service'
    const baseUrl = isService ? '/api/services' : '/api/addons'
    const url = modalMode.value === 'edit'
      ? `${baseUrl}/${editingId.value}`
      : baseUrl

    const body = isService
      ? {
          service_name: name,
          price_per_kg: price,
          is_active: form.value.is_active
        }
      : {
          addon_name: name,
          addon_price: price,
          is_active: form.value.is_active
        }

    const response = await fetch(url, {
      method: modalMode.value === 'edit' ? 'PATCH' : 'POST',
      headers: getHeaders(),
      body: JSON.stringify(body)
    })

    const data = await response.json()

    if (!response.ok) {
      let message = 'Unable to save changes.'

      if (data?.message) message = data.message

      if (data?.errors) {
        const firstError = Object.values(data.errors).flat()[0]
        if (firstError) message = firstError
      }

      throw new Error(message)
    }

    if (isService) {
      if (modalMode.value === 'edit') {
        const index = services.value.findIndex(
          service => Number(service.id) === Number(editingId.value)
        )
        if (index !== -1) services.value[index] = data
      } else {
        services.value = [data, ...services.value]
      }

      emit('services-updated', [...services.value])
    } else {
      if (modalMode.value === 'edit') {
        const index = addons.value.findIndex(
          addon => Number(addon.id) === Number(editingId.value)
        )
        if (index !== -1) addons.value[index] = data
      } else {
        addons.value = [data, ...addons.value]
      }

      emit('addons-updated', [...addons.value])
    }

    showModal.value = false
  } catch (error) {
    console.error('Save error:', error)
    errorMessage.value = error.message || 'Unable to save changes.'
  } finally {
    saving.value = false
  }
}

const goTo = (page) => {
  emit('navigate', page)
}

const handleLogout = async () => {
  const token = localStorage.getItem('washup_token')

  try {
    if (token) {
      await fetch('/api/logout', {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${token}`
        }
      })
    }
  } catch (error) {
    console.error('Logout error:', error)
  } finally {
    localStorage.removeItem('washup_token')
    localStorage.removeItem('washup_user')
    emit('logout')
  }
}
</script>


<style scoped>

* {
  box-sizing: border-box;
}


/* =========================
   APP LAYOUT
========================= */

.dashboard {
  display: flex;
  width: 100%;
  min-height: 100vh;
  background: #f7f9fb;
  color: #172033;
}


/* =========================
   SIDEBAR
========================= */

.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;

  width: 218px;

  display: flex;
  flex-direction: column;

  background: #ffffff;

  border-right: 1px solid #e4e9ef;

  z-index: 10;
}

.sidebar-logo {
  height: 84px;

  display: flex;
  align-items: center;

  padding: 0 22px;

  border-bottom: 1px solid #e4e9ef;
}

.logo-droplet {
  width: 35px;
  height: 35px;

  object-fit: contain;

  margin-right: 7px;
}

.logo-text {
  width: 108px;
  height: auto;

  object-fit: contain;
}

.sidebar-nav {
  padding: 20px 10px;
}

.nav-item {
  width: 100%;
  height: 40px;

  display: flex;
  align-items: center;

  padding: 0 12px;
  margin-bottom: 4px;

  border: none;
  border-radius: 7px;

  background: transparent;

  color: #52627a;

  font-family: inherit;
  font-size: 12px;

  text-align: left;

  cursor: pointer;

  transition:
    background 0.15s ease,
    color 0.15s ease;
}

.nav-item img {
  width: 15px;
  height: 15px;

  object-fit: contain;

  margin-right: 11px;
}

.nav-item:hover {
  background: #f1f7f9;
}

.nav-item.active {
  background: #edf8fa;
  color: #172c47;

  font-weight: 500;
}

.nav-item.active img {
  opacity: 1;
}

.sidebar-bottom {
  margin-top: auto;

  padding: 15px 10px 20px;

  border-top: 1px solid #e4e9ef;
}

.nav-item.logout {
  margin: 0;
}


/* =========================
   MAIN CONTENT
========================= */

.main-content {
  width: calc(100% - 218px);
  min-height: 100vh;

  margin-left: 218px;
}


/* =========================
   HEADER
========================= */

.top-header {
  height: 84px;

  display: flex;
  align-items: center;
  justify-content: space-between;

  padding: 0 32px;

  background: #ffffff;

  border-bottom: 1px solid #e4e9ef;
}

.date {
  font-size: 10px;
  color: #64748b;
}

.user-info {
  display: flex;
  align-items: center;

  gap: 10px;
}

.user-details {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.user-details strong {
  font-size: 11px;
  color: #172033;
}

.user-details span {
  margin-top: 2px;

  font-size: 9px;
  color: #8090a5;
}

.user-avatar {
  width: 28px;
  height: 28px;

  display: flex;
  align-items: center;
  justify-content: center;

  border-radius: 50%;

  background: #dff3f7;
  color: #217f98;

  font-size: 11px;
}


/* =========================
   CONTENT
========================= */

.content {
  padding: 32px 30px 50px;
}

.page-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;

  margin-bottom: 25px;
}

.page-header h1 {
  margin: 0;

  font-size: 24px;
  line-height: 1.2;

  font-weight: 600;

  color: #172033;
}

.page-header p {
  margin: 7px 0 0;

  font-size: 12px;

  color: #70819a;
}


/* =========================
   SECTION
========================= */

.management-section {
  margin-bottom: 27px;
}

.addons-section {
  margin-top: 4px;
}

.section-title-row {
  display: flex;
  align-items: center;
  justify-content: space-between;

  margin-bottom: 14px;
}

.section-title-row h2 {
  margin: 0;

  font-size: 18px;
  line-height: 1.2;

  font-weight: 600;

  color: #172033;
}


/* =========================
   ADD BUTTON
========================= */

.add-button {
  height: 38px;

  min-width: 150px;

  padding: 0 18px;

  display: flex;
  align-items: center;
  justify-content: center;

  gap: 9px;

  border: none;
  border-radius: 7px;

  background: #2085a0;

  color: #ffffff;

  font-family: inherit;

  font-size: 12px;
  font-weight: 600;

  cursor: pointer;

  box-shadow:
    0 4px 10px rgba(32, 133, 160, 0.18);

  transition:
    background 0.15s ease,
    transform 0.15s ease;
}

.add-button:hover {
  background: #19768f;
}

.add-button:active {
  transform: translateY(1px);
}

.plus {
  font-size: 18px;
  line-height: 1;
}


/* =========================
   MANAGEMENT CARD
========================= */

.management-card {
  width: 100%;

  background: #ffffff;

  border: 1px solid #e1e8ef;

  border-radius: 10px;

  overflow: hidden;

  box-shadow:
    0 3px 12px rgba(31, 58, 78, 0.04);
}

.table-wrapper {
  width: 100%;

  overflow-x: auto;
}

table {
  width: 100%;

  border-collapse: collapse;

  table-layout: fixed;
}

thead {
  background: #f8fafc;
}

th {
  height: 51px;

  padding: 0 23px;

  text-align: left;

  border-bottom: 1px solid #e6ebf0;

  color: #60738e;

  font-size: 11px;
  font-weight: 500;
}

td {
  height: 56px;

  padding: 0 23px;

  border-bottom: 1px solid #edf0f3;

  color: #4d6079;

  font-size: 12px;
}

tbody tr:last-child td {
  border-bottom: none;
}

tbody tr:hover {
  background: #fbfcfd;
}

.name-cell {
  color: #172033;

  font-weight: 500;
}


/* =========================
   STATUS
========================= */

.status-badge {
  display: inline-flex;
  align-items: center;

  gap: 6px;

  padding: 5px 10px;

  border-radius: 20px;

  font-size: 10px;
  font-weight: 500;

  white-space: nowrap;
}

.status-dot {
  width: 6px;
  height: 6px;

  border-radius: 50%;
}

.status-badge.active {
  background: #eaf8f3;
  color: #177b62;
}

.status-badge.active .status-dot {
  background: #20b88a;
}

.status-badge.inactive {
  background: #f1f3f5;
  color: #778397;
}

.status-badge.inactive .status-dot {
  background: #9aa6b5;
}


/* =========================
   EDIT BUTTON
========================= */

.edit-button {
  height: 31px;

  min-width: 62px;

  padding: 0 13px;

  border: 1px solid #2085a0;

  border-radius: 7px;

  background: #ffffff;

  color: #1680a0;

  font-family: inherit;

  font-size: 11px;
  font-weight: 500;

  cursor: pointer;

  transition:
    background 0.15s ease,
    color 0.15s ease;
}

.edit-button:hover {
  background: #edf8fa;
}


/* =========================
   EMPTY STATE
========================= */

.empty-state {
  height: 110px;

  text-align: center;

  color: #8b9aae;

  font-size: 11px;
}


/* =========================
   ERROR
========================= */

.error-message {
  margin-top: 15px;

  padding: 11px 14px;

  border: 1px solid #f1c7c7;

  border-radius: 7px;

  background: #fff5f5;

  color: #b44a4a;

  font-size: 11px;
}


/* =========================
   MODAL OVERLAY
========================= */

.modal-overlay {
  position: fixed;

  inset: 0;

  z-index: 100;

  display: flex;

  align-items: center;
  justify-content: center;

  padding: 20px;

  background: rgba(23, 32, 51, 0.38);

  backdrop-filter: blur(2px);
}


/* =========================
   MODAL
========================= */

.modal {
  width: 100%;
  max-width: 470px;

  background: #ffffff;

  border-radius: 12px;

  box-shadow:
    0 20px 60px rgba(23, 32, 51, 0.18);

  overflow: hidden;
}

.modal-header {
  display: flex;

  align-items: flex-start;
  justify-content: space-between;

  padding: 21px 23px 18px;

  border-bottom: 1px solid #e7edf2;
}

.modal-header h2 {
  margin: 0;

  font-size: 17px;

  font-weight: 600;

  color: #172033;
}

.modal-header p {
  margin: 5px 0 0;

  font-size: 10px;

  color: #8291a6;
}

.close-button {
  width: 29px;
  height: 29px;

  display: flex;
  align-items: center;
  justify-content: center;

  border: none;

  border-radius: 6px;

  background: transparent;

  color: #718198;

  font-size: 22px;

  line-height: 1;

  cursor: pointer;
}

.close-button:hover {
  background: #f3f6f8;

  color: #172033;
}


/* =========================
   FORM
========================= */

.modal-form {
  padding: 21px 23px 23px;
}

.form-group {
  margin-bottom: 17px;
}

.form-group label {
  display: block;

  margin-bottom: 7px;

  color: #52627a;

  font-size: 11px;

  font-weight: 500;
}

.form-group input,
.form-group select {
  width: 100%;

  height: 39px;

  padding: 0 12px;

  border: 1px solid #dce4ec;

  border-radius: 7px;

  outline: none;

  background: #ffffff;

  color: #172033;

  font-family: inherit;

  font-size: 11px;

  transition:
    border-color 0.15s ease,
    box-shadow 0.15s ease;
}

.form-group input::placeholder {
  color: #a0adbd;
}

.form-group input:focus,
.form-group select:focus {
  border-color: #2085a0;

  box-shadow:
    0 0 0 3px rgba(32, 133, 160, 0.08);
}


/* =========================
   MODAL ACTIONS
========================= */

.modal-actions {
  display: flex;

  justify-content: flex-end;

  gap: 9px;

  padding-top: 4px;
}

.cancel-button,
.save-button {
  height: 37px;

  padding: 0 18px;

  border-radius: 7px;

  font-family: inherit;

  font-size: 11px;
  font-weight: 600;

  cursor: pointer;
}

.cancel-button {
  border: 1px solid #dce4ec;

  background: #ffffff;

  color: #52627a;
}

.cancel-button:hover {
  background: #f7f9fb;
}

.save-button {
  border: 1px solid #2085a0;

  background: #2085a0;

  color: #ffffff;
}

.save-button:hover {
  background: #19768f;
}

.save-button:disabled {
  opacity: 0.65;

  cursor: not-allowed;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 900px) {

  .sidebar {
    width: 190px;
  }

  .main-content {
    width: calc(100% - 190px);

    margin-left: 190px;
  }

  .content {
    padding: 24px 20px 40px;
  }

}

@media (max-width: 700px) {

  .sidebar {
    width: 70px;
  }

  .sidebar-logo {
    justify-content: center;

    padding: 0;
  }

  .logo-text {
    display: none;
  }

  .logo-droplet {
    margin-right: 0;
  }

  .nav-item {
    justify-content: center;

    padding: 0;
  }

  .nav-item span {
    display: none;
  }

  .main-content {
    width: calc(100% - 70px);

    margin-left: 70px;
  }

  .content {
    padding: 20px 15px 35px;
  }

  .page-header {
    align-items: flex-start;
  }

  .section-title-row {
    gap: 12px;
  }

  .section-title-row h2 {
    font-size: 16px;
  }

  .add-button {
    min-width: 125px;
  }

}

</style>