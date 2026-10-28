<template>
  <div class="dashboard">

    <aside class="sidebar">

      <div class="sidebar-logo">
        <img src="../assets/droplet.png" alt="" class="logo-droplet"/>
        <img src="../assets/washupblue.png" alt="WashUP" class="logo-text"/>
      </div>

      <nav class="sidebar-nav">

        <button
          type="button"
          class="nav-item"
          @click="goTo('Dashboard')"
        >
          <img src="../assets/icondashboardblue.png" alt=""/>
          <span>Dashboard</span>
        </button>

        <button
          type="button"
          class="nav-item active"
          @click="goTo('Orders')"
        >
          <img src="../assets/iconordergray.png" alt=""/>
          <span>Orders</span>
        </button>

        <button
          type="button"
          class="nav-item"
          @click="goTo('Services & Add-ons')"
        >
          <img src="../assets/iconservicesgray.png" alt=""/>
          <span>Services &amp; Add-ons</span>
        </button>

        <button
          type="button"
          class="nav-item"
          @click="goTo('Reports')"
        >
          <img src="../assets/iconreportsgray.png" alt=""/>
          <span>Reports</span>
        </button>

      </nav>

      <div class="sidebar-bottom">

        <button
          class="nav-item logout"
          type="button"
          @click="handleLogout"
        >
          <img src="../assets/iconlogoutgray.png" alt=""/>
          <span>Logout</span>
        </button>

      </div>

    </aside>

    <main class="main-content">

      <TopHeader />

      <div class="content">

        <!-- PAGE HEADER -->
        <div class="page-header">

          <div>
            <h1>Orders</h1>

            <p>
              Manage incoming laundry orders, track status,
              and keep customer records up to date.
            </p>
          </div>

          <button
            type="button"
            class="add-order-button"
            @click="handleAddOrder"
          >
            <span class="plus">+</span>
            Add New Order
          </button>

        </div>


        <!-- SEARCH + FILTER -->
        <section class="filters-row">

          <div class="search-box">

            <span class="search-icon">⌕</span>

            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search by order number or customer name"
            />

          </div>

          <select
            v-model="statusFilter"
            class="status-filter"
          >
            <option value="All">
              All
            </option>

            <option value="Pending">
              Pending
            </option>

            <option value="Ready for Pickup">
              Ready for Pickup
            </option>

            <option value="Claimed">
              Claimed
            </option>
          </select>

        </section>


        <!-- ORDERS TABLE -->
        <section class="orders-card">

          <div class="table-wrapper">

            <table>

              <thead>
                <tr>
                  <th>Order No.</th>
                  <th>Customer</th>
                  <th>Service</th>
                  <th>Weight<br />(kg)</th>
                  <th>Total (₱)</th>
                  <th>Status</th>
                  <th>Date</th>
                </tr>
              </thead>

              <tbody>

  <tr
  v-for="order in filteredOrders"
  :key="order.orderNumber"
  class="order-row"
  tabindex="0"
  @click="viewOrder(order)"
  @keydown.enter="viewOrder(order)"
>

    <td class="order-number">
      {{ order.orderNumber }}
    </td>

    <td>
      {{ order.customer }}
    </td>

    <td>
  {{ order.service?.service_name ?? '—' }}
    </td>

    <td>
      {{ order.weight }}
    </td>

    <td class="total">
      ₱{{ order.total }}
    </td>

    <td>
      <span
        class="status-badge"
        :class="getStatusClass(order.status)"
      >
        <span class="status-dot"></span>
        {{ order.status }}
      </span>
    </td>

    <td>
      {{ order.date }}
    </td>


  </tr>

  <tr
    v-if="filteredOrders.length === 0"
  >
    <td
      colspan="7"
      class="empty-state"
    >
      No orders found.
    </td>
  </tr>

</tbody>

            </table>

          </div>


          <!-- TABLE FOOTER -->
          <div class="table-footer">

            <span>
              Showing 1-{{ filteredOrders.length }}
              of {{ props.orders.length }} orders
            </span>

            <div class="pagination">

              <button type="button">
                ‹
              </button>

              <button
                type="button"
                class="current-page"
              >
                1
              </button>

              <button type="button">
                2
              </button>

              <button type="button">
                3
              </button>

              <button type="button">
                4
              </button>

              <button type="button">
                ›
              </button>

            </div>

          </div>

        </section>

      </div>

    </main>

  </div>
</template>


<script setup>
import { computed, ref } from 'vue'

const emit = defineEmits([
  'navigate',
  'logout',
  'view-order'
])

import TopHeader from './TopHeader.vue'

const props = defineProps({
  orders: {
    type: Array,
    default: () => []
  },
  ordersLoading: {
    type: Boolean,
    default: false
  },
  ordersError: {
    type: String,
    default: ''
  }
})

/* -------------------------
   SEARCH + FILTER
------------------------- */

const searchQuery = ref('')
const statusFilter = ref('All')

/* -------------------------
   ORDERS
------------------------- */

/* -------------------------
   DATE FORMAT
------------------------- */

