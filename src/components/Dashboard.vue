<template>
  <div class="dashboard">

    <aside class="sidebar">

      <div class="sidebar-logo">
        <img src="../assets/droplet.png" alt="" class="logo-droplet"/>
        <img src="../assets/washupblue.png" alt="WashUP" class="logo-text"/>
      </div>

      <nav class="sidebar-nav">

        <button
          class="nav-item active"
          type="button"
        >
          <img src="../assets/icondashboardblue.png" alt=""/>
          <span>Dashboard</span>
        </button>

        <button
          class="nav-item"
          type="button"
          @click="handleNavigation('Orders')"
        >
          <img src="../assets/iconordergray.png" alt=""/>
          <span>Orders</span>
        </button>

        <button
          class="nav-item"
          type="button"
          @click="handleNavigation('Services & Add-ons')"
        >
          <img
            src="../assets/iconservicesgray.png"
            alt=""
          />
          <span>Services &amp; Add-ons</span>
        </button>

        <button
          class="nav-item"
          type="button"
          @click="handleNavigation('Reports')"
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

    <main class="main-content">

      <TopHeader />

      <div class="content-container">

        <!-- Section title -->
        <div class="section-heading">
          <span>ORDER SUMMARY</span>
        </div>

        <section class="summary-grid">

          <!-- Total Orders -->
          <article class="summary-card">

            <div class="card-top">
              <span>Total Orders</span>

              <div class="card-icon blue">
                <img
                  src="../assets/icontotalorder.png"
                  alt=""
                />
              </div>
            </div>

            <div class="card-value">
              {{ totalOrders }}
            </div>

            <div class="card-description">
              across all services
            </div>

          </article>

          <!-- Total Revenue -->
          <article class="summary-card">

            <div class="card-top">
              <span>Total Revenue</span>

              <div class="card-icon green">
                <img
                  src="../assets/icontotalrevenue.png"
                  alt=""
                />
              </div>
            </div>

            <div class="card-value">
              ₱{{ totalRevenue.toLocaleString('en-PH', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
              }) }}
            </div>

            <div class="card-description">
              total collected
            </div>

          </article>

          <!-- Claimed Orders -->
          <article class="summary-card">

            <div class="card-top">
              <span>Claimed Orders</span>

              <div class="card-icon claimed">
                <img
                  src="../assets/iconclaimedorders.png"
                  alt=""
                />
              </div>
            </div>

            <div class="card-value">
              {{ claimedOrders }}
            </div>

            <div class="card-description">
              picked up by customers
            </div>

          </article>

          <!-- Pending Orders -->
          <article class="summary-card">

            <div class="card-top">
              <span>Pending Orders</span>

              <div class="card-icon pending">
                <img
                  src="../assets/iconpendingorders.png"
                  alt=""
                />
              </div>
            </div>

            <div class="card-value">
              {{ pendingOrders }}
            </div>

            <div class="card-description">
              waiting for action
            </div>

          </article>

        </section>

        <!-- Orders and Revenue Chart -->
        <section class="chart-card">

          <div class="chart-header">

            <div>
              <h2>
                Orders &amp; Revenue Over Time
              </h2>

              <p>
                Last 7 days performance
              </p>
            </div>

            <div class="chart-legend">

              <div class="legend-item">
                <span class="legend-line orders"></span>
                <span>Orders</span>
              </div>

              <div class="legend-item">
                <span class="legend-line revenue"></span>
                <span>Revenue</span>
              </div>

            </div>

          </div>

          <div class="chart-container">
            <canvas ref="chartCanvas"></canvas>
          </div>

        </section>

        <!-- Recent Orders -->
        <section class="orders-card">

          <div class="orders-header">

            <div>
              <h2>Recent Orders</h2>

              <p>
                Latest Laundry Orders
              </p>
            </div>

            <button
              type="button"
              class="view-all"
              @click="handleNavigation('Orders')"
            >
              View all orders
            </button>

          </div>

          <div class="table-wrapper">

            <table>

              <thead>
                <tr>
                  <th>Order<br />Number</th>
                  <th>Customer<br />Name</th>
                  <th>Service</th>
                  <th>Weight</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>

              <tbody>

                <tr
                  v-for="order in recentOrders"
                  :key="order.id"
                >

                  <td>
                    {{ order.number }}
                  </td>

                  <td>
                    {{ order.customer }}
                  </td>

                  <td>
                    {{ order.service }}
                  </td>

                  <td>
                    {{ order.weight }}
                  </td>

                  <td class="total">
                    ₱{{ order.total }}
                  </td>

                  <td>

                    <span
                      class="status"
                      :class="order.statusClass"
                    >
                      <span class="status-dot"></span>

                      {{ order.status }}
                    </span>

                  </td>

                  <td>

                    <button
                      type="button"
                      class="view-button"
                      @click="viewOrder(order)"
                    >

                      <img
                        src="../assets/iconview.png"
                        alt=""
                      />

                      View

                    </button>

                  </td>

                </tr>

                <tr v-if="recentOrders.length === 0">
                  <td
                    colspan="7"
                    class="empty-orders"
                  >
                    No orders found.
                  </td>
                </tr>

              </tbody>

            </table>

          </div>

        </section>

      </div>

    </main>

  </div>
