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
          <img src="../assets/icondashboardblue.png" alt="" />
          <span>Dashboard</span>
        </button>

        <button
          type="button"
          class="nav-item active"
          @click="goBack"
        >
          <img src="../assets/iconordergray.png" alt="" />
          <span>Orders</span>
        </button>

        <button
          type="button"
          class="nav-item"
          @click="goTo('Services & Add-ons')"
        >
          <img src="../assets/iconservicesgray.png" alt="" />
          <span>Services &amp; Add-ons</span>
        </button>

        <button
          type="button"
          class="nav-item"
          @click="goTo('Reports')"
        >
          <img src="../assets/iconreportsgray.png" alt="" />
          <span>Reports</span>
        </button>

      </nav>

      <div class="sidebar-bottom">
        <button
          type="button"
          class="nav-item logout"
          @click="handleLogout"
        >
          <img src="../assets/iconlogoutgray.png" alt="" />
          <span>Logout</span>
        </button>
      </div>

    </aside>


    <!-- MAIN -->
    <main class="main-content">

      <!-- TOP HEADER -->
      <TopHeader />


      <!-- CONTENT -->
      <div class="content">



        <!-- ERROR -->
<div
  v-if="errorMessage && !order"
  class="state-card error"
>
  <h2>Unable to load order</h2>

  <p>{{ errorMessage }}</p>

  <button
    type="button"
    class="back-button"
    @click="goBack"
  >
    Back to Orders
  </button>
</div>

          <!-- ORDER -->
          <template v-else-if="order">

          <!-- PAGE HEADER -->
          <div class="page-header">

            <div class="page-title">

              <button
                type="button"
                class="back-link"
                @click="goBack"
              >
                <span class="back-arrow">‹</span>
                <span>Order details</span>
              </button>

              <span
                class="status-badge"
                :class="getStatusClass(order.status)"
              >
                <span class="status-dot"></span>
                {{ order.status }}
              </span>

            </div>


            <!-- STATUS ACTION -->
            <button
              v-if="nextStatus"
              type="button"
              class="status-action"
              :disabled="updatingStatus"
              @click="updateStatus"
            >
              <span v-if="!updatingStatus">✓</span>
              <span v-else class="spinner"></span>

              {{
                updatingStatus
                  ? 'Updating...'
                  : nextStatusLabel
              }}
            </button>

          </div>


          <!-- ORDER INFORMATION -->
          <section class="details-card">

            <h2>
              Order Information
            </h2>

            <div class="info-list">

              <div class="info-row">
                <span>Order No.</span>
                <strong>
                  {{ order.order_number }}
                </strong>
              </div>

              <div class="info-row">
                <span>Customer Name</span>
                <strong>
                  {{ order.customer_name }}
                </strong>
              </div>

              <div class="info-row">
                <span>Phone Number</span>
                <strong>
                  {{ order.phone_number }}
                </strong>
              </div>

              <div class="info-row">
                <span>Date Created</span>
                <strong>
                  {{ formatDate(order.date_created) }}
                </strong>
              </div>

              <div class="info-row">
                <span>Estimated Pickup</span>
                <strong>
                  {{ formatDate(order.estimated_pickup) }}
                </strong>
              </div>

            </div>

          </section>


          <!-- ORDER DETAILS -->
          <section class="details-card">

            <h2>
              Order Details
            </h2>

            <div class="info-list">

              <div class="info-row">
                <span>Service</span>
                <strong>
                  {{ order.service?.service_name ?? '—' }}
                </strong>
              </div>

              <div class="info-row">
                <span>Weight</span>
                <strong>
                  {{ Number(order.weight).toFixed(1) }} kg
                </strong>
              </div>

              <div class="info-row">
                <span>Service Cost</span>
                <strong>
                  ₱{{ serviceCost }}
                </strong>
              </div>

            </div>

          </section>


          <!-- ADD-ONS -->
          <section class="details-card">

            <h2>
              Add-ons
            </h2>

            <div
              v-if="order.addons?.length"
              class="addon-list"
            >

              <div
                v-for="addon in order.addons"
                :key="addon.id"
                class="addon-row"
              >
                <span>
                  {{ addon.addon_name }}
                  × {{ addon.quantity }}
                </span>

                <strong>
                  ₱{{
                    (
                      Number(addon.addon_price) *
                      Number(addon.quantity)
                    ).toFixed(2)
                  }}
                </strong>
              </div>

            </div>

            <div
              v-else
              class="no-addons"
            >
              No add-ons selected.
            </div>

          </section>


          <!-- TOTAL -->
          <div class="total-bar">

            <strong>
              TOTAL
            </strong>

            <strong>
              ₱{{ totalPrice }}
            </strong>

          </div>

        </template>

      </div>

    </main>

  </div>