const formatDate = (dateString) => {
  if (!dateString) {
    return '—'
  }

  const date = new Date(dateString)

  if (Number.isNaN(date.getTime())) {
    return '—'
  }

  return date.toLocaleDateString('en-PH', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

/* -------------------------
   FILTERED ORDERS
------------------------- */

const filteredOrders = computed(() => {
  const search = searchQuery.value
    .toLowerCase()
    .trim()

  return props.orders.filter((order) => {

    const matchesSearch =
      order.orderNumber
        .toLowerCase()
        .includes(search) ||

      order.customer
        .toLowerCase()
        .includes(search)

    const matchesStatus =
      statusFilter.value === 'All' ||
      order.status === statusFilter.value

    return matchesSearch && matchesStatus
  })
})

/* -------------------------
   NAVIGATION
------------------------- */

const goTo = (page) => {
  emit('navigate', page)
}

/* -------------------------
   LOGOUT
------------------------- */

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

/* -------------------------
   ORDER ACTIONS
------------------------- */

const viewOrder = (order) => {
  emit('view-order', order.id)
}

const handleAddOrder = () => {
  emit('navigate', 'Add Order')
}

/* -------------------------
   STATUS STYLE
------------------------- */

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


.main-content {
  width: calc(100% - 218px);
  min-height: 100vh;
  margin-left: 218px;
}


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


.content {
  padding: 32px 30px 50px;
}


/* =========================
   PAGE HEADER
========================= */

.page-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;

  margin-bottom: 24px;
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


.add-order-button {
  height: 38px;

  padding: 0 18px;

  display: flex;
  align-items: center;

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
}


.add-order-button:hover {
  background: #19768f;
}


.plus {
  font-size: 18px;
  line-height: 1;
}


/* =========================
   FILTERS
========================= */

.filters-row {
  display: flex;
  align-items: center;

  gap: 14px;

  margin-bottom: 22px;
}


.search-box {
  flex: 1;

  height: 38px;

  display: flex;
  align-items: center;

  padding: 0 13px;

  background: #ffffff;

  border: 1px solid #dce4ec;

  border-radius: 7px;
}


.search-icon {
  margin-right: 9px;

  color: #8ca0b8;

  font-size: 19px;
}


.search-box input {
  width: 100%;

  border: none;
  outline: none;

  background: transparent;

  font-family: inherit;

  font-size: 12px;

  color: #172033;
}


.search-box input::placeholder {
  color: #9aabc0;
}


.status-filter {
  width: 164px;

  height: 38px;

  padding: 0 12px;

  border: 1px solid #dce4ec;

  border-radius: 7px;

  background: #ffffff;

  color: #52627a;

  font-family: inherit;

  font-size: 12px;

  outline: none;

  cursor: pointer;
}


/* =========================
   ORDERS CARD
========================= */

.orders-card {
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

  min-width: 850px;
}


thead {
  background: #f8fafc;
}


th {
  height: 62px;

  padding: 0 20px;

  text-align: left;

  border-bottom: 1px solid #e6ebf0;

  color: #60738e;

  font-size: 10px;

  font-weight: 500;

  text-transform: none;
}


td {
  height: 51px;

  padding: 0 20px;

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


.order-number {
  color: #172033;

  font-weight: 500;
}


.total {
  color: #172033;

  font-weight: 600;
}


/* =========================
   STATUS
========================= */

.status-badge {
  display: inline-flex;
  align-items: center;

  gap: 6px;

  padding: 5px 9px;

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
   EMPTY STATE
========================= */

.empty-state {
  height: 120px;

  text-align: center;

  color: #8b9aae;
}


/* =========================
   FOOTER
========================= */

.table-footer {
  min-height: 58px;

  display: flex;
  align-items: center;
  justify-content: space-between;

  padding: 0 20px;

  border-top: 1px solid #e6ebf0;

  color: #70819a;

  font-size: 11px;
}


.pagination {
  display: flex;
  align-items: center;

  gap: 6px;
}


.pagination button {
  width: 29px;
  height: 29px;

  display: flex;
  align-items: center;
  justify-content: center;

  border: 1px solid #dce4ec;

  border-radius: 7px;

  background: #ffffff;

  color: #52627a;

  font-family: inherit;

  font-size: 11px;

  cursor: pointer;
}


.pagination button:hover {
  background: #f5fafc;
}


.pagination .current-page {
  background: #eaf6f9;

  border-color: #eaf6f9;

  color: #176f87;

  font-weight: 600;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 900px) {

  .sidebar {
    width: 190px;
  }

  .main-area {
    margin-left: 190px;
  }

  .content {
    padding: 24px 20px 40px;
  }

  .topbar {
    padding: 0 20px;
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

  .nav-item span:last-child,
  .logout-button span:last-child {
    display: none;
  }

  .main-area {
    margin-left: 70px;
  }

}
.order-row {
  cursor: pointer;
  transition: background 0.15s ease;
}

.order-row:hover {
  background: #f8fbfc;
}

.order-row:focus {
  outline: none;
  background: #f8fbfc;
}

</style>