</template>

<script setup>

import {
  computed,
  ref,
  watch,
  onMounted,
  onBeforeUnmount
} from 'vue'

import TopHeader from './TopHeader.vue'

const emit = defineEmits([
  'logout',
  'navigate',
  'view-order'
])

const props = defineProps({
  orders: {
    type: Array,
    default: () => []
  }
})

import {
  Chart,
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend
} from 'chart.js'

Chart.register(
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend
)

const chartCanvas = ref(null)

let ordersChart = null


/*
|--------------------------------------------------------------------------
| Status Classes
|--------------------------------------------------------------------------
*/

const statusClass = (status) => {
  if (status === 'Pending') {
    return 'pending-status'
  }

  if (status === 'Ready for Pickup') {
    return 'ready-status'
  }

  if (status === 'Claimed') {
    return 'claimed-status'
  }

  return ''
}


/*
|--------------------------------------------------------------------------
| Recent Orders
|--------------------------------------------------------------------------
*/

const recentOrders = computed(() => {
  return [...props.orders]
    .sort((a, b) => {

      const dateA = new Date(
        a.rawDateCreated ||
        a.date_created ||
        0
      )

      const dateB = new Date(
        b.rawDateCreated ||
        b.date_created ||
        0
      )

      return dateB - dateA
    })

    .slice(0, 5)

    .map((order) => ({
      id: order.id,

      number:
        order.orderNumber ||
        order.order_number ||
        '—',

      customer:
        order.customer ||
        order.customer_name ||
        '—',

      service:
        order.service?.service_name ||
        order.service_name ||
        '—',

      weight:
        Number(order.weight || 0).toFixed(1),

      total:
        Number(
          order.total ||
          order.total_price ||
          0
        ).toFixed(2),

      status:
        order.status ||
        '—',

      statusClass:
        statusClass(order.status)
    }))
})


/*
|--------------------------------------------------------------------------
| Dashboard Statistics
|--------------------------------------------------------------------------
*/

const totalOrders = computed(() => {
  return props.orders.length
})


const totalRevenue = computed(() => {

  return props.orders.reduce(
    (total, order) => {

      return total + Number(
        order.total ||
        order.total_price ||
        0
      )

    },
    0
  )
})


const claimedOrders = computed(() => {

  return props.orders.filter(
    (order) =>
      order.status === 'Claimed'
  ).length

})


const pendingOrders = computed(() => {

  return props.orders.filter(
    (order) =>
      order.status === 'Pending'
  ).length

})