</template>


<script setup>
import {
  computed,
  ref,
  watch
} from 'vue'

import TopHeader from './TopHeader.vue'


const props = defineProps({
  orderId: {
    type: [Number, String],
    required: true
  },
  order: {
    type: Object,
    default: null
  }
})


const emit = defineEmits([
  'navigate',
  'logout',
  'back',
  'order-updated'
])


const order = ref(null)
const updatingStatus = ref(false)
const errorMessage = ref('')


/* =========================
   ORDER DATA
   Uses the order already cached
   by App.vue so the page opens
   immediately without another GET.
========================= */

const normalizeOrder = (data) => {
  if (!data) {
    return null
  }

  // App.vue already provides this normalized shape.
  if (data.orderNumber !== undefined) {
    return {
      ...data,
      order_number: data.orderNumber,
      customer_name: data.customer,
      phone_number: data.phone,
      date_created: data.rawDateCreated ?? data.date_created,
      estimated_pickup: data.estimatedPickup ?? data.estimated_pickup,
      total_price: data.total ?? data.total_price,
      service:
        typeof data.service === 'object'
          ? data.service
          : {
              service_name: data.service ?? '—',
              price_per_kg: 0
            }
    }
  }

  // Keep compatibility with a raw Laravel API response.
  return data
}

const setOrderFromProps = () => {
  if (props.order) {
    order.value = normalizeOrder(props.order)
    errorMessage.value = ''
  }
}


/* =========================
   STATUS
========================= */

const nextStatus = computed(() => {

  if (!order.value) {
    return null
  }

  if (order.value.status === 'Pending') {
    return 'Ready for Pickup'
  }

  if (order.value.status === 'Ready for Pickup') {
    return 'Claimed'
  }

  return null
})


const nextStatusLabel = computed(() => {

  if (nextStatus.value === 'Ready for Pickup') {
    return 'Mark as Ready for Pickup'
  }

  if (nextStatus.value === 'Claimed') {
    return 'Mark as Claimed'
  }

  return ''

})