/*
|--------------------------------------------------------------------------
| Chart Helpers
|--------------------------------------------------------------------------
*/

const getLastSevenDays = () => {

  const days = []

  for (let i = 6; i >= 0; i--) {

    const date = new Date()

    date.setHours(
      0,
      0,
      0,
      0
    )

    date.setDate(
      date.getDate() - i
    )

    days.push(date)
  }

  return days
}


const formatChartDate = (date) => {

  return date.toLocaleDateString(
    'en-PH',
    {
      weekday: 'short'
    }
  )
}


const isSameDay = (
  dateA,
  dateB
) => {

  return (
    dateA.getFullYear() ===
      dateB.getFullYear() &&

    dateA.getMonth() ===
      dateB.getMonth() &&

    dateA.getDate() ===
      dateB.getDate()
  )
}


/*
|--------------------------------------------------------------------------
| Create Real Dashboard Chart
|--------------------------------------------------------------------------
*/

const createChart = () => {

  if (!chartCanvas.value) {
    return
  }


  /*
   * Destroy previous chart
   * before creating a new one.
   */
  if (ordersChart) {

    ordersChart.destroy()

    ordersChart = null
  }


  const lastSevenDays =
    getLastSevenDays()


  /*
   * Orders per day
   */
  const orderData =
    lastSevenDays.map((day) => {

      return props.orders.filter(
        (order) => {

          const rawDate =
            order.rawDateCreated ||
            order.date_created

          if (!rawDate) {
            return false
          }

          const orderDate =
            new Date(rawDate)

          if (
            Number.isNaN(
              orderDate.getTime()
            )
          ) {
            return false
          }

          return isSameDay(
            orderDate,
            day
          )
        }
      ).length
    })


  /*
   * Revenue per day
   */
  const revenueData =
    lastSevenDays.map((day) => {

      return props.orders

        .filter((order) => {

          const rawDate =
            order.rawDateCreated ||
            order.date_created

          if (!rawDate) {
            return false
          }

          const orderDate =
            new Date(rawDate)

          if (
            Number.isNaN(
              orderDate.getTime()
            )
          ) {
            return false
          }

          return isSameDay(
            orderDate,
            day
          )
        })

        .reduce(
          (total, order) => {

            return total + Number(
              order.total ||
              order.total_price ||
              0
            )

          },
          0
        )
    })


  const labels =
    lastSevenDays.map(
      formatChartDate
    )


  /*
   * Chart.js expects the revenue
   * dataset in thousands because
   * the right axis displays ₱K.
   */
  const revenueInThousands =
    revenueData.map(
      (value) => value / 1000
    )


  const data = {

    labels,

    datasets: [

      /*
       * Orders
       */
      {
        label: 'Orders',

        data: orderData,

        backgroundColor:
          '#247f99',

        borderRadius: 2,

        barThickness: 10,

        yAxisID: 'orders'
      },


      /*
       * Revenue
       */
      {
        label: 'Revenue',

        data:
          revenueInThousands,

        backgroundColor:
          '#42c49d',

        borderRadius: 2,

        barThickness: 10,

        yAxisID: 'revenue'
      }

    ]
  }


  /*
   * Automatically scale the
   * left orders axis.
   */
  const maxOrders =
    Math.max(
      ...orderData,
      1
    )


  /*
   * Automatically scale the
   * right revenue axis.
   */
  const maxRevenue =
    Math.max(
      ...revenueData,
      1000
    )


  const config = {

    type: 'bar',

    data,

    options: {

      responsive: true,

      maintainAspectRatio: false,

      interaction: {
        mode: 'index',
        intersect: false
      },


      plugins: {

        legend: {
          display: false
        },


        tooltip: {

          backgroundColor:
            '#172033',

          padding: 10,

          titleFont: {
            size: 11
          },

          bodyFont: {
            size: 10
          },


          callbacks: {

            label(context) {

              if (
                context.dataset.label ===
                'Revenue'
              ) {

                const revenue =
                  context.raw * 1000

                return (
                  ` Revenue: ₱${revenue.toLocaleString(
                    'en-PH',
                    {
                      minimumFractionDigits: 0,
                      maximumFractionDigits: 2
                    }
                  )}`
                )
              }


              return (
                ` Orders: ${context.raw}`
              )
            }

          }

        }

      },


      scales: {

        x: {

          grid: {
            display: false
          },

          border: {
            display: false
          },

          ticks: {

            color:
              '#9aa9bd',

            font: {
              size: 9
            }

          },

          stacked: false

        },


        orders: {

          type: 'linear',

          position: 'left',

          beginAtZero: true,

          suggestedMax:
            Math.max(
              maxOrders + 2,
              5
            ),

          ticks: {

            stepSize: 1,

            color:
              '#9aa9bd',

            font: {
              size: 9
            },

            precision: 0

          },

          grid: {

            color:
              '#edf1f5'

          },

          border: {
            display: false
          }

        },


        revenue: {

          type: 'linear',

          position: 'right',

          beginAtZero: true,

          suggestedMax:
            Math.ceil(
              maxRevenue / 1000
            ) + 1,

          ticks: {

            stepSize: 1,

            color:
              '#9aa9bd',

            font: {
              size: 9
            },

            callback(value) {

              return `₱${value}K`

            }

          },

          grid: {

            drawOnChartArea:
              false

          },

          border: {
            display: false
          }

        }

      }

    }

  }


  ordersChart = new Chart(
    chartCanvas.value,
    config
  )
}