const updateStatus = async () => {

  if (!nextStatus.value || !order.value) {
    return
  }

  updatingStatus.value = true
  errorMessage.value = ''

  const token =
    localStorage.getItem('washup_token')

  try {

    const response = await fetch(
      `/api/orders/${order.value.id}/status`,
      {
        method: 'PATCH',

        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`
        },

        body: JSON.stringify({
          status: nextStatus.value
        })
      }
    )

    if (!response.ok) {

      let message =
        'Unable to update order status.'

      try {
        const data = await response.json()

        if (data.message) {
          message = data.message
        }
      } catch {
        // Keep default message.
      }

      throw new Error(message)
    }

    const updatedOrder = await response.json()

    order.value = normalizeOrder(updatedOrder)

    // Keep the cached order in App.vue synchronized.
    emit('order-updated', order.value)

  } catch (error) {

    console.error(
      'Failed to update order status:',
      error
    )

    errorMessage.value =
      error.message ||
      'Unable to update order status.'

  } finally {

    updatingStatus.value = false

  }
}


/* =========================
   PRICE
========================= */

const serviceCost = computed(() => {

  if (!order.value) {
    return '0.00'
  }

  const servicePrice =
    Number(order.value.service?.price_per_kg)

  const weight =
    Number(order.value.weight)

  if (
    Number.isFinite(servicePrice) &&
    servicePrice > 0 &&
    Number.isFinite(weight)
  ) {
    return (servicePrice * weight).toFixed(2)
  }

  // Cached orders from App.vue may only contain
  // the service name. In that case, derive the
  // base service cost from the saved total.
  const total =
    Number(order.value.total_price) || 0

  const addonTotal =
    (order.value.addons ?? []).reduce(
      (sum, addon) =>
        sum +
        (
          Number(addon.addon_price) || 0
        ) *
        (
          Number(addon.quantity) || 0
        ),
      0
    )

  return Math.max(
    total - addonTotal,
    0
  ).toFixed(2)

})


const totalPrice = computed(() => {

  if (!order.value) {
    return '0.00'
  }

  return Number(
    order.value.total_price
  ).toFixed(2)

})


/* =========================
   DATE
========================= */

const formatDate = (dateString) => {

  if (!dateString) {
    return '—'
  }

  const date =
    new Date(dateString)

  if (
    Number.isNaN(date.getTime())
  ) {
    return '—'
  }

  return date.toLocaleDateString(
    'en-PH',
    {
      day: '2-digit',
      month: 'short',
      year: 'numeric'
    }
  )

}


/* =========================
   STATUS STYLE
========================= */

const getStatusClass = (status) => {

  if (status === 'Pending') {
    return 'pending'
  }

  if (status === 'Ready for Pickup') {
    return 'ready'
  }

  if (status === 'Claimed') {
    return 'claimed'
  }

  return ''

}


/* =========================
   NAVIGATION
========================= */

const goBack = () => {
  emit('back')
}


const goTo = (page) => {
  emit('navigate', page)
}


/* =========================
   LOGOUT
========================= */

const handleLogout = async () => {

  const token =
    localStorage.getItem('washup_token')

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

    console.error(
      'Logout error:',
      error
    )

  } finally {

    localStorage.removeItem(
      'washup_token'
    )

    localStorage.removeItem(
      'washup_user'
    )

    emit('logout')
  }
}


/* =========================
   INITIAL LOAD
========================= */

watch(
  () => props.order,
  () => {
    setOrderFromProps()
  },
  { immediate: true }
)
</script>


<style scoped>

* {
  box-sizing: border-box;
}


/* =========================
   LAYOUT
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
  inset: 0 auto 0 0;

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


.sidebar-bottom {
  margin-top: auto;

  padding: 15px 10px 20px;

  border-top: 1px solid #e4e9ef;
}


.nav-item.logout {
  margin: 0;
}


/* =========================
   MAIN
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
  padding: 30px 32px 40px;
}


.page-header {
  min-height: 44px;

  display: flex;
  align-items: center;
  justify-content: space-between;

  margin-bottom: 18px;
}


.page-title {
  display: flex;
  align-items: center;
  gap: 12px;
}


.back-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;

  padding: 0;

  border: none;
  background: transparent;

  color: #60738e;

  font-family: inherit;
  font-size: 12px;

  cursor: pointer;
}


.back-link:hover {
  color: #217f98;
}


.back-arrow {
  font-size: 22px;
  line-height: 10px;
}


/* =========================
   STATUS BADGE
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


.status-badge.pending {
  background: #fff7e7;
  color: #c77c00;
}


.status-badge.pending .status-dot {
  background: #e7a526;
}


.status-badge.ready {
  background: #e9f7fa;
  color: #17748d;
}


.status-badge.ready .status-dot {
  background: #2aa5c0;
}


.status-badge.claimed {
  background: #eaf8f0;
  color: #248553;
}


.status-badge.claimed .status-dot {
  background: #36ad6b;
}


/* =========================
   STATUS ACTION
========================= */

.status-action {
  min-height: 40px;

  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;

  padding: 0 17px;

  border: none;
  border-radius: 7px;

  background: #2085a0;

  color: #ffffff;

  font-family: inherit;
  font-size: 11px;
  font-weight: 500;

  cursor: pointer;

  box-shadow:
    0 4px 10px
    rgba(32, 133, 160, 0.12);

  transition:
    background 0.15s ease,
    transform 0.15s ease;
}


.status-action:hover:not(:disabled) {
  background: #19778f;
}


.status-action:active:not(:disabled) {
  transform: translateY(1px);
}


.status-action:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}


.spinner {
  width: 12px;
  height: 12px;

  border: 2px solid rgba(255, 255, 255, 0.45);
  border-top-color: #ffffff;

  border-radius: 50%;

  animation: spin 0.7s linear infinite;
}


@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}


/* =========================
   CARDS
========================= */

.details-card {
  margin-bottom: 16px;

  padding: 22px 20px;

  background: #ffffff;

  border: 1px solid #e1e8ef;

  border-radius: 10px;

  box-shadow:
    0 2px 8px
    rgba(31, 58, 78, 0.025);
}


.details-card h2 {
  margin: 0 0 16px;

  color: #172033;

  font-size: 15px;
  font-weight: 600;
}


/* =========================
   INFORMATION ROWS
========================= */

.info-list {
  display: flex;
  flex-direction: column;
}


.info-row {
  min-height: 32px;

  display: flex;
  align-items: center;
  justify-content: space-between;

  color: #60738e;

  font-size: 11px;
}


.info-row strong {
  color: #172033;

  font-size: 11px;
  font-weight: 500;

  text-align: right;
}


/* =========================
   ADD-ONS
========================= */

.addon-list {
  display: flex;
  flex-direction: column;
}


.addon-row {
  min-height: 32px;

  display: flex;
  align-items: center;
  justify-content: space-between;

  color: #60738e;

  font-size: 11px;
}


.addon-row strong {
  color: #172033;

  font-size: 11px;
  font-weight: 500;
}


.no-addons {
  color: #8a9bb0;
  font-size: 11px;
}


/* =========================
   TOTAL
========================= */

.total-bar {
  min-height: 54px;

  display: flex;
  align-items: center;
  justify-content: space-between;

  padding: 0 19px;

  border-radius: 10px;

  background: #2085a0;

  color: #ffffff;

  box-shadow:
    0 3px 10px
    rgba(32, 133, 160, 0.10);
}


.total-bar strong:first-child {
  font-size: 11px;
  font-weight: 600;
}


.total-bar strong:last-child {
  font-size: 15px;
  font-weight: 600;
}


/* =========================
   STATES
========================= */

.state-card {
  min-height: 220px;

  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;

  gap: 8px;

  padding: 30px;

  background: #ffffff;

  border: 1px solid #e1e8ef;
  border-radius: 10px;

  color: #70819a;

  font-size: 12px;

  text-align: center;
}


.state-card.error h2 {
  margin: 0;

  color: #172033;

  font-size: 17px;
}


.state-card.error p {
  margin: 0 0 10px;

  color: #8a9bb0;

  font-size: 11px;
}


.back-button {
  height: 36px;

  padding: 0 15px;

  border: none;
  border-radius: 7px;

  background: #2085a0;

  color: #ffffff;

  font-family: inherit;
  font-size: 11px;

  cursor: pointer;
}


.back-button:hover {
  background: #19768f;
}


/* =========================
   RESPONSIVE
========================= */

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
    padding: 22px 18px 30px;
  }

  .top-header {
    padding: 0 18px;
  }

  .page-header {
    align-items: flex-start;
    gap: 12px;
  }

  .status-action {
    padding: 0 12px;
  }

}
</style>