/*
|--------------------------------------------------------------------------
| Re-create chart whenever
| shared orders data changes.
|--------------------------------------------------------------------------
*/

watch(
  () => props.orders,
  () => {
    createChart()
  },
  {
    deep: true
  }
)


/*
|--------------------------------------------------------------------------
| Navigation
|--------------------------------------------------------------------------
*/

const handleNavigation = (
  page
) => {

  emit(
    'navigate',
    page
  )
}


/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

const handleLogout =
  async () => {

    const token =
      localStorage.getItem(
        'washup_token'
      )

    try {

      if (token) {

        await fetch(
          '/api/logout',
          {
            method: 'POST',

            headers: {
              'Accept':
                'application/json',

              'Authorization':
                `Bearer ${token}`
            }
          }
        )
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


/*
|--------------------------------------------------------------------------
| View Order
|--------------------------------------------------------------------------
*/

const viewOrder = (
  order
) => {

  if (!order?.id) {
    return
  }

  emit(
    'view-order',
    order.id
  )
}


/*
|--------------------------------------------------------------------------
| Mount
|--------------------------------------------------------------------------
*/

onMounted(() => {

  createChart()

})


/*
|--------------------------------------------------------------------------
| Cleanup
|--------------------------------------------------------------------------
*/

onBeforeUnmount(() => {

  if (ordersChart) {

    ordersChart.destroy()

    ordersChart = null
  }

})

</script>


<style scoped>

.dashboard {
  display: flex;
  width: 100%;
  min-height: 100vh;
  background: #f7f9fb;
  color: #172033;
}

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

.content-container {
  width: 100%;
  max-width: 1050px;
  margin: 0 auto;
  padding: 27px 25px 40px;
}

.section-heading {
  margin-bottom: 12px;
}

.section-heading span {
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 1px;
  color: #8ea1b8;
}

.summary-grid {
  display: grid;
  grid-template-columns:
    repeat(4, minmax(0, 1fr));
  gap: 12px;
  margin-bottom: 12px;
}

.summary-card {
  min-height: 106px;
  padding: 15px;
  background: #ffffff;
  border: 1px solid #e4e9ef;
  border-radius: 9px;
  box-shadow:
    0 1px 2px rgba(16, 24, 40, 0.02);
}

.card-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #62728a;
  font-size: 10px;
}

.card-icon {
  width: 25px;
  height: 25px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 7px;
}

.card-icon img {
  width: 15px;
  height: 15px;
  object-fit: contain;
}

.card-icon.blue {
  background: #edf7fa;
}

.card-icon.green {
  background: #e9faf4;
}

.card-icon.claimed {
  background: #e7faf4;
}

.card-icon.pending {
  background: #fff9e9;
}

.card-value {
  margin-top: 9px;
  font-size: 22px;
  font-weight: 700;
  line-height: 1;
  color: #172033;
}

.card-description {
  margin-top: 7px;
  font-size: 8px;
  color: #9aabc0;
}

.chart-card {
  height: 243px;
  margin-bottom: 19px;
  background: #ffffff;
  border: 1px solid #e4e9ef;
  border-radius: 9px;
  overflow: hidden;
}

.chart-header {
  height: 55px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 18px;
  border-bottom: 1px solid #e4e9ef;
}

.chart-header h2,
.orders-header h2 {
  margin: 0;
  font-size: 12px;
  font-weight: 700;
  color: #172033;
}

.chart-header p,
.orders-header p {
  margin: 4px 0 0;
  font-size: 8px;
  color: #7f91a8;
}

.chart-legend {
  display: flex;
  align-items: center;
  gap: 16px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 8px;
  color: #73839a;
}

.legend-line {
  width: 9px;
  height: 3px;
  border-radius: 3px;
}

.legend-line.orders {
  background: #247f99;
}

.legend-line.revenue {
  background: #42c49d;
}

.chart-container {
  position: relative;
  height: 187px;
  padding: 10px 17px 7px;
}

.orders-card {
  background: #ffffff;
  border: 1px solid #e4e9ef;
  border-radius: 9px;
  overflow: hidden;
}

.orders-header {
  min-height: 57px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 18px;
  border-bottom: 1px solid #e4e9ef;
}

.view-all {
  border: none;
  background: transparent;
  color: #237f98;
  font-family: inherit;
  font-size: 9px;
  cursor: pointer;
}

.view-all:hover {
  text-decoration: underline;
}

.table-wrapper {
  width: 100%;
}

table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
}

thead {
  background: #fbfcfd;
}

th {
  height: 47px;
  padding: 8px 18px;
  text-align: left;
  vertical-align: middle;
  border-bottom: 1px solid #e8edf2;
  color: #62728a;
  font-size: 9px;
  font-weight: 500;
}

td {
  height: 50px;
  padding: 8px 18px;
  border-bottom: 1px solid #edf0f4;
  color: #34445c;
  font-size: 9px;
}

tbody tr:last-child td {
  border-bottom: none;
}

td.total {
  color: #172033;
  font-weight: 600;
}

.status {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 4px 8px;
  border-radius: 20px;
  white-space: nowrap;
  font-size: 7px;
}

.status-dot {
  width: 4px;
  height: 4px;
  border-radius: 50%;
}

.pending-status {
  background: #fff7e4;
  color: #b17b12;
}

.pending-status .status-dot {
  background: #f5a900;
}

.ready-status {
  background: #edf8fb;
  color: #247d95;
}

.ready-status .status-dot {
  background: #32a9c4;
}

.claimed-status {
  background: #e8faf3;
  color: #1c9b72;
}

.claimed-status .status-dot {
  background: #22bd8a;
}

.view-button {
  height: 27px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 0 10px;
  border: 1px solid #e1e7ed;
  border-radius: 6px;
  background: #ffffff;
  color: #4d5d73;
  font-family: inherit;
  font-size: 8px;
  cursor: pointer;
}

.view-button:hover {
  background: #f7fafc;
  border-color: #d7e0ea;
}

.empty-orders {
  height: 100px;
  text-align: center;
  color: #9aabc0;
  font-size: 9px;
}

</style>